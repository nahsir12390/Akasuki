<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
</head>
<body style="margin:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border:1px solid #fed7aa;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="padding:24px;background:linear-gradient(135deg,#f97316,#dc2626);color:#ffffff;">
                            <h1 style="margin:0;font-size:24px;line-height:1.25;">Akatsuki Devs</h1>
                            <p style="margin:6px 0 0;font-size:14px;">Password reset request</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <h2 style="margin:0 0 12px;font-size:20px;">Reset your password</h2>
                            <p style="margin:0 0 18px;line-height:1.7;color:#475569;">We received a request to reset your Akatsuki Devs password. This link expires in 60 minutes.</p>
                            <p style="margin:24px 0;">
                                <a href="{{ $link }}" style="display:inline-block;background:#f97316;color:#ffffff;text-decoration:none;font-weight:700;padding:12px 18px;border-radius:8px;">Reset Password</a>
                            </p>
                            <p style="margin:0 0 8px;line-height:1.7;color:#475569;">If the button does not work, copy and paste this link into your browser:</p>
                            <p style="word-break:break-all;margin:0;color:#ea580c;font-size:13px;">{{ $link }}</p>
                            <p style="margin:24px 0 0;line-height:1.7;color:#64748b;font-size:13px;">If you did not request this, you can ignore this email.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
