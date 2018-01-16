<?php

use flexibuild\migrate\db\Migration;

class m170327_113228_drop_index_by_name_column_in_sport_center_table extends Migration
{
    private $table = 'sport_center';
    private $columnName = 'name';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropIndexAutoNamed($this->table, $this->columnName, true);
        $this->createIndexAutoNamed($this->table, $this->columnName);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndexAutoNamed($this->table, $this->columnName);
        $this->createIndexAutoNamed($this->table, $this->columnName, true);
    }
}
