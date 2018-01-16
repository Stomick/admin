<?php

use flexibuild\migrate\db\Migration;

class m170313_123051_add_sportCenterId_column_in_sport_center_table extends Migration
{
    private $table = 'sport_center';
    private $columnSportCenterId = 'sportCenterId';
    private $columnId = 'id';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn($this->table, $this->columnSportCenterId, $this->integer()->null());
        $this->createIndexAutoNamed($this->table, $this->columnSportCenterId);
        $this->addForeignKeyAutoNamed($this->table, $this->columnSportCenterId, $this->table, $this->columnId);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKeyAutoNamed($this->table, $this->columnSportCenterId, $this->table, $this->columnId);
        $this->dropIndexAutoNamed($this->table, $this->columnSportCenterId);
        $this->dropColumn($this->table, $this->columnSportCenterId);
    }
}
