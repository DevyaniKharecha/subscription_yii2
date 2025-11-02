<?php

use yii\db\Migration;

class m251102_113931_add_email_to_user_table extends Migration
{
    public function safeUp()
    {
        $table = '{{%user}}';
        if (!$this->db->getTableSchema($table)->getColumn('email')) {
            $this->addColumn($table, 'email', $this->string()->unique()->after('id'));
        }
        if (!$this->db->getTableSchema($table)->getColumn('password')) {
            $this->addColumn($table, 'password', $this->string()->after('email'));
        }
    }

    public function safeDown()
    {
        $table = '{{%user}}';
        if ($this->db->getTableSchema($table)->getColumn('email')) {
            $this->dropColumn($table, 'email');
        }
        if ($this->db->getTableSchema($table)->getColumn('password')) {
            $this->dropColumn($table, 'password');
        }
    }
}
