<?php

namespace oofbar\activity\controllers\console;

use craft\console\Controller;

/**
 * Utilies to assist with seeding test events.
 */
class SeedController extends Controller
{
    /**
     * @var string Fully-qualified Element type to populate Events on.
     */
    public $elementType;

    /**
     * @var int Number of Events to create.
     */
    public $count;

    /**
     * @var \DateTime Earliest date to create Events.
     */
    public $after;

    /**
     * @var \DateTime Latest date to create Events.
     */
    public $before;

    /**
     * @var string Event "category" to use.
     */
    public $category;

    /**
     * @inheritdoc
     */
    public function options($actionId)
    {
        $options = parent::options($actionId);

        $options[] = 'elementType';
        $options[] = 'count';
        $options[] = 'after';
        $options[] = 'before';
    }

    /**
     * Generates Events without target Elements.
     * 
     * @param 
     */
    public function actionIndex()
    {
        
    }

    /**
     * Generates Events for Entries.
     * 
     * @param 
     */
    public function actionEntries()
    {

    }
}
