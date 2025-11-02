<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\subscription\models\Subscription $model */

$this->title = 'Subscription #' . $model->id;
?>
<div class="subscription-view container py-4">
    <h1><?= Html::encode($this->title) ?></h1>

    <table class="table table-bordered">
        <tr><th>ID</th><td><?= Html::encode($model->id) ?></td></tr>
        <tr><th>Plan</th><td><?= Html::encode($model->plan->name ?? '-') ?></td></tr>
        <tr><th>Status</th><td><?= Html::encode($model->status) ?></td></tr>
        <tr><th>Type</th><td><?= Html::encode($model->type) ?></td></tr>
        <tr><th>Trial Ends</th><td><?= Html::encode($model->trial_end_at ?? '-') ?></td></tr>
        <tr><th>Created</th><td><?= Html::encode($model->created_at ?? '-') ?></td></tr>
    </table>

    <p>
        <?= Html::a('Back', ['index'], ['class' => 'btn btn-secondary']) ?>
        <?php if ($model->status === 'active'): ?>
            <?= Html::a('Cancel', ['cancel', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => ['method' => 'post', 'confirm' => 'Cancel this subscription?']
            ]) ?>
        <?php endif; ?>
    </p>
</div>
