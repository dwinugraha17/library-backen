<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Services\BrevoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BorrowController extends Controller
{
    public function borrow(Request $request, BrevoService $brevo)
    {
        $validator = Validator::make($request->all(), [
            'book_id' => 'required|exists:books,id',
            'borrow_date' => 'required|date',
            'return_date' => 'required|date|after:borrow_date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $book = Book::find($request->book_id);

        if ($book->stock <= 0) {
            return response()->json(['message' => 'Book is out of stock'], 400);
        }

        $borrowing = DB::transaction(function () use ($request, $book) {
            $borrowing = Borrowing::create([
                'user_id' => $request->user()->id,
                'book_id' => $request->book_id,
                'borrow_date' => $request->borrow_date,
                'return_date' => $request->return_date,
                'status' => 'borrowed',
            ]);

            $book->decrement('stock');
            if ($book->stock == 0) {
                $book->update(['status' => 'Unavailable']);
            }

            return $borrowing;
        });

        // Send Email Notification via Brevo API
        try {
            $user = $request->user();
            if ($user->email) {
                $borrowDate = Carbon::parse($borrowing->borrow_date)->format('d M Y');
                $returnDate = Carbon::parse($borrowing->return_date)->format('d M Y');
                
                $htmlContent = "
                    <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee;'>
                        <h2 style='color: #2563EB;'>Peminjaman Berhasil!</h2>
                        <p>Halo <strong>{$user->name}</strong>,</p>
                        <p>Anda telah berhasil meminjam buku:</p>
                        <ul style='list-style: none; padding: 0;'>
                            <li><strong>Judul:</strong> {$book->title}</li>
                            <li><strong>Tanggal Pinjam:</strong> {$borrowDate}</li>
                            <li><strong>Batas Kembali:</strong> <span style='color: red;'>{$returnDate}</span></li>
                        </ul>
                        <p>Terima kasih telah menggunakan UNILAM Library.</p>
                    </div>
                ";

                $brevo->sendEmail(
                    $user->email, 
                    $user->name, 
                    "Peminjaman Berhasil: {$book->title}", 
                    $htmlContent
                );
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send borrow email: " . $e->getMessage());
        }

        return response()->json($borrowing, 201);
    }

    public function returnBook(Request $request, Borrowing $borrowing)
    {
        if ($borrowing->status !== 'borrowed') {
            return response()->json(['message' => 'Book already returned'], 400);
        }

        return DB::transaction(function () use ($borrowing) {
            $borrowing->update([
                'actual_return_date' => now(),
                'status' => 'returned',
            ]);

            $book = $borrowing->book;
            $book->increment('stock');
            $book->update(['status' => 'Available']);

            return response()->json(['message' => 'Book returned successfully', 'borrowing' => $borrowing]);
        });
    }

    public function history(Request $request)
    {
        $history = Borrowing::with('book')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($history);
    }
}
