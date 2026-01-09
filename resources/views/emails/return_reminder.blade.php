<!DOCTYPE html>
<html>
<head>
    <title>Pengingat Pengembalian Buku</title>
</head>
<body>
    <h1>Halo, {{ $borrowing->user->name }}</h1>
    <p>Ini adalah pengingat ({{ $label }}) untuk mengembalikan buku:</p>
    <p><strong>{{ $borrowing->book->title }}</strong></p>
    <p>Batas waktu pengembalian adalah: {{ \Carbon\Carbon::parse($borrowing->return_date)->format('d M Y') }}.</p>
    <p>Terima kasih telah menggunakan UNILAM Library.</p>
</body>
</html>
