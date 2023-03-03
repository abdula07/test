<?php

namespace app\common\vendor;

use Yii;
use yii\console\Exception;
use yii\console\ExitCode;
use yii\gii\generators\model\Generator;
use yii\helpers\Console;

class MigrateController extends \yii\console\controllers\MigrateController
{

    /**
     * @param int $limit
     * @return int
     */
    public function actionUp($limit = 0)
    {
        $migrations = $this->getNewMigrations();
        if (empty($migrations)) {
            $this->stdout("No new migrations found. Your system is up-to-date.\n", Console::FG_GREEN);
            $this->generateModels();
            return ExitCode::OK;
        }

        $total = count($migrations);
        $limit = (int)$limit;
        if ($limit > 0) {
            $migrations = array_slice($migrations, 0, $limit);
        }

        $n = count($migrations);
        if ($n === $total) {
            $this->stdout("Total $n new " . ($n === 1 ? 'migration' : 'migrations') . " to be applied:\n",
                Console::FG_YELLOW);
        } else {
            $this->stdout("Total $n out of $total new " . ($total === 1 ? 'migration' : 'migrations') . " to be applied:\n",
                Console::FG_YELLOW);
        }

        foreach ($migrations as $migration) {
            $nameLimit = $this->getMigrationNameLimit();
            if ($nameLimit !== null && strlen($migration) > $nameLimit) {
                $this->stdout("\nThe migration name '$migration' is too long. Its not possible to apply this migration.\n",
                    Console::FG_RED);
                return ExitCode::UNSPECIFIED_ERROR;
            }
            $this->stdout("\t$migration\n");
        }
        $this->stdout("\n");

        $applied = 0;
        foreach ($migrations as $migration) {
            if (!$this->migrateUp($migration)) {
                $this->stdout("\n$applied from $n " . ($applied === 1 ? 'migration was' : 'migrations were') . " applied.\n",
                    Console::FG_RED);
                $this->stdout("\nMigration failed. The rest of the migrations are canceled.\n", Console::FG_RED);

                return ExitCode::UNSPECIFIED_ERROR;
            }
            $applied++;
        }

        $this->stdout("\n$n " . ($n === 1 ? 'migration was' : 'migrations were') . " applied.\n", Console::FG_GREEN);
        $this->stdout("\nMigrated up successfully.\n", Console::FG_GREEN);
        $this->generateModels();
    }

    /**
     * @param int $limit
     * @return int
     * @throws Exception
     */
    public function actionDown($limit = 0)
    {
        if ($limit === 'all') {
            $limit = null;
        } else {
            $limit = (int)$limit;
            if ($limit < 1) {
                throw new Exception('The step argument must be greater than 0.');
            }
        }

        $migrations = $this->getMigrationHistory($limit);

        if (empty($migrations)) {
            $this->stdout("No migration has been done before.\n", Console::FG_YELLOW);

            return ExitCode::OK;
        }

        $migrations = array_keys($migrations);

        $n = count($migrations);
        $this->stdout("Total $n " . ($n === 1 ? 'migration' : 'migrations') . " to be reverted:\n", Console::FG_YELLOW);
        foreach ($migrations as $migration) {
            $this->stdout("\t$migration\n");
        }
        $this->stdout("\n");

        $reverted = 0;
        foreach ($migrations as $migration) {
            if (!$this->migrateDown($migration)) {
                $this->stdout("\n$reverted from $n " . ($reverted === 1 ? 'migration was' : 'migrations were') . " reverted.\n",
                    Console::FG_RED);
                $this->stdout("\nMigration failed. The rest of the migrations are canceled.\n", Console::FG_RED);

                return ExitCode::UNSPECIFIED_ERROR;
            }
            $reverted++;
        }
        $this->stdout("\n$n " . ($n === 1 ? 'migration was' : 'migrations were') . " reverted.\n", Console::FG_GREEN);
        $this->stdout("\nMigrated down successfully.\n", Console::FG_GREEN);
        $this->generateModels();
    }

    private function generateModels()
    {
        $this->stdout("\nStarting to regenerate gii models.\n\n", Console::FG_BLUE);
        $tables = $this->db->createCommand("SELECT
            table_name
FROM
    information_schema.tables
WHERE
        table_type = 'BASE TABLE' AND table_name != 'migration'
  AND
        table_schema NOT IN ('pg_catalog', 'information_schema');")->queryAll();
        if (!empty($tables)) {
            foreach ($tables as $table) {
                $table = array_values($table)[0];
                $this->generateModel($table);
            }
        }
        $this->stdout("\nFiles were generated successfully!\n", Console::FG_GREEN);
    }

    private function generateModel($table)
    {
        /** @var Generator $generator */
        $generator = Yii::createObject(['class' => 'yii\\gii\\generators\\model\\Generator']);
        $generator->tableName = $table;
        //$generator->modelClass = 'Users';
        $generator->ns = 'app\\models\\gii';
        $generator->baseClass = 'app\\common\\vendor\\ActiveRecord';
        $files = $generator->generate();
        $n = count($files);
        if ($n === 0) {
            echo "No code to be generated.\n";
            return;
        }
        foreach ($files as $file) {
            $path = $file->getRelativePath();
            if (is_file($file->path)) {
                if (file_get_contents($file->path) === $file->content) {
                    echo '  ' . $this->ansiFormat('[unchanged]', Console::FG_GREY);
                    echo $this->ansiFormat(" $path\n", Console::FG_CYAN);
                } else {
                    echo '  ' . $this->ansiFormat('[changed]', Console::FG_YELLOW);
                    echo $this->ansiFormat(" $path\n", Console::FG_CYAN);
                }
            } else {
                echo '        ' . $this->ansiFormat('[new]', Console::FG_GREEN);
                echo $this->ansiFormat(" $path\n", Console::FG_CYAN);
            }
            $answers[$file->id] = true;
        }
        $generator->save($files, (array)$answers, $results);
    }


}
