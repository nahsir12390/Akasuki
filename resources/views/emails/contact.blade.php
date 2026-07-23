<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Message</title>
</head>
<body style="margin:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border:1px solid #fed7aa;border-radius:8px;overflow:hidden;">
                    <tr>
                        <td style="padding:24px;background:linear-gradient(135deg,#f97316,#dc2626);color:#ffffff;">
                            <h1 style="margin:0;font-size:24px;line-height:1.25;">New Akatsuki Devs Message</h1>
                            <p style="margin:6px 0 0;font-size:14px;">Contact form submission</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 10px;"><strong>Name:</strong> {{ $data['name'] }}</p>
                            <p style="margin:0 0 10px;"><strong>Email:</strong> <a href="mailto:{{ $data['email'] }}" style="color:#ea580c;">{{ $data['email'] }}</a></p>
                            <p style="margin:0 0 20px;"><strong>Subject:</strong> {{ $data['subject'] }}</p>
                            <div style="padding:18px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;">
                                <p style="margin:0 0 8px;font-weight:700;">Message</p>
                                <p style="margin:0;white-space:pre-line;line-height:1.7;color:#334155;">{{ $data['message'] }}</p>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
