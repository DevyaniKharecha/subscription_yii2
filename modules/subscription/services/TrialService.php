<?php
namespace app\modules\subscription\services;

use Yii;
use app\modules\subscription\models\Subscription;
use app\jobs\SendSubscriptionEmailJob;

class TrialService
{
    public function processExpiredTrials(): int
    {
        $expiredTrials = Subscription::findExpiredTrials()->all();
        $count = count($expiredTrials);

        foreach ($expiredTrials as $trial) {
            if ($trial->status === 'cancelled') {
                continue;
            }

            if ($trial->convertToPaid()) {
                Yii::info("Converted trial #{$trial->id} to paid.", __METHOD__);

                // Queue notification
                if (isset(Yii::$app->queue)) {
                    Yii::$app->queue->push(new SendSubscriptionEmailJob([
                        'userId' => $trial->user_id,
                        'subscriptionId' => $trial->id,
                        'subject' => 'Your trial has ended',
                        'body' => 'Your trial has been converted to a paid subscription.',
                    ]));
                }
            }
        }

        return $count;
    }
}