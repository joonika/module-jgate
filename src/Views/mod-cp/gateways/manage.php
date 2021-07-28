<?php

use Joonika\Controller\AstCtrl;

if (!\Joonika\ACL::ACL()->hasPermissionLogin('jgate_gateways')) {
    error403();
    die;
}
global $data;
$database = \Joonika\Database::connect();
\Joonika\Modules\Reports\Reports::echartJs_init();
AstCtrl::ADD_HEADER_STYLES_FILES('/assets/datatable-1-10-24/datatables.css');
AstCtrl::ADD_FOOTER_JS_FILES('/assets/datatable-1-10-24/datatables.min.js');
get_head($this);

$gateway = $database->get('jgate_gateways', '*', [
    "AND" => [
        "status[!]" => "removed",
        "id" => !empty($this->Route->path[3]) ? $this->Route->path[3] : 0,
    ],
    "ORDER" => "sort"
]);
if (!empty($gateway)) {
    $gatewayID=$gateway['id'];
    ?>
    <div class="card">
        <div class="card-body">
            <?php
            tab_menus(Modules\jgate\inc\Configs::gatewayConfigTabs(), JK_DOMAIN_LANG() . 'cp/jgate/gateways/manage/'.$gateway['id'].'/', 4,$this);
            ?>
            <hr/>
            <?php
            $linkName = !empty($this->Route->path[4])?$this->Route->path[4]:'-';
            if($linkName=='-'){
                echo alertInfo(__("please select tab"));
            }else{
            $filePath = JK_SITE_PATH() . 'vendor/joonika/module-jgate/src/Views/mod-cp/gateways/manageTabs/' . $linkName . '/index.php';
            if (file_exists($filePath)) {
                ?>
                <div class="card">
                    <div class="card-body">
                        <?php
                        include $filePath;
                        ?>
                    </div>
                </div>
                <?php
            } else {
                echo alertWarning(sprintf(__("tab (%s) not found"), $linkName));
            }
            }
            ?>
        </div>
    </div>
    <?php
    \Joonika\Controller\AstCtrl::ADD_FOOTER_SCRIPTS('<script>
</script>');
}else{
    echo alertDanger(__("gateway not found or active"));
}
get_foot($this);