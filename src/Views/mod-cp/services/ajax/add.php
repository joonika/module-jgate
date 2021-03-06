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
        $database->update('jgate.jgate_services', [
            "type" => $_POST['type'],
            "slug" => $_POST['slug'],
            "title" => $_POST['title'],
        ],[
            "id"=>$_POST['id']
        ]);
    } else {
        $database->insert('jgate.jgate_services', [
            "type" => $_POST['type'],
            "slug" => $_POST['slug'],
            "title" => $_POST['title'],
        ]);
    }

    echo redirect_to_js();
}
if ($_POST['id'] != "") {
    $data = $database->get('jgate.jgate_services', '*', [
        "id" => $_POST['id']
    ]);
}
echo div_container_row();


echo \Joonika\Forms::field_hidden([
    "name" => "id",
    "value" => $_POST['id'],
]);

echo div_start("col-md-4");
echo \Joonika\Forms::field_select([
    "name" => "type",
    "title" => __("type"),
    "first" => false,
    "array" => [
        "json" => "json",
        "file" => "file"
    ],
    "required" => true
]);
echo div_close();

echo div_start("col-md-4");
echo \Joonika\Forms::field_text([
    "name" => "title",
    "title" => __("title"),
    "required" => true,
]);
echo div_close();
echo div_start("col-md-4");
echo \Joonika\Forms::field_text([
    "name" => "slug",
    "direction" => "ltr",
    "title" => __("slug"),
    "required" => true,
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
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/services/ajax/add',
        "success_response" => "#modal_global_body",
        "loading" => loading_fa()
    ])
    . '</script>'
);

do_scripts();
