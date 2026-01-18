<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Output Rating Sheet (ORS)</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 20mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 24px;
        }

        .header strong {
            display: block;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 6px 4px;
            vertical-align: bottom;
        }

        .label {
            width: 35%;
            font-weight: bold;
        }

        .line {
            border-bottom: 1px solid #000;
            height: 14px;
        }

        .section {
            margin-top: 18px;
        }

        .indent {
            padding-left: 20px;
        }

        .spacer {
            height: 10px;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <strong>PROVINCIAL HUMAN RESOURCE MANAGEMENT OFFICE</strong>
        <strong>OUTPUT RATING SHEET</strong>
    </div>

    <!-- BASIC INFO -->
    <table>
        <tr>
            <td class="label">Ratee’s Name:</td>
            <td class="line">{{ $ors['ratee'] }}</td>
        </tr>
        <tr>
            <td class="label">Output:</td>
            <td class="line">{{ $ors['output'] }}</td>
        </tr>
        <tr>
            <td class="label">Date Submitted by Ratee to Rater:</td>
            <td class="line">{{ $ors['date_submitted'] }}</td>
        </tr>
    </table>

    <!-- RATING SECTION -->
    <div class="section">
        <table>
            <tr>
                <td class="label">RATING:</td>
                <td></td>
            </tr>
            <tr>
                <td class="label indent">Quantity:</td>
                <td class="line">{{ $ors['quantity'] }}</td>
            </tr>
            <tr>
                <td class="label indent">Quality:</td>
                <td class="line">{{ $ors['quality'] }}</td>
            </tr>
            <tr>
                <td class="label indent">Timeliness:</td>
                <td class="line">{{ $ors['timeliness'] }}</td>
            </tr>
        </table>
    </div>

    <!-- REMARKS -->
    <div class="section">
        <table>
            <tr>
                <td class="label">Remarks:</td>
                <td class="line">{{ $ors['remarks'] }}</td>
            </tr>
            <tr><td colspan="2" class="line"></td></tr>
            <tr><td colspan="2" class="line"></td></tr>
            <tr><td colspan="2" class="line"></td></tr>
        </table>
    </div>

    <!-- SIGNATURES -->
    <div class="section">
        <table>
            <tr>
                <td class="label">Rater Signature / Date:</td>
                <td class="line">{{ $ors['rater_signature'] }} {{ $ors['rater_date'] }}</td>
            </tr>
            <tr>
                <td class="label">Date returned by Rater to Ratee:</td>
                <td class="line">{{ $ors['date_returned'] }}</td>
            </tr>
        </table>
    </div>

</body>
</html>
