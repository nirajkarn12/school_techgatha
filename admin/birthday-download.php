<?php
session_start();
include('inc/config.php');
include('inc/functions.php');

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

ensureBirthdayTables($pdo);

if (!isset($_REQUEST['id'])) {
    header('Location: birthday.php');
    exit;
}

$id = (int)$_REQUEST['id'];
$statement = $pdo->prepare("SELECT generated_image FROM tbl_birthday_student WHERE id = ?");
$statement->execute(array($id));
$row = $statement->fetch(PDO::FETCH_ASSOC);
if (!$row || empty($row['generated_image'])) {
    header('Location: birthday.php');
    exit;
}

$generatedImage = $row['generated_image'];
$filePath = adminUploadsPath($generatedImage);
if (!is_file($filePath) || !is_readable($filePath)) {
    header('Location: birthday.php');
    exit;
}

$filename = basename($generatedImage);
$mimeType = 'application/octet-stream';
if (function_exists('finfo_open')) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    if ($finfo) {
        $detected = finfo_file($finfo, $filePath);
        if ($detected) {
            $mimeType = $detected;
        }
        finfo_close($finfo);
    }
}

header('Content-Description: File Transfer');
header('Content-Type: ' . $mimeType);
header('Content-Disposition: attachment; filename="' . str_replace('"', '', $filename) . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));

flush();
readfile($filePath);
exit;
