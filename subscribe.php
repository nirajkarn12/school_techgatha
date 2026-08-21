<?php
require_once __DIR__ . '/inc/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => loadLang('method_not_allowed')]);
    exit;
}

if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => loadLang('newsletter_invalid_email')]);
    exit;
}

$email = trim((string)($_POST['email'] ?? $_POST['subs_email'] ?? ''));
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => loadLang('newsletter_invalid_email')]);
    exit;
}

try {
    $check = $pdo->prepare('SELECT subs_id, subs_active FROM tbl_subscriber WHERE subs_email = ? LIMIT 1');
    $check->execute([$email]);
    $existing = $check->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        if ((int)$existing['subs_active'] === 1) {
            echo json_encode(['status' => 'error', 'message' => loadLang('newsletter_already_subscribed')]);
            exit;
        }

        $update = $pdo->prepare('UPDATE tbl_subscriber SET subs_active = 1, subs_date = ?, subs_date_time = ?, subs_hash = ? WHERE subs_id = ?');
        $update->execute([
            date('Y-m-d'),
            date('Y-m-d H:i:s'),
            bin2hex(random_bytes(8)),
            (int)$existing['subs_id'],
        ]);
    } else {
        $insert = $pdo->prepare('INSERT INTO tbl_subscriber (subs_email, subs_date, subs_date_time, subs_hash, subs_active) VALUES (?, ?, ?, ?, 1)');
        $insert->execute([
            $email,
            date('Y-m-d'),
            date('Y-m-d H:i:s'),
            bin2hex(random_bytes(8)),
        ]);
    }

    echo json_encode(['status' => 'success', 'message' => loadLang('newsletter_success')]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => loadLang('newsletter_invalid_email')]);
}
