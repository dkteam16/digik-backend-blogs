<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New job application</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Helvetica,Arial,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f5;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border-radius:10px;overflow:hidden;">

                    <tr>
                        <td style="background:#2f2f2f;padding:24px 32px;">
                            <h1 style="margin:0;color:#ffffff;font-size:18px;letter-spacing:.5px;text-transform:uppercase;">
                                New job application
                            </h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:28px 32px;color:#333333;font-size:15px;line-height:1.6;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                @foreach ([
                                    'Name'     => $fields['name'],
                                    'Email'    => $fields['email'],
                                    'Mobile'   => $fields['mobile'] ?? null,
                                    'Interest' => $fields['area_of_interest'] ?? null,
                                ] as $label => $value)
                                    <tr>
                                        <td style="padding:6px 12px 6px 0;color:#8a8a8a;font-size:13px;white-space:nowrap;vertical-align:top;width:90px;">
                                            {{ $label }}
                                        </td>
                                        <td style="padding:6px 0;font-size:14px;color:#222222;">
                                            @if (filled($value))
                                                @if ($label === 'Email')
                                                    <a href="mailto:{{ $value }}" style="color:#d98314;text-decoration:none;">{{ $value }}</a>
                                                @elseif ($label === 'Mobile')
                                                    <a href="tel:{{ $value }}" style="color:#d98314;text-decoration:none;">{{ $value }}</a>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            @else
                                                <span style="color:#bbbbbb;">&mdash;</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>

                            <div style="margin-top:22px;padding:14px 18px;background:#fafafa;border-left:3px solid #f0a020;border-radius:4px;">
                                <div style="color:#8a8a8a;font-size:12px;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">
                                    CV attached
                                </div>
                                <div style="font-size:14px;color:#222222;">
                                    {{ $cv['filename'] }}
                                </div>
                            </div>

                            <p style="margin:22px 0 0;font-size:13px;color:#8a8a8a;">
                                Reply directly to this email to respond to {{ $fields['name'] }}.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
