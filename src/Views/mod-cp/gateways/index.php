<?php
if (!\Joonika\ACL::ACL()->hasPermissionLogin('jgate_gateways')) {
    error403();
    die;
}
global $data;
$database = \Joonika\Database::connect();
get_head($this);

?>
    <div class="text-center my-2">
        <button type="button" class="btn btn-xs btn-outline-info" onclick="addGateway()">
            <i class="fal fa-plus"></i>
            <?= __("add gateway") ?>
        </button>
    </div>

<?php
echo div_container_row();

$gateways = $database->select('jgate_gateways', '*', [
    "status[!]" => "removed",
    "ORDER" => "sort"
]);
if (!empty($gateways)) {
    foreach ($gateways as $gateway) {
        echo div_start('col-md-6')
        ?>
        <div class="card card-info IRANSans">
            <div class="card-header text-center p-1">
                <div class="">
                    <?= $gateway['title'] ?>
                    -
                    <strong><?= $gateway['slugControl'] ?></strong>
                    <span id="gateWayBodyCheck_<?= $gateway['id'] ?>"></span>
                </div>
            </div>
            <div class="card-body p-1">
                <?php
                echo div_container_row();

                echo div_start('col-md-4 text-muted');
                __e("main address");
                echo div_close();

                echo div_start('col-md-8 ltr text-left', '', false, '', "style='overflow-x:scroll;white-space: nowrap;'");
                echo $gateway['mainAddress'];
                echo div_close();

                echo div_start('w-100 border-top my-1', '', true);

                echo div_start('col-md-4 text-muted');
                __e("api key");
                echo div_close();

                echo div_start('col-md-8 ltr text-left', '', false, '', "style='overflow-x:scroll;white-space: nowrap;'");
                $gatewayApiKey=$gateway['apiKey'];
                if(!empty($gatewayApiKey) && strlen($gatewayApiKey)>=9){
                    $gatewayApiKey=substr($gatewayApiKey,0,4).'......'.substr($gatewayApiKey,-4,4);
                }
                echo $gatewayApiKey;
                echo div_close();

                echo div_start('w-100 border-top my-1', '', true);

                echo div_start('col-md-4 text-muted');
                __e("last token");
                echo div_close();

                echo div_start('col-md-8 ltr text-left', '', false, '', "style='overflow-x:scroll;white-space: nowrap;'");
                echo $gateway['lastToken'];
                echo div_close();

                echo div_start('w-100 border-top my-1', '', true);

                echo div_start('col-md-4 text-muted');
                __e("last token date");
                echo div_close();

                echo div_start('col-md-8 ltr text-left', '', false, '', "style='overflow-x:scroll;white-space: nowrap;'");
                echo $gateway['lastTokenDate'];
                echo div_close();

                echo div_start('w-100 border-top my-1', '', true);

                echo div_start('col-md-4 text-muted d-flex flex-row justify-content-center align-items-center');
                __e("last token data");
                echo div_close();

                echo div_start('col-md-8 ltr text-left', '', false, '', "style='overflow-y:scroll;height:150px!important;'");
                $isJson = is_json($gateway['lastCheckToken'], true, true);
                if (checkArraySize($isJson)) {
                    echo '<pre>' . (json_encode($isJson, JSON_PRETTY_PRINT)) . '</pre>';
//                echo str_replace("\n","<br>",json_encode($isJson, JSON_PRETTY_PRINT));
                }
                echo div_close();

                echo div_container_row_close();
                ?>
            </div>
            <div class="card-footer text-center p-1">
                <a class="btn btn-xs btn-outline-success"
                   href="<?= JK_DOMAIN_LANG() . 'cp/jgate/gateways/manage/' . $gateway['id'] ?>">
                    <i class="fal fa-eye"></i>
                    <?= __("manage") ?>
                </a>

                <button class="btn btn-xs btn-outline-success" type="button"
                        onclick="refreshGToken(<?= $gateway['id'] ?>)">
                    <i class="fal fa-sync"></i>
                </button>
                <button class="btn btn-xs btn-outline-warning" type="button"
                        onclick="resetGToken(<?= $gateway['id'] ?>)">
                    <i class="fal fa-eraser"></i>
                </button>

                <button class="btn btn-xs btn-outline-info" type="button" onclick="addGateway(<?= $gateway['id'] ?>)">
                    <i class="fal fa-edit"></i>
                </button>
            </div>
        </div>
        <?php
        echo div_close();
    }
}else{
    echo alertWarning(__("not found any gateway"));
}

echo div_container_row_close();
\Joonika\Controller\AstCtrl::ADD_FOOTER_SCRIPTS('<script>
 function addGateway(id=\'\'){
    $("#modal_global_title").html("' . __("address") . '");
    $("#modal_global").modal("show");
    ' . ajax_load([
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/ajax/add',
        "success_response" => "#modal_global_body",
        "data" => "{id:id}",
        "loading" => ['iclass-size' => 1, 'elem' => 'span']
    ]) . '
} 
function refreshGToken(id){
    ' . ajax_load([
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/ajax/refreshGToken',
        "success_response" => "#gateWayBodyCheck_\"+id+\"",
        "data" => "{id:id}",
        "loading" => ['iclass-size' => 1, 'elem' => 'span']
    ]) . '
}
function resetGToken(id){
    ' . ajax_load([
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/ajax/resetGToken',
        "success_response" => "#gateWayBodyCheck_\"+id+\"",
        "data" => "{id:id}",
        "loading" => ['iclass-size' => 1, 'elem' => 'span']
    ]) . '
}
       
</script>');
get_foot($this);