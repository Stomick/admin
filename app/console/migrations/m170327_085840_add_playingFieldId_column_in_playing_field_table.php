<?php

use flexibuild\migrate\db\Migration;

class m170327_085840_add_playingFieldId_column_in_playing_field_table extends Migration
{
    private $table = 'playing_field';
    private $columnPlayingFieldId = 'playingFieldId';
    private $columnId = 'id';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn($this->table, $this->columnPlayingFieldId, $this->integer()->null());
        $this->addForeignKeyAutoNamed($this->table, $this->columnPlayingFieldId, $this->table, $this->columnId);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKeyAutoNamed($this->table, $this->columnPlayingFieldId, $this->table, $this->columnId);
        $this->dropColumn($this->table, $this->columnPlayingFieldId);
    }
}
