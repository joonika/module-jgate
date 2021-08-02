<?php
if (isset($_POST['gatewayId'], $_POST['serverId'])) {
    $agents = \Modules\jgate\src\jgate::request('manage/server', [
        "action" => "reStatus",
        "serverId" => $_POST['serverId'],
    ], false, $_POST['gatewayId']);
    if ($agents['success']) {
        echo alertInfo(__("api key refreshed successfully"));
        echo redirect_to_js();
    } else {
        echo \Modules\jgate\src\jgate::errorMessages($agents);
    }
}