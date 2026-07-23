<?php
require_once __DIR__ . '/inc/functions.php';

$albumId = (int) ($_GET['id'] ?? 0);
if ($albumId <= 0) {
    header('Location: ' . BASE_URL . 'gallery.php');
    exit;
}

$album = null;
$photos = [];
$totalPhotos = 0;
$pagination = buildPagination(0, 12);

try {
    $stmt = $pdo->prepare("SELECT * FROM tbl_gallery_album WHERE id = ? AND status = 'Active' LIMIT 1");
    $stmt->execute([$albumId]);
    $album = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

    if ($album) {
        $countStmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM tbl_gallery
            WHERE album_id = ?
              AND status = 'Active'
        ");
        $countStmt->execute([$albumId]);
        $totalPhotos = (int) $countStmt->fetchColumn();
        $pagination = buildPagination($totalPhotos, 12);

        $photoStmt = $pdo->prepare("
            SELECT *
            FROM tbl_gallery
            WHERE album_id = ?
              AND status = 'Active'
            ORDER BY sort_order ASC, id ASC
            LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}
        ");
        $photoStmt->execute([$albumId]);
        $photos = $photoStmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Throwable $e) {
    $album = null;
    $photos = [];
}

if (!$album) {
    header('Location: ' . BASE_URL . 'gallery.php');
    exit;
}

$pageTitle = $album['title'];
$metaDescription = seoCleanText($album['description'] ?: $album['title'], 160);
if (!empty($album['cover_photo'])) {
    $ogImage = $album['cover_photo'];
}

include __DIR__ . '/inc/header.php';
$breadcrumbs = [
    ['label' => t('home'), 'url' => BASE_URL],
    ['label' => t('gallery'), 'url' => BASE_URL . 'gallery.php'],
    ['label' => $album['title'], 'url' => ''],
];
echo renderBreadcrumbs($breadcrumbs);
?>
<div class="section-head mb-4 gallery-page-head">
  <div>
    <div class="section-kicker"><?php echo t('gallery'); ?></div>
    <h1 class="section-title"><?php echo e($album['title']); ?></h1>
    <?php if (!empty($album['description'])) { ?>
      <p class="section-subtitle mb-0"><?php echo e($album['description']); ?></p>
    <?php } else { ?>
      <p class="section-subtitle mb-0">
        <?php echo (int) $totalPhotos; ?> <?php echo $totalPhotos === 1 ? t('gallery_photo_one') : t('gallery_photos'); ?>
      </p>
    <?php } ?>
  </div>
  <a href="<?php echo BASE_URL; ?>gallery.php" class="btn btn-outline-dark btn-sm"><?php echo t('back_to_albums'); ?></a>
</div>

<?php if (!$photos) { ?>
  <div class="alert alert-light rounded-4"><?php echo t('no_gallery_yet'); ?></div>
<?php } else { ?>
  <div class="gallery-album-photos" id="galleryMosaic">
    <?php foreach ($photos as $item) {
        $img = getProductImage($item['photo']);
        $title = trim((string) ($item['title'] ?? $album['title']));
        $caption = trim((string) ($item['content'] ?? ''));
        ?>
      <a
        href="<?php echo e($img); ?>"
        class="gallery-album-photo"
        data-fancybox="album-<?php echo (int) $albumId; ?>-p<?php echo (int) $pagination['page']; ?>"
        data-caption="<?php echo e($title . ($caption !== '' ? ' — ' . $caption : '')); ?>"
      >
        <img src="<?php echo e($img); ?>" alt="<?php echo e($title); ?>" loading="lazy">
        <span class="gallery-album-photo-zoom" aria-hidden="true"><i class="fa fa-search-plus"></i></span>
      </a>
    <?php } ?>
  </div>
  <?php echo renderPagination($pagination); ?>
<?php } ?>

<?php include __DIR__ . '/inc/footer.php'; ?>
