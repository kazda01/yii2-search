<?php

namespace kazda01\search\models;

use yii\base\Model;

class SearchResult extends Model
{
    /**
     * @var Model $model Model that was found
     */
    public $model;

    /**
     * @var string $match Matched string with HTML highlighted matching part
     */
    public string $matchText;

    /**
     * @var string $match Matched attribute name
     */
    public string $match;

    /**
     * @var array<mixed>|string $route Route to redirect after click
     */
    public $route;

    /**
     * @var string $matchTitle Title for current match group (e.g. 'Users')
     */
    public string $matchTitle;
}
