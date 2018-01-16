<?php

use flexibuild\migrate\db\Migration;

class m170228_141402_add_authKey_and_passwordHash_columns_in_user_table extends Migration
{
    private $table = 'user';
    private $columnAuthKey = 'authKey';
    private $columnPasswordHash = 'passwordHash';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn($this->table, $this->columnAuthKey, $this->string(32)->null());
        $this->addColumn($this->table, $this->columnPasswordHash, $this->string()->null());
        $this->createIndexAutoNamed($this->table, $this->columnAuthKey, true);
        $this->createIndexAutoNamed($this->table, $this->columnPasswordHash, true);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndexAutoNamed($this->table, $this->columnAuthKey, true);
        $this->dropIndexAutoNamed($this->table, $this->columnPasswordHash, true);
        $this->dropColumn($this->table, $this->columnAuthKey);
        $this->dropColumn($this->table, $this->columnPasswordHash);
    }
}
