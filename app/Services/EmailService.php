<?php

// app/Services/EmailService.php
namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Send email using template
     */
    public function mail(string $templateName, string $toEmail, array $variables = [], array $options = []): bool
    {
        try {
            // Get email template
            $template = EmailTemplate::where('name', $templateName)->first();

            if (!$template) {
                throw new \Exception("Email template '{$templateName}' not found");
            }

            // Configure SMTP settings
            $this->configureSmtp();

            // Replace variables in subject and body
            $subject = $this->replaceVariables($template->subject, $variables);
            $body = $this->replaceVariables($template->body, $variables);

            // Send email
            Mail::html($body, function (Message $message) use ($toEmail, $subject, $options) {
                $message->to($toEmail)
                    ->subject($subject);

                // Add CC if provided
                if (isset($options['cc'])) {
                    $message->cc($options['cc']);
                }

                // Add BCC if provided
                if (isset($options['bcc'])) {
                    $message->bcc($options['bcc']);
                }

                // Add reply-to if provided
                if (isset($options['reply_to'])) {
                    $message->replyTo($options['reply_to']);
                }
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send plain text email
     */
    public function sendPlain(string $toEmail, string $subject, string $body, array $options = []): bool
    {
        try {
            $this->configureSmtp();

            Mail::raw($body, function (Message $message) use ($toEmail, $subject, $options) {
                $message->to($toEmail)
                    ->subject($subject);

                if (isset($options['cc'])) {
                    $message->cc($options['cc']);
                }

                if (isset($options['bcc'])) {
                    $message->bcc($options['bcc']);
                }

                if (isset($options['reply_to'])) {
                    $message->replyTo($options['reply_to']);
                }
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send HTML email
     */
    public function sendHtml(string $toEmail, string $subject, string $htmlBody, array $options = []): bool
    {
        try {
            $this->configureSmtp();

            Mail::html($htmlBody, function (Message $message) use ($toEmail, $subject, $options) {
                $message->to($toEmail)
                    ->subject($subject);

                if (isset($options['cc'])) {
                    $message->cc($options['cc']);
                }

                if (isset($options['bcc'])) {
                    $message->bcc($options['bcc']);
                }

                if (isset($options['reply_to'])) {
                    $message->replyTo($options['reply_to']);
                }
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all available email templates
     */
    public function getTemplates(): \Illuminate\Database\Eloquent\Collection
    {
        return EmailTemplate::all();
    }

    /**
     * Get template by name
     */
    public function getTemplate(string $name): ?EmailTemplate
    {
        return EmailTemplate::where('name', $name)->first();
    }

    /**
     * Test SMTP configuration
     */
    public function testSmtp(string $testEmail): bool
    {
        return $this->sendPlain(
            $testEmail,
            'SMTP Configuration Test',
            'This is a test email from your SMTP configuration. If you receive this email, your SMTP settings are working correctly!'
        );
    }

    /**
     * Configure SMTP settings from database
     */
    private function configureSmtp(): void
    {
        $smtpConfig = Setting::getByType('smtp_config');

        if (empty($smtpConfig)) {
            throw new \Exception('SMTP configuration not found');
        }

        Config::set([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => $smtpConfig['host'] ?? '',
            'mail.mailers.smtp.port' => $smtpConfig['port'] ?? 587,
            'mail.mailers.smtp.username' => $smtpConfig['username'] ?? '',
            'mail.mailers.smtp.password' => $smtpConfig['password'] ?? '',
            'mail.mailers.smtp.encryption' => $smtpConfig['encryption'] === 'null' ? null : ($smtpConfig['encryption'] ?? 'tls'),
            'mail.from.address' => $smtpConfig['from_address'] ?? '',
            'mail.from.name' => $smtpConfig['from_name'] ?? '',

            'mail.mailers.smtp.stream' => [
                'ssl' => [
                    'allow_self_signed' => false,
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                ],
            ],
        ]);
    }

    /**
     * Replace variables in text with actual values
     */
    private function replaceVariables(string $text, array $variables): string
    {
        foreach ($variables as $key => $value) {
            // Support both {{variable}} and {variable} formats
            $text = str_replace(['{{ ' . $key . ' }}', '{{' . $key . '}}', '{' . $key . '}'], $value, $text);
        }

        return $text;
    }
}
