<?php
namespace oofbar\activity\web\assets\xhr;

use craft\web\AssetBundle;

/**
 * Dependency-free XHR implementation designed for redeeming tokenized tracking requests.
 * 
 * The developer is free to implement this in their own way, and use the `trackAsync()` method instead of `trackXhr()`.
 */
class XhrAsset extends AssetBundle
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
            'js/Xhr.js'
        ];

        parent::init();
    }
}
