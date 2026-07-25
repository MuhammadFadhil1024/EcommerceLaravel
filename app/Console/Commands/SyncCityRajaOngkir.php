<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Actions\External\SyncCityRajaOngkir as SyncCityRajaOngkirAction;

#[Signature('api:sync-city-raja-ongkir')]
#[Description('Pull data city from RajaOngkir API and sync to local database')]
class SyncCityRajaOngkir extends Command
{
    /**
     * Execute the console command.
     */

    protected $signature = 'api:sync-city-raja-ongkir';
    protected $description = 'Pull data city from RajaOngkir API and sync to local database';

    public function handle(SyncCityRajaOngkirAction $syncCityRajaOngkir)
    {
        $this->components->info('Starting to sync cities from RajaOngkir API...');

        try {

            $microtime = microtime(true);
            $totalSaved = $syncCityRajaOngkir->execute();
            $executionTime = round(microtime(true) - $microtime, 2);

            $this->components->info("Successfully synced {$totalSaved} cities from RajaOngkir API in {$executionTime} seconds.");

            return Command::SUCCESS; // Return a zero exit code to indicate success

        } catch (\Exception $e) {
            $this->components->error('Error occurred while syncing cities: ' . $e->getMessage());
            return Command::FAILURE; // Return a non-zero exit code to indicate failure
        }
    }
}
