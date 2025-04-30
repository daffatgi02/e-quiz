<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            ['name' => 'Achmad Rizal Syafii', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Adji Saputra', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Agus Suryo Wihandoko', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Ahmad Catur Nugroho', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Andri Widyatmoko', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Aprilia Novianti', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Ardi Firda Pranantya', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Denny Iryanto', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Dimas Setyo Nugroho', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Donny Erlangga', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Febriana Yossy Savitri', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Fuad Yusuf Abdilah', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Hanif Ainul Yaqin', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Hildha Reva Aryanti', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Mahendra Yoga Krisnanto', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Muh Alfarizi Prabowo', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Muh. Wianantoni Zainal. A', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Muhammad Fahril Mantovani', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Nasywa Nora Shada', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Oky Tagrit Septiawan', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Puput Susanti', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Putra Imanuel', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Reni Karuniasih', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Ririn Monica Sari', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Risqi Arif Setiawan', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Rizqi Margi Amalia', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Robby Febrian', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Solichah', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Sri Wahyuni', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Vitoo Muhammad Andrian', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Yuda Bagus Samudra', 'position' => 'PRODUKSI', 'department' => 'OPERATION'],
            ['name' => 'Gilang Akbar Mauliddian', 'position' => 'GA. SUPPORT', 'department' => 'HCGA'],
            ['name' => 'Andy Anuari', 'position' => 'GA. SUPPORT', 'department' => 'HCGA'],
            ['name' => 'Muh Burhan Asidiq', 'position' => 'GA. SUPPORT', 'department' => 'HCGA'],
            ['name' => 'Muh Ismail', 'position' => 'GA. SUPPORT', 'department' => 'HCGA'],
            ['name' => 'Alvien Pratama Kurniawan', 'position' => 'GUDANG', 'department' => 'OPERATION'],
            ['name' => 'Bambino Magnifico', 'position' => 'SECURITY', 'department' => 'HCGA'],
            ['name' => 'Guntur Syauma Wibowo', 'position' => 'SECURITY', 'department' => 'HCGA'],
            ['name' => 'Toni Sulistiawan', 'position' => 'SECURITY', 'department' => 'HCGA'],
        ];

        foreach ($users as $index => $userData) {
            // Create email from name (lowercase, no spaces) + domain
            $email = strtolower(str_replace(' ', '', $userData['name'])) . '@dummy.com';

            // Create unique NIK with USR + sequential number
            $nik = 'USR' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            // Create user with login_token
            $user = User::create([
                'name' => $userData['name'],
                'email' => $email,
                'password' => Hash::make(Str::random(16)), // Random password
                'nik' => $nik,
                'position' => $userData['position'],
                'department' => $userData['department'],
                'perusahaan' => 'PT AGTA MANDIRI KONSULTAN', // Set company name
                'is_admin' => false,
                'is_active' => true,
            ]);

            // Generate login token
            $this->generateToken($user);
        }
    }

    private function generateToken($user)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $part1 = '';
        $part2 = '';

        // Generate 4 random letters for each part
        for ($i = 0; $i < 4; $i++) {
            $part1 .= $characters[rand(0, strlen($characters) - 1)];
            $part2 .= $characters[rand(0, strlen($characters) - 1)];
        }

        $user->login_token = $part1 . $part2;
        $user->token_issued_at = now();
        $user->save();

        return $user;
    }
}
