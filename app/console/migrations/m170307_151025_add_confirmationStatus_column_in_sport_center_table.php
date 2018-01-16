<?php

use flexibuild\migrate\db\Migration;

class m170307_151025_add_confirmationStatus_column_in_sport_center_table extends Migration
{
    private $tableSportCenter = 'sport_center';
    private $tableCompanyDetails = 'company_details';
    private $columnConfirmationStatus = 'confirmationStatus';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn(
            $this->tableSportCenter,
            $this->columnConfirmationStatus,
            $this->typeEnum(['empty', 'waiting', 'true', 'false'], 'empty')
        );
        $this->dropColumn($this->tableCompanyDetails, $this->columnConfirmationStatus);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn($this->tableSportCenter, $this->columnConfirmationStatus);
        $this->addColumn(
            $this->tableCompanyDetails,
            $this->columnConfirmationStatus,
            $this->typeEnum(['published', 'not published', 'request publication'], 'not published')
        );
    }
}
