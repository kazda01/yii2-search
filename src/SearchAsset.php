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
            defined('YII_ENV_DEV') && YII_ENV_DEV ? 'search.js' : 'search.min.js'
        ];
        
        parent::init();
    }

    public $css = [
        'search.css'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap5\BootstrapAsset'
    ];
}
