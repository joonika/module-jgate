<?php
if (isset( $_POST['gatewayId'])) {
    if (isset($_POST['submit'])) {
        $agents = \Modules\jgate\src\jgate::request('manage/agent', [
            "action"=>"add",
            "name"=>$_POST['name']
        ], false, $_POST['gatewayId']);
        if(!empty($agents['success'])){
                echo alertSuccess(__("agent added successfully"));
                echo redirect_to_js();
        }else{
            echo alertDanger(\Modules\jgate\src\jgate::errorMessages($agents));
        }
    }

    echo \Joonika\Forms::form_create(["id" => "addFormAgent"]);

    echo \Joonika\Forms::field_hidden([
        "name" => "gatewayId",
        "value" => $_POST['gatewayId'],
    ]);
    echo div_container_row();


    echo div_start('col-md-4');
    echo \Joonika\Forms::field_text([
        "name" => "name",
        "title" => __("agent name"),
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
        "formID" => "addFormAgent",
        "url" => JK_DOMAIN_LANG() . "cp/jgate/gateways/manageTabs/agents/addAgent",
        "success_response" => "#modal_global_body",
        "loading" => ['iclass-size' => 1, 'elem' => 'span']
    ]) . '
</script>');

do_scripts();