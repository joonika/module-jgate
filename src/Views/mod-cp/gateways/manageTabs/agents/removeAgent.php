<?php
if (isset($_POST['gatewayId'], $_POST['name'])) {
    $agents = \Modules\jgate\src\jgate::request('manage/agent', [
        "action" => "remove",
        "name" => $_POST['name']
    ], false, $_POST['gatewayId']);
    if (!empty($agents['success'])) {
            echo alertInfo(__("agent removed successfully"));
            echo redirect_to_js();
    } else {
        echo alertDanger(\Modules\jgate\src\jgate::errorMessages($agents));
    }
}