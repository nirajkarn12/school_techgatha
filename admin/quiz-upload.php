<?php require_once('header.php'); ?>

<?php

$error_message = '';
$success_message = '';

if (isset($_POST['form1'])) {

    $valid = 1;

    /*
    |--------------------------------------------------------------------------
    | CHECK FILE
    |--------------------------------------------------------------------------
    */

    if (
        !isset($_FILES['csv_file']) ||
        $_FILES['csv_file']['error'] == UPLOAD_ERR_NO_FILE
    ) {

        $valid = 0;

        $error_message .= 'Please select a CSV file<br>';

    } elseif ($_FILES['csv_file']['error'] != UPLOAD_ERR_OK) {

        $valid = 0;

        $error_message .= 'There was an error uploading the CSV file<br>';
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK EXTENSION
    |--------------------------------------------------------------------------
    */

    if ($valid == 1) {

        $file_name = $_FILES['csv_file']['name'];

        $extension = strtolower(
            pathinfo($file_name, PATHINFO_EXTENSION)
        );

        if ($extension != 'csv') {

            $valid = 0;

            $error_message .= 'Please upload a valid CSV file<br>';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | OPEN CSV
    |--------------------------------------------------------------------------
    */

    if ($valid == 1) {

        $file_tmp = $_FILES['csv_file']['tmp_name'];

        $handle = fopen($file_tmp, 'r');

        if ($handle === false) {

            $valid = 0;

            $error_message .= 'Unable to open CSV file<br>';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DETECT DELIMITER
    |--------------------------------------------------------------------------
    */

    if ($valid == 1) {

        $first_line = fgets($handle);

        if ($first_line === false) {

            $valid = 0;

            $error_message .= 'CSV file is empty<br>';

        } else {

            /*
             * Safely remove UTF-8 BOM.
             * This avoids preg_replace(null) deprecated warning.
             */

            if (substr($first_line, 0, 3) === "\xEF\xBB\xBF") {

                $first_line = substr($first_line, 3);
            }


            /*
             * Detect delimiter
             */

            $comma_count = substr_count(
                $first_line,
                ','
            );

            $semicolon_count = substr_count(
                $first_line,
                ';'
            );

            $tab_count = substr_count(
                $first_line,
                "\t"
            );


            if (
                $semicolon_count > $comma_count &&
                $semicolon_count >= $tab_count
            ) {

                $delimiter = ';';

            } elseif (
                $tab_count > $comma_count &&
                $tab_count > $semicolon_count
            ) {

                $delimiter = "\t";

            } else {

                $delimiter = ',';
            }


            rewind($handle);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | READ HEADER
    |--------------------------------------------------------------------------
    */

    if ($valid == 1) {

        $header = fgetcsv(
            $handle,
            0,
            $delimiter
        );


        if ($header === false || empty($header)) {

            $valid = 0;

            $error_message .=
                'CSV header could not be read<br>';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CLEAN HEADER
    |--------------------------------------------------------------------------
    */

    if ($valid == 1) {

        foreach ($header as $key => $value) {

            /*
             * Prevent null errors
             */

            if ($value === null) {

                $value = '';
            }


            /*
             * Remove UTF-8 BOM safely
             */

            if (substr($value, 0, 3) === "\xEF\xBB\xBF") {

                $value = substr($value, 3);
            }


            /*
             * Trim spaces, quotes and line breaks
             */

            $value = trim(
                $value,
                " \t\n\r\0\x0B\"'"
            );


            /*
             * Lowercase
             */

            $value = strtolower($value);


            $header[$key] = $value;
        }


        /*
         * Required columns
         *
         * set_no is included because it is now part
         * of your quiz table.
         */

        $required_columns = array(
            'question',
            'answer',
            'type',
            'set_no'
        );


        foreach ($required_columns as $required_column) {

            if (!in_array(
                $required_column,
                $header,
                true
            )) {

                $valid = 0;

                $error_message .=
                    'Missing CSV column: ' .
                    htmlspecialchars(
                        $required_column,
                        ENT_QUOTES,
                        'UTF-8'
                    ) .
                    '<br>';
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | IMPORT
    |--------------------------------------------------------------------------
    */

    if ($valid == 1) {

        /*
         * Find column indexes
         */

        $question_index = array_search(
            'question',
            $header,
            true
        );

        $answer_index = array_search(
            'answer',
            $header,
            true
        );

        $type_index = array_search(
            'type',
            $header,
            true
        );

        $set_no_index = array_search(
            'set_no',
            $header,
            true
        );

        /*
         * media_file is optional
         */

        $media_index = array_search(
            'media_file',
            $header,
            true
        );


        /*
         * Allowed question types
         */

        $allowed_types = array(
            'Gambling',
            'General',
            'Audio Visual'
        );


        $row_number = 1;

        $created = 0;

        $updated = 0;

        $skipped = 0;


        /*
         |--------------------------------------------------------------------------
         | READ CSV ROWS
         |--------------------------------------------------------------------------
         */

        while (($data = fgetcsv(
            $handle,
            0,
            $delimiter
        )) !== false) {

            $row_number++;


            /*
             * Skip empty rows
             */

            $empty_row = true;

            foreach ($data as $value) {

                if (
                    $value !== null &&
                    trim($value) !== ''
                ) {

                    $empty_row = false;

                    break;
                }
            }


            if ($empty_row) {

                continue;
            }


            /*
             |--------------------------------------------------------------------------
             | QUESTION
             |--------------------------------------------------------------------------
             */

            $question = '';

            if (
                isset($data[$question_index]) &&
                $data[$question_index] !== null
            ) {

                $question = trim(
                    $data[$question_index]
                );
            }


            /*
             |--------------------------------------------------------------------------
             | ANSWER
             |--------------------------------------------------------------------------
             */

            $answer = '';

            if (
                isset($data[$answer_index]) &&
                $data[$answer_index] !== null
            ) {

                $answer = trim(
                    $data[$answer_index]
                );
            }


            /*
             |--------------------------------------------------------------------------
             | TYPE
             |--------------------------------------------------------------------------
             */

            $type = '';

            if (
                isset($data[$type_index]) &&
                $data[$type_index] !== null
            ) {

                $type = trim(
                    $data[$type_index]
                );
            }


            /*
             |--------------------------------------------------------------------------
             | SET NUMBER
             |--------------------------------------------------------------------------
             */

            $set_no = '';

            if (
                isset($data[$set_no_index]) &&
                $data[$set_no_index] !== null
            ) {

                $set_no = trim(
                    $data[$set_no_index]
                );
            }


            /*
             |--------------------------------------------------------------------------
             | MEDIA FILE
             |--------------------------------------------------------------------------
             */

            $media_file = null;

            if (
                $media_index !== false &&
                isset($data[$media_index]) &&
                $data[$media_index] !== null
            ) {

                $media_file = trim(
                    $data[$media_index]
                );


                if ($media_file === '') {

                    $media_file = null;
                }
            }


            /*
             |--------------------------------------------------------------------------
             | VALIDATE QUESTION
             |--------------------------------------------------------------------------
             */

            if ($question === '') {

                $skipped++;

                $error_message .=
                    'Row ' .
                    $row_number .
                    ': Question is empty<br>';

                continue;
            }


            /*
             |--------------------------------------------------------------------------
             | VALIDATE ANSWER
             |--------------------------------------------------------------------------
             */

            if ($answer === '') {

                $skipped++;

                $error_message .=
                    'Row ' .
                    $row_number .
                    ': Answer is empty<br>';

                continue;
            }


            /*
             |--------------------------------------------------------------------------
             | VALIDATE TYPE
             |--------------------------------------------------------------------------
             */

            if (!in_array(
                $type,
                $allowed_types,
                true
            )) {

                $skipped++;

                $error_message .=
                    'Row ' .
                    $row_number .
                    ': Invalid type. Use General, Gambling, or Audio Visual<br>';

                continue;
            }


            /*
             |--------------------------------------------------------------------------
             | VALIDATE SET NUMBER
             |--------------------------------------------------------------------------
             */

            if ($set_no === '') {

                $skipped++;

                $error_message .=
                    'Row ' .
                    $row_number .
                    ': Set number is empty<br>';

                continue;
            }


            /*
             * Set number should be numeric.
             */

            if (!is_numeric($set_no)) {

                $skipped++;

                $error_message .=
                    'Row ' .
                    $row_number .
                    ': Set number must be numeric<br>';

                continue;
            }


            $set_no = (int)$set_no;


            /*
             |--------------------------------------------------------------------------
             | CHECK EXISTING QUESTION
             |--------------------------------------------------------------------------
             *
             * If the same question already exists:
             *
             * UPDATE:
             * - answer
             * - type
             * - set_no
             * - media_file
             *
             * Otherwise:
             *
             * INSERT new question.
             */

            $statement = $pdo->prepare("
                SELECT quiz_id
                FROM tbl_quiz
                WHERE question = ?
                LIMIT 1
            ");

            $statement->execute(array(
                $question
            ));


            $existing = $statement->fetch(
                PDO::FETCH_ASSOC
            );


            /*
             |--------------------------------------------------------------------------
             | UPDATE EXISTING QUESTION
             |--------------------------------------------------------------------------
             */

            if ($existing) {

                $quiz_id = (int)$existing['quiz_id'];


                $statement = $pdo->prepare("
                    UPDATE tbl_quiz
                    SET
                        answer = ?,
                        type = ?,
                        set_no = ?,
                        media_file = ?
                    WHERE quiz_id = ?
                ");


                $statement->execute(array(

                    $answer,

                    $type,

                    $set_no,

                    $media_file,

                    $quiz_id

                ));


                $updated++;


                /*
                 * Show which row was updated
                 * Uncomment if you want detailed messages.
                 */

                /*
                $success_message .=
                    'Row ' .
                    $row_number .
                    ': Existing question updated<br>';
                */


            } else {


                /*
                 |--------------------------------------------------------------------------
                 | INSERT NEW QUESTION
                 |--------------------------------------------------------------------------
                 */

                $statement = $pdo->prepare("
                    INSERT INTO tbl_quiz
                    (
                        question,
                        answer,
                        type,
                        set_no,
                        media_file
                    )
                    VALUES (?, ?, ?, ?, ?)
                ");


                $statement->execute(array(

                    $question,

                    $answer,

                    $type,

                    $set_no,

                    $media_file

                ));


                $created++;
            }
        }


        /*
         |--------------------------------------------------------------------------
         | CLOSE FILE
         |--------------------------------------------------------------------------
         */

        fclose($handle);


        /*
         |--------------------------------------------------------------------------
         | RESULT MESSAGE
         |--------------------------------------------------------------------------
         */

        if (
            $created > 0 ||
            $updated > 0
        ) {

            $success_message =
                'Import completed successfully.<br>' .

                'New questions created: ' .
                $created .

                '<br>' .

                'Existing questions updated: ' .
                $updated;


            if ($skipped > 0) {

                $success_message .=
                    '<br>Rows skipped: ' .
                    $skipped;
            }


        } elseif ($skipped > 0) {

            $error_message .=
                'No questions were imported.<br>';

        } else {

            $error_message .=
                'No quiz questions found in CSV.<br>';
        }
    }
}

?>


<section class="content-header">

    <div class="content-header-left">

        <h1>Upload Quiz Questions</h1>

    </div>


    <div class="content-header-right">

        <a
            href="quiz.php"
            class="btn btn-primary btn-sm">

            View All

        </a>

    </div>

</section>



<section class="content">

    <div class="row">

        <div class="col-md-12">


            <?php if($error_message): ?>

            <div class="callout callout-danger">

                <p>
                    <?php echo $error_message; ?>
                </p>

            </div>

            <?php endif; ?>


            <?php if($success_message): ?>

            <div class="callout callout-success">

                <p>
                    <?php echo $success_message; ?>
                </p>

            </div>

            <?php endif; ?>


            <div class="box box-info">

                <div class="box-header with-border">

                    <h3 class="box-title">
                        Import Quiz Questions from CSV
                    </h3>

                </div>


                <form
                    class="form-horizontal"
                    action=""
                    method="post"
                    enctype="multipart/form-data">


                    <div class="box-body">


                        <!-- CSV File -->

                        <div class="form-group">

                            <label class="col-sm-2 control-label">

                                CSV File <span>*</span>

                            </label>


                            <div class="col-sm-6">

                                <input
                                    type="file"
                                    name="csv_file"
                                    class="form-control"
                                    accept=".csv"
                                    required>


                                <p class="help-block">

                                    CSV columns must contain:

                                    <strong>
                                        question, answer, type, set_no
                                    </strong>

                                    <br>

                                    <small>
                                        media_file is optional.
                                    </small>

                                </p>

                            </div>

                        </div>



                        <!-- Import Mode -->

                        <div class="form-group">

                            <label class="col-sm-2 control-label">

                                Import Mode

                            </label>


                            <div class="col-sm-8">

                                <p class="text-muted">

                                    If the question already exists,
                                    its

                                    <strong>
                                        answer, type, set number
                                        and media
                                    </strong>

                                    will be updated.

                                    <br>

                                    If the question does not exist,
                                    a

                                    <strong>
                                        new question will be created.
                                    </strong>

                                </p>

                            </div>

                        </div>


                    </div>



                    <div class="box-footer">

                        <label class="col-sm-2 control-label"></label>


                        <button
                            type="submit"
                            name="form1"
                            class="btn btn-success">

                            <i class="fa fa-upload"></i>

                            Upload CSV

                        </button>


                        <a
                            href="quiz.php"
                            class="btn btn-default">

                            Cancel

                        </a>

                    </div>


                </form>

            </div>

        </div>

    </div>

</section>


<?php require_once('footer.php'); ?>