<?php

use flexibuild\migrate\db\Migration;

class m170323_135135_alter_phone_column_in_user_table extends Migration
{
    private $table = 'user';
    private $columnPhone = 'phone';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->alterColumn($this->table, $this->columnPhone, $this->string()->null());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->alterColumn($this->table, $this->columnPhone, $this->string()->notNull());
    }
}
