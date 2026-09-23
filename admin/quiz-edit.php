<?php require_once('header.php'); ?>

<?php

/* =========================================================
   CHECK QUIZ ID
========================================================= */

if (!isset($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) {

    header('location: quiz.php');
    exit;

}

$quizId = (int)$_REQUEST['id'];


/* =========================================================
   CHECK QUIZ EXISTS
========================================================= */

$statement = $pdo->prepare("
    SELECT *
    FROM tbl_quiz
    WHERE quiz_id = ?
");

$statement->execute([$quizId]);

$quiz = $statement->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {

    header('location: quiz.php');
    exit;

}


/* =========================================================
   DEFAULT VALUES
========================================================= */

$set_no = $quiz['set_no'] ?? 1;
$question = $quiz['question'] ?? '';
$answer = $quiz['answer'] ?? '';
$type = $quiz['type'] ?? 'General';
$media_file = $quiz['media_file'] ?? null;


/* =========================================================
   UPDATE QUIZ
========================================================= */

if (isset($_POST['form1'])) {

    $valid = 1;

    /* -----------------------------------------
       Get Form Values
    ----------------------------------------- */

    $set_no = trim($_POST['set_no'] ?? '1');
    $question = trim($_POST['question'] ?? '');
    $answer = trim($_POST['answer'] ?? '');
    $type = trim($_POST['type'] ?? 'General');


    /* -----------------------------------------
       Validate Set Number
    ----------------------------------------- */

    if ($set_no === '') {

        $set_no = 1;

    }

    if (!is_numeric($set_no) || (int)$set_no < 1) {

        $valid = 0;

        $error_message .=
            'Set number must be a valid number greater than 0<br>';

    } else {

        $set_no = (int)$set_no;

    }


    /* -----------------------------------------
       Validate Question
    ----------------------------------------- */

    if ($question === '') {

        $valid = 0;

        $error_message .=
            'Question can not be empty<br>';

    }


    /* -----------------------------------------
       Validate Answer
    ----------------------------------------- */

    if ($answer === '') {

        $valid = 0;

        $error_message .=
            'Answer can not be empty<br>';

    }


    /* -----------------------------------------
       Validate Type
    ----------------------------------------- */

    $allowed_types = [
        'Gambling',
        'General',
        'Audio Visual'
    ];

    if (!in_array($type, $allowed_types, true)) {

        $valid = 0;

        $error_message .=
            'Invalid question type<br>';

    }


    /* -----------------------------------------
       Existing Media
    ----------------------------------------- */

    $existing_media = $quiz['media_file'] ?? null;

    $media_file = $existing_media;


    /* =====================================================
       REMOVE CURRENT MEDIA
    ===================================================== */

    $remove_media = (
        isset($_POST['remove_media']) &&
        $_POST['remove_media'] == '1'
    );

    if ($remove_media) {

        if (
            !empty($existing_media) &&
            file_exists($existing_media)
        ) {

            unlink($existing_media);

        }

        $media_file = null;

    }


    /* =====================================================
       AUDIO VISUAL MEDIA VALIDATION
    ===================================================== */

    if ($type === 'Audio Visual') {

        /*
         * Check whether a new file was selected.
         */

        $has_new_file = (
            isset($_FILES['media_file']) &&
            $_FILES['media_file']['error'] !== UPLOAD_ERR_NO_FILE
        );


        /*
         * Existing media is available only if
         * user did not remove it.
         */

        $has_existing_file = !empty($media_file);


        /* -----------------------------------------
           Media Required
        ----------------------------------------- */

        if (
            !$has_new_file &&
            !$has_existing_file
        ) {

            $valid = 0;

            $error_message .=
                'Please upload an image, audio, or video file<br>';

        }


        /* -----------------------------------------
           Validate New File
        ----------------------------------------- */

        if ($has_new_file) {

            if (
                $_FILES['media_file']['error'] !==
                UPLOAD_ERR_OK
            ) {

                $valid = 0;

                $error_message .=
                    'There was an error uploading the media file<br>';

            } else {

                $original_name =
                    $_FILES['media_file']['name'];

                $extension = strtolower(
                    pathinfo(
                        $original_name,
                        PATHINFO_EXTENSION
                    )
                );


                /* -----------------------------------------
                   Allowed Extensions
                ----------------------------------------- */

                $allowed_extensions = [

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

                ];


                if (
                    !in_array(
                        $extension,
                        $allowed_extensions,
                        true
                    )
                ) {

                    $valid = 0;

                    $error_message .=
                        'Invalid media file type. Allowed: JPG, JPEG, PNG, GIF, WEBP, MP3, WAV, OGG, M4A, MP4, WEBM, MOV, AVI<br>';

                }

            }

        }

    }


    /* =====================================================
       UPLOAD NEW MEDIA
    ===================================================== */

    if (
        $valid == 1 &&
        $type === 'Audio Visual' &&
        isset($_FILES['media_file']) &&
        $_FILES['media_file']['error'] === UPLOAD_ERR_OK
    ) {

        $upload_dir = 'uploads/quiz/';


        /* -----------------------------------------
           Create Directory
        ----------------------------------------- */

        if (!is_dir($upload_dir)) {

            mkdir(
                $upload_dir,
                0777,
                true
            );

        }


        /* -----------------------------------------
           Generate Unique File Name
        ----------------------------------------- */

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


        /* -----------------------------------------
           Move Uploaded File
        ----------------------------------------- */

        if (
            move_uploaded_file(
                $_FILES['media_file']['tmp_name'],
                $destination
            )
        ) {

            /*
             * Delete old media only after
             * successful new upload.
             */

            if (
                !empty($existing_media) &&
                $existing_media !== $destination &&
                file_exists($existing_media)
            ) {

                unlink($existing_media);

            }


            $media_file = $destination;

        } else {

            $valid = 0;

            $error_message .=
                'Failed to upload media file<br>';

        }

    }


    /* =====================================================
       IF TYPE IS NOT AUDIO VISUAL
       REMOVE MEDIA
    ===================================================== */

    if (
        $valid == 1 &&
        $type !== 'Audio Visual'
    ) {

        if (
            !empty($existing_media) &&
            file_exists($existing_media)
        ) {

            unlink($existing_media);

        }

        $media_file = null;

    }


    /* =====================================================
       UPDATE DATABASE
    ===================================================== */

    if ($valid == 1) {

        $statement = $pdo->prepare("
            UPDATE tbl_quiz
            SET
                set_no = ?,
                question = ?,
                answer = ?,
                type = ?,
                media_file = ?
            WHERE quiz_id = ?
        ");


        $statement->execute([

            $set_no,
            $question,
            $answer,
            $type,
            $media_file,
            $quizId

        ]);


        header('location: quiz.php?updated=1');
        exit;

    }

}

?>

<section class="content-header">

    <div class="content-header-left">

        <h1>Edit Quiz Question</h1>

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


            <?php if (!empty($error_message)): ?>

                <div class="callout callout-danger">

                    <p>
                        <?php echo $error_message; ?>
                    </p>

                </div>

            <?php endif; ?>


            <?php if (!empty($success_message)): ?>

                <div class="callout callout-success">

                    <p>
                        <?php echo $success_message; ?>
                    </p>

                </div>

            <?php endif; ?>


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

                            <label class="col-sm-2 control-label">

                                Set No. <span>*</span>

                            </label>


                            <div class="col-sm-3">

                                <input
                                    type="number"
                                    min="1"
                                    class="form-control"
                                    name="set_no"
                                    value="<?php
                                    echo htmlspecialchars(
                                        $set_no,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>"
                                    required>

                                <p class="help-block">

                                    Example: 1, 2, 3...

                                </p>

                            </div>

                        </div>


                        <!-- =================================
                             QUESTION
                        ================================== -->

                        <div class="form-group">

                            <label class="col-sm-2 control-label">

                                Question <span>*</span>

                            </label>


                            <div class="col-sm-8">

                                <textarea
                                    class="form-control"
                                    name="question"
                                    rows="4"
                                    placeholder="Enter quiz question"
                                    required><?php

                                    echo htmlspecialchars(
                                        $question,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                ?></textarea>

                            </div>

                        </div>


                        <!-- =================================
                             ANSWER
                        ================================== -->

                        <div class="form-group">

                            <label class="col-sm-2 control-label">

                                Answer <span>*</span>

                            </label>


                            <div class="col-sm-6">

                                <input
                                    type="text"
                                    autocomplete="off"
                                    class="form-control"
                                    name="answer"
                                    placeholder="Enter correct answer"
                                    value="<?php

                                    echo htmlspecialchars(
                                        $answer,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    ?>"
                                    required>

                            </div>

                        </div>


                        <!-- =================================
                             TYPE
                        ================================== -->

                        <div class="form-group">

                            <label class="col-sm-2 control-label">

                                Type <span>*</span>

                            </label>


                            <div class="col-sm-4">

                                <select
                                    name="type"
                                    id="quiz_type"
                                    class="form-control"
                                    required>


                                    <option
                                        value="General"
                                        <?php

                                        echo (
                                            $type === 'General'
                                        )
                                        ? 'selected'
                                        : '';

                                        ?>>

                                        General

                                    </option>


                                    <option
                                        value="Gambling"
                                        <?php

                                        echo (
                                            $type === 'Gambling'
                                        )
                                        ? 'selected'
                                        : '';

                                        ?>>

                                        Gambling

                                    </option>


                                    <option
                                        value="Audio Visual"
                                        <?php

                                        echo (
                                            $type === 'Audio Visual'
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
                             CURRENT MEDIA
                        ================================== -->

                        <?php if (!empty($media_file)): ?>

                            <div
                                class="form-group"
                                id="existing_media_box">

                                <label class="col-sm-2 control-label">

                                    Current Media

                                </label>


                                <div class="col-sm-8">

                                    <?php

                                    $media_extension = strtolower(
                                        pathinfo(
                                            $media_file,
                                            PATHINFO_EXTENSION
                                        )
                                    );


                                    $image_extensions = [

                                        'jpg',
                                        'jpeg',
                                        'png',
                                        'gif',
                                        'webp'

                                    ];


                                    $audio_extensions = [

                                        'mp3',
                                        'wav',
                                        'ogg',
                                        'm4a'

                                    ];


                                    $video_extensions = [

                                        'mp4',
                                        'webm',
                                        'mov',
                                        'avi'

                                    ];


                                    /* =================================
                                       IMAGE
                                    ================================== */

                                    if (
                                        in_array(
                                            $media_extension,
                                            $image_extensions,
                                            true
                                        )
                                    ) {

                                    ?>

                                        <div>

                                            <img
                                                src="<?php
                                                echo htmlspecialchars(
                                                    $media_file,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                                ?>"
                                                style="
                                                    max-width:300px;
                                                    max-height:200px;
                                                    border:1px solid #ddd;
                                                    padding:5px;
                                                "
                                                alt="Quiz Media">

                                        </div>

                                    <?php

                                    }


                                    /* =================================
                                       AUDIO
                                    ================================== */

                                    elseif (
                                        in_array(
                                            $media_extension,
                                            $audio_extensions,
                                            true
                                        )
                                    ) {

                                    ?>

                                        <audio controls>

                                            <source
                                                src="<?php
                                                echo htmlspecialchars(
                                                    $media_file,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                                ?>">

                                            Your browser does not support
                                            audio playback.

                                        </audio>

                                    <?php

                                    }


                                    /* =================================
                                       VIDEO
                                    ================================== */

                                    elseif (
                                        in_array(
                                            $media_extension,
                                            $video_extensions,
                                            true
                                        )
                                    ) {

                                    ?>

                                        <video
                                            controls
                                            style="
                                                max-width:400px;
                                                max-height:250px;
                                            ">

                                            <source
                                                src="<?php
                                                echo htmlspecialchars(
                                                    $media_file,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                                ?>">

                                            Your browser does not support
                                            video playback.

                                        </video>

                                    <?php

                                    }

                                    ?>


                                    <br>


                                    <!-- REMOVE MEDIA -->

                                    <label
                                        style="
                                            margin-top:10px;
                                            font-weight:normal;
                                        ">

                                        <input
                                            type="checkbox"
                                            name="remove_media"
                                            id="remove_media"
                                            value="1">

                                        Remove current media

                                    </label>

                                </div>

                            </div>

                        <?php endif; ?>


                        <!-- =================================
                             MEDIA UPLOAD
                             ONLY AUDIO VISUAL
                        ================================== -->

                        <div
                            class="form-group"
                            id="media_upload_box"
                            style="<?php

                            echo (
                                $type === 'Audio Visual'
                            )
                            ? ''
                            : 'display:none;';

                            ?>">


                            <label class="col-sm-2 control-label">

                                <span id="media_label">

                                    <?php

                                    if (!empty($media_file)) {

                                        echo 'Replace Media';

                                    } else {

                                        echo 'Media File';

                                    }

                                    ?>

                                </span>

                                <span id="media_required_star">

                                    <?php

                                    if (empty($media_file)) {
                                        echo '*';
                                    }

                                    ?>

                                </span>

                            </label>


                            <div class="col-sm-6">

                                <input
                                    type="file"
                                    class="form-control"
                                    name="media_file"
                                    id="media_file"
                                    accept="image/*,audio/*,video/*"
                                    <?php

                                    if (
                                        $type === 'Audio Visual' &&
                                        empty($media_file)
                                    ) {

                                        echo 'required';

                                    }

                                    ?>>


                                <p
                                    class="help-block"
                                    id="media_help">

                                    <?php

                                    if (!empty($media_file)) {

                                        echo 'Upload a new image, audio, or video file to replace the current media. Leave empty to keep the current media.';

                                    } else {

                                        echo 'Upload an image, audio, or video file.';

                                    }

                                    ?>

                                </p>

                            </div>

                        </div>


                        <!-- =================================
                             SUBMIT
                        ================================== -->

                        <div class="form-group">

                            <label class="col-sm-2 control-label"></label>


                            <div class="col-sm-6">

                                <button
                                    type="submit"
                                    class="btn btn-success pull-left"
                                    name="form1">

                                    <i class="fa fa-save"></i>

                                    Update

                                </button>


                                <a
                                    href="quiz.php"
                                    class="btn btn-default"
                                    style="margin-left:5px;">

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


<script>

$(document).ready(function () {

    function toggleMediaUpload() {

        var type = $('#quiz_type').val();

        var hasExistingMedia =
            $('#existing_media_box').length > 0;

        if (type === 'Audio Visual') {

            /* --------------------------------
               SHOW MEDIA UPLOAD
            -------------------------------- */

            $('#media_upload_box').stop(true, true).slideDown();


            /*
             * If current media exists,
             * uploading a new file is optional.
             */

            if (hasExistingMedia) {

                $('#media_file').prop(
                    'required',
                    false
                );

                $('#media_label').text(
                    'Replace Media'
                );

                $('#media_required_star').text(
                    ''
                );

                $('#media_help').text(
                    'Upload a new image, audio, or video file to replace the current media. Leave empty to keep the current media.'
                );

            } else {

                /*
                 * No existing media:
                 * new media is required.
                 */

                $('#media_file').prop(
                    'required',
                    true
                );

                $('#media_label').text(
                    'Media File'
                );

                $('#media_required_star').text(
                    '*'
                );

                $('#media_help').text(
                    'Upload an image, audio, or video file.'
                );

            }

        } else {

            /* --------------------------------
               HIDE MEDIA UPLOAD
            -------------------------------- */

            $('#media_upload_box')
                .stop(true, true)
                .slideUp();


            $('#media_file').prop(
                'required',
                false
            );

        }

    }


    /* =================================
       TYPE CHANGE
    ================================== */

    $('#quiz_type').on(
        'change',
        function () {

            toggleMediaUpload();

        }
    );


    /* =================================
       REMOVE MEDIA CHECKBOX
    ================================== */

    $('#remove_media').on(
        'change',
        function () {

            if (
                $(this).is(':checked') &&
                $('#quiz_type').val() === 'Audio Visual'
            ) {

                /*
                 * If current media is removed,
                 * a new media file becomes required.
                 */

                $('#media_file').prop(
                    'required',
                    true
                );

                $('#media_required_star').text(
                    '*'
                );

                $('#media_help').text(
                    'Current media will be removed. Please upload a new image, audio, or video file.'
                );

            } else {

                toggleMediaUpload();

            }

        }
    );


    /* =================================
       INITIALIZE
    ================================== */

    toggleMediaUpload();

});

</script>


<?php require_once('footer.php'); ?>