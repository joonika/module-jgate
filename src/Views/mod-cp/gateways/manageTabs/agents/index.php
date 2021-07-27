<?php

use Joonika\Controller\AstCtrl;

$agents = \Modules\jgate\src\jgate::request('manage/agentsList', [], false, $gatewayID);
?>
    <button type="button" class="btn btn-outline-primary btn-xs" onclick="addAgent()"><i
                class="fal fa-plus"></i> <?= __("add agent") ?></button>
    <table class="table  text-left" id="agentsList">
        <thead>
        <tr>
            <th><?= __("id") ?></th>
            <th><?= __("name") ?></th>
            <th><?= __("api key") ?></th>
            <th><?= __("datetime") ?></th>
            <th><?= __("status") ?></th>
            <th><?= __("operation") ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($agents['data'])) {
            foreach ($agents['data'] as $agent) {
                $colorStatus = $agent['status'] == 1 ? 'success' : ($agent['status'] == 0 ? 'warning' : 'danger');
                $statusTxt = $agent['status'];
                switch ($agent['status']) {
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
                    <td><?= $agent['id'] ?></td>
                    <td><?= $agent['name'] ?></td>
                    <td>
                        <i class="fal fa-sync text-danger mx-2" onclick="refreshKey('<?= $agent['name'] ?>')"></i>
                        <?= $agent['apikey'] ?></td>
                    <td><?= \Joonika\Idate::date_int('Y/m/d-H:i', $agent['datetime']) ?></td>
                    <td>
                        <button type="button" class="btn btn-xs btn-outline-<?= $colorStatus ?>"
                                onclick="reStatus('<?= $agent['name'] ?>')"><?= $statusTxt ?></button>
                    </td>
                    <td>
                        <button type="button" class="btn btn-xs btn-outline-info"
                                onclick="showServices('<?= $agent['name'] ?>')"><i class="fal fa-list"></i>
                        </button>
                        <i class="fal fa-times text-danger mx-2" onclick="removeAgent('<?= $agent['name'] ?>')"></i>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
<?php

\Joonika\Controller\AstCtrl::ADD_FOOTER_SCRIPTS('
<script>
' . datatable_structure([
        "id" => "agentsList",
        "type" => "html",
    ]) . '
function showServices(agent=\'\') {
  $("#modal_global").modal("show");
      ' . ajax_load([
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/manageTabs/agents/showServices',
        "data" => "{agent:agent,gatewayId:" . $gatewayID . "}",
        "success_response" => "#modal_global_body",
        "loading" => ['iclass-size' => 1, 'elem' => 'span']
    ]) . '
}
function addAgent() {
  $("#modal_global").modal("show");
      ' . ajax_load([
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/manageTabs/agents/addAgent',
        "data" => "{gatewayId:" . $gatewayID . "}",
        "success_response" => "#modal_global_body",
        "loading" => ['iclass-size' => 1, 'elem' => 'span']
    ]) . '
}
function removeAgent(name){
    $("#modal_global").modal("hide");
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
        "data" => "{name:name,gatewayId:" . $gatewayID . "}",
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/manageTabs/agents/removeAgent',
        "success_response" => "#modal_global_body",
        "loading" => [
        ]
    ]) . '
    }
});
}
function refreshKey(name){
    swal({
  title: \'' . __("are you sure to refresh api key") . '\',
  type: \'warning\',
  showCancelButton: true,
  confirmButtonColor: \'#3085d6\',
  confirmButtonText: \'' . __("Yes, regenerate it") . '!\',
  cancelButtonText: \'' . __("cancel") . '!\'
}).then((result) => {
if(result.value){
    $("#modal_global-body").html(\'' . loading_fa() . '\');
    $("#modal_global").modal("show");
  ' . ajax_load([
        "data" => "{name:name,gatewayId:" . $gatewayID . "}",
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/manageTabs/agents/refreshKey',
        "success_response" => "#modal_global_body",
        "loading" => [
        ]
    ]) . '
    }
});
}
function reStatus(name){
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
        "data" => "{name:name,gatewayId:" . $gatewayID . "}",
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/manageTabs/agents/changeStatus',
        "success_response" => "#modal_global_body",
        "loading" => [
        ]
    ]) . '
    }
});
}

function removeRelation(agent,service){
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
        "data" => "{agent:agent,service:service,gatewayId:" . $gatewayID . "}",
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/manageTabs/agents/removeRelation',
        "success_response" => "#modal_global_body",
        "loading" => [
        ]
    ]) . '
    }
});
}
function statusServiceRelChange(agent,relId){
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
        "data" => "{statusServiceChangeRelId:relId,agent:agent,gatewayId:" . $gatewayID . "}",
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/manageTabs/agents/showServices',
        "success_response" => "#modal_global_body",
        "loading" => false
    ]) . '
    }
});
}
function tokenLessStatusChange(agent,relId){
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
        "data" => "{tokenlessServiceChangeRelId:relId,agent:agent,gatewayId:" . $gatewayID . "}",
        "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/manageTabs/agents/showServices',
        "success_response" => "#modal_global_body",
        "loading" => false
    ]) . '
    }
});
}
</script>
');
