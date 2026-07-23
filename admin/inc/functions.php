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