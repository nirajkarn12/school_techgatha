<?php
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/functions.php';

$id = isset($argv[1]) ? (int)$argv[1] : 1;
$statement = $pdo->prepare("SELECT s.*, t.template_image, t.output_x, t.output_y, t.output_width, t.output_height FROM tbl_birthday_student s LEFT JOIN tbl_birthday_template t ON t.id=s.template_id WHERE s.id=?");
$statement->execute(array($id));
$row = $statement->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    fwrite(STDERR, "No student found with id={$id}\n");
    exit(2);
}

$templateImage = $row['template_image'];
$studentImage = $row['student_image'];
$studentName = $row['name'];
$className = $row['class_name'];
$birthdayDate = $row['birthday_date'];
$details = $row['details'];

$output_x = (int)$row['output_x'];
$output_y = (int)$row['output_y'];
$output_width = (int)$row['output_width'];
$output_height = (int)$row['output_height'];

// Default overlay positions (same logic as the preview page)
$name_x = $output_x;
$name_y = $output_y + $output_height + 20;
$class_x = $output_x + 20;
$class_y = $name_y + 52;

$text_size = 36;
$text_color = '#0c2b5f';
$text_style = 'bold';
$text_shadow = '1';
$image_layer = 'front';

$outputName = 'birthday-test-' . $id . '-' . time() . '.jpg';
$outputPath = adminUploadsPath($outputName);

$opts = array(
    'output_x' => $output_x,
    'output_y' => $output_y,
    'output_width' => $output_width,
    'output_height' => $output_height,
    'name_x' => $name_x,
    'name_y' => $name_y,
    'class_x' => $class_x,
    'class_y' => $class_y,
    'text_size' => $text_size,
    'text_color' => $text_color,
    'text_style' => $text_style,
    'text_shadow' => $text_shadow,
    'image_layer' => $image_layer,
);

$result = generateBirthdayCardImage(
    adminUploadsPath($templateImage),
    adminUploadsPath($studentImage),
    $outputPath,
    $studentName,
    $className,
    $birthdayDate,
    $details,
    $opts
);

if ($result['ok']) {
    echo "OK: " . $result['output'] . "\n";
    exit(0);
} else {
    echo "ERROR: " . $result['error'] . "\n";
    exit(3);
}
