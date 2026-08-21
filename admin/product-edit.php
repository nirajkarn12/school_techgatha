<?php require_once('header.php'); ?>

<?php
if (!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
}

$p_id = (int)$_REQUEST['id'];
$statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
$statement->execute(array($p_id));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
if (!$result) {
	header('location: logout.php');
	exit;
}

if (!function_exists('adminCleanEditorHtml')) {
	function adminCleanEditorHtml($html) {
		$html = (string)$html;
		if ($html === '') return '';
		if (strpos($html, '&lt;') !== false && preg_match('/<(p|ul|ol|li|div|br)\b/i', $html) !== 1) {
			$html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		}
		$html = preg_replace('/<span[^>]*class="[^"]*PDq2pG_selectionAnchor[^"]*"[^>]*>.*?<\/span>/is', '', $html);
		$html = preg_replace('/<span[^>]*aria-hidden="true"[^>]*>\s*<\/span>/is', '', $html);
		$html = preg_replace('/\s+data-(?:section-id|start|end|is-last-node|testid)="[^"]*"/i', '', $html);
		$html = preg_replace('/<(script|style|iframe)[^>]*>.*?<\/\1>/is', '', $html);
		return trim(strip_tags($html, '<p><br><strong><b><em><i><u><ul><ol><li><h1><h2><h3><h4><h5><h6><a><img><span><div><blockquote><table><thead><tbody><tr><th><td><hr>'));
	}
}

$row = $result[0];
$p_name = $row['p_name'];
$p_featured_photo = $row['p_featured_photo'];
$p_description = $row['p_description'];
$p_short_description = $row['p_short_description'];
$p_feature = $row['p_feature'];
$p_is_featured = $row['p_is_featured'];
$p_is_active = $row['p_is_active'];
$ecat_id = (int)$row['ecat_id'];

