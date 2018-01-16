<?php

use flexibuild\migrate\db\Migration;

class m170309_094450_add_sportCenterName_playingFieldName_hour_type_columns_in_booking_table extends Migration
{
    private $tableBooking = 'booking';
    private $tableAvailableTime = 'available_time';
    private $columnUserId = 'userId';
    private $columnAvailableTimeId = 'availableTimeId';
    private $columnId = 'id';
    private $columnSportCenterName = 'sportCenterName';
    private $columnPlayingFieldName = 'playingFieldName';
    private $columnHour = 'hour';
    private $columnType = 'type';
    private $columnPrice = 'price';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropIndexAutoNamed($this->tableBooking, "$this->columnAvailableTimeId, $this->columnUserId", true);
        $this->dropForeignKeyAutoNamed(
            $this->tableBooking,
            $this->columnAvailableTimeId,
            $this->tableAvailableTime,
            $this->columnId
        );
        $this->dropColumn($this->tableBooking,$this->columnAvailableTimeId);
        $this->addColumn($this->tableBooking, $this->columnSportCenterName, $this->string()->notNull());
        $this->createIndexAutoNamed($this->tableBooking, $this->columnSportCenterName);
        $this->addColumn($this->tableBooking, $this->columnPlayingFieldName, $this->string()->notNull());
        $this->createIndexAutoNamed($this->tableBooking, $this->columnPlayingFieldName);
        $this->addColumn($this->tableBooking, $this->columnHour, $this->integer()->notNull());
        $this->createIndexAutoNamed($this->tableBooking, $this->columnHour);
        $this->addColumn($this->tableBooking, $this->columnType, $this->typeEnum(['work', 'weekend'], 'work'));
        $this->createIndexAutoNamed($this->tableBooking, $this->columnType);
        $this->addColumn($this->tableBooking, $this->columnPrice, $this->integer()->notNull());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->addColumn($this->tableBooking, $this->columnAvailableTimeId, $this->integer()->notNull());
        $this->addForeignKeyAutoNamed(
            $this->tableBooking,
            $this->columnAvailableTimeId,
            $this->tableAvailableTime,
            $this->columnId
        );
        $this->createIndexAutoNamed($this->tableBooking, "$this->columnAvailableTimeId, $this->columnUserId", true);
        $this->dropColumn($this->tableBooking,$this->columnPrice);
        $this->dropColumn($this->tableBooking,$this->columnType);
        $this->dropColumn($this->tableBooking,$this->columnHour);
        $this->dropColumn($this->tableBooking,$this->columnPlayingFieldName);
        $this->dropColumn($this->tableBooking,$this->columnSportCenterName);
    }
}
