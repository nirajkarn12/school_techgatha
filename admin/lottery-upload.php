<?php require_once('header.php'); ?>

<?php

if (isset($_POST['form1'])) {

    $valid = 1;

    /*
     * Check uploaded file
     */

    if (
        !isset($_FILES['csv_file']) ||
        $_FILES['csv_file']['error'] != UPLOAD_ERR_OK
    ) {

        $valid = 0;
        $error_message .= 'Please select a CSV file<br>';

    } else {

        $file_name = $_FILES['csv_file']['name'];
        $file_tmp = $_FILES['csv_file']['tmp_name'];

        $extension = strtolower(
            pathinfo($file_name, PATHINFO_EXTENSION)
        );

        if ($extension !== 'csv') {

            $valid = 0;
            $error_message .= 'Please upload a valid CSV file<br>';
        }
    }


    /*
     * Process CSV
     */

    if ($valid == 1) {

        $handle = fopen($file_tmp, 'r');

        if ($handle === false) {

            $valid = 0;
            $error_message .= 'Unable to read CSV file<br>';

        } else {

            $row_number = 0;
            $inserted = 0;
            $updated = 0;
            $skipped = 0;
            $errors = array();


            /*
             * Detect CSV delimiter automatically
             *
             * Supports:
             * 1. Comma ,
             * 2. TAB \t
             * 3. Semicolon ;
             */

            $first_line = fgets($handle);

            if ($first_line === false) {

                $valid = 0;
                $error_message .= 'CSV file is empty<br>';

            } else {

                /*
                 * Remove BOM before detecting delimiter
                 */

                $first_line = preg_replace(
                    '/^\xEF\xBB\xBF/',
                    '',
                    $first_line
                );


                /*
                 * Detect delimiter
                 */

                if (substr_count($first_line, "\t") > 0) {

                    $delimiter = "\t";

                } elseif (substr_count($first_line, ';') > 0) {

                    $delimiter = ';';

                } else {

                    $delimiter = ',';
                }


                /*
                 * Go back to beginning of file
                 */

                rewind($handle);


                /*
                 * Read CSV header
                 */

                $header = fgetcsv(
                    $handle,
                    0,
                    $delimiter
                );


                if ($header === false) {

                    $valid = 0;
                    $error_message .= 'CSV file is empty<br>';

                } else {

                    /*
                     * Remove BOM from first column
                     */

                    if (isset($header[0])) {

                        $header[0] = preg_replace(
                            '/^\xEF\xBB\xBF/',
                            '',
                            $header[0]
                        );
                    }


                    /*
                     * Normalize headers
                     */

                    $header = array_map(function ($value) {

                        return strtolower(trim($value));

                    }, $header);


                    /*
                     * Required columns
                     *
                     * ONLY ticket_number is required.
                     */

                    $required_columns = array(
                        'ticket_number'
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
                                htmlspecialchars($required_column) .
                                '<br>';
                        }
                    }
                }
            }


            /*
             * Continue if CSV is valid
             */

            if ($valid == 1) {


                /*
                 * Check ticket by ticket_number
                 */

                $check_statement = $pdo->prepare("
                    SELECT ticket_id
                    FROM tbl_lottery_ticket
                    WHERE ticket_number = ?
                    LIMIT 1
                ");


                /*
                 * Update existing ticket
                 */

                $update_statement = $pdo->prepare("
                    UPDATE tbl_lottery_ticket
                    SET
                        child_name = ?,
                        class = ?,
                        contact_number = ?,
                        ticket_price = ?,
                        prize = ?,
                        status = ?
                    WHERE ticket_number = ?
                ");


                /*
                 * Insert new ticket
                 */

                $insert_statement = $pdo->prepare("
                    INSERT INTO tbl_lottery_ticket
                    (
                        ticket_number,
                        child_name,
                        class,
                        contact_number,
                        ticket_price,
                        prize,
                        status
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");


                /*
                 * Read CSV rows
                 *
                 * Use the SAME delimiter detected above.
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

                    if (
                        count($data) == 1 &&
                        trim($data[0]) == ''
                    ) {

                        continue;
                    }


                    /*
                     * Create associative row
                     */

                    $row = array();

                    foreach ($header as $index => $column) {

                        $row[$column] =
                            isset($data[$index])
                            ? trim($data[$index])
                            : '';
                    }


                    /*
                     * Get values safely
                     *
                     * Only ticket_number is required.
                     */

                    $ticket_number = isset($row['ticket_number'])
                        ? trim($row['ticket_number'])
                        : '';

                    $child_name = isset($row['child_name'])
                        ? trim($row['child_name'])
                        : '';

                    $class = isset($row['class'])
                        ? trim($row['class'])
                        : '';

                    $contact_number = isset($row['contact_number'])
                        ? trim($row['contact_number'])
                        : '';

                    $ticket_price = isset($row['ticket_price'])
                        ? trim($row['ticket_price'])
                        : '';

                    $prize = isset($row['prize'])
                        ? trim($row['prize'])
                        : '';

                    $status = isset($row['status'])
                        ? trim($row['status'])
                        : '';


                    /*
                     * Validate ticket number
                     *
                     * ONLY ticket_number is required.
                     */

                    if ($ticket_number == '') {

                        $errors[] =
                            'Row ' .
                            ($row_number + 1) .
                            ': Ticket number is empty';

                        $skipped++;

                        continue;
                    }


                    /*
                     * Ticket price
                     *
                     * Empty = 0
                     */

                    if ($ticket_price == '') {

                        $ticket_price = 0;
                    }


                    if (
                        !is_numeric($ticket_price) ||
                        $ticket_price < 0
                    ) {

                        $errors[] =
                            'Row ' .
                            ($row_number + 1) .
                            ': Invalid ticket price';

                        $skipped++;

                        continue;
                    }


                    /*
                     * Status
                     *
                     * Empty/invalid = Available
                     */

                    $allowed_statuses = array(
                        'Available',
                        'Sold',
                        'Winner',
                        'Cancelled'
                    );


                    if (!in_array(
                        $status,
                        $allowed_statuses,
                        true
                    )) {

                        $status = 'Available';
                    }


                    /*
                     * Check whether ticket_number already exists
                     */

                    $check_statement->execute(
                        array($ticket_number)
                    );


                    $existing_ticket =
                        $check_statement->fetch(
                            PDO::FETCH_ASSOC
                        );


                    try {

                        /*
                         * Existing ticket
                         *
                         * UPDATE
                         */

                        if ($existing_ticket) {

                            $update_statement->execute(
                                array(
                                    $child_name,
                                    $class,
                                    $contact_number,
                                    $ticket_price,
                                    $prize,
                                    $status,
                                    $ticket_number
                                )
                            );

                            $updated++;


                        /*
                         * New ticket
                         *
                         * INSERT
                         */

                        } else {

                            $insert_statement->execute(
                                array(
                                    $ticket_number,
                                    $child_name,
                                    $class,
                                    $contact_number,
                                    $ticket_price,
                                    $prize,
                                    $status
                                )
                            );

                            $inserted++;
                        }


                    } catch (PDOException $e) {

                        $errors[] =
                            'Row ' .
                            ($row_number + 1) .
                            ': Could not save ticket';

                        $skipped++;
                    }
                }


                fclose($handle);


                /*
                 * Result message
                 */

                if (
                    $inserted > 0 ||
                    $updated > 0
                ) {

                    $success_message = '';


                    if ($inserted > 0) {

                        $success_message .=
                            $inserted .
                            ' new lottery ticket(s) imported successfully.';
                    }


                    if ($updated > 0) {

                        if ($success_message != '') {

                            $success_message .= '<br>';
                        }

                        $success_message .=
                            $updated .
                            ' existing lottery ticket(s) updated successfully.';
                    }


                    if ($skipped > 0) {

                        $success_message .=
                            '<br>' .
                            $skipped .
                            ' row(s) skipped.';
                    }


                } else {

                    $error_message .=
                        'No lottery tickets were imported or updated.<br>';
                }
            }
        }
    }
}

?>

<section class="content-header">

    <div class="content-header-left">

        <h1>Upload Lottery Tickets</h1>

    </div>

    <div class="content-header-right">

        <a
            href="lottery.php"
            class="btn btn-primary btn-sm"
        >
            View All
        </a>

    </div>

</section>


<section class="content">

    <div class="row">

        <div class="col-md-12">


            <?php if ($error_message): ?>

            <div class="callout callout-danger">

                <p>
                    <?php echo $error_message; ?>
                </p>

                <?php if (!empty($errors)): ?>

                    <hr>

                    <?php foreach ($errors as $error): ?>

                        <p>
                            <?php echo $error; ?>
                        </p>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

            <?php endif; ?>


            <?php if ($success_message): ?>

            <div class="callout callout-success">

                <p>
                    <?php echo $success_message; ?>
                </p>

                <?php if (!empty($errors)): ?>

                    <hr>

                    <?php foreach ($errors as $error): ?>

                        <p>
                            <?php echo $error; ?>
                        </p>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

            <?php endif; ?>


            <form
                class="form-horizontal"
                action=""
                method="post"
                enctype="multipart/form-data"
            >

                <div class="box box-info">

                    <div class="box-header with-border">

                        <h3 class="box-title">
                            Import Lottery Tickets from CSV
                        </h3>

                    </div>


                    <div class="box-body">


                        <div class="form-group">

                            <label class="col-sm-2 control-label">
                                CSV File <span>*</span>
                            </label>

                            <div
                                class="col-sm-6"
                                style="padding-top:5px"
                            >

                                <input
                                    type="file"
                                    name="csv_file"
                                    accept=".csv,text/csv"
                                    required
                                >

                                <p class="help-block">
                                    Only CSV files are allowed.
                                </p>

                            </div>

                        </div>


                        <div class="form-group">

                            <label class="col-sm-2 control-label">
                                CSV Format
                            </label>

                            <div class="col-sm-9">

                                <p>
                                    Only
                                    <strong>ticket_number</strong>
                                    is required.
                                </p>

                                <p>
                                    These columns are optional:
                                </p>

                                <code>
                                    ticket_number, child_name, class, contact_number, ticket_price, prize, status
                                </code>

                                <br>
                                <br>

                                <p>
                                    The importer automatically detects
                                    comma, TAB, or semicolon separated CSV files.
                                </p>

                                <p class="help-block">

                                    <strong>ticket_number</strong>
                                    must be present and must have a value.

                                    All other fields can be empty or omitted.

                                </p>

                                <p class="help-block">

                                    Existing tickets are matched using
                                    <strong>ticket_number</strong>.

                                    If the ticket number already exists,
                                    the ticket will be updated.

                                    Otherwise, a new ticket will be created.

                                </p>

                            </div>

                        </div>


                        <div class="form-group">

                            <label class="col-sm-2 control-label"></label>

                            <div class="col-sm-6">

                                <button
                                    type="submit"
                                    class="btn btn-success"
                                    name="form1"
                                >

                                    <i class="fa fa-upload"></i>
                                    Upload CSV

                                </button>


                                <a
                                    href="lottery.php"
                                    class="btn btn-default"
                                >
                                    Cancel
                                </a>

                            </div>

                        </div>


                    </div>

                </div>

            </form>

        </div>

    </div>

</section>


<?php require_once('footer.php'); ?>