<?php
namespace app\console\controllers;

use yii\console\Controller;
use app\modules\subscription\services\TrialService;

class TrialController extends Controller
{
    public function actionCheck()
    {
        $service = new TrialService();
        $count = $service->processExpiredTrials();
        $this->stdout("Processed {$count} expired trial(s).\n");
    }
}
