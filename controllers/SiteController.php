<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\modules\subscription\models\User;
use yii\filters\AccessControl;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionLogin()
    {
        $request = Yii::$app->request;
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if ($request->isPost) {
            $email = $request->post('email');
            $password = $request->post('password');

            // simple lookup - you can later hash passwords
            $user = User::find()->where(['email' => $email])->one();
            if ($user && $user->password === $password) {
                Yii::$app->user->login($user);
                return $this->redirect(['/subscription/subscription/index']);
            }

            Yii::$app->session->setFlash('error', 'Invalid credentials.');
        }

        return $this->render('login');
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['login']);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}
