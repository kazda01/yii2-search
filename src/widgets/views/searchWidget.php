<?php

/**
 * @author Antonín Kazda (kazda01)
 *
 * @var yii\web\View $this
 * @var string $search
 * @var kazda01\search\widgets\SearchResultWidget $widget
 * @var \yii\data\ArrayDataProvider $searchResultDataProvider
 * @var \yii\data\Pagination|null $pagination
 */

use yii\helpers\Url;
use yii\widgets\Pjax;

$groupedSearchResults = [];
foreach ($searchResultDataProvider->getModels() as $searchResult) {
    $groupedSearchResults[$searchResult->matchTitle][] = $searchResult;
}

if ($searchResultDataProvider->getTotalCount() === 0): ?>
    <p class='text-tertiary m-2 text-center'><?= Yii::t('app', 'No results') ?></p>
<?php else: ?>
    <?php Pjax::begin([
        'id' => 'search-results',
        'enablePushState' => false,
        'scrollTo' => true,
    ]) ?>

    <?php foreach ($groupedSearchResults as $matchTitle => $searchResults): ?>
        <div class="container-fluid border-bottom pb-2 mb-2">
            <div class="row g-0">
                <div class="flex-column d-flex justify-content-center h-100">
                    <span class="h6 mb-0 text-start"><?= $matchTitle ?></span>
                    <?php foreach ($searchResults as $searchResult): ?>
                        <div class="search-result position-relative p-1 ms-2 <?= $widget->searchResultClass ?>">
                            <a class="stretched-link search-focus" href="<?= Url::toRoute($searchResult->route) ?>"></a>
                            <div class="d-flex"><?= $searchResult->matchText ?></div>
                            <div class="match text-tertiary d-flex small">
                                <?php if ($widget->showMatchAttribute): ?>
                                    <span class="ms-2"><?= $searchResult->model->getAttributeLabel($searchResult->match) ?></span>
                                <?php endif; ?>
                                <div class="match-text ms-3">
                                    <?= $widget->createMatchText($searchResult->model->getAttribute($searchResult->match)) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if ($widget->paginate && $pagination): ?>
        <?= \yii\widgets\LinkPager::widget([
            'pagination' => $pagination,
            'options' => ['class' => 'pagination justify-content-center'],
            'linkOptions' => ['class' => 'page-link'],
            'maxButtonCount' => 3,
            'firstPageLabel' => false,
            'prevPageLabel' => '<i class="fas fa-chevron-left"></i> ' . Yii::t('app', 'Previous'),
            'nextPageLabel' => Yii::t('app', 'Next') . ' <i class="fas fa-chevron-right"></i>',
            'lastPageLabel' => false,
        ]) ?>
    <?php endif; ?>

    <?php Pjax::end() ?>

<?php endif; ?>