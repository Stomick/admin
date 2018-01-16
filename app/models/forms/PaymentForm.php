<?php

namespace app\models\forms;

use yii\base\Model;

/**
 * Class PaymentForm
 * @var integer $id
 * @var string $card
 * @var string $month
 * @var string $year
 * @var string $name
 * @var string $cvv
 */
class PaymentForm extends Model
{
    const PLATFORM_RO = 'RO';
    const PLATFORM_RU = 'RU';
    const PLATFORM_UA = 'UA';
    const PLATFORM_HU = 'HU';
    const PLATFORM_TR = 'TR';

    public $id;
    public $card;
    public $month;
    public $year;
    public $name;
    public $cvv;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['name', 'string'],
            ['card', 'string', 'length' => 16],
            ['month', 'string', 'length' => 2],
            ['year', 'string', 'length' => 4],
            ['cvv', 'string', 'length' => 3],
        ];
    }
}