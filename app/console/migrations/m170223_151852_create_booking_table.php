<?php

use flexibuild\migrate\db\CreateTableMigration;

class m170223_151852_create_booking_table extends CreateTableMigration
{
    /**
     * @inheritdoc
     */
    protected function tableName()
    {
        return 'booking';
    }

    /**
     * @inheritdoc
     */
    protected function tableColumns()
    {
        return [
            'id' => $this->primaryKey(),
            'availableTimeId' => $this->integer()->notNull(),
            'userId' => $this->integer()->notNull(),
            'year' => $this->integer()->notNull(),
            'month' => $this->float()->null(),

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
            'availableTimeId' => false,
            'userId' => false,
            'year' => false,
            'month' => false,
            'availableTimeId, userId' => true
        ];
    }

    /**
     * @inheritdoc
     */
    public function tableForeignKeys()
    {
        return [
            [
                self::CFG_COLUMNS => 'availableTimeId',
                self::CFG_REF_TABLE => 'available_time',
                self::CFG_REF_COLUMNS => 'id',
                self::CFG_ON_DELETE => self::FK_CASCADE,
                self::CFG_ON_UPDATE => self::FK_RESTRICT,
            ],
            [
                self::CFG_COLUMNS => 'userId',
                self::CFG_REF_TABLE => 'user',
                self::CFG_REF_COLUMNS => 'id',
                self::CFG_ON_DELETE => self::FK_CASCADE,
                self::CFG_ON_UPDATE => self::FK_RESTRICT,
            ],
        ];
    }
}
