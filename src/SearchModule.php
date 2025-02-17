<?php

namespace kazda01\search;

use kazda01\search\services\SearchService;
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
     * @var array<mixed> $rules Access rules for search
     */
    public $rules = [];

    /**
     * @var string $searchResultClass Class for search result
     */
    public $searchResultClass = 'rounded';

    /**
     * @var array<mixed> $searchConfig Search config
     */
    public $searchConfig = [];

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app): void
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

    public function init(): void
    {
        Yii::setAlias('@kazda01Search', __DIR__);

        Yii::$container->set(SearchService::class);

        parent::init();
    }
}
