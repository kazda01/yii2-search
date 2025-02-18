<?php

namespace kazda01\search\services;

use kazda01\search\models\SearchResult;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;

class SearchService
{
    const SEARCH_PAGE_SIZE = 20;

    const SEARCH_PAGE_QUERY_PARAM = 'page';

    /**
     * @param string $moduleId
     * @return array<mixed>
     */
    private function getSearchConfig(string $moduleId): array
    {
        if (Yii::$app->getModule($moduleId) === null) {
            throw new InvalidConfigException("Configuration for search '$moduleId' is not defined in web.php config (config[modules][$moduleId]).");
        }
        return Yii::$app->getModule($moduleId)?->searchConfig ?? [];
    }

    /**
     * Search for string in all search objects.
     *
     * @param string $search
     * @param string $moduleId
     * @param null|array<string>|string $filterSearch Filter which ModelSearch from $moduleId search config should apply, if null, all will be applied
     * @param bool $paginate
     * @return ArrayDataProvider
     */
    public function search(string $search, string $moduleId, $filterSearch = null, bool $paginate = false)
    {
        if (is_string($filterSearch)) {
            $filterSearch = [$filterSearch];
        }
        $searchConfig = $this->getSearchConfig($moduleId);
        $results = [];

        foreach ($searchConfig as $searchObjectName => $currentSearchConfig) {
            if ($filterSearch !== null && !in_array($searchObjectName, $filterSearch)) {
                continue;
            }
            $searchResults = $this->getSearchResults($searchObjectName, $currentSearchConfig, $search, $paginate);
            $results = array_merge($results, $searchResults);
        }

        $currentPage = Yii::$app->request->get(self::SEARCH_PAGE_QUERY_PARAM, 1);
        return new ArrayDataProvider([
            'allModels' => $results,
            'pagination' => $paginate ? [
                'totalCount' => count($results) > self::SEARCH_PAGE_SIZE ? self::SEARCH_PAGE_SIZE * $currentPage + 1 : self::SEARCH_PAGE_SIZE * $currentPage,
                'pageSize' => self::SEARCH_PAGE_SIZE,
            ] : false,
        ]);
    }

    /**
     * @param \yii\data\ActiveDataProvider $searchResult
     * @param array<mixed> $searchConfig
     * @param bool $paginate
     */
    private function applyPagination($searchResult, $searchConfig, $paginate): void
    {
        // Apply pagination
        if ($paginate) {
            $searchResult->pagination = false;
            $currentPage = Yii::$app->request->get(self::SEARCH_PAGE_QUERY_PARAM, 1);

            $searchResult->query->offset(($currentPage - 1) * self::SEARCH_PAGE_SIZE);
            $searchResult->query->limit(self::SEARCH_PAGE_SIZE + 1);
            return;
        }

        // Set maximum limit based on config or default pagination size
        $limit = $searchResult->pagination->pageSize;
        $searchResult->pagination = false;
        if (array_key_exists('limit', $searchConfig)) {
            $limit = $searchConfig['limit'];
        }
        $searchResult->query->limit($limit);
    }

    /**
     * @param \yii\data\ActiveDataProvider $searchResult
     * @param array<mixed> $searchConfig
     */
    private function applyGroupBy($searchResult, $searchConfig): void
    {
        if (array_key_exists('group_by', $searchConfig)) {
            if ((is_bool($searchConfig['group_by']) && $searchConfig['group_by']) ||
                is_array($searchConfig['group_by']) ||
                is_string($searchConfig['group_by'])
            ) {
                if (is_bool($searchConfig['group_by'])) {
                    $group_by_over = $searchConfig['columns'];
                } else {
                    $group_by_over = $searchConfig['group_by'];
                }

                /** @var \yii\db\ActiveQuery */
                $subquery = clone $searchResult->query;
                $primaryKey = $subquery->modelClass::primaryKey()[0];
                $subquery->select("MAX($primaryKey) AS 'search_max_primary_key'")->groupBy($group_by_over);
                $subquery->rightJoin(['search_grouped' => $subquery], "$primaryKey = search_grouped.search_max_primary_key");
            }
        }
    }

    /**
     * Get search results for specific object name and search string.
     *
     * @param string $searchObjectName classname of search object
     * @param mixed $searchConfig search configuration of search object
     * @param string $search
     * @param bool $paginate
     * @return array<SearchResult>
     */
    private function getSearchResults(string $searchObjectName, $searchConfig, string $search, bool $paginate)
    {
        $matchTitle = $this->evaluateMatchTitle($searchConfig);
        $results = [];

        foreach ($searchConfig["columns"] as $column) {
            // Create search object and search
            $searchObjectClass = $searchObjectName;
            if (mb_strpos($searchObjectName, '\\') === false) {
                $searchObjectClass = "app\\models\\search\\" . $searchObjectClass;
            }
            $splitClass = explode('\\', $searchObjectClass);
            $searchObjectClassname = end($splitClass);

            $searchObject = new $searchObjectClass();
            /** @var \yii\data\ActiveDataProvider $searchResult */
            $searchResult = $searchObject->search([$searchObjectClassname => [$column => $search]]);

            $this->applyPagination($searchResult, $searchConfig, $paginate);
            $this->applyGroupBy($searchResult, $searchConfig);

            // Add results to array and process route
            foreach ($searchResult->getModels() as $model) {
                if (array_key_exists('only_if', $searchConfig) && !$searchConfig['only_if']($model)) {
                    continue;
                }

                $route = $this->evaluateRoute($searchConfig, $model);

                $results[] = new SearchResult([
                    'model' => $model,
                    'matchText' => $searchConfig['matchText']($model),
                    'match' => $column,
                    'route' => $route,
                    'matchTitle' => $matchTitle,
                ]);
            }
        }

        return $results;
    }

    /**
     * @param array<mixed> $searchConfig
     * @param \yii\db\ActiveRecord $model
     * @return array<mixed> Route to redirect after click, in format for Url::to() function
     */
    private function evaluateRoute($searchConfig, $model): array
    {
        if (array_key_exists('route', $searchConfig)) {
            $route = [$searchConfig['route']];
        } else {
            $route = ['/' . str_replace('_', '-', $model->tableName()) . '/view'];
        }

        if (array_key_exists('route_params', $searchConfig)) {
            foreach ($searchConfig['route_params']($model) as $key => $value) {
                $route[$key] = $value;
            }
        } else {
            $route[array_keys($model->getPrimaryKey(true))[0]] = $model->primaryKey;
        }

        return $route;
    }

    /**
     * @param array<mixed> $searchConfig
     */
    private function evaluateMatchTitle($searchConfig): string
    {
        $matchTitle = $searchConfig['matchTitle'];
        if (is_callable($matchTitle)) {
            $matchTitle = $matchTitle();
        }
        return $matchTitle;
    }
}
