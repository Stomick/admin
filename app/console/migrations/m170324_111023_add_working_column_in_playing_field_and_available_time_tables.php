<?php

use flexibuild\migrate\db\Migration;

class m170324_111023_add_working_column_in_playing_field_and_available_time_tables extends Migration
{
    private $tablePlayingField = 'playing_field';
    private $tableAvailableTime = 'available_time';
    private $columnWorking = 'working';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn($this->tablePlayingField, $this->columnWorking, $this->boolean()->defaultValue(true));
        $this->addColumn($this->tableAvailableTime, $this->columnWorking, $this->boolean()->defaultValue(true));
        $this->createIndexAutoNamed($this->tablePlayingField, $this->columnWorking);
        $this->createIndexAutoNamed($this->tableAvailableTime, $this->columnWorking);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndexAutoNamed($this->tablePlayingField, $this->columnWorking);
        $this->dropIndexAutoNamed($this->tableAvailableTime, $this->columnWorking);
        $this->dropColumn($this->tablePlayingField, $this->columnWorking);
        $this->dropColumn($this->tableAvailableTime, $this->columnWorking);
    }
}
