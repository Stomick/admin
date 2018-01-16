<?php

use flexibuild\migrate\db\Migration;

class m170322_125220_add_smsId_column_in_user_table extends Migration
{
    private $table = 'user';
    private $columnSmsId = 'smsId';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn($this->table, $this->columnSmsId, $this->string()->null());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn($this->table, $this->columnSmsId);
    }
}
