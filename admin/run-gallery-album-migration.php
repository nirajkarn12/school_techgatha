<?php
require_once('inc/config.php');

function migrationTableExists($pdo, $table) {
    $statement = $pdo->prepare('SHOW TABLES LIKE ?');
    $statement->execute([$table]);
    return $statement->rowCount() > 0;
}

function migrationColumnExists($pdo, $table, $column) {
    $statement = $pdo->prepare("SHOW COLUMNS FROM `{$table}` LIKE ?");
    $statement->execute([$column]);
    return $statement->rowCount() > 0;
}

$messages = [];
$errors = [];

try {
    if (!migrationTableExists($pdo, 'tbl_gallery_album')) {
        $pdo->exec("
            CREATE TABLE `tbl_gallery_album` (
              `id` int NOT NULL AUTO_INCREMENT,
              `title` varchar(255) NOT NULL,
              `description` text,
              `cover_photo` varchar(255) NOT NULL DEFAULT '',
              `status` varchar(20) NOT NULL DEFAULT 'Active',
              `sort_order` int NOT NULL DEFAULT 0,
              `created_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `status_sort_idx` (`status`, `sort_order`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $messages[] = 'Created tbl_gallery_album';
    } else {
        $messages[] = 'Skipped tbl_gallery_album (exists)';
    }

    if (migrationTableExists($pdo, 'tbl_gallery') && !migrationColumnExists($pdo, 'tbl_gallery', 'album_id')) {
        $pdo->exec("ALTER TABLE `tbl_gallery` ADD COLUMN `album_id` int NULL DEFAULT NULL AFTER `id`");
        $pdo->exec("ALTER TABLE `tbl_gallery` ADD KEY `album_id_idx` (`album_id`)");
        $messages[] = 'Added tbl_gallery.album_id';
    } else {
        $messages[] = 'Skipped tbl_gallery.album_id';
    }
} catch (Throwable $e) {
    $errors[] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gallery Album Migration</title>
    <link rel="stylesheet" href="css/AdminLTE.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body style="padding:24px;">
    <h2>Gallery Album Migration</h2>
    <?php foreach ($messages as $msg) { ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
    <?php } ?>
    <?php foreach ($errors as $err) { ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
    <?php } ?>
    <p>
        <a class="btn btn-primary" href="gallery-album.php">Manage Albums</a>
        <a class="btn btn-default" href="gallery.php">All Photos</a>
    </p>
</body>
</html>
