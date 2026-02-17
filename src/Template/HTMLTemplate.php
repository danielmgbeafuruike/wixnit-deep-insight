<?php

    namespace Wixnit\DeepInsight\Template;

    class HTMLTemplate
    {
        public static function verification(string $otp): string
        {
            $content = <<<TEXT
                <!doctype html>
                <html lang="en">
                <head>
                <meta charset="utf-8"/>
                <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
                <title>Verify your email</title>
                </head>
                <body style="margin:0;padding:0;background-color:#f3f4f6;">
                <!-- Outer container -->
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#f3f4f6;padding:20px 0;">
                    <tr>
                    <td align="center">
                        <!-- Centered card -->
                        <table width="600" cellpadding="0" cellspacing="0" role="presentation" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 6px 18px rgba(15,23,42,0.08);">
                        <!-- Header -->
                        <tr>
                            <td style="padding:24px 28px;border-bottom:1px solid #e6e9ee;background-color:#ffffff;">
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td style="vertical-align:middle;">
                                        <!-- Logo -->
                                        <img src="https://deep-insight.alphacheq.com/logo.png" alt="Wixnit Deep Insight logo" width="120" style="display:block;border:0;outline:none;text-decoration:none;">
                                    </td>
                                    <td align="right" style="vertical-align:middle;font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;font-size:14px;color:#6b7280;">
                                        <!-- Optional short tagline -->
                                        <span style="display:inline-block;">DeepInsight</span>
                                    </td>
                                </tr>
                            </table>
                            </td>
                        </tr>

                        <!-- Body -->
                        <tr>
                            <td style="padding:32px 28px 16px 28px;font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#0f172a;">
                                <h1 style="margin:0 0 12px 0;font-size:20px;line-height:28px;font-weight:600;color:#0f172a;">
                                    Verify your email address
                                </h1>

                                <p style="margin:0 0 18px 0;font-size:15px;line-height:22px;color:#374151;">
                                    Hello,
                                </p>

                                <p style="margin:0 0 24px 0;font-size:15px;line-height:22px;color:#374151;">
                                    Thank you for creating an account with Wixnit Deep Insight. To complete your registration and activate your account, please verify your email address by clicking the button below. This link will expire in 30 mins.
                                </p>

                                <hr style="border:none;border-top:1px solid #eef2ff;margin:20px 0;">

                                <p style="margin:0;font-size:13px;line-height:20px;color:#6b7280;">
                                    If you did not request this email, you can safely ignore it. If you need help, contact us at
                                    <a href="mailto:deep-insight@wixnit.com" style="color:#044B7F;text-decoration:underline;">deep-insight@wixnit.com</a>.
                                </p>
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td style="padding:18px 28px 28px 28px;background-color:#f8fafc;border-top:1px solid #eef2ff;">
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td style="font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;font-size:13px;color:#6b7280;">
                                        <strong>Wixnit Deep Insight</strong><br>
                                        <a href="https://deep-insight.wixnit.com/privacy-policy" style="color:#6b7280;text-decoration:underline;">Privacy Policy</a> &nbsp; | &nbsp;
                                        <a href="https://deep-insight.wixnit.com/terms-of-service" style="color:#6b7280;text-decoration:underline;">Terms</a>
                                    </td>
                                </tr>
                            </table>
                            </td>
                        </tr>

                        </table>

                        <!-- Small legal / unsubscribe line -->
                        <table width="600" cellpadding="0" cellspacing="0" role="presentation" style="max-width:600px;width:100%;margin-top:12px;">
                            <tr>
                                <td align="center" style="font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;font-size:12px;color:#9ca3af;">
                                You received this email because you are trying to connect to a Wixnit Deep Insight Client. If you didn't, you can ignore this message.
                                </td>
                            </tr>
                        </table>

                    </td>
                    </tr>
                </table>
                </body>
                </html>
                TEXT;

            return $content;
        }

        public static function verificationAlt(string $otp): string
        {
            $context = <<<TEXT
                Subject: Verify your email address for Wixnit Deep Insight

                Hello,

                Thank you for creating an account with Wixnit Deep Insight.
                To complete your registration, verify your email by opening the link below (expires in 30 mins):

                $otp

                If you did not request this email, no action is required.
                For help, contact: support@deep-insight.wixnit.com
                TEXT;

            return $context;
        }
    }