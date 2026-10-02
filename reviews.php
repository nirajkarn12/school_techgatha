<?php
require_once __DIR__ . '/inc/functions.php';
$reviews = [];
try {
    $reviews = $pdo->query("SELECT * FROM tbl_testimonial WHERE status = 'Active' ORDER BY sort_order ASC, id DESC")->fetchAll();
} catch (Throwable $e) {
    $reviews = [];
}

$siteName = (string) getSiteSetting('site_name', SITE_NAME);
$pageTitle = loadLang('reviews_title');
$snippet = '';
if ($reviews) {
    $snippet = seoCleanText($reviews[0]['review'] ?? '', 90);
}
$metaDescription = seoCleanText(
    loadLang('reviews_subtitle') . (count($reviews) ? ' (' . count($reviews) . ' ' . loadLang('reviews') . ')' : '') .
    ($snippet !== '' ? ' — ' . $snippet : ''),
    160
);
$metaKeywords = seoPick(
    'reviews, testimonials, cleaning reviews, ' . $siteName,
    getHomeSeo()['keywords']
);

include __DIR__ . '/inc/header.php';
$breadcrumbs = [
    ['label' => t('home'), 'url' => BASE_URL],
    ['label' => t('reviews'), 'url' => '']
];
echo renderBreadcrumbs($breadcrumbs);

?>
<div class="section-head mb-4">
  <div>
    <div class="section-kicker"><?php echo t('reviews'); ?></div>
    <h1 class="section-title"><?php echo t('reviews_title'); ?></h1>
    <p class="section-subtitle mb-0"><?php echo t('reviews_subtitle'); ?></p>
  </div>
</div>

<?php if (!$reviews) { ?>
  <div class="alert alert-light rounded-4"><?php echo t('no_reviews_yet'); ?></div>
<?php } else { ?>
  <div class="row g-4">
    <?php foreach ($reviews as $item) {
      $initial = strtoupper(mb_substr($item['name'], 0, 1));
      $role = trim($item['designation'] . ($item['company'] !== '' ? ' · ' . $item['company'] : ''));
    ?>
      <div class="col-md-6 col-lg-4 reveal">
        <article class="review-card h-100">
          <p class="review-text">“<?php echo e($item['review']); ?>”</p>
          <div class="review-author">
            <?php if (!empty($item['photo'])) { ?>
              <img src="<?php echo e(getProductImage($item['photo'])); ?>" alt="<?php echo e($item['name']); ?>">
            <?php } else { ?>
              <span class="review-avatar"><?php echo e($initial); ?></span>
            <?php } ?>
            <div>
              <strong><?php echo e($item['name']); ?></strong>
              <?php if ($role !== '') { ?><div class="text-muted small"><?php echo e($role); ?></div><?php } ?>
            </div>
          </div>
        </article>
      </div>
    <?php } ?>
  </div>
<?php } ?>

<section class="cta-band mt-5">
  <div class="cta-band-inner">
    <h2><?php echo t('cta_ready_title'); ?></h2>
    <p><?php echo t('cta_ready_text'); ?></p>
    <a href="book-service.php" class="btn btn-light btn-lg"><?php echo t('book_now'); ?></a>
  </div>
</section>
<?php include __DIR__ . '/inc/footer.php'; ?>
