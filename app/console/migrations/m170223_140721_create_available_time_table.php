<?php

use flexibuild\migrate\db\CreateTableMigration;

class m170223_140721_create_available_time_table extends CreateTableMigration
{
    /**
     * @inheritdoc
     */
    protected function tableName()
    {
        return 'available_time';
    }

    /**
     * @inheritdoc
     */
    protected function tableColumns()
    {
        return [
            'id' => $this->primaryKey(),
            'playingFieldId' => $this->integer()->notNull(),
            'hour' => $this->integer()->notNull(),
            'price' => $this->float()->null(),
            'type' => $this->typeEnum(['work', 'weekend'], 'work'),

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
            'playingFieldId' => false,
            'hour' => false,
            'price' => false,
            'type' => false,
        ];
    }

    /**
     * @inheritdoc
     */
    public function tableForeignKeys()
    {
        return [
            [
                self::CFG_COLUMNS => 'playingFieldId',
                self::CFG_REF_TABLE => 'playing_field',
                self::CFG_REF_COLUMNS => 'id',
                self::CFG_ON_DELETE => self::FK_CASCADE,
                self::CFG_ON_UPDATE => self::FK_RESTRICT,
            ],
        ];
    }
}
