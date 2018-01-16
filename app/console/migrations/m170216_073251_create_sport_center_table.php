<?php

use flexibuild\migrate\db\CreateTableMigration;

class m170216_073251_create_sport_center_table extends CreateTableMigration
{
    /**
     * @inheritdoc
     */
    protected function tableName()
    {
        return 'sport_center';
    }

    /**
     * @inheritdoc
     */
    protected function tableColumns()
    {
        return [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'phone' => $this->string()->null(),
            'logo' => $this->string()->null(),
            'image1' => $this->string()->null(),
            'image2' => $this->string()->null(),
            'image3' => $this->string()->null(),
            'image4' => $this->string()->null(),
            'address' => $this->text()->null(),
            'description' => $this->string()->null(),
            'latitude' => $this->string()->null(),
            'longitude' => $this->string()->null(),
            'approvementStatus' => $this->boolean()->defaultValue(false),

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
            'name' => true,
            'phone' => false,
            'approvementStatus' => false,
        ];
    }
}
