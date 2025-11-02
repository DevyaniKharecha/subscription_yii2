<?php
namespace app\modules\subscription\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use app\modules\subscription\models\Subscription;
use app\modules\subscription\models\User;
use app\modules\subscription\models\Plan;
use yii\filters\VerbFilter;

class SubscriptionController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    // allow authenticated users to index/view their subscriptions
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    // admins can do anything (assumes RBAC role 'admin' exists)
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'cancel' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * List subscriptions for current user (with eager loading)
     */
    public function actionIndex()
    {
        $query = Subscription::find()->with(['plan', 'user']);

        // If logged in user is NOT admin, only show their own subscriptions
        $currentUser = Yii::$app->user->identity;
        if (!$currentUser->is_admin) {
            $query->andWhere(['user_id' => $currentUser->id]);
        }

        $models = $query->all();

        return $this->render('index', ['models' => $models]);
    }

    
    public function actionView($id)
    {
        $currentUser = Yii::$app->user->identity;

        $query = Subscription::find()->with(['plan', 'user'])->where(['id' => $id]);

        // Non-admins can only see their own subscriptions
        if (!$currentUser->is_admin) {
            $query->andWhere(['user_id' => $currentUser->id]);
        }

        $model = $query->one();

        if (!$model) {
            throw new NotFoundHttpException('Subscription not found or access denied.');
        }

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Cancel a subscription (POST)
     */
    public function actionCancel($id)
    {
        $model = Subscription::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Subscription not found');
        }

        if ($model->user_id !== Yii::$app->user->id && !Yii::$app->user->can('admin')) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to cancel this subscription');
        }

        $model->status = 'cancelled';
        $model->save(false, ['status','updated_at']);

        Yii::$app->session->setFlash('success', 'Subscription cancelled.');
        return $this->redirect(['view', 'id' => $id]);
    }
}
