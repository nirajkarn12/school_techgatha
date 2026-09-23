<?php require_once('header.php'); ?>

<?php

$error_message = '';
$success_message = '';

if (isset($_POST['form1'])) {

    $valid = 1;

    $set_no   = trim($_POST['set_no'] ?? '1');
    $question = trim($_POST['question'] ?? '');
    $answer   = trim($_POST['answer'] ?? '');
    $type     = trim($_POST['type'] ?? 'General');

    $media_file = null;
    $extension = '';


    /* --------------------------------
       Validate Set Number
    --------------------------------- */

    if ($set_no == '') {
        $set_no = 1;
    }

    if (!is_numeric($set_no) || (int)$set_no < 1) {

        $valid = 0;

        $error_message .=
            'Set number must be a valid number greater than 0<br>';

    } else {

        $set_no = (int)$set_no;

    }


    /* --------------------------------
       Validate Question
    --------------------------------- */

    if ($question == '') {

        $valid = 0;

        $error_message .=
            'Question can not be empty<br>';

    }


    /* --------------------------------
       Validate Answer
    --------------------------------- */

    if ($answer == '') {

        $valid = 0;

        $error_message .=
            'Answer can not be empty<br>';

    }


    /* --------------------------------
       Validate Type
    --------------------------------- */

    $allowed_types = array(
        'Gambling',
        'General',
        'Audio Visual'
    );

    if (!in_array($type, $allowed_types, true)) {

        $valid = 0;

        $error_message .=
            'Invalid question type<br>';

    }


    /* --------------------------------
       Validate Media
       Required ONLY for Audio Visual
    --------------------------------- */

    if ($type === 'Audio Visual') {

        if (
            !isset($_FILES['media_file']) ||
            $_FILES['media_file']['error'] === UPLOAD_ERR_NO_FILE
        ) {

            $valid = 0;

            $error_message .=
                'Please upload an image, audio, or video file<br>';

        } elseif (
            $_FILES['media_file']['error'] !== UPLOAD_ERR_OK
        ) {

            $valid = 0;

            $error_message .=
                'There was an error uploading the media file<br>';

        } else {

            $original_name = $_FILES['media_file']['name'];

            $extension = strtolower(
                pathinfo(
                    $original_name,
                    PATHINFO_EXTENSION
                )
            );


            /* --------------------------------
               Allowed Media Extensions
            --------------------------------- */

            $allowed_extensions = array(

                /* Images */
                'jpg',
                'jpeg',
                'png',
                'gif',
                'webp',

                /* Audio */
                'mp3',
                'wav',
                'ogg',
                'm4a',

                /* Video */
                'mp4',
                'webm',
                'mov',
                'avi'

            );


            if (!in_array(
                $extension,
                $allowed_extensions,
                true
            )) {

                $valid = 0;

                $error_message .=
                    'Invalid media file type<br>';

            }

        }

    }


    /* --------------------------------
       Upload Media
       ONLY for Audio Visual
    --------------------------------- */

    if (
        $valid == 1 &&
        $type === 'Audio Visual'
    ) {

        $upload_dir = 'uploads/quiz/';


        /* Create directory if not exists */

        if (!is_dir($upload_dir)) {

            if (!mkdir(
                $upload_dir,
                0777,
                true
            )) {

                $valid = 0;

                $error_message .=
                    'Unable to create upload directory<br>';

            }

        }


        if ($valid == 1) {

            $new_file_name =
                'quiz_' .
                time() .
                '_' .
                uniqid() .
                '.' .
                $extension;


            $destination =
                $upload_dir .
                $new_file_name;


            if (
                move_uploaded_file(
                    $_FILES['media_file']['tmp_name'],
                    $destination
                )
            ) {

                $media_file = $destination;

            } else {

                $valid = 0;

                $error_message .=
                    'Failed to upload media file<br>';

            }

        }

    }


    /* --------------------------------
       Insert Question
    --------------------------------- */

    if ($valid == 1) {

        $statement = $pdo->prepare("

            INSERT INTO tbl_quiz
            (
                set_no,
                question,
                answer,
                type,
                media_file
            )

            VALUES (?, ?, ?, ?, ?)

        ");


        $statement->execute(array(

            $set_no,
            $question,
            $answer,
            $type,
            $media_file

        ));


        header('location: quiz.php?success=1');

        exit;

    }

}

?>


<!-- =========================================
     PAGE HEADER
========================================= -->

<section class="content-header">

    <div class="content-header-left">

        <h1>Add Quiz Question</h1>

    </div>


    <div class="content-header-right">

        <a
            href="quiz.php"
            class="btn btn-primary btn-sm">

            View All

        </a>

    </div>

</section>


<!-- =========================================
     CONTENT
========================================= -->

<section class="content">

    <div class="row">

        <div class="col-md-12">


            <!-- Error -->

            <?php if (!empty($error_message)): ?>

                <div class="callout callout-danger">

                    <p>

                        <?php
                        echo $error_message;
                        ?>

                    </p>

                </div>

            <?php endif; ?>


            <!-- Success -->

            <?php if (!empty($success_message)): ?>

                <div class="callout callout-success">

                    <p>

                        <?php
                        echo $success_message;
                        ?>

                    </p>

                </div>

            <?php endif; ?>


            <!-- FORM -->

            <form
                class="form-horizontal"
                action=""
                method="post"
                enctype="multipart/form-data">


                <div class="box box-info">

                    <div class="box-body">


                        <!-- =================================
                             SET NUMBER
                        ================================== -->

                        <div class="form-group">

                            <label class="col-sm-3 control-label">

                                Set No. <span>*</span>

                            </label>


                            <div class="col-sm-4">

                                <input
                                    type="number"
                                    min="1"
                                    step="1"
                                    name="set_no"
                                    class="form-control"
                                    required
                                    value="<?php

                                    echo isset($_POST['set_no'])
                                        ? htmlspecialchars(
                                            $_POST['set_no'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        )
                                        : '1';

                                    ?>">


                                <p class="help-block">

                                    Example: 1, 2, 3...

                                </p>

                            </div>

                        </div>


                        <!-- =================================
                             QUESTION
                        ================================== -->

                        <div class="form-group">

                            <label class="col-sm-3 control-label">

                                Question <span>*</span>

                            </label>


                            <div class="col-sm-8">

                                <textarea
                                    name="question"
                                    class="form-control"
                                    rows="4"
                                    placeholder="Enter quiz question"
                                    required><?php

                                    echo isset($_POST['question'])
                                        ? htmlspecialchars(
                                            $_POST['question'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        )
                                        : '';

                                    ?></textarea>

                            </div>

                        </div>


                        <!-- =================================
                             ANSWER
                        ================================== -->

                        <div class="form-group">

                            <label class="col-sm-3 control-label">

                                Answer <span>*</span>

                            </label>


                            <div class="col-sm-6">

                                <input
                                    type="text"
                                    name="answer"
                                    class="form-control"
                                    autocomplete="off"
                                    placeholder="Enter correct answer"
                                    required
                                    value="<?php

                                    echo isset($_POST['answer'])
                                        ? htmlspecialchars(
                                            $_POST['answer'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        )
                                        : '';

                                    ?>">

                            </div>

                        </div>


                        <!-- =================================
                             TYPE
                        ================================== -->

                        <div class="form-group">

                            <label class="col-sm-3 control-label">

                                Type <span>*</span>

                            </label>


                            <div class="col-sm-4">

                                <select
                                    name="type"
                                    id="quiz_type"
                                    class="form-control"
                                    required>


                                    <!-- General -->

                                    <option
                                        value="General"
                                        <?php

                                        echo (
                                            !isset($_POST['type']) ||
                                            $_POST['type'] == 'General'
                                        )
                                            ? 'selected'
                                            : '';

                                        ?>>

                                        General

                                    </option>


                                    <!-- Gambling -->

                                    <option
                                        value="Gambling"
                                        <?php

                                        echo (
                                            isset($_POST['type']) &&
                                            $_POST['type'] == 'Gambling'
                                        )
                                            ? 'selected'
                                            : '';

                                        ?>>

                                        Gambling

                                    </option>


                                    <!-- Audio Visual -->

                                    <option
                                        value="Audio Visual"
                                        <?php

                                        echo (
                                            isset($_POST['type']) &&
                                            $_POST['type'] == 'Audio Visual'
                                        )
                                            ? 'selected'
                                            : '';

                                        ?>>

                                        Audio Visual

                                    </option>


                                </select>

                            </div>

                        </div>


                        <!-- =================================
                             MEDIA UPLOAD
                             HIDDEN BY DEFAULT
                        ================================== -->

                        <div
                            class="form-group"
                            id="media_upload_box"
                            style="display:none;">


                            <label class="col-sm-3 control-label">

                                Media File <span>*</span>

                            </label>


                            <div class="col-sm-4">

                                <input
                                    type="file"
                                    name="media_file"
                                    id="media_file"
                                    accept="image/*,audio/*,video/*">


                                <p class="help-block">

                                    Upload image, audio or video file.

                                </p>

                            </div>

                        </div>


                        <!-- =================================
                             SUBMIT
                        ================================== -->

                        <div class="form-group">

                            <label class="col-sm-3 control-label"></label>


                            <div class="col-sm-6">

                                <button
                                    type="submit"
                                    class="btn btn-success pull-left"
                                    name="form1">

                                    <i class="fa fa-save"></i>

                                    Submit

                                </button>

                            </div>

                        </div>


                    </div>

                </div>

            </form>

        </div>

    </div>

</section>


<!-- =========================================
     JAVASCRIPT
========================================= -->

<script>
$(document).ready(function () {

    function toggleMediaUpload() {

        var selectedType = $('#quiz_type').val();

        if (selectedType === 'Audio Visual') {

            // Show upload field
            $('#media_upload_box').show();

            // Make upload required
            $('#media_file').prop('required', true);

        } else {

            // Hide upload field
            $('#media_upload_box').hide();

            // Remove required
            $('#media_file').prop('required', false);

            // Clear selected file
            $('#media_file').val('');

        }
    }


    // When Type changes
    $('#quiz_type').on('change', function () {

        toggleMediaUpload();

    });


    // Run on page load
    toggleMediaUpload();

});
</script>


<?php require_once('footer.php'); ?>