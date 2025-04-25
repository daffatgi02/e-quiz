<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateLanguageFiles extends Command
{
    protected $signature = 'lang:create';
    protected $description = 'Create language files for English and Indonesian';

    public function handle()
    {
        $languages = ['en', 'id'];

        foreach ($languages as $lang) {
            $path = resource_path("lang/{$lang}");
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            // Create quiz.php
            $quizContent = $this->getQuizTranslations($lang);
            File::put("{$path}/quiz.php", $quizContent);

            // Create general.php
            $generalContent = $this->getGeneralTranslations($lang);
            File::put("{$path}/general.php", $generalContent);
        }

        $this->info('Language files created successfully!');
    }

    private function getQuizTranslations($lang)
    {
        if ($lang === 'en') {
            return "<?php\n\nreturn [\n    'title' => 'Quiz Management',\n    'create' => 'Create Quiz',\n    'edit' => 'Edit Quiz',\n    'delete' => 'Delete Quiz',\n    'start' => 'Start Quiz',\n    'submit' => 'Submit Quiz',\n    'history' => 'Quiz History',\n    'questions' => 'Questions',\n    'duration' => 'Duration',\n    'minutes' => 'minutes',\n    'score' => 'Score',\n    'status' => 'Status',\n    'active' => 'Active',\n    'inactive' => 'Inactive',\n    'completed' => 'Completed',\n    'graded' => 'Graded',\n    'in_progress' => 'In Progress',\n    'time_remaining' => 'Time Remaining',\n    'question' => 'Question',\n    'answer' => 'Answer',\n    'correct_answer' => 'Correct Answer',\n    'points' => 'Points',\n    'total_points' => 'Total Points',\n    'passing_score' => 'Passing Score',\n    'result' => 'Result',\n    'passed' => 'Passed',\n    'failed' => 'Failed',\n    'essay_answer' => 'Essay Answer',\n    'multiple_choice' => 'Multiple Choice',\n    'essay' => 'Essay',\n    'mixed' => 'Mixed',\n    'manual_grading' => 'Manual Grading Required',\n    'grade_essay' => 'Grade Essay',\n];";
        } else {
            return "<?php\n\nreturn [\n    'title' => 'Manajemen Kuis',\n    'create' => 'Buat Kuis',\n    'edit' => 'Edit Kuis',\n    'delete' => 'Hapus Kuis',\n    'start' => 'Mulai Kuis',\n    'submit' => 'Kirim Kuis',\n    'history' => 'Riwayat Kuis',\n    'questions' => 'Pertanyaan',\n    'duration' => 'Durasi',\n    'minutes' => 'menit',\n    'score' => 'Nilai',\n    'status' => 'Status',\n    'active' => 'Aktif',\n    'inactive' => 'Tidak Aktif',\n    'completed' => 'Selesai',\n    'graded' => 'Dinilai',\n    'in_progress' => 'Sedang Berlangsung',\n    'time_remaining' => 'Waktu Tersisa',\n    'question' => 'Pertanyaan',\n    'answer' => 'Jawaban',\n    'correct_answer' => 'Jawaban Benar',\n    'points' => 'Poin',\n    'total_points' => 'Total Poin',\n    'passing_score' => 'Nilai Kelulusan',\n    'result' => 'Hasil',\n    'passed' => 'Lulus',\n    'failed' => 'Tidak Lulus',\n    'essay_answer' => 'Jawaban Essay',\n    'multiple_choice' => 'Pilihan Ganda',\n    'essay' => 'Essay',\n    'mixed' => 'Campuran',\n    'manual_grading' => 'Perlu Penilaian Manual',\n    'grade_essay' => 'Nilai Essay',\n];";
        }
    }

    private function getGeneralTranslations($lang)
    {
        if ($lang === 'en') {
            return "<?php\n\nreturn [\n    'welcome' => 'Welcome',\n    'dashboard' => 'Dashboard',\n    'users' => 'Users',\n    'logout' => 'Logout',\n    'login' => 'Login',\n    'save' => 'Save',\n    'cancel' => 'Cancel',\n    'edit' => 'Edit',\n    'delete' => 'Delete',\n    'view' => 'View',\n    'create' => 'Create',\n    'update' => 'Update',\n    'search' => 'Search',\n    'filter' => 'Filter',\n    'back' => 'Back',\n    'next' => 'Next',\n    'previous' => 'Previous',\n    'confirm' => 'Confirm',\n    'success' => 'Success',\n    'error' => 'Error',\n    'warning' => 'Warning',\n    'info' => 'Information',\n    'yes' => 'Yes',\n    'no' => 'No',\n    'actions' => 'Actions',\n    'status' => 'Status',\n    'active' => 'Active',\n    'inactive' => 'Inactive',\n    'name' => 'Name',\n    'nik' => 'NIK',\n    'position' => 'Position',\n    'department' => 'Department',\n    'language' => 'Language',\n    'english' => 'English',\n    'indonesian' => 'Indonesian',\n    'admin' => 'Admin',\n    'user' => 'User',\n    'profile' => 'Profile',\n    'settings' => 'Settings',\n    'reports' => 'Reports',\n    'download' => 'Download',\n    'export' => 'Export',\n    'import' => 'Import',\n    'print' => 'Print',\n    'total' => 'Total',\n    'average' => 'Average',\n    'minimum' => 'Minimum',\n    'maximum' => 'Maximum',\n    'statistics' => 'Statistics',\n    'date' => 'Date',\n    'time' => 'Time',\n    'details' => 'Details',\n    'description' => 'Description',\n    'type' => 'Type',\n    'email' => 'Email',\n    'password' => 'Password',\n    'confirm_password' => 'Confirm Password',\n    'remember_me' => 'Remember Me',\n    'forgot_password' => 'Forgot Password?',\n    'reset_password' => 'Reset Password',\n];";
        } else {
            return "<?php\n\nreturn [\n    'welcome' => 'Selamat Datang',\n    'dashboard' => 'Dasbor',\n    'users' => 'Peserta',\n    'logout' => 'Keluar',\n    'login' => 'Masuk',\n    'save' => 'Simpan',\n    'cancel' => 'Batal',\n    'edit' => 'Edit',\n    'delete' => 'Hapus',\n    'view' => 'Lihat',\n    'create' => 'Buat',\n    'update' => 'Perbarui',\n    'search' => 'Cari',\n    'filter' => 'Filter',\n    'back' => 'Kembali',\n    'next' => 'Berikutnya',\n    'previous' => 'Sebelumnya',\n    'confirm' => 'Konfirmasi',\n    'success' => 'Berhasil',\n    'error' => 'Kesalahan',\n    'warning' => 'Peringatan',\n    'info' => 'Informasi',\n    'yes' => 'Ya',\n    'no' => 'Tidak',\n    'actions' => 'Aksi',\n    'status' => 'Status',\n    'active' => 'Aktif',\n    'inactive' => 'Tidak Aktif',\n    'name' => 'Nama',\n    'nik' => 'NIK',\n    'position' => 'Posisi',\n    'department' => 'Departemen',\n    'language' => 'Bahasa',\n    'english' => 'Inggris',\n    'indonesian' => 'Indonesia',\n    'admin' => 'Admin',\n    'user' => 'Peserta',\n    'profile' => 'Profil',\n    'settings' => 'Pengaturan',\n    'reports' => 'Laporan',\n    'download' => 'Unduh',\n    'export' => 'Ekspor',\n    'import' => 'Impor',\n    'print' => 'Cetak',\n    'total' => 'Total',\n    'average' => 'Rata-rata',\n    'minimum' => 'Minimum',\n    'maximum' => 'Maksimum',\n    'statistics' => 'Statistik',\n    'date' => 'Tanggal',\n    'time' => 'Waktu',\n    'details' => 'Detail',\n    'description' => 'Deskripsi',\n    'type' => 'Tipe',\n    'email' => 'Email',\n    'password' => 'Kata Sandi',\n    'confirm_password' => 'Konfirmasi Kata Sandi',\n    'remember_me' => 'Ingat Saya',\n    'forgot_password' => 'Lupa Kata Sandi?',\n    'reset_password' => 'Reset Kata Sandi',\n];";
        }
    }
    // app/Http/Controllers/LanguageController.php
    public function switchLang($lang)
    {
        if (array_key_exists($lang, config('app.languages'))) {
            Session::put('applocale', $lang);
            App::setLocale($lang);

            if (auth()->check()) {
                /** @var \App\Models\User $user */
                $user = auth()->user();
                $user->language = $lang;
                $user->save();
            }
        }
        return redirect()->back();
    }
}
