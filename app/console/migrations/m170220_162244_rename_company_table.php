<?php

use flexibuild\migrate\db\Migration;

class m170220_162244_rename_company_table extends Migration
{
    private $tableCompanyDetails = 'company_details';
    private $tableCompany = 'company';
    private $tableSportCenter = 'sport_center';
    private $columnSportCenterId = 'sportCenterId';
    private $columnLatitude = 'latitude';
    private $columnShortName = 'shortName';
    private $columnLongitude = 'longitude';
    private $columnFullName = 'fullName';
    private $columnImage1 = 'image1';
    private $columnLegalAddress = 'legalAddress';
    private $columnImage2 = 'image2';
    private $columnINN = 'inn';
    private $columnImage3 = 'image3';
    private $columnOGRN = 'ogrn';
    private $columnImage4 = 'image4';
    private $columnOKPO = 'okpo';
    private $columnLogo = 'logo';
    private $columnOKATO = 'okato';
    private $columnActive = 'active';
    private $columnOKVED = 'okved';
    private $columnConfirmationStatus = 'confirmationStatus';
    private $columnBank = 'bank';
    private $columnBIK = 'bik';
    private $columnCorrAccount = 'corrAccount';
    private $columnAccount = 'account';
    private $columnGeneralManager = 'generalManager';
    private $columnEmail = 'email';
    private $columnWebSite = 'webSite';
    private $columnFax = 'fax';
    private $columnPhone = 'phone';
    private $columnId = 'id';


    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->renameTable($this->tableCompany, $this->tableCompanyDetails);
        $this->addColumn($this->tableCompanyDetails, $this->columnSportCenterId, $this->integer()->notNull());
        $this->renameColumn($this->tableCompanyDetails, $this->columnLatitude, $this->columnShortName);
        $this->renameColumn($this->tableCompanyDetails, $this->columnLongitude, $this->columnFullName);
        $this->renameColumn($this->tableCompanyDetails, $this->columnImage1, $this->columnLegalAddress);
        $this->renameColumn($this->tableCompanyDetails, $this->columnImage2, $this->columnINN);
        $this->renameColumn($this->tableCompanyDetails, $this->columnImage3, $this->columnOGRN);
        $this->renameColumn($this->tableCompanyDetails, $this->columnImage4, $this->columnOKPO);
        $this->renameColumn($this->tableCompanyDetails, $this->columnLogo, $this->columnOKATO);
        $this->renameColumn($this->tableCompanyDetails, $this->columnActive, $this->columnConfirmationStatus);
        $this->alterColumn($this->tableCompanyDetails, $this->columnConfirmationStatus, $this->typeEnum(['confirmed', 'waiting', 'unconfirmed'], 'unconfirmed'));
        $this->addColumn($this->tableCompanyDetails, $this->columnBank, $this->string()->null());
        $this->addColumn($this->tableCompanyDetails, $this->columnOKVED, $this->string()->null());
        $this->addColumn($this->tableCompanyDetails, $this->columnBIK, $this->string()->null());
        $this->addColumn($this->tableCompanyDetails, $this->columnCorrAccount, $this->string()->null());
        $this->addColumn($this->tableCompanyDetails, $this->columnAccount, $this->string()->null());
        $this->addColumn($this->tableCompanyDetails, $this->columnGeneralManager, $this->string()->null());
        $this->addColumn($this->tableCompanyDetails, $this->columnEmail, $this->string()->null());
        $this->addColumn($this->tableCompanyDetails, $this->columnWebSite, $this->string()->null());
        $this->addColumn($this->tableCompanyDetails, $this->columnFax, $this->string()->null());
        $this->addColumn($this->tableCompanyDetails, $this->columnPhone, $this->string()->null());
        $this->createIndexAutoNamed($this->tableCompanyDetails, $this->columnSportCenterId);
        $this->addForeignKeyAutoNamed($this->tableCompanyDetails, $this->columnSportCenterId, $this->tableSportCenter, $this->columnId);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKeyAutoNamed(
            $this->tableCompanyDetails,
            $this->columnSportCenterId,
            $this->tableSportCenter, $this->columnId
        );
        $this->dropIndexAutoNamed($this->tableCompanyDetails, $this->columnSportCenterId);
        $this->dropColumn($this->tableCompanyDetails, $this->columnPhone);
        $this->dropColumn($this->tableCompanyDetails, $this->columnFax);
        $this->dropColumn($this->tableCompanyDetails, $this->columnWebSite);
        $this->dropColumn($this->tableCompanyDetails, $this->columnEmail);
        $this->dropColumn($this->tableCompanyDetails, $this->columnGeneralManager);
        $this->dropColumn($this->tableCompanyDetails, $this->columnAccount);
        $this->dropColumn($this->tableCompanyDetails, $this->columnCorrAccount);
        $this->dropColumn($this->tableCompanyDetails, $this->columnBIK);
        $this->dropColumn($this->tableCompanyDetails, $this->columnOKVED);
        $this->dropColumn($this->tableCompanyDetails, $this->columnBank);
        $this->alterColumn(
            $this->tableCompanyDetails,
            $this->columnConfirmationStatus,
            $this->typeEnum(['published', 'not published', 'request publication'], 'not published')
        );
        $this->renameColumn($this->tableCompanyDetails, $this->columnConfirmationStatus, $this->columnActive);
        $this->renameColumn($this->tableCompanyDetails, $this->columnOKATO, $this->columnLogo);
        $this->renameColumn($this->tableCompanyDetails, $this->columnOKPO, $this->columnImage4);
        $this->renameColumn($this->tableCompanyDetails, $this->columnOGRN, $this->columnImage3);
        $this->renameColumn($this->tableCompanyDetails, $this->columnINN, $this->columnImage2);
        $this->renameColumn($this->tableCompanyDetails, $this->columnLegalAddress, $this->columnImage1);
        $this->renameColumn($this->tableCompanyDetails, $this->columnFullName, $this->columnLongitude);
        $this->renameColumn($this->tableCompanyDetails, $this->columnShortName, $this->columnLatitude);
        $this->dropColumn($this->tableCompanyDetails, $this->columnSportCenterId);
        $this->renameTable($this->tableCompanyDetails, $this->tableCompany);
    }
}
