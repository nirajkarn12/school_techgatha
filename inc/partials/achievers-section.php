<?php
if (!isset($homeAchievers)) {
    $limit = isset($achieversLimit) ? (int) $achieversLimit : 4;
    $homeAchievers = getActiveAchievers($limit > 0 ? $limit : 0);
}
if (!$homeAchievers) {
    return;
}
$showSeeMore = !isset($hideAchieversSeeMore) || !$hideAchieversSeeMore;
$totalAchievers = isset($achieversTotal) ? (int) $achieversTotal : count(getActiveAchievers());
?>
<section class="achievers-band reveal">
  <div class="achievers-inner">
    <div class="achievers-head">
      <div class="achievers-kicker"><span><?php echo t('success_stories'); ?></span></div>
      <h2 class="achievers-title">
        <span class="achievers-title-light"><?php echo t('high_achievers_light'); ?></span>
        <span class="achievers-title-strong"><?php echo t('high_achievers_strong'); ?></span>
      </h2>
    </div>

    <div class="achievers-grid">
      <?php foreach ($homeAchievers as $achiever) {
        $photo = getProductImage($achiever['photo'] ?? '');
        $name = trim((string) ($achiever['name'] ?? ''));
        $achievement = trim((string) ($achiever['achievement'] ?? ''));
        $year = trim((string) ($achiever['year'] ?? ''));
      ?>
      <article class="achiever-card">
        <div class="achiever-photo">
          <img src="<?php echo e($photo); ?>" alt="<?php echo e($name); ?>" loading="lazy">
        </div>
        <h3 class="achiever-name"><?php echo e($name); ?></h3>
        <?php if ($achievement !== ''): ?>
          <p class="achiever-meta"><?php echo e($achievement); ?></p>
        <?php endif; ?>
        <?php if ($year !== ''): ?>
          <p class="achiever-year"><?php echo e($year); ?></p>
        <?php endif; ?>
      </article>
      <?php } ?>
    </div>

    <?php if ($showSeeMore && $totalAchievers > count($homeAchievers)): ?>
    <div class="achievers-more">
      <a href="<?php echo BASE_URL; ?>achievers.php"><?php echo t('see_more'); ?> <i class="fa fa-chevron-right"></i></a>
    </div>
    <?php endif; ?>
  </div>
</section>
