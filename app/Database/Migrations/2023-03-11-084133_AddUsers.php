<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 255,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'email' => [
                'type' => 'nvarchar',
                'constraint' => 50,
                'unique' => true,
            ],
            'password' => [
                'type' => 'nvarchar',
                'constraint' => 255
            ],
            'display_name' => [
                'type' => 'nvarchar',
                'constraint' => 50
            ],
            'role' => [
                'type' => 'INT',
                'constraint' => 2,
                'default' => 1
            ],
            'created_at' => [
                'type' => 'timestamp',
                'null' => true
            ],
            'modified_at' => [
                'type' => 'timestamp',
                'null' => true
            ],
            'deleted_at' => [
                'type' => 'timestamp',
                'null' => true
            ]
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable(('users'));
    }
}
