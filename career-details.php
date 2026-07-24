<?php 
require_once __DIR__ . '/inc/functions.php';

$id = (int)($_GET['id'] ?? 0);
$job = getVacancyById($id);

if (!$job) {
    redirect(BASE_URL . 'careers.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['career_form'])) {
    handleCareerFormSubmission(BASE_URL . 'career-details.php?id=' . $id . '#apply');
}

$pageTitle = $job['title'] ?? 'Career Details';
$metaDescription = seoCleanText($job['description'] ?? '', 160);

include __DIR__ . '/inc/header.php';

$breadcrumbs = [
    ['label' => t('home'), 'url' => BASE_URL],
    ['label' => t('careers'), 'url' => BASE_URL . 'careers.php'],
    ['label' => $job['title'] ?? '', 'url' => ''],
];
echo renderBreadcrumbs($breadcrumbs);
echo renderFlash(); 
?>

<div class="section-head mb-4">
    <div class="section-kicker"><?php echo t('careers'); ?></div>
    <h1><?php echo e($job['title'] ?? ''); ?></h1>
    
    <?php if (!empty($job['department'])) { ?>
        <p><strong><?php echo t('vacancy_department'); ?>:</strong> <?php echo e($job['department']); ?></p>
    <?php } ?>
    
    <?php if (!empty($job['deadline'])) { ?>
        <p><strong><?php echo t('vacancy_deadline'); ?>:</strong> <?php echo e($job['deadline']); ?></p>
    <?php } ?>
</div>

<div class="card p-4 mb-5">
    <?php echo nl2br(e($job['description'] ?? '')); ?>
</div>

<div id="apply" class="card card-hover p-4">
    <h2 class="h4 fw-bold mb-3"><?php echo t('apply_for_job'); ?></h2>
    
    <form method="post" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrfToken()); ?>">
        <input type="hidden" name="career_form" value="1">
        <input type="hidden" name="vacancy_id" value="<?php echo (int)$job['id']; ?>">

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
            <textarea class="form-control" name="cover_letter" rows="5"></textarea>
        </div>
        <div class="col-12">
            <button class="btn btn-dark" type="submit"><?php echo t('submit_application'); ?></button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/inc/footer.php'; ?>