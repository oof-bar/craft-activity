<?php

namespace oofbar\activity\widgets;

use Craft;
use craft\base\Widget;
use craft\db\Query;
use craft\db\Table;
use craft\helpers\ChartHelper;
use oofbar\activity\Activity;
use oofbar\activity\records\Event as EventRecord;

/**
 * Simple aggregate event plots.
 */
class Chart extends Widget
{
    /**
     * @var string Element class name to filter events by.
     */
    public $elementType;

    /**
     * @var string The event category to collect.
     */
    public $category;

    /**
     * @var string The interval to use when tallying events.
     */
    public $interval = 'day';

    /**
     * @var int The number of intervals to tally.
     */
    public $duration = 7;

    /**
     * @var string The SQL method to use when collecting events.
     */
    public $aggregationMethod = 'sum';

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('activity', 'Activity Chart');
    }

    /**
     * @inheritdoc
     */
    public static function icon()
    {
        return Craft::getAlias('@activity/web/icons/cp-chart.svg');
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return Craft::t('activity', 'Tracked Events');
    }

    /**
     * @inheritdoc
     */
    protected function defineRules(): array
    {
        $rules = [];

        $rules[] = [
            ['interval'],
            'in',
            'range' => ['day', 'month', 'year'],
        ];

        $rules[] = [
            ['duration'],
            'integer',
            'min' => 2,
        ];

        $rules[] = [
            ['aggregationMethod'],
            'in',
            'range' => ['sum', 'count'],
        ];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getBodyHtml()
    {
        $query = Activity::getInstance()->getEvents()->getQuery();

        if (!is_null($this->category)) {
            $query->where(['activity_events.category' => $this->category]);
        }

        if (!is_null($this->elementType)) {
            $query->leftJoin(['elements' => Table::ELEMENTS]);
            $query->andWhere([
                'elements.type' => $this->elementType,
                'elements.dateDeleted' => null,
            ]);
        }

        $dateStart = (new \DateTime)->modify("-{$this->duration} {$this->interval}");
        $dateEnd = (new \DateTime);

        $data = ChartHelper::getRunChartDataFromQuery($query, $dateStart, $dateEnd, 'activity_events.dateCreated', $this->aggregationMethod, 'value', [
            'intervalUnit' => $this->interval,
            'valueLabel' => Craft::t('activity', 'Events'),
        ]);

        return Craft::$app->getView()->renderTemplate('activity/_widgets/chart/body', [
            'widget' => $this,
            'data' => $data,
            'formats' => ChartHelper::formats(),
            'orientation' => Craft::$app->getLocale()->getOrientation(),
            'scale' => $this->interval,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('activity/_widgets/chart/settings', [
            'widget' => $this,
        ]);
    }

    /**
     * Returns a list of unique categories in the database.
     * 
     * @return string[]
     */
    public function getCategorySuggestions(): array
    {
        $query = Activity::getInstance()->getEvents()->getQuery();

        return $query
            ->select(['category'])
            ->distinct()
            ->column();
    }
}
