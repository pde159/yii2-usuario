<?php

/**
 * @var $this  yii\web\View
 * @var $model \Da\User\Model\Rule
 */

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

?>


<?php $form = ActiveForm::begin(
    [
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
    ]
) ?>

<?= $form->field($model, 'name') ?>

<?= $form->field($model, 'className') ?>

<?= Html::submitButton(Yii::t('usuario', 'Save'), ['class' => 'btn btn-success btn-block']) ?>

<?php ActiveForm::end() ?>
