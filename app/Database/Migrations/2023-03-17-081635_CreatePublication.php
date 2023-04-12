<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePublication extends Migration
{
    private $table_name = 'publications';
    public function up()
    {        
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 255,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'title' => [
                'type' => 'nvarchar',
                'constraint' => '255',
            ],
            'published_time' => [
                'type' => 'timestamp',
                'default' => new RawSql('CURRENT_TIMESTAMP')
            ],
            'is_active' => [
                'type' => 'BIT',
                'constraint' => 1,
                'default' => 1
            ],
            'cover' => [
                'type' => 'nvarchar',
                'constraint' => '255'
            ],
            'pdf' => [
                'type' => 'nvarchar',
                'constraint' => 255
            ],
            'created_at' => [
                'type'=> 'timestamp',
                'null' => true,
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
        $this->forge->createTable($this->table_name, true);
    }

    public function down()
    {
        $this->forge->dropTable($this->table_name, true);
    }
}