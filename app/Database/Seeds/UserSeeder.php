<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\RawSql;
use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'email' => 'admin@admin.com',
            'password' => '$2y$10$5kQprWT3zRSN8tnEIl6CJu38KxCNMpPl2wFcWCHwkzaxwhkEg0bYK',
            'display_name' => 'Admin',
            'created_at' => new RawSql('NOW()'),
            'modified_at' => new RawSql('NOW()')
        ];

        $this->db->table('users')->insert($data);
    }
}
