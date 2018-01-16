<?php

namespace app\controllers;

use app\models\SportCenter;
use yii\web\Controller;
use Yii;
use app\models\Booking;
use app\base\components\PayU;
use yii\web\Response;
use app\models\Payment;
use yii\i18n\Formatter;
use Zelenin\SmsRu\Api;
use Zelenin\SmsRu\Entity\Sms;
use Zelenin\SmsRu\Auth\LoginPasswordSecureAuth;
/**
 * Class AdvantageController
 * @package app\controllers
 */

class PaymentController extends Controller
{
    /**
     * @param Action $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if ($action->id === 'payment') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * @param integer $id
     * @param null|string $answer
     */
    public function actionPayment($id = null, $answer = null)
    {
        try {
            $option = [
                'merchant' => Yii::$app->params['payment']['Merchant code'],
                'secretkey' => Yii::$app->params['payment']['Secret key'],
                'debug' => 0
            ];
            if ($id != null) {
                $booking = Booking::findOne($id);

                Yii::$app->response->format = Response::FORMAT_HTML;

                if ($booking) {
                    $sportCenterReqvis = Yii::$app->getDb()->createCommand("SELECT `payu_id`, `procent` FROM `company_details` WHERE `sportCenterId` in ( 
                              (SELECT id FROM `sport_center` WHERE name LIKE (:sportName) AND `approvementStatus` = 'active'))",
                        [':sportName' => $booking->sportCenterName])->queryAll();
// Create form for request
                    if (!$sportCenterReqvis[0]['payu_id']) {
                        print "<div class='row'>Для Этого центра не настроен способ оплаты !</div>";
                    }
                    $formatter = new Formatter();
                    $formatter->datetimeFormat = 'yyyy-MM-dd H:m:s';
                    $formatter->timeZone = 'UTC';
                    $orderDate = $formatter->asDatetime(time(), 'yyyy-MM-dd HH:mm:ss');
                    $orderProdName = Booking::getNameBooking($id);
                    $orderProdCode = 'booking ' . $booking->id;
                    $orderProdInfo = 'Оплата заказа на ' . $booking->year . '-' . $booking->month . '-' . $booking->day . ' ' . $booking->hour . ':00';
                    $clearPrice = floatval(Booking::getPriceBooking($booking->id));
                    $orderProdPriceWithOutSale = $clearPrice - ($clearPrice * floatval($sportCenterReqvis[0]['procent']));
                    $orderProdPriceForWeev = $clearPrice * floatval($sportCenterReqvis[0]['procent']);

                    $forSend = [
                        'MERCHANT' => '',
                        'ORDER_REF' => $booking->id,
                        'ORDER_DATE' => $orderDate,
                        'ORDER_PNAME[0]' => [$orderProdName],
                        'ORDER_PNAME[1]' => ['Обслуживание площадки'],
                        'ORDER_PCODE[0]' => [$orderProdCode],
                        'ORDER_PCODE[1]' => [$orderProdCode],
                        'ORDER_PINFO[0]' => [$orderProdInfo],
                        'ORDER_PINFO[1]' => [$orderProdInfo],
                        'ORDER_PRICE[0]' => [$orderProdPriceWithOutSale],
                        'ORDER_PRICE[1]' => [$orderProdPriceForWeev],
                        'ORDER_QTY[0]' => [1],
                        'ORDER_QTY[1]' => [1],
                        'ORDER_VAT[0]' => [0],
                        'ORDER_VAT[1]' => [0],
//                        'ORDER_PRICE_TYPE[0]' =>['GROSS'],
//                        'ORDER_PRICE_TYPE[1]' =>['GROSS'],
                        // 'ORDER_SHIPPING' => 0,booking 89
                        'PRICES_CURRENCY' => 'RUB',
                        'PAY_METHOD' => 'CCVISAMC',
                        'ORDER_MPLACE_MERCHANT[0]' => [$sportCenterReqvis[0]['payu_id']],
                        'ORDER_MPLACE_MERCHANT[1]' => ['thrhrhhh'],
//                'BACK_REF' => 'http://api.weev.ru/payment/payment?answer=1',
                        'BILL_FNAME' => $booking->user ? $booking->user->name : null,
                        'BILL_LNAME' => $booking->user ? $booking->user->name : null,
//                        'BILL_BANKACCOUNT' => '',
                        'BILL_EMAIL' => 'info@weev.ru',
                        'BILL_PHONE' => $booking->user ? $booking->user->phone : null,
                        'BILL_COUNTRYCODE' => 'RU',
                        'AUTOMODE' => '1',
                        'TESTORDER' => 'FALSE',
                        'DEBUG' => '1',
                        'LANGUAGE' => 'RU',
                        'ORDER_HASH' => ''
                    ];

                    // Create form
                    if (!$answer) {
                        $pay = PayU::getInst()->setOptions($option)->setData($forSend)->LU();
                        $script = "<script>
                    (function(){
                        document.addEventListener('DOMContentLoaded', function() {
                            var form = document.getElementById('pay-form');
                            form.submit();
                        });
                    })();
                </script>";
                        print $pay . $script;
                    }
                }
            }
            $params = Yii::$app->request->get();


            if (isset($params['answer'])) {
                // Read answer (IPN)
                /*
                $payAnswer = PayU::getInst()->setOptions($option)->DATA_ANSWER($params);
                $payment = new Payment();
                $attributes = PayU::setAnswerData(Yii::$app->request->post(),
                    $opt = ['paymethod'=> 'CCVISAMC', 'phome' => '']);

                $payment->setAttributes(array_change_key_case($attributes));
                $payment->save();
*/
                try {
                    $payAnswer = PayU::getInst()->setOptions($option)->IPN();
                    $payment = new Payment();
                    $attributes = Yii::$app->request->post();

                    $payment->setAttributes(array_change_key_case($attributes));
                    $payment->save();

                    $formatter = new Formatter();
                    $formatter->datetimeFormat = 'yyyy-MM-dd H:m:s';
                    $formatter->timeZone = 'UTC';

                    $book = Booking::findOne($payment->refnoext);

                    if ($book) {
                        switch ($attributes['ORDERSTATUS']) {
                            case 'COMPLETE':
                                $UserInfo = Yii::$app->getDb()->createCommand("SELECT `email`,`phone` FROM `user` WHERE `id` in (
                                                    SELECT `userId` FROM `sport_center_user` WHERE `sportCenterId`=(
                                                        SELECT id FROM `sport_center` WHERE name LIKE ((
                                                        SELECT `book`.`sportCenterName` FROM `booking` AS book WHERE `book`.`id`=:bookId))
                                                        ))", [':bookId' => $payment->refnoext])->queryAll();
                                $client = new Api(new LoginPasswordSecureAuth(
                                    Yii::$app->params['sms']['login'],
                                    Yii::$app->params['sms']['password'],
                                    Yii::$app->params['sms']['api_id']
                                ));

                                $html_mail = '<br> Заказ:' . $book->id
                                    . '<br/> Корт: ' . $book->playingFieldName
                                    . '<br/> Дата: ' . $formatter->asDatetime(time(), 'yyyy-MM-dd HH:mm:ss')
                                    . '<br/> ФИО: ' . $book->user->name
                                    . '<br/> Телефон: ' . $book->user->phone
                                    . '<br/> Оплата заказа на ' . $book->year . '-' . $book->month . '-' . $book->day . ' С' . $book->start_hour . ' До ' . $book->end_hour
                                    . '<br/> Сумма :' . $book->price . " " . $payment->currency;
                                foreach ($UserInfo as $usi) {

                                    $sms = new Sms($usi['phone'], $book->sportCenterName . (strip_tags($html_mail)));
                                    $sms->from = 'WEEV';
                                    $client->smsSend($sms);

                                    Yii::$app->mailer->compose()
                                        ->setFrom('info@weev.ru')
                                        ->setTo($usi['email'])
                                        ->setSubject($book->sportCenterName)
                                        ->setHtmlBody($html_mail)
                                        ->send();

                                }

                                $book->submit = true;
                                $book->update(false);
                                $book->save();
                                break;
                            case 'REVERSED':
                            case 'REFUND':
                            case 'CARD_NOTAUTHORIZED':
                                $book->delete(false);
                                break;
                        }
                        print $payAnswer;
                    }

                } catch (\Exception $e) {
                    print $e->getMessage();
                }
            }

            // Check for real BACK_REF
            // $pay true|false
            if (isset($params['ctrl'])) {
                $pay = PayU::getInst()->setOptions($option)->checkBackRef();

                print $pay ? "Real request" : "Fake request";
            }
        }catch (\Exception $e)
        {
            print $e->getMessage();
        }
    }
}
