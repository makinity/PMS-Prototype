<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Output Rating Sheet (Stage II – Monitoring Copy)</title>
    <style>
        @page { size: A4 portrait; margin: 18mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #000; }
        .header { text-align: center; margin-bottom: 10px; }
        .header strong { display: block; }
        .title { margin: 8px 0 6px; font-size: 14px; font-weight: 700; text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 4px 4px; vertical-align: top; }
        .label { width: 32%; font-weight: bold; }
        .line { border-bottom: 1px solid #000; min-height: 14px; }
        .line-wide { border-bottom: 1px solid #000; min-height: 18px; }
        .section { margin-top: 12px; }
        .muted { font-size: 10px; color: #444; }
        .spacer { height: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <strong>Republic of the Philippines</strong>
        <strong style="font-size:13px;">PROVINCE OF DAVAO DEL SUR</strong>
        <strong>Matti, Digos City</strong>
        <strong style="margin-top:8px; font-size:12px;">PROVINCIAL HUMAN RESOURCE MANAGEMENT OFFICE</strong>
        <div class="title">OUTPUT RATING SHEET</div>
        <div class="muted">(Stage II – Monitoring Copy | Read-only)</div>
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

    <div class="spacer"></div>

    <table>
        <tr>
            <td class="label">RATING:</td>
            <td style="width:8%;"></td>
            <td class="label" style="width:20%;">Quantity:</td>
            <td class="line">{{ $ors['quantity'] }}</td>
        </tr>
        <tr>
            <td class="label"></td>
            <td></td>
            <td class="label">Quality:</td>
            <td class="line">{{ $ors['quality'] }}</td>
        </tr>
        <tr>
            <td class="label"></td>
            <td></td>
            <td class="label">Timeliness:</td>
            <td class="line">{{ $ors['timeliness'] }}</td>
        </tr>
    </table>
    <div class="muted" style="margin-top:4px;">Rating fields are completed during Stage III – Performance Review.</div>

    <div class="spacer"></div>

    <table>
        <tr>
            <td class="label">Remarks / Actual Accomplishment:</td>
            <td class="line-wide">{{ $ors['remarks'] }}</td>
        </tr>
        <tr><td></td><td class="line-wide"></td></tr>
        <tr><td></td><td class="line-wide"></td></tr>
        <tr><td></td><td class="line-wide"></td></tr>
    </table>

    <div class="spacer"></div>

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

    <div class="section">
        <div class="muted">
            This document is a system-generated, read-only monitoring copy.<br>
            Generated from ORS for Stage II – Performance Monitoring and Coaching.<br>
            It does not constitute validation, approval, or performance rating.
        </div>
    </div>

</body>
</html>
