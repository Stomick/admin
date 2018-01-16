<?php

use flexibuild\migrate\db\CreateTableMigration;

class m170223_123419_create_sport_center_advantage_table extends CreateTableMigration
{
    /**
     * @inheritdoc
     */
    protected function tableName()
    {
        return 'sport_center_advantage';
    }

    /**
     * @inheritdoc
     */
    protected function tableColumns()
    {
        return [
            'id' => $this->primaryKey(),
            'sportCenterId' => $this->integer()->notNull(),
            'advantageId' => $this->integer()->notNull(),

            'createdAt' => $this->integer()->notNull(),
            'updatedAt' => $this->integer()->notNull(),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function tableIndexes()
    {
        return [
            'sportCenterId' => false,
            'advantageId' => false,
            'sportCenterId, advantageId' => true
        ];
    }

    /**
     * @inheritdoc
     */
    public function tableForeignKeys()
    {
        return [
            [
                self::CFG_COLUMNS => 'sportCenterId',
                self::CFG_REF_TABLE => 'sport_center',
                self::CFG_REF_COLUMNS => 'id',
                self::CFG_ON_DELETE => self::FK_CASCADE,
                self::CFG_ON_UPDATE => self::FK_RESTRICT,
            ],
            [
                self::CFG_COLUMNS => 'advantageId',
                self::CFG_REF_TABLE => 'advantage',
                self::CFG_REF_COLUMNS => 'id',
                self::CFG_ON_DELETE => self::FK_CASCADE,
                self::CFG_ON_UPDATE => self::FK_RESTRICT,
            ],
        ];
    }
}
