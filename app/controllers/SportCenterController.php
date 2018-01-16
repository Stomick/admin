<?php

namespace app\controllers;

use app\base\web\ActiveController;
use app\files\UploadAction;
use app\models\AvailableTime;
use app\models\Booking;
use app\models\CompanyDetails;
use app\models\Image;
use app\models\PlayingField;
use app\models\Service;
use app\models\SportCenter;
use app\models\SportCenterAdvantage;
use app\models\SportCenterSearch;
use app\models\UnavailableTime;
use app\models\User;
use yii\base\UserException;
use yii\db\Transaction;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use Yii;
use app\base\helpers\SerializeHelper;
use yii\web\NotFoundHttpException;

/**
 * Class SportCenterController
 * @package app\controllers
 */
class SportCenterController extends ActiveController
{
    /**
     * @inheritdoc
     */
    public $modelClass = SportCenter::class;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => [
                        'index',
                        'view',
                        'list',
                        'sport-center'
                    ],
                    'roles' => [User::ROLE_USER],
                ],
                [
                    'allow' => false,
                    'actions' => [
                        'change-confirmation-status',
                    ],
                    'roles' => [User::ROLE_ADMIN],
                ],
                [
                    'allow' => true,
                    'roles' => [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN],
                ],
            ],
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $result = parent::actions();
        $result['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $result['upload-image'] = [
            'class' => UploadAction::class,
            'context' => 'sport-center-image',
            'postName' => 'file',
            'urlsScheme' => 'http',
        ];
        $result['upload-logo'] = [
            'class' => UploadAction::class,
            'context' => 'sport-center-logo',
            'postName' => 'file',
            'urlsScheme' => 'http',
        ];

        unset($result['create']);
        unset($result['update']);
        unset($result['delete']);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function prepareDataProvider()
    {
        $searchModel = new SportCenterSearch();

        return $searchModel->search($this->request->get());
    }

    /**
     * Creates a new SportCenter model.
     *
     * @throws UserException
     * @return SportCenter|string
     */
    public function actionCreate()
    {
        $sportCenter = new SportCenter();
        $sportCenter->scenario = SportCenter::SCENARIO_CREATE;

        if ($sportCenter->load(Yii::$app->request->post(), '') && $sportCenter->save()) {
            return $sportCenter;
        } else {
            return $sportCenter->errors;
        }

    }

    /**
     * @param integer $id
     * @throws NotFoundHttpException
     * @return SportCenter;
     */
    public function actionUpdate($id)
    {
        $sportCenter = SportCenter::findOne($id);

        if ($sportCenter) {
            $approvementStatus = $sportCenter->approvementStatus;

            if ($sportCenter->load(Yii::$app->getRequest()->getBodyParams(), '')) {
                $transaction = Yii::$app->db->beginTransaction();

                if (Yii::$app->user->identity->role == User::ROLE_SUPER_ADMIN) {
                    return $this->changeStatus($sportCenter, $approvementStatus, $transaction);
                }
                if (Yii::$app->user->identity->role == User::ROLE_ADMIN) {
                    if ($approvementStatus == SportCenter::APPROVEMENT_STATUS_NOT_ACTIVE ||
                        $approvementStatus == SportCenter::APPROVEMENT_STATUS_ACTIVE
                    ) {
                        $this->createModerSportCenter($sportCenter, $transaction);
                    }
                    if ($approvementStatus == SportCenter::APPROVEMENT_STATUS_MODER) {
                        $this->changeModerSportCenter($sportCenter, $transaction);
                    }
                }

                $transaction->commit();
            }
        } else {
            throw new NotFoundHttpException("Not Found Sport Center");
        }
    }

    /**
     * @param integer $year
     * @param integer $month
     * @param integer $day
     * @param integer $hour
     * @param string $type
     *
     * @return array
     */
    public function actionList($year)
    {
        $tableSportCenter = SportCenter::tableName();
        return SportCenter::find()
            ->joinWith(['playingFields.availableTimes'])
            ->andWhere([$tableSportCenter . '.[[approvementStatus]]' => SportCenter::APPROVEMENT_STATUS_ACTIVE, $tableSportCenter . '.[[confirmationStatus]]' => SportCenter::CONFIRMATION_STATUS_TRUE])
            ->orderBy($tableSportCenter . '.name')
            ->all();
    }

    /**
     * @param integer $id
     * @param integer $year
     * @param integer $month
     * @param integer $day
     * @param integer $hour
     * @param string $start_hour
     * @param string $end_hour
     * @param string $type
     * @throws NotFoundHttpException
     * @return array
     */
    public function actionSportCenter($id, $year, $month, $day, $type)
    {

        $tablePlayingField = PlayingField::tableName();
        $tableAvailableTime = AvailableTime::tableName();
        $cur_day = $year . '-' . $month . '-' . (intval($day) < 10 ? '0' . $day : $day);

        /*
                // теперь фактическая проверка на доступность
                // прозводиться в методе scedule на самом деле параметры с датой не нужны
                // этот метод юзался при старом варианте UI когда сначала выбиралась дата
                // а потом все остальное

        */
        $playingFields = PlayingField::find()
            ->joinWith(['availableTimes'])
            //->andWhere(['not in', $tableAvailableTime . '.playingFieldId', $subQuery])
            ->andWhere([$tableAvailableTime . '.[[type]]' => $type])
            //->andWhere([$tableAvailableTime . '.[[hour]]' => $hour])
            ->andWhere([$tableAvailableTime . '.[[working]]' => true])
            ->andWhere([$tablePlayingField . '.[[sportCenterId]]' => $id])
            ->orderBy($tablePlayingField . '.name')
            ->all();


        if ($playingFields) {

            $fields = [];


            foreach ($playingFields as $playingField) {
                $availableTime = [];
                $allPrice = [];
                $price = 0;

                $unavailableTime = UnavailableTime::find()
                    ->andWhere(['[[playingFieldId]]' => $playingField->id])
                    ->andWhere(['[[date]]' => $cur_day])
                    ->all();

                foreach (AvailableTime::find()
                             ->andWhere(['[[playingFieldId]]' => $playingField->id])
                             ->andWhere(['[[type]]' => $type])
                             ->all() as $k => $ava) {

                    $bookings = Booking::find()
                        ->andWhere(['[[year]]' => $year])
                        ->andWhere(['[[month]]' => $month])
                        ->andWhere(['[[day]]' => $day])
                        ->andWhere(['[[avaTimeId]]' => $ava->id])
                        ->all();

                    $price += $ava->price;

                    $allPrice[$k] = [
                        'start' => 'с ' . $ava->start_hour . ' до ' . $ava->end_hour,
                        'time_price' => $ava->price
                    ];

                    $start = explode(':', $ava->start_hour);
                    $end = explode(':', $ava->end_hour);
                    $min = $start[1];
                    $endMin = ($end[1] == '30') ? intval($end[0]) + 1 : intval($end[0]);
                    $availableTime[$ava->id] = [];
                    $allP = 0;

                    for ($i = intval($start[0]); $i < $endMin; $i++) {

                        $availableTime[$ava->id][$allP] = [
                            'price' => intval($ava->price) / 2,
                            'hour' => ($i < 10) ? 0 . $i . ':' . $min : $i . ':' . $min,
                            'status' => SportCenterController::inTimeArray(($i < 10) ? 0 . $i . ':' . $min : $i . ':' . $min, $unavailableTime, $bookings)
                        ];

                        if ($allP == 0 && $min == '30') {
                            $i++;
                        };

                        $allP++;
                        $min == '30' ? $min = '00' : $min = '30';
                        $availableTime[$ava->id][$allP] = [
                            'price' => intval($ava->price) / 2,
                            'hour' => ($i < 10) ? 0 . $i . ':' . $min : $i . ':' . $min,
                            'status' => SportCenterController::inTimeArray(($i < 10) ? 0 . $i . ':' . $min : $i . ':' . $min, $unavailableTime, $bookings)
                        ];
                        $allP++;
                        $min == '30' ? $min = '00' : $min = '30';
                    }

                    $availableTime[$ava->id][$allP] = [
                        'price' => intval($ava->price) / 2,
                        'hour' => join(':', $end),
                        'status' => SportCenterController::inTimeArray(join(':', $end), $unavailableTime, $bookings)
                    ];
                    $allPrice[$k] = [
                        'start' => 'с ' . $ava->start_hour . ' до ' . $ava->end_hour,
                        'time_price' => $ava->price,
                        'avalableTime' => $availableTime[$ava->id],
                        'availableTimeId' => $ava->id
                    ];
                }

                $fields[] = [
                    "id" => $playingField->id,
                    "name" => $playingField->name,
                    "info" => $playingField->info,
                    "price" => $price,
                    "allPrice" => $allPrice,
                    //"time" => $availableTime->hour,
                    //"availableTime" => $availableTime,
                    "unavalableTime" => $unavailableTime,
                    "day" => $cur_day
                ];

                if ($playingFields[0]->sportCenter->logoFile->isEmpty) {
                    $logoSrc = null;
                } else {
                    $logoSrc = SerializeHelper::fixFileUrl($playingFields[0]->sportCenter->logoFile->getUrl(null, true));
                }
            }
            return [
                "id" => $playingFields[0]->sportCenter->id,
                "name" => $playingFields[0]->sportCenter->name,
                "address" => $playingFields[0]->sportCenter->address,
                "longitude" => $playingFields[0]->sportCenter->longitude,
                "latitude" => $playingFields[0]->sportCenter->latitude,
                "logo" => $playingFields[0]->sportCenter->logo,
                "logoSrc" => $logoSrc,
                "images" => $playingFields[0]->sportCenter->images,
                "playingFields" => $fields,
                "services" => $playingFields[0]->sportCenter->services,
                'advantages' => $playingFields[0]->sportCenter->advantages,
                "description" => $playingFields[0]->sportCenter->description,
                'begin' => '00' . ":00",
                'end' => '24' . ":00",
                'typeTime' => true,
            ];
        } else {
            $playingFields = PlayingField::find()
                //->joinWith(['availableTimes'])
                //->andWhere(['not in', $tableAvailableTime . '.playingFieldId', $subQuery])
                //->andWhere([$tableAvailableTime . '.[[type]]' => $type])
                //->andWhere([$tableAvailableTime . '.[[hour]]' => $hour])
                //->andWhere([$tableAvailableTime . '.[[working]]' => true])
                ->andWhere([$tablePlayingField . '.[[sportCenterId]]' => $id])
                ->orderBy($tablePlayingField . '.name')
                ->all();
            if ($playingFields) {

                if ($playingFields[0]->sportCenter->logoFile->isEmpty) {
                    $logoSrc = null;
                } else {
                    $logoSrc = SerializeHelper::fixFileUrl($playingFields[0]->sportCenter->logoFile->getUrl(null, true));
                }

                return [
                    "id" => $playingFields[0]->sportCenter->id,
                    "name" => $playingFields[0]->sportCenter->name,
                    "address" => $playingFields[0]->sportCenter->address,
                    "longitude" => $playingFields[0]->sportCenter->longitude,
                    "latitude" => $playingFields[0]->sportCenter->latitude,
                    "logo" => $playingFields[0]->sportCenter->logo,
                    "logoSrc" => $logoSrc,
                    "images" => $playingFields[0]->sportCenter->images,
                    "playingFields" => null,
                    "services" => $playingFields[0]->sportCenter->services,
                    'advantages' => $playingFields[0]->sportCenter->advantages,
                    "description" => $playingFields[0]->sportCenter->description,
                    'begin' => '00' . ":00",
                    'end' => '24' . ":00",
                    'typeTime' => true,
                ];
            }
        }
        /*
        $end = PlayingField::find()
            ->joinWith('availableTimes')
            ->andWhere([$tablePlayingField . '.[[sportCenterId]]' => $id])
            ->max($tableAvailableTime . '.[[hour]]');
        $begin = PlayingField::find()
            ->joinWith('availableTimes')
            ->andWhere([$tablePlayingField . '.[[sportCenterId]]' => $id])
            ->min($tableAvailableTime . '.[[hour]]');
        $typeTime = $begin == 0 && $end == 23 ? true : false;

        if ($playingFields[0]->sportCenter->logoFile->isEmpty) {
            $logo = null;
        } else {
            $origin = SerializeHelper::fixFileUrl($playingFields[0]->sportCenter->logoFile->getUrl(null, true));
            $formats = [];
            $logo = [
                'origin' => $origin,
                'formats' => $formats,
            ];
        }

        $fields = [];


        foreach ($playingFields as $playingField) {
            $availableTime = AvailableTime::find()
                ->andWhere(['[[playingFieldId]]' => $playingField->id])
                ->andWhere(['[[hour]]' => $hour])
                ->andWhere(['[[type]]' => $type])
                ->one();
            $fields[] = [
                "id" => $playingField->id,
                "name" => $playingField->name,
                "info" => $playingField->info,
                "price" => $availableTime->price,
                "time" => $availableTime->hour,
                "availableTimeId" => $availableTime->id,
            ];
        }

        if ($playingFields[0]->sportCenter->logoFile->isEmpty) {
            $logoSrc = null;
        } else {
            $logoSrc = SerializeHelper::fixFileUrl($playingFields[0]->sportCenter->logoFile->getUrl(null, true));
        }

        return [
            "id" => $playingFields[0]->sportCenter->id,
            "name" => $playingFields[0]->sportCenter->name,
            "address" => $playingFields[0]->sportCenter->address,
            "longitude" => $playingFields[0]->sportCenter->longitude,
            "latitude" => $playingFields[0]->sportCenter->latitude,
            "logo" => $playingFields[0]->sportCenter->logo,
            "logoSrc" => $logoSrc,
            "images" => $playingFields[0]->sportCenter->images,
            "playingFields" => $fields,
            "services" => $playingFields[0]->sportCenter->services,
            'advantages' => $playingFields[0]->sportCenter->advantages,
            "description" => $playingFields[0]->sportCenter->description,
            'begin' => $begin . ":00",
            'end' => $end . ":00",
            'typeTime' => $typeTime,
        ];
    }
    */
        throw new NotFoundHttpException("Not available");
    }

    public function inTimeArray($time, $ArrayTime, $bookings)
    {
        try {
            foreach ($bookings as $booking) {
                foreach (SportCenterController::checkInRangeTime($booking->start_hour, $booking->end_hour) as $times) {
                    if ($time === $times) {
                        return ['st' => 'busy', 'opt' => 'Забронировано'];
                    }
                }
            }

            foreach ($ArrayTime as $times) {
                if ($time === $times->hour) {
                    return ['st' => 'busy', 'opt' => 'Не активно'];
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return ['st' => 'free', 'opt' => 'Свободно'];
    }

    private function checkInRangeTime($startTime, $endTime)
    {

        $start = explode(':', $startTime);
        $end = explode(':', $endTime);
        $min = $start[1];
        $retTime = [];
        $endMin = $end[1] == '30' ? intval($end[0]) + 1 : intval($end[0]);
        $allP = 0;

        for ($i = intval($start[0]); $i < $endMin; $i++) {

            $retTime[$allP] = ($i < 10) ? 0 . $i . ':' . $min : $i . ':' . $min;
            if ($allP == 0 && $min == '30') {
                $i++;
            };

            $allP++;
            $min == '30' ? $min = '00' : $min = '30';
            $retTime[$allP] = ($i < 10) ? 0 . $i . ':' . $min : $i . ':' . $min;
            $allP++;
            $min == '30' ? $min = '00' : $min = '30';
        }

        return $retTime;
    }

    /**
     * @param SportCenter $sportCenter
     * @return boolean
     */

    public function deleteSportCenter($sportCenter)
    {
        foreach ($sportCenter->sportCenterAdvantages as $sportCenterAdvantage) {
            $sportCenterAdvantage->delete();
        }

        foreach ($sportCenter->playingFields as $playingField) {
            foreach ($playingField->availableTimes as $availableTime) {
                $availableTime->delete();
            }

            $playingField->delete();
        }

        foreach ($sportCenter->services as $service) {
            $service->delete();
        }

        foreach ($sportCenter->sportCenterUsers as $sportCenterUser) {
            $sportCenterUser->delete();
        }

        foreach ($sportCenter->images as $image) {
            $image->delete();
        }

        return $sportCenter->delete();
    }

    /**
     * @param SportCenter $sportCenter
     * @param Transaction $transaction
     * @throws UserException
     */
    public function createModerSportCenter(SportCenter $sportCenter, Transaction $transaction)
    {
        if (!SportCenter::find()->andWhere(['sportCenterId' => $sportCenter->id])->exists()) {
            $sportCenterModer = new SportCenter();

            $sportCenterModer->load(Yii::$app->getRequest()->getBodyParams(), '');

            $sportCenterModer->sportCenterId = $sportCenter->id;
            $sportCenterModer->approvementStatus = SportCenter::APPROVEMENT_STATUS_MODER;

            if (!$sportCenterModer->save()) {
                $transaction->rollBack();

                throw new UserException(json_encode($sportCenterModer->errors));
            };

            $companyDetails = $sportCenter->details;

            if (is_null($companyDetails)) {
                $transaction->rollBack();

                throw new UserException("Missing Company Details");
            }

            $companyDetailsModer = new CompanyDetails();

            $companyDetailsModer->setAttributes($companyDetails->getAttributes());

            $companyDetailsModer->sportCenterId = $sportCenterModer->id;

            $companyDetailsModer->save();

            $playingFields = isset(Yii::$app->getRequest()->getBodyParams()['playingFieldModels'])
                ? Yii::$app->getRequest()->getBodyParams()['playingFieldModels']
                : false;

            if ($playingFields) {
                foreach ($playingFields as $playingField) {
                    $modelPlayingFieldModer = new PlayingField();

                    $modelPlayingFieldModer->setAttributes($playingField);

                    if (isset($playingField['id']) && !is_null($playingField['id'])) {
                        $modelPlayingFieldModer->playingFieldId = $playingField['id'];
                    }

                    $modelPlayingFieldModer->sportCenterId = $sportCenterModer->id;

                    $modelPlayingFieldModer->save();

                    if (!isset($playingField['availableTimeModels'])) {
                        $transaction->rollBack();

                        throw new UserException("Missing Available Time For Playing Field " . $modelPlayingFieldModer->name);
                    }

                    foreach ($playingField['availableTimeModels'] as $availableTime) {
                        $modelAvailableTimeModer = new AvailableTime();

                        $modelAvailableTimeModer->setAttributes($availableTime);

                        if (isset($playingField['id']) && !is_null($playingField['id'])) {
                            $modelAvailableTimeModer->availableTimeId = $availableTime['id'];
                        }

                        $modelAvailableTimeModer->playingFieldId = $modelPlayingFieldModer->id;

                        $modelAvailableTimeModer->save();
                    }
                }
            }
        } else {
            throw new UserException('This object is already in moderation');
        }
    }

    /**
     * @param SportCenter $sportCenter
     * @param Transaction $transaction
     * @throws UserException
     */
    public function changeModerSportCenter(SportCenter $sportCenter, Transaction $transaction)
    {
        $sportCenter->save();

        $playingFields = Yii::$app->getRequest()->getBodyParams()['playingFieldModels'];

        if ($playingFields) {
            foreach ($playingFields as $playingField) {
                if (isset($playingField['id']) && !is_null($playingField['id'])) {
                    $modelPlayingFieldModer = PlayingField::findOne(['id' => $playingField['id']]);

                    $modelPlayingFieldModer->setAttributes($playingField);
                    $modelPlayingFieldModer->save();

                    if (!isset($playingField['availableTimeModels'])) {
                        $transaction->rollBack();

                        throw new UserException("Missing Available Time For Playing Field " . $modelPlayingFieldModer->name);
                    }

                    foreach (AvailableTime::find()->where(['playingFieldId' => $playingField['id']])->all() as $delAval) {
                        $boolFind = false;

                        foreach ($playingField['availableTimeModels'] as $availableTime) {
                            if ($availableTime['id'] == $delAval->id) {
                                //throw new NotFoundHttpException("Not Found Available Time " . $availableTime['id'] . '   ' . $delAval->id);
                                $boolFind = true;
                            }
                        }
                        if (!$boolFind) {
                            $delAval->delete();
                        }

                    }
                    //
                    /** @var AvailableTime $availableTime */
                    foreach ($playingField['availableTimeModels'] as $availableTime) {
                        if (isset($availableTime['id'])) {
                            $modelAvailableTimeModer = AvailableTime::findOne([
                                'id' => $availableTime['id']
                            ]);
                            $modelAvailableTimeModer->setAttributes($availableTime);
                            $modelAvailableTimeModer->save();
                        } else {
                            $modelAvailableTimeModer = new AvailableTime();
                            $modelAvailableTimeModer->setAttributes($availableTime);
                            $modelAvailableTimeModer->playingFieldId = $playingField['id'];
                            $modelAvailableTimeModer->save();
                            //$transaction->rollBack();

                            //throw new UserException("Missing param id in Available Time For Playing Field " . $modelPlayingFieldModer->name);
                        }
                    }
                } else {
                    $modelPlayingFieldModer = new PlayingField();

                    $modelPlayingFieldModer->setAttributes($playingField);

                    $modelPlayingFieldModer->sportCenterId = $sportCenter->id;

                    $modelPlayingFieldModer->save();

                    if (!isset($playingField['availableTimeModels'])) {
                        $transaction->rollBack();

                        throw new UserException("Missing Available Time For Playing Field " . $modelPlayingFieldModer->name);
                    }

                    foreach ($playingField['availableTimeModels'] as $availableTime) {
                        $modelAvailableTimeModer = new AvailableTime();

                        $modelAvailableTimeModer->setAttributes($availableTime);

                        $modelAvailableTimeModer->playingFieldId = $modelPlayingFieldModer->id;

                        $modelAvailableTimeModer->save();
                    }
                }
            }
        }
    }

    /**
     * @param SportCenter $sportCenter
     * @param Transaction $transaction
     * @param string $approvementStatus
     * @throws NotFoundHttpException
     * @return SportCenter
     */
    public function changeStatus(SportCenter $sportCenter, $approvementStatus, Transaction $transaction)
    {
        if ($approvementStatus == SportCenter::APPROVEMENT_STATUS_MODER) {
            $sportCenterOld = SportCenter::findOne($sportCenter->sportCenterId);

            if ($sportCenter->approvementStatus == SportCenter::APPROVEMENT_STATUS_NOT_ACTIVE) {
                if ($sportCenterOld) {
                    $companyDetails = $sportCenter->details;
                    $companyDetails->delete();

                    $this->deleteSportCenter($sportCenter);
                    $transaction->commit();

                    return $sportCenterOld;
                } else {
                    $transaction->rollBack();

                    throw new NotFoundHttpException("Not Found Sport Center");
                }
            }
            if ($sportCenter->approvementStatus == SportCenter::APPROVEMENT_STATUS_ACTIVE) {
                if ($sportCenterOld) {
                    $companyDetails = $sportCenter->details;
                    $companyDetails->delete();

                    $sportCenter = SportCenter::findOne($sportCenter->id);
                    $sportCenterOld->setAttributes($sportCenter->getAttributes());

                    $sportCenterOld->sportCenterId = null;
                    $sportCenterOld->approvementStatus = SportCenter::APPROVEMENT_STATUS_ACTIVE;

                    $sportCenterOld->save();

                    if ($sportCenter->playingFields) {
                        foreach ($sportCenter->playingFields as $playingField) {
                            if ($playingField->playingFieldId) {
                                $playingFieldOld = PlayingField::findOne(['id' => $playingField->playingFieldId]);

                                if ($playingFieldOld) {
                                    $playingFieldOld->setAttributes($playingField->getAttributes());

                                    $playingFieldOld->sportCenterId = $sportCenterOld->id;
                                    $playingFieldOld->playingFieldId = null;

                                    $playingFieldOld->save();

                                    if ($playingField->availableTimes) {
                                        foreach ($playingField->availableTimes as $availableTime) {
                                            if ($availableTime->availableTimeId) {
                                                $availableTimeOld = AvailableTime::findOne(['id' => $availableTime->availableTimeId]);

                                                if ($availableTimeOld) {
                                                    $availableTimeOld->setAttributes($availableTime->getAttributes());

                                                    $availableTimeOld->playingFieldId = $playingFieldOld->id;
                                                    $availableTimeOld->availableTimeId = null;

                                                    $availableTimeOld->save();
                                                } else {
                                                    $transaction->rollBack();

                                                    throw new NotFoundHttpException("Not Found Available Time " . $availableTime->id);
                                                }
                                            } else {
                                                $availableTimeNew = new AvailableTime();

                                                $availableTimeNew->setAttributes($availableTime->getAttributes());

                                                $availableTimeNew->playingFieldId = $playingFieldOld->id;

                                                $availableTimeNew->save();
                                            }
                                        }
                                    }
                                } else {
                                    $transaction->rollBack();

                                    throw new NotFoundHttpException("Not Found Playing Field " . $playingField->id);
                                }
                            } else {
                                $playingFieldNew = new PlayingField();

                                $playingFieldNew->setAttributes($playingField->getAttributes());

                                $playingFieldNew->sportCenterId = $sportCenterOld->id;

                                $playingFieldNew->save();

                                if ($playingField->availableTimes) {
                                    foreach ($playingField->availableTimes as $availableTime) {
                                        $availableTimeNew = new AvailableTime();

                                        $availableTimeNew->setAttributes($availableTime->getAttributes());

                                        $availableTimeNew->playingFieldId = $playingFieldNew->id;

                                        $availableTimeNew->save();
                                    }
                                }
                            }
                        }
                    }

                    $this->updateOldModel(SportCenterAdvantage::tableName(), $sportCenterOld, $sportCenter);
                    $this->updateOldModel(Service::tableName(), $sportCenterOld, $sportCenter);
                    $this->updateOldModel(Image::tableName(), $sportCenterOld, $sportCenter);

                    $this->deleteSportCenter($sportCenter);
                    $transaction->commit();

                    return $sportCenterOld;
                } else {
                    $transaction->rollBack();

                    throw new NotFoundHttpException("Not Found Sport Center");
                }
            }
        }

        $sportCenter->save();
        $transaction->commit();

        return $sportCenter;
    }

    /**
     * @param string $id
     * @param string $confirmationStatus
     * @throws NotFoundHttpException;
     */
    public function actionChangeConfirmationStatus($id, $confirmationStatus = SportCenter::APPROVEMENT_STATUS_NOT_ACTIVE)
    {
        if ($sportCenter = SportCenter::findOne($id)) {
            $sportCenter->confirmationStatus = $confirmationStatus;
            $sportCenter->save(false);
        } else {
            throw new NotFoundHttpException('Sport Center NotFound');
        }
    }

    /**
     * @param string $tableName
     * @param SportCenter $sportCenterOld
     * @param SportCenter $sportCenter
     */
    public function updateOldModel($tableName, SportCenter $sportCenterOld, SportCenter $sportCenter)
    {
        Yii::$app->db->createCommand()
            ->delete($tableName, 'sportCenterId=:id', [
                ':id' => $sportCenterOld->id
            ])
            ->execute();
        Yii::$app->db->createCommand()
            ->update(
                $tableName,
                ['sportCenterId' => $sportCenterOld->id],
                'sportCenterId=:id', [
                ':id' => $sportCenter->id
            ])
            ->execute();
    }

    /**
     * @param string|null $id
     * @throws NotFoundHttpException
     */
    public function actionDelete($id = null)
    {
        if ($sportCenter = SportCenter::findOne($id)) {
            if ($sportCenter->details) {
                $sportCenter->details->delete();
            }
            if ($sportCenter->sportCenter) {
                $sportCenterOld = $sportCenter->sportCenter;
                $sportCenter->delete();

                if ($sportCenterOld->details) {
                    $sportCenterOld->details->delete();
                }

                $sportCenterOld->delete();
            } else {
                $sportCenter->delete();
            }
        } else {
            throw new NotFoundHttpException('Sport Center NotFound');
        }
    }
}
