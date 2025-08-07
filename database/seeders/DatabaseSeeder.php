<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UnitKerja;
use App\Models\Kegiatan;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create default users (without role)
        User::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'nama_lengkap' => 'Administrator'
        ]);

        User::create([
            'username' => 'user1',
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
            'nama_lengkap' => 'User Demo 1'
        ]);

        User::create([
            'username' => 'user2',
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
            'nama_lengkap' => 'User Demo 2'
        ]);

        // Create unit kerja
        $unitKerja = [
            'IT & Technology',
            'Human Resources',
            'Finance & Accounting',
            'Marketing',
            'Operations',
            'Legal & Compliance'
        ];

        foreach ($unitKerja as $nama) {
            UnitKerja::create(['nama_unit_kerja' => $nama]);
        }

        // Create sample kegiatan
        $kegiatan = [
            [
                'nama_kegiatan' => 'Rapat Koordinasi Mingguan',
                'person_in_charge' => 'John Doe',
                'anggota' => 'Jane Smith, Bob Johnson, Alice Brown',
                'tanggal' => '2024-08-15',
                'jam_mulai' => '09:00',
                'jam_selesai' => '10:30',
                'nama_tempat' => 'Ruang Rapat A',
                'id_unit_kerja' => 1
            ],
            [
                'nama_kegiatan' => 'Training Laravel Development',
                'person_in_charge' => 'Sarah Wilson',
                'anggota' => 'Developer Team',
                'tanggal' => '2024-08-16',
                'jam_mulai' => '13:00',
                'jam_selesai' => '16:00',
                'nama_tempat' => 'Lab Komputer 1',
                'id_unit_kerja' => 1
            ],
            [
                'nama_kegiatan' => 'Interview Kandidat Baru',
                'person_in_charge' => 'HR Manager',
                'anggota' => 'HR Team, Department Head',
                'tanggal' => '2024-08-17',
                'jam_mulai' => '10:00',
                'jam_selesai' => '12:00',
                'nama_tempat' => 'Ruang Interview',
                'id_unit_kerja' => 2
            ],
            [
                'nama_kegiatan' => 'Presentasi Laporan Keuangan Q2',
                'person_in_charge' => 'Finance Director',
                'anggota' => 'Finance Team, Management',
                'tanggal' => '2024-08-18',
                'jam_mulai' => '14:00',
                'jam_selesai' => '15:30',
                'nama_tempat' => 'Ruang Boardroom',
                'id_unit_kerja' => 3
            ],
            [
                'nama_kegiatan' => 'Brainstorming Campaign Marketing',
                'person_in_charge' => 'Marketing Lead',
                'anggota' => 'Creative Team, Marketing Team',
                'tanggal' => '2024-08-19',
                'jam_mulai' => '09:30',
                'jam_selesai' => '11:00',
                'nama_tempat' => 'Creative Room',
                'id_unit_kerja' => 4
            ]
        ];

        foreach ($kegiatan as $item) {
            Kegiatan::create($item);
        }
    }
}