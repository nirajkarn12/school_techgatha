<?php require_once('header.php'); ?>

<?php
$error_message = '';
$success_message = '';

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

if (isset($_POST['form1'])) {
	$valid = 1;

	if (empty($_POST['p_name'])) {
		$valid = 0;
		$error_message .= "Facility name cannot be empty<br>";
	}

	$ext = '';
	$featuredTmp = $_FILES['p_featured_photo']['tmp_name'] ?? '';
	$featuredErr = (int)($_FILES['p_featured_photo']['error'] ?? UPLOAD_ERR_NO_FILE);
	if (!empty($_FILES['p_featured_photo']['name']) && $featuredErr === UPLOAD_ERR_OK && is_uploaded_file($featuredTmp)) {
		$ext = strtolower(pathinfo($_FILES['p_featured_photo']['name'], PATHINFO_EXTENSION));
		if (!adminIsAllowedImageExt($ext, false)) {
			$valid = 0;
			$error_message .= "Featured photo must be jpg, jpeg, png, gif or webp<br>";
		}
	} else {
		$valid = 0;
		$error_message .= "Featured photo is required<br>";
	}

	if ($valid == 1) {
		$ecatId = resolveDefaultFacilityCategory($pdo);
		$statement = $pdo->prepare("
			INSERT INTO tbl_product (
				p_name,
				p_old_price,
				p_current_price,
				p_qty,
				p_featured_photo,
				p_description,
				p_short_description,
				p_feature,
				p_condition,
				p_return_policy,
				p_total_view,
				p_is_featured,
				p_is_active,
				ecat_id
			) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
		");
		$statement->execute([
			trim($_POST['p_name']),
			0,
			0,
			1,
			'',
			adminCleanEditorHtml($_POST['p_description'] ?? ''),
			adminCleanEditorHtml($_POST['p_short_description'] ?? ''),
			adminCleanEditorHtml($_POST['p_feature'] ?? ''),
			'',
			'',
			0,
			((int)($_POST['p_is_featured'] ?? 0) === 1) ? 1 : 0,
			((int)($_POST['p_is_active'] ?? 0) === 1) ? 1 : 0,
			$ecatId
		]);

		$p_id = (int)$pdo->lastInsertId();
		$featured_name = adminUniqueUploadName('facility', $ext, $p_id);
		if (!adminMoveUploadedFile($featuredTmp, $featured_name)) {
			$pdo->prepare("DELETE FROM tbl_product WHERE p_id=?")->execute([$p_id]);
			$error_message .= 'Could not save featured photo. Check uploads folder permissions.<br>';
		} else {
			$statement = $pdo->prepare("UPDATE tbl_product SET p_featured_photo=? WHERE p_id=?");
			$statement->execute([$featured_name, $p_id]);

			if (!empty($_FILES['photo']['name'][0])) {
				$photoDir = '../assets/uploads/product_photos/';
				if (!is_dir($photoDir)) {
					@mkdir($photoDir, 0755, true);
				}
				for ($i = 0; $i < count($_FILES['photo']['name']); $i++) {
					if (empty($_FILES['photo']['name'][$i]) || (int)$_FILES['photo']['error'][$i] !== UPLOAD_ERR_OK) {
						continue;
					}
					$ext1 = strtolower(pathinfo($_FILES['photo']['name'][$i], PATHINFO_EXTENSION));
					if (!adminIsAllowedImageExt($ext1, false)) {
						continue;
					}
					$photo_name = adminUniqueUploadName('facility-gallery', $ext1, $p_id);
					if (@move_uploaded_file($_FILES['photo']['tmp_name'][$i], $photoDir . $photo_name)) {
						$statement = $pdo->prepare("INSERT INTO tbl_product_photo (photo, p_id) VALUES (?,?)");
						$statement->execute([$photo_name, $p_id]);
					}
				}
			}

			header('location: product.php?success=1');
			exit;
		}
	}
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Add Facility</h1>
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
								<input type="text" name="p_name" class="form-control" value="<?php echo isset($_POST['p_name']) ? htmlspecialchars($_POST['p_name']) : ''; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Featured Photo <span>*</span></label>
							<div class="col-sm-4" style="padding-top:4px;">
								<input type="file" name="p_featured_photo" accept="<?php echo htmlspecialchars(adminImageAcceptAttribute(false)); ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Other Photos</label>
							<div class="col-sm-4" style="padding-top:4px;">
								<table id="ProductTable" style="width:100%;">
									<tbody>
										<tr>
											<td>
												<div class="upload-btn">
													<input type="file" name="photo[]" style="margin-bottom:5px;">
												</div>
											</td>
											<td style="width:28px;"><a href="javascript:void()" class="Delete btn btn-danger btn-xs">X</a></td>
										</tr>
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
								<textarea name="p_short_description" class="form-control" cols="30" rows="6" id="editor2"><?php echo isset($_POST['p_short_description']) ? htmlspecialchars($_POST['p_short_description']) : ''; ?></textarea>
								<p class="help-block">Shown on facility cards / listing.</p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Description</label>
							<div class="col-sm-8">
								<textarea name="p_description" class="form-control" cols="30" rows="10" id="editor1"><?php echo isset($_POST['p_description']) ? htmlspecialchars($_POST['p_description']) : ''; ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Features</label>
							<div class="col-sm-8">
								<textarea name="p_feature" class="form-control" cols="30" rows="8" id="editor3"><?php echo isset($_POST['p_feature']) ? htmlspecialchars($_POST['p_feature']) : ''; ?></textarea>
								<p class="help-block">Optional list of highlights on the detail page.</p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Is Featured?</label>
							<div class="col-sm-4">
								<select name="p_is_featured" class="form-control" style="width:auto;">
									<option value="0">No</option>
									<option value="1" selected>Yes</option>
								</select>
								<p class="help-block">Featured facilities appear first on the homepage.</p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Is Active?</label>
							<div class="col-sm-4">
								<select name="p_is_active" class="form-control" style="width:auto;">
									<option value="0">No</option>
									<option value="1" selected>Yes</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="form1">Add Facility</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>

<?php require_once('footer.php'); ?>
