<?php require_once('header.php'); ?>

<?php
ensureBirthdayTables($pdo);

if (!isset($_REQUEST['id'])) {
    header('location: birthday-template.php');
    exit;
}

$id = (int)$_REQUEST['id'];
$statement = $pdo->prepare("SELECT template_image FROM tbl_birthday_template WHERE id=?");
$statement->execute(array($id));
$row = $statement->fetch(PDO::FETCH_ASSOC);
if ($row) {
    $photo = $row['template_image'];
    $pdo->prepare("DELETE FROM tbl_birthday_template WHERE id=?")->execute(array($id));
    if ($photo !== '') {
        adminDeleteUploadIfUnused($pdo, $photo, 'tbl_birthday_template', 'template_image', $id);
    }
}

header('location: birthday-template.php?deleted=1');
exit;