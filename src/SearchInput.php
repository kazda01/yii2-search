<?php

namespace kazda01\search;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;

class SearchInput extends Widget
{
    public $search_id;
    public $placeholder = 'Search';
    public $formClass = '';
    public $buttonClass = 'btn btn-dark search-button';
    public $buttonContent = '<svg class="search-icon" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="search" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path></svg>';
    public $inputClass = 'form-control p-1 no-outline';
    public $wrapperClass = '';
    public $widgetClass = 'mx-auto shadow p-2 position-absolute bg-white';

    public function init()
    {
        Yii::setAlias('@kazda01Search', __DIR__);

        if ($this->search_id == null) {
            throw new InvalidConfigException("Parameter 'search_id' is required.");
        }

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

        if (Yii::$app->getModule($this->search_id) == null) {
            throw new InvalidConfigException("Configuration for search '$this->search_id' is not defined in web.php config (config[modules][$this->search_id]).");
        }

        return $this->render('searchInput', [
            'search_id' => $this->search_id,
            'placeholder' => $this->placeholder,
            'formClass' => $this->formClass,
            'buttonClass' => $this->buttonClass,
            'buttonContent' => $this->buttonContent,
            'inputClass' => $this->inputClass,
            'wrapperClass' => $this->wrapperClass,
            'widgetClass' => $this->widgetClass,
        ]);
    }
}
