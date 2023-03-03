<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Order;

/**
 * OrderSearch represents the model behind the search form of `app\models\Order`.
 */
class OrderSearch extends Order
{
    public string $yearFilter = '';
    public string $monthFilter = '';
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sum'], 'integer'],
            [['created_at', 'yearFilter', 'monthFilter'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Order::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'sum' => $this->sum,
            'created_at' => $this->created_at,
        ]);

        if ($this->monthFilter) {
            $lastDay = date("Y-m-t", strtotime($this->monthFilter));
            $date = date("Y-m-d H:i:s", strtotime("{$this->monthFilter}-01 00:00:00"));
            $query->andWhere("created_at >= '{$date}'");
            $date = date("Y-m-d H:i:s", strtotime("{$lastDay} 23:59:59"));
            $query->andWhere("created_at <= '{$date}'");
        }

        if ($this->yearFilter) {
            $date = date("Y-m-d H:i:s", strtotime("{$this->yearFilter}-01-01 00:00:00"));
            $query->andWhere("created_at >= '{$date}'");
            $date = date("Y-m-d H:i:s", strtotime("{$this->yearFilter}-12-31 23:59:59"));
            $query->andWhere("created_at <= '{$date}'");
        }
        return $dataProvider;
    }
}
