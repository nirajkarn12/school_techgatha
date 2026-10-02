<?php
require_once __DIR__ . '/inc/functions.php';

$pageTitle = loadLang('faqs');
$metaDescription = seoCleanText($pageTitle, 160);
$faqs = [];

try {
    $faqs = $pdo->query('SELECT faq_id, faq_title, faq_content FROM tbl_faq ORDER BY faq_id ASC')->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $faqs = [];
}

include __DIR__ . '/inc/header.php';
$breadcrumbs = [
    ['label' => t('home'), 'url' => BASE_URL],
    ['label' => $pageTitle, 'url' => ''],
];
echo renderBreadcrumbs($breadcrumbs);
?>

<section id="faq" class="section-block">
    <div class="section-head">
        <div>
            <div class="section-kicker"><?php echo t('faqs'); ?></div>
            <h1 class="section-title"><?php echo t('faqs'); ?></h1>
        </div>
    </div>

    <div class="accordion faq-accordion" id="faqAccordion">
        <?php if ($faqs) { foreach ($faqs as $faq) { ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeading<?php echo (int)$faq['faq_id']; ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse<?php echo (int)$faq['faq_id']; ?>" aria-expanded="false" aria-controls="faqCollapse<?php echo (int)$faq['faq_id']; ?>">
                        <span class="faq-question-icon"><i class="fa fa-question-circle"></i></span>
                        <?php echo e($faq['faq_title']); ?>
                    </button>
                </h2>
                <div id="faqCollapse<?php echo (int)$faq['faq_id']; ?>" class="accordion-collapse collapse" aria-labelledby="faqHeading<?php echo (int)$faq['faq_id']; ?>" data-bs-parent="#faqAccordion">
                    <div class="accordion-body faq-answer">
                        <div class="faq-answer-text rich-content"><?php echo renderRichHtml($faq['faq_content']); ?></div>
                    </div>
                </div>
            </div>
        <?php } } else { ?>
            <div class="alert alert-light rounded-4"><?php echo t('no_faqs_yet'); ?></div>
        <?php } ?>
    </div>
</section>

<?php include __DIR__ . '/inc/footer.php'; ?>
