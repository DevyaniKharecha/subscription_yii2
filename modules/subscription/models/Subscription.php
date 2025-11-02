<?php
namespace app\modules\subscription\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\modules\subscription\models\User;
use app\modules\subscription\models\Plan;

class Subscription extends ActiveRecord
{
    public static function tableName(){ return '{{%subscription}}'; }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['user_id', 'plan_id'], 'integer'],
            [['status', 'type'], 'string', 'max' => 20],
            [['start_at', 'end_at', 'trial_end_at', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert && $this->type === 'trial') {
            $this->start_at = date('Y-m-d H:i:s');
            $this->trial_end_at = (new \DateTime())->modify('+7 days')->format('Y-m-d H:i:s');
            $this->status = 'active';
        }
        return parent::beforeSave($insert);
    }

    // Relationships
    public function getUser() { return $this->hasOne(User::class, ['id' => 'user_id']); }
    public function getPlan() { return $this->hasOne(Plan::class, ['id' => 'plan_id']); }

    public function isTrial() { return $this->type === 'trial'; }

    public static function findActiveByUser(int $userId)
    {
        return static::find()
            ->where(['user_id' => $userId, 'status' => 'active'])
            ->one();
    }

    public static function findExpiredTrials()
    {
        return static::find()
            ->where(['type' => 'trial', 'status' => 'active'])
            ->andWhere(['<', 'trial_end_at', new Expression('NOW()')]);
    }

    public function convertToPaid()
    {
        if (!$this->isTrial() || $this->status !== 'active') {
            return false;
        }

        $this->type = 'paid';
        $this->start_at = date('Y-m-d H:i:s');
        $this->end_at = (new \DateTime())->modify('+30 days')->format('Y-m-d H:i:s');

        return $this->save(false, ['type', 'start_at', 'end_at', 'updated_at']);
    }
}