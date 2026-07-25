<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Actions\External\SyncDistrictRajaOngkir as SyncDistrictRajaOngkirAction;

#[Signature('api:sync-district-raja-ongkir')]
#[Description('Pull data district from RajaOngkir API and sync to local database')]
class SyncDistrictRajaOngkir extends Command
{
    /**
     * Execute the console command.
     */

    protected $signature = 'api:sync-district-raja-ongkir';
    protected $description = 'Pull data district from RajaOngkir API and sync to local database';

    public function handle(SyncDistrictRajaOngkirAction $syncDistrictRajaOngkir)
    {
        $this->components->info('Starting to sync districts from RajaOngkir API...');

        try {

            $microtime = microtime(true);
            $totalSaved = $syncDistrictRajaOngkir->execute();
            $executionTime = round(microtime(true) - $microtime, 2);

            $this->components->info("Successfully synced {$totalSaved} districts from RajaOngkir API in {$executionTime} seconds.");

            return Command::SUCCESS; // Return a zero exit code to indicate success

        } catch (\Exception $e) {
            $this->components->error('Error occurred while syncing districts: ' . $e->getMessage());
            return Command::FAILURE; // Return a non-zero exit code to indicate failure
        }
    }
}
