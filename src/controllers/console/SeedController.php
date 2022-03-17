<?php

namespace oofbar\activity\controllers\console;

use craft\console\Controller;
use craft\base\Element;
use craft\db\Query;
use craft\db\Table;
use craft\helpers\Console;
use craft\helpers\Db;
use oofbar\activity\Activity;
use yii\console\ExitCode;

use oofbar\activity\records\Event as EventRecord;

/**
 * Utilies to assist with seeding test events.
 */
class SeedController extends Controller
{
    /**
     * @var int Number of events to create.
     */
    public $num = 100;

    /**
     * @var string Event "category" to use.
     */
    public $category = 'seeded-event';

    /**
     * @var string Interval expression defining the earliest relative date an Event will be generated for.
     */
    public $within;

    /**
     * @inheritdoc
     */
    public function options($actionId)
    {
        $options = parent::options($actionId);

        $options[] = 'num';
        $options[] = 'category';
        $options[] = 'within';

        return $options;
    }

    /**
     * Generates Events without target Elements.
     * 
     * @param 
     */
    public function actionIndex()
    {}

    /**
     * Generates Events for the specified Element type.
     * 
     * @var string $elementType Element type class to create Events for.
     */
    public function actionElements($elementType)
    {
        try {
            $seeded = Activity::getInstance()->getSeed()->createEvents(
                $this->num,
                $this->category,
                $elementType,
                $this->within,
            );
        } catch (\Exception $e) {
            $this->stderr("{$e->getMessage()}\n", Console::FG_RED);
            $this->stderr($e);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout(sprintf("Seeded %d events!\n", $seeded), Console::FG_GREEN);
    }
}
