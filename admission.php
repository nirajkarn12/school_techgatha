<?php
require_once __DIR__ . '/inc/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['admission_form'])) {
    handleAdmissionFormSubmission(BASE_URL . 'admission.php');
}

$pageTitle = loadLang('admission_form');
$metaDescription = seoCleanText(loadLang('admission_intro'), 160);

include __DIR__ . '/inc/header.php';
$breadcrumbs = [
    ['label' => t('home'), 'url' => BASE_URL],
    ['label' => t('categories'), 'url' => ''],
    ['label' => t('admission_form'), 'url' => ''],
];
echo renderBreadcrumbs($breadcrumbs);
echo renderFlash();
?>
<div class="section-head mb-4">
  <div class="section-kicker"><?php echo t('categories'); ?></div>
  <h1 class="section-title mb-2"><?php echo t('admission_form'); ?></h1>
  <p class="text-muted mb-0"><?php echo t('admission_intro'); ?></p>
</div>

<div class="card card-hover p-4">
  <form method="post" class="row g-3">
    <input type="hidden" name="csrf_token" value="<?php echo e(csrfToken()); ?>">
    <input type="hidden" name="admission_form" value="1">

    <div class="col-md-6">
      <label class="form-label"><?php echo t('student_name'); ?> *</label>
      <input class="form-control" name="student_name" required>
    </div>
    <div class="col-md-3">
      <label class="form-label"><?php echo t('date_of_birth'); ?></label>
      <input class="form-control" type="date" name="dob">
    </div>
    <div class="col-md-3">
      <label class="form-label"><?php echo t('gender'); ?></label>
      <select class="form-select" name="gender">
        <option value=""><?php echo t('select'); ?></option>
        <option value="Male"><?php echo t('gender_male'); ?></option>
        <option value="Female"><?php echo t('gender_female'); ?></option>
        <option value="Other"><?php echo t('gender_other'); ?></option>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label"><?php echo t('class_applied'); ?> *</label>
      <input class="form-control" name="class_applied" required placeholder="<?php echo t('class_applied_placeholder'); ?>">
    </div>
    <div class="col-md-6">
      <label class="form-label"><?php echo t('previous_school'); ?></label>
      <input class="form-control" name="previous_school">
    </div>
    <div class="col-md-6">
      <label class="form-label"><?php echo t('parent_name'); ?> *</label>
      <input class="form-control" name="parent_name" required>
    </div>
    <div class="col-md-3">
      <label class="form-label"><?php echo t('phone'); ?> *</label>
      <input class="form-control" name="phone" required>
    </div>
    <div class="col-md-3">
      <label class="form-label"><?php echo t('email_address'); ?></label>
      <input class="form-control" type="email" name="email">
    </div>
    <div class="col-12">
      <label class="form-label"><?php echo t('address'); ?></label>
      <textarea class="form-control" name="address" rows="2"></textarea>
    </div>
    <div class="col-12">
      <label class="form-label"><?php echo t('message'); ?></label>
      <textarea class="form-control" name="message" rows="3" placeholder="<?php echo t('admission_message_placeholder'); ?>"></textarea>
    </div>
    <div class="col-12">
      <button class="btn btn-dark" type="submit"><?php echo t('submit_admission'); ?></button>
    </div>
  </form>
</div>

<?php include __DIR__ . '/inc/footer.php'; ?>
