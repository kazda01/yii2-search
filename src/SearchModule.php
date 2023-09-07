<?php

namespace kazda01\search;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Module;

class SearchModule extends Module implements BootstrapInterface
{
    /**
     * @var bool $allowGet Allow GET method for search
     */
    public $allowGet = false;

    /**
     * @var array $rules Access rules for search
     */
    public $rules = [];

    /**
     * @var string $searchResultClass Class for search result
     */
    public $searchResultClass = 'rounded';

    /**
     * @var array $searchConfig Search config
     */
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
