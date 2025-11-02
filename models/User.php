<?php
namespace app\modules\subscription\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model aligned with actual 'user' table.
 *
 * Table columns: id, username, auth_key, is_admin
 */
class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return '{{%user}}';
    }

    public function rules()
    {
        return [
            [['username'], 'required'],
            [['username'], 'string', 'max' => 50],
            [['auth_key'], 'string', 'max' => 32],
            [['is_admin'], 'boolean'],
            [['username'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'is_admin' => 'Is Admin',
        ];
    }

    /* ---------------- IdentityInterface implementation ---------------- */

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // Not implemented: app doesn’t use token auth
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /* ---------------- Custom helpers ---------------- */

    /**
     * Return whether this user is admin (used in AccessControl)
     */
    public function getIsAdmin()
    {
        return (bool)$this->is_admin;
    }

    /**
     * Find user by username
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Dummy password validation placeholder (since no password column)
     * You can expand if needed (e.g., external SSO or token login)
     */
    public function validatePassword($password)
    {
        return true; // no password in DB
    }
}
