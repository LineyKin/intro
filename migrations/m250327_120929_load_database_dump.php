<?php

use yii\db\Migration;

class m250327_120929_load_database_dump extends Migration
{
    const ORDERS_TABLE = 'orders';
    const SERVICES_TABLE = 'services';
    const USERS_TABLE = 'users';

    const STRUCTURE_DUMP = 'test_db_structure.sql';
    const DATA_DUMP = 'test_db_data.sql';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sqlStructureFile = Yii::getAlias('@app/' . self::STRUCTURE_DUMP);
        $sqlContent = file_get_contents($sqlStructureFile);
        $this->execute($sqlContent);
        unset($sqlStructureFile);

        $sqlDataFile = Yii::getAlias('@app/' . self::DATA_DUMP);
        $sqlContent = file_get_contents($sqlDataFile);
        $queries = explode(";\n", $sqlContent);
        unset($sqlContent);

        foreach ($queries as $query) {
            $this->execute($query);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::ORDERS_TABLE);
        $this->dropTable(self::SERVICES_TABLE);
        $this->dropTable(self::USERS_TABLE);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250327_120929_load_database_dump cannot be reverted.\n";

        return false;
    }
    */
}
