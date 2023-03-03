<?php

use yii\db\Migration;

/**
 * Class m230303_033350_orders
 */
class m230303_033350_orders extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('order', [
            'id' => $this->primaryKey(),
            'sum' => $this->integer()->comment('Сумма'),
            'created_at' => $this->dateTime()->defaultValue(date("Y-m-d H:i:s"))->notNull()->comment('Дата создание')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('order');
    }
}
