<?php

use flexibuild\migrate\db\CreateTableMigration;

class m170309_091404_create_booking_service_table extends CreateTableMigration
{
    /**
     * @inheritdoc
     */
    protected function tableName()
    {
        return 'booking_service';
    }

    /**
     * @inheritdoc
     */
    protected function tableColumns()
    {
        return [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'bookingId' => $this->integer()->notNull(),
            'price' => $this->float()->notNull(),

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
            'name' => false,
            'price' => false,
            'bookingId' => false
        ];
    }

    /**
     * @inheritdoc
     */
    public function tableForeignKeys()
    {
        return [
            [
                self::CFG_COLUMNS => 'bookingId',
                self::CFG_REF_TABLE => 'booking',
                self::CFG_REF_COLUMNS => 'id',
                self::CFG_ON_DELETE => self::FK_CASCADE,
                self::CFG_ON_UPDATE => self::FK_RESTRICT,
            ],
        ];
    }
}
