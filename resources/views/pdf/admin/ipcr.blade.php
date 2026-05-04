<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin IPCR Report</title>
    <style>
        @page { size: legal landscape; margin: 12mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #000; padding: 4px; vertical-align: top; text-align: left; }
        th { background: #f3f4f6; text-align: center; }
        .header { text-align: center; margin-bottom: 8px; }
        .employee { margin-top: 16px; page-break-inside: avoid; }
        .section { background: #e5e7eb; font-weight: bold; text-align: left; }
        .muted { color: #555; }
    </style>
</head>
<body>
    <div class="header">
        <h3 style="margin: 0;">ADMIN IPCR CONSOLIDATED REPORT</h3>
        <div>Office: {{ $officeName }}</div>
        <div>Performance Period: {{ $periodLabel }}</div>
    </div>

    @foreach ($employees as $employee)
        <div class="employee">
            <table>
                <tr>
                    <td style="width: 40%;"><strong>Employee:</strong> {{ $employee['employee_name'] }}</td>
                    <td style="width: 25%;"><strong>Position:</strong> {{ $employee['position'] }}</td>
                    <td style="width: 15%;"><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $employee['status'])) }}</td>
                    <td style="width: 10%;"><strong>Score:</strong> {{ $employee['final_score'] }}</td>
                    <td style="width: 10%;"><strong>Rating:</strong> {{ $employee['adjectival_rating'] }}</td>
                </tr>
            </table>

            <table>
                <thead>
                    <tr>
                        <th style="width: 20%;">Output</th>
                        <th style="width: 28%;">Indicator</th>
                        <th style="width: 14%;">Target Summary</th>
                        <th style="width: 38%;">Standards</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="4" class="section">Core Functions</td></tr>
                    @forelse ($employee['core_items'] as $item)
                        <tr>
                            <td>{{ $item['output'] }}</td>
                            <td>{{ $item['indicator'] }}</td>
                            <td>{{ $item['target_summary'] }}</td>
                            <td class="muted">{{ $item['standards'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align: center;">No core IPCR items found.</td></tr>
                    @endforelse
                    <tr><td colspan="4" class="section">Support Functions</td></tr>
                    @forelse ($employee['support_items'] as $item)
                        <tr>
                            <td>{{ $item['output'] }}</td>
                            <td>{{ $item['indicator'] }}</td>
                            <td>{{ $item['target_summary'] }}</td>
                            <td class="muted">{{ $item['standards'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align: center;">No support IPCR items found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endforeach
</body>
</html>
