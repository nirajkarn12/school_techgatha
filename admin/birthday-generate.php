<?php require_once('header.php'); ?>

<?php
ensureBirthdayTables($pdo);

if (!isset($_REQUEST['id'])) {
    header('location: birthday.php');
    exit;
}

$id = (int)$_REQUEST['id'];

$statement = $pdo->prepare("
    SELECT 
        s.*,
        t.template_image,
        t.output_x,
        t.output_y,
        t.output_width,
        t.output_height
    FROM tbl_birthday_student s
    LEFT JOIN tbl_birthday_template t 
        ON t.id = s.template_id
    WHERE s.id=?
");

$statement->execute(array($id));
$row = $statement->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    header('location: birthday.php');
    exit;
}

$templateImage = $row['template_image'];
$studentImage = $row['student_image'];
$studentName = $row['name'];
$className = $row['class_name'];
$birthdayDate = $row['birthday_date'];
$details = $row['details'];

$output_x = (int)($row['output_x'] ?? 215);
$output_y = (int)($row['output_y'] ?? 320);
$output_width = (int)($row['output_width'] ?? (int)$row['output_width']);
$output_height = (int)($row['output_height'] ?? (int)$row['output_height']);

$name_x = (int)($row['name_x'] ?? 390);
$name_y = (int)($row['name_y'] ?? 924);
$class_x = (int)($row['class_x'] ?? 348);
$class_y = (int)($row['class_y'] ?? 1017);

$text_size = (int)($row['text_size'] ?? 50);
$text_color = $row['text_color'] ?? '#0c2b5f';
$text_style = $row['text_style'] ?? 'bold';
$text_shadow = $row['text_shadow'] ?? '1';
$text_stroke_color = $row['text_stroke_color'] ?? '#ffffff';
$text_stroke_width = (int)($row['text_stroke_width'] ?? 2);
$text_stroke_position = $row['text_stroke_position'] ?? 'outside';
$image_layer = $row['image_layer'] ?? 'front';
$font_family = $row['font_family'] ?? 'Poppins';

$name_text_size = (int)($row['name_text_size'] ?? $text_size);
$name_text_style = $row['name_text_style'] ?? $text_style;
$name_text_color = $row['name_text_color'] ?? $text_color;
$name_text_shadow = $row['name_text_shadow'] ?? $text_shadow;
$name_text_stroke_color = $row['name_text_stroke_color'] ?? $text_stroke_color;
$name_text_stroke_width = (int)($row['name_text_stroke_width'] ?? $text_stroke_width);
$name_text_stroke_position = $row['name_text_stroke_position'] ?? $text_stroke_position;
$name_font_family = $row['name_font_family'] ?? $font_family;
$name_letter_spacing = (int)($row['name_letter_spacing'] ?? 0);

$class_text_size = (int)($row['class_text_size'] ?? 16);
$class_text_style = $row['class_text_style'] ?? $text_style;
$class_text_color = $row['class_text_color'] ?? '#ffffff';
$class_text_shadow = $row['class_text_shadow'] ?? $text_shadow;
$class_text_stroke_color = $row['class_text_stroke_color'] ?? $text_stroke_color;
$class_text_stroke_width = (int)($row['class_text_stroke_width'] ?? 0);
$class_text_stroke_position = $row['class_text_stroke_position'] ?? $text_stroke_position;
$class_font_family = $row['class_font_family'] ?? $font_family;
$class_letter_spacing = (int)($row['class_letter_spacing'] ?? 0);


$success_message = '';
$error_message = '';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['save_layout'])
) {
    $saveData = array(
        'output_x' => max(0, (int)($_POST['output_x'] ?? $output_x)),
        'output_y' => max(0, (int)($_POST['output_y'] ?? $output_y)),
        'output_width' => max(1, (int)($_POST['output_width'] ?? $output_width)),
        'output_height' => max(1, (int)($_POST['output_height'] ?? $output_height)),
        'name_x' => max(0, (int)($_POST['name_x'] ?? $name_x)),
        'name_y' => max(0, (int)($_POST['name_y'] ?? $name_y)),
        'class_x' => max(0, (int)($_POST['class_x'] ?? $class_x)),
        'class_y' => max(0, (int)($_POST['class_y'] ?? $class_y)),
        'text_size' => max(8, (int)($_POST['text_size'] ?? $text_size)),
        'text_color' => preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', (string)($_POST['text_color'] ?? $text_color)) ? $_POST['text_color'] : $text_color,
        'text_style' => in_array($_POST['text_style'] ?? $text_style, array('normal','bold','italic','bold-italic'), true) ? $_POST['text_style'] : $text_style,
        'text_shadow' => (isset($_POST['text_shadow']) && $_POST['text_shadow'] === '1') ? '1' : '0',
        'text_stroke_color' => preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', (string)($_POST['text_stroke_color'] ?? $text_stroke_color)) ? $_POST['text_stroke_color'] : $text_stroke_color,
        'text_stroke_width' => max(0, (int)($_POST['text_stroke_width'] ?? $text_stroke_width)),
        'text_stroke_position' => in_array($_POST['text_stroke_position'] ?? $text_stroke_position, array('outside','center','inside'), true) ? $_POST['text_stroke_position'] : $text_stroke_position,
        'font_family' => in_array($_POST['font_family'] ?? $font_family, array('Poppins','Preeti','Ganesh','OO1','ArapGraphic','Aakriti'), true) ? $_POST['font_family'] : $font_family,
        'name_text_size' => max(8, (int)($_POST['name_text_size'] ?? $name_text_size)),
        'name_text_style' => in_array($_POST['name_text_style'] ?? $name_text_style, array('normal','bold','italic','bold-italic'), true) ? $_POST['name_text_style'] : $name_text_style,
        'name_text_color' => preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', (string)($_POST['name_text_color'] ?? $name_text_color)) ? $_POST['name_text_color'] : $name_text_color,
        'name_text_shadow' => (isset($_POST['name_text_shadow']) && $_POST['name_text_shadow'] === '1') ? '1' : '0',
        'name_text_stroke_color' => preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', (string)($_POST['name_text_stroke_color'] ?? $name_text_stroke_color)) ? $_POST['name_text_stroke_color'] : $name_text_stroke_color,
        'name_text_stroke_width' => max(0, (int)($_POST['name_text_stroke_width'] ?? $name_text_stroke_width)),
        'name_text_stroke_position' => in_array($_POST['name_text_stroke_position'] ?? $name_text_stroke_position, array('outside','center','inside'), true) ? $_POST['name_text_stroke_position'] : $name_text_stroke_position,
        'name_font_family' => in_array($_POST['name_font_family'] ?? $name_font_family, array('Poppins','Preeti','Ganesh','OO1','ArapGraphic','Aakriti'), true) ? $_POST['name_font_family'] : $name_font_family,
        'name_letter_spacing' => max(-20, min(50, (int)($_POST['name_letter_spacing'] ?? $name_letter_spacing))),
        'class_text_size' => max(8, (int)($_POST['class_text_size'] ?? $class_text_size)),
        'class_text_style' => in_array($_POST['class_text_style'] ?? $class_text_style, array('normal','bold','italic','bold-italic'), true) ? $_POST['class_text_style'] : $class_text_style,
        'class_text_color' => preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', (string)($_POST['class_text_color'] ?? $class_text_color)) ? $_POST['class_text_color'] : $class_text_color,
        'class_text_shadow' => (isset($_POST['class_text_shadow']) && $_POST['class_text_shadow'] === '1') ? '1' : '0',
        'class_text_stroke_color' => preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', (string)($_POST['class_text_stroke_color'] ?? $class_text_stroke_color)) ? $_POST['class_text_stroke_color'] : $class_text_stroke_color,
        'class_text_stroke_width' => max(0, (int)($_POST['class_text_stroke_width'] ?? $class_text_stroke_width)),
        'class_text_stroke_position' => in_array($_POST['class_text_stroke_position'] ?? $class_text_stroke_position, array('outside','center','inside'), true) ? $_POST['class_text_stroke_position'] : $class_text_stroke_position,
        'class_font_family' => in_array($_POST['class_font_family'] ?? $class_font_family, array('Poppins','Preeti','Ganesh','OO1','ArapGraphic','Aakriti'), true) ? $_POST['class_font_family'] : $class_font_family,
        'class_letter_spacing' => max(-20, min(50, (int)($_POST['class_letter_spacing'] ?? $class_letter_spacing))),
        'image_layer' => in_array($_POST['image_layer'] ?? $image_layer, array('front','back'), true) ? $_POST['image_layer'] : $image_layer,
    );

    $columns = array();
    foreach ($saveData as $key => $value) {
        $columns[] = $key . ' = ?';
    }

    $sql = 'UPDATE tbl_birthday_student SET ' . implode(', ', $columns) . ' WHERE id=?';
    $params = array_values($saveData);
    $params[] = $id;
    $pdo->prepare($sql)->execute($params);

    $success_message = 'Layout saved successfully. You can reuse it later.';
    $statement = $pdo->prepare("SELECT * FROM tbl_birthday_student WHERE id=?");
    $statement->execute(array($id));
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $output_x = (int)($row['output_x'] ?? $output_x);
        $output_y = (int)($row['output_y'] ?? $output_y);
        $output_width = (int)($row['output_width'] ?? $output_width);
        $output_height = (int)($row['output_height'] ?? $output_height);
        $name_x = (int)($row['name_x'] ?? $name_x);
        $name_y = (int)($row['name_y'] ?? $name_y);
        $class_x = (int)($row['class_x'] ?? $class_x);
        $class_y = (int)($row['class_y'] ?? $class_y);
        $text_size = (int)($row['text_size'] ?? $text_size);
        $text_color = $row['text_color'] ?? $text_color;
        $text_style = $row['text_style'] ?? $text_style;
        $text_shadow = $row['text_shadow'] ?? $text_shadow;
        $text_stroke_color = $row['text_stroke_color'] ?? $text_stroke_color;
        $text_stroke_width = (int)($row['text_stroke_width'] ?? $text_stroke_width);
        $text_stroke_position = $row['text_stroke_position'] ?? $text_stroke_position;
        $font_family = $row['font_family'] ?? $font_family;
        $name_text_size = (int)($row['name_text_size'] ?? $name_text_size);
        $name_text_style = $row['name_text_style'] ?? $name_text_style;
        $name_text_color = $row['name_text_color'] ?? $name_text_color;
        $name_text_shadow = $row['name_text_shadow'] ?? $name_text_shadow;
        $name_text_stroke_color = $row['name_text_stroke_color'] ?? $name_text_stroke_color;
        $name_text_stroke_width = (int)($row['name_text_stroke_width'] ?? $name_text_stroke_width);
        $name_text_stroke_position = $row['name_text_stroke_position'] ?? $name_text_stroke_position;
        $name_font_family = $row['name_font_family'] ?? $name_font_family;
        $name_letter_spacing = (int)($row['name_letter_spacing'] ?? $name_letter_spacing);
        $class_text_size = (int)($row['class_text_size'] ?? $class_text_size);
        $class_text_style = $row['class_text_style'] ?? $class_text_style;
        $class_text_color = $row['class_text_color'] ?? $class_text_color;
        $class_text_shadow = $row['class_text_shadow'] ?? $class_text_shadow;
        $class_text_stroke_color = $row['class_text_stroke_color'] ?? $class_text_stroke_color;
        $class_text_stroke_width = (int)($row['class_text_stroke_width'] ?? $class_text_stroke_width);
        $class_text_stroke_position = $row['class_text_stroke_position'] ?? $class_text_stroke_position;
        $class_font_family = $row['class_font_family'] ?? $class_font_family;
        $class_letter_spacing = (int)($row['class_letter_spacing'] ?? $class_letter_spacing);
        $image_layer = $row['image_layer'] ?? $image_layer;
    }
}

