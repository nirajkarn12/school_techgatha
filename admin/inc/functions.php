<?php
function get_ext($pdo,$fname)
{

	$up_filename=$_FILES[$fname]["name"];
	$file_basename = substr($up_filename, 0, strripos($up_filename, '.')); // strip extention
	$file_ext = substr($up_filename, strripos($up_filename, '.')); // strip name
	return $file_ext;
}

/**
 * Allowed image extensions for admin uploads.
 * @param bool $includeIco include .ico (for favicon)
 */
function adminAllowedImageExtensions($includeIco = false) {
	$exts = array('jpg', 'jpeg', 'png', 'gif', 'webp');
	if ($includeIco) {
		$exts[] = 'ico';
	}
	return $exts;
}

function adminNormalizeUploadExt($filename) {
	return strtolower(pathinfo((string) $filename, PATHINFO_EXTENSION));
}

function adminIsAllowedImageExt($ext, $includeIco = false) {
	return in_array(strtolower((string) $ext), adminAllowedImageExtensions($includeIco), true);
}

function adminImageAcceptAttribute($includeIco = false) {
	$parts = array();
	foreach (adminAllowedImageExtensions($includeIco) as $ext) {
		$parts[] = '.' . $ext;
		$parts[] = 'image/' . ($ext === 'jpg' ? 'jpeg' : ($ext === 'ico' ? 'x-icon' : $ext));
	}
	return implode(',', array_unique($parts));
}

/**
 * Save an uploaded image into assets/uploads with a stable base name.
 * Deletes previous base-name variants (logo.png vs logo.jpg, etc.).
 *
 * @return array{ok:bool,filename:string,error:string}
 */
function adminSaveNamedImageUpload($filesKey, $baseName, $includeIco = false) {
	$upload = is_array($filesKey) ? $filesKey : array();
	$name = (string) ($upload['name'] ?? '');
	$tmp = (string) ($upload['tmp_name'] ?? '');
	$error = (int) ($upload['error'] ?? UPLOAD_ERR_NO_FILE);

	if ($name === '' || $error === UPLOAD_ERR_NO_FILE) {
		return array('ok' => false, 'filename' => '', 'error' => 'Please select an image file.<br>');
	}
	if ($error !== UPLOAD_ERR_OK || $tmp === '' || !is_uploaded_file($tmp)) {
		return array('ok' => false, 'filename' => '', 'error' => 'Image upload failed. Please try again.<br>');
	}

	$ext = adminNormalizeUploadExt($name);
	if (!adminIsAllowedImageExt($ext, $includeIco)) {
		$allowed = implode(', ', adminAllowedImageExtensions($includeIco));
		return array('ok' => false, 'filename' => '', 'error' => 'Invalid format. Allowed: ' . $allowed . '<br>');
	}

	// Soft MIME check (ico often reports as application/octet-stream)
	if (function_exists('finfo_open')) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = $finfo ? (string) finfo_file($finfo, $tmp) : '';
		if ($finfo) {
			finfo_close($finfo);
		}
		$okMimes = array(
			'image/jpeg', 'image/png', 'image/gif', 'image/webp',
			'image/x-icon', 'image/vnd.microsoft.icon', 'image/ico', 'image/icon',
			'application/octet-stream',
		);
		if ($mime !== '' && !in_array($mime, $okMimes, true) && strpos($mime, 'image/') !== 0) {
			return array('ok' => false, 'filename' => '', 'error' => 'File does not look like a valid image.<br>');
		}
	}

	$dir = dirname(__DIR__) . '/../assets/uploads/';
	$dir = realpath($dir) ?: (dirname(__DIR__) . '/../assets/uploads');
	$dir = rtrim(str_replace('\\', '/', $dir), '/') . '/';
	if (!is_dir($dir)) {
		@mkdir($dir, 0755, true);
	}

	$baseName = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $baseName);
	if ($baseName === '') {
		$baseName = 'image';
	}

	// Remove previous variants with any allowed extension
	foreach (adminAllowedImageExtensions(true) as $oldExt) {
		$oldPath = $dir . $baseName . '.' . $oldExt;
		if (is_file($oldPath)) {
			@unlink($oldPath);
		}
	}

	$finalName = $baseName . '.' . $ext;
	$dest = $dir . $finalName;
	if (!move_uploaded_file($tmp, $dest)) {
		return array('ok' => false, 'filename' => '', 'error' => 'Could not save uploaded image.<br>');
	}
	@chmod($dest, 0644);

	return array('ok' => true, 'filename' => $finalName, 'error' => '');
}

/**
 * Build an admin upload image URL with a filemtime cache-buster.
 * Fixes stale previews when uploads overwrite the same filename.
 */
function adminUploadUrl($filename, $subdir = '') {
	$filename = ltrim(str_replace('\\', '/', (string) $filename), '/');
	if ($filename === '' || preg_match('#^(https?:)?//#i', $filename)) {
		return $filename;
	}
	if (strpos($filename, 'assets/uploads/') === 0) {
		$relative = '../' . $filename;
	} elseif (strpos($filename, '../assets/uploads/') === 0) {
		$relative = $filename;
	} else {
		$prefix = $subdir !== '' ? (rtrim($subdir, '/') . '/') : '';
		$relative = '../assets/uploads/' . $prefix . $filename;
	}
	$fsPath = $relative;
	$queryPos = strpos($fsPath, '?');
	if ($queryPos !== false) {
		$fsPath = substr($fsPath, 0, $queryPos);
	}
	$v = is_file($fsPath) ? ((int) @filemtime($fsPath) . '-' . (int) @filesize($fsPath)) : (string) time();
	$base = preg_replace('/[?&]v=[^&]*/', '', $relative);
	$base = rtrim($base, '?&');
	return $base . (strpos($base, '?') !== false ? '&' : '?') . 'v=' . rawurlencode($v);
}

