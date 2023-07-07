<?php

namespace kazda01\search;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Module;

class SearchModule extends Module implements BootstrapInterface
{
    public $allowGet = false;
    public $rules = [];
    public $searchResultClass = 'rounded p-1 ms-2';
    public $searchConfig = [];

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\web\Application) {
            $app->getUrlManager()->addRules([
                [
                    'class' => 'yii\web\UrlRule',
                    'pattern' => $this->id . '/search',
                    'route' => $this->id . '/search'
                ],
            ], false);
        }
    }

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
