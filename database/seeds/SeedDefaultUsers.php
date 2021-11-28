<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SeedDefaultUsers extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Sydney Moreno',
            'email' => 'admin@email.com',
            'contact_number' => '+639999931103',
            'password' => Hash::make('admin'),
            'type'  => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('users')->insert([
            'name' => 'Juan Dela Cruz',
            'email' => 'doctor@email.com',
            'contact_number' => '+639568130283',
            'password' => Hash::make('doctor'),
            'type'  => 2,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('users')->insert([
            'name' => 'Darcy Harris',
            'email' => 'patient@email.com',
            'contact_number' => '+639777855160',
            'password' => Hash::make('patient'),
            'type'  => 3,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('users')->insert([
            'name' => 'Kate Herrera',
            'email' => 'receptionist@email.com',
            'contact_number' => '+639999931103',
            'password' => Hash::make('receptionist'),
            'type'  => 4,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('users')->insert([
            'name' => 'Harding Lawrence',
            'email' => 'hr@email.com',
            'contact_number' => '+639553115882',
            'password' => Hash::make('hr123'),
            'type'  => 5,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('users')->insert([
            'name' => 'Zelene Vega',
            'email' => 'accounting@email.com',
            'contact_number' => '+639553115882',
            'password' => Hash::make('accounting'),
            'type'  => 6,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('users')->insert([
            'name' => 'Madeline Wright',
            'email' => 'cashier@email.com',
            'contact_number' => '+639553115882',
            'password' => Hash::make('cashier'),
            'type'  => 7,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('users')->insert([
            'name' => 'Nurse',
            'email' => 'nurse@email.com',
            'contact_number' => '+639553115882',
            'password' => Hash::make('nurse'),
            'type'  => 8,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        
    }
}
