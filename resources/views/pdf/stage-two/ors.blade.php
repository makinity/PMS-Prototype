<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Output Rating Sheet (Stage II – Monitoring Copy)</title>
    <style>
        @page { size: A4 portrait; margin: 16mm; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
        }

        .header { text-align: center; margin-bottom: 10px; }
        .header strong { display: block; }
        .office { margin-top: 8px; font-size: 12px; font-weight: 700; }
        .sheet-title { margin: 10px 0 2px; font-size: 14px; font-weight: 800; text-decoration: underline; }
        .muted { font-size: 10px; color: #444; }

        table { width: 100%; border-collapse: collapse; }
        td { padding: 4px 4px; vertical-align: middle; }

        .label { width: 34%; font-weight: 700; }
        .line {
            border-bottom: 1px solid #000;
            min-height: 16px;
            padding: 2px 4px;
        }
        .line-wide {
            border-bottom: 1px solid #000;
            min-height: 18px;
            padding: 2px 4px;
        }

        .rating-wrap { margin-top: 10px; }
        .rating-label { width: 18%; font-weight: 700; }
        .rating-sub { width: 18%; font-weight: 700; }
        .rating-line { border-bottom: 1px solid #000; min-height: 16px; }

        .remarks-block { margin-top: 10px; }
        .remarks-label { width: 34%; font-weight: 700; vertical-align: top; padding-top: 6px; }
        .remarks-line { border-bottom: 1px solid #000; min-height: 18px; padding: 2px 4px; }

        .footer { margin-top: 12px; }
        .small-note { margin-top: 10px; font-size: 10px; color: #444; }

        .value { display: inline-block; }
        .value-center { text-align: center; font-weight: 700; }
    </style>
</head>
<body>

    <div class="header">
        <strong>Republic of the Philippines</strong>
        <strong style="font-size:13px;">PROVINCE OF DAVAO DEL SUR</strong>
        <strong>Matti, Digos City</strong>
        <div class="office">PROVINCIAL HUMAN RESOURCE MANAGEMENT OFFICE</div>
        <div class="sheet-title">OUTPUT RATING SHEET</div>
    </div>

    <table>
        <tr>
            <td class="label">Ratee’s Name:</td>
            <td class="line-wide">{{ $ors['ratee'] }}</td>
        </tr>
        <tr>
            <td class="label">Output:</td>
            <td class="line-wide">{{ $ors['output'] }}</td>
        </tr>
        <tr>
            <td class="label">Date Submitted by Ratee to Rater:</td>
            <td class="line-wide">{{ $ors['date_submitted'] }}</td>
        </tr>
    </table>

    <div class="rating-wrap">
        <table>
            <tr>
                <td class="rating-label">RATING:</td>
                <td style="width:6%;"></td>
                <td class="rating-sub">Quantity:</td>
                <td class="rating-line">
                    <div class="value value-center">{{ $ors['quantity'] }}</div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td class="rating-sub">Quality:</td>
                <td class="rating-line">
                    <div class="value value-center">{{ $ors['quality'] }}</div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td class="rating-sub">Timeliness:</td>
                <td class="rating-line">
                    <div class="value value-center">{{ $ors['timeliness'] }}</div>
                </td>
            </tr>
        </table>

    </div>

    <div class="remarks-block">
        <table>
            <tr>
                <td class="remarks-label">Remarks:</td>
                <td class="remarks-line">{{ $ors['remarks'] }}</td>
            </tr>
            <tr><td></td><td class="remarks-line"></td></tr>
            <tr><td></td><td class="remarks-line"></td></tr>
            <tr><td></td><td class="remarks-line"></td></tr>
        </table>
    </div>

    <div class="footer">
        <table>
            <tr>
                <td class="label">Rater Signature / Date:</td>
                <td class="line-wide">{{ $ors['rater_signature'] }}</td>
            </tr>
            <tr>
                <td class="label">Date returned by Rater to Ratee:</td>
                <td class="line-wide">{{ $ors['date_returned'] }}</td>
            </tr>
        </table>

    </div>

</body>
</html>
