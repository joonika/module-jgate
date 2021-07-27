<?php

use \Joonika\Modules\Users\Address;
use \Joonika\Modules\Users\Company;
use \Joonika\Modules\Users\Users;

$ACL = \Joonika\ACL::ACL();
global $data;
if (!$ACL->hasPermission('jgate_gateways')) {
    error403();
    die;
}
$database = \Joonika\Database::connect();

$gateways = $database->select("jgate_gateways", [
    "id" => [
        'id',
        'title',
        'slugControl',
        'status',
    ]
], [
    "status" => "active"
]);
if (empty($gateways)) {
    echo alertWarning(__("no gateway found"));
    die;
}
$gateways = $database->select("jgate_gateways", [
    "id" => [
        'id',
        'title',
        'slugControl',
        'status',
    ]
], [
    "status" => "active"
]);
if (empty($gateways)) {
    echo alertWarning(__("no gateway found"));
    die;
}
foreach ($gateways as $gateway) {
    $services = \Modules\jgate\src\jgate::request('manage/servicesList', ['rel'=>true], false, $gateway['id']);
    if ($services['status'] == 200 && !empty($services['status'])) {
        $servicesData = $services['data'];
        $dataInput = [];
        foreach ($servicesData as $service) {
            array_push($dataInput, [
                "address"=>$service['name'] . '/' . $service['method'],
                "tokenless"=> $service['tokenless'] ?? 0,
            ]);
        }
        $database->update('jgate_gateways', [
            "servicesJson" => json_encode($dataInput, 256)
        ], [
            "id" => $gateway['id']
        ]);
    }
}
echo redirect_to_js();