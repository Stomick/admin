<?php

use flexibuild\migrate\db\Migration;

class m170328_093028_add_sportCenterAddress_column_in_booking_table extends Migration
{
    private $table = 'booking';
    private $columnSportCenterAddress = 'sportCenterAddress';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn($this->table, $this->columnSportCenterAddress, $this->text()->null());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn($this->table, $this->columnSportCenterAddress);
    }
}
