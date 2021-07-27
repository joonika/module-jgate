<?php
if (isset($_POST['gatewayId'], $_POST['agent'], $_POST['service'])) {
    $removeRel = \Modules\jgate\src\jgate::request('manage/agentService', [
        "action" => "remove",
        "agent" => $_POST['agent'],
        "service" => $_POST['service'],
    ], false, $_POST['gatewayId']);
    if (!empty($removeRel['success'])) {
            echo alertInfo(__("relation removed successfully"));
            echo redirect_to_js('',500);
    } else {
        echo alertDanger(\Modules\jgate\src\jgate::errorMessages($removeRel));
    }
}