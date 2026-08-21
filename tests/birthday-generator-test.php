<?php
require_once __DIR__ . '/../admin/inc/functions.php';

$root = dirname(__DIR__);
$template = $root . '/birthday/birthday.png';
$student = $root . '/birthday/image_of student.png';
$output = $root . '/assets/uploads/birthday-test-output.jpg';

if (!is_file($template) || !is_file($student)) {
    fwrite(STDERR, "Sample birthday assets are missing.\n");
    exit(1);
}

$result = generateBirthdayCardImage($template, $student, $output, 'Test Student', 'Grade 4', '2026-08-04', 'Happy Birthday!', array(
    'output_x' => 285,
    'output_y' => 205,
    'output_width' => 510,
    'output_height' => 590,
));

if (!$result['ok']) {
    fwrite(STDERR, $result['error'] . "\n");
    exit(1);
}

if (!is_file($output)) {
    fwrite(STDERR, "Expected output image was not created.\n");
    exit(1);
}

echo "Birthday card generated: {$output}\n";
