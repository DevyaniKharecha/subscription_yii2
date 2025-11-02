<?php
use yii\db\Migration;

class m251102_112352_seed_users_and_rbac extends Migration
{
    public function safeUp()
    {
        $table = '{{%user}}';
        $db = $this->db;
        if ($db->schema->getTableSchema($table, true) === null) {
            $this->createTable($table, [
                'id' => $this->primaryKey(),
                'username' => $this->string()->notNull()->unique(),
                'password_hash' => $this->string()->null(),
                'auth_key' => $this->string(32)->null(),
                'is_admin' => $this->boolean()->defaultValue(0),
                'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
                'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            ]);
            return;
        }

        $schema = $db->schema->getTableSchema($table);
        $add = function ($col, $def) use ($schema, $table) {
            if (!isset($schema->columns[$col])) {
                Yii::$app->db->createCommand()->addColumn($table, $col, $def)->execute();
            }
        };
        $add('password_hash', 'VARCHAR(255)');
        $add('created_at', 'DATETIME NULL');
        $add('updated_at', 'DATETIME NULL');
    }

    public function safeDown()
    {
        echo "This migration cannot be reverted safely.\n";
        return false;
    }
}
