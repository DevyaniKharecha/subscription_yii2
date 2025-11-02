<?php
namespace app\modules\subscription\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\modules\subscription\services\SubscriptionService;

class SubscriptionController extends Controller
{
    private SubscriptionService $subscriptionService;

    public function __construct($id, $module, SubscriptionService $subscriptionService = null, $config = [])
    {
        $this->subscriptionService = $subscriptionService ?? new SubscriptionService();
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['cancel' => ['POST']],
            ],
        ];
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $models = $this->subscriptionService->getUserSubscriptions($user->id, $user->is_admin);
        return $this->render('index', ['models' => $models]);
    }

    public function actionView($id)
    {
        $user = Yii::$app->user->identity;
        $model = $this->subscriptionService->getSubscription($id, $user->id, $user->is_admin);
        return $this->render('view', ['model' => $model]);
    }

    public function actionCancel($id)
    {
        $user = Yii::$app->user->identity;
        $this->subscriptionService->cancelSubscription($id, $user->id, $user->is_admin);
        Yii::$app->session->setFlash('success', 'Subscription cancelled successfully.');
        return $this->redirect(['index']);
    }
}
