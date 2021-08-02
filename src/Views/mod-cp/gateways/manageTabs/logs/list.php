<?php

$getopt = datatable_get_opt();
if ($getopt) {
    $database = \Joonika\Database::connect();
    $countAll = 25;
    $lists = [];
    $serviceId = !empty($_GET['serviceId']) ? $_GET['serviceId'] : null;
    $gatewayId = !empty($_GET['gatewayId']) ? $_GET['gatewayId'] : null;
    $agent = !empty($_GET['agent']) ? $_GET['agent'] : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    $page = intval(($getopt['start'] / $getopt['length'])) + 1;
    $logs = \Modules\jgate\src\jgate::request('manage/logs', [
        "serviceId" => $serviceId,
        "agent" => $agent,
        "status" => $status,
        "page" => $page,
        "limit" => $getopt['length'],
    ], false, $gatewayId);
    $data = [];
    try {
        $countAll = !empty($logs['data']['recordsCount']) ? $logs['data']['recordsCount'] : 0;
        $rows = !empty($logs['data']['result']) ? $logs['data']['result'] : [];
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $service='{'.$row['serviceId'].'}'.$row['serviceName'].'/'.$row['serviceMethod'];
                if(empty($row['serviceId'])){
                    if(empty($row['serviceMethod'])){

                    }
                    $service='-';
                }
                array_push($data, [
                    "id" => $row['id'],
                    "service" => $service,
                    "agent" => $row['agent'],
                    "execTime" => $row['execTime'],
                    "logTime" => $row['logTime'],
                    "status" => $row['status'],
                    "details" => '<button class="btn btn-outline-info btn-xs" onclick=\'viewTextLog(' . $row['id'] . ')\' ><i class="fal fa-eye"></i></button>',
                ]);
            }
        }
    } catch (Exception $exception) {
        $countAll = 1;
        array_push($data, [
            "id" => \Joonika\Errors::exceptionString($exception),
            "service" => print_r($logs,true),
        ]);
    }

    echo datatable_view([
        "CountAll" => $countAll,
        "list" => $lists,
        "data" => $data,
    ]);

}