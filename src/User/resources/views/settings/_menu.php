<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use yii\bootstrap4\Html;
use Da\User\Widget\MenuWidget;

/** @var \Da\User\Model\User $user */
$user = Yii::$app->user->identity;
$module = Yii::$app->getModule('user');
$networksVisible = count(Yii::$app->authClientCollection->clients) > 0;

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            <?= Html::img(
                $user->profile->getAvatarUrl(24),
                [
                    'class' => 'img-rounded',
                    'alt' => $user->username,
                ]
            ) ?>
            <?= $user->username ?>
        </h3>
    </div>
    <div class="panel-body">
        <?= MenuWidget::widget(
            [
                'items' => [
                    ['label' => Yii::t('usuario', 'Profile'), 'url' => ['/user/settings/profile']],
                    ['label' => Yii::t('usuario', 'Account'), 'url' => ['/user/settings/account']],
                    ['label' => Yii::t('usuario', 'Privacy'),
                        'url' => ['/user/settings/privacy'],
                        'visible' => $module->enableGdprCompliance
                    ],
                    [
                        'label' => Yii::t('usuario', 'Networks'),
                        'url' => ['/user/settings/networks'],
                        'visible' => $networksVisible,
                    ],
                ],
            ]
        ) ?>
    </div>
</div>
