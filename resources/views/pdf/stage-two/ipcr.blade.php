<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>INDIVIDUAL PERFORMANCE COMMITMENT AND REVIEW (IPCR)</title>
    <style>
        @page { size: A4 landscape; margin: 7mm; }
        * { -webkit-print-color-adjust: exact; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; }
        table { width: 100%; border-collapse: collapse; border-spacing: 0; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 4px 3px; vertical-align: middle; text-align: center; box-sizing: border-box; }
        .ipcr-table { width: 100%; table-layout: fixed; border-collapse: collapse; border-spacing: 0; }
        .ipcr-table th, .ipcr-table td { border: 1px solid #000; box-sizing: border-box; }
        .col-output { width: 18%; }
        .col-indicators { width: 30%; }
        .col-summary { width: 10%; }
        .col-rating { width: 14%; }
        .col-remarks { width: 10%; }
        .col-standards { width: 18%; }
        .no-border td { border: 0; padding: 3px 2px; }
        .header { text-align: center; margin-bottom: 8px; }
        .header strong { display: block; }
        .left { text-align: left; }
        .grey { background: #d9d9d9; }
        .section-label { font-weight: bold; text-align: left; padding-left: 6px; }
        .small { font-size: 9px; }
        .sign-block { margin-top: 12px; }
        .remarks { background: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h3 style="margin:2px 0;">INDIVIDUAL PERFORMANCE COMMITMENT AND REVIEW (IPCR)</h3>
    </div>

    <table class="no-border" style="margin-bottom: 4px;">
        <tr>
            <td style="width:60%; text-align:left;">Measures for the period <u>{{ $performancePeriod }}</u></td>
            <td style="width:40%; text-align:right;">Reviewed by: ____________________ Date: ___________</td>
        </tr>
        <tr>
            <td style="text-align:left;">Employee: <u>{{ $employeeName }}</u> &nbsp;&nbsp; Office/Division: <u>{{ $officeDivision }}</u></td>
            <td style="text-align:right;">Approved by: ____________________ Date: ___________</td>
        </tr>
    </table>

    <table class="ipcr-table">
        <thead>
            <tr>
                <th rowspan="2" class="col-output">OUTPUT</th>
                <th rowspan="2" class="col-indicators">Success Indicators (Measure + Target)</th>
                <th rowspan="2" class="col-summary">6 Months Summary of Accomplishment</th>
                <th colspan="4" class="col-rating">Rating</th>
                <th rowspan="2" class="col-remarks">Remarks</th>
                <th colspan="5" class="col-standards">Standards per Success Indicator</th>
            </tr>
            <tr class="small grey">
                <th>Q</th><th>E</th><th>T</th><th>A</th>
                <th>5</th><th>4</th><th>3</th><th>2</th><th>1</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="section-label" colspan="10">A. CORE FUNCTIONS (80%)</td>
            </tr>
            @foreach ($coreFunctions as $item)
                <tr>
                    <td class="left">{{ $item['output'] }}</td>
                    <td class="left">
                        @foreach ($item['success_indicators'] as $indicator)
                            {{ $indicator }}<br>
                        @endforeach
                    </td>
                    <td>{{ $item['accomplishment'] }}</td>
                    <td></td><td></td><td></td><td></td>
                    <td class="remarks">{{ $item['remarks'] }}</td>
                    <td></td><td></td><td></td><td></td><td></td>
                </tr>
            @endforeach

            <tr>
                <td class="section-label" colspan="10">C. SUPPORT FUNCTIONS (20%)</td>
            </tr>
            @foreach ($supportFunctions as $item)
                <tr>
                    <td class="left">{{ $item['output'] }}</td>
                    <td class="left">
                        @foreach ($item['success_indicators'] as $indicator)
                            {{ $indicator }}<br>
                        @endforeach
                    </td>
                    <td>{{ $item['accomplishment'] }}</td>
                    <td></td><td></td><td></td><td></td>
                    <td class="remarks">{{ $item['remarks'] }}</td>
                    <td></td><td></td><td></td><td></td><td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="no-border" style="margin-top:6px;">
        <tr>
            <td class="left">Weighted Average Rating for Core Functions (80%)</td>
            <td style="border:1px solid #000; width:12%;">&nbsp;</td>
        </tr>
        <tr>
            <td class="left">Weighted Average Rating for Support Functions (20%)</td>
            <td style="border:1px solid #000;">&nbsp;</td>
        </tr>
        <tr>
            <td class="left"><strong>OVERALL RATING</strong></td>
            <td style="border:1px solid #000;">&nbsp;</td>
        </tr>
        <tr>
            <td class="left"><strong>ADJECTIVAL RATING</strong></td>
            <td style="border:1px solid #000;">&nbsp;</td>
        </tr>
    </table>

    <table class="no-border" style="margin-top:8px;">
        <tr>
            <td class="left" style="width:100%; background:#e6e6fa; padding:6px; border:1px solid #000;">
                Comments and Recommendations for Development Purposes:
            </td>
        </tr>
        <tr>
            <td style="height:40px; border-left:1px solid #000; border-right:1px solid #000; border-bottom:1px solid #000;"></td>
        </tr>
    </table>

    <div class="sign-block">
        <table class="no-border">
            <tr>
                <td style="width:33%; text-align:center; border-top:1px solid #000;">Discussed with and Agreed by:<br><span class="small">Employee</span></td>
                <td style="width:33%; text-align:center; border-top:1px solid #000;">Assessed by:<br><span class="small">Immediate Supervisor</span></td>
                <td style="width:34%; text-align:center; border-top:1px solid #000;">Approved by:<br><span class="small">PGDH</span></td>
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
