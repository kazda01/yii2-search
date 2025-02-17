<?php

namespace kazda01\search\services;

use Yii;
use yii\base\InvalidConfigException;

class SearchService
{
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
     * @param bool $ignoreLimits
     * @return array<int, array{matchTitle: string, table: array<int, array{model: \yii\db\ActiveRecord, matchText: string, match: string, route: array<string, mixed>}>}>
     */
    public function search(string $search, string $moduleId, bool $ignoreLimits = false): array
    {
        $searchConfig = $this->getSearchConfig($moduleId);
        $results = [];

        foreach ($searchConfig as $searchObjectName => $currentSearchConfig) {
            $tableResults = $this->getSearchResults($searchObjectName, $currentSearchConfig, $search, $ignoreLimits);
            if (!empty($tableResults)) {
                $matchTitle = $currentSearchConfig['matchTitle'];
                if (is_callable($matchTitle)) {
                    $matchTitle = $matchTitle();
                }
                $results[] = [
                    'matchTitle' => $matchTitle,
                    'table' => $tableResults,
                ];
            }
        }

        return $results;
    }

    /**
     * Get search results for specific object name and search string.
     *
     * @param string $searchObjectName classname of search object
     * @param mixed $searchConfig search configuration of search object
     * @param string $search
     * @param bool $ignoreLimits
     * @return array<int, array{model: \yii\db\ActiveRecord, matchText: string, match: string, route: array<string, mixed>}>
     */
    private function getSearchResults(string $searchObjectName, $searchConfig, string $search, bool $ignoreLimits)
    {
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
            $searchResult = $searchObject->search([$searchObjectClassname => [$column => $search]]);

            // Disable pagination and set maximum limit based on config or default pagination size
            if ($ignoreLimits) {
                $searchResult->pagination = false;
            } else {
                $limit = $searchResult->pagination->pageSize;
                $searchResult->pagination = false;
                if (array_key_exists('limit', $searchConfig)) {
                    $limit = $searchConfig['limit'];
                }
                $searchResult->query->limit($limit);
            }

            // Group by if specified
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

                    $subquery = clone $searchResult->query;
                    $primary_key = $subquery->modelClass::primaryKey()[0];
                    $subquery->select("MAX($primary_key) AS 'search_max_primary_key'")->groupBy($group_by_over);
                    $searchResult->query->rightJoin(['search_grouped' => $subquery], "$primary_key = search_grouped.search_max_primary_key");
                }
            }

            // Add results to array and process route
            foreach ($searchResult->getModels() as $model) {
                if (array_key_exists('only_if', $searchConfig)) {
                    if (!$searchConfig['only_if']($model)) {
                        continue;
                    }
                }

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

                $results[] = [
                    'model' => $model,
                    'matchText' => $searchConfig['matchText']($model),
                    'match' => $column,
                    'route' => $route,
                ];
            }
        }
        return $results;
    }
}
