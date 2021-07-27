<?php
if (!\Joonika\ACL::ACL()->hasPermissionLogin('jgate_gateways')) {
    error403();
    die;
}
global $data;
$database = \Joonika\Database::connect();
modules_assets_to_ctrl("cp/assets/js/jquery-sortable.js");

get_head($this);


?>
    <div class="text-center my-2">

        <button type="button" class="btn btn-xs btn-outline-primary" onclick="addService()">
            <i class="fal fa-plus"></i>
            <?= __("add service") ?>
        </button>
        <button type="button" class="btn btn-xs btn-outline-info" onclick="updateServices()">
            <i class="fal fa-sync"></i>
            <?= __("update service") ?>
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table responsive table-xs text-xs table-bordered  m-0 small">
                    <thead>
                    <tr>
                        <th><?= __("id") ?></th>
                        <th><?= __("slug") ?></th>
                        <th><?= __("title") ?></th>
                        <th><?= __("type") ?></th>
                        <th><?= __("status") ?></th>
                        <th><?= __("gateways") ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    $gateways = $database->select("jgate_gateways", [
                        "id" => [
                            'id',
                            'title',
                            'slugControl',
                            'servicesJson',
                        ]
                    ]);
                    $services = $database->select('jgate_services', '*', [
                        "ORDER" => "id"
                    ]);
                    if (checkArraySize($services)) {
                        foreach ($gateways as $gate) {
                            $addresses = [];
                            $addressF = is_json($gate['servicesJson'], true);
                            if (!empty($addressF)) {
                                foreach ($addressF as $ad) {
                                    array_push($addresses, $ad['address']);
                                }
                            }
                            $gateways[$gate['id']]['services'] = $addresses;
                        }
                        foreach ($services as $service) {
                            ?>
                            <tr>
                                <td><?= $service['id'] ?></td>
                                <td><?= $service['slug'] ?></td>
                                <td><?= $service['title'] ?></td>
                                <td><?= $service['type'] ?></td>
                                <td><?= statusReturnYesNoInt($service['status'], true) ?></td>
                                <td class="text-center p-0 " style="padding: 0!important;">
                                    <div id="nestable_service_<?= $service['id'] ?>" class="relativeNestable">
                                        <?php
                                        $relations = $database->select("jgate_services_rel", '*', [
                                            "AND" => [
                                                "status[!]" => 0,
                                                "sId" => $service['id'],
                                            ],
                                            "ORDER" => ["sort" => "ASC"]
                                        ]);
                                        if (checkArraySize($relations)) {
                                            $rowNum = 0;
                                            foreach ($relations as $relation) {
                                                $stColor = $relation['status'] == 1 ? 'success' : 'warning';
                                                $cls = 'default';
                                                if (empty($gateways[$relation['gId']]['services']) || !in_array($relation['address'], $gateways[$relation['gId']]['services'])) {
                                                    $cls = 'danger';
                                                }
                                                ?>
                                                <div id="relationSort_<?= $service['id'] . '_' . $relation['id'] ?>"
                                                     class="my-1 border">
                                                    <?php
                                                    $gatewayTitle = !empty($gateways[$relation['gId']]['title']) ? $gateways[$relation['gId']]['title'] : '-';
                                                    $gatewaySlug = !empty($gateways[$relation['gId']]['slugControl']) ? $gateways[$relation['gId']]['slugControl'] : '-';
                                                    echo '<span class="text-' . $cls . '">' . $relation['id'] . '-' . $gatewaySlug . '-' . $gatewayTitle . '</span>';
                                                    echo '<button class="btn-xs btn btn-outline-primary mx-1" onclick="copyText(\'\',\'' . $relation['address'] . '\')" type="button" ' . Joonika\Modules\Cp\Cp::tooltip_view($relation['address'], JK_DIRECTION_SIDE_R()) . '><i class="fal fa-link"></i></button>';
                                                    echo '<i class="fal fa-sync mx-1 text-' . $stColor . '" id="relStatus_' . $relation['id'] . '" onclick="statusServiceRel(' . $relation['id'] . ')"></i>';
                                                    echo '<i class="fal fa-times mx-1 text-danger" onclick="removeServiceRel(' . $relation['id'] . ')"></i>';
                                                    echo '<br/>';
                                                    ?>
                                                    <span id="relationSort_<?= $relation['id'] ?>"></span>
                                                    <span id="relationStatus_<?= $relation['id'] ?>"></span>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-primary btn-xs"
                                            onclick="addServiceRel(<?= $service['id'] ?>)"><i class="fal fa-list"></i>
                                        <?= __("add service gate") ?>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-xs"
                                            onclick="addService(<?= $service['id'] ?>)"><i class="fal fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <div id="action_body"></div>
            </div>
        </div>
    </div>
<?php
\Joonika\Controller\AstCtrl::ADD_FOOTER_SCRIPTS('<script>
function moveElement(arrow,id){
        ' . ajax_load([
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/services/ajax/moveElement',
        "success_response" => "#modal_global_body",
        "data" => "{id:id}",
        "loading" => ['iclass-size' => 1, 'elem' => 'span']
    ]) . '
}
 function updateServices(){
    $("#modal_global_title").html("' . __("service") . '");
    $("#modal_global").modal("show");
    ' . ajax_load([
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/services/ajax/updateServices',
        "success_response" => "#modal_global_body",
        "data" => "{}",
        "loading" => ['iclass-size' => 1, 'elem' => 'span']
    ]) . '
}   
function addService(id=\'\'){
    $("#modal_global_title").html("' . __("service") . '");
    $("#modal_global").modal("show");
    ' . ajax_load([
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/services/ajax/add',
        "success_response" => "#modal_global_body",
        "data" => "{id:id}",
        "loading" => ['iclass-size' => 1, 'elem' => 'span']
    ]) . '
}  
function addServiceRel(sId=\'\'){
    $("#modal_global_title").html("' . __("service") . '");
    $("#modal_global").modal("show");
    ' . ajax_load([
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/services/ajax/add_sId',
        "success_response" => "#modal_global_body",
        "data" => "{sId:sId}",
        "loading" => ['iclass-size' => 1, 'elem' => 'span']
    ]) . '
} 
function statusServiceRel(relId=\'\'){

swal({
  title: \'' . __("are you sure?") . '\',
  type: \'warning\',
  showCancelButton: true,
  confirmButtonColor: \'#3085d6\',
  confirmButtonText: \'' . __("yes") . '!\',
  cancelButtonText: \'' . __("cancel") . '!\'
}).then((result) => {
if(result.value){
  ' . ajax_load([
        "data" => "{relId:relId}",
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/services/ajax/statusServiceRel',
        "success_response" => "#relationStatus_\"+relId+\"",
        "loading" => [
        ]
    ]) . '
    }
});
}
 function removeServiceRel(relId=\'\'){

swal({
  title: \'' . __("are you sure?") . '\',
  type: \'warning\',
  showCancelButton: true,
  confirmButtonColor: \'#3085d6\',
  confirmButtonText: \'' . __("yes") . '!\',
  cancelButtonText: \'' . __("cancel") . '!\'
}).then((result) => {
if(result.value){
  ' . ajax_load([
        "data" => "{relId:relId}",
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/services/ajax/removeServiceRel',
        "success_response" => "#relationStatus_\"+relId+\"",
        "loading" => [
        ]
    ]) . '
    }
});
} 
  $(\'[data-toggle="tooltip"]\').tooltip({
  html:true
  });
    $(function () {
    $( ".relativeNestable" ).sortable({
  update: function( event, ui ) {
                var relationID=$(this).attr("id");
                var order = $("#"+relationID).sortable("serialize"); 
                      ' . ajax_load([
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/services/ajax/relativeNestable',
        "success_response" => "#action_body",
        "data" => "{order:order,relationID:relationID}",
        "loading" => ['iclass-size' => 1, 'elem' => 'span']
    ]) . '
            }
});
        });
    function gIdChange(){
        var gId=$("#gId").val();
                              ' . ajax_load([
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/services/ajax/gIdChange',
        "success_response" => "#gIdChangeBody",
        "data" => "{gId:gId}",
        "loading" => ['iclass-size' => 1, 'elem' => 'span']
    ]) . '
    }
</script>');
get_foot($this);