<?php
// database/seeders/AdminUserSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'daffatgi02@gmail.com',
            'password' => Hash::make('daffa123'),
            'nik' => 'ADM001',
            'position' => 'IT Staff',
            'department' => 'IT',
            'is_admin' => true,
            'is_active' => true,
        ]);
    }
}
