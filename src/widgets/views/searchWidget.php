<?php

/**
 * @author Antonín Kazda (kazda01)
 *
 * @var yii\web\View $this
 * @var string $search
 * @var kazda01\search\widgets\SearchResultWidget $widget
 * @var array<int, array{matchTitle: string, table: array<int, array{model: \yii\db\ActiveRecord, matchText: string, match: string, route: array<string, mixed>}>}> $results
 */

use yii\helpers\Url;

if (empty($results)) : ?>
    <p class='text-muted m-2 text-center'><?= Yii::t('app', 'No results') ?></p>
<?php endif; ?>

<?php foreach ($results as $table) : ?>
    <div class="container-fluid border-bottom pb-2 mb-2">
        <div class="row g-0">
            <div class="flex-column d-flex justify-content-center h-100">
                <span class="h6 mb-0 text-start"><?= $table['matchTitle'] ?></span>
                <?php foreach ($table['table'] as $index => $match) : ?>
                    <div class="search-result position-relative p-1 ms-2 <?= $widget->searchResultClass ?>">
                        <a class="stretched-link search-focus" href="<?= Url::toRoute($match['route']) ?>"></a>
                        <div class="d-flex"><?= $match['matchText'] ?></div>
                        <div class="match text-muted d-flex small">
                            <?php if ($widget->showMatchAttribute) : ?>
                                <span class="ms-2"><?= $match['model']->getAttributeLabel($match['match']) ?></span>
                            <?php endif; ?>
                            <div class="match-text ms-3">
                                <?= $widget->createMatchText($match['model']->getAttribute($match['match'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>