<?php

/**
 * @author Antonín Kazda (kazda01)
 *
 * @var yii\web\View $this
 * @var string $search_id
 * @var string $placeholder
 * @var string $formClass
 * @var string $buttonClass
 * @var string $buttonContent
 * @var string $inputClass
 * @var string $wrapperClass
 * @var string $widgetClass
 * @var string|null $searchUrl
 */
?>

<form autocomplete="off" onsubmit="return false" class="<?= $formClass ?>">
    <div class="<?= $wrapperClass ?>">
        <div class="input-group">
            <button class="search-button <?= $buttonClass ?>" aria-label="<?= Yii::t('app', 'Search') ?>"><?= $buttonContent ?></button>
            <input id="<?= 'search-input-' . $search_id ?>" data-url="<?= $searchUrl ?? '' ?>" data-search-id="<?= $search_id ?>" tabindex="0" placeholder="<?= $placeholder ?>" value="" type="text" class="search-focus search-input <?= $inputClass ?>">
            <div id="<?= 'search-widget-' . $search_id ?>" class="search-widget <?= $widgetClass ?>"></div>
        </div>
    </div>
</form>