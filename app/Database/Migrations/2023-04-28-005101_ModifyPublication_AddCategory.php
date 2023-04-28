<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyPublicationAddCategory extends Migration
{
    private $table_name = 'publications';
    public function up()
    {
        $this->forge->addColumn(
            $this->table_name,
            [
                'category' => [
                    'type' => 'varchar',
                    'constraint' => 4,
                    'null' => false,
                    'default' => 'PAST'
                ]
            ]
        );
    }

    public function down()
    {
        $this->forge->dropColumn(
            $this->table_name,
            [
                'category'
            ]
        );
    }
}
