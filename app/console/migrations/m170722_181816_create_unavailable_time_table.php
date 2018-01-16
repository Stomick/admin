<?php

use flexibuild\migrate\db\CreateTableMigration;

class m170722_181816_create_unavailable_time_table extends CreateTableMigration
{
    /**
     * @inheritdoc
     */
    protected function tableName()
    {
        return 'unavailable_time';
    }

    /**
     * @inheritdoc
     */
    protected function tableColumns()
    {
        return [
            'id' => $this->primaryKey(),
            'createdAt' => $this->integer()->notNull(),
            'updatedAt' => $this->integer()->notNull(),
            'date' => $this->date()->notNull(),
            'hour' => $this->integer()->notNull(),
            'playingFieldId' => $this->integer()->notNull(),
            'userId' => $this->integer()->notNull(),

        ];
    }

    /**
     * @inheritdoc
     */
    protected function tableForeignKeys()
    {
        return [
            [
                self::CFG_COLUMNS => 'userId',
                self::CFG_REF_TABLE => 'user',
                self::CFG_REF_COLUMNS => 'id',
                self::CFG_ON_DELETE => self::FK_CASCADE, // optional, restrict by default
                self::CFG_ON_UPDATE => self::FK_RESTRICT, // optional, restrict by default
                self::CFG_UNIQUE => false, // optional, false by default
            ],[
                self::CFG_COLUMNS => 'playingFieldId',
                self::CFG_REF_TABLE => 'playing_field',
                self::CFG_REF_COLUMNS => 'id',
                self::CFG_ON_DELETE => self::FK_CASCADE, // optional, restrict by default
                self::CFG_ON_UPDATE => self::FK_RESTRICT, // optional, restrict by default
                self::CFG_UNIQUE => false, // optional, false by default
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function tableIndexes()
    {
        return [
          'playingFieldId' => false,
          'date' => false,
          'hour' => false,
        ];
    }
}
