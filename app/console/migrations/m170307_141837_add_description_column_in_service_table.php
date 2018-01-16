<?php

use flexibuild\migrate\db\Migration;

class m170307_141837_add_description_column_in_service_table extends Migration
{
    private $table = 'service';
    private $columnDescription = 'description';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn($this->table, $this->columnDescription, $this->text()->null());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn($this->table, $this->columnDescription);
    }
}
