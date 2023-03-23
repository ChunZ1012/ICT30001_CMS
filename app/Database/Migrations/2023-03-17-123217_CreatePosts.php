<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePosts extends Migration
{
    private $table_name = "posts";
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 255,
                'auto_increment' => true
            ],
            'title' => [
                'type' => 'nvarchar',
                'constraint' => '64'
            ],
            'content' => [
                'type' => 'LONGTEXT'
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
