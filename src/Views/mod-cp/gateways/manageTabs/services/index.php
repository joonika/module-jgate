<?php
if (isset($gatewayID)) {
    $showRemoved = !empty($_GET['showRemoved']) ? $_GET['showRemoved'] : 0;
    $services = \Modules\jgate\src\jgate::request('manage/servicesList', [
        "showRemoved" => $showRemoved
    ], false, $gatewayID);
    if (!empty($services['data'])) {
        ?>
        <button type="button" class="btn btn-outline-primary btn-xs" onclick="addService()"><i
                    class="fal fa-plus"></i> <?= __("add service") ?></button>
        <a type="button"
           class="btn <?= !empty($_GET['showRemoved']) ? 'btn-primary' : 'btn-outline-secondary' ?> btn-xs"
           href="<?= JK_DOMAIN_LANG() ?>cp/jgate/gateways/manage/<?= $gatewayID ?>/services<?= !empty($_GET['showRemoved']) ? '' : '?showRemoved=1' ?>"
        ><i
                    class="fal fa-sync"></i> <?= __("show removed service") ?></a>
        <table class="table text-left">
            <thead>
            <tr>
                <th><?= __("id") ?></th>
                <th><?= __("name") ?></th>
                <th><?= __("address") ?></th>
                <th><?= __("type") ?></th>
                <th><?= __("auth less") ?></th>
                <th><?= __("datetime") ?></th>
                <th><?= __("status") ?></th>
                <th><?= __("agents") ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($services['data'] as $service) {
                $colorStatus = $service['status'] == 1 ? 'success' : ($service['status'] == 0 ? 'warning' : 'danger');
                $statusTxt = $service['status'];
                switch ($service['status']) {
                    case 1:
                        $statusTxt = __("active");
                        break;
                    case 0:
                        $statusTxt = __("inactive");
                        break;
                    case -1:
                        $statusTxt = __("removed");
                        break;
                }
                ?>
                <tr>
                    <td><?= $service['id'] ?></td>
                    <td><?= $service['name'] . '/' . $service['method']; ?></td>
                    <td class="ltr">
                        <?= $service['address'] ?></td>
                    <td><?= $service['type'] ?></td>
                    <td><?= checkValueHtmlFa($service['authLess']) ?></td>
                    <td><?= $service['datetime'] ?></td>
                    <td>
                        <button type="button" class="btn btn-xs btn-outline-<?= $colorStatus ?>"
                                onclick="reStatus('<?= $service['id'] ?>')"><?= $statusTxt ?></button>
                    </td>
                    <td class="text-nowrap">
                        <button type="button" class="btn btn-outline-info btn-xs"
                                onclick="showAgents('<?= $service['id'] ?>')"><i class="fal fa-users"></i></button>
                        <button type="button" class="btn btn-outline-primary btn-xs"
                                onclick="addService(<?= $service['id'] ?>)"><i
                                    class="fal fa-edit"></i></button>
                        <i class="fal fa-times text-danger mx-2" onclick="removeService('<?= $service['id'] ?>')"></i>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php
        \Joonika\Controller\AstCtrl::ADD_FOOTER_SCRIPTS('
<script>
function showAgents(serviceId=\'\') {
  $("#modal_global").modal("show");
      ' . ajax_load([
                "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/manageTabs/services/showAgents',
                "data" => "{serviceId:serviceId,gatewayId:" . $gatewayID . "}",
                "success_response" => "#modal_global_body",
                "loading" => ['iclass-size' => 1, 'elem' => 'span']
            ]) . '
}
function addService(serviceId=\'\') {
  $("#modal_global").modal("show");
      ' . ajax_load([
                "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/manageTabs/services/addService',
                "data" => "{gatewayId:" . $gatewayID . ",serviceId:serviceId}",
                "success_response" => "#modal_global_body",
                "loading" => ['iclass-size' => 1, 'elem' => 'span']
            ]) . '
}
function removeService(serviceId){
    swal({
  title: \'' . __("are you sure to remove") . '\',
  type: \'warning\',
  showCancelButton: true,
  confirmButtonColor: \'#3085d6\',
  confirmButtonText: \'' . __("Yes, delete it") . '!\',
  cancelButtonText: \'' . __("cancel") . '!\'
}).then((result) => {
if(result.value){
    $("#modal_global-body").html(\'' . loading_fa() . '\');
    $("#modal_global").modal("show");
  ' . ajax_load([
                "data" => "{serviceId:serviceId,gatewayId:" . $gatewayID . "}",
                "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/manageTabs/services/removeService',
                "success_response" => "#modal_global_body",
                "loading" => [
                ]
            ]) . '
    }
});
}

function reStatus(serviceId){
    swal({
  title: \'' . __("are you sure to change status") . '\',
  type: \'warning\',
  showCancelButton: true,
  confirmButtonColor: \'#3085d6\',
  confirmButtonText: \'' . __("yes") . '!\',
  cancelButtonText: \'' . __("cancel") . '!\'
}).then((result) => {
if(result.value){
    $("#modal_global-body").html(\'' . loading_fa() . '\');
    $("#modal_global").modal("show");
  ' . ajax_load([
                "data" => "{serviceId:serviceId,gatewayId:" . $gatewayID . "}",
                "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/manageTabs/services/changeStatus',
                "success_response" => "#modal_global_body",
                "loading" => [
                ]
            ]) . '
    }
});
}
function removeRelation(agent,serviceId){
    swal({
  title: \'' . __("are you sure to remove?") . '\',
  type: \'warning\',
  showCancelButton: true,
  confirmButtonColor: \'#3085d6\',
  confirmButtonText: \'' . __("yes") . '!\',
  cancelButtonText: \'' . __("cancel") . '!\'
}).then((result) => {
if(result.value){
    $("#modal_global-body").html(\'' . loading_fa() . '\');
    $("#modal_global").modal("show");
  ' . ajax_load([
                "data" => "{agent:agent,service:serviceId,gatewayId:" . $gatewayID . "}",
                "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/manageTabs/agents/removeRelation',
                "success_response" => "#modal_global_body",
                "loading" => [
                ]
            ]) . '
    }
});
}
function statusServiceRelChange(agent,relId,serviceId){
    swal({
  title: \'' . __("are you sure?") . '\',
  type: \'warning\',
  showCancelButton: true,
  confirmButtonColor: \'#3085d6\',
  confirmButtonText: \'' . __("yes") . '!\',
  cancelButtonText: \'' . __("cancel") . '!\'
}).then((result) => {
if(result.value){
    $("#modal_global-body").html(\'' . loading_fa() . '\');
    $("#modal_global").modal("show");
  ' . ajax_load([
                "data" => "{statusServiceChangeRelId:relId,agent:agent,serviceId:serviceId,gatewayId:" . $gatewayID . "}",
                "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/manageTabs/services/showAgents',
                "success_response" => "#modal_global_body",
                "loading" => false
            ]) . '
    }
});
}
</script>
');
    }
}