/**
 * Rewrite upload <img src> URLs in buffered admin HTML so replaced files show immediately.
 */
function adminBustUploadImageUrls($html) {
	return preg_replace_callback(
		'#(\bsrc\s*=\s*)(["\'])((?:\.\./)?assets/uploads/[^"\']+)\2#i',
		static function ($m) {
			$src = $m[3];
			$clean = preg_replace('/[?&]v=[^&]*/', '', $src);
			$clean = rtrim($clean, '?&');
			$fsPath = (strpos($clean, '../') === 0) ? $clean : ('../' . ltrim($clean, '/'));
			$v = is_file($fsPath) ? ((int) @filemtime($fsPath) . '-' . (int) @filesize($fsPath)) : (string) time();
			$bust = $clean . (strpos($clean, '?') !== false ? '&' : '?') . 'v=' . rawurlencode($v);
			return $m[1] . $m[2] . $bust . $m[2];
		},
		(string) $html
	);
}

function ext_check($pdo,$allowed_ext,$my_ext) 
{

	$arr1 = array();
	$arr1 = explode("|",$allowed_ext);	
	$count_arr1 = count(explode("|",$allowed_ext));	

	for($i=0;$i<$count_arr1;$i++)
	{
		$arr1[$i] = '.'.$arr1[$i];
	}
	

	$str = '';
	$stat = 0;
	for($i=0;$i<$count_arr1;$i++)
	{
		if($my_ext == $arr1[$i])
		{
			$stat = 1;
			break;
		}
	}

	if($stat == 1)
		return true; // file extension match
	else
		return false; // file extension not match
}


function get_ai_id($pdo,$tbl_name) 
{
	// Prefer information_schema — SHOW TABLE STATUS LIKE treats "_" as a wildcard
	// and can return the wrong table's Auto_increment (causing filename collisions).
	$statement = $pdo->prepare("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?");
	$statement->execute(array((string) $tbl_name));
	$val = $statement->fetchColumn();
	if ($val !== false && $val !== null) {
		return $val;
	}
	$like = str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), (string) $tbl_name);
	$statement = $pdo->prepare("SHOW TABLE STATUS LIKE ?");
	$statement->execute(array($like));
	$row = $statement->fetch(PDO::FETCH_ASSOC);
	return $row ? $row['Auto_increment'] : null;
}

/**
 * Unique upload filename: {prefix}-{id}-{time}-{random}.{ext}
 * Never reuse a fixed id-only name — that overwrites other records' images.
 */
function adminUniqueUploadName($prefix, $ext, $id = 0) {
	$prefix = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $prefix);
	if ($prefix === '') {
		$prefix = 'file';
	}
	$ext = strtolower(preg_replace('/[^a-z0-9]/', '', (string) $ext));
	$id = (int) $id;
	$rand = function_exists('random_bytes') ? bin2hex(random_bytes(3)) : (string) mt_rand(100000, 999999);
	$parts = array($prefix);
	if ($id > 0) {
		$parts[] = (string) $id;
	}
	$parts[] = (string) time();
	$parts[] = $rand;
	$name = implode('-', $parts);
	return $ext !== '' ? ($name . '.' . $ext) : $name;
}

function adminUploadsPath($filename = '') {
	$dir = dirname(__DIR__) . '/../assets/uploads';
	$real = realpath($dir);
	$dir = $real ? $real : $dir;
	$dir = rtrim(str_replace('\\', '/', $dir), '/') . '/';
	$filename = basename(str_replace('\\', '/', (string) $filename));
	return $filename === '' ? $dir : ($dir . $filename);
}

function adminMoveUploadedFile($tmp, $filename) {
	$filename = basename(str_replace('\\', '/', (string) $filename));
	if ($filename === '' || $tmp === '' || !is_uploaded_file($tmp)) {
		return false;
	}
	$dir = adminUploadsPath();
	if (!is_dir($dir)) {
		@mkdir($dir, 0755, true);
	}
	$dest = $dir . $filename;
	if (!@move_uploaded_file($tmp, $dest)) {
		return false;
	}
	@chmod($dest, 0644);
	return true;
}

/**
 * Delete an upload only when no other row still references the same filename.
 */
function adminDeleteUploadIfUnused($pdo, $filename, $table, $column, $excludeId = 0, $idColumn = 'id') {
	$filename = basename(str_replace('\\', '/', (string) $filename));
	if ($filename === '' || !preg_match('/^[a-zA-Z0-9._-]+$/', $filename)) {
		return;
	}
	$table = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $table);
	$column = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $column);
	$idColumn = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $idColumn);
	if ($table === '' || $column === '' || $idColumn === '') {
		return;
	}
	$sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = ?";
	$params = array($filename);
	if ((int) $excludeId > 0) {
		$sql .= " AND `{$idColumn}` <> ?";
		$params[] = (int) $excludeId;
	}
	try {
		$st = $pdo->prepare($sql);
		$st->execute($params);
		if ((int) $st->fetchColumn() > 0) {
			return;
		}
	} catch (Exception $e) {
		return;
	}
	$path = adminUploadsPath($filename);
	if (is_file($path)) {
		@unlink($path);
	}
}

/**
 * Services/facilities attach to mid category in the UI.
 * Internally we reuse tbl_end_category so existing product.ecat_id keeps working.
 */
