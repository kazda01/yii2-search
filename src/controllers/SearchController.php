<?php

namespace kazda01\search\controllers;

use Yii;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * SearchController implements model searching.
 */
class SearchController extends Controller
{
    private $searchParams;

    public function init()
    {
        $this->searchParams = $this->module->searchConfig;
        parent::init();
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => (bool) $this->module->allowGet ? ['POST', 'GET'] : ['POST'],
                ],
            ],
        ];

        if (!empty($this->module->rules)) {
            $behaviors['access'] = [
                'class' => AccessControl::class,
                'rules' => $this->module->rules
            ];
        }

        return $behaviors;
    }

    public function getSearchResults($searchObjectName, $search)
    {
        $results = [];
        foreach ($this->searchParams[$searchObjectName]["columns"] as $column) {
            $searchObjectClass = "app\\models\\search\\" . $searchObjectName;
            $searchObject = new $searchObjectClass;
            $searchResult = $searchObject->search([$searchObjectName => [$column => $search]]);

            if (array_key_exists('group_by', $this->searchParams[$searchObjectName])) {
                if ((is_bool($this->searchParams[$searchObjectName]['group_by']) && $this->searchParams[$searchObjectName]['group_by']) ||
                    is_array($this->searchParams[$searchObjectName]['group_by']) ||
                    is_string($this->searchParams[$searchObjectName]['group_by'])
                ) {
                    if (is_bool($this->searchParams[$searchObjectName]['group_by'])) $group_by_over = $this->searchParams[$searchObjectName]['columns'];
                    else $group_by_over = $this->searchParams[$searchObjectName]['group_by'];

                    $subquery = clone $searchResult->query;
                    $primary_key = $subquery->modelClass::primaryKey()[0];
                    $subquery->select("MAX($primary_key) AS 'search_max_primary_key'")->groupBy($group_by_over);
                    $searchResult->query->rightJoin(['search_grouped' => $subquery], "$primary_key = search_grouped.search_max_primary_key");
                }
            }

            foreach ($searchResult->getModels() as $model) {
                if (array_key_exists('only_if', $this->searchParams[$searchObjectName])) {
                    if (!$this->searchParams[$searchObjectName]['only_if']($model)) {
                        continue;
                    }
                }

                if (array_key_exists('route', $this->searchParams[$searchObjectName])) {
                    $route = [$this->searchParams[$searchObjectName]['route']];
                } else {
                    $route = [str_replace('_', '-', $model->tableName()) . '/view'];
                }

                if (array_key_exists('route_params', $this->searchParams[$searchObjectName])) {
                    foreach ($this->searchParams[$searchObjectName]['route_params']($model) as $key => $value) {
                        $route[$key] = $value;
                    }
                } else {
                    $route[array_keys($model->getPrimaryKey(true))[0]] = $model->primaryKey;
                }

                $results[] = [
                    'model' => $model,
                    'matchText' => $this->searchParams[$searchObjectName]['matchText']($model),
                    'match' => $column,
                    'route' => $route,
                ];
            }
        }
        return $results;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->request->isPost) {
            $search = Yii::$app->request->post('search');
        } else {
            $search = Yii::$app->request->get('search');
        }
        $results = [];

        foreach ($this->searchParams as $key => $_) {
            $tableResults = $this->getSearchResults($key, $search);
            if (!empty($tableResults)) $results[] = [
                'matchTitle' => $this->searchParams[$key]['matchTitle'],
                'table' => $tableResults,
            ];
        }

        return $this->renderPartial('/searchWidget', [
            'results' => $results,
            'search' => $search,
            'searchResultClass' => $this->module->searchResultClass,
        ]);
    }
}
