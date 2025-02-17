<?php

namespace kazda01\search\controllers;

use kazda01\search\widgets\SearchResultWidget;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * @author Antonín Kazda (kazda01)
 * @author Radim Mifka (mifka01)
 *
 * SearchController implements model searching.
 * @property \kazda01\search\SearchModule $module
 */
class SearchController extends Controller
{
    /**
     * @inheritDoc
     * @return array<mixed>
     */
    public function behaviors(): array
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => $this->module->allowGet ? ['POST', 'GET'] : ['POST'],
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
     * Creates data provider instance with search query applied
     *
     * @return string
     */
    public function actionIndex(): string
    {
        if (Yii::$app->request->isPost) {
            $search = Yii::$app->request->post('search');
        } else {
            $search = Yii::$app->request->get('search');
        }

        if (empty($search)) {
            return '';
        }

        return SearchResultWidget::widget([
            'searchId' => $this->module->id,
            'search' => $search,
        ]);
    }
}
