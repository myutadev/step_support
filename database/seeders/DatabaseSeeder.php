<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ])
        $this->call(AttendanceTypeSeeder::class);
        $this->call(CompanySeeder::class);
        $this->call(CounselorSeeder::class);
        $this->call(DisabilityCategorySeeder::class);
        $this->call(ResidenceSeeder::class);
        $this->call(ScheduleTypeSeeder::class);
        $this->call(WorkScheduleSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(AdminDetailSeeder::class);
    }
}
