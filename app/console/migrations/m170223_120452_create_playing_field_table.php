<?php

use flexibuild\migrate\db\CreateTableMigration;

class m170223_120452_create_playing_field_table extends CreateTableMigration
{
    /**
     * @inheritdoc
     */
    protected function tableName()
    {
        return 'playing_field';
    }

    /**
     * @inheritdoc
     */
    protected function tableColumns()
    {
        return [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'sportCenterId' => $this->integer()->notNull(),
            'price' => $this->float()->null(),
            'info' => $this->text()->null(),

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
            'sportCenterId' => false
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
        ];
    }
}
