<?php

namespace oofbar\activity\widgets;

use Craft;
use craft\base\Widget;
use craft\db\Query;
use craft\db\Table;
use craft\helpers\ChartHelper;

use oofbar\activity\records\Event as EventRecord;

/**
 * Compact report widget.
 */
class Report extends Widget
{
    /**
     * @var string The event category to collect.
     */
    public $category;
}
