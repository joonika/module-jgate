<?php
$statistics = \Modules\jgate\src\jgate::request('manage/statistics_logs', [
    "timeRange" => 'h',
//    "from" => date("Y-m-d",strtotime('-30 days')),
//    "to" => date("Y-m-d"),
], false, $_POST['gatewayID']);
if(empty($statistics['success'])){
    echo alertWarning(Modules\jgate\src\jgate::errorMessages($statistics));
    die;
}
$logs = !empty($statistics['data']) ? $statistics['data'] : [];

if (empty($logs)) {
    echo alertWarning(__("no report found"));
    die;
}

use Hisune\EchartsPHP\ECharts;

$report = new \Joonika\Modules\Reports\Reports();

\Hisune\EchartsPHP\Config::$renderScript = false;
$chart = new ECharts('');
$chart->tooltip->show = true;
$chart->tooltip->trigger = "axis";
$chart->tooltip->axisPointer->type = "shadow";
$chart->legend->data[] = "ناوگان بومی";
$chart->legend->data[] = "کل";
$arrDataX = [];
$arrDataS1 = [];
$arrDataS2 = [];
$arrDataS3 = [];
$datesSample = [];
$totalStatuses = [];

foreach ($logs as $chd) {
    if (!isset($datesSample[$chd['date']])) {
        $datesSample[$chd['date']] = [];
    }
    $datesSample[$chd['date']][$chd['status']] = $chd['count'];
    if (!in_array($chd['status'], $totalStatuses)) {
        array_push($totalStatuses, $chd['status']);
    }
}
$seriesCheck = [];

foreach ($datesSample as $dataK => $dataV) {
    $dataKTime=strpos($dataK,'/')?\Joonika\Idate::date_int('d/y/F', $dataK):$dataK;
    array_push($arrDataX,$dataKTime );
    foreach ($totalStatuses as $totalSt) {
        $v = 0;
        if (isset($dataV[$totalSt])) {
            $v = $dataV[$totalSt];
        }
        if (!isset($seriesCheck[$totalSt])) {
            $seriesCheck[$totalSt] = [];
        }
        array_push($seriesCheck[$totalSt], $v);
    }
}
$chart->xAxis[] = array(
    'type' => 'category',
    'data' => $arrDataX
);
$chart->yAxis[] = array(
    'type' => 'value',
    'axisTick' => ['show' => false],
);


foreach ($seriesCheck as $s => $v) {
    if ($s == 200) {
        $color = "green";
    } else {
        $color = 'blue';
    }
    $chart->series[] = array(
        'name' => $s,
        'type' => 'line',
        'color' => $color,
        'stack' => $s,
        'data' => $v,
        'smooth' => true,
    );
}


//$chart->series[] = array(
//    'name' => 'کل',
//    'type' => 'line',
//    'color' => '#00a6ca',
//    'stack' => 'تعداد کل',
//    'data' => $arrDataS2,
//    'smooth' => true,
//    'markLine' => [
//        'data' => [
//            ['type' => 'average', 'name' => 'میانگین']
//        ]
//    ],
//    'markPoint' => [
//        'data' => [
//            ['type' => 'max', 'name' => 'بیشترین'],
//            ['type' => 'min', 'name' => 'کمترین']
//        ]
//    ]
//);


$chart->textStyle->fontFamily = 'IranSans';
$title = div_start('col-12 bg-info p-1 rounded text-center text-white', '', true, "گزارش درخواست ها بر اساس روز");
echo div_start('col-12 col-md-12', '', true, $title . $chart->render('chart_weight_hour'));




