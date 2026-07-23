<?php
require_once __DIR__ . '/inc/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['career_form'])) {
    handleCareerFormSubmission(BASE_URL . 'careers.php');
}

$vacancies = getActiveVacancies();
$preselect = (int) ($_GET['vacancy'] ?? 0);
$pageTitle = loadLang('careers_title');
$metaDescription = seoCleanText(loadLang('careers_intro'), 160);

include __DIR__ . '/inc/header.php';
$breadcrumbs = [
    ['label' => t('home'), 'url' => BASE_URL],
    ['label' => t('careers'), 'url' => ''],
];
echo renderBreadcrumbs($breadcrumbs);
echo renderFlash();
?>
<div class="section-head mb-4">
  <div class="section-kicker"><?php echo t('careers'); ?></div>
  <h1 class="section-title mb-2"><?php echo t('careers_title'); ?></h1>
  <p class="text-muted mb-0"><?php echo t('careers_intro'); ?></p>
</div>

<?php if (!$vacancies) { ?>
  <div class="alert alert-light border rounded-4 mb-4"><?php echo t('no_vacancies'); ?></div>
<?php } else { ?>
  <div class="row g-3 mb-5">
    <?php foreach ($vacancies as $job) { ?>
      <div class="col-md-6">
        <article class="card card-hover h-100 p-4">
          <h3 class="h5 fw-bold mb-2"><?php echo e($job['title']); ?></h3>
          <?php if (!empty($job['department'])) { ?>
            <p class="small text-muted mb-1"><strong><?php echo t('vacancy_department'); ?>:</strong> <?php echo e($job['department']); ?></p>
          <?php } ?>
          <?php if (!empty($job['deadline'])) { ?>
            <p class="small text-muted mb-2"><strong><?php echo t('vacancy_deadline'); ?>:</strong> <?php echo e($job['deadline']); ?></p>
          <?php } ?>
          <?php if (!empty($job['description'])) { ?>
            <div class="small mb-3"><?php echo nl2br(e(excerpt(strip_tags($job['description']), 220))); ?></div>
          <?php } ?>
          <a class="btn btn-outline-dark btn-sm" href="careers.php?vacancy=<?php echo (int)$job['id']; ?>#apply"><?php echo t('apply_for_job'); ?></a>
        </article>
      </div>
    <?php } ?>
  </div>
<?php } ?>

<?php if ($vacancies) { ?>
<div class="card card-hover p-4" id="apply">
  <h2 class="h4 fw-bold mb-3"><?php echo t('apply_for_job'); ?></h2>
  <form method="post" class="row g-3">
    <input type="hidden" name="csrf_token" value="<?php echo e(csrfToken()); ?>">
    <input type="hidden" name="career_form" value="1">
    <div class="col-md-6">
      <label class="form-label"><?php echo t('select_vacancy'); ?> *</label>
      <select class="form-select" name="vacancy_id" required>
        <option value=""><?php echo t('select_vacancy'); ?></option>
        <?php foreach ($vacancies as $job) { ?>
          <option value="<?php echo (int)$job['id']; ?>" <?php echo $preselect === (int)$job['id'] ? 'selected' : ''; ?>>
            <?php echo e($job['title']); ?>
          </option>
        <?php } ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label"><?php echo t('full_name'); ?> *</label>
      <input class="form-control" name="full_name" required>
    </div>
    <div class="col-md-6">
      <label class="form-label"><?php echo t('phone'); ?> *</label>
      <input class="form-control" name="phone" required>
    </div>
    <div class="col-md-6">
      <label class="form-label"><?php echo t('email_address'); ?> *</label>
      <input class="form-control" type="email" name="email" required>
    </div>
    <div class="col-12">
      <label class="form-label"><?php echo t('resume_note'); ?></label>
      <textarea class="form-control" name="resume_note" rows="3"></textarea>
    </div>
    <div class="col-12">
      <label class="form-label"><?php echo t('cover_letter'); ?></label>
      <textarea class="form-control" name="cover_letter" rows="4"></textarea>
    </div>
    <div class="col-12">
      <button class="btn btn-dark" type="submit"><?php echo t('submit_application'); ?></button>
    </div>
  </form>
</div>
<?php } ?>

<?php include __DIR__ . '/inc/footer.php'; ?>
