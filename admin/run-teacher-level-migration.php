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
    if (!migrationTableExists($pdo, 'tbl_teacher_level')) {
        $pdo->exec("
            CREATE TABLE `tbl_teacher_level` (
              `id` int NOT NULL AUTO_INCREMENT,
              `name` varchar(120) NOT NULL,
              `sort_order` int NOT NULL DEFAULT 0,
              `status` varchar(20) NOT NULL DEFAULT 'Active',
              `created_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `sort_order_idx` (`sort_order`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $messages[] = 'Created tbl_teacher_level';
    } else {
        $messages[] = 'Skipped tbl_teacher_level (exists)';
    }

    if (migrationTableExists($pdo, 'tbl_staff') && !migrationColumnExists($pdo, 'tbl_staff', 'level_id')) {
        $pdo->exec("ALTER TABLE `tbl_staff` ADD COLUMN `level_id` int NULL DEFAULT NULL");
        $messages[] = 'Added tbl_staff.level_id';
    } else {
        $messages[] = 'Skipped tbl_staff.level_id';
    }

    if (migrationTableExists($pdo, 'tbl_staff') && !migrationColumnExists($pdo, 'tbl_staff', 'sort_order')) {
        $pdo->exec("ALTER TABLE `tbl_staff` ADD COLUMN `sort_order` int NOT NULL DEFAULT 0");
        $messages[] = 'Added tbl_staff.sort_order';
    } else {
        $messages[] = 'Skipped tbl_staff.sort_order';
    }

    $count = (int) $pdo->query('SELECT COUNT(*) FROM tbl_teacher_level')->fetchColumn();
    if ($count === 0) {
        $insert = $pdo->prepare("
            INSERT INTO tbl_teacher_level (name, sort_order, status, created_at)
            VALUES (?, ?, 'Active', NOW())
        ");
        $defaults = [
            ['Leadership', 1],
            ['Primary Level', 2],
            ['Secondary Level', 3],
            ['Support Staff', 4],
        ];
        foreach ($defaults as $row) {
            $insert->execute($row);
        }
        $messages[] = 'Seeded default teacher levels';
    } else {
        $messages[] = 'Skipped default levels (already present)';
    }
} catch (Throwable $e) {
    $errors[] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Teacher Level Migration</title>
    <link rel="stylesheet" href="css/AdminLTE.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body style="padding:24px;">
    <h2>Teacher Level Migration</h2>
    <?php foreach ($messages as $msg) { ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
    <?php } ?>
    <?php foreach ($errors as $err) { ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
    <?php } ?>
    <p>
        <a class="btn btn-primary" href="teacher-level.php">Manage Levels</a>
        <a class="btn btn-default" href="staff.php">Teachers</a>
    </p>
</body>
</html>
