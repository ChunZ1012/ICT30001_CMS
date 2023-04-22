<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStaffs extends Migration
{
    private $table_name = 'staffs';
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 255,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'name' => [
                'type' => 'nvarchar',
                'constraint' => 160,
                'null' => false
            ],
            'age' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => false,
                'unsigned' => true
            ],
            'gender' => [
                'type' => 'varchar',
                'constraint' => 1,
                'null' => false
            ],
            'avatar' => [
                'type' => 'varchar',
                'constraint' => 48,
                'null' => true
            ],
            'contact' => [
                'type' => 'varchar',
                'constraint' => 12,
                'null' => true
            ],
            'email' => [
                'type' => 'varchar',
                'constraint' => 60,
                'null' => true
            ],
            'office_contact' => [
                'type' => 'varchar',
                'constraint' => 10,
                'null' => true
            ],
            'office_fax' => [
                'type' => 'varchar',
                'constraint' => 10,
                'null' => true
            ],
            'created_at' => [
                'type'=> 'timestamp',
                'null' => true
            ],
            'modified_at' => [
                'type' => 'timestamp',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'BIGINT',
                'constraint' => 255,
                'unsigned' => true
            ],
            'modified_by' => [
                'type' => 'BIGINT',
                'constraint' => 255,
                'unsigned' => true,
                'null' => true
            ]
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('modified_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable($this->table_name);
    }

    public function down()
    {
        $this->forge->dropTable($this->table_name);
    }
}