/*
|--------------------------------------------------------------------------
| GENERATE CARD
|--------------------------------------------------------------------------
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['generate_card'])
) {

    /*
    |--------------------------------------------------------------------------
    | IMAGE POSITION
    |--------------------------------------------------------------------------
    */

    $output_x = max(
        0,
        (int)($_POST['output_x'] ?? $output_x)
    );

    $output_y = max(
        0,
        (int)($_POST['output_y'] ?? $output_y)
    );

    $output_width = max(
        1,
        (int)($_POST['output_width'] ?? $output_width)
    );

    $output_height = max(
        1,
        (int)($_POST['output_height'] ?? $output_height)
    );


    /*
    |--------------------------------------------------------------------------
    | NAME POSITION
    |--------------------------------------------------------------------------
    */

    $name_x = max(
        0,
        (int)($_POST['name_x'] ?? $name_x)
    );

    $name_y = max(
        0,
        (int)($_POST['name_y'] ?? $name_y)
    );


    /*
    |--------------------------------------------------------------------------
    | CLASS POSITION
    |--------------------------------------------------------------------------
    */

    $class_x = max(
        0,
        (int)($_POST['class_x'] ?? $class_x)
    );

    $class_y = max(
        0,
        (int)($_POST['class_y'] ?? $class_y)
    );


    /*
    |--------------------------------------------------------------------------
    | GENERAL TEXT SETTINGS
    |--------------------------------------------------------------------------
    */

    $text_size = max(
        8,
        (int)($_POST['text_size'] ?? $text_size)
    );

    if (
        isset($_POST['text_color']) &&
        preg_match(
            '/^#([0-9a-f]{3}|[0-9a-f]{6})$/i',
            $_POST['text_color']
        )
    ) {
        $text_color = $_POST['text_color'];
    }

    $text_style = in_array(
        $_POST['text_style'] ?? 'bold',
        array(
            'normal',
            'bold',
            'italic',
            'bold-italic'
        ),
        true
    )
        ? $_POST['text_style']
        : 'bold';


    $text_shadow =
        isset($_POST['text_shadow']) &&
        $_POST['text_shadow'] === '1'
            ? '1'
            : '0';


    if (
        isset($_POST['text_stroke_color']) &&
        preg_match(
            '/^#([0-9a-f]{3}|[0-9a-f]{6})$/i',
            $_POST['text_stroke_color']
        )
    ) {
        $text_stroke_color =
            $_POST['text_stroke_color'];
    }


    $text_stroke_width = max(
        0,
        (int)(
            $_POST['text_stroke_width']
            ?? $text_stroke_width
        )
    );


    $text_stroke_position = in_array(
        $_POST['text_stroke_position'] ?? 'outside',
        array(
            'outside',
            'center',
            'inside'
        ),
        true
    )
        ? $_POST['text_stroke_position']
        : 'outside';


    $image_layer = in_array(
        $_POST['image_layer'] ?? 'front',
        array(
            'front',
            'back'
        ),
        true
    )
        ? $_POST['image_layer']
        : 'front';


    /*
    |--------------------------------------------------------------------------
    | TEXT VALUES
    |--------------------------------------------------------------------------
    */

    if (isset($_POST['name_text'])) {
        $studentName = trim(
            $_POST['name_text']
        );
    }

    if (isset($_POST['class_text'])) {
        $className = trim(
            $_POST['class_text']
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FONT
    |--------------------------------------------------------------------------
    */

    $allowedFonts = array(
        'Poppins',
        'Preeti',
        'Ganesh',
        'OO1',
        'ArapGraphic',
        'Aakriti'
    );

    $font_family = in_array(
        $_POST['font_family'] ?? 'Poppins',
        $allowedFonts,
        true
    )
        ? $_POST['font_family']
        : 'Poppins';


    /*
    |--------------------------------------------------------------------------
    | NAME TEXT SETTINGS
    |--------------------------------------------------------------------------
    */

    $name_text_size = max(
        8,
        (int)(
            $_POST['name_text_size']
            ?? $text_size
        )
    );

    $name_text_style = in_array(
        $_POST['name_text_style']
        ?? $text_style,
        array(
            'normal',
            'bold',
            'italic',
            'bold-italic'
        ),
        true
    )
        ? $_POST['name_text_style']
        : $text_style;


    if (
        isset($_POST['name_text_color']) &&
        preg_match(
            '/^#([0-9a-f]{3}|[0-9a-f]{6})$/i',
            $_POST['name_text_color']
        )
    ) {
        $name_text_color =
            $_POST['name_text_color'];
    }


    $name_text_shadow =
        isset($_POST['name_text_shadow']) &&
        $_POST['name_text_shadow'] === '1'
            ? '1'
            : '0';


    if (
        isset($_POST['name_text_stroke_color']) &&
        preg_match(
            '/^#([0-9a-f]{3}|[0-9a-f]{6})$/i',
            $_POST['name_text_stroke_color']
        )
    ) {
        $name_text_stroke_color =
            $_POST['name_text_stroke_color'];
    }


    $name_text_stroke_width = max(
        0,
        (int)(
            $_POST['name_text_stroke_width']
            ?? 2
        )
    );


    $name_text_stroke_position = in_array(
        $_POST['name_text_stroke_position']
        ?? $text_stroke_position,
        array(
            'outside',
            'center',
            'inside'
        ),
        true
    )
        ? $_POST['name_text_stroke_position']
        : $text_stroke_position;


    $name_font_family = in_array(
        $_POST['name_font_family']
        ?? $font_family,
        $allowedFonts,
        true
    )
        ? $_POST['name_font_family']
        : $font_family;


    $name_letter_spacing = max(
        -20,
        min(
            50,
            (int)(
                $_POST['name_letter_spacing']
                ?? 0
            )
        )
    );


    /*
    |--------------------------------------------------------------------------
    | CLASS TEXT SETTINGS
    |--------------------------------------------------------------------------
    */

    $class_text_size = max(
        8,
        (int)(
            $_POST['class_text_size']
            ?? 13
        )
    );


    $class_text_style = in_array(
        $_POST['class_text_style']
        ?? $text_style,
        array(
            'normal',
            'bold',
            'italic',
            'bold-italic'
        ),
        true
    )
        ? $_POST['class_text_style']
        : $text_style;


    if (
        isset($_POST['class_text_color']) &&
        preg_match(
            '/^#([0-9a-f]{3}|[0-9a-f]{6})$/i',
            $_POST['class_text_color']
        )
    ) {
        $class_text_color =
            $_POST['class_text_color'];
    } else {
        $class_text_color = '#ffffff';
    }


    $class_text_shadow =
        isset($_POST['class_text_shadow']) &&
        $_POST['class_text_shadow'] === '1'
            ? '1'
            : '0';


    if (
        isset($_POST['class_text_stroke_color']) &&
        preg_match(
            '/^#([0-9a-f]{3}|[0-9a-f]{6})$/i',
            $_POST['class_text_stroke_color']
        )
    ) {
        $class_text_stroke_color =
            $_POST['class_text_stroke_color'];
    }


    $class_text_stroke_width = max(
        0,
        (int)(
            $_POST['class_text_stroke_width']
            ?? 0
        )
    );


    $class_text_stroke_position = in_array(
        $_POST['class_text_stroke_position']
        ?? $text_stroke_position,
        array(
            'outside',
            'center',
            'inside'
        ),
        true
    )
        ? $_POST['class_text_stroke_position']
        : $text_stroke_position;


    $class_font_family = in_array(
        $_POST['class_font_family']
        ?? $font_family,
        $allowedFonts,
        true
    )
        ? $_POST['class_font_family']
        : $font_family;


    $class_letter_spacing = max(
        -20,
        min(
            50,
            (int)(
                $_POST['class_letter_spacing']
                ?? 0
            )
        )
    );


    /*
    |--------------------------------------------------------------------------
    | GENERATE IMAGE
    |--------------------------------------------------------------------------
    */

    $outputName =
        'birthday-' .
        $id .
        '-' .
        time() .
        '.jpg';


    $outputPath =
        adminUploadsPath($outputName);


    $opts = array(

        'output_x' =>
            $output_x,

        'output_y' =>
            $output_y,

        'output_width' =>
            $output_width,

        'output_height' =>
            $output_height,

        'name_x' =>
            $name_x,

        'name_y' =>
            $name_y,

        'class_x' =>
            $class_x,

        'class_y' =>
            $class_y,

        'text_size' =>
            $text_size,

        'text_color' =>
            $text_color,

        'text_style' =>
            $text_style,

        'text_shadow' =>
            $text_shadow,

        'text_stroke_color' =>
            $text_stroke_color,

        'text_stroke_width' =>
            $text_stroke_width,

        'text_stroke_position' =>
            $text_stroke_position,

        'font_family' =>
            $font_family,

        'name_text_size' =>
            $name_text_size,

        'name_text_style' =>
            $name_text_style,

        'name_text_color' =>
            $name_text_color,

        'name_text_shadow' =>
            $name_text_shadow,

        'name_text_stroke_color' =>
            $name_text_stroke_color,

        'name_text_stroke_width' =>
            $name_text_stroke_width,

        'name_text_stroke_position' =>
            $name_text_stroke_position,

        'name_font_family' =>
            $name_font_family,

        'name_letter_spacing' =>
            $name_letter_spacing,

        'class_text_size' =>
            $class_text_size,

        'class_text_style' =>
            $class_text_style,

        'class_text_color' =>
            $class_text_color,

        'class_text_shadow' =>
            $class_text_shadow,

        'class_text_stroke_color' =>
            $class_text_stroke_color,

        'class_text_stroke_width' =>
            $class_text_stroke_width,

        'class_text_stroke_position' =>
            $class_text_stroke_position,

        'class_font_family' =>
            $class_font_family,

        'class_letter_spacing' =>
            $class_letter_spacing,

        'image_layer' =>
            $image_layer
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

        $pdo->prepare("
            UPDATE tbl_birthday_student
            SET generated_image=?
            WHERE id=?
        ")->execute(
            array(
                $outputName,
                $id
            )
        );


        $success_message =
            'Birthday card generated successfully. Use the button below to download it.';

    } else {

        $error_message =
            $result['error'];
    }
}
?>


<section class="content-header">

    <div class="content-header-left">
        <h1>Generate Birthday Card</h1>
    </div>

    <div class="content-header-right">
        <a
            href="birthday.php"
            class="btn btn-primary btn-sm"
        >
            Back
        </a>
    </div>

</section>


<section class="content">

<?php if ($error_message): ?>

    <div class="row">

        <div class="col-md-12">

            <div class="callout callout-danger">
                <p>
                    <?php echo htmlspecialchars($error_message); ?>
                </p>
            </div>

        </div>

    </div>

<?php endif; ?>


<form
    method="post"
    action=""
    class="form-horizontal"
>

    <input
        type="hidden"
        name="generate_card"
        value="1"
    >


    <!-- IMAGE HIDDEN VALUES -->

    <input
        type="hidden"
        id="output_x"
        name="output_x"
        value="<?php echo $output_x; ?>"
    >

    <input
        type="hidden"
        id="output_y"
        name="output_y"
        value="<?php echo $output_y; ?>"
    >

    <input
        type="hidden"
        id="output_width"
        name="output_width"
        value="<?php echo $output_width; ?>"
    >

    <input
        type="hidden"
        id="output_height"
        name="output_height"
        value="<?php echo $output_height; ?>"
    >


    <!-- NAME HIDDEN VALUES -->

    <input
        type="hidden"
        id="name_x"
        name="name_x"
        value="<?php echo $name_x; ?>"
    >

    <input
        type="hidden"
        id="name_y"
        name="name_y"
        value="<?php echo $name_y; ?>"
    >


    <!-- CLASS HIDDEN VALUES -->

    <input
        type="hidden"
        id="class_x"
        name="class_x"
        value="<?php echo $class_x; ?>"
    >

    <input
        type="hidden"
        id="class_y"
        name="class_y"
        value="<?php echo $class_y; ?>"
    >


    <div class="row">

        <!-- ========================================================= -->
        <!-- PREVIEW -->
        <!-- ========================================================= -->

        <div class="col-md-8">

            <div class="box box-info">

                <div class="box-header with-border">

                    <h3 class="box-title">
                        Drag Student Photo to Position
                    </h3>

                </div>


                <div class="box-body">

                    <link
                        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap"
                        rel="stylesheet"
                    >


                    <style>

                        @font-face {
                            font-family: 'DejaVuSansLocal';
                            src: url('../vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf')
                                format('truetype');
                            font-weight: normal;
                        }

                        @font-face {
                            font-family: 'DejaVuSansLocal';
                            src: url('../vendor/dompdf/dompdf/lib/fonts/DejaVuSans-Bold.ttf')
                                format('truetype');
                            font-weight: bold;
                        }


                        @font-face {
                            font-family: 'PreetiLocal';
                            src: url('../assets/fonts/Preeti.ttf')
                                format('truetype');
                        }

                        @font-face {
                            font-family: 'GaneshLocal';
                            src: url('../assets/fonts/Ganesh.ttf')
                                format('truetype');
                        }

                        @font-face {
                            font-family: 'OO1Local';
                            src: url('../assets/fonts/OO1.ttf')
                                format('truetype');
                        }

                        @font-face {
                            font-family: 'ArapGraphicLocal';
                            src: url('../assets/fonts/ArapGraphic.ttf')
                                format('truetype');
                        }

                        @font-face {
                            font-family: 'AakritiLocal';
                            src: url('../assets/fonts/Aakriti.ttf')
                                format('truetype');
                        }


                        #name-overlay,
                        #class-overlay {
                            font-family:
                                'Poppins',
                                'DejaVuSansLocal',
                                Arial,
                                sans-serif;
                        }


                        #preview-container {
                            padding: 0;
                            background: transparent;
                            border: 0 !important;
                            box-shadow: none;
                            border-radius: 0;
                        }


                        .box.box-info {
                            margin-bottom: 18px;
                        }


                        .form-group {
                            margin-bottom: 12px;
                        }


                        .editor-panel {
                            background: #fff;
                            border: 1px solid #dae1e7;
                            border-radius: 8px;
                            padding: 12px;
                            margin-bottom: 10px;
                            box-shadow:
                                0 1px 4px rgba(0,0,0,0.05);
                        }


                        .editor-panel-header {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            margin-bottom: 12px;
                        }


                        .editor-panel-header .panel-title {
                            font-weight: 700;
                            color: #2c3e50;
                            font-size: 14px;
                        }


                        .editor-panel-body {
                            display: grid;
                            gap: 10px;
                        }


                        .panel-toolbar {
                            display: flex;
                            flex-wrap: wrap;
                            gap: 8px;
                            align-items: center;
                            justify-content: space-between;
                        }


                        .panel-toolbar .toolbar-group {
                            display: flex;
                            gap: 6px;
                            align-items: center;
                        }


                        .panel-toolbar .font-group {
                            flex: 0 1 220px;
                            min-width: 140px;
                        }


                        .font-select {
                            width: 100%;
                            min-width: 140px;
                            max-width: 240px;
                        }


                        .panel-group {
                            display: grid;
                            gap: 10px;
                        }


                        .panel-item {
                            display: flex;
                            flex-direction: column;
                        }


                        .panel-item label {
                            font-size: 11px;
                            color: #6c757d;
                            margin-bottom: 4px;
                        }


                        .panel-item .input-group {
                            width: 100%;
                        }


                        .panel-item .form-control {
                            width: 100%;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | TWO COLUMNS
                        |--------------------------------------------------------------------------
                        */

                        .panel-grid {
                            display: grid;
                            grid-template-columns:
                                repeat(
                                    2,
                                    minmax(120px, 1fr)
                                );
                            gap: 10px;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | FULL WIDTH ROW
                        |--------------------------------------------------------------------------
                        */

                        .panel-full-width {
                            grid-column: 1 / -1;
                        }


                        .panel-actions {
                            display: flex;
                            justify-content: flex-end;
                            align-items: center;
                        }


                        .panel-actions .btn {
                            min-width: 140px;
                        }


                        .tool-button {
                            min-width: 38px;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            padding: 6px 8px;
                            border-radius: 4px;
                            border: 1px solid #d2d6de;
                            background: #fff;
                            color: #444;
                        }


                        .tool-button:hover {
                            background: #f5f7fa;
                            border-color: #c6c8cc;
                        }


                        .compact-ui .form-control {
                            padding: 4px 6px;
                            height: 30px;
                            font-size: 13px;
                        }


                        .compact-ui .box-body {
                            padding: 8px;
                        }


                        .compact-ui .editor-toolbar,
                        .compact-ui .editor-toolbar-2,
                        .compact-ui .compact-controls {
                            display: flex;
                            flex-wrap: wrap;
                            gap: 6px;
                        }


                        .compact-ui .compact-row {
                            width: 100%;
                            display: flex;
                            flex-wrap: wrap;
                            gap: 6px;
                            align-items: center;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | PREVIEW
                        |--------------------------------------------------------------------------
                        */

                        #preview-container {
                            position: relative;
                            border: 0;
                            padding: 0;
                            margin: 0;
                            background: transparent;
                            max-width: 100%;
                            display: inline-block;
                            overflow: hidden;
                            touch-action: none;
                        }


                        #template-preview {
                            display: block;
                            width: 100%;
                            height: auto;
                            position: relative;
                            z-index: 1;
                        }


                        #student-wrapper {
                            position: absolute;
                            cursor: move;
                            box-sizing: border-box;
                            border: 2px dashed #00a65a;
                            touch-action: none;
                        }


                        #student-preview {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                            user-drag: none;
                            user-select: none;
                            -webkit-user-drag: none;
                            -webkit-touch-callout: none;
                        }


                        .resize-handle {
                            position: absolute;
                            width: 12px;
                            height: 12px;
                            background: #00a65a;
                            border: 2px solid #fff;
                        }


                        @media (max-width: 767px) {

                            .panel-grid {
                                grid-template-columns:
                                    repeat(
                                        2,
                                        minmax(100px, 1fr)
                                    );
                            }

                        }

                    </style>


                    <div id="preview-container">

                        <img
                            id="template-preview"
                            src="<?php echo htmlspecialchars(adminUploadUrl($templateImage)); ?>"
                            alt="Template Preview"
                        >


                        <!-- STUDENT IMAGE -->

                        <div
                            id="student-wrapper"
                            style="
                                left:<?php echo $output_x; ?>px;
                                top:<?php echo $output_y; ?>px;
                                width:<?php echo $output_width; ?>px;
                                height:<?php echo $output_height; ?>px;
                                z-index:2;
                            "
                        >

                            <img
                                id="student-preview"
                                src="<?php echo htmlspecialchars(adminUploadUrl($studentImage)); ?>"
                                alt="Student Preview"
                                draggable="false"
                            >


                            <div
                                class="resize-handle"
                                data-dir="nw"
                                style="
                                    top:-8px;
                                    left:-8px;
                                    cursor:nwse-resize;
                                "
                            ></div>


                            <div
                                class="resize-handle"
                                data-dir="ne"
                                style="
                                    top:-8px;
                                    right:-8px;
                                    cursor:nesw-resize;
                                "
                            ></div>


                            <div
                                class="resize-handle"
                                data-dir="sw"
                                style="
                                    bottom:-8px;
                                    left:-8px;
                                    cursor:nesw-resize;
                                "
                            ></div>


                            <div
                                class="resize-handle"
                                data-dir="se"
                                style="
                                    bottom:-8px;
                                    right:-8px;
                                    cursor:nwse-resize;
                                "
                            ></div>

                        </div>


                        <!-- NAME -->

                        <div
                            id="name-overlay"
                            style="
                                position:absolute;
                                left:<?php echo $name_x; ?>px;
                                top:<?php echo $name_y; ?>px;
                                color:<?php echo htmlspecialchars($name_text_color); ?>;
                                font-weight:700;
                                font-size:<?php echo (int)$name_text_size; ?>px;
                                font-family:<?php echo htmlspecialchars($name_font_family); ?>, 'DejaVuSansLocal', Arial, sans-serif;
                                line-height:1;
                                display:inline-block;
                                vertical-align:top;
                                letter-spacing:<?php echo (int)$name_letter_spacing; ?>px;
                                z-index:3;
                                white-space:nowrap;
                                cursor:move;
                                touch-action:none;
                                text-shadow:<?php echo $name_text_shadow === '1' ? '2px 2px rgba(0,0,0,0.25)' : 'none'; ?>;
                                -webkit-text-stroke:
                                    <?php echo (int)$name_text_stroke_width; ?>px
                                    <?php echo htmlspecialchars($name_text_stroke_color); ?>;
                            "
                        >
                            <?php
                            echo htmlspecialchars($studentName);
                            ?>
                        </div>


                        <!-- CLASS -->

                        <div
                            id="class-overlay"
                            style="
                                position:absolute;
                                left:<?php echo $class_x; ?>px;
                                top:<?php echo $class_y; ?>px;
                                color:<?php echo htmlspecialchars($class_text_color); ?>;
                                font-weight:600;
                                font-size:<?php echo (int)$class_text_size; ?>px;
                                font-family:<?php echo htmlspecialchars($class_font_family); ?>, 'DejaVuSansLocal', Arial, sans-serif;
                                line-height:1;
                                display:inline-block;
                                vertical-align:top;
                                letter-spacing:<?php echo (int)$class_letter_spacing; ?>px;
                                z-index:3;
                                white-space:nowrap;
                                cursor:move;
                                touch-action:none;
                                text-shadow:<?php echo $class_text_shadow === '1' ? '2px 2px rgba(0,0,0,0.25)' : 'none'; ?>;
                                -webkit-text-stroke:
                                    <?php echo (int)$class_text_stroke_width; ?>px
                                    <?php echo htmlspecialchars($class_text_stroke_color); ?>;
                            "
                        >
                            <?php
                            echo htmlspecialchars($className);
                            ?>
                        </div>

                    </div>


                    <p class="help-block">
                        Drag the student image, Name or Class.
                        X/Y coordinates update automatically.
                    </p>

                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- RIGHT CONTROL PANEL -->
        <!-- ========================================================= -->

        <div class="col-md-4">

            <div class="box box-info">

                <div class="box-header with-border">

                    <h3 class="box-title">
                        Position / Size
                    </h3>

                </div>


                <div class="box-body">

                    <div class="editor-panel">

                        <div class="editor-panel-header">

                            <span class="panel-title">
                                Text & Layer Controls
                            </span>


                            <button
                                type="button"
                                id="toggle_compact"
                                class="btn btn-default btn-sm tool-button"
                                title="Toggle compact UI"
                            >
                                <i class="fa fa-compress"></i>
                            </button>

                        </div>


                        <div class="editor-panel-body">


                            <!-- TOOLBAR -->

                            <div class="panel-toolbar">

                                <div class="toolbar-group">

                                    <button
                                        type="button"
                                        id="btn-bold"
                                        class="btn btn-default btn-sm tool-button"
                                        title="Bold"
                                    >
                                        <i class="fa fa-bold"></i>
                                    </button>


                                    <button
                                        type="button"
                                        id="btn-italic"
                                        class="btn btn-default btn-sm tool-button"
                                        title="Italic"
                                    >
                                        <i class="fa fa-italic"></i>
                                    </button>


                                    <button
                                        type="button"
                                        id="btn-size-decr"
                                        class="btn btn-default btn-sm tool-button"
                                        title="Decrease size"
                                    >
                                        <i class="fa fa-minus"></i>
                                    </button>


                                    <button
                                        type="button"
                                        id="btn-size-incr"
                                        class="btn btn-default btn-sm tool-button"
                                        title="Increase size"
                                    >
                                        <i class="fa fa-plus"></i>
                                    </button>


                                    <input
                                        id="toolbar_text_size_input"
                                        type="number"
                                        class="form-control input-sm"
                                        value="<?php echo $text_size; ?>"
                                        min="8"
                                        step="1"
                                        style="width:80px;"
                                    >

                                </div>


                                <div class="toolbar-group font-group">

                                    <div
                                        class="input-group input-group-sm"
                                        style="width:100%;"
                                    >

                                        <span class="input-group-addon">
                                            <i class="fa fa-font"></i>
                                        </span>


                                        <select
                                            id="font_family_input"
                                            name="font_family"
                                            class="form-control font-select"
                                        >

                                            <option
                                                value="Poppins"
                                                <?php echo $font_family === 'Poppins' ? 'selected' : ''; ?>
                                            >
                                                Poppins
                                            </option>


                                            <option
                                                value="Preeti"
                                                <?php echo $font_family === 'Preeti' ? 'selected' : ''; ?>
                                            >
                                                Preeti (Nepali)
                                            </option>


                                            <option
                                                value="Ganesh"
                                                <?php echo $font_family === 'Ganesh' ? 'selected' : ''; ?>
                                            >
                                                Ganesh (Nepali)
                                            </option>


                                            <option
                                                value="OO1"
                                                <?php echo $font_family === 'OO1' ? 'selected' : ''; ?>
                                            >
                                                OO1 (Nepali)
                                            </option>


                                            <option
                                                value="ArapGraphic"
                                                <?php echo $font_family === 'ArapGraphic' ? 'selected' : ''; ?>
                                            >
                                                Arap Graphic (Nepali)
                                            </option>


                                            <option
                                                value="Aakriti"
                                                <?php echo $font_family === 'Aakriti' ? 'selected' : ''; ?>
                                            >
                                                Aakriti (Nepali)
                                            </option>

                                        </select>

                                    </div>

                                </div>

                            </div>


                            <!-- ACTIVE TEXT LAYER -->

                            <div class="panel-group">

                                <div class="panel-item">

                                    <label>
                                        Text layer
                                    </label>


                                    <select
                                        id="text_layer_select"
                                        class="form-control"
                                    >

                                        <option value="name">
                                            Name
                                        </option>

                                        <option value="class">
                                            Class
                                        </option>

                                    </select>

                                </div>


                                <div class="panel-item">

                                    <label>
                                        Text
                                    </label>


                                    <div class="input-group input-group-sm">

                                        <span class="input-group-addon">
                                            <i class="fa fa-pencil"></i>
                                        </span>


                                        <input
                                            type="text"
                                            id="active_layer_text_input"
                                            class="form-control"
                                            placeholder="Text"
                                            value="<?php echo htmlspecialchars($studentName); ?>"
                                        >

                                    </div>

                                </div>


                                <!-- NAME HIDDEN SETTINGS -->

                                <input
                                    type="hidden"
                                    id="name_text_input"
                                    name="name_text"
                                    value="<?php echo htmlspecialchars($studentName); ?>"
                                >

                                <input
                                    type="hidden"
                                    id="name_text_size_input"
                                    name="name_text_size"
                                    value="<?php echo $text_size; ?>"
                                >

                                <input
                                    type="hidden"
                                    id="name_text_style_input"
                                    name="name_text_style"
                                    value="<?php echo htmlspecialchars($text_style); ?>"
                                >

                                <input
                                    type="hidden"
                                    id="name_text_color_input"
                                    name="name_text_color"
                                    value="<?php echo htmlspecialchars($text_color); ?>"
                                >

                                <input
                                    type="hidden"
                                    id="name_text_shadow_input"
                                    name="name_text_shadow"
                                    value="<?php echo $text_shadow; ?>"
                                >

                                <input
                                    type="hidden"
                                    id="name_text_stroke_color_input"
                                    name="name_text_stroke_color"
                                    value="<?php echo htmlspecialchars($text_stroke_color); ?>"
                                >

                                <input
                                    type="hidden"
                                    id="name_text_stroke_width_input"
                                    name="name_text_stroke_width"
                                    value="<?php echo (int)$text_stroke_width; ?>"
                                >

                                <input
                                    type="hidden"
                                    id="name_text_stroke_position_input"
                                    name="name_text_stroke_position"
                                    value="<?php echo htmlspecialchars($text_stroke_position); ?>"
                                >

                                <input
                                    type="hidden"
                                    id="name_font_family_input"
                                    name="name_font_family"
                                    value="<?php echo htmlspecialchars($font_family); ?>"
                                >

                                <input
                                    type="hidden"
                                    id="name_letter_spacing_input"
                                    name="name_letter_spacing"
                                    value="0"
                                >


                                <!-- CLASS HIDDEN SETTINGS -->

                                <input
                                    type="hidden"
                                    id="class_text_input"
                                    name="class_text"
                                    value="<?php echo htmlspecialchars($className); ?>"
                                >

                                <input
                                    type="hidden"
                                    id="class_text_size_input"
                                    name="class_text_size"
                                    value="<?php echo (int)$class_text_size; ?>"
                                >

                                <input
                                    type="hidden"
                                    id="class_text_style_input"
                                    name="class_text_style"
                                    value="<?php echo htmlspecialchars($text_style); ?>"
                                >

                                <input
                                    type="hidden"
                                    id="class_text_color_input"
                                    name="class_text_color"
                                    value="#ffffff"
                                >

                                <input
                                    type="hidden"
                                    id="class_text_shadow_input"
                                    name="class_text_shadow"
                                    value="<?php echo $text_shadow; ?>"
                                >

                                <input
                                    type="hidden"
                                    id="class_text_stroke_color_input"
                                    name="class_text_stroke_color"
                                    value="<?php echo htmlspecialchars($text_stroke_color); ?>"
                                >

                                <input
                                    type="hidden"
                                    id="class_text_stroke_width_input"
                                    name="class_text_stroke_width"
                                    value="<?php echo (int)$class_text_stroke_width; ?>"
                                >

                                <input
                                    type="hidden"
                                    id="class_text_stroke_position_input"
                                    name="class_text_stroke_position"
                                    value="<?php echo htmlspecialchars($text_stroke_position); ?>"
                                >

                                <input
                                    type="hidden"
                                    id="class_font_family_input"
                                    name="class_font_family"
                                    value="<?php echo htmlspecialchars($font_family); ?>"
                                >

                                <input
                                    type="hidden"
                                    id="class_letter_spacing_input"
                                    name="class_letter_spacing"
                                    value="0"
                                >

                            </div>


                            <!-- ================================================= -->
                            <!-- COMPACT CONTROLS -->
                            <!-- ================================================= -->

                            <div class="panel-grid">


                                <!-- TEXT SIZE -->

                                <div class="panel-item">

                                    <label>
                                        Text size
                                    </label>

                                    <div class="input-group input-group-sm">

                                        <span class="input-group-addon">
                                            <i class="fa fa-text-height"></i>
                                        </span>

                                        <input
                                            id="compact_text_size"
                                            type="number"
                                            class="form-control"
                                            value="<?php echo $text_size; ?>"
                                            min="8"
                                        >

                                    </div>

                                </div>


                                <!-- TEXT COLOR -->

                                <div class="panel-item">

                                    <label>
                                        Text color
                                    </label>

                                    <div class="input-group input-group-sm">

                                        <span class="input-group-addon">
                                            <i class="fa fa-fill-drip"></i>
                                        </span>

                                        <input
                                            id="compact_text_color"
                                            type="color"
                                            class="form-control"
                                            value="<?php echo htmlspecialchars($text_color); ?>"
                                        >

                                    </div>

                                </div>


                                <!-- STROKE COLOR -->

                                <div class="panel-item">

                                    <label>
                                        Stroke color
                                    </label>

                                    <div class="input-group input-group-sm">

                                        <span class="input-group-addon">
                                            <i class="fa fa-paint-brush"></i>
                                        </span>

                                        <input
                                            id="compact_stroke_color"
                                            type="color"
                                            class="form-control"
                                            value="<?php echo htmlspecialchars($text_stroke_color); ?>"
                                        >

                                    </div>

                                </div>


                                <!-- STROKE WIDTH -->

                                <div class="panel-item">

                                    <label>
                                        Stroke width
                                    </label>

                                    <div class="input-group input-group-sm">

                                        <span class="input-group-addon">
                                            <i class="fa fa-minus-square"></i>
                                        </span>

                                        <input
                                            id="compact_stroke_width"
                                            type="number"
                                            class="form-control"
                                            value="<?php echo (int)$text_stroke_width; ?>"
                                            min="0"
                                            max="10"
                                        >

                                    </div>

                                </div>


                                <!-- TRACKING -->

                                <div class="panel-item">

                                    <label>
                                        Tracking
                                    </label>

                                    <div class="input-group input-group-sm">

                                        <span class="input-group-addon">
                                            <i class="fa fa-text-width"></i>
                                        </span>

                                        <input
                                            id="compact_letter_spacing"
                                            type="number"
                                            class="form-control"
                                            value="0"
                                            min="-10"
                                            max="30"
                                        >

                                    </div>

                                </div>


                                <!-- STROKE POSITION -->

                                <div class="panel-item">

                                    <label>
                                        Stroke position
                                    </label>

                                    <div class="input-group input-group-sm">

                                        <span class="input-group-addon">
                                            <i class="fa fa-layer-group"></i>
                                        </span>

                                        <select
                                            id="compact_stroke_pos"
                                            class="form-control"
                                        >

                                            <option value="outside">
                                                Outside
                                            </option>

                                            <option value="center">
                                                Center
                                            </option>

                                            <option value="inside">
                                                Inside
                                            </option>

                                        </select>

                                    </div>

                                </div>


                                <!-- ================================================= -->
                                <!-- NAME X -->
                                <!-- ================================================= -->

                                <div class="panel-item">

                                    <label>
                                        Name X
                                    </label>

                                    <div class="input-group input-group-sm">

                                        <span class="input-group-addon">
                                            <i class="fa fa-arrows-alt-h"></i>
                                        </span>

                                        <input
                                            id="name_position_x"
                                            type="number"
                                            class="form-control"
                                            value="<?php echo $name_x; ?>"
                                            min="0"
                                        >

                                    </div>

                                </div>


                                <!-- NAME Y -->

                                <div class="panel-item">

                                    <label>
                                        Name Y
                                    </label>

                                    <div class="input-group input-group-sm">

                                        <span class="input-group-addon">
                                            <i class="fa fa-arrows-alt-v"></i>
                                        </span>

                                        <input
                                            id="name_position_y"
                                            type="number"
                                            class="form-control"
                                            value="<?php echo $name_y; ?>"
                                            min="0"
                                        >

                                    </div>

                                </div>


                                <!-- ================================================= -->
                                <!-- CLASS X -->
                                <!-- ================================================= -->

                                <div class="panel-item">

                                    <label>
                                        Class X
                                    </label>

                                    <div class="input-group input-group-sm">

                                        <span class="input-group-addon">
                                            <i class="fa fa-arrows-alt-h"></i>
                                        </span>

                                        <input
                                            id="class_position_x"
                                            type="number"
                                            class="form-control"
                                            value="<?php echo $class_x; ?>"
                                            min="0"
                                        >

                                    </div>

                                </div>


                                <!-- CLASS Y -->

                                <div class="panel-item">

                                    <label>
                                        Class Y
                                    </label>

                                    <div class="input-group input-group-sm">

                                        <span class="input-group-addon">
                                            <i class="fa fa-arrows-alt-v"></i>
                                        </span>

                                        <input
                                            id="class_position_y"
                                            type="number"
                                            class="form-control"
                                            value="<?php echo $class_y; ?>"
                                            min="0"
                                        >

                                    </div>

                                </div>


                                <!-- ================================================= -->
                                <!-- IMAGE X -->
                                <!-- ================================================= -->

                                <div class="panel-item">

                                    <label>
                                        Image X
                                    </label>

                                    <div class="input-group input-group-sm">

                                        <span class="input-group-addon">
                                            <i class="fa fa-arrows-alt-h"></i>
                                        </span>

                                        <input
                                            id="compact_x"
                                            type="number"
                                            class="form-control"
                                            value="<?php echo $output_x; ?>"
                                            min="0"
                                        >

                                    </div>

                                </div>


                                <!-- IMAGE Y -->

                                <div class="panel-item">

                                    <label>
                                        Image Y
                                    </label>

                                    <div class="input-group input-group-sm">

                                        <span class="input-group-addon">
                                            <i class="fa fa-arrows-alt-v"></i>
                                        </span>

                                        <input
                                            id="compact_y"
                                            type="number"
                                            class="form-control"
                                            value="<?php echo $output_y; ?>"
                                            min="0"
                                        >

                                    </div>

                                </div>


                                <!-- ================================================= -->
                                <!-- LAYER FULL WIDTH -->
                                <!-- ================================================= -->

                                <div class="panel-item panel-full-width">

                                    <label>
                                        Layer
                                    </label>

                                    <div class="input-group input-group-sm">

                                        <span class="input-group-addon">
                                            <i class="fa fa-clone"></i>
                                        </span>

                                        <select
                                            id="compact_image_layer"
                                            class="form-control"
                                        >

                                            <option
                                                value="front"
                                                <?php echo $image_layer === 'front' ? 'selected' : ''; ?>
                                            >
                                                Front
                                            </option>

                                            <option
                                                value="back"
                                                <?php echo $image_layer === 'back' ? 'selected' : ''; ?>
                                            >
                                                Back
                                            </option>

                                        </select>

                                    </div>

                                </div>


                                <!-- DOWNLOAD -->

                                <div class="panel-item panel-full-width panel-actions">

                                    <button
                                        type="submit"
                                        name="save_layout"
                                        value="1"
                                        class="btn btn-warning btn-sm"
                                    >
                                        <i class="fa fa-save"></i>
                                        Save Layout
                                    </button>

                                    <button
                                        type="button"
                                        id="download-preview"
                                        class="btn btn-primary btn-sm"
                                    >
                                        <i class="fa fa-check"></i>
                                        Download Preview
                                    </button>

                                </div>

                            </div>


                            <!-- ================================================= -->
                            <!-- OLD / FULL CONTROLS -->
                            <!-- ================================================= -->

                            <div style="display:none;">

                                <input
                                    type="number"
                                    id="input_x"
                                    value="<?php echo $output_x; ?>"
                                >

                                <input
                                    type="number"
                                    id="input_y"
                                    value="<?php echo $output_y; ?>"
                                >

                                <input
                                    type="number"
                                    id="input_width"
                                    value="<?php echo $output_width; ?>"
                                >

                                <input
                                    type="number"
                                    id="input_height"
                                    value="<?php echo $output_height; ?>"
                                >


                                <input
                                    type="number"
                                    id="name_x_input"
                                    value="<?php echo $name_x; ?>"
                                >

                                <input
                                    type="number"
                                    id="name_y_input"
                                    value="<?php echo $name_y; ?>"
                                >

                                <input
                                    type="number"
                                    id="class_x_input"
                                    value="<?php echo $class_x; ?>"
                                >

                                <input
                                    type="number"
                                    id="class_y_input"
                                    value="<?php echo $class_y; ?>"
                                >


                                <input
                                    type="number"
                                    id="text_size_input"
                                    name="text_size"
                                    value="<?php echo $text_size; ?>"
                                >

                                <input
                                    type="number"
                                    id="text_letter_spacing_input"
                                    name="letter_spacing"
                                    value="0"
                                >

                                <input
                                    type="color"
                                    id="text_stroke_color_input"
                                    name="text_stroke_color"
                                    value="<?php echo htmlspecialchars($text_stroke_color); ?>"
                                >

                                <input
                                    type="number"
                                    id="text_stroke_width_input"
                                    name="text_stroke_width"
                                    value="<?php echo (int)$text_stroke_width; ?>"
                                >


                                <select
                                    id="text_stroke_position_input"
                                    name="text_stroke_position"
                                >
                                    <option value="outside">Outside</option>
                                    <option value="center">Center</option>
                                    <option value="inside">Inside</option>
                                </select>


                                <select
                                    id="text_style_input"
                                    name="text_style"
                                >
                                    <option value="normal">Normal</option>
                                    <option value="bold">Bold</option>
                                    <option value="italic">Italic</option>
                                    <option value="bold-italic">Bold Italic</option>
                                </select>


                                <input
                                    type="color"
                                    id="text_color_input"
                                    name="text_color"
                                    value="<?php echo htmlspecialchars($text_color); ?>"
                                >


                                <input
                                    type="checkbox"
                                    id="text_shadow_input"
                                    name="text_shadow"
                                    value="1"
                                    <?php echo $text_shadow === '1' ? 'checked' : ''; ?>
                                >


                                <select
                                    id="image_layer"
                                    name="image_layer"
                                >
                                    <option value="front">Front</option>
                                    <option value="back">Back</option>
                                </select>

                            </div>


                        </div>

                    </div>


                    <?php if ($success_message): ?>

                        <div class="box box-success">

                            <div class="box-body">

                                <p>
                                    <?php echo htmlspecialchars($success_message); ?>
                                </p>


                                <a
                                    href="birthday-download.php?id=<?php echo (int)$id; ?>"
                                    class="btn btn-info"
                                >
                                    Download Generated Image
                                </a>

                            </div>

                        </div>

                    <?php endif; ?>


                    <div style="margin-top:10px; text-align:right;">

                        <button
                            type="submit"
                            class="btn btn-success"
                        >
                            Generate Image
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

