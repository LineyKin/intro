<?php

use yii\db\Migration;

class m250408_071830_add_index extends Migration
{
    const TABLE_NAME = 'orders';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx_service_id', self::TABLE_NAME, 'service_id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_service_id', self::TABLE_NAME);
    }
}
