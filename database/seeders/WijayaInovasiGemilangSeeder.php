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
            ['name' => 'JOSE AMADEUS ABDI A.L.P', 'nik' => '30124004', 'position' => 'DIREKTUR UTAMA'],
            ['name' => 'DINDA BUDIARTI', 'nik' => '21120001', 'position' => 'LEAD OF FINANCE'],
            ['name' => 'RIKA NIDIAWATI', 'nik' => '31121001', 'position' => 'STAFF ACCOUNT RECIPABLE'],
            ['name' => 'TRI SAPTA MAHARDIKA', 'nik' => '30123001', 'position' => 'SPV CREATIF TEAM'],
            ['name' => 'SERLI INDRIANI', 'nik' => '30723002', 'position' => 'STAFF DOCUMENT CONTROL'],
            ['name' => 'HIZKIA YANUAR PAMBUDI', 'nik' => '31023003', 'position' => 'STAFF CONTENT CREATOR B2C'],
            ['name' => 'ANDHIKA YOGATAMA YANUAR', 'nik' => '31223001', 'position' => 'STAFF CONTENT CREATOR B2C'],
            ['name' => 'RIZKA ZAHARA', 'nik' => '31223006', 'position' => 'STAFF CONTENT CREATOR B2C'],
            ['name' => 'VITA OKTAVIARI', 'nik' => '30523001', 'position' => 'ADMIN MARKETPLACE'],
            ['name' => 'SYAHRIL QUDUS IBNU AHMAD', 'nik' => '30124007', 'position' => 'SPV FINANCE & TAX'],
            ['name' => 'WICAKSONO AJI PAMUNGKAS', 'nik' => '30124008', 'position' => 'SPV PACKAGING DESIGNER'],
            ['name' => 'SATRIA GANDA', 'nik' => '30324002', 'position' => 'STAFF WAREHOUSE'],
            ['name' => 'ANANG SISWANTO', 'nik' => '30524004', 'position' => 'MANAGER OPERASIONAL'],
            ['name' => 'ENDRU RISKI HERMANSYA', 'nik' => '30624001', 'position' => 'STAFF OPERASIONAL & MAINTENANCE'],
            ['name' => 'AHMAD NURROSAD', 'nik' => '30624002', 'position' => 'STAFF PPIC'],
            ['name' => 'RICKY ADITYA PERMANA', 'nik' => '30624005', 'position' => 'STAFF PURCHASING'],
            ['name' => 'REFO GANGGAWASA UTOMO', 'nik' => '30824003', 'position' => 'STAFF CONTENT CREATOR B2B'],
            ['name' => 'ARIS SUDARISMAN', 'nik' => '30924001', 'position' => 'STAFF SUPPLY PLANNER'],
            ['name' => 'DIMAS BHRANTA PUTERA ADI', 'nik' => '30724005', 'position' => 'LEAD OF OPERATION'],
            ['name' => 'RIANA KUSNIAWATI', 'nik' => '30924004', 'position' => 'SUPERVISOR QUALITY ASSURANCE'],
            ['name' => 'RAHADIYAN PURBA', 'nik' => '30924005', 'position' => 'Lead of HUMAN CAPITAL'],
            ['name' => 'RINDA MEKA BRAWATI', 'nik' => '30924006', 'position' => 'SUPERVISOR HUMAN CAPITAL'],
            ['name' => 'WAHYU AGUS WIDADI', 'nik' => '30924007', 'position' => 'STAFF GENERAL AFFAIR'],
            ['name' => 'LILIN INDAH KHANSA KHAIRUN', 'nik' => '30924011', 'position' => 'CS'],
            ['name' => 'DONI CIPTA RENADA', 'nik' => '31024058', 'position' => 'STAFF RND'],
            ['name' => 'AISYAH QUROTA AYUN', 'nik' => '31024060', 'position' => 'STAFF QC'],
            ['name' => 'FADETA ILHAN GANDHI', 'nik' => '31024059', 'position' => 'STAFF IT'],
            ['name' => 'RATRI YULIANA', 'nik' => '31124061', 'position' => 'Admin Purchasing'],
            ['name' => 'ZAIFUL RICHI NURROHMAT', 'nik' => '31124062', 'position' => 'Staff QC'],
            ['name' => 'Muhammad Fuad Al Khafiz', 'nik' => '31124063', 'position' => 'Formulator'],
            ['name' => 'AHMAD NAJIB', 'nik' => '31224064', 'position' => 'Formulator'],
            ['name' => 'KANIA GAYATRI', 'nik' => '30125065', 'position' => 'STAFF REGULATORY'],
            ['name' => 'RODHIYAH BINTI SHOLEHAH', 'nik' => '30125066', 'position' => 'STAFF REGULATORY'],
            ['name' => 'R. Ibnu Wicaksono Wibowo', 'nik' => '30125067', 'position' => 'STAFF DESIGN'],
            ['name' => 'Mochammad Yunus', 'nik' => '30125068', 'position' => 'Sales, Marketing & Business Development Manager'],
            ['name' => 'Wahyu Ghita Setiawan', 'nik' => '30125070', 'position' => 'Supervisor of Sales'],
            ['name' => 'Bayu Budi Prasetyo', 'nik' => '30225072', 'position' => 'Supervisor Operation n Maintenance'],
            ['name' => 'Salistya Adi Nugraha', 'nik' => '30225073', 'position' => 'STAFF DESIGN'],
            ['name' => 'Daffa Fakhuddin Arrozy', 'nik' => '30225074', 'position' => 'STAFF IT'],
            ['name' => 'Rofiul Fajri Kurniawan', 'nik' => '30425075', 'position' => 'STAFF ONM'],
            ['name' => 'Aryo Wicaksono', 'nik' => '30425076', 'position' => 'Market Research and Media Development'],
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
