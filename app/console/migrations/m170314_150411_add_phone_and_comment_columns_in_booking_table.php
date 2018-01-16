<?php

use flexibuild\migrate\db\Migration;

class m170314_150411_add_phone_and_comment_columns_in_booking_table extends Migration
{
    private $tableBooking = 'booking';
    private $tableSportCenter = 'sport_center';
    private $columnPhone = 'phone';
    private $columnComment = 'comment';
    private $columnImage = 'image';
    private $columnImage2 = 'image2';
    private $columnImage3 = 'image3';
    private $columnImage4 = 'image4';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn($this->tableBooking, $this->columnPhone, $this->string()->null());
        $this->addColumn($this->tableBooking, $this->columnComment, $this->text()->null());
        $this->dropColumn($this->tableSportCenter, $this->columnImage);
        $this->dropColumn($this->tableSportCenter, $this->columnImage2);
        $this->dropColumn($this->tableSportCenter, $this->columnImage3);
        $this->dropColumn($this->tableSportCenter, $this->columnImage4);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->addColumn($this->tableSportCenter, $this->columnImage, $this->string()->null());
        $this->addColumn($this->tableSportCenter, $this->columnImage2, $this->string()->null());
        $this->addColumn($this->tableSportCenter, $this->columnImage3, $this->string()->null());
        $this->addColumn($this->tableSportCenter, $this->columnImage4, $this->string()->null());
        $this->dropColumn($this->tableBooking, $this->columnComment);
        $this->dropColumn($this->tableBooking, $this->columnPhone);
    }
}