function resolveServiceEndCategory($pdo, $mcatId)
{
	$mcatId = (int) $mcatId;
	if ($mcatId <= 0) {
		return 0;
	}

	$statement = $pdo->prepare("SELECT ecat_id FROM tbl_end_category WHERE mcat_id = ? ORDER BY ecat_id ASC LIMIT 1");
	$statement->execute(array($mcatId));
	$row = $statement->fetch(PDO::FETCH_ASSOC);
	if ($row) {
		return (int) $row['ecat_id'];
	}

	$statement = $pdo->prepare("SELECT mcat_name FROM tbl_mid_category WHERE mcat_id = ? LIMIT 1");
	$statement->execute(array($mcatId));
	$mid = $statement->fetch(PDO::FETCH_ASSOC);
	$ecatName = $mid && !empty($mid['mcat_name']) ? $mid['mcat_name'] : 'Services';

	$statement = $pdo->prepare("INSERT INTO tbl_end_category (ecat_name, mcat_id) VALUES (?, ?)");
	$statement->execute(array($ecatName, $mcatId));
	return (int) $pdo->lastInsertId();
}

/**
 * Default category bucket for Facilities when admin does not pick one.
 * Keeps tbl_product.ecat_id valid without exposing category UI.
 */
function resolveDefaultFacilityCategory($pdo)
{
	static $cached = null;
	if ($cached !== null) {
		return $cached;
	}

	try {
		$statement = $pdo->prepare("SELECT ecat_id FROM tbl_end_category WHERE ecat_name = ? ORDER BY ecat_id ASC LIMIT 1");
		$statement->execute(array('Facilities'));
		$id = (int) $statement->fetchColumn();
		if ($id > 0) {
			$cached = $id;
			return $cached;
		}

		$id = (int) $pdo->query("SELECT ecat_id FROM tbl_end_category ORDER BY ecat_id ASC LIMIT 1")->fetchColumn();
		if ($id > 0) {
			$cached = $id;
			return $cached;
		}

		$tcatId = (int) $pdo->query("SELECT tcat_id FROM tbl_top_category ORDER BY tcat_id ASC LIMIT 1")->fetchColumn();
		if ($tcatId <= 0) {
			$pdo->exec("INSERT INTO tbl_top_category (tcat_name, show_on_menu) VALUES ('General', 0)");
			$tcatId = (int) $pdo->lastInsertId();
		}

		$mcatId = (int) $pdo->query("SELECT mcat_id FROM tbl_mid_category ORDER BY mcat_id ASC LIMIT 1")->fetchColumn();
		if ($mcatId <= 0) {
			$st = $pdo->prepare("INSERT INTO tbl_mid_category (mcat_name, tcat_id) VALUES (?, ?)");
			$st->execute(array('Facilities', $tcatId));
			$mcatId = (int) $pdo->lastInsertId();
		}

		$st = $pdo->prepare("INSERT INTO tbl_end_category (ecat_name, mcat_id) VALUES (?, ?)");
		$st->execute(array('Facilities', $mcatId));
		$cached = (int) $pdo->lastInsertId();
		return $cached;
	} catch (Throwable $e) {
		$cached = 0;
		return 0;
	}
}

