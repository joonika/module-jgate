<?php
$queryCountAll = "";
$htmlData = '-';
$statistics = \Modules\jgate\src\jgate::request('manage/statistics', [], false, $_POST['gatewayID']);
if(empty($statistics['success'])){
    echo alertWarning(Modules\jgate\src\jgate::errorMessages($statistics));
    die;
}
$html_services = '-';
$html_agents = '-';
$html_logs = '-';
function statistics_parser($array = [])
{
    $colors=[
            '200'=>"success",
            'active'=>"success",
            '403'=>"danger",
            '400'=>"warning",
    ];
    $output = '-';
    if (!empty($array)) {
        $output = '';
        foreach ($array as $a) {
            $output .= '<div class=" bg-'.(!empty($colors[$a['status']])?$colors[$a['status']]:'info').' rounded my-1 mx-1 px-1 text-white d-inline-block">' . (!empty($a['status']) ? $a['status'] : '-') . ' = ' . (!empty($a['count']) ? $a['count'] : '-') . '</div>';
        }
    }
    return $output;
}
$html_agents = !empty($statistics['data']['agents']) ? statistics_parser($statistics['data']['agents']) : '-';
$html_services = !empty($statistics['data']['services']) ? statistics_parser($statistics['data']['services']) : '-';
$html_logs = !empty($statistics['data']['logs']) ? statistics_parser($statistics['data']['logs']) : '-';

?>
    <script>
        $("#countBody_agents").html('<?=$html_agents?>');
        $("#countBody_services").html('<?=$html_services?>');
        $("#countBody_logs").html('<?=$html_logs?>');
    </script>
<?php