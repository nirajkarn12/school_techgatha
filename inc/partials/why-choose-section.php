<?php
$whyFeatures = getActiveWhyFeatures();
if (!$whyFeatures) {
    return;
}

$mid = (int) ceil(count($whyFeatures) / 2);
$leftFeatures = array_slice($whyFeatures, 0, $mid);
$rightFeatures = array_slice($whyFeatures, $mid);

$whyHero = getWhyChooseHeroImage();
$brandLabel = strtoupper(trim((string) getSiteSetting('site_name', SITE_NAME)));
?>
<section class="why-choose-band reveal">
  <div class="why-choose-inner">
    <div class="why-choose-head">
      <div class="why-choose-kicker"><span><?php echo e($brandLabel); ?> ACADEMICS</span></div>
      <h2 class="why-choose-title">
        <span class="why-choose-title-light"><?php echo t('why_choose_why'); ?></span>
        <span class="why-choose-title-strong"><?php echo t('why_choose_rest'); ?></span>
      </h2>
    </div>

    <div class="why-choose-layout">
      <div class="why-choose-col why-choose-col--left">
        <?php foreach ($leftFeatures as $i => $feature) {
            $n = str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
            $iconClass = trim((string) ($feature['icon_class'] ?? 'fa-star'));
            if ($iconClass !== '' && strpos($iconClass, 'fa-') !== 0) {
                $iconClass = 'fa-' . ltrim($iconClass, '.');
            }
        ?>
        <article class="why-feature why-feature--left">
          <div class="why-feature-icon">
            <?php if (!empty($feature['icon'])): ?>
              <img src="<?php echo e(getProductImage($feature['icon'])); ?>" alt="">
            <?php else: ?>
              <i class="fa <?php echo e($iconClass); ?>"></i>
            <?php endif; ?>
            <span class="why-feature-num"><?php echo e($n); ?></span>
          </div>
          <p class="why-feature-text"><?php echo e($feature['title']); ?></p>
        </article>
        <?php } ?>
      </div>

      <div class="why-choose-center">
        <img class="why-choose-hero" src="<?php echo e($whyHero); ?>" alt="<?php echo e($brandLabel); ?>" loading="lazy">
      </div>

      <div class="why-choose-col why-choose-col--right">
        <?php foreach ($rightFeatures as $i => $feature) {
            $n = str_pad((string) (count($leftFeatures) + $i + 1), 2, '0', STR_PAD_LEFT);
            $iconClass = trim((string) ($feature['icon_class'] ?? 'fa-star'));
            if ($iconClass !== '' && strpos($iconClass, 'fa-') !== 0) {
                $iconClass = 'fa-' . ltrim($iconClass, '.');
            }
        ?>
        <article class="why-feature why-feature--right">
          <p class="why-feature-text"><?php echo e($feature['title']); ?></p>
          <div class="why-feature-icon">
            <?php if (!empty($feature['icon'])): ?>
              <img src="<?php echo e(getProductImage($feature['icon'])); ?>" alt="">
            <?php else: ?>
              <i class="fa <?php echo e($iconClass); ?>"></i>
            <?php endif; ?>
            <span class="why-feature-num"><?php echo e($n); ?></span>
          </div>
        </article>
        <?php } ?>
      </div>
    </div>
  </div>
</section>
