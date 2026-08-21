<?php
$brochures = getActiveBrochures();
if (!$brochures) {
    return;
}
?>
<section class="section-block home-gallery-showcase brochure-showcase reveal">
  <div class="home-gallery-head">
    <h2 class="home-gallery-title"><?php echo t('brochure_prospectus'); ?></h2>
    <div class="home-gallery-nav">
      <button type="button" class="home-gallery-btn brochure-prev" aria-label="<?php echo t('previous'); ?>"><i class="fa fa-arrow-left"></i></button>
      <button type="button" class="home-gallery-btn home-gallery-next brochure-next" aria-label="<?php echo t('next'); ?>"><i class="fa fa-arrow-right"></i></button>
    </div>
  </div>
  <div class="swiper brochureSwiper">
    <div class="swiper-wrapper">
      <?php foreach ($brochures as $item) {
        $img = getProductImage($item['image'] ?? '');
        $title = trim((string) ($item['title'] ?? ''));
        if ($title === '') {
            $title = loadLang('brochure_prospectus');
        }
        $year = trim((string) ($item['year'] ?? ''));
        $file = trim((string) ($item['file'] ?? ''));
        $label = $title . ($year !== '' ? ' — ' . $year : '');
        $href = $file !== '' ? (BASE_URL . 'assets/uploads/' . rawurlencode($file)) : $img;
        $isDownload = $file !== '';
      ?>
      <div class="swiper-slide">
        <a
          href="<?php echo e($href); ?>"
          class="home-gallery-card"
          <?php if ($isDownload) { ?>
          target="_blank"
          rel="noopener noreferrer"
          download
          <?php } else { ?>
          data-fancybox="brochures"
          data-caption="<?php echo e($label); ?>"
          <?php } ?>
        >
          <img src="<?php echo e($img); ?>" alt="<?php echo e($title); ?>" loading="lazy">
          <span class="home-gallery-shade"></span>
          <span class="home-gallery-meta">
            <span class="home-gallery-label"><?php echo e($label); ?></span>
            <span class="home-gallery-go" aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
          </span>
        </a>
      </div>
      <?php } ?>
    </div>
    <div class="swiper-pagination brochure-dots home-gallery-dots"></div>
  </div>
</section>