function ensureClientTable($pdo) {
	static $ready = null;
	if ($ready !== null) {
		return $ready;
	}

	try {
		$pdo->query('SELECT 1 FROM tbl_client LIMIT 1');
		$ready = true;
	} catch (Throwable $e) {
		try {
			$pdo->exec("
				CREATE TABLE `tbl_client` (
				  `id` int NOT NULL AUTO_INCREMENT,
				  `name` varchar(150) NOT NULL DEFAULT '',
				  `logo` varchar(255) NOT NULL,
				  `website_url` varchar(255) NOT NULL DEFAULT '',
				  `status` varchar(20) NOT NULL DEFAULT 'Active',
				  `sort_order` int NOT NULL DEFAULT 0,
				  `created_at` datetime DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
			");
			$ready = true;
		} catch (Throwable $e2) {
			$ready = false;
		}
	}

	return $ready;
}

function ensureWhyFeatureTable($pdo) {
	static $ready = null;
	if ($ready !== null) {
		return $ready;
	}

	try {
		$pdo->query('SELECT 1 FROM tbl_why_feature LIMIT 1');
		$ready = true;
	} catch (Throwable $e) {
		try {
			$pdo->exec("
				CREATE TABLE `tbl_why_feature` (
				  `id` int NOT NULL AUTO_INCREMENT,
				  `title` varchar(255) NOT NULL,
				  `icon` varchar(255) NOT NULL DEFAULT '',
				  `icon_class` varchar(100) NOT NULL DEFAULT 'fa-star',
				  `sort_order` int NOT NULL DEFAULT 0,
				  `status` varchar(20) NOT NULL DEFAULT 'Active',
				  `created_at` datetime DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
			");
			$ready = true;
		} catch (Throwable $e2) {
			$ready = false;
			return $ready;
		}
	}

	if ($ready) {
		try {
			$count = (int) $pdo->query('SELECT COUNT(*) FROM tbl_why_feature')->fetchColumn();
			if ($count === 0) {
				$defaults = array(
					array('40 years of Excellence in Education.', 'fa-award', 1),
					array('Winner of Numerous National and Regional Educational Awards.', 'fa-trophy', 2),
					array('Well-Equipped Science and Computer Laboratories.', 'fa-flask', 3),
					array('Highly trained and Experienced Teachers.', 'fa-chalkboard-user', 4),
					array('ECA Training Imparted by Full-time National-Level Coaches.', 'fa-person-running', 5),
					array('Psychosocial counsellors and Career counsellors Available.', 'fa-comments', 6),
				);
				$ins = $pdo->prepare('INSERT INTO tbl_why_feature (title, icon_class, sort_order, status, created_at) VALUES (?, ?, ?, ?, NOW())');
				foreach ($defaults as $row) {
					$ins->execute(array($row[0], $row[1], $row[2], 'Active'));
				}
			}
		} catch (Throwable $e) {
			// ignore
		}
	}

	return $ready;
}

function ensureAchieverTable($pdo) {
	static $ready = null;
	if ($ready !== null) {
		return $ready;
	}

	try {
		$pdo->query('SELECT 1 FROM tbl_achiever LIMIT 1');
		$ready = true;
	} catch (Throwable $e) {
		try {
			$pdo->exec("
				CREATE TABLE `tbl_achiever` (
				  `id` int NOT NULL AUTO_INCREMENT,
				  `name` varchar(150) NOT NULL,
				  `photo` varchar(255) NOT NULL DEFAULT '',
				  `achievement` varchar(255) NOT NULL DEFAULT '',
				  `year` varchar(20) NOT NULL DEFAULT '',
				  `sort_order` int NOT NULL DEFAULT 0,
				  `status` varchar(20) NOT NULL DEFAULT 'Active',
				  `created_at` datetime DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
			");
			$ready = true;
		} catch (Throwable $e2) {
			$ready = false;
		}
	}

	return $ready;
}

function ensureBrochureTable($pdo) {
	static $ready = null;
	if ($ready !== null) {
		return $ready;
	}

	try {
		$pdo->query('SELECT 1 FROM tbl_brochure LIMIT 1');
		$ready = true;
	} catch (Throwable $e) {
		try {
			$pdo->exec("
				CREATE TABLE `tbl_brochure` (
				  `id` int NOT NULL AUTO_INCREMENT,
				  `title` varchar(255) NOT NULL,
				  `year` varchar(20) NOT NULL DEFAULT '',
				  `image` varchar(255) NOT NULL DEFAULT '',
				  `file` varchar(255) NOT NULL DEFAULT '',
				  `sort_order` int NOT NULL DEFAULT 0,
				  `status` varchar(20) NOT NULL DEFAULT 'Active',
				  `created_at` datetime DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
			");
			$ready = true;
		} catch (Throwable $e2) {
			$ready = false;
		}
	}

	return $ready;
}

function ensureBirthdayTables($pdo) {
	static $ready = null;
	if ($ready !== null) {
		return $ready;
	}

	try {
		$pdo->query('SELECT 1 FROM tbl_birthday_template LIMIT 1');
	} catch (Throwable $e) {
		try {
			$pdo->exec("
				CREATE TABLE `tbl_birthday_template` (
				  `id` int NOT NULL AUTO_INCREMENT,
				  `title` varchar(255) NOT NULL DEFAULT '',
				  `template_image` varchar(255) NOT NULL DEFAULT '',
				  `output_x` int NOT NULL DEFAULT 0,
				  `output_y` int NOT NULL DEFAULT 0,
				  `output_width` int NOT NULL DEFAULT 0,
				  `output_height` int NOT NULL DEFAULT 0,
				  `status` varchar(20) NOT NULL DEFAULT 'Active',
				  `sort_order` int NOT NULL DEFAULT 0,
				  `created_at` datetime DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
			");
		} catch (Throwable $e2) {
			// ignore
		}
	}

	try {
		$pdo->query('SELECT 1 FROM tbl_birthday_student LIMIT 1');
	} catch (Throwable $e) {
		try {
			$pdo->exec("
				CREATE TABLE `tbl_birthday_student` (
				  `id` int NOT NULL AUTO_INCREMENT,
				  `template_id` int NOT NULL DEFAULT 0,
				  `name` varchar(150) NOT NULL DEFAULT '',
				  `class_name` varchar(100) NOT NULL DEFAULT '',
				  `birthday_date` varchar(50) NOT NULL DEFAULT '',
				  `details` text DEFAULT NULL,
				  `student_image` varchar(255) NOT NULL DEFAULT '',
				  `generated_image` varchar(255) NOT NULL DEFAULT '',
				  `status` varchar(20) NOT NULL DEFAULT 'Active',
				  `sort_order` int NOT NULL DEFAULT 0,
				  `created_at` datetime DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
			");
		} catch (Throwable $e2) {
			// ignore
		}
	}

	try {
		$ready = true;
		ensureBirthdayTemplateDefaults($pdo);
	} catch (Throwable $e) {
		$ready = false;
	}

	return $ready;
}

function ensureBirthdayTemplateDefaults($pdo) {
	static $done = null;
	if ($done !== null) {
		return $done;
	}

	try {
		$count = (int) $pdo->query('SELECT COUNT(*) FROM tbl_birthday_template')->fetchColumn();
	} catch (Throwable $e) {
		$done = false;
		return false;
	}

	if ($count > 0) {
		$done = true;
		return true;
	}

	$source = dirname(__DIR__) . '/birthday/birthday.png';
	$targetName = 'birthday-template-default.png';
	$targetPath = adminUploadsPath($targetName);
	if (is_file($source) && !is_file($targetPath)) {
		@copy($source, $targetPath);
		@chmod($targetPath, 0644);
	}

	try {
		$statement = $pdo->prepare("INSERT INTO tbl_birthday_template (title, template_image, output_x, output_y, output_width, output_height, status, sort_order, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
		$statement->execute(array('Default Birthday Card', $targetName, 285, 205, 510, 590, 'Active', 0));
	} catch (Throwable $e) {
		$done = false;
		return false;
	}

	$done = true;
	return true;
}

function birthdayResolveImagePath($path) {
	$path = str_replace('\\', '/', (string) $path);
	if ($path === '') {
		return '';
	}
	if (preg_match('#^(https?:)?//#i', $path)) {
		return '';
	}
	if (strpos($path, '/') === 0) {
		return $path;
	}
	$relative = $path;
	if (strpos($relative, '../') === 0 || strpos($relative, './') === 0) {
		return realpath($relative) ?: $relative;
	}
	$local = realpath($relative);
	if ($local !== false) {
		return $local;
	}
	$forUploads = adminUploadsPath($relative);
	if (is_file($forUploads)) {
		return $forUploads;
	}
	return $relative;
}

function generateBirthdayCardImage($templateImagePath, $studentImagePath, $outputPath, $studentName, $className, $birthdayDate, $details, $options = array()) {
	$templateImagePath = birthdayResolveImagePath($templateImagePath);
	$studentImagePath = birthdayResolveImagePath($studentImagePath);
	if ($templateImagePath === '' || !is_file($templateImagePath)) {
		return array('ok' => false, 'error' => 'Template image is missing.', 'output' => '');
	}
	if ($studentImagePath === '' || !is_file($studentImagePath)) {
		return array('ok' => false, 'error' => 'Student image is missing.', 'output' => '');
	}

	$templateImage = @imagecreatefromstring(file_get_contents($templateImagePath));
	$studentImage = @imagecreatefromstring(file_get_contents($studentImagePath));
	if (!$templateImage || !$studentImage) {
		return array('ok' => false, 'error' => 'The images could not be loaded.', 'output' => '');
	}

	$outputDir = dirname($outputPath);
	if ($outputDir !== '' && !is_dir($outputDir)) {
		@mkdir($outputDir, 0755, true);
	}

	$width = imagesx($templateImage);
	$height = imagesy($templateImage);

	$opts = array(
		'output_x' => isset($options['output_x']) ? (int) $options['output_x'] : 130,
		'output_y' => isset($options['output_y']) ? (int) $options['output_y'] : 220,
		'output_width' => isset($options['output_width']) ? (int) $options['output_width'] : 480,
		'output_height' => isset($options['output_height']) ? (int) $options['output_height'] : 520,
		'name_x' => isset($options['name_x']) ? (int) $options['name_x'] : 20,
		'name_y' => isset($options['name_y']) ? (int) $options['name_y'] : max(20, $height - 80),
		'class_x' => isset($options['class_x']) ? (int) $options['class_x'] : 20,
		'class_y' => isset($options['class_y']) ? (int) $options['class_y'] : max(20, $height - 40),
		'text_size' => isset($options['text_size']) ? max(8, (int) $options['text_size']) : 36,
		'text_color' => isset($options['text_color']) ? (string) $options['text_color'] : '#0c2b5f',
		'text_style' => isset($options['text_style']) ? (string) $options['text_style'] : 'bold',
		'text_shadow' => isset($options['text_shadow']) && $options['text_shadow'] === '1',
		'text_stroke_color' => isset($options['text_stroke_color']) ? (string) $options['text_stroke_color'] : '#ffffff',
		'text_stroke_width' => isset($options['text_stroke_width']) ? max(0, (int)$options['text_stroke_width']) : 0,
		'text_stroke_position' => isset($options['text_stroke_position']) ? (string)$options['text_stroke_position'] : 'outside',
		'letter_spacing' => isset($options['letter_spacing']) ? (int) $options['letter_spacing'] : 0,
		'image_layer' => isset($options['image_layer']) && $options['image_layer'] === 'back' ? 'back' : 'front',
	);
	$canvas = imagecreatetruecolor($width, $height);

	$studentWidth = imagesx($studentImage);
	$studentHeight = imagesy($studentImage);
	$destWidth = $opts['output_width'] > 0 ? $opts['output_width'] : $studentWidth;
	$destHeight = $opts['output_height'] > 0 ? $opts['output_height'] : $studentHeight;
	$destX = $opts['output_x'];
	$destY = $opts['output_y'];
	if ($destX < 0 || $destY < 0) {
		$destX = 0;
		$destY = 0;
	}

	if ($opts['image_layer'] === 'back') {
		imagecopyresampled($canvas, $studentImage, $destX, $destY, 0, 0, $destWidth, $destHeight, $studentWidth, $studentHeight);
		imagecopy($canvas, $templateImage, 0, 0, 0, 0, $width, $height);
	} else {
		imagecopy($canvas, $templateImage, 0, 0, 0, 0, $width, $height);
		imagecopyresampled($canvas, $studentImage, $destX, $destY, 0, 0, $destWidth, $destHeight, $studentWidth, $studentHeight);
	}

	$blue = imagecolorallocate($canvas, 12, 43, 95);
	$white = imagecolorallocate($canvas, 255, 255, 255);
	$shadowColor = imagecolorallocatealpha($canvas, 0, 0, 0, 80);

	// Prefer Poppins if installed in assets/fonts, otherwise fall back to DejaVu fonts
	$poppinsDir = realpath(dirname(__DIR__) . '/../assets/fonts') ?: (dirname(__DIR__) . '/../assets/fonts');
	$fontPoppinsRegular = is_dir($poppinsDir) ? realpath($poppinsDir . '/Poppins-Regular.ttf') : false;
	$fontPoppinsBold = is_dir($poppinsDir) ? realpath($poppinsDir . '/Poppins-Bold.ttf') : false;
	$fontPoppinsItalic = is_dir($poppinsDir) ? realpath($poppinsDir . '/Poppins-Italic.ttf') : false;
	$fontPoppinsBoldItalic = is_dir($poppinsDir) ? realpath($poppinsDir . '/Poppins-BoldItalic.ttf') : false;

	// Determine requested font families for name and class separately. Prefer Poppins or Nepali fonts in assets/fonts.
	$resolveFontFamily = function($requestedFont) use ($fontPoppinsRegular, $fontPoppinsBold, $fontPoppinsItalic, $fontPoppinsBoldItalic) {
		$fontRegular = false; $fontBold = false; $fontItalic = false; $fontBoldItalic = false;
		if ($requestedFont === 'Poppins') {
			$fontRegular = $fontPoppinsRegular ?: realpath(__DIR__ . '/../vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf');
			$fontBold = $fontPoppinsBold ?: realpath(__DIR__ . '/../vendor/dompdf/dompdf/lib/fonts/DejaVuSans-Bold.ttf');
			$fontItalic = $fontPoppinsItalic ?: realpath(__DIR__ . '/../vendor/dompdf/dompdf/lib/fonts/DejaVuSans-Oblique.ttf');
			$fontBoldItalic = $fontPoppinsBoldItalic ?: realpath(__DIR__ . '/../vendor/dompdf/dompdf/lib/fonts/DejaVuSans-BoldOblique.ttf');
		} else {
			$base = preg_replace('/[^A-Za-z0-9_\-]/', '', $requestedFont);
			$tryPaths = array(
				$fontPoppinsRegular,
				realpath(dirname(__DIR__) . '/../assets/fonts/' . $base . '.ttf'),
				realpath(dirname(__DIR__) . '/../assets/fonts/' . $base . '-Regular.ttf'),
				realpath(dirname(__DIR__) . '/../assets/fonts/' . $base . '-Bold.ttf'),
				realpath(dirname(__DIR__) . '/../assets/fonts/' . $base . '-Italic.ttf'),
				realpath(dirname(__DIR__) . '/../assets/fonts/' . $base . '-BoldItalic.ttf'),
			);
			$tryPaths = array_filter($tryPaths);
			foreach ($tryPaths as $p) {
				$lower = strtolower(basename($p));
				if (!$fontRegular && preg_match('/regular|\b' . strtolower($base) . '\b|^' . preg_quote($base, '/') . '\.ttf$/i', basename($p))) {
					$fontRegular = $p;
				}
				if (!$fontBold && stripos($lower, 'bold') !== false) {
					$fontBold = $p;
				}
				if (!$fontItalic && stripos($lower, 'italic') !== false) {
					$fontItalic = $p;
				}
				if (!$fontBoldItalic && stripos($lower, 'bolditalic') !== false) {
					$fontBoldItalic = $p;
				}
			}
			if (!$fontRegular && !empty($tryPaths)) $fontRegular = reset($tryPaths);
			if (!$fontBold) $fontBold = $fontRegular;
			if (!$fontItalic) $fontItalic = $fontRegular;
			if (!$fontBoldItalic) $fontBoldItalic = $fontBold;
		}
		return array($fontRegular, $fontBold, $fontItalic, $fontBoldItalic);
	};

	list($nameFontRegular, $nameFontBold, $nameFontItalic, $nameFontBoldItalic) = $resolveFontFamily(isset($options['name_font_family']) ? (string)$options['name_font_family'] : 'Poppins');
	list($classFontRegular, $classFontBold, $classFontItalic, $classFontBoldItalic) = $resolveFontFamily(isset($options['class_font_family']) ? (string)$options['class_font_family'] : 'Poppins');

	$useTtfName = ($nameFontBold && $nameFontRegular && is_file($nameFontBold) && is_file($nameFontRegular));
	$useTtfClass = ($classFontBold && $classFontRegular && is_file($classFontBold) && is_file($classFontRegular));
	$useTtf = $useTtfName || $useTtfClass;

	$nameTextColor = $blue;
	if (preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $opts['name_text_color'])) {
		$hex = ltrim($opts['name_text_color'], '#');
		if (strlen($hex) === 3) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		$nameTextColor = imagecolorallocate($canvas, hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)));
	}

	$classTextColor = $blue;
	if (preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $opts['class_text_color'])) {
		$hex = ltrim($opts['class_text_color'], '#');
		if (strlen($hex) === 3) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		$classTextColor = imagecolorallocate($canvas, hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)));
	}

	// Stroke color
	$nameStrokeColorAlloc = null;
	$nameStrokeHex = $opts['name_text_stroke_color'];
	$nameStrokeWidth = max(0, (int)$opts['name_text_stroke_width']);
	$nameStrokePosition = in_array($opts['name_text_stroke_position'], array('outside','center','inside'), true) ? $opts['name_text_stroke_position'] : 'outside';
	$classStrokeColorAlloc = null;
	$classStrokeHex = $opts['class_text_stroke_color'];
	$classStrokeWidth = max(0, (int)$opts['class_text_stroke_width']);
	$classStrokePosition = in_array($opts['class_text_stroke_position'], array('outside','center','inside'), true) ? $opts['class_text_stroke_position'] : 'outside';
	$letterSpacing = max(-20, min(50, (int) $opts['letter_spacing']));
	if ($nameStrokeWidth > 0 && preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $nameStrokeHex)) {
		$hex = ltrim($nameStrokeHex, '#');
		if (strlen($hex) === 3) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		$nameStrokeColorAlloc = imagecolorallocate($canvas, hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)));
	}
	if ($classStrokeWidth > 0 && preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $classStrokeHex)) {
		$hex = ltrim($classStrokeHex, '#');
		if (strlen($hex) === 3) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		$classStrokeColorAlloc = imagecolorallocate($canvas, hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)));
	}

	function getFontForStyle($style, $regular, $bold, $italic, $boldItalic) {
		if ($style === 'bold') {
			return $bold ?: $regular;
		} elseif ($style === 'italic') {
			return $italic ?: $regular;
		} elseif ($style === 'bold-italic') {
			return $boldItalic ?: $bold ?: $regular;
		}
		return $regular;
	}

	$nameFont = getFontForStyle($opts['name_text_style'], $nameFontRegular, $nameFontBold, $nameFontItalic, $nameFontBoldItalic);
	$classFont = getFontForStyle($opts['class_text_style'], $classFontRegular, $classFontBold, $classFontItalic, $classFontBoldItalic);
	if (!$useTtfName) {
		$nameFont = null;
	}
	if (!$useTtfClass) {
		$classFont = null;
	}

	$lines = array();
	$lines[] = array(
		'text' => trim((string) $studentName),
		'font' => $nameFont,
		'size' => $opts['name_text_size'],
		'x' => $opts['name_x'],
		'y' => $opts['name_y'],
		'color' => $nameTextColor,
		'shadow' => $opts['name_text_shadow'] === '1',
		'strokeColor' => $nameStrokeColorAlloc,
		'strokeWidth' => $nameStrokeWidth,
		'strokePosition' => $nameStrokePosition,
		'letterSpacing' => max(-20, min(50, (int) $opts['name_letter_spacing']))
	);
	if ($className !== '') {
		$lines[] = array(
			'text' => trim((string) $className),
			'font' => $classFont,
			'size' => $opts['class_text_size'],
			'x' => $opts['class_x'],
			'y' => $opts['class_y'],
			'color' => $classTextColor,
			'shadow' => $opts['class_text_shadow'] === '1',
			'strokeColor' => $classStrokeColorAlloc,
			'strokeWidth' => $classStrokeWidth,
			'strokePosition' => $classStrokePosition,
			'letterSpacing' => max(-20, min(50, (int) $opts['class_letter_spacing']))
		);
	}

	foreach ($lines as $index => $line) {
		if ($line['text'] === '') {
			continue;
		}

		$font = $line['font'];
		$size = $line['size'];
		$color = $line['color'];
		$shadow = $line['shadow'] ? $shadowColor : null;
		$baselineY = $line['y'];

		if ($useTtf && $font) {
			$bbox = imagettfbbox($size, 0, $font, $line['text']);
			$minY = min($bbox[1], $bbox[3], $bbox[5], $bbox[7]);
			$baselineY = $line['y'] - $minY;
		}

		if ($useTtf && $font) {
			if ($shadow) {
				imagettftext($canvas, $size, 0, $line['x'] + 2, $baselineY + 2, $shadow, $font, $line['text']);
			}

			$drawText = function($text, $colorAlloc, $offsetX = 0, $offsetY = 0) use ($canvas, $font, $size, $letterSpacing, $baselineY, $line) {
				$x = $line['x'] + $offsetX;
				$y = $baselineY + $offsetY;
				if ($letterSpacing === 0 || $text === '') {
					imagettftext($canvas, $size, 0, $x, $y, $colorAlloc, $font, $text);
					return;
				}

				$chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
				foreach ($chars as $char) {
					$bbox = imagettfbbox($size, 0, $font, $char);
					$charWidth = abs($bbox[2] - $bbox[0]);
					imagettftext($canvas, $size, 0, $x, $y, $colorAlloc, $font, $char);
					$x += $charWidth + $letterSpacing;
				}
			};

			// Draw stroke depending on requested position
			if ($strokeWidth > 0 && $strokeColorAlloc) {
				if ($strokePosition === 'outside' || $strokePosition === 'center') {
					$maxR = min(10, $strokeWidth);
					for ($r = 1; $r <= $maxR; $r++) {
						for ($angle = 0; $angle < 360; $angle += 45) {
							$rad = deg2rad($angle);
							$dx = (int) round(cos($rad) * $r);
							$dy = (int) round(sin($rad) * $r);
							$drawText($line['text'], $strokeColorAlloc, $dx, $dy);
						}
					}
				} elseif ($strokePosition === 'inside') {
					$maxR = min(6, max(1, (int) round($strokeWidth / 1)));
					for ($r = $maxR; $r >= 1; $r--) {
						for ($angle = 0; $angle < 360; $angle += 90) {
							$rad = deg2rad($angle);
							$dx = (int) round(cos($rad) * $r / 2);
							$dy = (int) round(sin($rad) * $r / 2);
							$drawText($line['text'], $strokeColorAlloc, $dx, $dy);
						}
					}
				}
			}

			// Finally draw main fill text on top
			$drawText($line['text'], $color);
		} else {
			if ($shadow) {
				imagestring($canvas, 5, $line['x'] + 1, $line['y'] + 1, $line['text'], $shadow);
			}
			imagestring($canvas, 5, $line['x'], $line['y'], $line['text'], $color);
		}
	}

	$ext = strtolower(pathinfo((string) $outputPath, PATHINFO_EXTENSION));
	if ($ext === 'png') {
		imagepng($canvas, $outputPath);
	} elseif ($ext === 'webp') {
		imagewebp($canvas, $outputPath);
	} elseif ($ext === 'gif') {
		imagegif($canvas, $outputPath);
	} else {
		imagejpeg($canvas, $outputPath, 90);
	}

	imagedestroy($templateImage);
	imagedestroy($studentImage);
	imagedestroy($canvas);

	return array('ok' => true, 'error' => '', 'output' => $outputPath);
}

function ensureServiceLocationColumns($pdo) {
	static $done = false;
	if ($done) {
		return;
	}
	$done = true;
	$targets = array(
		'tbl_payment' => array('service_lat', 'service_lng'),
		'tbl_booking_assignment' => array('service_lat', 'service_lng'),
	);
	foreach ($targets as $table => $columns) {
		foreach ($columns as $column) {
			try {
				$statement = $pdo->prepare("SHOW COLUMNS FROM `" . $table . "` LIKE ?");
				$statement->execute(array($column));
				if ($statement->rowCount() === 0) {
					$pdo->exec("ALTER TABLE `" . $table . "` ADD COLUMN `" . $column . "` DECIMAL(10,7) NULL");
				}
			} catch (Exception $e) {
				// ignore
			}
		}
	}
}

function normalizeMapCoordinate($value, $min, $max) {
	if ($value === null || $value === '') {
		return null;
	}
	if (!is_numeric($value)) {
		return null;
	}
	$num = (float) $value;
	if ($num < $min || $num > $max) {
		return null;
	}
	return round($num, 7);
}

function adminServiceLocationAssets() {
	$base = rtrim(BASE_URL, '/') . '/';
	return '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">'
		. '<link rel="stylesheet" href="' . $base . 'assets/css/service-location-map.css?v=20260721">'
		. '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>'
		. '<script src="' . $base . 'assets/js/service-location-map.js?v=20260721"></script>';
}

function adminRenderServiceLocationPicker($lat = '', $lng = '', $addressInput = '#service_address') {
	ob_start();
	?>
	<div class="service-location-map-wrap admin-map" data-service-map="picker" data-address-input="<?php echo htmlspecialchars($addressInput); ?>">
		<div class="service-location-map-toolbar">
			<input type="search" class="form-control input-sm" data-map-search placeholder="Search place, street, or landmark...">
			<button type="button" class="btn btn-default btn-sm" data-map-search-btn>Search</button>
			<button type="button" class="btn btn-default btn-sm" data-map-locate-btn>Use my location</button>
		</div>
		<div class="service-location-map-canvas" data-map-canvas></div>
		<div class="service-location-map-meta" data-map-meta>Tap the map or search to pin the cleaning location.</div>
		<div class="service-location-map-meta" data-map-status></div>
		<input type="hidden" name="service_lat" data-map-lat value="<?php echo htmlspecialchars((string)$lat); ?>">
		<input type="hidden" name="service_lng" data-map-lng value="<?php echo htmlspecialchars((string)$lng); ?>">
	</div>
	<?php
	return ob_get_clean();
}

function getInvoiceCompanyProfile($pdo) {
	static $profile = null;
	if ($profile !== null) {
		return $profile;
	}

	$row = [];
	try {
		$statement = $pdo->query("SELECT * FROM tbl_settings WHERE id=1 LIMIT 1");
		$row = $statement ? ($statement->fetch(PDO::FETCH_ASSOC) ?: []) : [];
	} catch (Exception $e) {
		$row = [];
	}

	$logoFile = !empty($row['logo']) ? $row['logo'] : '';
	$logoUrl = '';
	if ($logoFile !== '') {
		$logoUrl = rtrim(BASE_URL, '/') . '/assets/uploads/' . ltrim($logoFile, '/');
	} else {
		$logoUrl = rtrim(BASE_URL, '/') . '/assets/images/placeholder.png';
	}

	$profile = array(
		'site_name' => !empty($row['site_name']) ? $row['site_name'] : 'Techgatha School',
		'logo' => $logoFile,
		'logo_url' => $logoUrl,
		'address' => $row['contact_address'] ?? '',
		'email' => $row['contact_email'] ?? '',
		'phone' => $row['contact_phone'] ?? '',
		'copyright' => $row['footer_copyright'] ?? '',
		'about' => $row['footer_about'] ?? '',
		'vat_no' => $row['invoice_vat_no'] ?? '',
		'due_days' => (int)($row['invoice_due_days'] ?? 30),
		'footer_note' => $row['invoice_footer_note'] ?? 'Thank you for choosing our cleaning service.',
	);
	if ($profile['due_days'] <= 0) {
		$profile['due_days'] = 30;
	}
	return $profile;
}

function adminRenderServiceLocationViewer($lat, $lng, $address = '') {
	$lat = normalizeMapCoordinate($lat, -90, 90);
	$lng = normalizeMapCoordinate($lng, -180, 180);
	$query = ($lat !== null && $lng !== null) ? ($lat . ',' . $lng) : $address;
	$google = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($query);
	$directions = 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode($query);
	$osm = ($lat !== null && $lng !== null)
		? ('https://www.openstreetmap.org/?mlat=' . rawurlencode((string)$lat) . '&mlon=' . rawurlencode((string)$lng) . '#map=16/' . rawurlencode((string)$lat) . '/' . rawurlencode((string)$lng))
		: ('https://www.openstreetmap.org/search?query=' . rawurlencode((string)$address));
	ob_start();
	?>
	<div class="service-location-map-wrap admin-map" data-service-map="view" data-lat="<?php echo htmlspecialchars((string)$lat); ?>" data-lng="<?php echo htmlspecialchars((string)$lng); ?>" data-address="<?php echo htmlspecialchars((string)$address); ?>">
		<div class="service-location-map-canvas" data-map-canvas></div>
		<div class="service-location-map-meta" data-map-meta></div>
		<div class="service-location-map-actions">
			<a class="btn btn-success btn-sm" data-map-directions href="<?php echo htmlspecialchars($directions); ?>" target="_blank" rel="noopener"><i class="fa fa-location-arrow"></i> Get directions</a>
			<a class="btn btn-primary btn-sm" data-map-google href="<?php echo htmlspecialchars($google); ?>" target="_blank" rel="noopener"><i class="fa fa-map-marker"></i> Google Maps</a>
			<a class="btn btn-default btn-sm" data-map-osm href="<?php echo htmlspecialchars($osm); ?>" target="_blank" rel="noopener">OpenStreetMap</a>
		</div>
	</div>
	<?php
	return ob_get_clean();
}