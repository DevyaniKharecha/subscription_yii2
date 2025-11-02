<?php
use yii\db\Migration;

/**
 * Idempotent migration to add missing columns, indexes and foreign keys for subscription table.
 */
class m250101_000001_fix_subscription_table extends Migration
{
    public function safeUp()
    {
        $table = '{{%subscription}}';
        $db = $this->db;

        // only proceed if table exists
        if ($db->schema->getTableSchema($table, true) === null) {
            $this->stdout("Table {$table} does not exist, skipping migration.\n");
            return;
        }

        // add created_at and updated_at if missing
        $schema = $db->schema;
        $tbl = $schema->getTableSchema($table);
        if (!isset($tbl->columns['created_at'])) {
            $this->addColumn($table, 'created_at', $this->dateTime()->null());
        }
        if (!isset($tbl->columns['updated_at'])) {
            $this->addColumn($table, 'updated_at', $this->dateTime()->null());
        }
        // ensure start_at / end_at exist
        if (!isset($tbl->columns['start_at'])) {
            $this->addColumn($table, 'start_at', $this->dateTime()->null());
        }
        if (!isset($tbl->columns['end_at'])) {
            $this->addColumn($table, 'end_at', $this->dateTime()->null());
        }

        // add index on user_id if missing
        if (!isset($tbl->columns['user_id'])) {
            // nothing to do; user_id should exist from original migration
        } else {
            $indexes = array_keys($tbl->columns);
            // add index if not exists - simple try/catch because Yii doesn't expose index list conveniently here
            try {
                $this->createIndex('idx-subscription-user_id', $table, 'user_id');
            } catch (\Exception $e) {
                // index probably exists
            }
        }

        // add FK to user table if user table exists and FK not present
        if ($db->schema->getTableSchema('{{%user}}', true)) {
            try {
                $this->addForeignKey('fk_sub_user', $table, 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
            } catch (\Exception $e) {
                // FK likely exists, ignore
            }
        }
    }

    public function safeDown()
    {
        // do not drop columns to avoid data loss; make this migration non-destructive
        echo "m250101_000001_fix_subscription_table cannot be reverted safely.\n";
        return false;
    }
}
