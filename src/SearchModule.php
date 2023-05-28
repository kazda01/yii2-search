<?php

namespace kazda01\search;

use Yii;
use yii\base\Module;

class SearchModule extends Module
{
    public $allowGet = false;
    public $rules = [
        [
            'allow' => true,
            'roles' => ['@'],
            'actions' => ['index'],
        ],
    ];
    public $searchResultClass = 'rounded p-1 ms-2';
    public $config;

    public function init()
    {
        Yii::setAlias('@kazda01Search', __DIR__);

        parent::init();
    }

    public function getViewPath()
    {
        return Yii::getAlias('@kazda01Search/views');
    }

    public function run()
    {
        parent::run();

        SearchAsset::register($this->getView());

        echo $this->render('searchInput');
    }
}
