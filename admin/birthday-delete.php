<?php require_once('header.php'); ?>

<?php
ensureBirthdayTables($pdo);

if (!isset($_REQUEST['id'])) {
    header('location: birthday.php');
    exit;
}

$id = (int)$_REQUEST['id'];
$statement = $pdo->prepare("SELECT student_image, generated_image FROM tbl_birthday_student WHERE id=?");
$statement->execute(array($id));
$row = $statement->fetch(PDO::FETCH_ASSOC);
if ($row) {
    $image = $row['student_image'];
    $generated = $row['generated_image'];
    $pdo->prepare("DELETE FROM tbl_birthday_student WHERE id=?")->execute(array($id));
    if ($image !== '') {
        adminDeleteUploadIfUnused($pdo, $image, 'tbl_birthday_student', 'student_image', $id);
    }
    if ($generated !== '') {
        adminDeleteUploadIfUnused($pdo, $generated, 'tbl_birthday_student', 'generated_image', $id);
    }
}

header('location: birthday.php?deleted=1');
exit;