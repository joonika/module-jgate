<?php
if (isset($gatewayID)) {
    echo div_container_row();

    echo div_start('col-md-4');
    ?>
    <div class="card text-center">
        <div class="card-header p-1"><?= __("agents count") ?></div>
        <div class="card-body p-1 text-center" id="countBody_agents"><?=loading_fa(['iclass-size' => 1, 'class' => "small"])?></div>
    </div>
    <?php

    echo div_close();


    echo div_start('col-md-4');
    ?>
    <div class="card text-center">
        <div class="card-header p-1"><?= __("services count") ?></div>
        <div class="card-body p-1 text-center" id="countBody_services"><?=loading_fa(['iclass-size' => 1, 'class' => "small"])?></div>
    </div>
    <?php

    echo div_close();

    echo div_start('col-md-4');
    ?>
    <div class="card text-center">
        <div class="card-header p-1"><?= __("logs count") ?></div>
        <div class="card-body p-1 text-center" id="countBody_logs"><?=loading_fa(['iclass-size' => 1, 'class' => "small"])?></div>
    </div>
    <?php

    echo div_close();


    echo div_start('col-md-12');
    ?>
    <div class="card text-center">
        <div class="card-header p-1"><?= __("logs") ?></div>
        <div class="card-body p-1 text-center" id="stats_log_body">-</div>
        <div class="card-body p-1 text-center" id="statsHour_log_body">-</div>
    </div>
    <?php

    echo div_close();

    echo div_start('col-12','statistics_body',true);

    echo div_container_row_close();


    \Joonika\Controller\AstCtrl::ADD_FOOTER_SCRIPTS('
<script>
function statistics_gateway(){
      ' . ajax_load([
            "url" => JK_DOMAIN_LANG() . "cp/jgate/gateways/manageTabs/stats/statistics",
            "success_response" => "#statistics_body",
            "data" => "{gatewayID:" . $gatewayID . "}",
            "loading" => false
        ]) . '
}

function statsLogsChart(){
      ' . ajax_load([
            "url" => JK_DOMAIN_LANG() . "cp/jgate/gateways/manageTabs/stats/chart",
            "success_response" => "#stats_log_body",
            "data" => "{gatewayID:" . $gatewayID . "}",
            "loading" => ['iclass-size' => 1, 'elem' => 'span']
        ]) . '
}
function statsLogsChartHour(){
      ' . ajax_load([
            "url" => JK_DOMAIN_LANG() . "cp/jgate/gateways/manageTabs/stats/chartHour",
            "success_response" => "#statsHour_log_body",
            "data" => "{gatewayID:" . $gatewayID . "}",
            "loading" => ['iclass-size' => 1, 'elem' => 'span']
        ]) . '
}

$(document).ready(function() {
statistics_gateway();
statsLogsChart();
statsLogsChartHour();
    });

</script>
');
}