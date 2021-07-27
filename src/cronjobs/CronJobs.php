<?php


namespace Modules\jgate\cronjobs;


use Medoo\Medoo;
use Modules\cronjobs\src\CronJob;

class CronJobs extends \Joonika\CronJobs
{
    public function init()
    {
        if (JK_SERVER_TYPE == 'main') {
            CronJob::setCronFunction('jgate', 'removeExpired', '*/10 * * * *', __CLASS__);
        }
    }

    public function removeExpired()
    {
        $database = \Joonika\Database::connect();
        $database->update('gateway_cache', [
            "status" => 0
        ], [
            "expireDate[<=]" => now(),
        ]);
        return true;
    }
}