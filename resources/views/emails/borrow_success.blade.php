<!DOCTYPE html>
<html>
<head>
    <title>Peminjaman Buku Berhasil</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #2563EB; text-align: center;">UNILAM Library</h2>
        <p>Halo, <strong>{{ $user->name }}</strong>!</p>
        <p>Terima kasih telah meminjam buku di perpustakaan kami. Berikut adalah detail peminjaman Anda:</p>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong>Judul Buku</strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ $book->title }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong>Penulis</strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ $book->author }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong>Tanggal Pinjam</strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong>Batas Kembali</strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee; color: #d9534f; font-weight: bold;">{{ \Carbon\Carbon::parse($borrowing->return_date)->format('d M Y') }}</td>
            </tr>
        </table>

        <p>Mohon untuk mengembalikan buku tepat waktu agar tidak dikenakan sanksi.</p>
        
        <div style="text-align: center; margin-top: 30px; font-size: 12px; color: #777;">
            <p>&copy; {{ date('Y') }} UNILAM Library. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
