<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SimpleAdminController extends Controller
{
    public function loginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Hardcoded admin check or Database check
        // For simplicity, let's allow any user with role 'admin'
        if (auth()->attempt($credentials)) {
            if (auth()->user()->role === 'admin') {
                $request->session()->regenerate();
                return redirect()->intended('/simple-admin/dashboard');
            } else {
                auth()->logout();
                return back()->withErrors(['email' => 'Anda bukan admin.']);
            }
        }

        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    public function dashboard()
    {
        $users = User::latest()->get();
        
        try {
            // Gunakan Model Eloquent jika ada, atau fallback ke empty
            $books = Book::latest()->get();
        } catch (\Exception $e) {
            $books = collect([]); // Return empty collection jika error (misal tabel belum dimigrate)
        }

        return view('admin.dashboard', compact('users', 'books'));
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/simple-admin/login');
    }
}
