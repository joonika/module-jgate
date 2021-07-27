<?php
if (isset($_POST['serviceId'], $_POST['gatewayId'])) {
    if (isset($_POST['submit'])) {
        $addRel = \Modules\jgate\src\jgate::request('manage/agentService', [
            "action" => "add",
            "agent" => $_POST['agent'],
            "service" => $_POST['serviceId'],
            "dailyLimit" => $_POST['dailyLimit'],
            "monthlyLimit" => $_POST['monthlyLimit'],
        ], false, $_POST['gatewayId']);

        if ($addRel['success']) {
            echo alertInfo(__("agent removed successfully"));
            echo redirect_to_js();
            die;
        } else {
            echo \Modules\jgate\src\jgate::errorMessages($addRel);
        }
    }elseif(isset($_POST['statusServiceChangeRelId'])){
        $addRel = \Modules\jgate\src\jgate::request('manage/agentService', [
            "action" => "reStatus",
            "agent" => $_POST['agent'],
            "service" => $_POST['statusServiceChangeRelId'],
        ], false, $_POST['gatewayId']);

        if ($addRel['success']) {
            echo alertInfo(__("service changed successfully"));
            die;
        } else {
            echo \Modules\jgate\src\jgate::errorMessages($addRel);
        }

    }
    $agentsRel = \Modules\jgate\src\jgate::request('manage/agentsServicesList', [
        "serviceId" => $_POST['serviceId']
    ], false, $_POST['gatewayId']);
    $agents = \Modules\jgate\src\jgate::request('manage/agentsList', [
    ], false, $_POST['gatewayId']);

    if(!empty($agents['data'])){
        $oldAgents=[];
        if(!empty($agentsRel['data'])){
            foreach ($agentsRel['data'] as $r){
                array_push($oldAgents,$r['agentName']);
            }
        }
        $agentsArray = [];
        foreach ($agents['data'] as $row) {
            if(!in_array($row['name'],$oldAgents)){
            $agentsArray[$row['name']] = $row['name'];
            }
        }

        echo \Joonika\Forms::form_create(["id" => "addFormAgent"]);

        echo \Joonika\Forms::field_hidden([
            "name" => "serviceId",
            "value" => $_POST['serviceId'],
        ]);
        echo \Joonika\Forms::field_hidden([
            "name" => "gatewayId",
            "value" => $_POST['gatewayId'],
        ]);
        echo div_container_row();

        echo div_start('col-md-4');
        echo \Joonika\Forms::field_select([
            "name" => "agent",
            "id" => "agent",
            "required" => true,
            "first" => true,
            "title" => __("agent"),
            "array" => $agentsArray
        ]);
        echo div_close();

        echo div_start('col-md-4');
        echo \Joonika\Forms::field_text([
            "name" => "dailyLimit",
            "title" => __("daily limit"),
            "value" => 0,
            "direction" => "ltr",
        ]);
        echo div_close();

        echo div_start('col-md-4');
        echo \Joonika\Forms::field_text([
            "name" => "monthlyLimit",
            "title" => __("monthly limit"),
            "value" => 0,
            "direction" => "ltr",
        ]);
        echo div_close();

        echo div_container_row_close();

        echo \Joonika\Forms::field_submit([
            "text" => __("save"),
            "ColType" => "12,12",
            "btn-class" => "btn btn-primary",
            "icon" => "fal fa-save"
        ]);

        echo \Joonika\Forms::form_end();

    }

    ?>
    <table class="table text-left small">
        <thead>
        <tr>
            <th><?= __("allow status") ?></th>
            <th><?= __("service name") ?></th>
            <th><?= __("agent name") ?></th>
            <th><?= __("daily limit") ?></th>
            <th><?= __("monthly limit") ?></th>
            <th><?= __("datetime") ?></th>
            <th><?= __("operation") ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($agentsRel['data'])) {
            foreach ($agentsRel['data'] as $agent) {
                $bgColor = "white";
                if (empty($agent['serviceId'])) {
                    $bgColor = "orange";
                }
                $allowStatus = $agent['status'] == 1 ? '<i class="fal fa-check-circle text-success" ></i>' : '<i class="fal fa-times text-danger" ></i>';
                $serviceStatusColor = $agent['serviceStatus'] == 1 ? 'text-success' : 'text-danger';
                $agentStatusColor = $agent['agentStatus'] == 1 ? 'text-success' : 'text-danger';
                ?>
                <tr style="background-color: <?= $bgColor ?>!important;">
                    <td><button class="btn btn-outline-light btn-xs" onclick="statusServiceRelChange('<?=$agent['agentName']?>',<?=$agent['id']?>,<?=$agent['serviceId']?>)"><?= $allowStatus ?></button></td>
                    <td class="<?= $serviceStatusColor ?>"><?= $agent['serviceName'] ?></td>
                    <td class="<?= $agentStatusColor ?>"><?= $agent['agentName'] ?></td>
                    <td><?= $agent['dailyLimit'] ?></td>
                    <td><?= $agent['monthlyLimit'] ?></td>
                    <td><?= \Joonika\Idate::date_int('Y/m/d-H:i', $agent['datetime']) ?></td>
                    <td>
                        <button type="button" class="btn btn-xs btn-outline-info"
                                onclick="removeRelation('<?= $agent['agentName'] ?>','<?= $agent['serviceId'] ?>')"><i class="fal fa-times"></i>
                        </button>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
    <?php
    \Joonika\Controller\AstCtrl::FOOTER_SCRIPTS('<script>
    ' . ajax_validate([
            "on" => "submit",
            "formID" => "addFormAgent",
            "url" => JK_DOMAIN_LANG() . "cp/jgate/gateways/manageTabs/services/showAgents",
            "success_response" => "#modal_global_body",
            "loading" => ['iclass-size' => 1, 'elem' => 'span']
        ]) . '
</script>');

    do_scripts();
}