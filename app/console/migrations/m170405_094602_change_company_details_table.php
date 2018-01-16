<?php

use flexibuild\migrate\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m170405_094602_change_company_details_table extends Migration
{
    private $tableCompanyDetails = 'company_details';
    private $tableSportCenter = 'sport_center';
    private $columnSportCenterId = 'sportCenterId';
    private $columnCreatedAt = 'createdAt';
    private $columnUpdatedAt = 'updatedAt';
    private $columnId = 'id';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $sportCenters = (new Query())
            ->select($this->tableSportCenter . '.id')
            ->from($this->tableSportCenter)
            ->innerJoin(
                $this->tableCompanyDetails,
                $this->tableCompanyDetails . '.' . $this->columnSportCenterId . '=' . $this->tableSportCenter . '.' . $this->columnId
            )
            ->all();
        $ids = ArrayHelper::getColumn($sportCenters, 'id');
        $sportCenters = (new Query())
            ->select('id')
            ->from($this->tableSportCenter)
            ->andWhere(['not in', 'id', $ids])
            ->all();

        foreach ($sportCenters as $sportCenter) {
            Yii::$app->db->createCommand()
                ->insert($this->tableCompanyDetails, [
                    $this->columnSportCenterId => $sportCenter["$this->columnId"],
                    $this->columnCreatedAt => time(),
                    $this->columnUpdatedAt => time()
                ])
                ->execute();
        }
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        return true;
    }
}
