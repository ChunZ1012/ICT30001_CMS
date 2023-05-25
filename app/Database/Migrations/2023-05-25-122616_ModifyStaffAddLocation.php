<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyStaffAddLocation extends Migration
{
    private $table_name = 'staffs';
    public function up()
    {
        $this->forge->addColumn(
            $this->table_name,
            [
                'location' => [
                    'type' => 'varchar',
                    'constraint' => 80,
                    'null' => true
                ]
            ]
        );
    }

    public function down()
    {
        $this->forge->dropColumn(
            $this->table_name,
            [
                'location'
            ]
        );
    }
}
