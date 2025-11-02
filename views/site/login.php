<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Login';
?>
<h1><?= Html::encode($this->title) ?></h1>

<form method="post" action="<?= Url::to(['site/login']) ?>">
    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
    <div>
        <label>Email:</label><br>
        <input type="text" name="email" required>
    </div>
    <div>
        <label>Password:</label><br>
        <input type="password" name="password" required>
    </div>
    <br>
    <button type="submit">Login</button>
</form>
