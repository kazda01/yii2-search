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
    public $buttonContent = '<i class="fa fa-search"></i>';
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
