<?php
namespace app\modules\subscription\services;

use Yii;
use app\modules\subscription\models\Subscription;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

class SubscriptionService
{
    /**
     * Get all subscriptions for the current user or all if admin.
     */
    public function getUserSubscriptions($userId, $isAdmin = false)
    {
        $query = Subscription::find()->with('plan');

        if (!$isAdmin) {
            $query->andWhere(['user_id' => $userId]);
        }

        return $query->orderBy(['created_at' => SORT_DESC])->all();
    }

    /**
     * Get a single subscription by ID for the current user.
     */
    public function getSubscription($id, $userId, $isAdmin = false)
    {
        $conditions = ['id' => $id];
        if (!$isAdmin) {
            $conditions['user_id'] = $userId;
        }

        $model = Subscription::find()->with('plan')->where($conditions)->one();

        if (!$model) {
            throw new NotFoundHttpException('Subscription not found.');
        }

        return $model;
    }

    /**
     * Cancel a subscription (only if active).
     */
    public function cancelSubscription($id, $userId, $isAdmin = false)
    {
        $model = $this->getSubscription($id, $userId, $isAdmin);

        if ($model->status !== 'active') {
            throw new ForbiddenHttpException('Only active subscriptions can be cancelled.');
        }

        $model->status = 'cancelled';
        $model->updated_at = date('Y-m-d H:i:s');
        $model->save(false, ['status', 'updated_at']);

        return $model;
    }
}
