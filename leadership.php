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
  <div class="row g-4 align-items-start">
    <div class="col-md-4">
      <div class="card card-hover p-3 text-center">
        <?php if (!empty($row['photo'])) { ?>
          <img class="img-fluid rounded-4 mb-3" src="<?php echo getProductImage($row['photo']); ?>" alt="<?php echo e($row['person_name']); ?>">
        <?php } ?>
        <h3 class="h5 fw-bold mb-1"><?php echo e($row['person_name'] ?: $pageTitle); ?></h3>
        <p class="text-muted mb-0"><?php echo e($row['designation'] ?: $roles[$role]); ?></p>
      </div>
    </div>
    <div class="col-md-8">
      <div class="card card-hover p-4">
        <div class="content-body">
          <?php echo $row['message']; ?>
        </div>
      </div>
    </div>
  </div>
<?php } ?>

<?php include __DIR__ . '/inc/footer.php'; ?>
