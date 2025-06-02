<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Training {{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            margin: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 16px;
            margin: 5px 0;
        }
        .header h2 {
            font-size: 14px;
            margin: 5px 0;
        }
        .info-section {
            margin-bottom: 15px;
            font-size: 9px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 3px 8px;
            border: none;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px 4px;
            text-align: left;
            font-size: 9px;
            vertical-align: middle;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .center {
            text-align: center;
        }
        .passed {
            background-color: #d4edda;
            color: #155724;
            font-weight: bold;
        }
        .failed {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
        }
        .no-data {
            text-align: center;
            font-style: italic;
            color: #666;
        }
        .summary {
            margin-top: 10px;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN TRAINING</h1>
        <h2>{{ strtoupper($title) }}</h2>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td style="width: 25%;"><strong>Pre Test Quiz:</strong></td>
                <td style="width: 25%;">{{ $preTestTitle ?? 'N/A' }}</td>
                <td style="width: 25%;"><strong>Tanggal Pre Test:</strong></td>
                <td style="width: 25%;">{{ $preTestDate ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Post Test Quiz:</strong></td>
                <td>{{ $postTestTitle ?? 'N/A' }}</td>
                <td><strong>Tanggal Post Test:</strong></td>
                <td>{{ $postTestDate ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Total Peserta:</strong></td>
                <td>{{ $totalUsers ?? count($reportData) }}</td>
                <td><strong>Tanggal Export:</strong></td>
                <td>{{ $exportDate ?? date('d F Y') }}</td>
            </tr>
        </table>
    </div>

    @if(!empty($reportData))
        <table>
            <thead>
                <tr>
                    <th style="width: 4%;">NO</th>
                    <th style="width: 12%;">NIK</th>
                    <th style="width: 20%;">NAMA PESERTA</th>
                    <th style="width: 15%;">POSISI</th>
                    {{-- <th style="width: 12%;">DEPARTEMEN</th> --}}
                    <th style="width: 8%;">PRE TEST</th>
                    <th style="width: 8%;">POST TEST</th>
                    <th style="width: 10%;">STATUS</th>
                    <th style="width: 11%;">PERUSAHAAN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $data)
                    <tr>
                        <td class="center">{{ $data['no'] }}</td>
                        <td class="center">{{ $data['nik'] }}</td>
                        <td>{{ $data['name'] }}</td>
                        <td>{{ $data['position'] }}</td>
                        {{-- <td>{{ $data['department'] ?? '-' }}</td> --}}
                        <td class="center">{{ $data['pre_test_score'] }}</td>
                        <td class="center">{{ $data['post_test_score'] }}</td>
                        <td class="center {{ $data['keterangan'] == 'LULUS' || $data['keterangan'] == 'PASSED' ? 'passed' : ($data['keterangan'] == 'TIDAK LULUS' || $data['keterangan'] == 'FAILED' ? 'failed' : '') }}">
                            {{ $data['keterangan'] }}
                        </td>
                        <td style="font-size: 8px;">{{ $data['company'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            @php
                $passed = collect($reportData)->where('keterangan', 'LULUS')->count() + collect($reportData)->where('keterangan', 'PASSED')->count();
                $failed = collect($reportData)->where('keterangan', 'TIDAK LULUS')->count() + collect($reportData)->where('keterangan', 'FAILED')->count();
                $total = count($reportData);
                $passRate = $total > 0 ? round(($passed / $total) * 100, 1) : 0;
            @endphp
            <table style="width: 50%; margin-left: auto;">
                <tr>
                    <td><strong>Total Peserta:</strong></td>
                    <td class="center">{{ $total }}</td>
                </tr>
                <tr>
                    <td><strong>Lulus:</strong></td>
                    <td class="center passed">{{ $passed }}</td>
                </tr>
                <tr>
                    <td><strong>Tidak Lulus:</strong></td>
                    <td class="center failed">{{ $failed }}</td>
                </tr>
                <tr>
                    <td><strong>Tingkat Kelulusan:</strong></td>
                    <td class="center"><strong>{{ $passRate }}%</strong></td>
                </tr>
            </table>
        </div>
    @else
        <div class="no-data">
            <p><strong>TIDAK ADA DATA DITEMUKAN</strong></p>
            <p>Tidak ada peserta yang mengerjakan quiz yang dipilih atau belum ada yang selesai.</p>
        </div>
    @endif

    {{-- <div style="margin-top: 20px; font-size: 8px; color: #666;">
        <p><strong>Catatan:</strong></p>
        <ul>
            <li>Nilai diambil dari attempt terbaik jika peserta mengerjakan lebih dari sekali</li>
            <li>Status kelulusan: Lead/Supervisor ≥80, Staff ≥70</li>
            <li>"-" menunjukkan peserta belum mengerjakan quiz tersebut</li>
        </ul>
    </div> --}}
</body>
</html>