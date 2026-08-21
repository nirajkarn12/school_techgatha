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
    if (!migrationTableExists($pdo, 'tbl_school_message')) {
        $pdo->exec("
            CREATE TABLE `tbl_school_message` (
              `id` int NOT NULL AUTO_INCREMENT,
              `role` varchar(40) NOT NULL,
              `person_name` varchar(150) NOT NULL DEFAULT '',
              `designation` varchar(150) NOT NULL DEFAULT '',
              `photo` varchar(255) NOT NULL DEFAULT '',
              `message` text NOT NULL,
              `status` varchar(20) NOT NULL DEFAULT 'Active',
              `sort_order` int NOT NULL DEFAULT 0,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `role_unique` (`role`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $messages[] = 'Created tbl_school_message';
    } else {
        $messages[] = 'Skipped tbl_school_message (exists)';
    }

    if (!migrationTableExists($pdo, 'tbl_admission')) {
        $pdo->exec("
            CREATE TABLE `tbl_admission` (
              `id` int NOT NULL AUTO_INCREMENT,
              `student_name` varchar(150) NOT NULL,
              `dob` date DEFAULT NULL,
              `gender` varchar(20) NOT NULL DEFAULT '',
              `class_applied` varchar(80) NOT NULL DEFAULT '',
              `parent_name` varchar(150) NOT NULL DEFAULT '',
              `phone` varchar(50) NOT NULL DEFAULT '',
              `email` varchar(150) NOT NULL DEFAULT '',
              `address` text,
              `previous_school` varchar(255) NOT NULL DEFAULT '',
              `message` text,
              `status` varchar(30) NOT NULL DEFAULT 'New',
              `created_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $messages[] = 'Created tbl_admission';
    } else {
        $messages[] = 'Skipped tbl_admission (exists)';
    }

    if (!migrationTableExists($pdo, 'tbl_calendar_event')) {
        $pdo->exec("
            CREATE TABLE `tbl_calendar_event` (
              `id` int NOT NULL AUTO_INCREMENT,
              `title` varchar(255) NOT NULL,
              `description` text,
              `event_date` date NOT NULL,
              `end_date` date DEFAULT NULL,
              `event_time` varchar(50) NOT NULL DEFAULT '',
              `location` varchar(255) NOT NULL DEFAULT '',
              `status` varchar(20) NOT NULL DEFAULT 'Active',
              `created_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `event_date_idx` (`event_date`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $messages[] = 'Created tbl_calendar_event';
    } else {
        $messages[] = 'Skipped tbl_calendar_event (exists)';
    }

    if (migrationTableExists($pdo, 'tbl_staff') && !migrationColumnExists($pdo, 'tbl_staff', 'bio')) {
        $pdo->exec("ALTER TABLE `tbl_staff` ADD COLUMN `bio` text NULL");
        $messages[] = 'Added tbl_staff.bio';
    } else {
        $messages[] = 'Skipped tbl_staff.bio';
    }

    $defaults = [
        ['principal', 'Principal', 'Message from the Principal', 1],
        ['chairman', 'Chairman', 'Message from the Chairman', 2],
        ['vice_principal', 'Vice Principal', 'Message from the Vice Principal', 3],
    ];
    $count = (int) $pdo->query('SELECT COUNT(*) FROM tbl_school_message')->fetchColumn();
    if ($count === 0) {
        $insert = $pdo->prepare("
            INSERT INTO tbl_school_message (role, person_name, designation, photo, message, status, sort_order, updated_at)
            VALUES (?, '', ?, '', ?, 'Active', ?, NOW())
        ");
        foreach ($defaults as $row) {
            $insert->execute([
                $row[0],
                $row[1],
                '<p>Welcome. Please update this message from the admin panel.</p>',
                $row[3],
            ]);
        }
        $messages[] = 'Seeded default leadership message rows';
    }
} catch (Throwable $e) {
    $errors[] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>School Migration</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="container" style="padding:40px;">
    <h2>School Website Migration</h2>
    <?php foreach ($messages as $m) { ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($m); ?></div>
    <?php } ?>
    <?php foreach ($errors as $e) { ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($e); ?></div>
    <?php } ?>
    <p>
        <a class="btn btn-primary" href="leadership.php">Leadership Messages</a>
        <a class="btn btn-default" href="calendar-event.php">Calendar</a>
        <a class="btn btn-default" href="admission-list.php">Admissions</a>
    </p>
</body>
</html>
