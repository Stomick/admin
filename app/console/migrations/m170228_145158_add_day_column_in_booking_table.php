<?php

use flexibuild\migrate\db\Migration;

class m170228_145158_add_day_column_in_booking_table extends Migration
{
    private $table = 'booking';
    private $columnDay = 'day';
    private $columnMonth = 'month';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn($this->table, $this->columnDay, $this->integer()->notNull());
        $this->alterColumn($this->table, $this->columnMonth, $this->integer()->notNull());
        $this->createIndexAutoNamed($this->table, $this->columnDay);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndexAutoNamed($this->table, $this->columnDay);
        $this->alterColumn($this->table, $this->columnMonth, $this->float()->null());
        $this->dropColumn($this->table, $this->columnDay);
    }
}
