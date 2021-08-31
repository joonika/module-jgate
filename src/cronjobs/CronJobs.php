<?php


namespace Modules\jgate\cronjobs;


use Medoo\Medoo;

class CronJobs extends \Joonika\CronJobs
{
    public function init()
    {
        if (JK_SERVER_TYPE == 'main') {
            self::setCronFunction('jgate', 'removeExpired', '*/10 * * * *', __CLASS__);
        }
    }

    public function removeExpired()
    {
        $database = \Joonika\Database::connect();
        $database->update('jgate.jgate_cache', [
            "status" => 0
        ], [
            "expireDate[<=]" => now(),
        ]);
        return true;
    }
}