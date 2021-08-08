<?php

use \Joonika\Modules\Users\Address;
use \Joonika\Modules\Users\Company;
use \Joonika\Modules\Users\Users;

$ACL = \Joonika\ACL::ACL();
global $data;
if (!$ACL->hasPermission('jgate_gateways')) {
    error403();
    die;
}
$database = \Joonika\Database::connect();
$continue = true;
$operator = $ACL->hasPermission('jgate_gateways');
echo \Joonika\Forms::form_create([
    'id' => "addModalForm"
]);
$userID = isset($_POST['userID']) ? $_POST['userID'] : JK_LOGINID();
if (isset($_POST['submit'])) {
    $database->insert('jgate.jgate_services_rel', [
        "sId" => $_POST['sId'],
        "gId" => $_POST['gId'],
        "address" => $_POST['serviceAddress'],
    ]);
    echo redirect_to_js();
    die;
}
echo div_container_row();


echo \Joonika\Forms::field_hidden([
    "name" => "sId",
    "value" => $_POST['sId'],
]);

echo div_start("col-md-4");
$gateways = $database->select("jgate.jgate_gateways", [
    "id", "title"
]);
$gatewaysArray = [];
if (!empty($gateways)) {
    foreach ($gateways as $gateway) {
        $gatewaysArray[$gateway['id']] = $gateway['title'];
    }
}
echo \Joonika\Forms::field_select([
    "name" => "gId",
    "title" => __("gateway"),
    "first" => true,
    "onchange" => 'gIdChange()',
    "array" => $gatewaysArray,
    "required" => true
]);
echo div_close();

echo div_start("col-md-8",'gIdChangeBody');
echo \Joonika\Forms::field_select([
    "name" => "serviceAddress",
    "array" => [],
    "required" => true,
    "first" => true,
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
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/services/ajax/add_sId',
        "success_response" => "#modal_global_body",
        "loading" => loading_fa()
    ])
    . '</script>'
);

do_scripts();
