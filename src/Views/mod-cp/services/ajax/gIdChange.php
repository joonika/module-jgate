<?php
$servicesArray = [];
$database = \Joonika\Database::connect();
if (!empty($_POST['gId'])) {
    $gatewayServices = $database->get("jgate.jgate_gateways", 'servicesJson', [
        "id" => $_POST['gId']
    ]);
    if (empty($gatewayServices)) {
        echo alertWarning(__("not found any service update, please update first"));
        exit();
    }
    $isJson = is_json($gatewayServices, true, true);
    if (empty($isJson)) {
        echo alertWarning(__("not found any service update, please update first"));
        exit();
    }
    $servicesArray = [];
    if (!empty($isJson)) {
        foreach ($isJson as $ad) {
            $add = $ad['address'];
            $servicesArray[$add] = $add;
        }
    }
}
echo \Joonika\Forms::field_select([
    "name" => "serviceAddress",
    "array" => $servicesArray,
    "required" => true,
    "first" => true,
]);
do_scripts();
