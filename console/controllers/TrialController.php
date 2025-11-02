<?php
namespace app\console\controllers;

use Yii;
use yii\console\Controller;
use app\modules\subscription\models\Subscription;
use app\jobs\SendSubscriptionEmailJob;

/**
 * Console command to process expired trials
 *
 * Usage: php yii trial/check
 */
class TrialController extends Controller
{
    /**
     * Find expired trials and convert them to paid (unless cancelled).
     */
    public function actionCheck()
    {
        $expired = Subscription::findExpiredTrials()->all();
        $count = count($expired);
        $this->stdout("Found {$count} expired trial(s).\n");

        foreach ($expired as $sub) {
            // Refresh record to ensure we're not operating on stale data
            /** @var Subscription $sub */
            $sub = Subscription::findOne($sub->id);
            if (!$sub || $sub->status !== 'active') {
                continue;
            }

            // If cancelled skip
            if ($sub->status === 'cancelled') {
                continue;
            }

            $oldType = $sub->type;
            if ($sub->convertToPaid()) {
                $this->stdout("Converted subscription {$sub->id} to paid.\n");

                // Queue email notification job (if queue exists)
                if (isset(Yii::$app->queue)) {
                    Yii::$app->queue->push(new SendSubscriptionEmailJob([
                        'userId' => $sub->user_id,
                        'subscriptionId' => $sub->id,
                        'subject' => 'Your trial has ended',
                        'body' => 'Your trial has ended and has been converted to a paid subscription.',
                    ]));
                    $this->stdout("Queued email job for user {$sub->user_id}.\n");
                } else {
                    Yii::info("Queue component not configured; skipping email job for subscription {$sub->id}", __METHOD__);
                }
            } else {
                $this->stdout("Failed to convert subscription {$sub->id}.\n");
            }
        }

        return Controller::EXIT_CODE_NORMAL;
    }
}
