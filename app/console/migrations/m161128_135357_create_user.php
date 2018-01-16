<?php

use flexibuild\migrate\db\CreateTableMigration;

class m161128_135357_create_user extends CreateTableMigration
{
    /**
     * @inheritdoc
     */
    protected function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    protected function tableColumns()
    {
        return [
            'id' => $this->primaryKey(),
            'name' => $this->string()->null(),
            'email' => $this->string()->null(),
            'role' => $this->string()->notNull(),

            'phone' => $this->string()->notNull(),
            'description' => $this->text()->null(),

            'apiToken' => $this->string(64)->notNull(),

            'code' => $this->string()->null(),
            'facebook' => $this->string()->null(),
            'google' => $this->string()->null(),
            'linkedin' => $this->string()->null(),

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
            'name' => false, // unique
            'phone' => true, // unique
            'email' => true, // unique
            'apiToken' => true, // unique
        ];
    }
}
