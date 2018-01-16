<?php

use flexibuild\migrate\db\Migration;

class m170314_084711_delete_price_column_in_playing_field_table extends Migration
{
    private $table = 'playing_field';
    private $columnPrice = 'price';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropColumn($this->table, $this->columnPrice);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->addColumn($this->table, $this->columnPrice, $this->float()->null());
    }
}
