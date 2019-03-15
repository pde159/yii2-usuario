<?php

if (Yii::$app->user->getIsGuest()) {
    echo \yii\bootstrap4\Html::a('Login', ['/user/security/login']);
    echo \yii\bootstrap4\Html::a('Registration', ['/user/registration/register']);
} else {
    echo \yii\bootstrap4\Html::a('Logout', ['/user/security/logout']);
}

echo $content;
