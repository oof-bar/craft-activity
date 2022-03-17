<?php
namespace oofbar\activity;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\services\Dashboard;
use craft\web\View;

use yii\base\Event;

use oofbar\activity\services\Events;
use oofbar\activity\services\Reports;
use oofbar\activity\services\Seed;
use oofbar\activity\twig\ActivityExtension;
use oofbar\activity\widgets\Chart;

/**
 * Base Activity plugin class.
 * 
 * Responsible for bootstrapping and injecting custom features.
 */
class Activity extends Plugin
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Craft::setAlias('@activity', $this->getBasePath());

        $this->setComponents([
            'events' => Events::class,
            'reports' => Reports::class,
            'seed' => Seed::class,
        ]);

        // Set the controller namespace based on the type of request:
        $request = Craft::$app->getRequest();

        if ($request->getIsConsoleRequest()) {
            $this->controllerNamespace = 'oofbar\\activity\\controllers\\console';
        } else if ($request->getIsCpRequest()) {
            $this->controllerNamespace = 'oofbar\\activity\\controllers\\cp';
        } else {
            $this->controllerNamespace = 'oofbar\\activity\\controllers\\site';
        }

        $view = Craft::$app->getView();

        $view->registerTwigExtension(new ActivityExtension);

        // Element Edit View Hooks
        $elementEditHandler = [$this->getReports(), 'renderElementActivity'];

        $view->hook('cp.assets.edit.content', $elementEditHandler);
        $view->hook('cp.categories.edit.content', $elementEditHandler);
        $view->hook('cp.entries.edit.content', $elementEditHandler);
        $view->hook('cp.globals.edit.content', $elementEditHandler);
        $view->hook('cp.users.edit.content', $elementEditHandler);

        Event::on(
            View::class,
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
            function (RegisterTemplateRootsEvent $e) {
                $e->roots[$this->id] = Craft::getAlias('@activity/templates');
            });

        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $e) {
                $e->types[] = Chart::class;
            });
    }

    /**
     * Returns the Events service/component.
     * 
     * @return Events
     */
    public function getEvents(): Events
    {
        return $this->get('events');
    }

    /**
     * Returns the Reports service/component.
     * 
     * @return Reports
     */
    public function getReports(): Reports
    {
        return $this->get('reports');
    }

    /**
     * Returns the Seed service/component.
     * 
     * @return Seed
     */
    public function getSeed(): Seed
    {
        return $this->get('seed');
    }
}
