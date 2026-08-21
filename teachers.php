<?php
require_once __DIR__ . '/inc/functions.php';

$teachers = getQualifiedTeachers();
$teacherGroups = groupTeachersByLevel($teachers);
$pageTitle = loadLang('our_team_page');
$metaDescription = seoCleanText(loadLang('our_team_page_subtitle'), 160);

include __DIR__ . '/inc/header.php';
$breadcrumbs = [
    ['label' => t('home'), 'url' => BASE_URL],
    ['label' => t('our_team_page'), 'url' => ''],
];
echo renderBreadcrumbs($breadcrumbs);
?>
<div class="section-head mb-4">
  <div class="section-kicker"><?php echo t('categories'); ?></div>
  <h1 class="section-title mb-2"><?php echo t('our_team_page'); ?></h1>
  <p class="text-muted mb-0"><?php echo t('our_team_page_subtitle'); ?></p>
</div>

<?php if (!$teacherGroups) { ?>
  <div class="alert alert-light border rounded-4"><?php echo t('no_teachers_yet'); ?></div>
<?php } else { ?>
  <?php foreach ($teacherGroups as $group) { ?>
    <section class="team-level-block">
      <h2 class="team-level-title"><?php echo e($group['name']); ?></h2>
      <div class="row g-4 team-photo-grid">
        <?php foreach ($group['teachers'] as $teacher) {
            $photo = getProductImage($teacher['photo'] ?? 'user-1.jpg');
            $name = $teacher['full_name'] ?? '';
            $role = $teacher['designation'] ?: loadLang('teacher');
            $caption = $name . ($role !== '' ? ' — ' . $role : '');
            ?>
          <div class="col-6 col-md-4 col-lg-3">
            <a
              href="<?php echo e($photo); ?>"
              class="team-photo-card"
              data-fancybox="school-team"
              data-caption="<?php echo e($caption); ?>"
            >
              <img src="<?php echo e($photo); ?>" alt="<?php echo e($name); ?>" loading="lazy">
              <span class="team-photo-meta">
                <span class="team-photo-name"><?php echo e($name); ?></span>
                <span class="team-photo-role"><?php echo e($role); ?></span>
              </span>
            </a>
          </div>
        <?php } ?>
      </div>
    </section>
  <?php } ?>
<?php } ?>

<?php include __DIR__ . '/inc/footer.php'; ?>
