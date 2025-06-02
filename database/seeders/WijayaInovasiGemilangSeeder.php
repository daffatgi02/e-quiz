<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WijayaInovasiGemilangSeeder extends Seeder
{
    public function run()
    {
        $users = [
            ['name' => 'RAHADIYAN PURBA', 'nik' => '30924005', 'position' => 'Lead of HUMAN CAPITAL'],
            ['name' => 'Daffa Fakhuddin Arrozy', 'nik' => '30225074', 'position' => 'STAFF IT'],
            ['name' => 'Rofiul Fajri Kurniawan', 'nik' => '30425075', 'position' => 'STAFF ONM'],
            ['name' => 'SERLI INDRIANI', 'nik' => '30723002', 'position' => 'STAFF DOCUMENT CONTROL'],
            ['name' => 'AHMAD NAJIB', 'nik' => '31224064', 'position' => 'Formulator'],
            ['name' => 'AISYAH QUROTA AYUN', 'nik' => '31024060', 'position' => 'STAFF QC'],
            ['name' => 'DONI CIPTA RENADA', 'nik' => '31024058', 'position' => 'STAFF RND'],
            ['name' => 'KANIA GAYATRI', 'nik' => '30125065', 'position' => 'STAFF REGULATORY'],
            ['name' => 'Muhammad Fuad Al Khafiz', 'nik' => '31124063', 'position' => 'Formulator'],
            ['name' => 'RODHIYAH BINTI SHOLEHAH', 'nik' => '30125066', 'position' => 'STAFF REGULATORY'],
            ['name' => 'DIMAS BHRANTA PUTERA ADI', 'nik' => '30724005', 'position' => 'LEAD OF OPERATION'],
            ['name' => 'SYAHRIL QUDUS IBNU AHMAD', 'nik' => '30124007', 'position' => 'SPV FINANCE & TAX'],
            ['name' => 'RIANA KUSNIAWATI', 'nik' => '30924004', 'position' => 'SUPERVISOR QUALITY ASSURANCE'],
            ['name' => 'RATRI YULIANA', 'nik' => '31124061', 'position' => 'Admin Purchasing'],
            ['name' => 'DINDA BUDIARTI', 'nik' => '21120001', 'position' => 'LEAD OF FINANCE'],
            ['name' => 'LILIN INDAH KHANSA KHAIRUN', 'nik' => '30924011', 'position' => 'CS'],
            ['name' => 'RIKA NIDIAWATI', 'nik' => '31121001', 'position' => 'STAFF ACCOUNT RECIPABLE'],
            ['name' => 'WAHYU AGUS WIDADI', 'nik' => '30924007', 'position' => 'STAFF GENERAL AFFAIR'],
            ['name' => 'WICAKSONO AJI PAMUNGKAS', 'nik' => '30124008', 'position' => 'SPV PACKAGING DESIGNER'],
            ['name' => 'ANDHIKA YOGATAMA YANUAR', 'nik' => '31223001', 'position' => 'STAFF CONTENT CREATOR B2C'],
            ['name' => 'ZAIFUL RICHI NURROHMAT', 'nik' => '31124062', 'position' => 'Staff QC'],
        ];

        foreach ($users as $userData) {
            // Create email from name (lowercase, no spaces) + domain
            $email = strtolower(str_replace(' ', '', $userData['name'])) . '@wijayainovasi.com';

            // Create user with login_token
            $user = User::create([
                'name' => $userData['name'],
                'email' => $email,
                'password' => Hash::make(Str::random(16)), // Random password
                'nik' => $userData['nik'],
                'position' => $userData['position'],
                'department' => '', // Empty department as requested
                'perusahaan' => 'PT WIJAYA INOVASI GEMILANG',
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