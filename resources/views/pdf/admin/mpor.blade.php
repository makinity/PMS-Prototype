<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin MPOR Report</title>
    <style>
        @page { size: legal landscape; margin: 12mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #000; padding: 4px; vertical-align: top; text-align: center; }
        th { background: #f3f4f6; }
        .header { text-align: center; margin-bottom: 8px; }
        .employee { margin-top: 16px; page-break-inside: avoid; }
        .left { text-align: left; }
    </style>
</head>
<body>
    <div class="header">
        <h3 style="margin: 0;">ADMIN MPOR CONSOLIDATED REPORT</h3>
        <div>Office: {{ $officeName }}</div>
        <div>Performance Period: {{ $periodLabel }}</div>
    </div>

    @foreach ($employees as $employee)
        <div class="employee">
            <div><strong>Employee:</strong> {{ $employee['employee_name'] }}</div>
            <table>
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 18%;">Output</th>
                        @foreach ($months as $month)
                            <th colspan="3">{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y') }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach ($months as $month)
                            <th>Qty</th>
                            <th>Quality</th>
                            <th>Timeliness</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employee['rows'] as $row)
                        <tr>
                            <td class="left">{{ $row['output'] }}</td>
                            @foreach ($months as $month)
                                <td>{{ rtrim(rtrim(number_format($row['months'][$month]['qty'], 2, '.', ''), '0'), '.') }}</td>
                                <td>{{ rtrim(rtrim(number_format($row['months'][$month]['quality'], 2, '.', ''), '0'), '.') }}</td>
                                <td>{{ rtrim(rtrim(number_format($row['months'][$month]['timeliness'], 2, '.', ''), '0'), '.') }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</body>
</html>
