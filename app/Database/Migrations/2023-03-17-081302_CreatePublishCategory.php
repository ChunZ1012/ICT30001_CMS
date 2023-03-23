<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePublishCategory extends Migration
{
    private $table_name = 'publication_category';
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 255,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'shortcode' => [
                'type' => 'varchar',
                'constraint' => 10,
                'unique' => true
            ],
            'name' => [
                'type' => 'varchar',
                'constraint' => '255'
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
            'deleted_at' => [
                'type' => 'timestamp',
                'null' => true
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
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('modified_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable($this->table_name, true);
    }

    public function down()
    {
        // Drop table if exist
        $this->forge->dropTable($this->table_name, true);
    }
}
