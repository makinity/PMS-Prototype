<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin SMPOR Report</title>
    <style>
        @page { size: a4 landscape; margin: 12mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #000; padding: 5px; vertical-align: middle; text-align: center; }
        th { background: #f3f4f6; }
        .header { text-align: center; margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h3 style="margin: 0;">ADMIN SMPOR CONSOLIDATED REPORT</h3>
        <div>Office: {{ $officeName }}</div>
        <div>Performance Period: {{ $periodLabel }}</div>
    </div>

    <table>
        <tr>
            <td><strong>Average Quality</strong><br>{{ $summary['avg_quality'] }}</td>
            <td><strong>Average Timeliness</strong><br>{{ $summary['avg_timeliness'] }}</td>
            <td><strong>Overall Score</strong><br>{{ $summary['overall_score'] }}</td>
            <td><strong>Adjectival Rating</strong><br>{{ $summary['adjectival_rating'] }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 40%;">Employee</th>
                <th style="width: 15%;">Quality Avg</th>
                <th style="width: 15%;">Timeliness Avg</th>
                <th style="width: 15%;">Overall Score</th>
                <th style="width: 15%;">Adjectival Rating</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td style="text-align: left;">{{ $item['employee_name'] }}</td>
                    <td>{{ $item['quality_avg'] }}</td>
                    <td>{{ $item['timeliness_avg'] }}</td>
                    <td>{{ $item['overall_score'] }}</td>
                    <td>{{ $item['adjectival_rating'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
