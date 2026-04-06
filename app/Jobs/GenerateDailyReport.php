<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Services\Report\SalesReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateDailyReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $queue   = 'reports';
    public int    $timeout = 120;

    public function __construct(
        private readonly int    $tenantId,
        private readonly string $date
    ) {}

    public function handle(SalesReportService $reportService): void
    {
        $reportService->generateDailySummary($this->tenantId, $this->date);
    }
}
