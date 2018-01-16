<?php
namespace app\console\controllers;

/**
 * Class MigrateController
 * @package app\console\controllers
 */
class MigrateController extends \flexibuild\migrate\controllers\MigrateController
{
    /**
     * @inheritdoc
     */
    public $migrationPath = '@console/migrations';

}
