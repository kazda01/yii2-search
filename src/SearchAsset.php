<?php

/**
 * @copyright Copyright (c) 2018
 * @author Alexandr Kozhevnikov <onmotion1@gmail.com>
 * @package yii2-widget-apexcharts
 */

namespace kazda01\search;

class SearchAsset extends \yii\web\AssetBundle
{
    public function init(): void
    {
        $this->sourcePath = __DIR__ . '/assets';

        $this->js = [
            'search.js'
        ];

        parent::init();
    }

    /**
     * @var string[]
     */
    public $css = [
        'search.css'
    ];

    /**
     * @var string[]
     */
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap5\BootstrapAsset'
    ];
}