if (isset($_POST['form1'])) {
	$valid = 1;

	if (empty($_POST['p_name'])) {
		$valid = 0;
		$error_message .= "Facility name cannot be empty<br>";
	}

	$path = $_FILES['p_featured_photo']['name'] ?? '';
	$path_tmp = $_FILES['p_featured_photo']['tmp_name'] ?? '';
	$imgErr = (int)($_FILES['p_featured_photo']['error'] ?? UPLOAD_ERR_NO_FILE);
	$ext = '';
	$hasNewFeatured = ($path !== '' && $imgErr === UPLOAD_ERR_OK && is_uploaded_file($path_tmp));
	if ($hasNewFeatured) {
		$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		if (!adminIsAllowedImageExt($ext, false)) {
			$valid = 0;
			$error_message .= 'Featured photo must be jpg, jpeg, png, gif or webp<br>';
			$hasNewFeatured = false;
		}
	}

	if ($valid == 1) {
		$ecatId = $ecat_id > 0 ? $ecat_id : resolveDefaultFacilityCategory($pdo);
		// Extra gallery photos
		if (!empty($_FILES['photo']['name'][0])) {
			$photoDir = '../assets/uploads/product_photos/';
			if (!is_dir($photoDir)) {
				@mkdir($photoDir, 0755, true);
			}
			for ($i = 0; $i < count($_FILES['photo']['name']); $i++) {
				if (empty($_FILES['photo']['name'][$i]) || (int)$_FILES['photo']['error'][$i] !== UPLOAD_ERR_OK) {
					continue;
				}
				$my_ext1 = strtolower(pathinfo($_FILES['photo']['name'][$i], PATHINFO_EXTENSION));
				if (!adminIsAllowedImageExt($my_ext1, false)) {
					continue;
				}
				$final_name1 = adminUniqueUploadName('facility-gallery', $my_ext1, $p_id);
				if (@move_uploaded_file($_FILES['photo']['tmp_name'][$i], $photoDir . $final_name1)) {
					$statement = $pdo->prepare("INSERT INTO tbl_product_photo (photo,p_id) VALUES (?,?)");
					$statement->execute(array($final_name1, $p_id));
				}
			}
		}

		$cleanDescription = adminCleanEditorHtml($_POST['p_description'] ?? '');
		$cleanShort = adminCleanEditorHtml($_POST['p_short_description'] ?? '');
		$cleanFeature = adminCleanEditorHtml($_POST['p_feature'] ?? '');
		$final_name = $p_featured_photo;

		if ($hasNewFeatured) {
			$final_name = adminUniqueUploadName('facility', $ext, $p_id);
			if (!adminMoveUploadedFile($path_tmp, $final_name)) {
				$valid = 0;
				$error_message .= 'Could not save featured photo. Check uploads folder permissions.<br>';
				$final_name = $p_featured_photo;
			}
		}

		if ($valid == 1) {
			$statement = $pdo->prepare("UPDATE tbl_product SET
				p_name=?,
				p_qty=?,
				p_featured_photo=?,
				p_description=?,
				p_short_description=?,
				p_feature=?,
				p_is_featured=?,
				p_is_active=?,
				ecat_id=?
				WHERE p_id=?");
			$statement->execute(array(
				trim($_POST['p_name']),
				1,
				$final_name,
				$cleanDescription,
				$cleanShort,
				$cleanFeature,
				((int)($_POST['p_is_featured'] ?? 0) === 1) ? 1 : 0,
				((int)($_POST['p_is_active'] ?? 0) === 1) ? 1 : 0,
				$ecatId,
				$p_id
			));

			if ($hasNewFeatured && $p_featured_photo !== '' && $p_featured_photo !== $final_name) {
				adminDeleteUploadIfUnused($pdo, $p_featured_photo, 'tbl_product', 'p_featured_photo', $p_id, 'p_id');
			}

			header('location: product.php?updated=1');
			exit;
		}
	}

	$p_name = trim($_POST['p_name'] ?? $p_name);
	$p_description = $_POST['p_description'] ?? $p_description;
	$p_short_description = $_POST['p_short_description'] ?? $p_short_description;
	$p_feature = $_POST['p_feature'] ?? $p_feature;
	$p_is_featured = $_POST['p_is_featured'] ?? $p_is_featured;
	$p_is_active = $_POST['p_is_active'] ?? $p_is_active;
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Edit Facility</h1>
	</div>
	<div class="content-header-right">
		<a href="product.php" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<?php if ($error_message): ?>
			<div class="callout callout-danger"><p><?php echo $error_message; ?></p></div>
			<?php endif; ?>
			<?php if ($success_message): ?>
			<div class="callout callout-success"><p><?php echo $success_message; ?></p></div>
			<?php endif; ?>

			<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
				<div class="box box-info">
					<div class="box-body">
						<div class="form-group">
							<label class="col-sm-3 control-label">Facility Name <span>*</span></label>
							<div class="col-sm-4">
								<input type="text" name="p_name" class="form-control" value="<?php echo htmlspecialchars($p_name); ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Existing Featured Photo</label>
							<div class="col-sm-4" style="padding-top:4px;">
								<?php if ($p_featured_photo !== '' && is_file(adminUploadsPath($p_featured_photo))): ?>
									<img src="<?php echo htmlspecialchars(adminUploadUrl($p_featured_photo)); ?>" class="existing-photo" alt="" style="width:150px;">
								<?php else: ?>
									<span class="text-muted">No photo on file</span>
								<?php endif; ?>
								<input type="hidden" name="current_photo" value="<?php echo htmlspecialchars($p_featured_photo); ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Change Featured Photo</label>
							<div class="col-sm-4" style="padding-top:4px;">
								<input type="file" name="p_featured_photo" accept="<?php echo htmlspecialchars(adminImageAcceptAttribute(false)); ?>">
								<span class="help-block">JPG, JPEG, PNG, GIF, WEBP</span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Other Photos</label>
							<div class="col-sm-4" style="padding-top:4px;">
								<table id="ProductTable" style="width:100%;">
									<tbody>
										<?php
										$statement = $pdo->prepare("SELECT * FROM tbl_product_photo WHERE p_id=?");
										$statement->execute(array($p_id));
										foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $prow) {
											?>
											<tr>
												<td>
													<img src="../assets/uploads/product_photos/<?php echo htmlspecialchars($prow['photo']); ?>" alt="" style="width:150px;margin-bottom:5px;">
												</td>
												<td style="width:28px;">
													<a onclick="return confirmDelete();" href="product-other-photo-delete.php?id=<?php echo (int)$prow['pp_id']; ?>&id1=<?php echo $p_id; ?>" class="btn btn-danger btn-xs">X</a>
												</td>
											</tr>
											<?php
										}
										?>
									</tbody>
								</table>
							</div>
							<div class="col-sm-2">
								<input type="button" id="btnAddNew" value="Add Item" style="margin-top:5px;margin-bottom:10px;border:0;color:#fff;font-size:14px;border-radius:3px;" class="btn btn-warning btn-xs">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Short Description</label>
							<div class="col-sm-8">
								<textarea name="p_short_description" class="form-control" cols="30" rows="6" id="editor2"><?php echo htmlspecialchars($p_short_description); ?></textarea>
								<p class="help-block">Shown on facility cards / listing.</p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Description</label>
							<div class="col-sm-8">
								<textarea name="p_description" class="form-control" cols="30" rows="10" id="editor1"><?php echo htmlspecialchars($p_description); ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Features</label>
							<div class="col-sm-8">
								<textarea name="p_feature" class="form-control" cols="30" rows="8" id="editor3"><?php echo htmlspecialchars($p_feature); ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Is Featured?</label>
							<div class="col-sm-4">
								<select name="p_is_featured" class="form-control" style="width:auto;">
									<option value="0" <?php echo ((string)$p_is_featured === '0') ? 'selected' : ''; ?>>No</option>
									<option value="1" <?php echo ((string)$p_is_featured === '1') ? 'selected' : ''; ?>>Yes</option>
								</select>
								<p class="help-block">Featured facilities appear first on the homepage.</p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Is Active?</label>
							<div class="col-sm-4">
								<select name="p_is_active" class="form-control" style="width:auto;">
									<option value="0" <?php echo ((string)$p_is_active === '0') ? 'selected' : ''; ?>>No</option>
									<option value="1" <?php echo ((string)$p_is_active === '1') ? 'selected' : ''; ?>>Yes</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="form1">Update Facility</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>

<?php require_once('footer.php'); ?>
