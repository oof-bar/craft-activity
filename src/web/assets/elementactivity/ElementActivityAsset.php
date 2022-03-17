<?php
namespace oofbar\activity\web\assets\elementactivity;

use craft\web\AssetBundle;

/**
 * Accompanies markup injected via hooks on each Element type's edit page.
 */
class ElementActivityAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__ . '/dist';

        $this->depends = [];

        $this->css = [];

        $this->js = [
            'js/ElementActivity.js'
        ];

        parent::init();
    }
}
