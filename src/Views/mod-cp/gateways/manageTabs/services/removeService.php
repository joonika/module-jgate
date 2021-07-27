<?php

if (isset($_POST['gatewayId'], $_POST['serviceId'])) {
    $agents = \Modules\jgate\src\jgate::request('manage/service', [
        "action" => "remove",
        "serviceId" => $_POST['serviceId']
    ], false, $_POST['gatewayId']);
        if ($agents['success']) {
            echo alertInfo(__("agent removed successfully"));
            echo redirect_to_js();
        } else {
            echo \Modules\jgate\src\jgate::errorMessages($agents);
        }
}