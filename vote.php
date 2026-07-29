<?php
require_once __DIR__.'/inc/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $candidateId = (int)$_POST['candidate'];

    $stmt = $pdo->prepare("
        UPDATE tbl_elections
        SET vote_count = vote_count + 1
        WHERE id=?
    ");

    $success = $stmt->execute([$candidateId]);

    echo json_encode([
        'success' => $success
    ]);
}