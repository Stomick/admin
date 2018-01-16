<?php

use flexibuild\migrate\db\Migration;

class m170310_135040_add_playingFieldId_column_in_booking_table extends Migration
{
    private $table = 'booking';
    private $columnPlayingFieldId = 'playingFieldId';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn($this->table, $this->columnPlayingFieldId, $this->integer()->notNull());
        $this->createIndexAutoNamed($this->table, $this->columnPlayingFieldId);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndexAutoNamed($this->table, $this->columnPlayingFieldId);
        $this->dropColumn($this->table, $this->columnPlayingFieldId);
    }
}
