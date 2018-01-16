<?php
namespace app\controllers\actions\user;

use Yii;
use app\models\User;
use yii\base\InvalidValueException;

/**
 * Class CreateAction
 * @package app\controllers\actions\user
 */
class CreateAction extends \yii\rest\CreateAction
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $result = parent::run();

        if (\Yii::$app->getResponse()->getStatusCode() === 201 && $result->sendUserPasswordEmail) {
//            $this->sendEmail($result);
        }

        return $result;
    }

    /**
     * Send email to user with password
     * @param User $model
     * @throws InvalidValueException
     */
    protected function sendEmail(User $model)
    {
        switch ($model->scenario) {
            case User::SCENARIO_CREATE_USER :
                $view = [
                    'html' => 'investor-registration/html.sphp',
                    'text' => 'investor-registration/text',
                ];
                
                break;
            case User::SCENARIO_CREATE_ADMIN :
                $view = [
                    'html' => 'admin-registration/html.sphp',
                    'text' => 'admin-registration/text',
                ];

                break;
        }
        $send = Yii::$app
            ->mailer
            ->compose($view, ['model' => $model])
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($model->email)
            ->setSubject('Invitation to TargetGlobal Dataroom.')
            ->send();

        if (!$send) {
            throw new InvalidValueException("Can't send email");
        }
    }
}