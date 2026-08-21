<?php require_once('header.php'); ?>

<?php
ensureBirthdayTables($pdo);

if (!isset($_REQUEST['id'])) {
    header('location: birthday.php');
    exit;
}

$id = (int)$_REQUEST['id'];
$statement = $pdo->prepare("SELECT s.*, t.template_image, t.output_x, t.output_y, t.output_width, t.output_height FROM tbl_birthday_student s LEFT JOIN tbl_birthday_template t ON t.id=s.template_id WHERE s.id=?");
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

$output_x = (int)$row['output_x'];
$output_y = (int)$row['output_y'];
$output_width = (int)$row['output_width'];
$output_height = (int)$row['output_height'];
$name_x = $output_x;
$name_y = $output_y + $output_height + 20;
$class_x = $output_x + 20;
$class_y = $name_y + 52;
$text_size = 36;
$text_color = '#0c2b5f';
$text_style = 'bold';
$text_shadow = '1';
$text_stroke_color = '#ffffff';
$text_stroke_width = 0;
$text_stroke_position = 'outside';
$image_layer = 'front';
$font_family = 'Poppins';

$name_text_size = $text_size;
$name_text_style = $text_style;
$name_text_color = $text_color;
$name_text_shadow = $text_shadow;
$name_text_stroke_color = $text_stroke_color;
$name_text_stroke_width = $text_stroke_width;
$name_text_stroke_position = $text_stroke_position;
$name_font_family = $font_family;
$name_letter_spacing = 0;

$class_text_size = max(14, (int) round($text_size * 0.65));
$class_text_style = $text_style;
$class_text_color = $text_color;
$class_text_shadow = $text_shadow;
$class_text_stroke_color = $text_stroke_color;
$class_text_stroke_width = $text_stroke_width;
$class_text_stroke_position = $text_stroke_position;
$class_font_family = $font_family;
$class_letter_spacing = 0;

$success_message = '';
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_card'])) {
    $output_x = max(0, (int)($_POST['output_x'] ?? $output_x));
    $output_y = max(0, (int)($_POST['output_y'] ?? $output_y));
    $output_width = max(1, (int)($_POST['output_width'] ?? $output_width));
    $output_height = max(1, (int)($_POST['output_height'] ?? $output_height));
    $name_x = max(0, (int)($_POST['name_x'] ?? $name_x));
    $name_y = max(0, (int)($_POST['name_y'] ?? $name_y));
    $class_x = max(0, (int)($_POST['class_x'] ?? $class_x));
    $class_y = max(0, (int)($_POST['class_y'] ?? $class_y));
    $text_size = max(8, (int)($_POST['text_size'] ?? $text_size));
    $text_color = preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $_POST['text_color'] ?? '') ? $_POST['text_color'] : $text_color;
    $text_style = in_array($_POST['text_style'] ?? 'bold', array('normal','bold','italic','bold-italic'), true) ? $_POST['text_style'] : 'bold';
    $text_shadow = isset($_POST['text_shadow']) && $_POST['text_shadow'] === '1' ? '1' : '0';
    $text_stroke_color = preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $_POST['text_stroke_color'] ?? '') ? $_POST['text_stroke_color'] : $text_stroke_color;
    $text_stroke_width = max(0, (int)($_POST['text_stroke_width'] ?? $text_stroke_width));
    $text_stroke_position = in_array($_POST['text_stroke_position'] ?? 'outside', array('outside','center','inside'), true) ? $_POST['text_stroke_position'] : 'outside';
    $image_layer = in_array($_POST['image_layer'] ?? 'front', array('front','back'), true) ? $_POST['image_layer'] : 'front';
    // Allow overriding name/class from editor inputs (so edited text is used when generating)
    $studentName = isset($_POST['name_text']) ? trim($_POST['name_text']) : $studentName;
    $className = isset($_POST['class_text']) ? trim($_POST['class_text']) : $className;
    $font_family = in_array($_POST['font_family'] ?? 'Poppins', array('Poppins','Preeti','Ganesh','OO1','ArapGraphic','Aakriti'), true) ? $_POST['font_family'] : 'Poppins';

    $name_text_size = max(8, (int)($_POST['name_text_size'] ?? $text_size));
    $name_text_style = in_array($_POST['name_text_style'] ?? $text_style, array('normal','bold','italic','bold-italic'), true) ? $_POST['name_text_style'] : $text_style;
    $name_text_color = preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $_POST['name_text_color'] ?? '') ? $_POST['name_text_color'] : $text_color;
    $name_text_shadow = isset($_POST['name_text_shadow']) && $_POST['name_text_shadow'] === '1' ? '1' : '0';
    $name_text_stroke_color = preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $_POST['name_text_stroke_color'] ?? '') ? $_POST['name_text_stroke_color'] : $text_stroke_color;
    $name_text_stroke_width = max(0, (int)($_POST['name_text_stroke_width'] ?? $text_stroke_width));
    $name_text_stroke_position = in_array($_POST['name_text_stroke_position'] ?? $text_stroke_position, array('outside','center','inside'), true) ? $_POST['name_text_stroke_position'] : $text_stroke_position;
    $name_font_family = in_array($_POST['name_font_family'] ?? $font_family, array('Poppins','Preeti','Ganesh','OO1','ArapGraphic','Aakriti'), true) ? $_POST['name_font_family'] : $font_family;
    $name_letter_spacing = max(-20, min(50, (int)($_POST['name_letter_spacing'] ?? 0)));

    $class_text_size = max(8, (int)($_POST['class_text_size'] ?? max(14, (int)round($text_size * 0.65))));
    $class_text_style = in_array($_POST['class_text_style'] ?? $text_style, array('normal','bold','italic','bold-italic'), true) ? $_POST['class_text_style'] : $text_style;
    $class_text_color = preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $_POST['class_text_color'] ?? '') ? $_POST['class_text_color'] : $text_color;
    $class_text_shadow = isset($_POST['class_text_shadow']) && $_POST['class_text_shadow'] === '1' ? '1' : '0';
    $class_text_stroke_color = preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $_POST['class_text_stroke_color'] ?? '') ? $_POST['class_text_stroke_color'] : $text_stroke_color;
    $class_text_stroke_width = max(0, (int)($_POST['class_text_stroke_width'] ?? $text_stroke_width));
    $class_text_stroke_position = in_array($_POST['class_text_stroke_position'] ?? $text_stroke_position, array('outside','center','inside'), true) ? $_POST['class_text_stroke_position'] : $text_stroke_position;
    $class_font_family = in_array($_POST['class_font_family'] ?? $font_family, array('Poppins','Preeti','Ganesh','OO1','ArapGraphic','Aakriti'), true) ? $_POST['class_font_family'] : $font_family;
    $class_letter_spacing = max(-20, min(50, (int)($_POST['class_letter_spacing'] ?? 0)));

    $outputName = 'birthday-' . $id . '-' . time() . '.jpg';
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
        'text_stroke_color' => $text_stroke_color,
        'text_stroke_width' => $text_stroke_width,
        'text_stroke_position' => $text_stroke_position,
        'font_family' => $font_family,
        'name_text_size' => $name_text_size,
        'name_text_style' => $name_text_style,
        'name_text_color' => $name_text_color,
        'name_text_shadow' => $name_text_shadow,
        'name_text_stroke_color' => $name_text_stroke_color,
        'name_text_stroke_width' => $name_text_stroke_width,
        'name_text_stroke_position' => $name_text_stroke_position,
        'name_font_family' => $name_font_family,
        'name_letter_spacing' => $name_letter_spacing,
        'class_text_size' => $class_text_size,
        'class_text_style' => $class_text_style,
        'class_text_color' => $class_text_color,
        'class_text_shadow' => $class_text_shadow,
        'class_text_stroke_color' => $class_text_stroke_color,
        'class_text_stroke_width' => $class_text_stroke_width,
        'class_text_stroke_position' => $class_text_stroke_position,
        'class_font_family' => $class_font_family,
        'class_letter_spacing' => $class_letter_spacing,
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
        $pdo->prepare("UPDATE tbl_birthday_student SET generated_image=? WHERE id=?")->execute(array($outputName, $id));
        $success_message = 'Birthday card generated successfully. Use the button below to download it.';
    } else {
        $error_message = $result['error'];
    }
}
?>

