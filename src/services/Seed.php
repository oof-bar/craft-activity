<?php

namespace oofbar\activity\services;

use Craft;
use craft\base\Component;
use craft\db\Query;
use craft\db\Table;
use craft\helpers\Db;
use craft\helpers\StringHelper;

use yii\base\InvalidConfigException;

use oofbar\activity\records\Event as EventRecord;

class Seed extends Component
{
    /**
     * @var string Default Event category to assign.
     */
    const DEFAULT_CATEGORY = 'seeded-event';

    /**
     * @var string Default interval expression used to distribute Event dates.
     */
    const DEFAULT_PERIOD = 'P1M';

    /**
     * @var int Maximum number of records to insert in each batch.
     */
    const MAX_BATCH_SIZE = 10000;

    /**
     * Creates fake Event records.
     * 
     * @param int $num Number of events to seed.
     * @param string $category
     * @param string $elementType Optional Element Type
     * @param string $recency Optional interval expression for how long ago
     * @return int Number of inserted rows.
     * @throws InvalidConfigException
     */
    public function createEvents(
        int $num,
        string $category = null,
        string $elementType = null,
        string $recency = null,
    ): int
    {
        $now = (new \DateTime);
        $edge = (clone $now)->sub(new \DateInterval($recency ?? self::DEFAULT_PERIOD));

        Craft::info(sprintf('Getting ready to seed %d total Events...', $num), 'activity');

        $elementIdsQuery = (new Query)
            ->from([Table::ELEMENTS])
            ->select(['id']);

        if (class_exists($elementType) && is_a($elementType, Element::class, true)) {
            $elementIdsQuery->where([
                'type' => $elementType,
            ]);
        }

        $elementIds = $elementIdsQuery->column();

        $inserted = 0;

        while ($inserted < $num) {
            $events = [];
            $remaining = $num - $inserted;
            $batchSize = min($remaining, self::MAX_BATCH_SIZE);

            Craft::info(sprintf('Generating batch of %d Events!', $batchSize), 'activity');

            while ($batchSize > 0) {
                $date = Db::prepareDateForDb(new \DateTime('@' . mt_rand($edge->getTimestamp(), $now->getTimestamp())));

                $events[] = [
                    'elementId' => $elementIds[array_rand($elementIds)],
                    'category' => $category ?? self::DEFAULT_CATEGORY,
                    'value' => 1,
                    'detail' => 'This record was seeded programmatically. It does not represent an actual event!',
                    'dateCreated' => $date,
                    'dateUpdated' => $date,
                    'uid' => StringHelper::UUID(),
                ];

                $batchSize--;
            }

            $rows = Db::batchInsert(EventRecord::tableName(), [
                'elementId',
                'category',
                'value',
                'detail',
                'dateCreated',
                'dateUpdated',
                'uid',
            ], $events, false);

            Craft::info(sprintf('Inserted %d Event records.', $rows), 'activity');

            $inserted = $inserted + $rows;
        }

        return $inserted;
    }
}
