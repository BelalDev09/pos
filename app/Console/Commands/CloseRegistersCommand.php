<?php

namespace App\Console\Commands;

use App\Models\PosSession;
use Illuminate\Console\Command;

class CloseRegistersCommand extends Command
{
    protected $signature   = 'pos:close-registers';
    protected $description = 'Auto-close any POS sessions that have been open for more than 24 hours';

    public function handle(): int
    {
        $this->info('Checking for stale POS sessions...');

        $staleSessions = PosSession::where('status', 'open')
            ->where('opened_at', '<', now()->subHours(24))
            ->get();

        foreach ($staleSessions as $session) {
            $session->update([
                'status'    => 'closed',
                'closed_at' => now(),
                'notes'     => 'Auto-closed by system after 24h',
            ]);
            $this->line("  Closed session #{$session->id} for register {$session->register_id}");
        }

        $this->info("Closed {$staleSessions->count()} stale sessions.");
        return Command::SUCCESS;
    }
}
