<?php

use flexibuild\migrate\db\Migration;

class m170405_072958_add_kpp_column_in_company_details_table extends Migration
{
    private $table = 'company_details';
    private $columnKPP = 'kpp';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn($this->table, $this->columnKPP, $this->string()->null());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn($this->table, $this->columnKPP);
    }
}
