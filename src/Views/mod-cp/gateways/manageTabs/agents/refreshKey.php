<?php
if (isset($_POST['gatewayId'], $_POST['name'])) {
    $agents = \Modules\jgate\src\jgate::request('manage/agent', [
        "action" => "refreshApikey",
        "name" => $_POST['name']
    ], false, $_POST['gatewayId']);
    if (!empty($agents['success'])) {
            echo alertInfo(__("api key refreshed successfully"));
            echo redirect_to_js('',500);
    } else {
        echo alertDanger(\Modules\jgate\src\jgate::errorMessages($agents));
    }
}