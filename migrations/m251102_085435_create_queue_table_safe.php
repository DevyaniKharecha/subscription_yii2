<?php

use yii\db\Migration;

class m251102_085435_create_queue_table_safe extends Migration
{
    public function safeUp()
    {
        if ($this->db->getTableSchema('{{%queue}}', true) === null) {
            $this->createTable('{{%queue}}', [
                'id' => $this->bigPrimaryKey(),
                'channel' => $this->string()->notNull(),
                'job' => $this->binary()->notNull(),
                'pushed_at' => $this->integer()->notNull(),
                'ttr' => $this->integer()->notNull(),
                'delay' => $this->integer()->notNull(),
                'priority' => $this->integer()->unsigned(),
                'reserved_at' => $this->integer(),
                'attempt' => $this->integer(),
                'done_at' => $this->integer(),
            ]);

            $this->createIndex('idx_queue_channel', '{{%queue}}', 'channel');
            $this->createIndex('idx_queue_reserved_at', '{{%queue}}', 'reserved_at');
        }
    }

    public function safeDown()
    {
        if ($this->db->getTableSchema('{{%queue}}', true) !== null) {
            $this->dropTable('{{%queue}}');
        }
    }
}
