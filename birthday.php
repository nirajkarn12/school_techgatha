<?php require_once('header.php'); ?>

<?php
ensureBirthdayTables($pdo);

$success_message = '';
$error_message = '';

/*
|--------------------------------------------------------------------------
| CSV UPLOAD
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_csv'])) {

    if (!isset($_FILES['birthday_csv']) || $_FILES['birthday_csv']['error'] !== UPLOAD_ERR_OK) {
        $error_message = 'Please select a valid CSV file.';
    } else {

        $file = $_FILES['birthday_csv'];

        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($extension !== 'csv') {
            $error_message = 'Only CSV files are allowed.';
        } else {

            $handle = fopen($file['tmp_name'], 'r');

            if ($handle === false) {
                $error_message = 'Unable to read the CSV file.';
            } else {

                /*
                |--------------------------------------------------------------------------
                | Find Default Birthday Card Template
                |--------------------------------------------------------------------------
                */
                $templateStmt = $pdo->prepare("
                    SELECT id
                    FROM tbl_birthday_template
                    WHERE LOWER(TRIM(title)) = LOWER(TRIM(?))
                    LIMIT 1
                ");
                $templateStmt->execute(['Default Birthday Card']);
                $defaultTemplate = $templateStmt->fetch(PDO::FETCH_ASSOC);

                if (!$defaultTemplate) {
                    fclose($handle);
                    $error_message = 'Default Birthday Card template was not found. Please create the template first.';
                } else {

                    $templateId = (int)$defaultTemplate['id'];

                    // Read CSV header
                    $header = fgetcsv($handle);

                    if (!$header) {
                        fclose($handle);
                        $error_message = 'The CSV file is empty.';
                    } else {

                        // Remove BOM from first column if present
                        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

                        // Normalize header names
                        $header = array_map(function ($value) {
                            return strtolower(trim($value));
                        }, $header);

                        $requiredColumns = [
                            'student_name',
                            'class',
                            'birthday',
                            'template',
                            'status'
                        ];

                        $missingColumns = [];

                        foreach ($requiredColumns as $column) {
                            if (!in_array($column, $header, true)) {
                                $missingColumns[] = $column;
                            }
                        }

                        if (!empty($missingColumns)) {
                            fclose($handle);

                            $error_message = 'Missing CSV column(s): ' . implode(', ', $missingColumns);
                        } else {

                            // Get column positions
                            $studentNameIndex = array_search('student_name', $header, true);
                            $classIndex       = array_search('class', $header, true);
                            $birthdayIndex    = array_search('birthday', $header, true);
                            $templateIndex    = array_search('template', $header, true);
                            $statusIndex      = array_search('status', $header, true);

                            $imported = 0;
                            $failed = 0;
                            $errors = [];
                            $rowNumber = 1;

                            try {

                                $pdo->beginTransaction();

                                /*
                                |--------------------------------------------------------------------------
                                | Insert Statement
                                |--------------------------------------------------------------------------
                                */
                                $insertStmt = $pdo->prepare("
                                    INSERT INTO tbl_birthday_student
                                    (
                                        name,
                                        class_name,
                                        birthday_date,
                                        template_id,
                                        status
                                    )
                                    VALUES
                                    (
                                        ?,
                                        ?,
                                        ?,
                                        ?,
                                        ?
                                    )
                                ");

                                while (($data = fgetcsv($handle)) !== false) {

                                    $rowNumber++;

                                    // Skip completely empty rows
                                    if (
                                        count($data) === 1 &&
                                        trim($data[0]) === ''
                                    ) {
                                        continue;
                                    }

                                    $studentName = trim($data[$studentNameIndex] ?? '');
                                    $className   = trim($data[$classIndex] ?? '');
                                    $birthday    = trim($data[$birthdayIndex] ?? '');
                                    $template    = trim($data[$templateIndex] ?? '');
                                    $status      = trim($data[$statusIndex] ?? '');

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Validate Student Name
                                    |--------------------------------------------------------------------------
                                    */
                                    if ($studentName === '') {
                                        $failed++;
                                        $errors[] = "Row {$rowNumber}: Student name is required.";
                                        continue;
                                    }

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Validate Class
                                    |--------------------------------------------------------------------------
                                    */
                                    if ($className === '') {
                                        $failed++;
                                        $errors[] = "Row {$rowNumber}: Class is required.";
                                        continue;
                                    }

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Validate Birthday
                                    |--------------------------------------------------------------------------
                                    */
                                    if ($birthday === '') {
                                        $failed++;
                                        $errors[] = "Row {$rowNumber}: Birthday is required.";
                                        continue;
                                    }

                                    $dateObject = DateTime::createFromFormat('Y-m-d', $birthday);

                                    if (
                                        !$dateObject ||
                                        $dateObject->format('Y-m-d') !== $birthday
                                    ) {
                                        $failed++;
                                        $errors[] = "Row {$rowNumber}: Invalid birthday '{$birthday}'. Use YYYY-MM-DD.";
                                        continue;
                                    }

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Template MUST be Default Birthday Card
                                    |--------------------------------------------------------------------------
                                    */
                                    if (strcasecmp($template, 'Default Birthday Card') !== 0) {
                                        $failed++;
                                        $errors[] = "Row {$rowNumber}: Template must be 'Default Birthday Card'.";
                                        continue;
                                    }

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Status MUST be Active
                                    |--------------------------------------------------------------------------
                                    */
                                    if (strcasecmp($status, 'Active') !== 0) {
                                        $failed++;
                                        $errors[] = "Row {$rowNumber}: Status must be 'Active'.";
                                        continue;
                                    }

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Insert
                                    |--------------------------------------------------------------------------
                                    */
                                    $insertStmt->execute([
                                        $studentName,
                                        $className,
                                        $birthday,
                                        $templateId,
                                        'Active'
                                    ]);

                                    $imported++;
                                }

                                fclose($handle);

                                $pdo->commit();

                                if ($imported > 0) {
                                    $success_message = "{$imported} birthday student(s) imported successfully.";

                                    if ($failed > 0) {
                                        $success_message .= " {$failed} row(s) were skipped.";
                                    }
                                } else {
                                    $error_message = 'No students were imported.';

                                    if (!empty($errors)) {
                                        $error_message .= ' Please check the errors below.';
                                    }
                                }

                            } catch (Exception $e) {

                                if ($pdo->inTransaction()) {
                                    $pdo->rollBack();
                                }

                                fclose($handle);

                                $error_message = 'CSV import failed: ' . $e->getMessage();
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Display Row Errors
                            |--------------------------------------------------------------------------
                            */
                            if (!empty($errors)) {
                                $error_message .= '<br><br><strong>Import Errors:</strong><br>';
                                $error_message .= implode('<br>', array_map('htmlspecialchars', $errors));
                            }
                        }
                    }
                }
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| Success / Delete Messages
|--------------------------------------------------------------------------
*/
if (isset($_GET['success'])) {
    $success_message = 'Birthday student is added successfully!';
} elseif (isset($_GET['updated'])) {
    $success_message = 'Birthday student is updated successfully!';
} elseif (isset($_GET['deleted'])) {
    $success_message = 'Birthday student is deleted successfully!';
}
?>

<section class="content-header">

    <div class="content-header-left">
        <h1>Birthday Students</h1>
    </div>

    <div class="content-header-right">

        <!-- CSV Upload Button -->
        <button
            type="button"
            class="btn btn-success btn-sm"
            data-toggle="modal"
            data-target="#csvUploadModal"
        >
            <i class="fa fa-upload"></i> Upload CSV
        </button>

        <a href="birthday-add.php" class="btn btn-primary btn-sm">
            Add Student
        </a>

    </div>

</section>


<section class="content">

    <div class="row">

        <div class="col-md-12">

            <?php if ($error_message): ?>
                <div class="callout callout-danger">
                    <p><?php echo $error_message; ?></p>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="callout callout-success">
                    <p><?php echo htmlspecialchars($success_message); ?></p>
                </div>
            <?php endif; ?>


            <div class="box box-info">

                <div class="box-body table-responsive">

                    <table
                        id="example1"
                        class="table table-bordered table-hover table-striped"
                    >

                        <thead>

                            <tr>
                                <th width="30">#</th>
                                <th width="80">Student Photo</th>
                                <th>Name</th>
                                <th>Class</th>
                                <th>Birthday</th>
                                <th>Template</th>
                                <th>Status</th>
                                <th width="210">Action</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php

                            $i = 0;

                            $statement = $pdo->prepare("
                                SELECT
                                    s.*,
                                    t.title AS template_title
                                FROM tbl_birthday_student s
                                LEFT JOIN tbl_birthday_template t
                                    ON t.id = s.template_id
                                ORDER BY
                                    s.sort_order ASC,
                                    s.id DESC
                            ");

                            $statement->execute();

                            foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {

                                $i++;

                            ?>

                                <tr>

                                    <td>
                                        <?php echo $i; ?>
                                    </td>

                                    <td>

                                        <?php if (
                                            !empty($row['student_image']) &&
                                            is_file(adminUploadsPath($row['student_image']))
                                        ): ?>

                                            <img
                                                src="<?php echo htmlspecialchars(
                                                    adminUploadUrl($row['student_image'])
                                                ); ?>"
                                                style="
                                                    width:58px;
                                                    height:66px;
                                                    object-fit:cover;
                                                    background:#f2f2f2;
                                                    padding:4px;
                                                "
                                            >

                                        <?php else: ?>

                                            <span class="text-muted">
                                                Missing
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['name']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['class_name']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['birthday_date']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['template_title'] ?? ''); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </td>

                                    <td>

                                        <a
                                            href="birthday-edit.php?id=<?php echo (int)$row['id']; ?>"
                                            class="btn btn-primary btn-xs"
                                        >
                                            Edit
                                        </a>

                                        <a
                                            href="birthday-generate.php?id=<?php echo (int)$row['id']; ?>"
                                            class="btn btn-success btn-xs"
                                        >
                                            Generate
                                        </a>

                                        <?php if (!empty($row['generated_image'])): ?>

                                            <a
                                                href="birthday-download.php?id=<?php echo (int)$row['id']; ?>"
                                                class="btn btn-info btn-xs"
                                            >
                                                Download
                                            </a>

                                        <?php endif; ?>

                                        <a
                                            href="#"
                                            class="btn btn-danger btn-xs"
                                            data-href="birthday-delete.php?id=<?php echo (int)$row['id']; ?>"
                                            data-toggle="modal"
                                            data-target="#confirm-delete"
                                        >
                                            Delete
                                        </a>

                                    </td>

                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</section>


<!--
|--------------------------------------------------------------------------
| CSV UPLOAD MODAL
|--------------------------------------------------------------------------
-->

<div
    class="modal fade"
    id="csvUploadModal"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="post"
                enctype="multipart/form-data"
            >

                <div class="modal-header">

                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                    >
                        &times;
                    </button>

                    <h4 class="modal-title">
                        Upload Birthday Students CSV
                    </h4>

                </div>


                <div class="modal-body">

                    <div class="alert alert-info">

                        <strong>CSV Format:</strong>

                        <br><br>

                        Your CSV must contain these columns:

                        <br>

                        <code>
                            student_name,class,birthday,template,status
                        </code>

                        <br><br>

                        <strong>Example:</strong>

                        <br>

                        <code>
                            student_name,class,birthday,template,status
                        </code>

                        <br>

                        <code>
                            John Doe,Class 5,2012-08-15,Default Birthday Card,Active
                        </code>

                        <br>

                        <code>
                            Jane Smith,Class 6,2011-09-20,Default Birthday Card,Active
                        </code>

                        <br><br>

                        <strong>Important:</strong>

                        <ul style="padding-left:20px;margin-bottom:0;">

                            <li>
                                Birthday format must be
                                <strong>YYYY-MM-DD</strong>
                            </li>

                            <li>
                                Template must be
                                <strong>Default Birthday Card</strong>
                            </li>

                            <li>
                                Status must be
                                <strong>Active</strong>
                            </li>

                        </ul>

                    </div>


                    <div class="form-group">

                        <label>
                            Select CSV File
                        </label>

                        <input
                            type="file"
                            name="birthday_csv"
                            class="form-control"
                            accept=".csv"
                            required
                        >

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-default"
                        data-dismiss="modal"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        name="upload_csv"
                        value="1"
                        class="btn btn-success"
                    >
                        <i class="fa fa-upload"></i>
                        Upload CSV
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!--
|--------------------------------------------------------------------------
| DELETE MODAL
|--------------------------------------------------------------------------
-->

<div
    class="modal fade"
    id="confirm-delete"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                >
                    &times;
                </button>

                <h4 class="modal-title">
                    Delete Confirmation
                </h4>

            </div>

            <div class="modal-body">

                <p>
                    Are you sure want to delete this birthday student?
                </p>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-default"
                    data-dismiss="modal"
                >
                    Cancel
                </button>

                <a
                    class="btn btn-danger btn-ok"
                >
                    Delete
                </a>

            </div>

        </div>

    </div>

</div>


<?php require_once('footer.php'); ?>