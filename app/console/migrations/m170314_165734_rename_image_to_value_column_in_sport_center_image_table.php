<?php

use flexibuild\migrate\db\Migration;

class m170314_165734_rename_image_to_value_column_in_sport_center_image_table extends Migration
{
    private $table = 'sport_center_image';
    private $columnImage = 'image';
    private $columnValue = 'value';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->renameColumn($this->table, $this->columnImage, $this->columnValue);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->renameColumn($this->table, $this->columnValue, $this->columnImage);
    }
}
