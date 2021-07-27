<?php
if (!empty($_POST['order'])) {
    $serviceID = str_replace('nestable_service_', '', $_POST['relationID']);
    parse_str(html_entity_decode($_POST['order']), $order);
    if (!empty($order['relationSort_'.$serviceID])) {
        $sort = 0;
        foreach ($order['relationSort_'.$serviceID] as $pv) {
            $database=\Joonika\Database::connect();
            $database->update('jgate_services_rel', [
                "sort" => $sort
            ], [
                "AND" => [
                    "id" => $pv,
                    "sId" => $serviceID
                ]
            ]);
            $sort += 1;
        }
    }
}
