<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Form;

use Da\User\Factory\EmailChangeStrategyFactory;
use Da\User\Helper\SecurityHelper;
use Da\User\Model\User;
use Da\User\Traits\ContainerAwareTrait;
use Da\User\Traits\ModuleAwareTrait;
use Yii;
use yii\base\Model;

class SettingsForm extends Model
{
    use ModuleAwareTrait;
    use ContainerAwareTrait;

    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $new_password;
    /**
     * @var string
     */
    public $current_password;
    /**
     * @var SecurityHelper
     */
    protected $securityHelper;

    /** @var User */
    protected $user;

    public function __construct(SecurityHelper $securityHelper, array $config = [])
    {
        $this->securityHelper = $securityHelper;
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'usernameRequired' => ['username', 'required'],
            'usernameTrim' => ['username', 'filter', 'filter' => 'trim'],
            'usernameLength' => ['username', 'string', 'min' => 3, 'max' => 255],
            'usernamePattern' => ['username', 'match', 'pattern' => '/^[-a-zA-Z0-9_\.@]+$/'],
            'emailRequired' => ['email', 'required'],
            'emailTrim' => ['email', 'filter', 'filter' => 'trim'],
            'emailPattern' => ['email', 'email'],
            'emailUsernameUnique' => [
                ['email', 'username'],
                'unique',
                'when' => function ($model, $attribute) {
                    return $this->getUser()->$attribute != $model->$attribute;
                },
                'targetClass' => $this->getClassMap()->get(User::class),
            ],
            'newPasswordLength' => ['new_password', 'string', 'max' => 72, 'min' => 6],
            'currentPasswordRequired' => ['current_password', 'required'],
            'currentPasswordValidate' => [
                'current_password',
                function ($attribute) {
                    if (!$this->securityHelper->validatePassword($this->$attribute, $this->getUser()->password_hash)) {
                        $this->addError($attribute, Yii::t('usuario', 'Current password is not valid'));
                    }
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('usuario', 'Email'),
            'username' => Yii::t('usuario', 'Username'),
            'new_password' => Yii::t('usuario', 'New password'),
            'current_password' => Yii::t('usuario', 'Current password'),
        ];
    }

    /**
     * @return User|null|\yii\web\IdentityInterface
     */
    public function getUser()
    {
        if ($this->user == null) {
            $this->user = Yii::$app->user->identity;
        }

        return $this->user;
    }

    /**
     * Saves new account settings.
     *
     * @return bool
     */
    public function save()
    {
        if ($this->validate()) {
            $this->user->scenario = 'settings';
            $this->user->username = $this->username;
            $this->user->password = $this->new_password;
            if ($this->email == $this->user->email && $this->user->unconfirmed_email != null) {
                $this->user->unconfirmed_email = null;
            } elseif ($this->email != $this->user->email) {
                $strategy = EmailChangeStrategyFactory::makeByStrategyType(
                    $this->getModule()->emailChangeStrategy,
                    $this
                );

                return $strategy->run();
            }

            return $this->user->save();
        }

        return false;
    }
}
