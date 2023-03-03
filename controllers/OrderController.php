<?php

namespace app\controllers;

use app\models\Order;
use app\models\search\OrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Order models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $countOrders = ["countOrdersInYear" => [], "countOrdersInMonth" => []];

        $searchModelForFilter = new OrderSearch();
        $dataProviderForFilter = $searchModelForFilter->search([]);
        $dataProviderForFilter->pagination->pageSize = false;
        foreach ($dataProviderForFilter->models as $order) {
            $created_at_to_time = strtotime($order->created_at);
            $dateYear = date('Y', $created_at_to_time);
            $dateMonth = date('m', $created_at_to_time);
            if (!isset($countOrders["countOrdersInYear"][$dateYear])) {
                $countOrders["countOrdersInYear"][$dateYear] = 1;
            } else {
                $countOrders["countOrdersInYear"][$dateYear]++;
            }
            if (!isset($countOrders["countOrdersInMonth"][$dateYear][$dateMonth])) {
                $countOrders["countOrdersInMonth"][$dateYear][$dateMonth] = 1;
            } else {
                $countOrders["countOrdersInMonth"][$dateYear][$dateMonth]++;
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'countOrders' => $countOrders
        ]);
    }
}
