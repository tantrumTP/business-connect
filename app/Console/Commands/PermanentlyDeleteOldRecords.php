<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Business;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;

class PermanentlyDeleteOldRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:permanently-delete-old-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete records deleted more than 30 days ago';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $thresholdDate = Carbon::now()->subDays(30);

        User::onlyTrashed()
            ->where('deleted_at', '<', $thresholdDate)
            ->forceDelete();

        Business::onlyTrashed()
            ->where('deleted_at', '<', $thresholdDate)
            ->forceDelete();

        Product::onlyTrashed()
            ->where('deleted_at', '<', $thresholdDate)
            ->forceDelete();

        Service::onlyTrashed()
            ->where('deleted_at', '<', $thresholdDate)
            ->forceDelete();

        $this->info('Old records deleted permanently.');
    }
}
