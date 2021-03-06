How to Use ReCaptcha Widget
============================

We have included a [Google ReCAPTCHA](https://developers.google.com/recaptcha) widget if you wish to use it instead of 
Yii's captcha. The widget is based on reCaptcha v2.0.

To make use of the widget you need to: 

- [Signup for a reCaptcha API site key](https://www.google.com/recaptcha/admin#createsite)
- Configure the `ReCaptchaComponent` on the `components` section of your application configuration
- Override the Form class you wish to add the captcha rule to
- Override the view where the form is rendering 
- Configure Module and Application

Configuring the ReCaptchaComponent 
----------------------------------

Once you have the API site key you will also be displayed a secret key. You have to configure the component as follows: 

```php 
'components' => [
    'recaptcha' => [ // *important* this name must be like this
        'class' => 'Da\User\Component\ReCaptchaComponent',
        'key' => 'yourSiteKey',
        'secret' => 'secretKeyGivenByGoogle
    ]
]
```
  
Override the Form 
-----------------

For the sake of the example, we are going to override the `Da\User\Form\RecoveryForm` class: 

```php 
namespace app\forms;

class RecoveryForm extends Da\User\Form\RecoveryForm {
    
    public $captcha;
    
    public function rules() {
    
        $rules = parent::rules();
        
        $rules[] = [['captcha'], 'required'];
        $rules[] = [['captcha'], 'Da\User\Validator\ReCaptchaValidator'];
        
        return $rules;
    }
}

```

Overriding the View
-------------------

Create a new file and name it `request.php` and add it in `@app/views/user/recovery`. Add the captcha widget to it: 

```php 
<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use Da\User\Widget\ReCaptchaWidget;

/**
 * @var yii\web\View $this
 * @var yii\bootstrap4\ActiveForm $form
 * @var \Da\User\Form\RecoveryForm $model
 */

$this->title = Yii::t('usuario', 'Recover your password');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(
                    [
                        'id' => $model->formName(),
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => false,
                    ]
                ); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
                
                <?= $form->field($model, 'captcha')->widget(ReCaptchaWidget::className(), ['theme' => 'dark']) ?>

                <?= Html::submitButton(Yii::t('usuario', 'Continue'), ['class' => 'btn btn-primary btn-block']) ?><br>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

```

Configure Module and Application
--------------------------------

Finally, we have to configure the module and the application to ensure is using our form and our view: 

```php

// ... 

'modules' => [
    'user' => [
        'class' => Da\User\Module::class,
        'classMap' => [
            'RecoveryForm' => 'app\forms\RecoveryForm'
        ], 
        'controllerMap' => [
            'recovery' => [
                 'class' => '\app\controllers\RecoveryController' 
             ]
        ]
    ]
], 

// ...

'components' => [
    'view' => [
        'theme' => [
            'pathMap' => [
                '@Da/User/resources/views' => '@app/views/user'
            ]
        ]
    ]
]

```

© [2amigos](http://www.2amigos.us/) 2013-2017
