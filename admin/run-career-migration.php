<?php
require_once('inc/config.php');

function migrationTableExists($pdo, $table) {
    $statement = $pdo->prepare('SHOW TABLES LIKE ?');
    $statement->execute([$table]);
    return $statement->rowCount() > 0;
}

$messages = [];
$errors = [];

try {
    if (!migrationTableExists($pdo, 'tbl_vacancy')) {
        $pdo->exec("
            CREATE TABLE `tbl_vacancy` (
              `id` int NOT NULL AUTO_INCREMENT,
              `title` varchar(255) NOT NULL,
              `department` varchar(150) NOT NULL DEFAULT '',
              `description` text,
              `deadline` date DEFAULT NULL,
              `status` varchar(20) NOT NULL DEFAULT 'Active',
              `created_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $messages[] = 'Created tbl_vacancy';
    } else {
        $messages[] = 'Skipped tbl_vacancy (exists)';
    }

    if (!migrationTableExists($pdo, 'tbl_career_application')) {
        $pdo->exec("
            CREATE TABLE `tbl_career_application` (
              `id` int NOT NULL AUTO_INCREMENT,
              `vacancy_id` int NOT NULL,
              `full_name` varchar(150) NOT NULL,
              `phone` varchar(50) NOT NULL DEFAULT '',
              `email` varchar(150) NOT NULL DEFAULT '',
              `resume_note` text,
              `cover_letter` text,
              `status` varchar(30) NOT NULL DEFAULT 'New',
              `created_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `vacancy_id_idx` (`vacancy_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $messages[] = 'Created tbl_career_application';
    } else {
        $messages[] = 'Skipped tbl_career_application (exists)';
    }
} catch (Throwable $e) {
    $errors[] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Career Migration</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="container" style="padding:40px;">
    <h2>Career / Vacancy Migration</h2>
    <?php foreach ($messages as $m) { ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($m); ?></div>
    <?php } ?>
    <?php foreach ($errors as $e) { ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($e); ?></div>
    <?php } ?>
    <p>
        <a class="btn btn-primary" href="vacancy.php">Manage Vacancies</a>
        <a class="btn btn-default" href="career-application.php">Applications</a>
    </p>
</body>
</html>
