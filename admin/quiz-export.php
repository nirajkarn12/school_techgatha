<?php

/*
|--------------------------------------------------------------------------
| QUIZ CSV EXPORT
|--------------------------------------------------------------------------
*/

/*
 * Do NOT include header.php here.
 * Replace this path if your PDO connection is located elsewhere.
 */

require_once('inc/config.php');


/*
|--------------------------------------------------------------------------
| FETCH DATA
|--------------------------------------------------------------------------
*/

$statement = $pdo->prepare("
    SELECT
        question,
        answer,
        type,
        set_no,
        media_file,
        created_at
    FROM tbl_quiz
    ORDER BY quiz_id DESC
");

$statement->execute();

$result = $statement->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| CSV FILE NAME
|--------------------------------------------------------------------------
*/

$filename = 'quiz_questions_' . date('Y-m-d_H-i-s') . '.csv';


/*
|--------------------------------------------------------------------------
| DOWNLOAD HEADERS
|--------------------------------------------------------------------------
*/

header('Content-Type: text/csv; charset=UTF-8');

header(
    'Content-Disposition: attachment; filename="' .
    $filename .
    '"'
);

header('Pragma: no-cache');
header('Expires: 0');
header('Cache-Control: no-cache, must-revalidate');


/*
|--------------------------------------------------------------------------
| OPEN CSV
|--------------------------------------------------------------------------
*/

$output = fopen('php://output', 'w');


/*
|--------------------------------------------------------------------------
| UTF-8 BOM
|--------------------------------------------------------------------------
|
| This helps Excel correctly recognize UTF-8 Nepali text.
|
*/

fwrite(
    $output,
    "\xEF\xBB\xBF"
);


/*
|--------------------------------------------------------------------------
| CSV HEADER
|--------------------------------------------------------------------------
|
| IMPORTANT:
| Keep these names exactly the same as quiz-upload.php.
|
*/

fputcsv(
    $output,
    array(
        'question',
        'answer',
        'type',
        'set_no',
        'media_file',
        'created_at'
    )
);


/*
|--------------------------------------------------------------------------
| CSV DATA
|--------------------------------------------------------------------------
*/

foreach ($result as $row) {

    fputcsv(
        $output,
        array(

            /*
             * Question
             */
            $row['question'] ?? '',

            /*
             * Answer
             */
            $row['answer'] ?? '',

            /*
             * Type
             */
            $row['type'] ?? '',

            /*
             * Set Number
             */
            $row['set_no'] ?? '',

            /*
             * Media File
             */
            $row['media_file'] ?? '',

            /*
             * Created At
             *
             * Exported for reference.
             * quiz-upload.php does not need
             * to import this column.
             */
            $row['created_at'] ?? ''
        )
    );
}


/*
|--------------------------------------------------------------------------
| CLOSE
|--------------------------------------------------------------------------
*/

fclose($output);

exit;

?>