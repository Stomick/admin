<?php

namespace app\base\components;

use Yii;
use yii\base\Component;
use yii\i18n\Formatter;
use app\models\User;

/**
 * Class PayU
 * @property string $answer
 * @property array $dataArr
 * @property array $IPNcell
 * @property integer $debug
 * @property string $button
 * @property string $showinputs
 * @package app\base\components
 */
class PayU extends Component
{
    public $luUrl = "https://secure.payu.ru/order/lu.php";
    public $button = "<input type='submit'>";
    public $debug = 0;
    public $showinputs = "hidden";
    private static $Inst = false;
    private static $merchant;
    private static $key;
    private $data = [];
    private $dataArr = [];
    private $answer = "";
    private $LUcell = [
        'MERCHANT' => 1,
        'ORDER_REF' => 0,
        'ORDER_DATE' => 1,
        'ORDER_PNAME[0]' => 1,
        'ORDER_PNAME[1]' => 1,
        'ORDER_PGROUP' => 0,
        'ORDER_PCODE[0]' => 1,
        'ORDER_PCODE[1]' => 1,
        'ORDER_PINFO[0]' => 0,
        'ORDER_PINFO[1]' => 0,
        'ORDER_PRICE[0]' => 1,
        'ORDER_PRICE[1]' => 1,
        'ORDER_QTY[0]' => 1,
        'ORDER_QTY[1]' => 1,
        'ORDER_VAT[0]' => 1,
        'ORDER_VAT[1]' => 1,
        'PRICES_CURRENCY' => 1,
        'PAY_METHOD' => 1,
        'ORDER_MPLACE_MERCHANT[0]' => 1,
        'ORDER_MPLACE_MERCHANT[1]' => 1,
        'DISCOUNT' => 0,
        'TESTORDER' => 1,
        'ORDER_PRICE_TYPE[0]' => 0,
        'ORDER_PRICE_TYPE[1]' => 0
    ];
    private $IPNcell = [
        "IPN_PID",
        "IPN_PNAME",
        "IPN_DATE",
        "ORDERSTATUS"
    ];
    private $ANSWER_GET_DATA = [
        'answer',
        'result',
        '3dsecure',
        'date',
        'payrefno',
        'ctrl'
    ];
    private $ANSWER_DATA = [
        'RefNo',
        'MerchantRefNo',
        'Amount',
        'Currency',
        'TransactionResult',
        'Code',
        'Message',
        'TimeStamp',
        'Signature'
    ];
    public function __toString()
    {
        return $this->answer === "" ? "<!-- Answer are not exists -->" : $this->answer;
    }

    public static function getInst()
    {
        if (self::$Inst === false) {
            self::$Inst = new PayU();
        }

        return self::$Inst;
    }
#---------------------------------------------
# Add all options for PayU object.
# Can change all public variables;
# $opt = array( merchant, secretkey, [ luUrl, debug, button ] );
#---------------------------------------------
    function setOptions($opt = [])
    {
        if (!isset($opt['merchant']) || !isset($opt['secretkey'])) {
            die("No params");
        }

        self::$merchant = $opt['merchant'];
        self::$key = $opt['secretkey'];

        unset($opt['merchant'], $opt['secretkey']);

        if (count($opt) === 0) {
            return $this;
        }

        foreach ($opt as $k => $v) {
            $this->$k = $v;
        }

        return $this;
    }

    /**
     * @param array|null $array
     * @return $this
     */
    function setData($array = null)
    {
        if ($array === null) {
            die("No data");
        }

        $this->dataArr = $array;

        return $this;
    }

#--------------------------------------------------------
#	Generate HASH
#--------------------------------------------------------
    function Signature($data = null)
    {
        $str = "";

        foreach ($data as $v) {
            $str .= $this->convData($v);
        }

        return hash_hmac("md5", $str, self::$key);
    }

#--------------------------------------------------------
# Outputs a string for hmac format.
# For a string like 'aa' it will return '2aa'.
#--------------------------------------------------------
    private function convString($string)
    {
        return mb_strlen($string, '8bit') . $string;
    }

#--------------------------------------------------------
# The same as convString except that it receives
# an array of strings and returns the string from all values within the array.
#--------------------------------------------------------
    private function convArray($array)
    {
        $return = '';

        foreach ($array as $v) {
            $return .= $this->convString($v);
        }

        return $return;
    }

