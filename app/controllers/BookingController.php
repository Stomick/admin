<?php

namespace app\controllers;

use app\base\web\ActiveController;
use app\files\UploadAction;
use app\models\AvailableTime;
use app\models\Booking;
use app\models\BookingSearch;
use app\models\BookingService;
use app\models\PlayingField;
use app\models\Service;
use app\models\SportCenter;
use app\models\User;
use app\models\UnavailableTime;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class BookingController
 * @package app\controllers
 */
class BookingController extends ActiveController
{
    private $startYear;
    private $startMonth;
    private $startDay;
    private $startHour;
    private $endYear;
    private $endMonth;
    private $endDay;
    private $endHour;

    /**
     * @inheritdoc
     */
    public $modelClass = Booking::class;

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
                        'my-bookings',
                        'my-booking',
                        'create-booking',
                        'schedule',
                        'payment'
                    ],
                    'roles' => [User::ROLE_USER],
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
        $result['create']['scenario'] = Booking::SCENARIO_CREATE;
        $result['update']['scenario'] = Booking::SCENARIO_UPDATE;
        $result['upload-image'] = [
            'class' => UploadAction::class,
            'context' => 'company-image',
            'postName' => 'file',
            'urlsScheme' => 'http',
        ];
        $result['upload-document'] = [
            'class' => UploadAction::class,
            'context' => 'company-document',
            'postName' => 'file',
            'urlsScheme' => 'http',
        ];

        return $result;
    }

    public function actionMyBookings()
    {
        return Booking::find()
            ->andWhere(['[[userId]]' => Yii::$app->user->id])
            //->andWhere(['or', ['submit' => true], ['>', 'createdAt', time() - 1800]])
            ->andWhere(['submit' => true])
            ->orderBy('id')
            ->all();
    }

    /**
     * @param integer $id
     * @throws NotFoundHttpException
     * @return array
     */
    public function actionMyBooking($id)
    {

        $booking = Booking::find()
            ->andWhere(['[[userId]]' => Yii::$app->user->id, '[[id]]' => $id])
            ->andWhere(['submit' => true])
            ->one();

        if (!$booking) {
            throw new NotFoundHttpException("Admin not found: $id");
        }

        return $booking;
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionList()
    {
        return Booking::find()->all();
    }

    /**
     * @return string|Booking
     */
    public function actionCreateBooking()
    {
        $booking = new Booking();

        if ($booking->load(Yii::$app->request->post(), '')) {

            $availableTime = AvailableTime::findOne($booking->availableTimeId);

            if ($availableTime) {
                /* @var  $availableTime AvailableTime */
                $booking->playingFieldName = $availableTime->playingField->name;
                $booking->playingFieldId = $availableTime->playingFieldId;
                $booking->sportCenterName = $availableTime->playingField->sportCenter->name;
                $booking->sportCenterAddress = $availableTime->playingField->sportCenter->address;
                $booking->createdAt = $booking->updatedAt = time();
                //$booking->start_hour = $booking->end_hour =
                $booking->hour = $availableTime->hour;
                $booking->type = $availableTime->type;
                $booking->avaTimeId = $availableTime->id;
                $booking->userId = Yii::$app->user->id;
                //return $booking;
                $idsService = [];
                try {

                    $ss = $booking->save();

                } catch (Exception $e) {
                    return 'Выброшено исключение: ' . $e->getMessage() . "\n";
                }
                if (count($booking->serviceIds) > 0) {
                    foreach ($booking->serviceIds as $service) {
                        $idsService[] = $service["id"];
                    }

                    foreach ($services = Service::find()
                        ->andWhere(['in', '[[id]]', $idsService])
                        ->indexBy('id')
                        ->all() as $service) {
                        $booking->price += $service['price'];
                    }

                    if ($ss) {
                        foreach ($services as $service) {
                            $bookingService = new BookingService();
                            $bookingService->name = $service["name"];
                            $bookingService->bookingId = $booking->id;
                            $bookingService->price = $service["price"];
                            $bookingService->save();
                        }
                    }
                    //return $booking;
                }
                return $booking;

            } else {
                return 'Данное время не найдено';
            }
        } else {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function prepareDataProvider()
    {
        $searchModel = new BookingSearch();

        return $searchModel->search($this->request->get());
    }

    /**
     * @param Service[] $services
     * @return int
     *
     */
    public function getPriceForBooking($services)
    {
        $price = 0;

        foreach ($services as $service) {
            $price += $service->price;
        }

        return $price;
    }

    /**
     * @param Booking $booking
     * @return int
     *
     */
    public function isAvailableTime($booking)
    {
        $model = Booking::find()
            ->andWhere(['[[playingFieldId]]' => $booking->playingFieldId])
            ->andWhere(['[[hour]]' => $booking->hour])
            ->andWhere(['[[day]]' => $booking->day])
            ->andWhere(['[[month]]' => $booking->month])
            ->andWhere(['[[year]]' => $booking->year])
            ->andWhere(['[[type]]' => $booking->type])
            ->andWhere(['>', 'createdAt', time() - 1800])
            ->one();

        return $model ? false : true;
    }

    /**
     * @param integer $id
     * @param integer $startDate
     * @param integer $endDate
     * @throws NotFoundHttpException
     * @return array
     */
    public function actionSchedule($id, $startDate, $endDate)
    {

        // На самом деле поиск броней работает неправильно, потому что
        // даты хранятся как три значения - год,месяц и день
        // этот метод принимает юникс тайм начала и конца, потом парсит его в поля класса котроллера
        // и делает выборку опретаром between для года, месяца и дня
        // но это не работает потому что для промежутка 01-01-2016 01-01-2017 дата 10-01-2016 не попадет
        $this->startMonth = intval(Yii::$app->formatter->asDate($startDate, 'M')) + 1;

        $startY = Yii::$app->formatter->asDate($startDate, 'YY-MM-d');


        $playingFields = PlayingField::find()
            ->with('availableTimes')
            ->andWhere(['[[sportCenterId]]' => $id])
            ->all();

        $playingFieldIds = ArrayHelper::getColumn($playingFields, 'id');

        if (empty($playingFields)) {
            throw new NotFoundHttpException("PlayingFields Not Found");
        }

        $this->parseDateTime($startDate, $endDate);

        $result = ["sportCenterName" => $playingFields[0]->sportCenter->name];

        $bookings = Booking::find()
            ->andWhere(['between', 'year', $this->startYear, $this->endYear])
            ->andWhere(['between', 'month', $this->startMonth, $this->endMonth])
            ->andWhere(['between', 'day', $this->startDay, $this->endDay])
            ->andWhere(['in', 'playingFieldId', $playingFieldIds])
            //->andWhere(['[[submit]]' => true])
            ->all();

        $listBookings = [];

        /** @var Booking $booking */
        foreach ($bookings as $booking) {
            // проверяем является ли юзер администратором объекта и так же - оплачено ли бронирование
            // если нет - то он не увидиит подробностей о бронировании, только дату и время и кроме того
            // не сможет попытаться забронировать неподтвержденный другим юзером слот
            if (User::isUserAdmin(Yii::$app->user->id)) {
                $listBookings[] = [
                    'id' => $booking->id,
                    'playingFieldName' => $booking->playingField ? $booking->playingField->name : $booking->playingFieldName,
                    'availableTimeId' => $booking->avaTimeId,
                    'createDate' => $booking->createdAt,
                    'userName' => $booking->user->name,
                    'phone' => $booking->user->phone,
                    'bookingDate' => [
                        'date' => $booking->year . '-' . $booking->month . '-' . (intval($booking->day) < 10 ? '0' . $booking->day : $booking->day),
                        'time' => BookingController::makeRangeTime($booking->start_hour, $booking->end_hour)
                    ],
                    'status'=> $booking->submit,
                    'service' => $booking->services,
                    'price' => $booking->price,
                    'adminBooking' => $booking->user->role == User::ROLE_ADMIN ? true : false,
                ];
            } else {
                $listBookings[] = [
                    'bookingDate' => [
                        'date' => $booking->year . '-' . $booking->month . '-' . (intval($booking->day) < 10 ? '0' . $booking->day : $booking->day),
                        'time' => BookingController::makeRangeTime($booking->start_hour, $booking->end_hour)
                    ],
                    'playingFieldId' => $booking->playingField->id,
                    'availableTimeId' => $booking->avaTimeId,
                ];
            }
        }

        $result['bookings'] = $listBookings;

        $listPlayingFields = [];

        /** @var PlayingField $playingField */
        foreach ($playingFields as $playingField) {
            $listAvailableTimes = [];

            /** @var AvailableTime $availableTime */
            foreach ($playingField->availableTimes as $availableTime) {
                if ($availableTime->working) {
                    $listAvailableTimes[] = [
                        'id' => $availableTime->id,
                        'hour' => $availableTime->hour,
                        'start_hour' => $availableTime->start_hour,
                        'end_hour' => $availableTime->end_hour,
                        'type' => $availableTime->type,
                        'price' => $availableTime->price,
                        'working' => $availableTime->working,
                    ];
                }
            }

            //ArrayHelper::multisort($listAvailableTimes, 'hour', SORT_ASC);

            $unavalableTimes = $this->getUnavailableTimes($playingField);

            $listPlayingFields[] = [
                'availableTime' => $listAvailableTimes,
                'unavailableTimes' => $unavalableTimes,
                'playingFieldName' => $playingField->name,
                'playingFieldId' => $playingField->id];
        }

        $result['playingFields'] = $listPlayingFields;

        return $result;
    }

    public function makeRangeTime($startTime, $endTime)
    {

        $start = explode(':', $startTime);
        $end = explode(':', $endTime);
        $min = $start[1];
        $maxEnd = ($end[1] == '30') ? intval($end[0]) + 1 : intval($end[0]);
        $allP = 0;
        $time[] = [];

        for ($i = intval($start[0]); $i < $maxEnd; $i++) {

            $time[$allP] = ($i < 10) ? 0 . $i . ':' . $min : $i . ':' . $min;
            if ($allP == 0 && $min == '30') {
                $i++;
            };
            $allP++;
            $min == '30' ? $min = '00' : $min = '30';
            $time[$allP] = ($i < 10) ? 0 . $i . ':' . $min : $i . ':' . $min;
            $allP++;
            $min == '30' ? $min = '00' : $min = '30';
        }
        $time[$allP] = join(':', $end);

        return $time;

    }

    /**
     * Блокирует поле на определенную дату и время
     * повторный запрос разблокирует
     *
     * Заблокированные часы недоступны для брони в приложении
     * @param  string $date В формате '2017-07-22'
     * @param integer $hour Заблокированное время
     * @return object
     */
    public function actionBlock($fieldId, $date, $hour)
    {
        $block = UnavailableTime::find()
            ->andWhere(['hour' => $hour])
            ->andWhere(['date' => $date])
            ->one();

        if ($block == null) {
            $unavalibleTime = new UnavailableTime();
            $unavalibleTime->userId = Yii::$app->user->id;
            $unavalibleTime->playingFieldId = $fieldId;
            $unavalibleTime->hour = $hour;
            $unavalibleTime->date = $date;
            $test = $unavalibleTime->save();
            $result['status'] = 'unavalable ' . $test;
            return $result;
        } else {
            $test = $block->delete();
            $result['status'] = 'available' . $test;
            return $result;
        }
    }

    /**
     * @param integer $startDate
     * @param integer $endDate
     */
    public function parseDateTime($startDate, $endDate)
    {
        $startDate = intval($startDate / 1000);
        $endDate = intval($endDate / 1000);
        $this->startYear = Yii::$app->formatter->asDate($startDate, 'yyyy');

        $this->startMonth = Yii::$app->formatter->asDate($startDate, 'M');
        $this->startDay = Yii::$app->formatter->asDate($startDate, 'd');
        $this->startHour = Yii::$app->formatter->asDate($startDate, 'H');

        $this->endYear = Yii::$app->formatter->asDate($endDate, 'yyyy');
        $this->endMonth = Yii::$app->formatter->asDate($endDate, 'M');
        $this->endDay = Yii::$app->formatter->asDate($endDate, 'd');
        $this->endHour = Yii::$app->formatter->asDate($endDate, 'H');
    }

    public function getUnavailableTimes($playingField)
    {
        $result = [];

        $startDate = sprintf('%s-%s-%s', $this->startYear, $this->startMonth, $this->startDay);
        $endDate = sprintf('%s-%s-%s', $this->endYear, $this->endMonth, $this->endDay);

        $blockedTimes = UnavailableTime::find()
            ->andWhere(['playingFieldId' => $playingField->id])
            ->andWhere(['between', 'date', $startDate, $endDate])
            ->all();

        return array_map(function ($item) {
            $obj['date'] = $item->date;
            $obj['hour'] = $item->hour;
            return $obj;
        }, $blockedTimes);
    }
}
