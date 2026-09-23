<?php
require_once __DIR__ . '/inc/functions.php';

$brandName = e(getSiteSetting('site_name', SITE_NAME));
$pageTitle = 'Brochure | ' . $brandName;
$metaDescription = 'View our digital brochure online.';
$metaKeywords = 'brochure, digital brochure, ' . $brandName;
$fullWidth = true;
$showWaterSplash = false;

include __DIR__ . '/inc/header.php';
?>

<style>
.brochure-page {
    background: #f7f8fa;
    padding: 50px 0 70px;
}

.brochure-header {
    text-align: center;
    margin-bottom: 30px;
}

.brochure-kicker {
    font-size: 13px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    opacity: .65;
    margin-bottom: 8px;
}

.brochure-title {
    margin: 0 0 10px;
    font-size: clamp(30px, 4vw, 48px);
    font-weight: 800;
}

.brochure-description {
    max-width: 720px;
    margin: 0 auto;
    color: #6c757d;
    font-size: 16px;
}

.brochure-viewer {
    width: min(1400px, 94vw);
    margin: 0 auto;
    background: #fff;
    border-radius: 18px;
    padding: 12px;
    box-shadow: 0 15px 45px rgba(0, 0, 0, .10);
    overflow: hidden;
}

.brochure-iframe-wrap {
    position: relative;
    width: 100%;
    height: min(78vh, 900px);
    min-height: 600px;
    overflow: hidden;
    border-radius: 12px;
    background: #eee;
}

.brochure-iframe {
    width: 100%;
    height: 100%;
    border: 0;
    display: block;
}

.brochure-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 22px;
}

.brochure-actions a {
    text-decoration: none;
}

@media (max-width: 767px) {
    .brochure-page {
        padding: 30px 0 45px;
    }

    .brochure-viewer {
        width: 96vw;
        padding: 6px;
        border-radius: 12px;
    }

    .brochure-iframe-wrap {
        height: 72vh;
        min-height: 500px;
        border-radius: 8px;
    }

    .brochure-description {
        padding: 0 15px;
        font-size: 15px;
    }
}
</style>

<main class="brochure-page">
    <div class="brochure-header">
        <div class="brochure-kicker"><?php echo $brandName; ?></div>
        <h1 class="brochure-title">Our Brochure</h1>
        <p class="brochure-description">
            Explore our digital brochure and learn more about our services, facilities and offerings.
        </p>
    </div>

    <div class="brochure-viewer">
        <div class="brochure-iframe-wrap">
            <iframe
                class="brochure-iframe"
                src="https://anyflip.com/zvjup/pjmz/"
                title="<?php echo e($brandName); ?> Digital Brochure"
                loading="lazy"
                allowfullscreen
                referrerpolicy="strict-origin-when-cross-origin">
            </iframe>
        </div>

        <div class="brochure-actions">
            <a
                href="https://anyflip.com/zvjup/pjmz/"
                target="_blank"
                rel="noopener noreferrer"
                class="btn btn-dark">
                Open Brochure in New Tab <i class="fa fa-external-link"></i>
            </a>
        </div>
    </div>
</main>

<?php include __DIR__ . '/inc/footer.php'; ?>
