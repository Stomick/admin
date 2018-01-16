<?php

use flexibuild\migrate\db\Migration;

class m170315_133542_alter_description_column_in_sport_center_table extends Migration
{
    private $table = 'sport_center';
    private $columnDescription = 'description';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->alterColumn($this->table, $this->columnDescription, $this->text()->null());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->alterColumn($this->table, $this->columnDescription, $this->string()->null);
    }
}
