<?php

namespace oofbar\activity\migrations;

use craft\db\Migration;
use craft\db\Table;
use craft\helpers\MigrationHelper;
use oofbar\activity\records\Event as EventRecord;

class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(EventRecord::tableName(), [
            'id' => $this->primaryKey(),
            'elementId' => $this->integer(),
            'category' => $this->string()->notNull(),
            'value' => $this->integer()->notNull()->defaultValue(1),
            'detail' => $this->text(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        // Ensure integrity of events with Element ID references, and clean them when the Element is deleted:
        $this->addForeignKey(null, EventRecord::tableName(), ['elementId'], Table::ELEMENTS, ['id'], 'CASCADE');

        // Make filtering by `category` more efficient:
        $this->createIndex(null, EventRecord::tableName(), 'category');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        MigrationHelper::dropForeignKeyIfExists(EventRecord::tableName(), ['elementId'], $this);

        $this->dropTable(EventRecord::tableName());
    }
}
