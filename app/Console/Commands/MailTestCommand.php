<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailTestCommand extends Command
{
    protected $signature = 'mail:test {email : Address to send the test message to}';

    protected $description = 'Send a test email to verify the SMTP configuration';

    public function handle(): int
    {
        $email = $this->argument('email');

        // $this->newLine();
        // $this->line('  Sending with:');
        // $this->table([], [
        //     ['mailer',   config('mail.default')],
        //     ['host',     config('mail.mailers.smtp.host')],
        //     ['port',     config('mail.mailers.smtp.port')],
        //     ['encrypt',  config('mail.mailers.smtp.encryption') ?: '(none)'],
        //     ['username', config('mail.mailers.smtp.username') ?: '(none)'],
        //     ['password', $this->maskedPassword()],
        //     ['from',     config('mail.from.address')],
        //     ['to',       $email],
        // ]);

        try {
            Mail::raw(
                "This is a test message from " . config('mail.from.name') . ".\n\n"
                . "If you are reading this, your SMTP settings are working.",
                fn ($message) => $message->to($email)->subject('SMTP test — ' . config('mail.from.name'))
            );
        } catch (Throwable $e) {
            $this->newLine();
            $this->error('  Send failed: ' . $e->getMessage());
            $this->hintFor($e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('  Sent. Check the inbox for ' . $email . ' (including spam).');

        if (config('mail.default') === 'log') {
            $this->warn('  MAIL_MAILER=log, so nothing was actually sent — see storage/logs/laravel.log.');
        }

        return self::SUCCESS;
    }

    private function maskedPassword(): string
    {
        $password = (string) config('mail.mailers.smtp.password');

        if ($password === '') {
            return '(none)';
        }

        return str_repeat('*', max(strlen($password) - 4, 0)) . substr($password, -4);
    }

    /**
     * Gmail's SMTP errors are terse, so map the common ones to the actual fix.
     */
    private function hintFor(string $error): void
    {
        $hint = match (true) {
            str_contains($error, '535')    => 'Gmail rejected the credentials. MAIL_PASSWORD must be a 16-char App Password from myaccount.google.com/apppasswords, not your normal Gmail password.',
            str_contains($error, '534')    => 'Gmail wants an App Password. Enable 2-Step Verification, then generate one at myaccount.google.com/apppasswords.',
            str_contains($error, 'Connection could not be established') => 'Could not reach the SMTP host. Check MAIL_HOST/MAIL_PORT, and whether a firewall or your ISP blocks outbound port 587.',
            str_contains($error, 'certificate') => 'TLS certificate verification failed — common on local Windows/XAMPP installs missing a CA bundle. Point curl.cainfo and openssl.cafile in php.ini at a cacert.pem.',
            default => null,
        };

        if ($hint) {
            $this->newLine();
            $this->line('  <comment>Hint:</comment> ' . $hint);
        }

        $this->line('  <comment>Note:</comment> run <info>php artisan config:clear</info> after editing .env.');
    }
}
