<?php
namespace app\modules\subscription\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\modules\subscription\models\User;

/**
 * Subscription ActiveRecord
 *
 * Columns (expected):
 * - id, user_id, plan_id, status, type, trial_end_at, start_at, end_at, created_at, updated_at
 */
class Subscription extends ActiveRecord
{
    public static function tableName(){ return '{{%subscription}}'; }
    
    /**
     * Set default start and trial_end for trial subscriptions on creation.
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if ($this->isNewRecord && $this->type === 'trial') {
                if (empty($this->start_at)) {
                    $this->start_at = date('Y-m-d H:i:s');
                }
                if (empty($this->trial_end_at)) {
                    $this->trial_end_at = (new \DateTime())->modify('+7 days')->format('Y-m-d H:i:s');
                }
            }
            return true;
        }
        return false;
    }

    public function behaviors()
    {
        return [
            // Use DB expression for timestamp so it is consistent with DB timezone
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
            [['user_id','plan_id'], 'integer'],
            [['status','type'], 'string', 'max' => 20],
            [['start_at','end_at','trial_end_at','created_at','updated_at'], 'safe'],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }


    public function getPlan()
    {
        return $this->hasOne(Plan::class, ['id' => 'plan_id']);
    }

    public function isTrial()
    {
        return $this->type === 'trial';
    }

    /**
     * Return active subscription for user (parameterized, safe)
     *
     * @param int $userId
     * @return Subscription|null
     */
    public static function findActiveByUser(int $userId)
    {
        return static::find()
            ->where(['user_id' => $userId, 'status' => 'active'])
            ->one();
    }

    /**
     * Scope: expired trials
     * @return \yii\db\ActiveQuery
     */
    public static function findExpiredTrials()
    {
        return static::find()
            ->where(['type' => 'trial', 'status' => 'active'])
            ->andWhere(['<', 'trial_end_at', new Expression('NOW()')]);
    }

    /**
     * Convert a trial subscription to paid.
     *
     * @param string $paidPlanType optional plan or logic to convert
     * @return bool
     */
    public function convertToPaid()
    {
        if (!$this->isTrial() || $this->status !== 'active') {
            return false;
        }

        $this->type = 'paid';
        // set start and end appropriately (here keep same length as trial if end_at set)
        if ($this->trial_end_at && $this->start_at) {
            // extend end_at by 30 days by default if no end_at set
            $this->start_at = $this->start_at;
            $this->end_at = (new \DateTime($this->trial_end_at))->modify('+30 days')->format('Y-m-d H:i:s');
        } else {
            $this->start_at = date('Y-m-d H:i:s');
            $this->end_at = (new \DateTime())->modify('+30 days')->format('Y-m-d H:i:s');
        }

        return $this->save(false, ['type','start_at','end_at','updated_at']);
    }
}
