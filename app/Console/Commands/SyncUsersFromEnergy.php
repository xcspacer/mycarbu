<?php

namespace App\Console\Commands;

use App\Mail\WelcomeUserFromEnergy;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SyncUsersFromEnergy extends Command
{
    protected $signature = 'energy:sync-users';

    protected $description = 'Synchronize users from Energy API';

    public function handle(): int
    {
        $energyApiUrl = config('services.energy_api.url');
        $energyApiToken = config('services.energy_api.token');

        $this->info('Fetching users from Energy API...');

        try {
            $response = Http::withHeaders([
                'X-API-Token' => $energyApiToken,
                'Accept' => 'application/json',
            ])->get("{$energyApiUrl}/api/fleet-customers");

            if (!$response->successful()) {
                $this->error('Failed to fetch users from Energy API');
                return Command::FAILURE;
            }

            $data = $response->json();

            if (!isset($data['success']) || !$data['success'] || !isset($data['data'])) {
                $this->error('Invalid response from Energy API');
                return Command::FAILURE;
            }

            $fleetCustomers = $data['data'];
            $created = 0;
            $updated = 0;
            $disabled = 0;

            foreach ($fleetCustomers as $fleetCustomer) {
                $user = User::where('email', $fleetCustomer['email'])->first();

                if (!$user) {
                    if (!$fleetCustomer['is_active']) {
                        continue;
                    }

                    $password = Str::random(12);
                    
                    $user = User::create([
                        'name' => $fleetCustomer['name'],
                        'email' => $fleetCustomer['email'],
                        'password' => bcrypt($password),
                        'role' => 'user',
                        'energy_fleet_customer_id' => $fleetCustomer['id'],
                        'is_active_from_energy' => $fleetCustomer['is_active'],
                        'email_verified_at' => now(),
                    ]);

                    Mail::to($user->email)->send(new WelcomeUserFromEnergy($user, $password));

                    $created++;
                    $this->info("Created user: {$user->email}");
                } else {
                    $nameChanged = $user->name !== $fleetCustomer['name'];
                    $statusChanged = $user->is_active_from_energy !== (bool) $fleetCustomer['is_active'];
                    $needsSave = false;

                    if ($nameChanged) {
                        $user->name = $fleetCustomer['name'];
                        $needsSave = true;
                    }

                    if ($user->energy_fleet_customer_id !== $fleetCustomer['id']) {
                        $user->energy_fleet_customer_id = $fleetCustomer['id'];
                        $needsSave = true;
                    }

                    if ($fleetCustomer['is_active']) {
                        if (!$user->is_active_from_energy) {
                            $user->is_active_from_energy = true;
                            $needsSave = true;
                        }
                        
                        if ($needsSave) {
                            $user->save();
                            $updated++;
                            $this->info("Updated user: {$user->email}");
                        }
                    } else {
                        if ($user->is_active_from_energy) {
                            $user->is_active_from_energy = false;
                            $user->save();
                            $disabled++;
                            $this->info("Disabled user: {$user->email}");
                        }
                    }
                }
            }

            $this->info("Sync completed. Created: {$created}, Updated: {$updated}, Disabled: {$disabled}");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error syncing users: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
