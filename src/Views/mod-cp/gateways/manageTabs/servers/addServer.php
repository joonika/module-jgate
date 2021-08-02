<?php
global $data;
if (isset($_POST['gatewayId'])) {
    if (isset($_POST['submit'])) {
        if (empty($_POST['serverId'])) {
            $servers = \Modules\jgate\src\jgate::request('manage/server', [
                "action" => "add",
                "name" => $_POST['name'],
                "address" => $_POST['address'],
                "addressTest" => $_POST['addressTest'],
            ], false, $_POST['gatewayId']);
        } else {
            $servers = \Modules\jgate\src\jgate::request('manage/server', [
                "action" => "edit",
                "serverId" => $_POST['serverId'],
                "name" => $_POST['name'],
                "address" => $_POST['address'],
                "addressTest" => $_POST['addressTest'],
            ], false, $_POST['gatewayId']);
        }
        if ($servers['success']) {
            echo alertSuccess(__("server added/updated successfully"));
            echo redirect_to_js();
        } else {
            echo \Modules\jgate\src\jgate::errorMessages($servers);
        }

    }
    if (!empty($_POST['serverId'])) {
        $server = \Modules\jgate\src\jgate::request('manage/server', [
            "action" => "info",
            "serverId" => $_POST['serverId'],
        ], false, $_POST['gatewayId']);
        if ($server['success']) {
            $data = $server['data'];
        }
    }

    echo \Joonika\Forms::form_create(["id" => "addFormServer"]);

    echo \Joonika\Forms::field_hidden([
        "name" => "gatewayId",
        "value" => $_POST['gatewayId'],
    ]);
    echo \Joonika\Forms::field_hidden([
        "name" => "serverId",
        "value" => !empty($_POST['serverId']) ? $_POST['serverId'] : '',
    ]);
    echo div_container_row();


    echo div_start('col-md-4');
    echo \Joonika\Forms::field_text([
        "name" => "name",
        "title" => __("server name"),
        "direction" => "ltr",
    ]);
    echo div_close();

    echo div_start('col-md-12');
    echo \Joonika\Forms::field_text([
        "name" => "address",
        "title" => __("address"),
        "direction" => "ltr",
    ]);
    echo div_close();

    echo div_start('col-md-12');
    echo \Joonika\Forms::field_text([
        "name" => "addressTest",
        "title" => __("test address"),
        "direction" => "ltr",
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
        "formID" => "addFormServer",
        "url" => JK_DOMAIN_LANG() . "cp/jgate/gateways/manageTabs/servers/addServer",
        "success_response" => "#modal_global_body",
        "loading" => ['iclass-size' => 1, 'elem' => 'span']
    ]) . '
</script>');

do_scripts();