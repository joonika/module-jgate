<?php
if (!empty($_POST['gatewayId'])) {
    $log = \Modules\jgate\src\jgate::request('manage/log', [
        "id" => $_POST['rowId'],
    ], false, $_POST['gatewayId']);
    if (!empty($log['data'])) {
        $data = $log['data'];
        $service = '{' . $data['serviceId'] . '}' . $data['serviceName'] . '/' . $data['serviceMethod'];
        if (empty($row['serviceId'])) {
            $service = '-';
        }
        echo div_container_row();

        echo div_start('col-12 col-md-3 border text-center p-0');
        ?>
        <div class="bg-info text-white"><?= __("id") ?></div>
        <div><?= $data['id'] ?></div>
        <?php
        echo div_close();

        echo div_start('col-12 col-md-3 border text-center p-0');
        ?>
        <div class="bg-info text-white"><?= __("agent") ?></div>
        <div><?= $data['agent'] ?></div>
        <?php
        echo div_close();

        echo div_start('col-12 col-md-3 border text-center p-0');
        ?>
        <div class="bg-info text-white"><?= __("status") ?></div>
        <div><?= $data['status'] ?></div>
        <?php
        echo div_close();

        echo div_start('col-12 col-md-3 border text-center p-0');
        ?>
        <div class="bg-info text-white"><?= __("execute duration") ?></div>
        <div><?= $data['execTime'] ?></div>
        <?php
        echo div_close();

        echo div_start('col-12 col-md-3 border text-center p-0');
        ?>
        <div class="bg-info text-white"><?= __("datetime") ?></div>
        <div><?= \Joonika\Idate::date_int('Y/m/d-H:i:s', $data['logTime']) ?></div>
        <?php
        echo div_close();

        echo div_start('col-12 col-md-3 border text-center p-0');
        ?>
        <div class="bg-info text-white"><?= __("service") ?></div>
        <div><?= $service ?></div>
        <?php
        echo div_close();
        echo div_start('col-12 col-md-3 border text-center p-0');
        ?>
        <div class="bg-info text-white"><?= __("ip") ?></div>
        <div><?= $data['requestIp'] ?></div>
        <?php
        echo div_close();

        echo div_start('col-12 border text-center p-0 my-2');
        $json = json_encode($data['input'], JSON_UNESCAPED_UNICODE);
        ?>
        <div class="bg-info text-white"><?= __("input") ?></div>
        <pre class="ltr text-left"><?= prettyJsonPrint($json) ?></pre>
        <?php
        echo div_close();

        echo div_start('col-12 border text-center p-0');
        $json = json_encode($data['data'], JSON_UNESCAPED_UNICODE);
        ?>
        <div class="bg-info text-white"><?= __("data") ?></div>
        <pre class="ltr text-left"><?= prettyJsonPrint($json) ?></pre>
        <?php
        echo div_close();

        echo div_container_row_close();
    }
}
