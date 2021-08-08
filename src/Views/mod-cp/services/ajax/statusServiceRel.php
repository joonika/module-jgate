<?php
if(!empty($_POST['relId'])){
    $database=\Joonika\Database::connect();
    $status=$database->get('jgate.jgate_services_rel','status', [
        "id" => $_POST['relId'],
    ]);
    $newStatus=$status==1?2:1;

    $database->update('jgate.jgate_services_rel', [
        "status" => $newStatus
    ], [
            "id" => $_POST['relId'],
    ]);

    $stColor=$newStatus==1?'success':'warning';
    $stColorRev=$newStatus==1?'warning':'success';

    ?>
<script>
    $("#relStatus_<?=$_POST['relId']?>").removeClass("text-<?=$stColorRev?>").addClass("text-<?=$stColor?>");
</script>
<?php
}