<section class="content-header">
    <div class="content-header-left"><h1>Generate Birthday Card</h1></div>
    <div class="content-header-right"><a href="birthday.php" class="btn btn-primary btn-sm">Back</a></div>
</section>

<section class="content">
    <?php if ($error_message): ?>
        <div class="row">
            <div class="col-md-12"><div class="callout callout-danger"><p><?php echo $error_message; ?></p></div></div>
        </div>
    <?php endif; ?>

    <form method="post" action="" class="form-horizontal">
        <input type="hidden" name="generate_card" value="1">
        <input type="hidden" id="output_x" name="output_x" value="<?php echo $output_x; ?>">
        <input type="hidden" id="output_y" name="output_y" value="<?php echo $output_y; ?>">
        <input type="hidden" id="output_width" name="output_width" value="<?php echo $output_width; ?>">
        <input type="hidden" id="output_height" name="output_height" value="<?php echo $output_height; ?>">
        <input type="hidden" id="name_x" name="name_x" value="<?php echo $name_x; ?>">
        <input type="hidden" id="name_y" name="name_y" value="<?php echo $name_y; ?>">
        <input type="hidden" id="class_x" name="class_x" value="<?php echo $class_x; ?>">
        <input type="hidden" id="class_y" name="class_y" value="<?php echo $class_y; ?>">

        <div class="row">
            <div class="col-md-8">
                <div class="box box-info">
                    <div class="box-header with-border"><h3 class="box-title">Drag Student Photo to Position</h3></div>
                    <div class="box-body">
                        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
                        <style>
                            @font-face { font-family: 'DejaVuSansLocal'; src: url('../vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf') format('truetype'); font-weight: normal; }
                            @font-face { font-family: 'DejaVuSansLocal'; src: url('../vendor/dompdf/dompdf/lib/fonts/DejaVuSans-Bold.ttf') format('truetype'); font-weight: bold; }
                            #name-overlay, #class-overlay { font-family: 'Poppins', 'DejaVuSansLocal', Arial, sans-serif; }
                            #preview-container { padding:12px; background:#fbfbfb; box-shadow:0 6px 18px rgba(0,0,0,0.06); border-radius:6px; }
                            .box.box-info { margin-bottom:18px; }
                            .form-group { margin-bottom:12px; }
                            .editor-toolbar-2 input { margin-right:6px; width:45%; display:inline-block; }
                            .compact-controls .form-control.input-sm { width:80px; display:inline-block; margin-right:6px; }
                            .compact-controls .btn { padding:4px 6px; }
                            .compact-controls .control-item { display:flex; align-items:center; gap:4px; margin-right:6px; }
                            .compact-controls .control-item i { color:#777; font-size:14px; }
                            .compact-controls .control-item input,
                            .compact-controls .control-item select { width:80px; }
                            .compact-controls .btn { min-width:96px; }
                            .editor-panel { background:#fff; border:1px solid #dae1e7; border-radius:8px; padding:12px; margin-bottom:10px; box-shadow:0 1px 4px rgba(0,0,0,0.05); }
                            .editor-panel-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
                            .editor-panel-header .panel-title { font-weight:700; color:#2c3e50; font-size:14px; }
                            .editor-panel-body { display:grid; gap:10px; }
                            .panel-toolbar { display:flex; flex-wrap:wrap; gap:8px; align-items:center; justify-content:space-between; }
                            .panel-toolbar .toolbar-group { display:flex; gap:6px; align-items:center; }
                            .panel-toolbar .font-group { flex:0 1 220px; min-width:140px; }
                            .font-select { width:100%; min-width:140px; max-width:240px; }
                            .panel-item .input-group .form-control { width:100%; }
                            .panel-grid { display:grid; grid-template-columns:repeat(2,minmax(120px,1fr)); gap:10px; }
                            .panel-group .tool-button { border-color:#ced4da; }
                            .panel-grid { display:grid; grid-template-columns:repeat(2,minmax(120px,1fr)); gap:10px; }
                            .panel-item { display:flex; flex-direction:column; }
                            .panel-item label { font-size:11px; color:#6c757d; margin-bottom:4px; }
                            .panel-item .input-group { width:100%; }
                            .panel-actions { display:flex; justify-content:flex-end; gap:8px; align-items:center; }
                            .panel-actions .btn { min-width:112px; }
                            .editor-toolbar.compact-toolbar { justify-content:flex-start; align-items:center; margin-bottom:0; }
                            .editor-toolbar.compact-toolbar .toolbar-group { display:flex; gap:6px; align-items:center; }
                            .tool-button { min-width:38px; display:flex; justify-content:center; align-items:center; padding:6px 8px; border-radius:4px; border:1px solid #d2d6de; background:#fff; color:#444; }
                            .tool-button:hover { background:#f5f7fa; border-color:#c6c8cc; }
                            .inline-field { width:100%; max-width:280px; }
                            .compact-panel { background:#f7f7f7; border:1px solid #e1e1e1; border-radius:6px; padding:8px; }
                            .box-body > .form-group { display:none; }
                            .compact-ui .form-group { display:none; }
                            .compact-ui .form-control { padding:4px 6px; height:30px; font-size:13px; }
                            .compact-ui .box-body { padding:8px; }
                            .compact-ui .editor-toolbar,
                            .compact-ui .editor-toolbar-2,
                            .compact-ui .compact-controls { display:flex; flex-wrap:wrap; gap:6px; }
                            .compact-ui .editor-toolbar-2 input { width:calc(50% - 8px); }
                            .compact-ui .compact-row { width:100%; display:flex; flex-wrap:wrap; gap:6px; align-items:center; }
                            .compact-ui .compact-row .form-control.input-sm { width:auto; flex:1 1 80px; }
                            .compact-ui .compact-row button { flex:0 0 auto; }
                            .compact-ui .box-body > .form-group:first-child { display:none; }
                            .editor-toolbar { display:flex; gap:6px; align-items:center; margin-bottom:8px; }
                            .editor-toolbar .btn { padding:4px 6px; font-size:12px; }
                            .font-select { width:140px; }
                            @font-face { font-family: 'PreetiLocal'; src: url('../assets/fonts/Preeti.ttf') format('truetype'); }
                            @font-face { font-family: 'GaneshLocal'; src: url('../assets/fonts/Ganesh.ttf') format('truetype'); }
                            @font-face { font-family: 'OO1Local'; src: url('../assets/fonts/OO1.ttf') format('truetype'); }
                            @font-face { font-family: 'ArapGraphicLocal'; src: url('../assets/fonts/ArapGraphic.ttf') format('truetype'); }
                            @font-face { font-family: 'AakritiLocal'; src: url('../assets/fonts/Aakriti.ttf') format('truetype'); }
                        </style>
                        <div id="preview-container" style="position:relative; border:1px solid #ddd; background:#fff; max-width:100%; display:inline-block; overflow:hidden; touch-action:none;">
                            <img id="template-preview" src="<?php echo htmlspecialchars(adminUploadUrl($templateImage)); ?>" alt="Template Preview" style="display:block; width:100%; height:auto; position:relative; z-index:1;">
                            <div id="student-wrapper" style="position:absolute; left:<?php echo $output_x; ?>px; top:<?php echo $output_y; ?>px; width:<?php echo $output_width; ?>px; height:<?php echo $output_height; ?>px; cursor:move; z-index:2; box-sizing:border-box; border:2px dashed #00a65a; touch-action:none;">
                                <img id="student-preview" src="<?php echo htmlspecialchars(adminUploadUrl($studentImage)); ?>" alt="Student Preview" draggable="false" style="width:100%; height:100%; object-fit:cover; user-drag:none; user-select:none; -webkit-user-drag:none; -webkit-touch-callout:none;">
                                <div class="resize-handle" data-dir="nw" style="position:absolute; width:12px; height:12px; background:#00a65a; border:2px solid #fff; top:-8px; left:-8px; cursor:nwse-resize;"></div>
                                <div class="resize-handle" data-dir="ne" style="position:absolute; width:12px; height:12px; background:#00a65a; border:2px solid #fff; top:-8px; right:-8px; cursor:nesw-resize;"></div>
                                <div class="resize-handle" data-dir="sw" style="position:absolute; width:12px; height:12px; background:#00a65a; border:2px solid #fff; bottom:-8px; left:-8px; cursor:nesw-resize;"></div>
                                <div class="resize-handle" data-dir="se" style="position:absolute; width:12px; height:12px; background:#00a65a; border:2px solid #fff; bottom:-8px; right:-8px; cursor:nwse-resize;"></div>
                            </div>
                            <div id="name-overlay" style="position:absolute; left:<?php echo $name_x; ?>px; top:<?php echo $name_y; ?>px; color:#0c2b5f; font-weight:700; font-size:22px; line-height:1.2; text-shadow:2px 2px 6px rgba(0,0,0,.35); z-index:3; white-space:nowrap; cursor:move; touch-action:none; -webkit-text-stroke:<?php echo $text_stroke_width; ?>px <?php echo $text_stroke_color; ?>;">
                                <?php echo htmlspecialchars($studentName); ?>
                            </div>
                            <div id="class-overlay" style="position:absolute; left:<?php echo $class_x; ?>px; top:<?php echo $class_y; ?>px; color:#0c2b5f; font-weight:600; font-size:16px; line-height:1.2; text-shadow:2px 2px 6px rgba(0,0,0,.35); z-index:3; white-space:nowrap; cursor:move; touch-action:none; -webkit-text-stroke:<?php echo $text_stroke_width; ?>px <?php echo $text_stroke_color; ?>;">
                                <?php echo htmlspecialchars($className); ?>
                            </div>
                        </div>
                        <p class="help-block">Drag the student image over the template and update the numeric fields for precise placement.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box box-info">
                    <div class="box-header with-border"><h3 class="box-title">Position / Size</h3></div>
                    <div class="box-body">
                        <div class="editor-panel">
                            <div class="editor-panel-header">
                                <span class="panel-title">Text & Layer Controls</span>
                                <button type="button" id="toggle_compact" class="btn btn-default btn-sm tool-button" title="Toggle compact UI"><i class="fa fa-compress"></i></button>
                            </div>
                            <div class="editor-panel-body">
                                <div class="panel-toolbar">
                                    <div class="toolbar-group">
                                        <button type="button" id="btn-bold" class="btn btn-default btn-sm tool-button" title="Bold"><i class="fa fa-bold"></i></button>
                                        <button type="button" id="btn-italic" class="btn btn-default btn-sm tool-button" title="Italic"><i class="fa fa-italic"></i></button>
                                        <button type="button" id="btn-size-decr" class="btn btn-default btn-sm tool-button" title="Decrease size"><i class="fa fa-minus"></i></button>
                                        <button type="button" id="btn-size-incr" class="btn btn-default btn-sm tool-button" title="Increase size"><i class="fa fa-plus"></i></button>
                                        <input id="toolbar_text_size_input" type="number" class="form-control input-sm" value="<?php echo $text_size; ?>" min="8" step="1" style="width:80px; margin-left:6px;">
                                    </div>
                                    <div class="toolbar-group font-group">
                                        <div class="input-group input-group-sm" style="width:100%; max-width:230px;">
                                            <span class="input-group-addon"><i class="fa fa-font"></i></span>
                                            <select id="font_family_input" name="font_family" class="form-control font-select" style="font-size:13px;">
                                                <option value="Poppins"<?php echo $font_family === 'Poppins' ? ' selected' : ''; ?>>Poppins</option>
                                                <option value="Preeti"<?php echo $font_family === 'Preeti' ? ' selected' : ''; ?>>Preeti (Nepali)</option>
                                                <option value="Ganesh"<?php echo $font_family === 'Ganesh' ? ' selected' : ''; ?>>Ganesh (Nepali)</option>
                                                <option value="OO1"<?php echo $font_family === 'OO1' ? ' selected' : ''; ?>>OO1 (Nepali)</option>
                                                <option value="ArapGraphic"<?php echo $font_family === 'ArapGraphic' ? ' selected' : ''; ?>>Arap Graphic (Nepali)</option>
                                                <option value="Aakriti"<?php echo $font_family === 'Aakriti' ? ' selected' : ''; ?>>Aakriti (Nepali)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-group">
                                    <div class="panel-item">
                                        <label>Text layer</label>
                                        <select id="text_layer_select" class="form-control">
                                            <option value="name">Name</option>
                                            <option value="class">Class</option>
                                        </select>
                                    </div>
                                    <div class="panel-item">
                                        <label>Text</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"><i class="fa fa-pencil-alt"></i></span>
                                            <input type="text" id="active_layer_text_input" class="form-control" placeholder="Text" value="<?php echo htmlspecialchars($studentName); ?>">
                                        </div>
                                    </div>
                                    <input type="hidden" id="name_text_input" name="name_text" value="<?php echo htmlspecialchars($studentName); ?>">
                                    <input type="hidden" id="class_text_input" name="class_text" value="<?php echo htmlspecialchars($className); ?>">
                                    <input type="hidden" id="name_text_size_input" name="name_text_size" value="<?php echo $text_size; ?>">
                                    <input type="hidden" id="name_text_style_input" name="name_text_style" value="<?php echo htmlspecialchars($text_style); ?>">
                                    <input type="hidden" id="name_text_color_input" name="name_text_color" value="<?php echo htmlspecialchars($text_color); ?>">
                                    <input type="hidden" id="name_text_shadow_input" name="name_text_shadow" value="<?php echo $text_shadow; ?>">
                                    <input type="hidden" id="name_text_stroke_color_input" name="name_text_stroke_color" value="<?php echo htmlspecialchars($text_stroke_color); ?>">
                                    <input type="hidden" id="name_text_stroke_width_input" name="name_text_stroke_width" value="<?php echo (int)$text_stroke_width; ?>">
                                    <input type="hidden" id="name_text_stroke_position_input" name="name_text_stroke_position" value="<?php echo htmlspecialchars($text_stroke_position); ?>">
                                    <input type="hidden" id="name_font_family_input" name="name_font_family" value="<?php echo htmlspecialchars($font_family); ?>">
                                    <input type="hidden" id="name_letter_spacing_input" name="name_letter_spacing" value="0">
                                    <input type="hidden" id="class_text_size_input" name="class_text_size" value="<?php echo max(14, (int)round($text_size * 0.65)); ?>">
                                    <input type="hidden" id="class_text_style_input" name="class_text_style" value="<?php echo htmlspecialchars($text_style); ?>">
                                    <input type="hidden" id="class_text_color_input" name="class_text_color" value="<?php echo htmlspecialchars($text_color); ?>">
                                    <input type="hidden" id="class_text_shadow_input" name="class_text_shadow" value="<?php echo $text_shadow; ?>">
                                    <input type="hidden" id="class_text_stroke_color_input" name="class_text_stroke_color" value="<?php echo htmlspecialchars($text_stroke_color); ?>">
                                    <input type="hidden" id="class_text_stroke_width_input" name="class_text_stroke_width" value="<?php echo (int)$text_stroke_width; ?>">
                                    <input type="hidden" id="class_text_stroke_position_input" name="class_text_stroke_position" value="<?php echo htmlspecialchars($text_stroke_position); ?>">
                                    <input type="hidden" id="class_font_family_input" name="class_font_family" value="<?php echo htmlspecialchars($font_family); ?>">
                                    <input type="hidden" id="class_letter_spacing_input" name="class_letter_spacing" value="0">
                                </div>
                                <div class="panel-grid">
                                    <div class="panel-item">
                                        <label>Text size</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"><i class="fa fa-text-height"></i></span>
                                            <input id="compact_text_size" type="number" class="form-control" value="<?php echo $text_size; ?>" min="8" title="Text size">
                                        </div>
                                    </div>
                                    <div class="panel-item">
                                        <label>Text color</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"><i class="fa fa-fill-drip"></i></span>
                                            <input id="compact_text_color" type="color" class="form-control" value="<?php echo htmlspecialchars($text_color); ?>" title="Text color">
                                        </div>
                                    </div>
                                    <div class="panel-item">
                                        <label>Stroke color</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"><i class="fa fa-paint-brush"></i></span>
                                            <input id="compact_stroke_color" type="color" class="form-control" value="<?php echo htmlspecialchars($text_stroke_color); ?>" title="Stroke color">
                                        </div>
                                    </div>
                                    <div class="panel-item">
                                        <label>Stroke width</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"><i class="fa fa-minus-square"></i></span>
                                            <input id="compact_stroke_width" type="number" class="form-control" value="<?php echo (int)$text_stroke_width; ?>" min="0" max="10" title="Stroke width">
                                        </div>
                                    </div>
                                    <div class="panel-item">
                                        <label>Tracking</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"><i class="fa fa-text-width"></i></span>
                                            <input id="compact_letter_spacing" type="number" class="form-control" value="0" min="-10" max="30" title="Character spacing">
                                        </div>
                                    </div>
                                    <div class="panel-item">
                                        <label>Stroke position</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"><i class="fa fa-layer-group"></i></span>
                                            <select id="compact_stroke_pos" class="form-control" title="Stroke position">
                                                <option value="outside"<?php echo $text_stroke_position==='outside'?' selected':''; ?>>Outside</option>
                                                <option value="center"<?php echo $text_stroke_position==='center'?' selected':''; ?>>Center</option>
                                                <option value="inside"<?php echo $text_stroke_position==='inside'?' selected':''; ?>>Inside</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="panel-item">
                                        <label>Layer</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"><i class="fa fa-clone"></i></span>
                                            <select id="compact_image_layer" class="form-control" title="Image layer">
                                                <option value="front"<?php echo $image_layer==='front'?' selected':''; ?>>Front</option>
                                                <option value="back"<?php echo $image_layer==='back'?' selected':''; ?>>Back</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="panel-item">
                                        <label>X</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"><i class="fa fa-arrows-alt-h"></i></span>
                                            <input id="compact_x" type="number" class="form-control" value="<?php echo $output_x; ?>" title="X">
                                        </div>
                                    </div>
                                    <div class="panel-item">
                                        <label>Y</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-addon"><i class="fa fa-arrows-alt-v"></i></span>
                                            <input id="compact_y" type="number" class="form-control" value="<?php echo $output_y; ?>" title="Y">
                                        </div>
                                    </div>
                                    <div class="panel-item panel-actions">
                                         <button type="button" id="download-preview" class="btn btn-primary btn-sm" title="Download Preview"><i class="fa fa-check"></i> Download Preview</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">X</label>
                            <input type="number" id="input_x" class="form-control" value="<?php echo $output_x; ?>" min="0">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Y</label>
                            <input type="number" id="input_y" class="form-control" value="<?php echo $output_y; ?>" min="0">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Width</label>
                            <input type="number" id="input_width" class="form-control" value="<?php echo $output_width; ?>" min="1">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Height</label>
                            <input type="number" id="input_height" class="form-control" value="<?php echo $output_height; ?>" min="1">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Name X</label>
                            <input type="number" id="name_x_input" class="form-control" value="<?php echo $name_x; ?>" min="0">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Name Y</label>
                            <input type="number" id="name_y_input" class="form-control" value="<?php echo $name_y; ?>" min="0">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Class X</label>
                            <input type="number" id="class_x_input" class="form-control" value="<?php echo $class_x; ?>" min="0">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Class Y</label>
                            <input type="number" id="class_y_input" class="form-control" value="<?php echo $class_y; ?>" min="0">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Text Size</label>
                            <input type="number" id="text_size_input" class="form-control" name="text_size" value="<?php echo $text_size; ?>" min="8">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Tracking</label>
                            <input type="number" id="text_letter_spacing_input" class="form-control" name="letter_spacing" value="0" min="-10" max="30">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Stroke Color</label>
                            <input type="color" id="text_stroke_color_input" name="text_stroke_color" class="form-control" value="<?php echo htmlspecialchars($text_stroke_color); ?>">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Stroke Width</label>
                            <input type="number" id="text_stroke_width_input" name="text_stroke_width" class="form-control" value="<?php echo (int)$text_stroke_width; ?>" min="0" max="10">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Stroke Position</label>
                            <select id="text_stroke_position_input" name="text_stroke_position" class="form-control">
                                <option value="outside"<?php echo $text_stroke_position === 'outside' ? ' selected' : ''; ?>>Outside</option>
                                <option value="center"<?php echo $text_stroke_position === 'center' ? ' selected' : ''; ?>>Center</option>
                                <option value="inside"<?php echo $text_stroke_position === 'inside' ? ' selected' : ''; ?>>Inside (approx)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Font Style</label>
                            <select id="text_style_input" name="text_style" class="form-control">
                                <option value="normal"<?php echo $text_style === 'normal' ? ' selected' : ''; ?>>Normal</option>
                                <option value="bold"<?php echo $text_style === 'bold' ? ' selected' : ''; ?>>Bold</option>
                                <option value="italic"<?php echo $text_style === 'italic' ? ' selected' : ''; ?>>Italic</option>
                                <option value="bold-italic"<?php echo $text_style === 'bold-italic' ? ' selected' : ''; ?>>Bold Italic</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Text Color</label>
                            <input type="color" id="text_color_input" class="form-control" name="text_color" value="<?php echo htmlspecialchars($text_color); ?>">
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="text_shadow_input" name="text_shadow" value="1"<?php echo $text_shadow === '1' ? ' checked' : ''; ?>> Enable drop shadow
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Image Layer</label>
                            <select id="image_layer" name="image_layer" class="form-control">
                                <option value="front"<?php echo $image_layer === 'front' ? ' selected' : ''; ?>>Front</option>
                                <option value="back"<?php echo $image_layer === 'back' ? ' selected' : ''; ?>>Back</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="button" id="apply-position" class="btn btn-default">Apply</button>
                            <button type="button" id="download-preview" class="btn btn-info">Download Preview</button>
                            <button type="submit" class="btn btn-success pull-right">Generate Image</button>
                        </div>
                    </div>
                </div>
                <?php if ($success_message): ?>
                    <div class="box box-success">
                        <div class="box-body">
                            <p><?php echo $success_message; ?></p>
                            <a href="birthday-download.php?id=<?php echo (int)$id; ?>" class="btn btn-info">Download Generated Image</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</section>

<?php require_once('footer.php'); ?>

<script>
(function($) {
    var $template = $('#template-preview');
    var $wrapper = $('#student-wrapper');
    var $student = $('#student-preview');
    var $container = $('#preview-container');
    var $inputX = $('#input_x');
    var $inputY = $('#input_y');
    var $inputW = $('#input_width');
    var $inputH = $('#input_height');
    var $nameX = $('#name_x');
    var $nameY = $('#name_y');
    var $classX = $('#class_x');
    var $classY = $('#class_y');
    var $textSize = $('#text_size_input');
    var $toolbarTextSizeInput = $('#toolbar_text_size_input');
    var $textStyle = $('#text_style_input');
    var $textColor = $('#text_color_input');
    var $textShadow = $('#text_shadow_input');
    var $textStrokeColor = $('#text_stroke_color_input');
    var $textStrokeWidth = $('#text_stroke_width_input');
    var $textStrokePosition = $('#text_stroke_position_input');
    var $textLetterSpacing = $('#text_letter_spacing_input');
    var $fontFamily = $('#font_family_input');
    var $imageLayer = $('#image_layer');
    var $compactTextSize = $('#compact_text_size');
    var $compactLetterSpacing = $('#compact_letter_spacing');
    var $compactTextColor = $('#compact_text_color');
    var $compactStrokeColor = $('#compact_stroke_color');
    var $compactStrokeWidth = $('#compact_stroke_width');
    var $compactStrokePos = $('#compact_stroke_pos');
    var $compactImageLayer = $('#compact_image_layer');
    var $compactX = $('#compact_x');
    var $compactY = $('#compact_y');
    var $hiddenX = $('#output_x');
    var $hiddenY = $('#output_y');
    var $hiddenW = $('#output_width');
    var $hiddenH = $('#output_height');
    var $nameOverlay = $('#name-overlay');
    var $classOverlay = $('#class-overlay');
    var $nameTextInput = $('#name_text_input');
    var $classTextInput = $('#class_text_input');
    var $nameTextSize = $('#name_text_size_input');
    var $nameTextStyle = $('#name_text_style_input');
    var $nameTextColor = $('#name_text_color_input');
    var $nameTextShadow = $('#name_text_shadow_input');
    var $nameTextStrokeColor = $('#name_text_stroke_color_input');
    var $nameTextStrokeWidth = $('#name_text_stroke_width_input');
    var $nameTextStrokePosition = $('#name_text_stroke_position_input');
    var $nameFontFamily = $('#name_font_family_input');
    var $nameLetterSpacingInput = $('#name_letter_spacing_input');
    var $classTextSize = $('#class_text_size_input');
    var $classTextStyle = $('#class_text_style_input');
    var $classTextColor = $('#class_text_color_input');
    var $classTextShadow = $('#class_text_shadow_input');
    var $classTextStrokeColor = $('#class_text_stroke_color_input');
    var $classTextStrokeWidth = $('#class_text_stroke_width_input');
    var $classTextStrokePosition = $('#class_text_stroke_position_input');
    var $classFontFamily = $('#class_font_family_input');
    var $classLetterSpacingInput = $('#class_letter_spacing_input');
    var $activeLayerTextInput = $('#active_layer_text_input');
    var $textLayerSelect = $('#text_layer_select');
    var $activeOverlay = null;

    var layerFields = {
        name: {
            textInput: $nameTextInput,
            textSize: $nameTextSize,
            textStyle: $nameTextStyle,
            textColor: $nameTextColor,
            textShadow: $nameTextShadow,
            textStrokeColor: $nameTextStrokeColor,
            textStrokeWidth: $nameTextStrokeWidth,
            textStrokePosition: $nameTextStrokePosition,
            fontFamily: $nameFontFamily,
            letterSpacing: $nameLetterSpacingInput
        },
        class: {
            textInput: $classTextInput,
            textSize: $classTextSize,
            textStyle: $classTextStyle,
            textColor: $classTextColor,
            textShadow: $classTextShadow,
            textStrokeColor: $classTextStrokeColor,
            textStrokeWidth: $classTextStrokeWidth,
            textStrokePosition: $classTextStrokePosition,
            fontFamily: $classFontFamily,
            letterSpacing: $classLetterSpacingInput
        }
    };

    var scale = 1;
    var mode = '';
    var resizeDir = '';
    var startRect = {};
    var startPointer = { x: 0, y: 0 };

    function getPointer(event) {
        event = event.originalEvent || event;
        if (event.touches && event.touches.length) {
            return { x: event.touches[0].pageX, y: event.touches[0].pageY };
        }
        if (event.changedTouches && event.changedTouches.length) {
            return { x: event.changedTouches[0].pageX, y: event.changedTouches[0].pageY };
        }
        return { x: event.pageX || event.clientX, y: event.pageY || event.clientY };
    }

    function updateScale() {
        var naturalWidth = $template[0].naturalWidth || $template.width();
        var currentWidth = $template.width();
        scale = naturalWidth > 0 ? currentWidth / naturalWidth : 1;
    }

    function clampRect(rect) {
        rect.width = Math.max(10, rect.width);
        rect.height = Math.max(10, rect.height);
        rect.left = Math.max(0, rect.left);
        rect.top = Math.max(0, rect.top);
        if (rect.left + rect.width > $container.width()) {
            rect.left = Math.max(0, $container.width() - rect.width);
        }
        if (rect.top + rect.height > $container.height()) {
            rect.top = Math.max(0, $container.height() - rect.height);
        }
        return rect;
    }

    function applyRect(rect) {
        rect = clampRect(rect);
        $wrapper.css({
            left: rect.left + 'px',
            top: rect.top + 'px',
            width: rect.width + 'px',
            height: rect.height + 'px'
        });
        updateInputsFromWrapper();
    }

    function updateInputsFromWrapper() {
        var left = parseInt($wrapper.css('left'), 10) || 0;
        var top = parseInt($wrapper.css('top'), 10) || 1;
        var width = parseInt($wrapper.width(), 10) || 1;
        var height = parseInt($wrapper.height(), 10) || 1;

        $inputX.val(Math.round(left / scale));
        $inputY.val(Math.round(top / scale));
        $inputW.val(Math.round(width / scale));
        $inputH.val(Math.round(height / scale));
        $hiddenX.val(Math.round(left / scale));
        $hiddenY.val(Math.round(top / scale));
        $hiddenW.val(Math.round(width / scale));
        $hiddenH.val(Math.round(height / scale));
    }

    function updateWrapperFromFields() {
        var x = Math.max(0, parseInt($inputX.val(), 10) || 0);
        var y = Math.max(0, parseInt($inputY.val(), 10) || 0);
        var w = Math.max(10, parseInt($inputW.val(), 10) || 1);
        var h = Math.max(10, parseInt($inputH.val(), 10) || 1);
        applyRect({
            left: Math.round(x * scale),
            top: Math.round(y * scale),
            width: Math.round(w * scale),
            height: Math.round(h * scale)
        });
        updateOverlays();
    }

    function updateOverlayPosition($overlay, x, y) {
        $overlay.css({
            left: Math.round(x * scale) + 'px',
            top: Math.round(y * scale) + 'px'
        });
    }

    function getFontFamilyCss(value) {
        switch ((value || 'Poppins')) {
            case 'Preeti': return 'PreetiLocal, Poppins, DejaVuSansLocal, Arial, sans-serif';
            case 'Ganesh': return 'GaneshLocal, Poppins, DejaVuSansLocal, Arial, sans-serif';
            case 'OO1': return 'OO1Local, Poppins, DejaVuSansLocal, Arial, sans-serif';
            case 'ArapGraphic': return 'ArapGraphicLocal, Poppins, DejaVuSansLocal, Arial, sans-serif';
            case 'Aakriti': return 'AakritiLocal, Poppins, DejaVuSansLocal, Arial, sans-serif';
            default: return 'Poppins, DejaVuSansLocal, Arial, sans-serif';
        }
    }

    function getLayerSettings(layer) {
        var fields = layerFields[layer];
        return {
            text: fields.textInput.val() || '',
            size: Math.max(8, parseInt(fields.textSize.val(), 10) || 24),
            style: fields.textStyle.val() || 'bold',
            color: fields.textColor.val() || '#0c2b5f',
            shadow: fields.textShadow.val() === '1',
            strokeColor: fields.textStrokeColor.val() || '#ffffff',
            strokeWidth: Math.max(0, parseInt(fields.textStrokeWidth.val(), 10) || 0),
            strokePosition: fields.textStrokePosition.val() || 'outside',
            fontFamily: fields.fontFamily.val() || 'Poppins',
            letterSpacing: Math.max(-20, Math.min(50, parseInt(fields.letterSpacing.val(), 10) || 0))
        };
    }

    function buildStrokeShadow(strokeWidth, strokeColor) {
        var parts = [];
        for (var i = 1; i <= strokeWidth; i++) {
            parts.push(-i + 'px 0 0 ' + strokeColor);
            parts.push(i + 'px 0 0 ' + strokeColor);
            parts.push('0 ' + -i + 'px 0 ' + strokeColor);
            parts.push('0 ' + i + 'px 0 ' + strokeColor);
            parts.push(-i + 'px ' + -i + 'px 0 ' + strokeColor);
            parts.push(i + 'px ' + -i + 'px 0 ' + strokeColor);
            parts.push(-i + 'px ' + i + 'px 0 ' + strokeColor);
            parts.push(i + 'px ' + i + 'px 0 ' + strokeColor);
        }
        return parts.join(',');
    }

    function updateOverlays() {
        var nameLeft = Math.max(0, parseInt($nameX.val(), 10) || 0);
        var nameTop = Math.max(0, parseInt($nameY.val(), 10) || 0);
        var classLeft = Math.max(0, parseInt($classX.val(), 10) || 0);
        var classTop = Math.max(0, parseInt($classY.val(), 10) || 0);

        var nameSettings = getLayerSettings('name');
        var classSettings = getLayerSettings('class');

        updateOverlayPosition($nameOverlay, nameLeft, nameTop);
        updateOverlayPosition($classOverlay, classLeft, classTop);

        if ($nameTextInput && $nameTextInput.length) {
            $nameOverlay.text($nameTextInput.val() || '<?php echo addslashes($studentName); ?>');
        }
        if ($classTextInput && $classTextInput.length) {
            $classOverlay.text($classTextInput.val() || '<?php echo addslashes($className); ?>');
        }

        var nameFontWeight = nameSettings.style.indexOf('bold') !== -1 ? '700' : '400';
        var nameFontStyle = nameSettings.style.indexOf('italic') !== -1 ? 'italic' : 'normal';
        var nameFontFamilyCss = getFontFamilyCss(nameSettings.fontFamily);
        var classFontWeight = classSettings.style.indexOf('bold') !== -1 ? '700' : '400';
        var classFontStyle = classSettings.style.indexOf('italic') !== -1 ? 'italic' : 'normal';
        var classFontFamilyCss = getFontFamilyCss(classSettings.fontFamily);

        var nameStrokeCss = (nameSettings.strokeWidth > 0) ? (nameSettings.strokeWidth + 'px ' + nameSettings.strokeColor) : '0px transparent';
        var classStrokeCss = (classSettings.strokeWidth > 0) ? (classSettings.strokeWidth + 'px ' + classSettings.strokeColor) : '0px transparent';

        var nameTextShadow = 'none';
        if (nameSettings.strokeWidth > 0 && nameSettings.strokePosition === 'outside') {
            nameTextShadow = buildStrokeShadow(nameSettings.strokeWidth, nameSettings.strokeColor);
        } else if (nameSettings.shadow) {
            nameTextShadow = '2px 2px 8px rgba(0,0,0,0.35)';
        }

        var classTextShadow = 'none';
        if (classSettings.strokeWidth > 0 && classSettings.strokePosition === 'outside') {
            classTextShadow = buildStrokeShadow(classSettings.strokeWidth, classSettings.strokeColor);
        } else if (classSettings.shadow) {
            classTextShadow = '2px 2px 8px rgba(0,0,0,0.35)';
        }

        $nameOverlay.css({
            color: nameSettings.color,
            fontSize: nameSettings.size + 'px',
            fontWeight: nameFontWeight,
            fontStyle: nameFontStyle,
            textShadow: nameTextShadow,
            '-webkit-text-stroke': nameSettings.strokePosition === 'outside' ? '0px transparent' : nameStrokeCss,
            'font-family': nameFontFamilyCss,
            'letter-spacing': nameSettings.letterSpacing + 'px',
            lineHeight: '1.2'
        });

        $classOverlay.css({
            color: classSettings.color,
            fontSize: classSettings.size + 'px',
            fontWeight: classFontWeight,
            fontStyle: classFontStyle,
            textShadow: classTextShadow,
            '-webkit-text-stroke': classSettings.strokePosition === 'outside' ? '0px transparent' : classStrokeCss,
            'font-family': classFontFamilyCss,
            'letter-spacing': classSettings.letterSpacing + 'px',
            lineHeight: '1.2'
        });
    }

    function updateLayerPreview() {
        if ($imageLayer.val() === 'back') {
            $template.css('z-index', 2);
            $wrapper.css('z-index', 1);
        } else {
            $template.css('z-index', 1);
            $wrapper.css('z-index', 2);
        }
        $nameOverlay.css('z-index', 3);
        $classOverlay.css('z-index', 3);
    }

    function startAction(event, action, dir, overlay) {
        var pointer = getPointer(event);
        mode = action;
        resizeDir = dir || '';
        startPointer = { x: pointer.x, y: pointer.y };
        $activeOverlay = overlay || null;

        if (action === 'overlay') {
            startRect = {
                left: parseInt($activeOverlay.css('left'), 10) || 0,
                top: parseInt($activeOverlay.css('top'), 10) || 0
            };
        } else {
            startRect = {
                left: parseInt($wrapper.css('left'), 10) || 0,
                top: parseInt($wrapper.css('top'), 10) || 0,
                width: parseInt($wrapper.width(), 10) || 1,
                height: parseInt($wrapper.height(), 10) || 1
            };
        }

        event.preventDefault();
        event.stopPropagation();
    }

    function handleMove(event) {
        if (!mode) return;
        var pointer = getPointer(event);
        var dx = pointer.x - startPointer.x;
        var dy = pointer.y - startPointer.y;
        var rect = $.extend({}, startRect);

        if (mode === 'drag') {
            rect.left += dx;
            rect.top += dy;
            applyRect(rect);
        } else if (mode === 'resize') {
            if (resizeDir.indexOf('n') !== -1) {
                rect.top += dy;
                rect.height -= dy;
            }
            if (resizeDir.indexOf('s') !== -1) {
                rect.height += dy;
            }
            if (resizeDir.indexOf('w') !== -1) {
                rect.left += dx;
                rect.width -= dx;
            }
            if (resizeDir.indexOf('e') !== -1) {
                rect.width += dx;
            }
            applyRect(rect);
        } else if (mode === 'overlay' && $activeOverlay) {
            rect.left += dx;
            rect.top += dy;
            var clamped = clampRect({ left: rect.left, top: rect.top, width: $activeOverlay.outerWidth(), height: $activeOverlay.outerHeight() });
            $activeOverlay.css({ left: clamped.left + 'px', top: clamped.top + 'px' });
            if ($activeOverlay.is($nameOverlay)) {
                $nameX.val(Math.round(clamped.left / scale));
                $nameY.val(Math.round(clamped.top / scale));
            } else if ($activeOverlay.is($classOverlay)) {
                $classX.val(Math.round(clamped.left / scale));
                $classY.val(Math.round(clamped.top / scale));
            }
        }

        event.preventDefault();
    }

    function stopAction() {
        mode = '';
        resizeDir = '';
    }

    function init() {
        if ($template[0].complete) {
            updateScale();
            updateWrapperFromFields();
        } else {
            $template.on('load', function() {
                updateScale();
                updateWrapperFromFields();
            });
        }

        $wrapper.on('mousedown touchstart', function(event) {
            if ($(event.target).hasClass('resize-handle')) {
                return;
            }
            startAction(event, 'drag');
        });

        $('.resize-handle').on('mousedown touchstart', function(event) {
            startAction(event, 'resize', $(event.currentTarget).data('dir'));
        });

        $nameOverlay.on('mousedown touchstart', function(event) {
            startAction(event, 'overlay', '', $nameOverlay);
        });

        $classOverlay.on('mousedown touchstart', function(event) {
            startAction(event, 'overlay', '', $classOverlay);
        });

        $(document).on('mousemove touchmove', handleMove);
        $(document).on('mouseup touchend touchcancel mouseleave', stopAction);

        $(document).on('selectstart', function(event) {
            if (mode) {
                event.preventDefault();
                return false;
            }
        });

        $('#apply-position').on('click', function() {
            updateScale();
            updateWrapperFromFields();
        });

        var $nameXInput = $('#name_x_input');
        var $nameYInput = $('#name_y_input');
        var $classXInput = $('#class_x_input');
        var $classYInput = $('#class_y_input');

        $inputX.add($inputY).add($inputW).add($inputH).add($nameX).add($nameY).add($classX).add($classY).on('input change', function() {
            updateScale();
            updateWrapperFromFields();
            updateOverlays();
            updateLayerPreview();
            updatePositionFields();
        });

        function updatePositionFields() {
            $('#name_x_input').val($nameX.val());
            $('#name_y_input').val($nameY.val());
            $('#class_x_input').val($classX.val());
            $('#class_y_input').val($classY.val());
        }

        function updateActiveLayerTextField() {
            if ($textLayerSelect.val() === 'class') {
                $activeLayerTextInput.val($classTextInput.val());
                $textSize.val($classTextSize.val());
                $toolbarTextSizeInput.val($classTextSize.val());
                $textStyle.val($classTextStyle.val());
                $textColor.val($classTextColor.val());
                $textShadow.prop('checked', $classTextShadow.val() === '1');
                $textStrokeColor.val($classTextStrokeColor.val());
                $textStrokeWidth.val($classTextStrokeWidth.val());
                $textStrokePosition.val($classTextStrokePosition.val());
                $fontFamily.val($classFontFamily.val());
                $textLetterSpacing.val($classLetterSpacingInput.val());
            } else {
                $activeLayerTextInput.val($nameTextInput.val());
                $textSize.val($nameTextSize.val());
                $toolbarTextSizeInput.val($nameTextSize.val());
                $textStyle.val($nameTextStyle.val());
                $textColor.val($nameTextColor.val());
                $textShadow.prop('checked', $nameTextShadow.val() === '1');
                $textStrokeColor.val($nameTextStrokeColor.val());
                $textStrokeWidth.val($nameTextStrokeWidth.val());
                $textStrokePosition.val($nameTextStrokePosition.val());
                $fontFamily.val($nameFontFamily.val());
                $textLetterSpacing.val($nameLetterSpacingInput.val());
            }

            syncFullToCompact();
        }

        function updateHiddenTextFields() {
            if ($textLayerSelect.val() === 'class') {
                $classTextInput.val($activeLayerTextInput.val());
                $classTextSize.val($textSize.val());
                $classTextStyle.val($textStyle.val());
                $classTextColor.val($textColor.val());
                $classTextShadow.val($textShadow.is(':checked') ? '1' : '0');
                $classTextStrokeColor.val($textStrokeColor.val());
                $classTextStrokeWidth.val($textStrokeWidth.val());
                $classTextStrokePosition.val($textStrokePosition.val());
                $classFontFamily.val($fontFamily.val());
                $classLetterSpacingInput.val($textLetterSpacing.val());
            } else {
                $nameTextInput.val($activeLayerTextInput.val());
                $nameTextSize.val($textSize.val());
                $nameTextStyle.val($textStyle.val());
                $nameTextColor.val($textColor.val());
                $nameTextShadow.val($textShadow.is(':checked') ? '1' : '0');
                $nameTextStrokeColor.val($textStrokeColor.val());
                $nameTextStrokeWidth.val($textStrokeWidth.val());
                $nameTextStrokePosition.val($textStrokePosition.val());
                $nameFontFamily.val($fontFamily.val());
                $nameLetterSpacingInput.val($textLetterSpacing.val());
            }
        }

        $textLayerSelect.on('change', function() {
            updateActiveLayerTextField();
            stopAction();
            updateOverlays();
        });

        $activeLayerTextInput.on('input', function() {
            updateHiddenTextFields();
            stopAction();
            updateOverlays();
        });

        $textSize.add($textStyle).add($textColor).add($textShadow).add($textStrokeColor).add($textStrokeWidth).add($textStrokePosition).add($textLetterSpacing).add($fontFamily).on('input change', function() {
            updateHiddenTextFields();
            stopAction();
            syncFullToCompact();
            updateOverlays();
        });

        $imageLayer.on('input change', function() {
            stopAction();
            syncFullToCompact();
            updateLayerPreview();
        });

        $fontFamily.on('change', function(){ stopAction(); updateOverlays(); });

        function syncCompactToFull() {
            var ts = Math.max(8, parseInt($compactTextSize.val()||36,10));
            var tc = $compactTextColor.val() || '#0c2b5f';
            var sc = $compactStrokeColor.val() || '#ffffff';
            var sw = Math.max(0, parseInt($compactStrokeWidth.val()||0,10));
            var sp = $compactStrokePos.val() || 'outside';
            var ls = parseInt($compactLetterSpacing.val()||0,10);
            var il = $compactImageLayer.val() || 'front';
            var ox = Math.max(0, parseInt($compactX.val()||0,10));
            var oy = Math.max(0, parseInt($compactY.val()||0,10));

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

        function syncFullToCompact() {
            $compactTextSize.val($textSize.val());
            $compactTextColor.val($textColor.val());
            $compactStrokeColor.val($textStrokeColor.val());
            $compactStrokeWidth.val($textStrokeWidth.val());
            $compactStrokePos.val($textStrokePosition.val());
            $compactLetterSpacing.val($textLetterSpacing.val());
            $compactImageLayer.val($imageLayer.val());
            $compactX.val($inputX.val());
            $compactY.val($inputY.val());
        }

        var $compactStyleControls = $compactTextSize.add($compactTextColor).add($compactStrokeColor).add($compactStrokeWidth).add($compactStrokePos).add($compactLetterSpacing);
        var $compactPlacementControls = $compactX.add($compactY);
        var $compactLayerControl = $compactImageLayer;

        $compactStyleControls.on('input change', function() {
            syncCompactToFull();
            updateHiddenTextFields();
            stopAction();
            updateOverlays();
        });

        $compactPlacementControls.on('input change', function() {
            syncCompactToFull();
            stopAction();
            updateWrapperFromFields();
            updateOverlays();
            updateLayerPreview();
        });

        $compactLayerControl.on('input change', function() {
            syncCompactToFull();
            stopAction();
            updateLayerPreview();
            updateOverlays();
        });

        $textSize.add($textStyle).add($textColor).add($textShadow).add($textStrokeColor).add($textStrokeWidth).add($textStrokePosition).add($textLetterSpacing).on('input change', function() {
            syncFullToCompact();
            stopAction();
            updateOverlays();
        });

        $imageLayer.on('input change', function() {
            stopAction();
            syncFullToCompact();
            updateLayerPreview();
            updateOverlays();
        });

        // compact controls: apply to all
        $('#apply_all_compact').on('click', function(){
            stopAction();
            syncCompactToFull();
            var ts = Math.max(8, parseInt($compactTextSize.val()||36,10));
            var ox = Math.max(0, parseInt($compactX.val()||0,10));
            var oy = Math.max(0, parseInt($compactY.val()||0,10));

            $nameX.val(ox);
            $nameY.val(oy + Math.round(ts * 1.2));
            $classX.val(ox);
            $classY.val(oy + Math.round(ts * 1.2) + Math.round(ts * 0.7));

            updateWrapperFromFields();
            updateOverlays();
            updateLayerPreview();
        });

        // name/class live edit
        $nameTextInput.on('input', function(){ stopAction(); updateOverlays(); });
        $classTextInput.on('input', function(){ stopAction(); updateOverlays(); });

        // compact toggle
        $('#toggle_compact').on('click', function(){
            $('.box-body').toggleClass('compact-ui');
        });

        // toolbar buttons
        $('#btn-bold').on('click', function(){
            var cur = $textStyle.val() || 'bold';
            if (cur.indexOf('bold') === -1) {
                $textStyle.val('bold');
            } else {
                $textStyle.val('normal');
            }
            updateHiddenTextFields();
            syncFullToCompact();
            stopAction();
            updateOverlays();
        });
        $('#btn-italic').on('click', function(){
            var cur = $textStyle.val() || 'bold';
            if (cur.indexOf('italic') === -1) {
                if (cur.indexOf('bold') !== -1) $textStyle.val('bold-italic'); else $textStyle.val('italic');
            } else {
                if (cur.indexOf('bold') !== -1) $textStyle.val('bold'); else $textStyle.val('normal');
            }
            updateHiddenTextFields();
            syncFullToCompact();
            stopAction();
            updateOverlays();
        });
        $('#btn-size-incr').on('click', function(){
            var current = Math.max(8, parseInt($textSize.val()||36,10));
            $textSize.val(current + 2);
            $toolbarTextSizeInput.val(current + 2);
            updateHiddenTextFields();
            syncFullToCompact();
            stopAction();
            updateOverlays();
        });
        $('#btn-size-decr').on('click', function(){
            var current = Math.max(8, parseInt($textSize.val()||36,10));
            $textSize.val(Math.max(8, current - 2));
            $toolbarTextSizeInput.val(Math.max(8, current - 2));
            updateHiddenTextFields();
            syncFullToCompact();
            stopAction();
            updateOverlays();
        });

        $nameXInput.add($nameYInput).add($classXInput).add($classYInput).on('input change', function() {
            stopAction();
            $nameX.val($nameXInput.val());
            $nameY.val($nameYInput.val());
            $classX.val($classXInput.val());
            $classY.val($classYInput.val());
            updateOverlays();
        });

        $imageLayer.on('change', function() {
            stopAction();
            syncFullToCompact();
            updateLayerPreview();
        });

        $('input, select').on('mousedown touchstart', function() {
            stopAction();
        });

        $student.on('dragstart', function(event) {
            event.preventDefault();
            return false;
        });

        $(window).on('resize', function() {
            updateScale();
            updateWrapperFromFields();
        });

        $wrapper.css('touch-action', 'none');
        $nameOverlay.css('touch-action', 'none');
        $classOverlay.css('touch-action', 'none');
        updateLayerPreview();
    }

    $(document).ready(init);
})(jQuery);
</script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script>
(function () {

    var btn = document.getElementById('download-preview');

    if (!btn) {
        return;
    }

    btn.addEventListener('click', function () {

        var container = document.getElementById('preview-container');
        var template = document.getElementById('template-preview');

        if (!container || !template) {
            alert('Preview not ready');
            return;
        }

        // Wait until fonts are completely loaded
        var ready = (
            document.fonts &&
            document.fonts.ready
        )
        ? document.fonts.ready
        : Promise.resolve();

        ready.then(function () {

            /*
             * Calculate the original image size.
             * This keeps the downloaded JPG at good quality.
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
                    ? (natural / displayed)
                    : 1;


            /*
             * Hide editor controls before creating JPG.
             * This prevents the green border and resize handles
             * from appearing in the downloaded image.
             */
            var wrapperEl =
                document.getElementById('student-wrapper');

            var handles =
                container.querySelectorAll('.resize-handle');

            var origBorder =
                wrapperEl
                    ? wrapperEl.style.border
                    : '';

            var origHandles = [];


            handles.forEach(function (handle) {

                origHandles.push(
                    handle.style.display
                );

                handle.style.display = 'none';

            });


            if (wrapperEl) {
                wrapperEl.style.border = 'none';
            }


            /*
             * Convert the preview container into a canvas.
             */
            html2canvas(container, {

                scale: Math.max(1, ratio),

                useCORS: true,

                /*
                 * JPG does not support transparency.
                 * Therefore use white background.
                 */
                backgroundColor: '#ffffff'

            })

            .then(function (canvas) {

                /*
                 * Convert canvas to JPG.
                 *
                 * 0.95 = 95% JPG quality.
                 */
                return new Promise(function (resolve) {

                    canvas.toBlob(
                        function (blob) {

                            resolve(blob);

                        },
                        'image/jpeg',
                        0.95
                    );

                });

            })

            .then(function (blob) {

                if (!blob) {

                    alert(
                        'Failed to generate JPG'
                    );

                    return;
                }


                /*
                 * Create download link.
                 */
                var a =
                    document.createElement('a');


                /*
                 * File name.
                 */
                var fname =
                    'birthday-' +
                    (<?php echo json_encode($studentName); ?> || 'card') +
                    '-' +
                    Date.now() +
                    '.jpg';


                /*
                 * Create temporary URL.
                 */
                var url =
                    URL.createObjectURL(blob);

                a.href = url;

                /*
                 * Force browser to download JPG.
                 */
                a.download =
                    fname.replace(
                        /[^a-zA-Z0-9_\-\.]/g,
                        '_'
                    );


                document.body.appendChild(a);

                a.click();

                a.remove();


                /*
                 * Release temporary URL.
                 */
                setTimeout(function () {

                    URL.revokeObjectURL(url);

                }, 5000);

            })

            .catch(function (err) {

                alert(
                    'JPG generation failed: ' +
                    (
                        err && err.message
                            ? err.message
                            : err
                    )
                );

            })

            .finally(function () {

                /*
                 * Restore editor border.
                 */
                if (wrapperEl) {

                    wrapperEl.style.border =
                        origBorder;

                }


                /*
                 * Restore resize handles.
                 */
                handles.forEach(
                    function (handle, index) {

                        handle.style.display =
                            origHandles[index] || '';

                    }
                );

            });

        });

    });

})();
</script>