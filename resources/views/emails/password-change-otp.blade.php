<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Update</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family:Arial, Helvetica, sans-serif; color:#111827;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f4f6; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px; width:100%; background:#ffffff; border-radius:8px; overflow:hidden; border:1px solid #e5e7eb;">
                    <tr>
                        <td style="background:#1e3a8a; color:#ffffff; font-size:26px; font-weight:700; text-align:center; padding:24px;">
                            Security Update
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px; font-size:16px; line-height:1.6;">
                            <p style="margin:0 0 16px;">Hello,</p>
                            <p style="margin:0 0 16px;">We received a request to reset your password for your {{ $appName }} account.</p>

                            <div style="border-left:4px solid #2563eb; background:#eff6ff; padding:14px 16px; margin:16px 0 20px;">
                                <p style="margin:0 0 8px; font-size:14px; color:#1f2937;">Use this one-time password (OTP):</p>
                                <p style="margin:0; font-size:28px; font-weight:700; letter-spacing:4px; color:#1e3a8a;">{{ $otp }}</p>
                            </div>

                            <p style="margin:0 0 16px;">This OTP expires in {{ $expiresInMinutes }} minutes.</p>
                            <p style="margin:0 0 16px;">If you did not request this, you can safely ignore this email.</p>

                            <div style="background:#fef2f2; border-radius:6px; padding:12px 14px; color:#b91c1c; font-size:14px; margin-top:8px;">
                                <strong>Didn't make this request?</strong><br>
                                Contact support immediately to secure your account.
                            </div>

                            <p style="margin:24px 0 0;">Best regards,<br><strong>{{ $appName }} Administration</strong></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
