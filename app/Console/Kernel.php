<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Publish scheduled posts every minute
        $schedule->call(function () {
            \App\Models\Post::where('status', 'scheduled')
                ->where('published_at', '<=', now())
                ->update(['status' => 'published']);
        })->everyMinute()->name('publish-scheduled-posts');

        // Clean up expired tokens daily
        $schedule->command('sanctum:prune-expired --hours=24')->daily();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
