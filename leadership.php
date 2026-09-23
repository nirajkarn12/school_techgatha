<?php
require_once __DIR__ . '/inc/functions.php';

$roles = schoolLeadershipRoles();
$role = (string) ($_GET['role'] ?? 'principal');
if (!isset($roles[$role])) {
    $role = 'principal';
}

$row = getSchoolMessage($role);
$pageTitle = loadLang('leadership_' . $role);
$metaDescription = seoCleanText(($row['message'] ?? '') ?: $pageTitle, 160);

include __DIR__ . '/inc/header.php';
$breadcrumbs = [
    ['label' => t('home'), 'url' => BASE_URL],
    ['label' => t('categories'), 'url' => ''],
    ['label' => $pageTitle, 'url' => ''],
];
echo renderBreadcrumbs($breadcrumbs);
?>
<div class="section-head mb-4">
  <div class="section-kicker"><?php echo t('categories'); ?></div>
  <h1 class="section-title mb-2"><?php echo e($pageTitle); ?></h1>
</div>

<?php if (!$row) { ?>
  <div class="alert alert-light border rounded-4"><?php echo t('leadership_empty'); ?></div>
<?php } else { ?>
  <div class="leadership-profile-shell">
    <div class="leadership-profile-panel">
      <div class="leadership-profile-photo-wrap">
        <?php if (!empty($row['photo'])) { ?>
          <img class="leadership-profile-photo" src="<?php echo getProductImage($row['photo']); ?>" alt="<?php echo e($row['person_name']); ?>">
        <?php } ?>
      </div>
      <div class="leadership-profile-copy">
        <div class="section-kicker"><?php echo t('leadership'); ?></div>
        <h2 class="leadership-profile-name"><?php echo e($row['person_name'] ?: $pageTitle); ?></h2>
        <p class="leadership-profile-role"><?php echo e($row['designation'] ?: $roles[$role]); ?></p>
        <div class="content-body leadership-message-copy">
          <?php echo $row['message']; ?>
        </div>
      </div>
    </div>
  </div>
<?php } ?>

<?php include __DIR__ . '/inc/footer.php'; ?>
