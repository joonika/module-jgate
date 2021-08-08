<?php
$ACL = \Joonika\ACL::ACL();
$database = \Joonika\Database::connect();
if (!$ACL->hasPermission('jgate_gateways')) {
    error403();
    die;
}
$data = $database->get('jgate.jgate_gateways', ['id', 'lastToken'], [
    "id" => $_POST['id']
]);
if (empty($data['lastToken'])) {

    $test = \Modules\jgate\src\jgate::request('getToken', [], false, $data['id']);
    if(!empty($test['success'])){
        echo alertSuccess(json_encode($test['data']));
        if(!empty($test['data']['token'])){
            $database->update("jgate.jgate_gateways",[
                "lastToken"=>$test['data']['token'],
                "lastTokenDate"=>now(),
            ],[
                "id"=>$data['id']
            ]);
            echo redirect_to_js('',1000);
        }
        $database->update("jgate.jgate_gateways",[
            "lastCheckToken"=>json_encode($test,JSON_UNESCAPED_UNICODE),
        ],[
            "id"=>$data['id']
        ]);
    }else{
        echo alertWarning(\Modules\jgate\src\jgate::errorMessages($test));
    }
} else {
    $test = \Modules\jgate\src\jgate::request('checkToken', ["token" => $data['lastToken']], false, $data['id']);
    if(!empty($test['success'])){
        echo alertSuccess(json_encode($test['data']));
        echo redirect_to_js('',1000);
    }else{
        echo alertWarning(\Modules\jgate\src\jgate::errorMessages($test));
    }
    $database->update("jgate.jgate_gateways",[
        "lastCheckToken"=>json_encode($test,JSON_UNESCAPED_UNICODE),
    ],[
        "id"=>$data['id']
    ]);
}