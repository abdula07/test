<?php

use app\models\Order;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\search\OrderSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $countOrders title */

$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;

$monthsName = [
    '01' => 'Январь',
    '02' => 'Февраль',
    '03' => 'Март',
    '04' => 'Апрель',
    '05' => 'Май',
    '06' => 'Июнь',
    '07' => 'Июль',
    '08' => 'Август',
    '09' => 'Сентябрь',
    '10' => 'Октябрь',
    '11' => 'Ноябрь',
    '12' => 'Декабрь'
];
?>
<div class="order-index">

    <div class="row">
        <div class="col-sm-2">
            <?php $form = ActiveForm::begin([
                'id'     => 'OrderSearchForm',
                'action' => ['index'],
                'method' => 'get'
            ]); ?>
            <ul>
                <?php
                foreach ($countOrders["countOrdersInYear"] as $year => $countYear) { ?>
                    <li click-event="Filter" name-filter="OrderSearch[yearFilter]" value="<?= $year ?>">
                        <?= $year ?> (<?= $countYear ?>)
                    </li>
                    <ul>
                        <?php foreach ($countOrders["countOrdersInMonth"][$year] as $month => $countMonth) { ?>
                            <li name-filter="OrderSearch[monthFilter]" click-event="Filter"
                                value="<?= $year ?>-<?= $month ?>"> <?= $monthsName[$month] ?>
                                (<?= $countMonth ?>)
                            </li>
                        <?php } ?>
                    </ul>
                <?php }
                ?>
            </ul>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-sm-10">
            <h1><?= Html::encode($this->title) ?></h1>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns'      => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => 'Сумма',
                        'value' => function (Order $model) {
                            return $model->sum;
                        }
                    ],
                    [
                        'label' => 'Дата',
                        'value' => function (Order $model) {
                            return $model->created_at;
                        }
                    ],
                ],
            ]); ?>
        </div>
    </div>


</div>

<script>
    $(document)
        .on('click', '[click-event="Filter"]', function () {

            let value = $(this).attr('value');
            let name = $(this).attr('name-filter');
            let form = $('#OrderSearchForm');
            $(this).append(`<input hidden="hidden" name="${name}" value="${value}">`)
            form.submit();
        })
</script>