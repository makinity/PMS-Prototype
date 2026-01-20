<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SUMMARY MONTHLY PERFORMANCE OUTPUT REPORT</title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px 3px; vertical-align: middle; text-align: center; }
        .no-border td { border: none; padding: 3px 2px; }
        .header { text-align: center; margin-bottom: 8px; }
        .header strong { display: block; }
        .section-header { background: #d9e2f3; font-weight: bold; text-align: left; padding-left: 6px; }
        .small { font-size: 9px; }
        .left { text-align: left; }
        .sign-block { margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <strong>Republic of the Philippines</strong>
        <strong>PROVINCE OF DAVAO DEL SUR</strong>
        <div>{{ $provinceCity }}</div>
        <h3 style="margin: 6px 0;">SUMMARY MONTHLY PERFORMANCE OUTPUT REPORT</h3>
    </div>

    <table class="no-border" style="margin-bottom: 4px;">
        <tr>
            <td style="width:40%;">Name: <u>{{ $employeeName }}</u></td>
            <td style="width:40%;">Office/Division: <u>{{ $officeDivision }}</u></td>
            <td style="width:20%;">Semestral Period: <u>{{ $semestralPeriod }}</u></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width:18%;">EXPECTED OUTPUTS</th>
                <th colspan="8">EFFICIENCY/QUANTITY</th>
                <th colspan="8">QUALITY/EFFECTIVENESS</th>
                <th colspan="8">TIMELINESS</th>
            </tr>
            <tr class="small">
                <th>Jan</th><th>Feb</th><th>March</th><th>April</th><th>May</th><th>June</th><th>Total</th><th>Average</th>
                <th>Jan</th><th>Feb</th><th>March</th><th>April</th><th>May</th><th>June</th><th>Total</th><th>Average</th>
                <th>Jan</th><th>Feb</th><th>March</th><th>April</th><th>May</th><th>June</th><th>Total</th><th>Average</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="section-header" colspan="25">CORE FUNCTION (80%)</td>
            </tr>
            @foreach ($coreFunctions as $row)
                <tr>
                    <td class="left">{{ $row['output'] }}</td>
                    <td>{{ $row['efficiency']['Jan'] }}</td><td>{{ $row['efficiency']['Feb'] }}</td><td>{{ $row['efficiency']['Mar'] }}</td><td>{{ $row['efficiency']['Apr'] }}</td><td>{{ $row['efficiency']['May'] }}</td><td>{{ $row['efficiency']['Jun'] }}</td><td>{{ $row['efficiency']['Total'] }}</td><td>{{ $row['efficiency']['Average'] }}</td>
                    <td>{{ $row['quality']['Jan'] }}</td><td>{{ $row['quality']['Feb'] }}</td><td>{{ $row['quality']['Mar'] }}</td><td>{{ $row['quality']['Apr'] }}</td><td>{{ $row['quality']['May'] }}</td><td>{{ $row['quality']['Jun'] }}</td><td>{{ $row['quality']['Total'] }}</td><td>{{ $row['quality']['Average'] }}</td>
                    <td>{{ $row['quality']['Jan'] }}</td><td>{{ $row['quality']['Feb'] }}</td><td>{{ $row['quality']['Mar'] }}</td><td>{{ $row['quality']['Apr'] }}</td><td>{{ $row['quality']['May'] }}</td><td>{{ $row['quality']['Jun'] }}</td><td>{{ $row['quality']['Total'] }}</td><td>{{ $row['quality']['Average'] }}</td>
                </tr>
            @endforeach

            <tr>
                <td class="section-header" colspan="25">SUPPORT FUNCTIONS (20%)</td>
            </tr>
            @foreach ($supportFunctions as $row)
                <tr>
                    <td class="left">{{ $row['output'] }}</td>
                    <td>{{ $row['efficiency']['Jan'] }}</td><td>{{ $row['efficiency']['Feb'] }}</td><td>{{ $row['efficiency']['Mar'] }}</td><td>{{ $row['efficiency']['Apr'] }}</td><td>{{ $row['efficiency']['May'] }}</td><td>{{ $row['efficiency']['Jun'] }}</td><td>{{ $row['efficiency']['Total'] }}</td><td>{{ $row['efficiency']['Average'] }}</td>
                    <td>{{ $row['quality']['Jan'] }}</td><td>{{ $row['quality']['Feb'] }}</td><td>{{ $row['quality']['Mar'] }}</td><td>{{ $row['quality']['Apr'] }}</td><td>{{ $row['quality']['May'] }}</td><td>{{ $row['quality']['Jun'] }}</td><td>{{ $row['quality']['Total'] }}</td><td>{{ $row['quality']['Average'] }}</td>
                    <td>{{ $row['quality']['Jan'] }}</td><td>{{ $row['quality']['Feb'] }}</td><td>{{ $row['quality']['Mar'] }}</td><td>{{ $row['quality']['Apr'] }}</td><td>{{ $row['quality']['May'] }}</td><td>{{ $row['quality']['Jun'] }}</td><td>{{ $row['quality']['Total'] }}</td><td>{{ $row['quality']['Average'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="margin-top:10px;">
        <tr>
            <td class="left" style="width:50%;">MAN DAY(S) LOST THRU ABSENCE</td>
            <td>Jan</td><td>Feb</td><td>March</td><td>April</td><td>May</td><td>June</td><td>Total</td>
        </tr>
        <tr>
            <td class="left">MAN HRS./MINUTES LOST THRU TARDINESS/UNDERTIME</td>
            <td>{{ $timeliness['core']['Jan'] }}</td><td>{{ $timeliness['core']['Feb'] }}</td><td>{{ $timeliness['core']['Mar'] }}</td><td>{{ $timeliness['core']['Apr'] }}</td><td>{{ $timeliness['core']['May'] }}</td><td>{{ $timeliness['core']['Jun'] }}</td><td>{{ $manHoursLost }}mins</td>
        </tr>
    </table>

    <div class="sign-block">
        <table class="no-border">
            <tr>
                <td style="width:33%; text-align:center; border-top:1px solid #000;">Direct Supervisor<br><span class="small">Position</span></td>
                <td style="width:33%; text-align:center; border-top:1px solid #000;">Department Head<br><span class="small">Position</span></td>
                <td style="width:34%; text-align:center; border-top:1px solid #000;">Employees' Name<br><span class="small">Position</span></td>
            </tr>
            <tr>
                <td style="text-align:center;">Date: _____________</td>
                <td style="text-align:center;">Date: _____________</td>
                <td style="text-align:center;">Date: _____________</td>
            </tr>
        </table>
    </div>
</body>
</html>
