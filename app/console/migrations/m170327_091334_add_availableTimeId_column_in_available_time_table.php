<?php

use flexibuild\migrate\db\Migration;

class m170327_091334_add_availableTimeId_column_in_available_time_table extends Migration
{
    private $table = 'available_time';
    private $columnAvailableTimeId = 'availableTimeId';
    private $columnId = 'id';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn($this->table, $this->columnAvailableTimeId, $this->integer()->null());
        $this->addForeignKeyAutoNamed($this->table, $this->columnAvailableTimeId, $this->table, $this->columnId);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKeyAutoNamed($this->table, $this->columnAvailableTimeId, $this->table, $this->columnId);
        $this->dropColumn($this->table, $this->columnAvailableTimeId);
    }
}
