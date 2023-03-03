<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MinCountCoinsController extends Controller
{

    public function actionIndex($number)
    {
        $coins = [10, 5, 1];
        $countCoins = [];
        while ($number != 0) {
            foreach ($coins as $coin) {
                if ($coin > $number) {
                    continue;
                }
                $number -= $coin;
                if (!isset($countCoins[$coin])) {
                    $countCoins[$coin] = 1;

                } else {
                    $countCoins[$coin]++;
                }
                break;
            }
        }
        print_r($countCoins);
    }
}
