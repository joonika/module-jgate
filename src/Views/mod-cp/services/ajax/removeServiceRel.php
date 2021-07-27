<?php
if(!empty($_POST['relId'])){
    $database=\Joonika\Database::connect();
    $serviceID=$database->get('jgate_services_rel','sId', [
        "id" => $_POST['relId'],
    ]);
    $database->update('jgate_services_rel', [
        "status" => 0
    ], [
            "id" => $_POST['relId'],
    ]);
    ?>
<script>
    $("#relationSort_<?=$serviceID?>_<?=$_POST['relId']?>").remove();
</script>
<?php
}
