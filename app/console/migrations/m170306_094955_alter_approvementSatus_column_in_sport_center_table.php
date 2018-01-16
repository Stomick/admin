<?php

use flexibuild\migrate\db\Migration;

class m170306_094955_alter_approvementSatus_column_in_sport_center_table extends Migration
{
    private $table = 'sport_center';
    private $columnApprovementStatus = 'approvementStatus';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->alterColumn($this->table, $this->columnApprovementStatus, $this->typeEnum(['active', 'not active', 'moder'], 'not active'));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->alterColumn($this->table, $this->columnApprovementStatus, $this->boolean()->defaultValue(false));
    }
}