    private function convData($val)
    {
        return is_array($val) ? $this->convArray($val) : $this->convString($val);
    }
#----------------------------

#====================== LU GENERETE FORM =================================================
    public function LU()
    {
        $arr = &$this->dataArr;

        $arr['MERCHANT'] = self::$merchant;

        $arr['ORDER_HASH'] = $this->Signature($this->checkArray($arr));

        //$arr['TESTORDER'] = $this->debug == 1 ? "TRUE" : "FALSE";
        //$arr['DEBUG'] = $this->debug;

        $this->answer = $this->genereteForm($arr);

        return $this->answer;
    }

#-----------------------------
# Check array for correct data
#-----------------------------
    private function checkArray($data)
    {
        $ret = [];

        foreach ($this->LUcell as $k => $v) {
            if (isset($data[$k])) {
                $ret[$k] = $data[$k];
            } elseif ($v == 1) {
                die("$k is not set");
            }
        }

        return $ret;
    }

#-----------------------------
# Method which create a form
#-----------------------------
    private function genereteForm($data)
    {
        $form = '<form method="post" action="' . $this->luUrl . '" accept-charset="utf-8" id="pay-form">';

        foreach ($data as $k => $v) {
            $form .= $this->makeString($k, $v);
        }

        return $form . "</form>";
    }

#-----------------------------
# Make inputs for form
#-----------------------------
    private function makeString($name, $val)
    {
        $str = "";

        if (!is_array($val)) {
            return '<input type="' . $this->showinputs . '" name="' . $name . '" value="' . htmlspecialchars($val) . '">' . "\n";
        }

        foreach ($val as $v) {
            $str .= $this->makeString($name . '', $v);
        }

        return $str;
    }
#======================= END LU =====================================

#======================= IPN READ ANSWER ============================
    public function IPN()
    {
        $arr = &$this->dataArr;
        $arr = Yii::$app->request->post();

        foreach ($this->IPNcell as $name) {
            if (!isset($arr[$name])) {
                die("Incorrect data");
            }
        }

        $hash = $arr["HASH"];

        unset($arr["HASH"]);

        $sign = $this->Signature($arr);

        if ($hash != $sign) {
            die("Incorrect hash");
        }

        $datetime = date("YmdHis");
        $sign = $this->Signature([
            "IPN_PID" => $arr["IPN_PID"][0],
            "IPN_PNAME" => $arr["IPN_PNAME"][0],
            "IPN_DATE" => $arr["IPN_DATE"],
            "DATE" => $datetime
        ]);
        $this->answer = "<!-- <EPAYMENT>$datetime|$sign</EPAYMENT> -->";

        return $this;
    }
#======================= END IPN ============================

#======================= IPN READ ANSWER ============================
    public function DATA_ANSWER($params)
    {
        foreach ($this->ANSWER_GET_DATA as $name) {
            if (!isset($params[$name])) {
                die("Incorrect data");
            }
        }
    }
#======================= END IPN ============================


    public function setAnswerData($data ,$opt){

        $retData = [
            'orderstatus' => ($data['TransactionResult'] == 'true')? true:false,
            'refno' => $data['RefNo'],
            'saledate' => $data['TimeStamp'],
            'paymethod' => $opt['paymethod'],
            'country',
            'phone',
            'customeremail',
            'currency' => $data['Currency'],
            'hash' => $data['Signature']
        ];

        return $retData;
    }
#======================= Check BACK_REF =====================
    function checkBackRef($type = "http")
    {
        $path = $type . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $tmp = explode("?", $path);
        $url = $tmp[0] . '?';
        $params = [];

        foreach ($_GET as $k => $v) {
            if ($k != "ctrl") {
                $params[] = $k . '=' . rawurlencode($v);
            }
        }

        $url = $url . implode("&", $params);

        $arr = [$url];
        $sign = $this->Signature($arr);
        #echo "$sign === ".$_GET['ctrl'];
        $this->answer = $sign === $_GET['ctrl'] ? true : false;

        return $this->answer;
    }
#======================= END Check BACK_REF =================
}
