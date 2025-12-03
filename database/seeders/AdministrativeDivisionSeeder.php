<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdministrativeDivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Import provinces and wards from provinces.json
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding administrative divisions from provinces.json...');
        
        // Load provinces.json
        $jsonPath = public_path('data/provinces.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error('❌ File provinces.json not found at: ' . $jsonPath);
            return;
        }
        
        $provincesData = json_decode(File::get($jsonPath), true);
        
        if (!$provincesData) {
            $this->command->error('❌ Failed to parse provinces.json');
            return;
        }
        
        // Get Vietnam country ID
        $vietnamId = DB::table('countries')
            ->where('iso_code_2', 'VN')
            ->orWhere('country_name', 'like', '%Vietnam%')
            ->orWhere('country_name', 'like', '%Việt Nam%')
            ->value('id');
        
        if (!$vietnamId) {
            $this->command->error('❌ Vietnam not found in countries table. Please seed countries first.');
            return;
        }
        
        $provinceCount = 0;
        $wardCount = 0;
        
        // Process each province
        foreach ($provincesData as $provinceData) {
            try {
                // Insert province
                $provinceId = DB::table('administrative_divisions')->insertGetId([
                    'country_id' => $vietnamId,
                    'parent_id' => null,
                    'division_name' => $provinceData['name'],
                    'division_type' => 'province',
                    'code' => (string) $provinceData['code'],
                    'codename' => $provinceData['codename'],
                    'phone_code' => $provinceData['phone_code'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $provinceCount++;
                
                // Insert wards for this province
                if (isset($provinceData['wards']) && is_array($provinceData['wards'])) {
                    foreach ($provinceData['wards'] as $wardData) {
                        try {
                            DB::table('administrative_divisions')->insert([
                                'country_id' => $vietnamId,
                                'parent_id' => $provinceId,
                                'division_name' => $wardData['name'],
                                'division_type' => 'ward',
                                'code' => (string) $wardData['code'],
                                'codename' => $wardData['codename'] ?? null,
                                'short_codename' => $wardData['short_codename'] ?? null,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            
                            $wardCount++;
                        } catch (\Exception $e) {
                            // Skip duplicates
                            continue;
                        }
                    }
                }
                
                $this->command->info("  ✓ {$provinceData['name']}: {$wardCount} wards");
                
            } catch (\Exception $e) {
                $this->command->warn("  ⚠ Skipped province: {$provinceData['name']} - {$e->getMessage()}");
                continue;
            }
        }
        
        $this->command->newLine();
        $this->command->info("✅ Seeded {$provinceCount} provinces and {$wardCount} wards");
    }
}
