<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Actions\External\SyncProvinceRajaOngkir as SyncProvinceRajaOngkirAction;

#[Signature('api:sync-province-raja-ongkir')]
#[Description('Pull data province from RajaOngkir API and sync to local database')]
class SyncProvinceRajaOngkir extends Command
{
    /**
     * Execute the console command.
     */
    
    protected $signature = 'api:sync-province-raja-ongkir';
    protected $description = 'Pull data province from RajaOngkir API and sync to local database';

    public function handle(SyncProvinceRajaOngkirAction $syncProvinceRajaOngkir)
    {
        $this->components->info('Starting to sync provinces from RajaOngkir API...');

        try {

            $microtime = microtime(true);
            $totalSaved = $syncProvinceRajaOngkir->execute();
            $executionTime = round(microtime(true) - $microtime, 2);

            $this->components->info("Successfully synced {$totalSaved} provinces from RajaOngkir API in {$executionTime} seconds.");

            return Command::SUCCESS; // Return a zero exit code to indicate success

        } catch (\Exception $e) {
            $this->components->error('Error occurred while syncing provinces: ' . $e->getMessage());
            return Command::FAILURE; // Return a non-zero exit code to indicate failure
        }
    }
}
