<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thanks for subscribing</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Helvetica,Arial,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f5;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:10px;overflow:hidden;">

                    <tr>
                        <td style="background:#2f2f2f;padding:28px 32px;">
                            <h1 style="margin:0;color:#ffffff;font-size:20px;letter-spacing:.5px;text-transform:uppercase;">
                                You're subscribed
                            </h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px;color:#333333;font-size:15px;line-height:1.6;">
                            <p style="margin:0 0 16px;">Hi there,</p>

                            <p style="margin:0 0 16px;">
                                Thanks for signing up for the {{ config('mail.from.name') }} newsletter with
                                <strong>{{ $subscriber->email }}</strong>.
                            </p>

                            <p style="margin:0 0 24px;">
                                We'll send you new articles, job openings and updates as they go live.
                                No spam &mdash; and you can unsubscribe any time by replying to this email.
                            </p>

                            <a href="{{ config('app.url') }}"
                               style="display:inline-block;background:#f0a020;color:#ffffff;text-decoration:none;
                                      font-weight:bold;font-size:14px;padding:12px 28px;border-radius:6px;
                                      text-transform:uppercase;letter-spacing:.5px;">
                                Visit the blog
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 32px;background:#fafafa;border-top:1px solid #ececec;
                                   color:#8a8a8a;font-size:12px;line-height:1.5;">
                            You received this email because {{ $subscriber->email }} was entered into the
                            newsletter form on our website.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
