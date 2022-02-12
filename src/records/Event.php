<?php

namespace oofbar\activity\records;

use craft\db\ActiveRecord;

class Event extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%activity_events}}';
    }
}
