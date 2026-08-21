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
                        <p class="small text-muted mb-1">
                            <strong><?php echo t('vacancy_department'); ?>:</strong> 
                            <?php echo e($job['department']); ?>
                        </p>
                    <?php } ?>
                    
                    <?php if (!empty($job['deadline'])) { ?>
                        <p class="small text-muted mb-2">
                            <strong><?php echo t('vacancy_deadline'); ?>:</strong> 
                            <?php echo e($job['deadline']); ?>
                        </p>
                    <?php } ?>
                    
                    <?php if (!empty($job['description'])) { ?>
                        <div class="small mb-3">
                            <?php echo nl2br(e(excerpt(strip_tags($job['description']), 220))); ?>
                        </div>
                    <?php } ?>
                    
                    <div class="d-flex gap-2 mt-3">
                        <a href="career-details.php?id=<?php echo (int)$job['id']; ?>" 
                           class="btn btn-outline-dark btn-sm">
                            <?php echo t('view_more'); ?>
                        </a>
                        <a href="career-details.php?id=<?php echo (int)$job['id']; ?>#apply" 
                           class="btn btn-dark btn-sm">
                            <?php echo t('apply_for_job'); ?>
                        </a>
                    </div>
                </article>
            </div>
        <?php } ?>
    </div>
<?php } ?>

<?php include __DIR__ . '/inc/footer.php'; ?>