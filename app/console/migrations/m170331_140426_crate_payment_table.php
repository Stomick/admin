<?php

use flexibuild\migrate\db\CreateTableMigration;

class m170331_140426_crate_payment_table extends CreateTableMigration
{
    /**
     * @inheritdoc
     */
    protected function tableName()
    {
        return 'payment';
    }

    /**
     * @inheritdoc
     */
    protected function tableColumns()
    {
        return [
            'id' => $this->primaryKey(),
            'orderstatus' => $this->string()->null(),
            'refno' => $this->string()->null(),
            'saledate' => $this->string()->null(),
            'paymethod' => $this->string()->null(),
            'country' => $this->string()->null(),
            'phone' => $this->string()->null(),
            'customeremail' => $this->string()->null(),
            'currency' => $this->text()->null(),
            'hash' => $this->string()->null(),
            'refnoext' => $this->integer()->null(),
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
            'orderstatus' => false,
        ];
    }

    /**
     * @inheritdoc
     */
    public function tableForeignKeys()
    {
        return [
            [
                self::CFG_COLUMNS => 'refnoext',
                self::CFG_REF_TABLE => 'booking',
                self::CFG_REF_COLUMNS => 'id',
                self::CFG_ON_DELETE => self::FK_CASCADE,
                self::CFG_ON_UPDATE => self::FK_RESTRICT,
            ],
        ];
    }
}
