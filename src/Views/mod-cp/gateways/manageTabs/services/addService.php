<?php
global $data;
if (isset($_POST['gatewayId'])) {
    $servers = \Modules\jgate\src\jgate::request('manage/serversList', [
    ], false, $_POST['gatewayId']);
    $serversList=[];
    if(!empty($servers['data'])){
        foreach ($servers['data'] as $serverList){
            $serversList[$serverList['id']]=$serverList['id'].'-'.$serverList['name'];
        }
    }else{
        echo alertDanger(__("not found any server"));

    }

    if (isset($_POST['submit'])) {
        if (empty($_POST['serviceId'])) {
            $agents = \Modules\jgate\src\jgate::request('manage/service', [
                "action" => "add",
                "method" => $_POST['method'],
                "serverId" => $_POST['serverId'],
                "type" => $_POST['type'],
            ], false, $_POST['gatewayId']);
        } else {
            $agents = \Modules\jgate\src\jgate::request('manage/service', [
                "action" => "edit",
                "serviceId" => $_POST['serviceId'],
                "method" => $_POST['method'],
                "serverId" => $_POST['serverId'],
                "type" => $_POST['type'],
            ], false, $_POST['gatewayId']);
        }
        if ($agents['success']) {
            echo alertSuccess(__("agent added/updated successfully"));
            echo redirect_to_js();
        } else {
            echo \Modules\jgate\src\jgate::errorMessages($agents);
        }

    }
    if (!empty($_POST['serviceId'])) {
        $service = \Modules\jgate\src\jgate::request('manage/service', [
            "action" => "info",
            "serviceId" => $_POST['serviceId'],
        ], false, $_POST['gatewayId']);
        if ($service['success']) {
            $data = $service['data'];
        }
    }

    echo \Joonika\Forms::form_create(["id" => "addFormAgent"]);

    echo \Joonika\Forms::field_hidden([
        "name" => "gatewayId",
        "value" => $_POST['gatewayId'],
    ]);
    echo \Joonika\Forms::field_hidden([
        "name" => "serviceId",
        "value" => !empty($_POST['serviceId']) ? $_POST['serviceId'] : '',
    ]);
    echo div_container_row();

    echo div_start('col-md-4');
    echo \Joonika\Forms::field_text([
        "name" => "method",
        "title" => __("service method"),
        "direction" => "ltr",
    ]);
    echo div_close();
    echo div_start('col-md-4');
    echo \Joonika\Forms::field_select([
        "name" => "serverId",
        "direction" => "ltr",
        "title" => __("server"),
        "array" => $serversList,
    ]);
    echo div_close();
    echo div_start('col-md-4');
    echo \Joonika\Forms::field_select([
        "name" => "type",
        "title" => __("type"),
        "array" => [
            "json" => "json",
            "html" => "html",
        ],
    ]);
    echo div_close();
    echo div_container_row_close();

    echo \Joonika\Forms::field_submit([
        "text" => __("save"),
        "ColType" => "12,12",
        "btn-class" => "btn btn-primary",
        "icon" => "fal fa-save"
    ]);
    echo \Joonika\Forms::form_end();
}

?>
<?php
\Joonika\Controller\AstCtrl::FOOTER_SCRIPTS('<script>
    ' . ajax_validate([
        "on" => "submit",
        "formID" => "addFormAgent",
        "url" => JK_DOMAIN_LANG() . "cp/jgate/gateways/manageTabs/services/addService",
        "success_response" => "#modal_global_body",
        "loading" => ['iclass-size' => 1, 'elem' => 'span']
    ]) . '
</script>');

do_scripts();