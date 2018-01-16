<?php

use flexibuild\migrate\db\Migration;

class m170302_084045_add_submit_column_in_booking_table extends Migration
{
    private $table = 'booking';
    private $columnSubmit = 'submit';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn($this->table, $this->columnSubmit, $this->boolean()->defaultValue(false));
        $this->createIndexAutoNamed($this->table, $this->columnSubmit);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndexAutoNamed($this->table, $this->columnSubmit);
        $this->dropColumn($this->table, $this->columnSubmit);
    }
}
