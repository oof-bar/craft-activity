<?php

namespace oofbar\activity\services;

use Craft;
use craft\base\Component;

use oofbar\activity\web\assets\elementactivity\ElementActivityAsset;

class Reports extends Component
{
    /**
     * Injects statistics into Element edit views.
     * 
     * @param array $context Current Twig template context.
     * @return string Markup to render
     */
    public function renderElementActivity(&$context): string
    {
        $view = Craft::$app->getView();

        $element = $context['element'] ?? null;

        if (is_null($element) || is_null($element->id)) {
            return '';
        }

        $view->registerAssetBundle(ElementActivityAsset::class);

        return $view->renderTemplate('activity/_reporting/element', [
            'element' => $element,
        ]);
    }
}
