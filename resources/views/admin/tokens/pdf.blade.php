{{-- resources/views/admin/tokens/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar ID-Trainer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .token-highlight {
            font-family: Courier;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Daftar ID-Trainer</h1>
        <p>Tanggal: {{ date('d F Y') }}</p>
        @if(!empty($filters['department']) || !empty($filters['position']))
            <p>Filter:
                @if(!empty($filters['department'])) Department: {{ $filters['department'] }} @endif
                @if(!empty($filters['position'])) | Posisi: {{ $filters['position'] }} @endif
            </p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Department</th>
                <th>Posisi</th>
                <th>ID-Trainer</th>
                <th>Status PIN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->nik }}</td>
                    <td>{{ $user->department }}</td>
                    <td>{{ $user->position }}</td>
                    <td class="token-highlight">{{ $user->login_token }}</td>
                    <td>{{ $user->pin_set ? 'Sudah Set' : 'Belum Set' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 10px;">
        <p><strong>Catatan:</strong></p>
        <ul>
            <li>ID-Trainer ini bersifat rahasia dan hanya diberikan kepada masing-masing peserta</li>
            <li>Peserta akan diminta membuat PIN 6 digit pada saat login pertama kali</li>
            <li>Untuk reset PIN, silakan hubungi administrator</li>
        </ul>
    </div>
</body>
</html>
