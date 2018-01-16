<?php

use flexibuild\migrate\db\CreateTableMigration;

class m170213_142910_create_company_table extends CreateTableMigration
{
    /**
     * @inheritdoc
     */
    protected function tableName()
    {
        return 'company';
    }

    /**
     * @inheritdoc
     */
    protected function tableColumns()
    {
        return [
            'id' => $this->primaryKey(),

            'latitude' => $this->string()->null(),
            'longitude' => $this->string()->null(),
            'logo' => $this->string()->null(),
            'image1' => $this->string()->null(),
            'image2' => $this->string()->null(),
            'image3' => $this->string()->null(),
            'image4' => $this->string()->null(),
            
            'active' => $this->typeEnum(['published', 'not published', 'request publication'], 'not published'),

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
            'active' => false,
        ];
    }
}
