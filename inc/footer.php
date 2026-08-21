    <?php if (empty($fullWidth)) { ?>
    </div>
    <?php } ?>
</main>
<a href="#" class="back-to-top" id="backToTop" aria-label="<?php echo t('back_to_top'); ?>"><i class="fa fa-arrow-up"></i></a>
<?php
$whatsAppLink = getWhatsAppLink();
if ($whatsAppLink !== '') {
?>
<a href="<?php echo e($whatsAppLink); ?>" class="floating-wa" target="_blank" rel="noreferrer" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
<?php } ?>
<footer class="site-footer">
    <div class="container py-5">
        <div class="row g-4 align-items-start">
            <div class="col-lg-3">
                <div class="footer-brand mb-3">
                    <img src="<?php echo getProductImage(getSiteSetting('logo', 'logo.jpg')); ?>" alt="<?php echo t('brand_logo'); ?>">
                    <span><?php echo e(getSiteSetting('site_name', SITE_NAME)); ?></span>
                </div>
                <p class="text-white-50 mb-0"><?php echo t('crafted_with_care'); ?></p>
            </div>
            <div class="col-lg-3">
                <h5 class="fw-bold mb-3"><?php echo t('quick_links'); ?></h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?php echo BASE_URL; ?>admission.php"><?php echo t('admission_form'); ?></a></li>
                    <li><a href="<?php echo BASE_URL; ?>gallery.php"><?php echo t('gallery'); ?></a></li>
                    <li><a href="<?php echo BASE_URL; ?>about.php"><?php echo t('about'); ?></a></li>
                    <li><a href="<?php echo BASE_URL; ?>blog.php"><?php echo t('blog'); ?></a></li>
                    <li><a href="<?php echo BASE_URL; ?>careers.php"><?php echo t('careers'); ?></a></li>
                    <li><a href="<?php echo BASE_URL; ?>contact.php"><?php echo t('contact'); ?></a></li>
                </ul>
            </div>
            <div class="col-lg-3">
                <h5 class="fw-bold mb-3"><?php echo t('contact'); ?></h5>
                <p class="text-white-50 mb-2"><i class="fa fa-location-dot me-2"></i><?php echo e(getSiteSetting('contact_address', 'Kathmandu, Nepal')); ?></p>
                <p class="text-white-50 mb-2"><i class="fa fa-phone me-2"></i><?php echo e(getSiteSetting('contact_phone', '+977 9869224134')); ?></p>
                <p class="text-white-50 mb-0"><i class="fa fa-envelope me-2"></i><?php echo e(getSiteSetting('contact_email', 'contact@resinnepal.com.np')); ?></p>
            </div>
            <div class="col-lg-3">
                <?php if ((int)getSiteSetting('newsletter_on_off', 1) === 1) {
                    $newsletterText = trim((string)getSiteSetting('newsletter_text', ''));
                    if ($newsletterText === '') {
                        $newsletterText = loadLang('newsletter_default_text');
                    }
                ?>
                <h5 class="fw-bold mb-2"><?php echo t('newsletter'); ?></h5>
                <p class="text-white-50 small mb-3"><?php echo e($newsletterText); ?></p>
                <form class="footer-newsletter-form js-newsletter-form mb-4" action="<?php echo BASE_URL; ?>subscribe.php" method="post" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo e(csrfToken()); ?>">
                    <div class="footer-newsletter-row">
                        <input type="email" name="email" class="form-control" placeholder="<?php echo t('your_email'); ?>" required autocomplete="email">
                        <button type="submit" class="btn btn-light"><?php echo t('subscribe'); ?></button>
                    </div>
                    <div class="footer-newsletter-msg js-newsletter-msg small mt-2" aria-live="polite"></div>
                </form>
                <?php } ?>
                <h5 class="fw-bold mb-3"><?php echo t('follow_us'); ?></h5>
                <div class="d-flex flex-wrap gap-2 social-links">
                    <?php foreach (getSocialLinks() as $social) { ?>
                        <a href="<?php echo e($social['url']); ?>"
                           target="_blank"
                           rel="noreferrer"
                           class="social-link"
                           aria-label="<?php echo e($social['name']); ?>">
                            <i class="<?php echo e($social['icon']); ?>"></i>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center small gap-2">
            <span>
                <?php
                $copyright = getSiteSetting(
                    'footer_copyright',
                    '© {YEAR} ' . SITE_NAME . '. All rights reserved.'
                );
                echo e(str_replace('{YEAR}', date('Y'), $copyright));
                ?>
            </span>
        </div>
    </div>
</footer>
<div class="modal fade quick-view-modal" id="quickViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-3">
            <div class="modal-body" id="quickViewBody"></div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
<script>
$(function() {
    $('[data-fancybox="product-gallery"], [data-fancybox="home-gallery"], [data-fancybox="home-facilities"], [data-fancybox="school-team"], [data-fancybox="brochures"]').fancybox({
        buttons: [
            "slideShow",
            "fullScreen",
            "thumbs",
            "zoom",
            "close"
        ],
        loop: true,
        protect: true
    });
});
</script>
<script>window.BASE_URL = <?php echo json_encode(BASE_URL); ?>;</script>
<script src="<?php echo ASSET_URL; ?>js/app.js?v=20260724c"></script>
<?php
$scriptName = basename($_SERVER['SCRIPT_NAME'] ?? '');
if ($scriptName === 'gallery.php' || $scriptName === 'gallery-album.php') {
    echo '<script src="' . ASSET_URL . 'js/gallery-page.js?v=20260723a"></script>';
}
?>
</body>
</html>
