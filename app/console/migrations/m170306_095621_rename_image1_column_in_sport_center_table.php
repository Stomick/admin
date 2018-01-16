<?php

use flexibuild\migrate\db\Migration;

class m170306_095621_rename_image1_column_in_sport_center_table extends Migration
{
    private $table = 'sport_center';
    private $columnImage1 = 'image1';
    private $columnImage = 'image';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->renameColumn($this->table, $this->columnImage1, $this->columnImage);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->renameColumn($this->table, $this->columnImage, $this->columnImage1);
    }
}
