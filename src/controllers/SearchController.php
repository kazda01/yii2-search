<?php

namespace kazda01\search\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * @author Antonín Kazda (kazda01)
 * @author Radim Mifka (mifka01)
 *
 * SearchController implements model searching.
 */
class SearchController extends Controller
{
    // Search params from module config
    // For more info and examples see the documentation
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

    /**
     * Get search results for specific object name and search string.
     *
     * @param string $searchObjectName
     * @param string $search
     * @return array
     */
    public function getSearchResults($searchObjectName, $search)
    {
        $results = [];
        foreach ($this->searchParams[$searchObjectName]["columns"] as $column) {
            // Create search object and search
            $searchObjectClass = "app\\models\\search\\" . $searchObjectName;
            $searchObject = new $searchObjectClass();
            $searchResult = $searchObject->search([$searchObjectName => [$column => $search]]);

            // Group by if specified
            if (array_key_exists('group_by', $this->searchParams[$searchObjectName])) {
                if (
                    (is_bool($this->searchParams[$searchObjectName]['group_by']) && $this->searchParams[$searchObjectName]['group_by']) ||
                    is_array($this->searchParams[$searchObjectName]['group_by']) ||
                    is_string($this->searchParams[$searchObjectName]['group_by'])
                ) {
                    if (is_bool($this->searchParams[$searchObjectName]['group_by'])) {
                        $group_by_over = $this->searchParams[$searchObjectName]['columns'];
                    } else {
                        $group_by_over = $this->searchParams[$searchObjectName]['group_by'];
                    }

                    $subquery = clone $searchResult->query;
                    $primary_key = $subquery->modelClass::primaryKey()[0];
                    $subquery->select("MAX($primary_key) AS 'search_max_primary_key'")->groupBy($group_by_over);
                    $searchResult->query->rightJoin(['search_grouped' => $subquery], "$primary_key = search_grouped.search_max_primary_key");
                }
            }

            // Add results to array and process route
            foreach ($searchResult->getModels() as $model) {
                if (array_key_exists('only_if', $this->searchParams[$searchObjectName])) {
                    if (!$this->searchParams[$searchObjectName]['only_if']($model)) {
                        continue;
                    }
                }

                if (array_key_exists('route', $this->searchParams[$searchObjectName])) {
                    $route = [$this->searchParams[$searchObjectName]['route']];
                } else {
                    $route = ['/' . str_replace('_', '-', $model->tableName()) . '/view'];
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

        foreach ($this->searchParams as $searchObjectName => $searchParam) {
            $tableResults = $this->getSearchResults($searchObjectName, $search);
            if (!empty($tableResults)) {
                $matchTitle = $searchParam['matchTitle'];
                if (is_callable($matchTitle)) {
                    $matchTitle = $matchTitle();
                }
                $results[] = [
                    'matchTitle' => $matchTitle,
                    'table' => $tableResults,
                ];
            }
        }

        return $this->renderPartial('/searchWidget', [
            'results' => $results,
            'search' => $search,
            'searchResultClass' => $this->module->searchResultClass,
        ]);
    }
}
