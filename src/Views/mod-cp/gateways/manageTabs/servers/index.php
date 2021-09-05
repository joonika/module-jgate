<?php
if (isset($gatewayID)) {
    $showRemoved = !empty($_GET['showRemoved']) ? $_GET['showRemoved'] : 0;
    $servers = \Modules\jgate\src\jgate::request('manage/serversList', [
        "showRemoved" => $showRemoved
    ], false, $gatewayID);
    ?>
    <button type="button" class="btn btn-outline-primary btn-xs" onclick="addServer()"><i
                class="fal fa-plus"></i> <?= __("add server") ?></button>
    <a type="button"
       class="btn <?= !empty($_GET['showRemoved']) ? 'btn-primary' : 'btn-outline-secondary' ?> btn-xs"
       href="<?= JK_DOMAIN_LANG() ?>cp/jgate/gateways/manage/<?= $gatewayID ?>/servers<?= !empty($_GET['showRemoved']) ? '' : '?showRemoved=1' ?>"
    ><i
                class="fal fa-sync"></i> <?= __("show removed servers") ?></a>

    <?php
    if (!empty($servers['data'])) {
        ?>
        <table class="table text-left">
            <thead>
            <tr>
                <th><?= __("id") ?></th>
                <th><?= __("name") ?></th>
                <th><?= __("address") ?></th>
                <th><?= __("test address") ?></th>
                <th><?= __("status") ?></th>
                <th><?= __("operations") ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($servers['data'] as $server) {
                $colorStatus = $server['status'] == 1 ? 'success' : ($server['status'] == 0 ? 'warning' : 'danger');
                $statusTxt = $server['status'];
                switch ($server['status']) {
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
                    <td><?= $server['id'] ?></td>
                    <td><?= $server['name'] ; ?></td>
                    <td class="ltr">
                        <?= $server['address'] ?></td>
                    <td class="ltr">
                        <?= $server['addressTest'] ?></td>
                    <td>
                        <button type="button" class="btn btn-xs btn-outline-<?= $colorStatus ?>"
                                onclick="reStatus('<?= $server['id'] ?>')"><?= $statusTxt ?></button>
                    </td>
                    <td class="text-nowrap">
                        <button type="button" class="btn btn-outline-primary btn-xs"
                                onclick="addServer(<?= $server['id'] ?>)"><i
                                    class="fal fa-edit"></i></button>
                        <i class="fal fa-times text-danger mx-2" onclick="removeServer('<?= $server['id'] ?>')"></i>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php
    }
    \Joonika\Controller\AstCtrl::ADD_FOOTER_SCRIPTS('
<script>
function addServer(serverId=\'\') {
  $("#modal_global").modal("show");
      ' . ajax_load([
            "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/manageTabs/servers/addServer',
            "data" => "{gatewayId:" . $gatewayID . ",serverId:serverId}",
            "success_response" => "#modal_global_body",
            "loading" => ['iclass-size' => 1, 'elem' => 'span']
        ]) . '
}
function removeServer(serverId){
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
            "data" => "{serverId:serverId,gatewayId:" . $gatewayID . "}",
            "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/manageTabs/servers/removeServer',
            "success_response" => "#modal_global_body",
            "loading" => [
            ]
        ]) . '
    }
});
}

function reStatus(serverId){
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
            "data" => "{serverId:serverId,gatewayId:" . $gatewayID . "}",
            "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/manageTabs/servers/changeStatus',
            "success_response" => "#modal_global_body",
            "loading" => [
            ]
        ]) . '
    }
});
}
</script>
');
}