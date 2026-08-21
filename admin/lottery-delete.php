<?php require_once('header.php'); ?>

<?php

if (isset($_GET['id'])) {

    $id = (int)$_GET['id'];

    $statement = $pdo->prepare("
        DELETE FROM tbl_lottery_ticket
        WHERE ticket_id = ?
    ");

    $statement->execute(array($id));

    header('location: lottery.php?deleted=1');
    exit;
}

header('location: lottery.php');
exit;

?>