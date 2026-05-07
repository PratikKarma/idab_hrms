<?php

namespace App\Console\Commands;

use App\Http\Controllers\ShiftTurnerController;
use Illuminate\Console\Command;

class AutoShiftRotate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:shift-rotate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically rotate shifts for employees based on shift turner settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Running auto:shift-rotate command...');
        $controller = app(ShiftTurnerController::class);

        $creatorIds = \App\Models\ShiftTurner::distinct('created_by')->pluck('created_by');

        foreach ($creatorIds as $creatorId) {
            $controller->autoShiftRotationByCreator($creatorId);
        }

        $this->info('Auto shift rotation executed successfully.');

        return 0;
    }
}
