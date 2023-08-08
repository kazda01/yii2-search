<?php

/** @var yii\web\View $this */

?>

<form autocomplete="off" onsubmit="return false" class="<?= $formClass ?>">
    <div class="<?= $wrapperClass ?>">
        <div class="input-group">
            <button class="search-button <?= $buttonClass ?>" aria-label="<?= Yii::t('app', 'Search') ?>"><?= $buttonContent ?></button>
            <input id="<?= 'search-input-' . $search_id ?>" data-search-id="<?= $search_id ?>" tabindex="0" placeholder="<?= $placeholder ?>" value="" type="text" class="search-focus search-input <?= $inputClass ?>">
            <div id="<?= 'search-widget-' . $search_id ?>" class="search-widget <?= $widgetClass ?>">
            </div>
        </div>
    </div>
</form>