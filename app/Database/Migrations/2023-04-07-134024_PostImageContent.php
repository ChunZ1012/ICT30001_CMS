<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PostImageContent extends Migration
{
    private $table_name = 'post_image_content';
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 255,
                'auto_increment' => true,
                'unsigned' => true
            ],
            'post_id' => [
                'type' => 'BIGINT',
                'constraint' => 255
            ],
            'path' => [
                'type' => 'LONGTEXT',
                'null' => true
            ],
            'description' => [
                'type' => 'varchar',
                'constraint' => 128,
                'null' => true
            ],
            'content' => [
                'type' => 'LONGTEXT',
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
        $this->forge->addForeignKey('post_id', 'posts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('modified_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable($this->table_name);
    }

    public function down()
    {
        $this->forge->dropTable($this->table_name);
    }
}
