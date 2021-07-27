<?php
$countAll = 25;
$lists = [];
$serviceId = !empty($_GET['serviceId']) ? $_GET['serviceId'] : null;
$gatewayId = !empty($_GET['gatewayId']) ? $_GET['gatewayId'] : null;
$agent = !empty($_GET['agent']) ? $_GET['agent'] : null;
$status = !empty($_GET['status']) ? $_GET['status'] : null;
$page = 1;
$log_prepare = \Modules\jgate\src\jgate::request('manage/log_prepare', [
], false, $gatewayID);
if (!empty($log_prepare['success'])) {
    echo \Joonika\Forms::form_create([
        "id" => "filter_form"
    ]);
    echo \Joonika\Forms::field_hidden([
        "name" => "gatewayId",
        "value" => $gatewayID,
    ]);
    echo div_container_row();

    echo div_start('col-md-3');
    $arrayServices = [];
    if (!empty($log_prepare['data']['services'])) {
        foreach ($log_prepare['data']['services'] as $result) {
            $arrayServices[$result['id']] = $result['name'] . '/' . $result['method'];
        }
    }

    echo \Joonika\Forms::field_select([
        "title" => __("service"),
        "name" => "serviceId",
        "id" => "serviceId",
        "first" => true,
        "firstTitle" => __("all services"),
        "array" => $arrayServices,
    ]);
    echo div_close();


    echo div_start('col-md-3');
    $arrayAgents = [];
    if (!empty($log_prepare['data']['agents'])) {
        foreach ($log_prepare['data']['agents'] as $result) {
            $arrayAgents[$result['name']] = $result['name'];
        }
    }

    echo \Joonika\Forms::field_select([
        "title" => __("agent"),
        "name" => "agent",
        "id" => "agent",
        "first" => true,
        "firstTitle" => __("all agents"),
        "array" => $arrayAgents,
    ]);
    echo div_close();

    echo div_start('col-md-3');

    $arrayAgents = [];
    if (!empty($log_prepare['data']['statuses'])) {
        foreach ($log_prepare['data']['statuses'] as $result) {
            if (!empty($result)) {
                $arrayAgents[$result] = $result;
            }
        }
    }

    echo \Joonika\Forms::field_select([
        "title" => __("status code"),
        "name" => "status",
        "id" => "status",
        "first" => true,
        "firstTitle" => __("all statuses"),
        "array" => $arrayAgents,
    ]);

    echo div_close();

    echo div_container_row_close();

    echo \Joonika\Forms::form_end();
    ?>
    <table class="table responsive table-sm table-bordered small" id="datatable_list">
        <thead>
        <tr>
            <th><?= __("id") ?></th>
            <th><?= __("service") ?></th>
            <th><?= __("agent") ?></th>
            <th><?= __("executed time") ?></th>
            <th><?= __("created on") ?></th>
            <th><?= __("status") ?></th>
            <th><?= __("details") ?></th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <?php
    \Joonika\Controller\AstCtrl::FOOTER_SCRIPTS('
<script>
' . datatable_structure([
            "id" => "datatable_list",
            "type" => "ajax",
            "tabIndex" => 1,
            "drawCallback" => "",
            "ajax_url" => JK_DOMAIN_LANG() . "cp/jgate/gateways/manageTabs/logs/list?\"+$(\"#filter_form\").serialize()+\"",
            "columns" => [
                "id",
                "service",
                "agent",
                "execTime",
                "logTime",
                "status",
                "details",
            ],
        ]) . '
$("#filter_form").on("change",function(){
    $(\'#datatable_list\').DataTable().ajax.url("' . JK_DOMAIN_LANG() . "cp/jgate/gateways/manageTabs/logs/list?\"+$(\"#filter_form\").serialize()+\"" . '").load();
});
function viewTextLog(rowId){
        $("#modal_global").modal("show");
      ' . ajax_load([
            "url" => JK_DOMAIN_LANG() . 'cp/jgate/gateways/manageTabs/logs/log',
            "success_response" => "#modal_global_body",
            "data" => "{rowId:rowId,gatewayId:" . $gatewayID . "}",
            "loading" => ['iclass-size' => 1, 'elem' => 'span']
        ]) . '
}
</script>
');
}