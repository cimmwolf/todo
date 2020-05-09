<?php

use yii\db\{Migration, Schema};

/**
 * Class m200508_153555_init_database
 */
class m200508_153555_init_database extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(
            'user',
            [
                'id'       => Schema::TYPE_PK,
                'username' => Schema::TYPE_STRING . ' NOT NULL',
                'password' => Schema::TYPE_STRING . ' NOT NULL',
                'token'    => Schema::TYPE_STRING,
                'role'     => Schema::TYPE_STRING . ' DEFAULT user'
            ]
        );
        $this->createTable(
            'note',
            [
                'id'     => Schema::TYPE_PK,
                'name'   => 'VARCHAR(60) NOT NULL',
                'userId' => Schema::TYPE_INTEGER . ' NOT NULL',
            ]
        );

        $this->createTable(
            'todo',
            [
                'id'     => Schema::TYPE_PK,
                'noteId' => Schema::TYPE_INTEGER . ' NOT NULL',
                'name'   => Schema::TYPE_STRING . ' NOT NULL',
                'status'  => Schema::TYPE_BOOLEAN . ' DEFAULT 1',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user');
        $this->dropTable('note');
        $this->dropTable('todo');
    }
}
