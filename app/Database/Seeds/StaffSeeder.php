<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\RawSql;
use CodeIgniter\Database\Seeder;

class StaffSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'image' => 'BongImg.jpg',
            'name' => 'Bong Xing Qiu',
            'contact' => '0168495867',
            'email' => 'bong@gmail.com',
            'position' => 'Head',
            'location' => 'jalan.rock',
            'created_at' => new RawSql('NOW()'),
            'modified_at' => new RawSql('NOW()'),
            'created_by' => 1,
            'modified_by' => 1
        ];
        $data1 = [
            'image' => 'SongImg.jpg',
            'name' => 'Song Xing Qiu',
            'contact' => '0178495868',
            'email' => 'song@gmail.com',
            'position' => 'Kepala',
            'location' => 'jalan.batu',
            'created_at' => new RawSql('NOW()'),
            'modified_at' => new RawSql('NOW()'),
            'created_by' => 1,
            'modified_by' => 1
        ];
        $data2 = [
            'image' => 'DongImg.jpg',
            'name' => 'Dong Xing Qiu',
            'contact' => '0188495869',
            'email' => 'dong@gmail.com',
            'position' => 'tou',
            'location' => 'jalan.shi',
            'created_at' => new RawSql('NOW()'),
            'modified_at' => new RawSql('NOW()'),
            'created_by' => 1,
            'modified_by' => 1
        ];
        $data3 = [
            'image' => 'AongImg.jpg',
            'name' => 'Aong Xing Qiu',
            'contact' => '0188495869',
            'email' => 'Aong@gmail.com',
            'position' => 'tou',
            'location' => 'jalan.ashi',
            'created_at' => new RawSql('NOW()'),
            'modified_at' => new RawSql('NOW()'),
            'created_by' => 1,
            'modified_by' => 1
        ];
        $data4 = [
            'image' => 'CongImg.jpg',
            'name' => 'Cong Xing Qiu',
            'contact' => '0188495869',
            'email' => 'cong@gmail.com',
            'position' => 'tou',
            'location' => 'jalan.cshi',
            'created_at' => new RawSql('NOW()'),
            'modified_at' => new RawSql('NOW()'),
            'created_by' => 1,
            'modified_by' => 1
        ];
        $data5 = [
            'image' => 'ZongImg.jpg',
            'name' => 'Zong Xing Qiu',
            'contact' => '0188495869',
            'email' => 'zong@gmail.com',
            'position' => 'tou',
            'location' => 'jalan.zshi',
            'created_at' => new RawSql('NOW()'),
            'modified_at' => new RawSql('NOW()'),
            'created_by' => 1,
            'modified_by' => 1
        ];

        $this->db->table('staffs')->insert($data);
        $this->db->table('staffs')->insert($data1);
        $this->db->table('staffs')->insert($data2);
        $this->db->table('staffs')->insert($data3);
        $this->db->table('staffs')->insert($data4);
        $this->db->table('staffs')->insert($data5);
    }
}
    
