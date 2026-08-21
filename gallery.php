<?php
require_once __DIR__ . '/inc/functions.php';

$albums = [];
$totalAlbums = 0;
$pagination = buildPagination(0, 12);

try {
    $totalAlbums = (int) $pdo->query("SELECT COUNT(*) FROM tbl_gallery_album WHERE status = 'Active'")->fetchColumn();
    $pagination = buildPagination($totalAlbums, 12);
    $albums = $pdo->query("
        SELECT
            a.*,
            (
                SELECT COUNT(*)
                FROM tbl_gallery g
                WHERE g.album_id = a.id
                  AND g.status = 'Active'
            ) AS photo_count
        FROM tbl_gallery_album a
        WHERE a.status = 'Active'
        ORDER BY a.sort_order ASC, a.id DESC
        LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $albums = [];
    $totalAlbums = 0;
}

// Fallback: if no albums table/data yet, show legacy flat gallery tiles
$legacyItems = [];
$legacyPagination = buildPagination(0, 12);
if (!$albums && $totalAlbums === 0) {
    try {
        $totalLegacy = (int) $pdo->query("
            SELECT COUNT(*) FROM tbl_gallery
            WHERE status = 'Active'
              AND (album_id IS NULL OR album_id = 0)
        ")->fetchColumn();
        $legacyPagination = buildPagination($totalLegacy, 12);
        $legacyItems = $pdo->query("
            SELECT g.*
            FROM tbl_gallery g
            WHERE g.status = 'Active'
              AND (g.album_id IS NULL OR g.album_id = 0)
            ORDER BY g.sort_order ASC, g.id DESC
            LIMIT {$legacyPagination['per_page']} OFFSET {$legacyPagination['offset']}
        ")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        try {
            $totalLegacy = (int) $pdo->query("SELECT COUNT(*) FROM tbl_gallery WHERE status = 'Active'")->fetchColumn();
            $legacyPagination = buildPagination($totalLegacy, 12);
            $legacyItems = $pdo->query("
                SELECT g.* FROM tbl_gallery g
                WHERE g.status = 'Active'
                ORDER BY g.sort_order ASC, g.id DESC
                LIMIT {$legacyPagination['per_page']} OFFSET {$legacyPagination['offset']}
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e2) {
            $legacyItems = [];
        }
    }
}

$pageTitle = loadLang('gallery');
$metaDescription = seoCleanText(loadLang('gallery_subtitle'), 160);

include __DIR__ . '/inc/header.php';
$breadcrumbs = [
    ['label' => t('home'), 'url' => BASE_URL],
    ['label' => t('gallery'), 'url' => ''],
];
echo renderBreadcrumbs($breadcrumbs);
?>
<div class="section-head mb-4 gallery-page-head">
  <div>
    <div class="section-kicker"><?php echo t('gallery'); ?></div>
    <h1 class="section-title"><?php echo t('gallery_title'); ?></h1>
    <p class="section-subtitle mb-0"><?php echo t('gallery_albums_subtitle'); ?></p>
  </div>
  <?php if ($totalAlbums > 0) { ?>
  <div class="gallery-count-pill">
    <span><?php echo (int) $totalAlbums; ?></span>
    <small><?php echo t('gallery_albums'); ?></small>
  </div>
  <?php } ?>
</div>

<?php if ($albums) { ?>
  <div class="gallery-album-grid">
    <?php foreach ($albums as $album) {
        $count = (int) ($album['photo_count'] ?? 0);
        $cover = !empty($album['cover_photo'])
            ? getProductImage($album['cover_photo'])
            : ASSET_URL . 'images/placeholder.svg';
        ?>
      <a class="gallery-album-card" href="<?php echo BASE_URL; ?>gallery-album.php?id=<?php echo (int) $album['id']; ?>">
        <span class="gallery-album-cover">
          <img src="<?php echo e($cover); ?>" alt="<?php echo e($album['title']); ?>" loading="lazy">
        </span>
        <span class="gallery-album-body">
          <strong class="gallery-album-title"><?php echo e($album['title']); ?></strong>
          <span class="gallery-album-count">
            <?php echo $count; ?> <?php echo $count === 1 ? t('gallery_photo_one') : t('gallery_photos'); ?>
          </span>
        </span>
      </a>
    <?php } ?>
  </div>
  <?php echo renderPagination($pagination); ?>
<?php } elseif ($legacyItems) { ?>
  <div class="gallery-mosaic" id="galleryMosaic">
    <?php foreach ($legacyItems as $index => $item) {
        $img = getProductImage($item['photo']);
        $title = trim((string) $item['title']);
        $caption = trim((string) ($item['content'] ?? ''));
        ?>
      <a
        href="<?php echo e($img); ?>"
        class="gallery-tile gallery-animate"
        data-fancybox="gallery-legacy"
        data-caption="<?php echo e($title . ($caption !== '' ? ' — ' . $caption : '')); ?>"
        style="--gallery-delay: <?php echo min($index, 12) * 55; ?>ms;"
      >
        <span class="gallery-tile-media">
          <img src="<?php echo e($img); ?>" alt="<?php echo e($title); ?>" loading="lazy">
        </span>
        <span class="gallery-tile-overlay">
          <span class="gallery-tile-zoom"><i class="fa fa-search-plus"></i></span>
          <span class="gallery-tile-meta">
            <strong><?php echo e($title); ?></strong>
          </span>
        </span>
      </a>
    <?php } ?>
  </div>
  <?php echo renderPagination($legacyPagination); ?>
<?php } else { ?>
  <div class="alert alert-light rounded-4"><?php echo t('no_gallery_yet'); ?></div>
<?php } ?>

<?php include __DIR__ . '/inc/footer.php'; ?>
