<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Training {{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .subheader {
            text-align: center;
            margin-bottom: 10px;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .passed {
            background-color: #d4edda;
            color: #155724;
        }
        .failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        .page-break {
            page-break-after: always;
        }
        .dates-info {
            margin-bottom: 15px;
            font-size: 11px;
        }
        .dates-info table {
            border: none;
            width: auto;
            margin-left: auto;
            margin-right: auto;
        }
        .dates-info table td, .dates-info table th {
            border: none;
            padding: 3px 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN TRAINING</h1>
        <h2>Materi Training: {{ $title }}</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="15%">NIK</th>
                <th width="25%">NAMA PESERTA</th>
                <th width="20%">POSISI</th>
                <th width="10%">NILAI PRE TEST</th>
                <th width="10%">NILAI POST TEST</th>
                <th width="15%">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $data)
                <tr>
                    <td>{{ $data['no'] }}</td>
                    <td>{{ $data['nik'] }}</td>
                    <td>{{ $data['name'] }}</td>
                    <td>{{ $data['position'] }}</td>
                    <td style="text-align: center;">{{ $data['pre_test_score'] }}</td>
                    <td style="text-align: center;">{{ $data['post_test_score'] }}</td>
                    <td class="{{ $data['keterangan'] == 'LULUS' ? 'passed' : ($data['keterangan'] == 'TIDAK LULUS' ? 'failed' : '') }}">
                        {{ $data['keterangan'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="dates-info">
        <table>
            <tr>
                <th>Tanggal Pre Test:</th>
                <td>{{ $preTestDate }}</td>
            </tr>
            <tr>
                <th>Tanggal Post Test:</th>
                <td>{{ $postTestDate }}</td>
            </tr>
            {{-- <tr>
                <th>Tanggal Export:</th>
                <td>{{ $exportDate }}</td>
            </tr> --}}
        </table>
    </div>
</body>
</html>
