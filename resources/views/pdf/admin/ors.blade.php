<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin ORS Report</title>
    <style>
        @page { size: legal portrait; margin: 12mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #000; padding: 4px; vertical-align: top; }
        th { background: #f3f4f6; text-align: center; }
        .header { text-align: center; margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h3 style="margin: 0;">ADMIN ORS CONSOLIDATED REPORT</h3>
        <div>Office: {{ $officeName }}</div>
        <div>Performance Period: {{ $periodLabel }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 16%;">Employee</th>
                <th style="width: 24%;">Output</th>
                <th style="width: 10%;">Date</th>
                <th style="width: 8%;">Qty</th>
                <th style="width: 8%;">Quality</th>
                <th style="width: 8%;">Timeliness</th>
                <th style="width: 14%;">Supervisor</th>
                <th style="width: 12%;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($entries as $entry)
                <tr>
                    <td>{{ $entry['employee_name'] }}</td>
                    <td>{{ $entry['output'] }}</td>
                    <td>{{ $entry['work_date'] }}</td>
                    <td style="text-align: center;">{{ $entry['quantity'] }}</td>
                    <td style="text-align: center;">{{ $entry['quality'] }}</td>
                    <td style="text-align: center;">{{ $entry['timeliness'] }}</td>
                    <td>{{ $entry['supervisor'] }}</td>
                    <td>{{ $entry['remarks'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
