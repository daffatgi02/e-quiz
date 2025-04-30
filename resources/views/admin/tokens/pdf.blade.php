<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>ID-Trainer Cards</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: space-between;
        }

        .card {
            border: 1px dashed #000;
            padding: 15px;
            width: 45%;
            box-sizing: border-box;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .card-header {
            text-align: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .card-body {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .field {
            display: flex;
            justify-content: space-between;
        }

        .field-label {
            font-weight: bold;
            width: 100px;
        }

        .token-highlight {
            font-family: Courier;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            padding: 5px;
            margin: 10px 0;
            background-color: #f2f2f2;
            border: 1px solid #ddd;
        }

        .card-footer {
            font-size: 9px;
            margin-top: 10px;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        .scissor-line {
            border: none;
            border-top: 1px dashed #000;
            height: 0;
            width: 100%;
            margin: 20px 0;
            position: relative;
        }

        .scissor-line::before {
            content: "✂";
            position: absolute;
            top: -10px;
            left: -15px;
        }

        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .filter-info {
            text-align: center;
            font-size: 12px;
            margin-bottom: 15px;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="report-header">
        <h1>Daftar ID-Trainer</h1>
        <p>Tanggal: {{ date('d F Y') }}</p>

        @if ($filters['department'] || $filters['position'])
            <div class="filter-info">
                Filter:
                @if ($filters['department'])
                    Departemen: <strong>{{ $filters['department'] }}</strong>
                @endif

                @if ($filters['position'])
                    @if ($filters['department'])
                        |
                    @endif
                    Posisi: <strong>{{ $filters['position'] }}</strong>
                @endif
            </div>
        @endif
    </div>

    <div class="card-container">
        @foreach ($users as $user)
            <div class="card">
                <div class="card-header">
                    <h3>ID-Trainer</h3>
                </div>
                <div class="card-body">
                    <div class="field">
                        <span class="field-label">Nama:</span>
                        <span>{{ $user->name }}</span>
                    </div>
                    <div class="field">
                        <span class="field-label">NIK:</span>
                        <span>{{ $user->nik }}</span>
                    </div>
                    <div class="field">
                        <span class="field-label">Department:</span>
                        <span>{{ $user->department }}</span>
                    </div>
                    <div class="field">
                        <span class="field-label">Posisi:</span>
                        <span>{{ $user->position }}</span>
                    </div>
                    <div>
                        <span class="field-label">ID-Trainer:</span>
                        <div class="token-highlight">{{ $user->login_token }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <p><strong>Catatan:</strong> ID-Trainer ini bersifat rahasia. Anda akan diminta membuat PIN 6 digit
                        pada login pertama. Untuk reset PIN, hubungi administrator.</p>
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>
