<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Performance Output Report</title>
    <style>
        @page { size: A4 portrait; margin: 12mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px 3px; vertical-align: middle; text-align: center; }
        .no-border td { border: none; padding: 2px 2px; text-align: left; }
        .header { text-align: center; margin-bottom: 8px; }
        .header strong { display: block; }
        .title { font-size: 12px; font-weight: 700; text-decoration: underline; margin: 6px 0; }
        .section-label { font-weight: 700; text-align: left; background: #f5f5f5; }
        .left { text-align: left; }
        .muted { font-size: 9px; color: #333; text-align: left; }
    </style>
</head>
<body>

    <div class="header">
        <strong>REPUBLIC OF THE PHILIPPINES</strong>
        <strong>PROVINCE OF DAVAO DEL SUR</strong>
        <strong>PROVINCIAL HUMAN RESOURCE MANAGEMENT OFFICE</strong>
        <div class="title">MONTHLY PERFORMANCE OUTPUT REPORT (MPOR)</div>
        <div class="muted">(Stage II – Monitoring Copy | Read-only)</div>
    </div>

    <table class="no-border">
        <tr>
            <td style="width:25%;"><strong>Employee Name:</strong></td>
            <td style="border-bottom:1px solid #000; width:35%;">{{ $mpor['employee'] }}</td>
            <td style="width:20%;"><strong>Month & Year:</strong></td>
            <td style="border-bottom:1px solid #000; width:20%;">{{ $mpor['month'] }}</td>
        </tr>
        <tr>
            <td><strong>Office / Unit:</strong></td>
            <td style="border-bottom:1px solid #000;">{{ $mpor['office'] }}</td>
            <td colspan="2"></td>
        </tr>
    </table>

    <table style="margin-top:6px;">
        <thead>
            <tr>
                <th rowspan="2" style="width:24%;">EXPECTED OUTPUTS</th>
                <th colspan="5">EFFICIENCY / QUANTITY</th>
                <th colspan="5">QUALITY / EFFECTIVENESS</th>
                <th colspan="5">TIMELINESS</th>
            </tr>
            <tr>
                <th style="width:4%;">W1</th>
                <th style="width:4%;">W2</th>
                <th style="width:4%;">W3</th>
                <th style="width:4%;">W4</th>
                <th style="width:4%;">TOTAL</th>
                <th style="width:4%;">W1</th>
                <th style="width:4%;">W2</th>
                <th style="width:4%;">W3</th>
                <th style="width:4%;">W4</th>
                <th style="width:4%;">TOTAL</th>
                <th style="width:4%;">W1</th>
                <th style="width:4%;">W2</th>
                <th style="width:4%;">W3</th>
                <th style="width:4%;">W4</th>
                <th style="width:4%;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="section-label" colspan="16">CORE FUNCTIONS (80%)</td>
            </tr>
            @foreach ($mpor['core'] as $row)
                <tr>
                    <td class="left">{{ $row['output'] }}</td>
                    <td>{{ $row['week1'] }}</td>
                    <td>{{ $row['week2'] }}</td>
                    <td>{{ $row['week3'] }}</td>
                    <td>{{ $row['week4'] }}</td>
                    <td>{{ $row['total'] }}</td>
                    <td>{{ $row['week1'] }}</td>
                    <td>{{ $row['week2'] }}</td>
                    <td>{{ $row['week3'] }}</td>
                    <td>{{ $row['week4'] }}</td>
                    <td>{{ $row['total'] }}</td>
                    <td>{{ $row['week1'] }}</td>
                    <td>{{ $row['week2'] }}</td>
                    <td>{{ $row['week3'] }}</td>
                    <td>{{ $row['week4'] }}</td>
                    <td>{{ $row['total'] }}</td>
                </tr>
            @endforeach

            <tr>
                <td class="section-label" colspan="16">SUPPORT FUNCTIONS (20%)</td>
            </tr>
            @foreach ($mpor['support'] as $row)
                <tr>
                    <td class="left">{{ $row['output'] }}</td>
                    <td>{{ $row['week1'] }}</td>
                    <td>{{ $row['week2'] }}</td>
                    <td>{{ $row['week3'] }}</td>
                    <td>{{ $row['week4'] }}</td>
                    <td>{{ $row['total'] }}</td>
                    <td>{{ $row['week1'] }}</td>
                    <td>{{ $row['week2'] }}</td>
                    <td>{{ $row['week3'] }}</td>
                    <td>{{ $row['week4'] }}</td>
                    <td>{{ $row['total'] }}</td>
                    <td>{{ $row['week1'] }}</td>
                    <td>{{ $row['week2'] }}</td>
                    <td>{{ $row['week3'] }}</td>
                    <td>{{ $row['week4'] }}</td>
                    <td>{{ $row['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="margin-top:6px;">
        <thead>
            <tr>
                <th class="left" style="width:24%;">&nbsp;</th>
                <th>WEEK 1</th>
                <th>WEEK 2</th>
                <th>WEEK 3</th>
                <th>WEEK 4</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="left"><strong>MAN DAY(S) LOST THRU ABSENCE</strong></td>
                <td>{{ $mpor['attendance']['absence']['week1'] }}</td>
                <td>{{ $mpor['attendance']['absence']['week2'] }}</td>
                <td>{{ $mpor['attendance']['absence']['week3'] }}</td>
                <td>{{ $mpor['attendance']['absence']['week4'] }}</td>
                <td>{{ $mpor['attendance']['absence']['week1'] + $mpor['attendance']['absence']['week2'] + $mpor['attendance']['absence']['week3'] + $mpor['attendance']['absence']['week4'] }}days</td>
            </tr>
            <tr>
                <td class="left"><strong>MAN HRS./MINUTES LOST THRU TARDINESS / UNDERTIME</strong></td>
                <td>{{ $mpor['attendance']['tardiness']['week1'] }}</td>
                <td>{{ $mpor['attendance']['tardiness']['week2'] }}</td>
                <td>{{ $mpor['attendance']['tardiness']['week3'] }}</td>
                <td>{{ $mpor['attendance']['tardiness']['week4'] }}</td>
                <td>{{ $mpor['attendance']['tardiness']['week1'] + $mpor['attendance']['tardiness']['week2'] + $mpor['attendance']['tardiness']['week3'] + $mpor['attendance']['tardiness']['week4'] }}mins</td>
            </tr>
        </tbody>
    </table>

    <table class="no-border" style="margin-top:10px;">
        <tr>
            <td style="width:45%; text-align:center;">
                <div style="border-top:1px solid #000; margin-top:18px; padding-top:2px;">CONFIRMED:</div>
                <div style="min-height:18px;">&nbsp;</div>
            </td>
            <td style="width:10%;"></td>
            <td style="width:45%; text-align:center;">
                <div style="border-top:1px solid #000; margin-top:18px; padding-top:2px;">Above information are true and correct:</div>
                <div style="min-height:18px;">&nbsp;</div>
            </td>
        </tr>
    </table>

    <div class="muted" style="margin-top:8px;">
        This is a system-generated monitoring report derived from ORS. Validation, SMPOR generation, and performance rating occur in Stage III – Performance Review.
    </div>

</body>
</html>
