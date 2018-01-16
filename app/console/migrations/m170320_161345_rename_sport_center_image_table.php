<?php

use flexibuild\migrate\db\Migration;

class m170320_161345_rename_sport_center_image_table extends Migration
{
    private $tableSportCenterImage = 'sport_center_image';
    private $tableImage = 'image';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->renameTable($this->tableSportCenterImage, $this->tableImage);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->renameTable($this->tableImage, $this->tableSportCenterImage);
    }
}
