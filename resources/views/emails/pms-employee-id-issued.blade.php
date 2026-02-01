<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your PMS Employee ID</title>
</head>
<body style="background:#f4f4f4;color:#111;font-family:system-ui,-apple-system,Segoe UI,sans-serif;margin:0;padding:0;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center" style="padding:2rem;">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width:600px;background:#fff;border-radius:12px;padding:2rem;box-shadow:0 10px 25px rgba(0,0,0,.1);">
                    <tr>
                        <td>
                            <h1 style="margin:0 0 1rem;font-size:1.5rem;">Hello {{ $name }},</h1>
                            <p style="margin:0 0 1rem;font-size:1rem;">Thank you for joining the PMS family. Below are the details you will need to activate your account.</p>
                            <p style="margin:0 0 .5rem;font-size:.95rem;"><strong>Employee ID:</strong> {{ $employeeId }}</p>
                            <p style="margin:0 0 1rem;font-size:.95rem;"><strong>Email address:</strong> {{ $email }}</p>
                            <p style="margin:0 0 1rem;font-size:.95rem;">Use the Employee ID above with this email address to verify your identity in the PMS activation modal.</p>
                            <ul style="margin:0 0 1.25rem;padding-left:1.2rem;font-size:.95rem;line-height:1.5;">
                                <li>Step 1: Enter your Employee ID and email to verify your identity.</li>
                                <li>Step 2: Set a secure password, then log in to explore the dashboard.</li>
                            </ul>
                            <p style="margin:0 0 1rem;font-size:.95rem;">If you have already activated your account, kindly disregard this message.</p>
                            <p style="margin:0;font-size:.85rem;color:#666;">Need help? Contact HR for assistance.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
