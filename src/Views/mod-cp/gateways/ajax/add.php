<?php

use \Joonika\Modules\Users\Address;
use \Joonika\Modules\Users\Company;
use \Joonika\Modules\Users\Users;
$ACL=\Joonika\ACL::ACL();
global $data;
if (!$ACL->hasPermission('jgate_gateways')) {
    error403();
    die;
}
$database=\Joonika\Database::connect();
$continue = true;
$operator = $ACL->hasPermission('jgate_gateways');
echo \Joonika\Forms::form_create([
    'id' => "addModalForm"
]);
$userID = isset($_POST['userID']) ? $_POST['userID'] : JK_LOGINID();
if (isset($_POST['submit'])) {
    if ($_POST['id'] != "") {
        $database->update('jgate_gateways', [
            "title" => $_POST['title'],
            "mainAddress" => $_POST['mainAddress'],
            "coreType" => $_POST['coreType'],
            "slugControl" => $_POST['slugControl'],
        ],[
            "id"=>$_POST['id']
        ]);
        if(!empty($_POST['apiKey'])){
            $database->update('jgate_gateways', [
                "apiKey" => $_POST['apiKey'],
            ],[
                "id"=>$_POST['id']
            ]);
        }
    } else {
        $database->insert('jgate_gateways', [
            "createdBy" => JK_LOGINID(),
            "createdOn" => date("Y-m-d H:i:s"),
            "title" => $_POST['title'],
            "slugControl" => $_POST['slugControl'],
            "mainAddress" => $_POST['mainAddress'],
            "apiKey" => $_POST['apiKey'],
            "coreType" => $_POST['coreType'],
        ]);
    }

    echo redirect_to_js();
}
if ($_POST['id'] != "") {
    $data = $database->get('jgate_gateways', '*', [
        "id" => $_POST['id']
    ]);
}
echo div_container_row();


echo \Joonika\Forms::field_hidden([
    "name" => "id",
    "value" => $_POST['id'],
]);

echo div_start("col-md-6");
echo \Joonika\Forms::field_select([
    "name" => "coreType",
    "title" => __("core type"),
    "first" => false,
    "array" => [
        "Joonika" => "Joonika"
    ],
    "required" => true
]);
echo div_close();
echo div_start('w-100', '', true);

echo div_start("col-md-4");
echo \Joonika\Forms::field_text([
    "name" => "title",
    "title" => __("title"),
    "required" => true,
]);
echo div_close();
echo div_start("col-md-4");
echo \Joonika\Forms::field_text([
    "name" => "slugControl",
    "direction" => "ltr",
    "title" => __("slug"),
    "required" => true,
]);
echo div_close();


echo div_start("col-md-4");
$data['apiKey']="";
echo \Joonika\Forms::field_text([
    "name" => "apiKey",
    "title" => __("api key").(!empty($_POST['id'])?(' : '.__("if changed")):''),
    "direction" => "ltr",
]);
echo div_close();

echo div_start("col-12");
echo \Joonika\Forms::field_text([
    "name" => "mainAddress",
    "title" => __("main address"),
    "direction" => "ltr",
]);
echo div_close();
echo div_container_row_close();
echo \Joonika\Forms::field_submit(
    [
        "btn-class" => "btn btn-primary btn-lg btn-block",
        "icon" => "fal fa-save"
    ]
);
echo \Joonika\Forms::form_end();
echo hr_html();

\Joonika\Controller\AstCtrl::ADD_FOOTER_SCRIPTS('<script>'
    . ajax_validate([
        "on" => "submit",
        "formID" => "addModalForm",
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/ajax/add',
        "success_response" => "#modal_global_body",
        "loading" => loading_fa()
    ])
    . '</script>'
);

do_scripts();
