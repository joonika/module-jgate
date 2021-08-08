<?php
$ACL = \Joonika\ACL::ACL();
$database = \Joonika\Database::connect();
if (!$ACL->hasPermission('jgate_gateways')) {
    error403();
    die;
}
$data = $database->update('jgate.jgate_gateways', [
    "lastToken" => null,
    "lastTokenDate" => null,
    "lastCheckToken" => null,
], [
    "id" => $_POST['id']
]);
echo redirect_to_js();
