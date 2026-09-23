<?php require_once('header.php'); ?>

<?php

if (isset($_GET['id'])) {

    $id = (int)$_GET['id'];

    $statement = $pdo->prepare("
        SELECT media_file
        FROM tbl_quiz
        WHERE quiz_id = ?
    ");

    $statement->execute(array($id));

    $quiz = $statement->fetch(PDO::FETCH_ASSOC);

    if ($quiz) {

        // Delete associated media file if it exists
        if (!empty($quiz['media_file']) && file_exists($quiz['media_file'])) {
            unlink($quiz['media_file']);
        }

        // Delete quiz question
        $statement = $pdo->prepare("
            DELETE FROM tbl_quiz
            WHERE quiz_id = ?
        ");

        $statement->execute(array($id));
    }

    header('location: quiz.php?deleted=1');
    exit;
}

header('location: quiz.php');
exit;

?>