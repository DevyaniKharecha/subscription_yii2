<?php

use yii\db\Migration;

/**
 * Class m251102_120000_alter_user_table_add_missing_columns
 *
 * This migration safely adds missing columns to the `user` table:
 * - password_hash
 * - created_at
 * - updated_at
 *
 * It checks first if each column exists before adding it (idempotent),
 * so it can be safely re-run multiple times with zero downtime.
 */
class m251102_112240_user_table_add_missing_columns extends Migration
{
    public function safeUp()
    {
        $table = '{{%user}}';
        $schema = $this->db->schema->getTableSchema($table, true);

        if (!$schema) {
            echo "⚠️ Table `user` does not exist — skipping.\n";
            return;
        }

        // Add password_hash
        if (!isset($schema->columns['password_hash'])) {
            $this->addColumn($table, 'password_hash', $this->string()->after('auth_key')->null());
            echo "✅ Added column: password_hash\n";
        }

        // Add created_at
        if (!isset($schema->columns['created_at'])) {
            $this->addColumn($table, 'created_at', $this->integer()->null()->after('is_admin'));
            echo "✅ Added column: created_at\n";
        }

        // Add updated_at
        if (!isset($schema->columns['updated_at'])) {
            $this->addColumn($table, 'updated_at', $this->integer()->null()->after('created_at'));
            echo "✅ Added column: updated_at\n";
        }
    }

    public function safeDown()
    {
        // Optional — not reverting to preserve data integrity
        echo "🔒 safeDown() skipped intentionally (non-destructive migration).\n";
        return false;
    }
}
