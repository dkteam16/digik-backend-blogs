<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New newsletter subscriber</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Helvetica,Arial,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f5;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#ffffff;border-radius:10px;padding:28px 32px;">
                    <tr>
                        <td style="color:#333333;font-size:15px;line-height:1.6;">
                            <h2 style="margin:0 0 18px;font-size:17px;color:#2f2f2f;">New newsletter subscriber</h2>

                            <p style="margin:0 0 6px;"><strong>Email:</strong> {{ $subscriber->email }}</p>
                            <p style="margin:0 0 20px;"><strong>Subscribed:</strong> {{ $subscriber->created_at->format('M d, Y h:i A') }}</p>

                            <a href="{{ route('admin.newsletter.index') }}"
                               style="display:inline-block;background:#f0a020;color:#ffffff;text-decoration:none;
                                      font-weight:bold;font-size:13px;padding:10px 22px;border-radius:6px;">
                                View all subscribers
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
