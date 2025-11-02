<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\subscription\models\Subscription[] $models */

$this->title = 'My Subscriptions';
?>
<div class="subscription-index container py-4">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (empty($models)): ?>
        <p>No subscriptions found.</p>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Plan</th>
                    <th>Status</th>
                    <th>Type</th>
                    <th>Trial End</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($models as $model): ?>
                    <tr>
                        <td><?= Html::encode($model->id) ?></td>
                        <td><?= Html::encode($model->plan->name ?? '-') ?></td>
                        <td><?= Html::encode($model->status) ?></td>
                        <td><?= Html::encode($model->type) ?></td>
                        <td><?= Html::encode($model->trial_end_at ?? '-') ?></td>
                        <td>
                            <?= Html::a('View', ['view', 'id' => $model->id], ['class' => 'btn btn-sm btn-info']) ?>
                            <?php if ($model->status === 'active'): ?>
                                <?= Html::a('Cancel', ['cancel', 'id' => $model->id], [
                                    'class' => 'btn btn-sm btn-danger',
                                    'data' => ['method' => 'post', 'confirm' => 'Cancel this subscription?']
                                ]) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