</form>

</section>


<?php require_once('footer.php'); ?>


<!-- ================================================================ -->
<!-- HTML2CANVAS -->
<!-- ================================================================ -->

<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>


<script>
(function($) {

    /*
    |--------------------------------------------------------------------------
    | ELEMENTS
    |--------------------------------------------------------------------------
    */

    var $template = $('#template-preview');
    var $wrapper = $('#student-wrapper');
    var $student = $('#student-preview');
    var $container = $('#preview-container');


    /* Image hidden/full fields */
    var $inputX = $('#input_x');
    var $inputY = $('#input_y');
    var $inputW = $('#input_width');
    var $inputH = $('#input_height');

    var $hiddenX = $('#output_x');
    var $hiddenY = $('#output_y');
    var $hiddenW = $('#output_width');
    var $hiddenH = $('#output_height');


    /* Name/Class hidden positions */
    var $nameX = $('#name_x');
    var $nameY = $('#name_y');

    var $classX = $('#class_x');
    var $classY = $('#class_y');


    /* Dedicated Name position controls */
    var $namePositionX = $('#name_position_x');
    var $namePositionY = $('#name_position_y');


    /* Dedicated Class position controls */
    var $classPositionX = $('#class_position_x');
    var $classPositionY = $('#class_position_y');


    /* Text settings */
    var $textSize = $('#text_size_input');
    var $toolbarTextSizeInput =
        $('#toolbar_text_size_input');

    var $textStyle = $('#text_style_input');
    var $textColor = $('#text_color_input');
    var $textShadow = $('#text_shadow_input');
    var $textStrokeColor =
        $('#text_stroke_color_input');

    var $textStrokeWidth =
        $('#text_stroke_width_input');

    var $textStrokePosition =
        $('#text_stroke_position_input');

    var $textLetterSpacing =
        $('#text_letter_spacing_input');

    var $fontFamily =
        $('#font_family_input');

    var $imageLayer =
        $('#image_layer');


    /* Compact */
    var $compactTextSize =
        $('#compact_text_size');

    var $compactLetterSpacing =
        $('#compact_letter_spacing');

    var $compactTextColor =
        $('#compact_text_color');

    var $compactStrokeColor =
        $('#compact_stroke_color');

    var $compactStrokeWidth =
        $('#compact_stroke_width');

    var $compactStrokePos =
        $('#compact_stroke_pos');

    var $compactImageLayer =
        $('#compact_image_layer');

    var $compactX =
        $('#compact_x');

    var $compactY =
        $('#compact_y');


    /* Active text */
    var $activeLayerTextInput =
        $('#active_layer_text_input');

    var $textLayerSelect =
        $('#text_layer_select');


    /* Overlays */
    var $nameOverlay =
        $('#name-overlay');

    var $classOverlay =
        $('#class-overlay');


    /* Hidden text fields */
    var $nameTextInput =
        $('#name_text_input');

    var $classTextInput =
        $('#class_text_input');


    var $nameTextSize =
        $('#name_text_size_input');

    var $nameTextStyle =
        $('#name_text_style_input');

    var $nameTextColor =
        $('#name_text_color_input');

    var $nameTextShadow =
        $('#name_text_shadow_input');

    var $nameTextStrokeColor =
        $('#name_text_stroke_color_input');

    var $nameTextStrokeWidth =
        $('#name_text_stroke_width_input');

    var $nameTextStrokePosition =
        $('#name_text_stroke_position_input');

    var $nameFontFamily =
        $('#name_font_family_input');

    var $nameLetterSpacingInput =
        $('#name_letter_spacing_input');


    var $classTextSize =
        $('#class_text_size_input');

    var $classTextStyle =
        $('#class_text_style_input');

    var $classTextColor =
        $('#class_text_color_input');

    var $classTextShadow =
        $('#class_text_shadow_input');

    var $classTextStrokeColor =
        $('#class_text_stroke_color_input');

    var $classTextStrokeWidth =
        $('#class_text_stroke_width_input');

    var $classTextStrokePosition =
        $('#class_text_stroke_position_input');

    var $classFontFamily =
        $('#class_font_family_input');

    var $classLetterSpacingInput =
        $('#class_letter_spacing_input');


    var scale = 1;

    var mode = '';

    var resizeDir = '';

    var startRect = {};

    var startPointer = {
        x: 0,
        y: 0
    };

    var $activeOverlay = null;


    /*
    |--------------------------------------------------------------------------
    | LAYER FIELDS
    |--------------------------------------------------------------------------
    */

    var layerFields = {

        name: {

            textInput: $nameTextInput,

            textSize: $nameTextSize,

            textStyle: $nameTextStyle,

            textColor: $nameTextColor,

            textShadow: $nameTextShadow,

            textStrokeColor:
                $nameTextStrokeColor,

            textStrokeWidth:
                $nameTextStrokeWidth,

            textStrokePosition:
                $nameTextStrokePosition,

            fontFamily:
                $nameFontFamily,

            letterSpacing:
                $nameLetterSpacingInput
        },


        class: {

            textInput: $classTextInput,

            textSize: $classTextSize,

            textStyle: $classTextStyle,

            textColor: $classTextColor,

            textShadow: $classTextShadow,

            textStrokeColor:
                $classTextStrokeColor,

            textStrokeWidth:
                $classTextStrokeWidth,

            textStrokePosition:
                $classTextStrokePosition,

            fontFamily:
                $classFontFamily,

            letterSpacing:
                $classLetterSpacingInput
        }

    };


    /*
    |--------------------------------------------------------------------------
    | POINTER
    |--------------------------------------------------------------------------
    */

    function getPointer(event) {

        event =
            event.originalEvent || event;


        if (
            event.touches &&
            event.touches.length
        ) {

            return {
                x: event.touches[0].pageX,
                y: event.touches[0].pageY
            };

        }


        if (
            event.changedTouches &&
            event.changedTouches.length
        ) {

            return {
                x: event.changedTouches[0].pageX,
                y: event.changedTouches[0].pageY
            };

        }


        return {

            x:
                event.pageX ||
                event.clientX,

            y:
                event.pageY ||
                event.clientY

        };

    }


    /*
    |--------------------------------------------------------------------------
    | SCALE
    |--------------------------------------------------------------------------
    */

    function updateScale() {

        var naturalWidth =
            $template[0].naturalWidth ||
            $template.width();


        var currentWidth =
            $template.width();


        scale =
            naturalWidth > 0
                ? currentWidth / naturalWidth
                : 1;

    }


    /*
    |--------------------------------------------------------------------------
    | CLAMP IMAGE
    |--------------------------------------------------------------------------
    */

    function clampRect(rect) {

        rect.width =
            Math.max(
                10,
                rect.width
            );


        rect.height =
            Math.max(
                10,
                rect.height
            );


        rect.left =
            Math.max(
                0,
                rect.left
            );


        rect.top =
            Math.max(
                0,
                rect.top
            );


        if (
            rect.left + rect.width >
            $container.width()
        ) {

            rect.left =
                Math.max(
                    0,
                    $container.width() -
                    rect.width
                );

        }


        if (
            rect.top + rect.height >
            $container.height()
        ) {

            rect.top =
                Math.max(
                    0,
                    $container.height() -
                    rect.height
                );

        }


        return rect;

    }


    /*
    |--------------------------------------------------------------------------
    | APPLY IMAGE RECT
    |--------------------------------------------------------------------------
    */

    function applyRect(rect) {

        rect =
            clampRect(rect);


        $wrapper.css({

            left:
                rect.left + 'px',

            top:
                rect.top + 'px',

            width:
                rect.width + 'px',

            height:
                rect.height + 'px'

        });


        updateInputsFromWrapper();

    }


    /*
    |--------------------------------------------------------------------------
    | IMAGE -> INPUTS
    |--------------------------------------------------------------------------
    */

    function updateInputsFromWrapper() {

        var left =
            parseInt(
                $wrapper.css('left'),
                10
            ) || 0;


        var top =
            parseInt(
                $wrapper.css('top'),
                10
            ) || 0;


        var width =
            parseInt(
                $wrapper.width(),
                10
            ) || 1;


        var height =
            parseInt(
                $wrapper.height(),
                10
            ) || 1;


        var x =
            Math.round(left / scale);

        var y =
            Math.round(top / scale);

        var w =
            Math.round(width / scale);

        var h =
            Math.round(height / scale);


        $inputX.val(x);
        $inputY.val(y);
        $inputW.val(w);
        $inputH.val(h);


        $hiddenX.val(x);
        $hiddenY.val(y);
        $hiddenW.val(w);
        $hiddenH.val(h);


        $compactX.val(x);
        $compactY.val(y);

    }


    /*
    |--------------------------------------------------------------------------
    | INPUTS -> IMAGE
    |--------------------------------------------------------------------------
    */

    function updateWrapperFromFields() {

        var x =
            Math.max(
                0,
                parseInt($inputX.val(), 10) || 0
            );


        var y =
            Math.max(
                0,
                parseInt($inputY.val(), 10) || 0
            );


        var w =
            Math.max(
                10,
                parseInt($inputW.val(), 10) || 1
            );


        var h =
            Math.max(
                10,
                parseInt($inputH.val(), 10) || 1
            );


        applyRect({

            left:
                Math.round(x * scale),

            top:
                Math.round(y * scale),

            width:
                Math.round(w * scale),

            height:
                Math.round(h * scale)

        });


        updateOverlays();

    }


    /*
    |--------------------------------------------------------------------------
    | OVERLAY POSITION
    |--------------------------------------------------------------------------
    */

    function updateOverlayPosition(
        $overlay,
        x,
        y
    ) {

        $overlay.css({

            left:
                Math.round(x * scale) +
                'px',

            top:
                Math.round(y * scale) +
                'px'

        });

    }


    /*
    |--------------------------------------------------------------------------
    | FONT
    |--------------------------------------------------------------------------
    */

    function getFontFamilyCss(value) {

        switch (value || 'Poppins') {

            case 'Preeti':
                return 'PreetiLocal, Poppins, DejaVuSansLocal, Arial, sans-serif';

            case 'Ganesh':
                return 'GaneshLocal, Poppins, DejaVuSansLocal, Arial, sans-serif';

            case 'OO1':
                return 'OO1Local, Poppins, DejaVuSansLocal, Arial, sans-serif';

            case 'ArapGraphic':
                return 'ArapGraphicLocal, Poppins, DejaVuSansLocal, Arial, sans-serif';

            case 'Aakriti':
                return 'AakritiLocal, Poppins, DejaVuSansLocal, Arial, sans-serif';

            default:
                return 'Poppins, DejaVuSansLocal, Arial, sans-serif';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | GET LAYER SETTINGS
    |--------------------------------------------------------------------------
    */

    function getLayerSettings(layer) {

        var fields =
            layerFields[layer];


        return {

            text:
                fields.textInput.val() || '',

            size:
                Math.max(
                    8,
                    parseInt(
                        fields.textSize.val(),
                        10
                    ) || 24
                ),

            style:
                fields.textStyle.val() ||
                'bold',

            color:
                fields.textColor.val() ||
                '#0c2b5f',

            shadow:
                fields.textShadow.val() === '1',

            strokeColor:
                fields.textStrokeColor.val() ||
                '#ffffff',

            strokeWidth:
                Math.max(
                    0,
                    parseInt(
                        fields.textStrokeWidth.val(),
                        10
                    ) || 0
                ),

            strokePosition:
                fields.textStrokePosition.val() ||
                'outside',

            fontFamily:
                fields.fontFamily.val() ||
                'Poppins',

            letterSpacing:
                Math.max(
                    -20,
                    Math.min(
                        50,
                        parseInt(
                            fields.letterSpacing.val(),
                            10
                        ) || 0
                    )
                )

        };

    }


    /*
    |--------------------------------------------------------------------------
    | STROKE
    |--------------------------------------------------------------------------
    */

    function buildStrokeShadow(
        strokeWidth,
        strokeColor
    ) {

        var parts = [];


        for (
            var i = 1;
            i <= strokeWidth;
            i++
        ) {

            parts.push(
                -i +
                'px 0 0 ' +
                strokeColor
            );

            parts.push(
                i +
                'px 0 0 ' +
                strokeColor
            );

            parts.push(
                '0 ' +
                -i +
                'px 0 ' +
                strokeColor
            );

            parts.push(
                '0 ' +
                i +
                'px 0 ' +
                strokeColor
            );

            parts.push(
                -i +
                'px ' +
                -i +
                'px 0 ' +
                strokeColor
            );

            parts.push(
                i +
                'px ' +
                -i +
                'px 0 ' +
                strokeColor
            );

            parts.push(
                -i +
                'px ' +
                i +
                'px 0 ' +
                strokeColor
            );

            parts.push(
                i +
                'px ' +
                i +
                'px 0 ' +
                strokeColor
            );

        }


        return parts.join(',');

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE TEXT OVERLAYS
    |--------------------------------------------------------------------------
    */

    function updateOverlays() {

        var nameLeft =
            Math.max(
                0,
                parseInt(
                    $nameX.val(),
                    10
                ) || 0
            );


        var nameTop =
            Math.max(
                0,
                parseInt(
                    $nameY.val(),
                    10
                ) || 0
            );


        var classLeft =
            Math.max(
                0,
                parseInt(
                    $classX.val(),
                    10
                ) || 0
            );


        var classTop =
            Math.max(
                0,
                parseInt(
                    $classY.val(),
                    10
                ) || 0
            );


        var nameSettings =
            getLayerSettings('name');


        var classSettings =
            getLayerSettings('class');


        /*
        |--------------------------------------------------------------------------
        | POSITIONS
        |--------------------------------------------------------------------------
        */

        updateOverlayPosition(
            $nameOverlay,
            nameLeft,
            nameTop
        );


        updateOverlayPosition(
            $classOverlay,
            classLeft,
            classTop
        );


        /*
        |--------------------------------------------------------------------------
        | TEXT
        |--------------------------------------------------------------------------
        */

        $nameOverlay.text(
            $nameTextInput.val() ||
            ''
        );


        $classOverlay.text(
            $classTextInput.val() ||
            ''
        );


        /*
        |--------------------------------------------------------------------------
        | NAME STYLE
        |--------------------------------------------------------------------------
        */

        var nameFontWeight =
            nameSettings.style.indexOf('bold') !== -1
                ? '700'
                : '400';


        var nameFontStyle =
            nameSettings.style.indexOf('italic') !== -1
                ? 'italic'
                : 'normal';


        var nameFontFamilyCss =
            getFontFamilyCss(
                nameSettings.fontFamily
            );


        /*
        |--------------------------------------------------------------------------
        | CLASS STYLE
        |--------------------------------------------------------------------------
        */

        var classFontWeight =
            classSettings.style.indexOf('bold') !== -1
                ? '700'
                : '400';


        var classFontStyle =
            classSettings.style.indexOf('italic') !== -1
                ? 'italic'
                : 'normal';


        var classFontFamilyCss =
            getFontFamilyCss(
                classSettings.fontFamily
            );


        /*
        |--------------------------------------------------------------------------
        | STROKE
        |--------------------------------------------------------------------------
        */

        var nameStrokeCss =
            nameSettings.strokeWidth > 0
                ? nameSettings.strokeWidth +
                  'px ' +
                  nameSettings.strokeColor
                : '0px transparent';


        var classStrokeCss =
            classSettings.strokeWidth > 0
                ? classSettings.strokeWidth +
                  'px ' +
                  classSettings.strokeColor
                : '0px transparent';


        /*
        |--------------------------------------------------------------------------
        | NAME SHADOW
        |--------------------------------------------------------------------------
        */

        var nameTextShadow = 'none';


        if (
            nameSettings.strokeWidth > 0 &&
            nameSettings.strokePosition ===
            'outside'
        ) {

            nameTextShadow =
                buildStrokeShadow(
                    nameSettings.strokeWidth,
                    nameSettings.strokeColor
                );

        } else if (
            nameSettings.shadow
        ) {

            nameTextShadow =
                '2px 2px 8px rgba(0,0,0,0.35)';

        }


        /*
        |--------------------------------------------------------------------------
        | CLASS SHADOW
        |--------------------------------------------------------------------------
        */

        var classTextShadow = 'none';


        if (
            classSettings.strokeWidth > 0 &&
            classSettings.strokePosition ===
            'outside'
        ) {

            classTextShadow =
                buildStrokeShadow(
                    classSettings.strokeWidth,
                    classSettings.strokeColor
                );

        } else if (
            classSettings.shadow
        ) {

            classTextShadow =
                '2px 2px 8px rgba(0,0,0,0.35)';

        }


        /*
        |--------------------------------------------------------------------------
        | APPLY NAME
        |--------------------------------------------------------------------------
        */

        $nameOverlay.css({

            color:
                nameSettings.color,

            fontSize:
                nameSettings.size + 'px',

            fontWeight:
                nameFontWeight,

            fontStyle:
                nameFontStyle,

            textShadow:
                nameTextShadow,

            '-webkit-text-stroke':
                nameSettings.strokePosition ===
                'outside'
                    ? '0px transparent'
                    : nameStrokeCss,

            'font-family':
                nameFontFamilyCss,

            'letter-spacing':
                nameSettings.letterSpacing + 'px',

            lineHeight:
                '1.2'

        });


        /*
        |--------------------------------------------------------------------------
        | APPLY CLASS
        |--------------------------------------------------------------------------
        */

        $classOverlay.css({

            color:
                classSettings.color,

            fontSize:
                classSettings.size + 'px',

            fontWeight:
                classFontWeight,

            fontStyle:
                classFontStyle,

            textShadow:
                classTextShadow,

            '-webkit-text-stroke':
                classSettings.strokePosition ===
                'outside'
                    ? '0px transparent'
                    : classStrokeCss,

            'font-family':
                classFontFamilyCss,

            'letter-spacing':
                classSettings.letterSpacing + 'px',

            lineHeight:
                '1.2'

        });


        /*
        |--------------------------------------------------------------------------
        | SYNC POSITION CONTROLS
        |--------------------------------------------------------------------------
        */

        syncPositionControls();

    }


    /*
    |--------------------------------------------------------------------------
    | SYNC NAME / CLASS X Y CONTROLS
    |--------------------------------------------------------------------------
    */

    function syncPositionControls() {

        var nx =
            parseInt(
                $nameX.val(),
                10
            ) || 0;


        var ny =
            parseInt(
                $nameY.val(),
                10
            ) || 0;


        var cx =
            parseInt(
                $classX.val(),
                10
            ) || 0;


        var cy =
            parseInt(
                $classY.val(),
                10
            ) || 0;


        /*
        | Name row
        */

        $namePositionX.val(nx);
        $namePositionY.val(ny);


        /*
        | Class row
        */

        $classPositionX.val(cx);
        $classPositionY.val(cy);

    }


    /*
    |--------------------------------------------------------------------------
    | ACTIVE LAYER POSITION
    |--------------------------------------------------------------------------
    */

    function updateActiveLayerPositionFields() {

        var isClass =
            $textLayerSelect.val() === 'class';


        var x =
            isClass
                ? (
                    parseInt(
                        $classX.val(),
                        10
                    ) || 0
                )
                : (
                    parseInt(
                        $nameX.val(),
                        10
                    ) || 0
                );


        var y =
            isClass
                ? (
                    parseInt(
                        $classY.val(),
                        10
                    ) || 0
                )
                : (
                    parseInt(
                        $nameY.val(),
                        10
                    ) || 0
                );


        /*
        |--------------------------------------------------------------------------
        | Active fields
        |--------------------------------------------------------------------------
        */

        $('#active_text_x').val(x);
        $('#active_text_y').val(y);


        /*
        |--------------------------------------------------------------------------
        | Keep dedicated fields synchronized
        |--------------------------------------------------------------------------
        */

        if (isClass) {

            $classPositionX.val(x);
            $classPositionY.val(y);

        } else {

            $namePositionX.val(x);
            $namePositionY.val(y);

        }

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE ACTIVE TEXT
    |--------------------------------------------------------------------------
    */

    function updateActiveLayerTextField() {

        var isClass =
            $textLayerSelect.val() === 'class';


        if (isClass) {

            $activeLayerTextInput.val(
                $classTextInput.val()
            );

            $textSize.val(
                $classTextSize.val()
            );

            $toolbarTextSizeInput.val(
                $classTextSize.val()
            );

            $textStyle.val(
                $classTextStyle.val()
            );

            $textColor.val(
                $classTextColor.val()
            );

            $textShadow.prop(
                'checked',
                $classTextShadow.val() === '1'
            );

            $textStrokeColor.val(
                $classTextStrokeColor.val()
            );

            $textStrokeWidth.val(
                $classTextStrokeWidth.val()
            );

            $textStrokePosition.val(
                $classTextStrokePosition.val()
            );

            $fontFamily.val(
                $classFontFamily.val()
            );

            $textLetterSpacing.val(
                $classLetterSpacingInput.val()
            );

        } else {

            $activeLayerTextInput.val(
                $nameTextInput.val()
            );

            $textSize.val(
                $nameTextSize.val()
            );

            $toolbarTextSizeInput.val(
                $nameTextSize.val()
            );

            $textStyle.val(
                $nameTextStyle.val()
            );

            $textColor.val(
                $nameTextColor.val()
            );

            $textShadow.prop(
                'checked',
                $nameTextShadow.val() === '1'
            );

            $textStrokeColor.val(
                $nameTextStrokeColor.val()
            );

            $textStrokeWidth.val(
                $nameTextStrokeWidth.val()
            );

            $textStrokePosition.val(
                $nameTextStrokePosition.val()
            );

            $fontFamily.val(
                $nameFontFamily.val()
            );

            $textLetterSpacing.val(
                $nameLetterSpacingInput.val()
            );

        }


        syncFullToCompact();

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE HIDDEN TEXT FIELDS
    |--------------------------------------------------------------------------
    */

    function updateHiddenTextFields() {

        if (
            $textLayerSelect.val() ===
            'class'
        ) {

            $classTextInput.val(
                $activeLayerTextInput.val()
            );

            $classTextSize.val(
                $textSize.val()
            );

            $classTextStyle.val(
                $textStyle.val()
            );

            $classTextColor.val(
                $textColor.val()
            );

            $classTextShadow.val(
                $textShadow.is(':checked')
                    ? '1'
                    : '0'
            );

            $classTextStrokeColor.val(
                $textStrokeColor.val()
            );

            $classTextStrokeWidth.val(
                $textStrokeWidth.val()
            );

            $classTextStrokePosition.val(
                $textStrokePosition.val()
            );

            $classFontFamily.val(
                $fontFamily.val()
            );

            $classLetterSpacingInput.val(
                $textLetterSpacing.val()
            );

        } else {

            $nameTextInput.val(
                $activeLayerTextInput.val()
            );

            $nameTextSize.val(
                $textSize.val()
            );

            $nameTextStyle.val(
                $textStyle.val()
            );

            $nameTextColor.val(
                $textColor.val()
            );

            $nameTextShadow.val(
                $textShadow.is(':checked')
                    ? '1'
                    : '0'
            );

            $nameTextStrokeColor.val(
                $textStrokeColor.val()
            );

            $nameTextStrokeWidth.val(
                $textStrokeWidth.val()
            );

            $nameTextStrokePosition.val(
                $textStrokePosition.val()
            );

            $nameFontFamily.val(
                $fontFamily.val()
            );

            $nameLetterSpacingInput.val(
                $textLetterSpacing.val()
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | COMPACT -> FULL
    |--------------------------------------------------------------------------
    */

    function syncCompactToFull() {

        var ts =
            Math.max(
                8,
                parseInt(
                    $compactTextSize.val() ||
                    36,
                    10
                )
            );


        var tc =
            $compactTextColor.val() ||
            '#0c2b5f';


        var sc =
            $compactStrokeColor.val() ||
            '#ffffff';


        var sw =
            Math.max(
                0,
                parseInt(
                    $compactStrokeWidth.val() ||
                    0,
                    10
                )
            );


        var sp =
            $compactStrokePos.val() ||
            'outside';


        var ls =
            parseInt(
                $compactLetterSpacing.val() ||
                0,
                10
            );


        var il =
            $compactImageLayer.val() ||
            'front';


        var ox =
            Math.max(
                0,
                parseInt(
                    $compactX.val() ||
                    0,
                    10
                )
            );


        var oy =
            Math.max(
                0,
                parseInt(
                    $compactY.val() ||
                    0,
                    10
                )
            );


        $textSize.val(ts);

        $textColor.val(tc);

        $textStrokeColor.val(sc);

        $textStrokeWidth.val(sw);

        $textStrokePosition.val(sp);

        $textLetterSpacing.val(ls);

        $imageLayer.val(il);

        $inputX.val(ox);

        $inputY.val(oy);

        $hiddenX.val(ox);

        $hiddenY.val(oy);

    }


    /*
    |--------------------------------------------------------------------------
    | FULL -> COMPACT
    |--------------------------------------------------------------------------
    */

    function syncFullToCompact() {

        $compactTextSize.val(
            $textSize.val()
        );

        $compactTextColor.val(
            $textColor.val()
        );

        $compactStrokeColor.val(
            $textStrokeColor.val()
        );

        $compactStrokeWidth.val(
            $textStrokeWidth.val()
        );

        $compactStrokePos.val(
            $textStrokePosition.val()
        );

        $compactLetterSpacing.val(
            $textLetterSpacing.val()
        );

        $compactImageLayer.val(
            $imageLayer.val()
        );

        $compactX.val(
            $inputX.val()
        );

        $compactY.val(
            $inputY.val()
        );

    }


    /*
    |--------------------------------------------------------------------------
    | START ACTION
    |--------------------------------------------------------------------------
    */

    function startAction(
        event,
        action,
        dir,
        overlay
    ) {

        var pointer =
            getPointer(event);


        mode = action;

        resizeDir =
            dir || '';


        startPointer = {

            x:
                pointer.x,

            y:
                pointer.y

        };


        $activeOverlay =
            overlay || null;


        if (
            action === 'overlay'
        ) {

            startRect = {

                left:
                    parseInt(
                        $activeOverlay.css('left'),
                        10
                    ) || 0,

                top:
                    parseInt(
                        $activeOverlay.css('top'),
                        10
                    ) || 0

            };

        } else {

            startRect = {

                left:
                    parseInt(
                        $wrapper.css('left'),
                        10
                    ) || 0,

                top:
                    parseInt(
                        $wrapper.css('top'),
                        10
                    ) || 0,

                width:
                    parseInt(
                        $wrapper.width(),
                        10
                    ) || 1,

                height:
                    parseInt(
                        $wrapper.height(),
                        10
                    ) || 1

            };

        }


        event.preventDefault();
        event.stopPropagation();

    }


    /*
    |--------------------------------------------------------------------------
    | MOVE
    |--------------------------------------------------------------------------
    */

    function handleMove(event) {

        if (!mode) {
            return;
        }


        var pointer =
            getPointer(event);


        var dx =
            pointer.x -
            startPointer.x;


        var dy =
            pointer.y -
            startPointer.y;


        var rect =
            $.extend(
                {},
                startRect
            );


        /*
        |--------------------------------------------------------------------------
        | IMAGE DRAG
        |--------------------------------------------------------------------------
        */

        if (
            mode === 'drag'
        ) {

            rect.left += dx;

            rect.top += dy;

            applyRect(rect);

        }


        /*
        |--------------------------------------------------------------------------
        | IMAGE RESIZE
        |--------------------------------------------------------------------------
        */

        else if (
            mode === 'resize'
        ) {

            if (
                resizeDir.indexOf('n') !== -1
            ) {

                rect.top += dy;

                rect.height -= dy;

            }


            if (
                resizeDir.indexOf('s') !== -1
            ) {

                rect.height += dy;

            }


            if (
                resizeDir.indexOf('w') !== -1
            ) {

                rect.left += dx;

                rect.width -= dx;

            }


            if (
                resizeDir.indexOf('e') !== -1
            ) {

                rect.width += dx;

            }


            applyRect(rect);

        }


        /*
        |--------------------------------------------------------------------------
        | TEXT DRAG
        |--------------------------------------------------------------------------
        */

        else if (
            mode === 'overlay' &&
            $activeOverlay
        ) {

            rect.left += dx;

            rect.top += dy;


            var clamped =
                clampRect({

                    left:
                        rect.left,

                    top:
                        rect.top,

                    width:
                        $activeOverlay.outerWidth(),

                    height:
                        $activeOverlay.outerHeight()

                });


            $activeOverlay.css({

                left:
                    clamped.left + 'px',

                top:
                    clamped.top + 'px'

            });


            /*
            |--------------------------------------------------------------------------
            | NAME
            |--------------------------------------------------------------------------
            */

            if (
                $activeOverlay.is(
                    $nameOverlay
                )
            ) {

                var newNameX =
                    Math.round(
                        clamped.left /
                        scale
                    );


                var newNameY =
                    Math.round(
                        clamped.top /
                        scale
                    );


                $nameX.val(newNameX);

                $nameY.val(newNameY);


                $namePositionX.val(
                    newNameX
                );

                $namePositionY.val(
                    newNameY
                );


                $textLayerSelect.val(
                    'name'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | CLASS
            |--------------------------------------------------------------------------
            */

            else if (
                $activeOverlay.is(
                    $classOverlay
                )
            ) {

                var newClassX =
                    Math.round(
                        clamped.left /
                        scale
                    );


                var newClassY =
                    Math.round(
                        clamped.top /
                        scale
                    );


                $classX.val(newClassX);

                $classY.val(newClassY);


                $classPositionX.val(
                    newClassX
                );

                $classPositionY.val(
                    newClassY
                );


                $textLayerSelect.val(
                    'class'
                );

            }


            updateActiveLayerPositionFields();

        }


        event.preventDefault();

    }


    /*
    |--------------------------------------------------------------------------
    | STOP
    |--------------------------------------------------------------------------
    */

    function stopAction() {

        mode = '';

        resizeDir = '';

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE LAYER
    |--------------------------------------------------------------------------
    */

    function updateLayerPreview() {

        if (
            $imageLayer.val() ===
            'back'
        ) {

            $template.css(
                'z-index',
                2
            );

            $wrapper.css(
                'z-index',
                1
            );

        } else {

            $template.css(
                'z-index',
                1
            );

            $wrapper.css(
                'z-index',
                2
            );

        }


        $nameOverlay.css(
            'z-index',
            3
        );

        $classOverlay.css(
            'z-index',
            3
        );

    }


    /*
    |--------------------------------------------------------------------------
    | INIT
    |--------------------------------------------------------------------------
    */

    function init() {

        /*
        |--------------------------------------------------------------------------
        | TEMPLATE LOAD
        |--------------------------------------------------------------------------
        */

        if (
            $template[0].complete
        ) {

            updateScale();

            updateWrapperFromFields();

        } else {

            $template.on(
                'load',
                function() {

                    updateScale();

                    updateWrapperFromFields();

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | IMAGE DRAG
        |--------------------------------------------------------------------------
        */

        $wrapper.on(
            'mousedown touchstart',
            function(event) {

                if (
                    $(event.target).hasClass(
                        'resize-handle'
                    )
                ) {

                    return;

                }


                startAction(
                    event,
                    'drag'
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | RESIZE
        |--------------------------------------------------------------------------
        */

        $('.resize-handle').on(
            'mousedown touchstart',
            function(event) {

                startAction(
                    event,
                    'resize',
                    $(event.currentTarget).data(
                        'dir'
                    )
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | NAME DRAG
        |--------------------------------------------------------------------------
        */

        $nameOverlay.on(
            'mousedown touchstart',
            function(event) {

                $textLayerSelect.val(
                    'name'
                );

                updateActiveLayerTextField();

                updateActiveLayerPositionFields();

                startAction(
                    event,
                    'overlay',
                    '',
                    $nameOverlay
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | CLASS DRAG
        |--------------------------------------------------------------------------
        */

        $classOverlay.on(
            'mousedown touchstart',
            function(event) {

                $textLayerSelect.val(
                    'class'
                );

                updateActiveLayerTextField();

                updateActiveLayerPositionFields();

                startAction(
                    event,
                    'overlay',
                    '',
                    $classOverlay
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | MOUSE MOVE
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'mousemove touchmove',
            handleMove
        );


        $(document).on(
            'mouseup touchend touchcancel mouseleave',
            stopAction
        );


        /*
        |--------------------------------------------------------------------------
        | SELECT
        |--------------------------------------------------------------------------
        */

        $(document).on(
            'selectstart',
            function(event) {

                if (mode) {

                    event.preventDefault();

                    return false;

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | IMAGE POSITION INPUTS
        |--------------------------------------------------------------------------
        */

        $inputX
            .add($inputY)
            .add($inputW)
            .add($inputH)
            .on(
                'input change',
                function() {

                    updateScale();

                    updateWrapperFromFields();

                    updateOverlays();

                    updateLayerPreview();

                }
            );


        /*
        |--------------------------------------------------------------------------
        | NAME X/Y
        |--------------------------------------------------------------------------
        */

        $namePositionX
            .add($namePositionY)
            .on(
                'input change',
                function() {

                    stopAction();


                    var x =
                        Math.max(
                            0,
                            parseInt(
                                $namePositionX.val(),
                                10
                            ) || 0
                        );


                    var y =
                        Math.max(
                            0,
                            parseInt(
                                $namePositionY.val(),
                                10
                            ) || 0
                        );


                    $nameX.val(x);

                    $nameY.val(y);


                    updateOverlays();

                    updateActiveLayerPositionFields();

                }
            );


        /*
        |--------------------------------------------------------------------------
        | CLASS X/Y
        |--------------------------------------------------------------------------
        */

        $classPositionX
            .add($classPositionY)
            .on(
                'input change',
                function() {

                    stopAction();


                    var x =
                        Math.max(
                            0,
                            parseInt(
                                $classPositionX.val(),
                                10
                            ) || 0
                        );


                    var y =
                        Math.max(
                            0,
                            parseInt(
                                $classPositionY.val(),
                                10
                            ) || 0
                        );


                    $classX.val(x);

                    $classY.val(y);


                    updateOverlays();

                    updateActiveLayerPositionFields();

                }
            );


        /*
        |--------------------------------------------------------------------------
        | ACTIVE LAYER SELECT
        |--------------------------------------------------------------------------
        */

        $textLayerSelect.on(
            'change',
            function() {

                stopAction();

                updateActiveLayerTextField();

                updateActiveLayerPositionFields();

                updateOverlays();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | ACTIVE TEXT INPUT
        |--------------------------------------------------------------------------
        */

        $activeLayerTextInput.on(
            'input',
            function() {

                stopAction();

                updateHiddenTextFields();

                updateOverlays();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | TEXT SETTINGS
        |--------------------------------------------------------------------------
        */

        $textSize
            .add($textStyle)
            .add($textColor)
            .add($textShadow)
            .add($textStrokeColor)
            .add($textStrokeWidth)
            .add($textStrokePosition)
            .add($textLetterSpacing)
            .add($fontFamily)
            .on(
                'input change',
                function() {

                    updateHiddenTextFields();

                    syncFullToCompact();

                    stopAction();

                    updateOverlays();

                }
            );


        /*
        |--------------------------------------------------------------------------
        | IMAGE LAYER
        |--------------------------------------------------------------------------
        */

        $imageLayer.on(
            'input change',
            function() {

                stopAction();

                syncFullToCompact();

                updateLayerPreview();

                updateOverlays();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | COMPACT TEXT CONTROLS
        |--------------------------------------------------------------------------
        */

        $compactTextSize
            .add($compactTextColor)
            .add($compactStrokeColor)
            .add($compactStrokeWidth)
            .add($compactStrokePos)
            .add($compactLetterSpacing)
            .on(
                'input change',
                function() {

                    syncCompactToFull();

                    updateHiddenTextFields();

                    stopAction();

                    updateOverlays();

                }
            );


        /*
        |--------------------------------------------------------------------------
        | COMPACT IMAGE X/Y
        |--------------------------------------------------------------------------
        */

        $compactX
            .add($compactY)
            .on(
                'input change',
                function() {

                    syncCompactToFull();

                    stopAction();

                    updateWrapperFromFields();

                    updateOverlays();

                    updateLayerPreview();

                }
            );


        /*
        |--------------------------------------------------------------------------
        | COMPACT LAYER
        |--------------------------------------------------------------------------
        */

        $compactImageLayer.on(
            'input change',
            function() {

                syncCompactToFull();

                stopAction();

                updateLayerPreview();

                updateOverlays();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | TOOLBAR BOLD
        |--------------------------------------------------------------------------
        */

        $('#btn-bold').on(
            'click',
            function() {

                var cur =
                    $textStyle.val() ||
                    'bold';


                if (
                    cur.indexOf('bold') === -1
                ) {

                    $textStyle.val(
                        'bold'
                    );

                } else {

                    $textStyle.val(
                        'normal'
                    );

                }


                updateHiddenTextFields();

                syncFullToCompact();

                stopAction();

                updateOverlays();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | TOOLBAR ITALIC
        |--------------------------------------------------------------------------
        */

        $('#btn-italic').on(
            'click',
            function() {

                var cur =
                    $textStyle.val() ||
                    'bold';


                if (
                    cur.indexOf('italic') === -1
                ) {

                    if (
                        cur.indexOf('bold') !== -1
                    ) {

                        $textStyle.val(
                            'bold-italic'
                        );

                    } else {

                        $textStyle.val(
                            'italic'
                        );

                    }

                } else {

                    if (
                        cur.indexOf('bold') !== -1
                    ) {

                        $textStyle.val(
                            'bold'
                        );

                    } else {

                        $textStyle.val(
                            'normal'
                        );

                    }

                }


                updateHiddenTextFields();

                syncFullToCompact();

                stopAction();

                updateOverlays();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | INCREASE SIZE
        |--------------------------------------------------------------------------
        */

        $('#btn-size-incr').on(
            'click',
            function() {

                var current =
                    Math.max(
                        8,
                        parseInt(
                            $textSize.val() ||
                            36,
                            10
                        )
                    );


                current += 2;


                $textSize.val(
                    current
                );

                $toolbarTextSizeInput.val(
                    current
                );


                updateHiddenTextFields();

                syncFullToCompact();

                stopAction();

                updateOverlays();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | DECREASE SIZE
        |--------------------------------------------------------------------------
        */

        $('#btn-size-decr').on(
            'click',
            function() {

                var current =
                    Math.max(
                        8,
                        parseInt(
                            $textSize.val() ||
                            36,
                            10
                        )
                    );


                current =
                    Math.max(
                        8,
                        current - 2
                    );


                $textSize.val(
                    current
                );

                $toolbarTextSizeInput.val(
                    current
                );


                updateHiddenTextFields();

                syncFullToCompact();

                stopAction();

                updateOverlays();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | TOOLBAR SIZE INPUT
        |--------------------------------------------------------------------------
        */

        $toolbarTextSizeInput.on(
            'input change',
            function() {

                var value =
                    Math.max(
                        8,
                        parseInt(
                            $(this).val(),
                            10
                        ) || 36
                    );


                $textSize.val(value);

                updateHiddenTextFields();

                syncFullToCompact();

                updateOverlays();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | TOGGLE COMPACT
        |--------------------------------------------------------------------------
        */

        $('#toggle_compact').on(
            'click',
            function() {

                $('.editor-panel-body')
                    .toggleClass(
                        'compact-ui'
                    );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | STOP WHEN INPUT SELECTED
        |--------------------------------------------------------------------------
        */

        $('input, select').on(
            'mousedown touchstart',
            function() {

                stopAction();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | PREVENT IMAGE DRAG
        |--------------------------------------------------------------------------
        */

        $student.on(
            'dragstart',
            function(event) {

                event.preventDefault();

                return false;

            }
        );


        /*
        |--------------------------------------------------------------------------
        | WINDOW RESIZE
        |--------------------------------------------------------------------------
        */

        $(window).on(
            'resize',
            function() {

                updateScale();

                updateWrapperFromFields();

                updateOverlays();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | INITIAL STATE
        |--------------------------------------------------------------------------
        */

        $wrapper.css(
            'touch-action',
            'none'
        );

        $nameOverlay.css(
            'touch-action',
            'none'
        );

        $classOverlay.css(
            'touch-action',
            'none'
        );


        updateScale();

        updateActiveLayerTextField();

        syncPositionControls();

        updateActiveLayerPositionFields();

        updateOverlays();

        updateLayerPreview();

    }


    $(document).ready(init);


})(jQuery);
</script>


<!-- ================================================================ -->
<!-- DOWNLOAD PREVIEW -->
<!-- ================================================================ -->

<script>
(function() {

    var btn =
        document.getElementById(
            'download-preview'
        );


    if (!btn) {
        return;
    }


    btn.addEventListener(
        'click',
        function() {

            var container =
                document.getElementById(
                    'preview-container'
                );


            var template =
                document.getElementById(
                    'template-preview'
                );


            if (
                !container ||
                !template
            ) {

                alert(
                    'Preview not ready'
                );

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | WAIT FOR FONTS
            |--------------------------------------------------------------------------
            */

            var ready = (
                document.fonts &&
                document.fonts.ready
            )
                ? document.fonts.ready
                : Promise.resolve();


            ready.then(
                function() {

                    /*
                    |--------------------------------------------------------------------------
                    | ORIGINAL IMAGE SIZE
                    |--------------------------------------------------------------------------
                    */

                    var natural =
                        template.naturalWidth ||
                        template.width ||
                        container.clientWidth;


                    var displayed =
                        template.clientWidth ||
                        container.clientWidth;


                    var ratio =
                        displayed > 0
                            ? natural / displayed
                            : 1;


                    /*
                    |--------------------------------------------------------------------------
                    | HIDE EDITOR ELEMENTS
                    |--------------------------------------------------------------------------
                    */

                    var wrapperEl =
                        document.getElementById(
                            'student-wrapper'
                        );

                    var classOverlay =
                        document.getElementById(
                            'class-overlay'
                        );


                    var handles =
                        container.querySelectorAll(
                            '.resize-handle'
                        );


                    var origBorder =
                        wrapperEl
                            ? wrapperEl.style.border
                            : '';

                    var origClassTop =
                        classOverlay
                            ? classOverlay.style.top
                            : '';


                    var origHandles = [];


                    handles.forEach(
                        function(handle) {

                            origHandles.push(
                                handle.style.display
                            );

                            handle.style.display =
                                'none';

                        }
                    );


                    if (wrapperEl) {

                        wrapperEl.style.border =
                            '2px solid transparent';

                    }

                    if (classOverlay) {
                        classOverlay.style.top =
                            (parseFloat(getComputedStyle(classOverlay).top) + 6) + 'px';
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CAPTURE
                    |--------------------------------------------------------------------------
                    */

                    html2canvas(
                        container,
                        {

                            scale:
                                Math.max(
                                    1,
                                    ratio
                                ),

                            useCORS:
                                true,

                            backgroundColor:
                                '#ffffff'

                        }
                    )


                    /*
                    |--------------------------------------------------------------------------
                    | CANVAS -> JPG
                    |--------------------------------------------------------------------------
                    */

                    .then(
                        function(canvas) {

                            return new Promise(
                                function(resolve) {

                                    canvas.toBlob(
                                        function(blob) {

                                            resolve(
                                                blob
                                            );

                                        },
                                        'image/jpeg',
                                        0.95
                                    );

                                }
                            );

                        }
                    )


                    /*
                    |--------------------------------------------------------------------------
                    | DOWNLOAD
                    |--------------------------------------------------------------------------
                    */

                    .then(
                        function(blob) {

                            if (!blob) {

                                alert(
                                    'Failed to generate JPG'
                                );

                                return;

                            }


                            var a =
                                document.createElement(
                                    'a'
                                );


                            var fname =
                                'birthday-' +
                                (
                                    <?php
                                    echo json_encode(
                                        $studentName
                                    );
                                    ?>
                                    ||
                                    'card'
                                ) +
                                '-' +
                                Date.now() +
                                '.jpg';


                            var url =
                                URL.createObjectURL(
                                    blob
                                );


                            a.href = url;


                            a.download =
                                fname.replace(
                                    /[^a-zA-Z0-9_\-\.]/g,
                                    '_'
                                );


                            document.body.appendChild(
                                a
                            );


                            a.click();


                            a.remove();


                            setTimeout(
                                function() {

                                    URL.revokeObjectURL(
                                        url
                                    );

                                },
                                5000
                            );

                        }
                    )


                    /*
                    |--------------------------------------------------------------------------
                    | ERROR
                    |--------------------------------------------------------------------------
                    */

                    .catch(
                        function(err) {

                            alert(
                                'JPG generation failed: ' +
                                (
                                    err &&
                                    err.message
                                        ? err.message
                                        : err
                                )
                            );

                        }
                    )


                    /*
                    |--------------------------------------------------------------------------
                    | RESTORE
                    |--------------------------------------------------------------------------
                    */

                    .finally(
                        function() {

                            if (wrapperEl) {

                                wrapperEl.style.border =
                                    origBorder;

                            }

                            if (classOverlay) {
                                classOverlay.style.top =
                                    origClassTop;
                            }


                            handles.forEach(
                                function(
                                    handle,
                                    index
                                ) {

                                    handle.style.display =
                                        origHandles[index] ||
                                        '';

                                }
                            );

                        }
                    );

                }
            );

        }
    );

})();
</script>