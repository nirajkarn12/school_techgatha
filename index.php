<?php
require_once __DIR__ . '/inc/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['whatsapp_action'] ?? '') === 'send_message') {
  header('Content-Type: application/json; charset=utf-8');
  if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
    http_response_code(419);
    echo json_encode(['ok' => false, 'error' => 'Your session expired. Refresh the page and try again.']);
    exit;
  }

  $result = sendWhatsAppCloudMessage($_POST['recipient'] ?? '', $_POST['message'] ?? '');
  if (!$result['ok']) {
    http_response_code(400);
  }
  echo json_encode($result);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['contact_form'])) {
    handleContactFormSubmission(BASE_URL . 'index.php');
}

$homeSeo = getHomeSeo();
$pageTitle = $homeSeo['title'];
$metaDescription = $homeSeo['description'];
$metaKeywords = $homeSeo['keywords'];
$fullWidth = true;
$showWaterSplash = false;
include __DIR__ . '/inc/header.php';

$posts = $pdo->query('SELECT post_id, post_title, post_content, photo FROM tbl_post ORDER BY post_id DESC LIMIT 3')->fetchAll();
$faqs = $pdo->query('SELECT faq_id, faq_title, faq_content FROM tbl_faq ORDER BY faq_id ASC LIMIT 5')->fetchAll();
$settings = $pdo->query('SELECT * FROM tbl_settings LIMIT 1')->fetch();
$heroSlides = $pdo->query('SELECT * FROM tbl_slider ORDER BY id ASC')->fetchAll();
$brandName = e(getSiteSetting('site_name', SITE_NAME));
$heroFallback = ASSET_URL . 'images/cleaning-hero.jpg';
$phone = e(getSiteSetting('contact_phone', '+977 9869224134'));
$homeServices = [];
try {
    $homeServices = $pdo->query("
        SELECT p_id, p_name, p_featured_photo, p_short_description
        FROM tbl_product
        WHERE p_is_active = 1
        ORDER BY p_is_featured DESC, p_id DESC
        LIMIT 12
    ")->fetchAll();
} catch (Throwable $e) {
    $homeServices = [];
}
if (!$homeServices) {
    try {
        $homeServices = $pdo->query("
            SELECT id AS p_id, title AS p_name, photo AS p_featured_photo, content AS p_short_description
            FROM tbl_service
            ORDER BY id DESC
            LIMIT 12
        ")->fetchAll();
    } catch (Throwable $e) {
        $homeServices = [];
    }
}
$homeGallery = [];
try {
    $homeGallery = $pdo->query("
        SELECT g.id, g.title, g.content, g.photo
        FROM tbl_gallery g
        WHERE g.status = 'Active'
        ORDER BY g.sort_order ASC, g.id DESC
        LIMIT 12
    ")->fetchAll();
} catch (Throwable $e) {
    $homeGallery = [];
}
$aboutPage = $pdo->query('SELECT about_title, about_content, about_banner FROM tbl_page LIMIT 1')->fetch(PDO::FETCH_ASSOC) ?: [];
$schoolMessages = [];
try {
  $schoolMessages = $pdo->query("SELECT role, person_name, designation, photo, message FROM tbl_school_message WHERE role = 'principal' AND status = 'Active' AND TRIM(person_name) <> '' LIMIT 1")->fetchAll();
} catch (Throwable $e) {
  $schoolMessages = [];
}
$allCalendarEvents = getCalendarEvents(false);
$calendarEventsJson = array_map(static function ($event) {
    return [
        'id' => (int) ($event['id'] ?? 0),
        'title' => (string) ($event['title'] ?? ''),
        'description' => (string) ($event['description'] ?? ''),
        'event_date' => (string) ($event['event_date'] ?? ''),
        'end_date' => (string) ($event['end_date'] ?? ''),
        'event_time' => (string) ($event['event_time'] ?? ''),
        'location' => (string) ($event['location'] ?? ''),
    ];
}, $allCalendarEvents);
$facebookPageUrl = getFacebookPageUrl();
?>
<div class="hero-notice-stack">
<section class="hero-banner">
  <div class="swiper heroSwiper">
    <div class="swiper-wrapper">
      <?php if ($heroSlides) { foreach ($heroSlides as $i => $slide) {
        $slideImg = !empty($slide['photo']) ? getProductImage($slide['photo']) : $heroFallback;
        $heading = !empty($slide['heading']) ? $slide['heading'] : loadLang('hero_default_title');
      ?>
      <div class="swiper-slide">
        <div class="hero-slide">
          <img class="hero-slide-bg" src="<?php echo e($slideImg); ?>" alt="<?php echo e($heading); ?>" <?php echo $i === 0 ? 'loading="eager"' : 'loading="lazy"'; ?>>
          <div class="hero-slide-shade"></div>
          <div class="hero-slide-content">
            <div class="hero-copy">
              <div class="hero-kicker"><span><?php echo $brandName; ?></span></div>
              <?php echo renderHeroHeadline($heading); ?>
            </div>
          </div>
        </div>
      </div>
      <?php } } else { ?>
      <div class="swiper-slide">
        <div class="hero-slide">
          <img class="hero-slide-bg" src="<?php echo e($heroFallback); ?>" alt="<?php echo $brandName; ?>" loading="eager">
          <div class="hero-slide-shade"></div>
          <div class="hero-slide-content">
            <div class="hero-copy">
              <div class="hero-kicker"><span><?php echo $brandName; ?></span></div>
              <?php echo renderHeroHeadline(loadLang('hero_default_title')); ?>
            </div>
          </div>
        </div>
      </div>
      <?php } ?>
    </div>
    <div class="swiper-button-prev hero-nav" aria-label="<?php echo t('previous'); ?>"></div>
    <div class="swiper-button-next hero-nav" aria-label="<?php echo t('next'); ?>"></div>
  </div>
</section>
<?php include __DIR__ . '/inc/partials/marquee-ribbon.php'; ?>
</div>

<section class="trust-strip">
  <div class="container">
    <div class="trust-grid">
      <div class="trust-item"><i class="fa fa-graduation-cap"></i><span><?php echo t('trust_vetted'); ?></span></div>
      <div class="trust-item"><i class="fa fa-chalkboard-user"></i><span><?php echo t('trust_ontime'); ?></span></div>
      <div class="trust-item"><i class="fa fa-book-open"></i><span><?php echo t('trust_home_office'); ?></span></div>
      <div class="trust-item"><i class="fa fa-phone"></i><a href="tel:<?php echo preg_replace('/\s+/', '', $phone); ?>"><?php echo $phone; ?></a></div>
    </div>
  </div>
</section>

<div class="container page-wrap pt-5">

<?php
$aboutCompact = true;
include __DIR__ . '/inc/partials/about-section.php';
?>

<?php if ($schoolMessages) { ?>
<?php $principal = $schoolMessages[0]; ?>
<section class="site-ribbon site-ribbon-b principal-divider-ribbon reveal">
  <div class="site-ribbon-inner principal-story-inner">
    <figure class="principal-ribbon-media">
      <img src="<?php echo e(!empty($principal['photo']) ? getProductImage($principal['photo']) : ASSET_URL . 'images/placeholder.svg'); ?>" alt="<?php echo e($principal['person_name']); ?>" loading="lazy">
    </figure>
    <div class="site-ribbon-copy principal-ribbon-copy">
      <div class="section-kicker principal-ribbon-kicker"><?php echo t('leadership'); ?></div>
      <h2 class="site-ribbon-title principal-ribbon-title"><?php echo e($principal['person_name']); ?></h2>
      <p class="site-ribbon-text principal-ribbon-designation"><?php echo e($principal['designation'] ?: loadLang('principal_role_label')); ?></p>
      <?php if (trim((string) ($principal['message'] ?? '')) !== '') { ?>
      <div class="principal-ribbon-message">
        <?php echo renderRichHtml($principal['message']); ?>
      </div>
      <?php } ?>
      <a href="<?php echo BASE_URL; ?>leadership.php?role=principal" class="btn btn-light principal-ribbon-link"><?php echo t('read_more'); ?></a>
    </div>
  </div>
</section>
<?php } ?>

</div>
<div class="container page-wrap">

<?php if ($homeServices) { ?>
<section class="section-block section-block--before-ribbon home-services-showcase reveal">
  <div class="home-gallery-head">
    <h2 class="home-gallery-title"><?php echo t('our_facilities'); ?></h2>
    <div class="home-gallery-nav">
      <button type="button" class="home-gallery-btn home-services-prev" aria-label="<?php echo t('previous'); ?>"><i class="fa fa-arrow-left"></i></button>
      <button type="button" class="home-gallery-btn home-gallery-next home-services-next" aria-label="<?php echo t('next'); ?>"><i class="fa fa-arrow-right"></i></button>
    </div>
  </div>
  <div class="swiper homeServicesSwiper">
    <div class="swiper-wrapper">
      <?php foreach ($homeServices as $svc) {
        $svcTitle = trim((string) ($svc['p_name'] ?? ''));
        if ($svcTitle === '') {
            $svcTitle = loadLang('our_facilities');
        }
        $svcImg = getProductImage($svc['p_featured_photo'] ?? '');
        $svcId = (int) ($svc['p_id'] ?? 0);
      ?>
      <div class="swiper-slide">
        <a
          href="<?php echo $svcId > 0 ? (BASE_URL . 'product.php?id=' . $svcId) : e($svcImg); ?>"
          class="home-gallery-card"
          <?php if ($svcId <= 0) { ?>
          data-fancybox="home-facilities"
          data-caption="<?php echo e($svcTitle); ?>"
          <?php } ?>
        >
          <img src="<?php echo e($svcImg); ?>" alt="<?php echo e($svcTitle); ?>" loading="lazy">
          <span class="home-gallery-shade"></span>
          <span class="home-gallery-meta">
            <span class="home-gallery-label"><?php echo e($svcTitle); ?></span>
            <span class="home-gallery-go" aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
          </span>
        </a>
      </div>
      <?php } ?>
    </div>
    <div class="swiper-pagination home-services-dots"></div>
  </div>
  <div class="home-gallery-footer">
    <a href="<?php echo BASE_URL; ?>products.php" class="look-at-us-link"><?php echo t('view_all_facilities'); ?> <i class="fa fa-arrow-right"></i></a>
  </div>
</section>
<?php } ?>

<?php include __DIR__ . '/inc/partials/why-choose-section.php'; ?>

</div>

<div class="container page-wrap">

<?php if ($homeGallery) { ?>
<section class="section-block home-gallery-showcase reveal">
  <div class="home-gallery-head">
    <h2 class="home-gallery-title"><?php echo t('home_gallery_title'); ?></h2>
    <div class="home-gallery-nav">
      <button type="button" class="home-gallery-btn home-gallery-prev" aria-label="<?php echo t('previous'); ?>"><i class="fa fa-arrow-left"></i></button>
      <button type="button" class="home-gallery-btn home-gallery-next" aria-label="<?php echo t('next'); ?>"><i class="fa fa-arrow-right"></i></button>
    </div>
  </div>
  <div class="swiper homeGallerySwiper">
    <div class="swiper-wrapper">
      <?php foreach ($homeGallery as $gItem) {
        $gImg = getProductImage($gItem['photo']);
        $gTitle = trim((string)($gItem['title'] ?? ''));
        if ($gTitle === '') {
            $gTitle = loadLang('gallery');
        }
        $gCaption = trim((string)($gItem['content'] ?? ''));
      ?>
      <div class="swiper-slide">
        <a
          href="<?php echo e($gImg); ?>"
          class="home-gallery-card"
          data-fancybox="home-gallery"
          data-caption="<?php echo e($gTitle . ($gCaption !== '' ? ' — ' . $gCaption : '')); ?>"
        >
          <img src="<?php echo e($gImg); ?>" alt="<?php echo e($gTitle); ?>" loading="lazy">
          <span class="home-gallery-shade"></span>
          <span class="home-gallery-meta">
            <span class="home-gallery-label"><?php echo e($gTitle); ?></span>
            <span class="home-gallery-go" aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
          </span>
        </a>
      </div>
      <?php } ?>
    </div>
    <div class="swiper-pagination home-gallery-dots"></div>
  </div>
  <div class="home-gallery-footer">
    <a href="<?php echo BASE_URL; ?>gallery.php" class="look-at-us-link"><?php echo t('view_full_gallery'); ?> <i class="fa fa-arrow-right"></i></a>
  </div>
</section>
<?php } ?>

</div>
<?php
$achieversLimit = 4;
include __DIR__ . '/inc/partials/achievers-section.php';
?>
<div class="container page-wrap">

<section class="section-block section-block--tight section-block--before-ribbon home-events-facebook reveal">
  <div class="home-events-facebook-grid">
    <div class="home-split-panel home-split-panel--calendar">
      <div class="section-head section-head--tight">
        <div>
          <div class="section-kicker"><?php echo t('school_calendar'); ?></div>
          <h2 class="section-title mb-0"><?php echo t('school_calendar'); ?></h2>
        </div>
        <a href="<?php echo BASE_URL; ?>calendar.php" class="btn btn-outline-dark btn-sm"><?php echo t('view_all_events'); ?></a>
      </div>
      <div
        id="homeNepaliCalendar"
        class="school-nepali-calendar"
        data-lang="<?php echo e(getCurrentLang()); ?>"
      ></div>
    </div>
    <div class="home-split-panel">
      <div class="section-head section-head--tight">
        <div>
          <div class="section-kicker"><?php echo t('follow_us'); ?></div>
          <h2 class="section-title mb-0"><?php echo t('facebook_page'); ?></h2>
        </div>
      </div>
      <div class="facebook-page-shell" id="facebookPageShell">
        <?php if ($facebookPageUrl !== '') { ?>
          <div id="fb-root"></div>
          <div
            class="fb-page"
            data-href="<?php echo e($facebookPageUrl); ?>"
            data-tabs="timeline"
            data-width="340"
            data-height="520"
            data-small-header="true"
            data-adapt-container-width="true"
            data-hide-cover="false"
            data-show-facepile="true"
          ></div>
          <script>
          (function () {
            var shell = document.getElementById('facebookPageShell');
            var page = shell ? shell.querySelector('.fb-page') : null;
            if (shell && page) {
              var w = Math.max(280, Math.floor(shell.clientWidth || 340));
              page.setAttribute('data-width', String(w));
            }
          })();
          </script>
          <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v18.0"></script>
        <?php } else { ?>
          <div class="alert alert-light border rounded-4 mb-0"><?php echo t('facebook_not_configured'); ?></div>
        <?php } ?>
      </div>
    </div>
  </div>
</section>

<script src="https://unpkg.com/nepali-date-picker-converter@0.1.32/dist/bundle.umd.js"></script>
<script src="<?php echo ASSET_URL; ?>js/school-nepali-calendar.js?v=20260723b"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var root = document.getElementById('homeNepaliCalendar');
  if (!root || typeof SchoolNepaliCalendar === 'undefined') return;
  if (!window.NepaliDatePickerConverter || !window.NepaliDatePickerConverter.adToBs) {
    root.innerHTML = '<div class="alert alert-warning mb-0"><?php echo e(loadLang('calendar_loader_error')); ?></div>';
    return;
  }
  new SchoolNepaliCalendar(root, {
    events: <?php echo json_encode($calendarEventsJson, JSON_UNESCAPED_UNICODE); ?>,
    lang: root.getAttribute('data-lang') || 'en',
    labels: {
      bs_label: <?php echo json_encode(loadLang('calendar_bs_label')); ?>,
      school_event: <?php echo json_encode(loadLang('calendar_school_event')); ?>,
      today: <?php echo json_encode(loadLang('calendar_today')); ?>,
      no_events_day: <?php echo json_encode(loadLang('calendar_no_events_day')); ?>
    }
  });
});
</script>

</div>

<section class="site-ribbon site-ribbon-c reveal">
  <div class="site-ribbon-inner">
    <div class="site-ribbon-copy">
      <div class="site-ribbon-kicker"><?php echo t('ribbon_news_kicker'); ?></div>
      <h2 class="site-ribbon-title"><?php echo t('ribbon_news_title'); ?></h2>
      <p class="site-ribbon-text"><?php echo t('ribbon_news_text'); ?></p>
    </div>
    <a href="<?php echo BASE_URL; ?>blog.php" class="btn btn-light btn-lg"><?php echo t('ribbon_news_cta'); ?></a>
  </div>
</section>

<div class="container page-wrap">

<?php if ($posts) { ?>
<section class="section-block home-tips-showcase reveal">
  <div class="section-head">
    <div>
      <div class="section-kicker"><?php echo t('blog'); ?></div>
      <h2 class="section-title"><?php echo t('from_the_blog'); ?></h2>
    </div>
    <a href="<?php echo BASE_URL; ?>blog.php" class="btn btn-outline-dark"><?php echo t('read_more'); ?></a>
  </div>
  <div class="row g-4">
    <?php foreach ($posts as $post) { ?>
      <div class="col-md-6 col-lg-4">
        <article class="card-hover blog-card home-tip-card h-100">
          <div class="home-tip-media">
            <img src="<?php echo getProductImage($post['photo']); ?>" alt="<?php echo e($post['post_title']); ?>" loading="lazy">
          </div>
          <div class="home-tip-body">
            <h5><?php echo e($post['post_title']); ?></h5>
            <p class="text-muted"><?php echo excerpt(strip_tags($post['post_content']), 120); ?></p>
            <a class="btn btn-dark" href="blog.php?id=<?php echo (int)$post['post_id']; ?>"><?php echo t('read_more'); ?></a>
          </div>
        </article>
      </div>
    <?php } ?>
  </div>
</section>
<?php } ?>
<section class="site-ribbon site-ribbon-b reveal">
  <div class="site-ribbon-inner">
    <div class="site-ribbon-copy">
      <div class="site-ribbon-kicker"><?php echo t('ribbon_teams_kicker'); ?></div>
      <h2 class="site-ribbon-title"><?php echo t('ribbon_teams_title'); ?></h2>
      <p class="site-ribbon-text"><?php echo t('ribbon_teams_text'); ?></p>
    </div>
    <a href="<?php echo BASE_URL; ?>teachers.php" class="btn btn-light btn-lg"><?php echo t('ribbon_teams_cta'); ?></a>
  </div>
</section>

<?php include __DIR__ . '/inc/partials/brochure-section.php'; ?>

<section class="section-block">
  <div class="section-head">
    <div>
      <div class="section-kicker"><?php echo t('faqs'); ?></div>
      <h2 class="section-title"><?php echo t('faqs'); ?></h2>
    </div>
  </div>
  <div class="accordion faq-accordion" id="faqAccordion">
    <?php if ($faqs) { foreach ($faqs as $faq) { ?>
      <div class="accordion-item">
        <h2 class="accordion-header" id="faqHeading<?php echo $faq['faq_id']; ?>">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse<?php echo $faq['faq_id']; ?>">
            <span class="faq-question-icon"><i class="fa fa-question-circle"></i></span>
            <?php echo e($faq['faq_title']); ?>
          </button>
        </h2>
        <div id="faqCollapse<?php echo $faq['faq_id']; ?>" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
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

</div>

<?php if ((int)getSiteSetting('newsletter_on_off', 1) === 1) {
  $newsletterRibbonText = trim((string)getSiteSetting('newsletter_text', ''));
  if ($newsletterRibbonText === '') {
      $newsletterRibbonText = loadLang('newsletter_default_text');
  }
?>
<section class="site-ribbon site-ribbon-c reveal">
  <div class="site-ribbon-inner site-ribbon-inner--subscribe">
    <div class="site-ribbon-copy">
      <div class="site-ribbon-kicker"><?php echo t('ribbon_news_kicker'); ?></div>
      <h2 class="site-ribbon-title"><?php echo t('newsletter'); ?></h2>
      <p class="site-ribbon-text"><?php echo e($newsletterRibbonText); ?></p>
    </div>
    <form class="site-ribbon-subscribe js-newsletter-form" action="<?php echo BASE_URL; ?>subscribe.php" method="post" novalidate>
      <input type="hidden" name="csrf_token" value="<?php echo e(csrfToken()); ?>">
      <div class="site-ribbon-subscribe-row">
        <input type="email" name="email" class="form-control" placeholder="<?php echo t('your_email'); ?>" required autocomplete="email">
        <button type="submit" class="btn btn-light btn-lg"><?php echo t('subscribe'); ?></button>
      </div>
      <div class="site-ribbon-subscribe-msg js-newsletter-msg" hidden aria-live="polite"></div>
    </form>
  </div>
</section>
<?php } ?>

<div class="container page-wrap">
<?php include __DIR__ . '/inc/partials/contact-section.php'; ?>

<section class="section-block">
  <div class="section-head">
    <div>
      <div class="section-kicker"><?php echo t('visit_us'); ?></div>
      <h2 class="section-title"><?php echo t('visit_us'); ?></h2>
    </div>
  </div>
  <div class="map-shell">
    <?php echo !empty($settings['contact_map_iframe']) ? $settings['contact_map_iframe'] : '<iframe loading="lazy" title="' . e(loadLang('visit_us')) . '" src="https://www.google.com/maps?q=Kathmandu,Nepal&output=embed"></iframe>'; ?>
  </div>
</section>
</div>

<?php
$partnerClients = getActiveClients();
if ($partnerClients) {
    $logoItems = '';
    foreach ($partnerClients as $client) {
        $logoSrc = getProductImage($client['logo'] ?? '');
        $logoAlt = trim((string)($client['name'] ?? '')) !== '' ? $client['name'] : loadLang('our_clients');
        $logoImg = '<img src="' . e($logoSrc) . '" alt="' . e($logoAlt) . '" loading="lazy">';
        $website = trim((string)($client['website_url'] ?? ''));
        if ($website !== '') {
            $logoItems .= '<a class="client-logo-item" href="' . e($website) . '" target="_blank" rel="noopener noreferrer">' . $logoImg . '</a>';
        } else {
            $logoItems .= '<span class="client-logo-item">' . $logoImg . '</span>';
        }
    }
?>
<section class="clients-band reveal">
  <div class="clients-band-inner">
    <div class="clients-kicker"><?php echo t('preferred_by_professionals'); ?></div>
    <h2 class="clients-title"><?php echo t('our_clients'); ?></h2>
  </div>
  <div class="clients-marquee" aria-label="<?php echo t('our_clients'); ?>">
    <div class="clients-marquee-track">
      <?php echo $logoItems . $logoItems; ?>
    </div>
  </div>
</section>
<?php } ?>

<?php if (!empty($settings['banner_login'])): ?>
<div class="modal fade" id="welcomePopup" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 bg-transparent shadow-none">
      <button type="button" class="btn-close bg-white rounded-circle position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="<?php echo t('close'); ?>" style="z-index:999;"></button>
      <img src="<?php echo getProductImage($settings['banner_login']); ?>" class="img-fluid rounded-4 shadow" alt="<?php echo t('welcome_banner'); ?>">
    </div>
  </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
  if (!sessionStorage.getItem("welcomePopupShown")) {
    new bootstrap.Modal(document.getElementById('welcomePopup')).show();
    sessionStorage.setItem("welcomePopupShown", "true");
  }
});
</script>
<?php endif; ?>

<!-- WhatsApp Chat Widget -->
<style>
.whatsapp-chat-widget {
    position: fixed;
    right: 22px;
    bottom: 22px;
    z-index: 9999;
    font-family: inherit;
}
.whatsapp-chat-button {
    width: 58px;
    height: 58px;
    border: 0;
    border-radius: 50%;
    background: #25D366;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    cursor: pointer;
    box-shadow: 0 8px 25px rgba(0,0,0,.20);
    transition: transform .2s ease, box-shadow .2s ease;
}
.whatsapp-chat-button:hover {
    transform: scale(1.06);
    box-shadow: 0 10px 30px rgba(0,0,0,.25);
}
.whatsapp-chat-box {
    width: 320px;
    max-width: calc(100vw - 30px);
    margin-bottom: 12px;
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 12px 40px rgba(0,0,0,.20);
    display: none;
}
.whatsapp-chat-box.is-open {
    display: block;
    animation: whatsappChatIn .2s ease;
}
.whatsapp-chat-header {
    background: #075E54;
    color: #fff;
    padding: 15px 16px;
    display: flex;
    align-items: center;
    gap: 11px;
}
.whatsapp-chat-header-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #25D366;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}
.whatsapp-chat-header strong {
    display: block;
    font-size: 15px;
}
.whatsapp-chat-header small {
    display: block;
    margin-top: 2px;
    opacity: .85;
    font-size: 12px;
}
.whatsapp-chat-body {
    padding: 16px;
    background: #f7f7f7;
}
.whatsapp-chat-message {
    background: #fff;
    border-radius: 4px 14px 14px 14px;
    padding: 11px 13px;
    font-size: 14px;
    line-height: 1.5;
    color: #333;
    box-shadow: 0 1px 2px rgba(0,0,0,.08);
    margin-bottom: 12px;
}
.whatsapp-chat-input {
    width: 100%;
    min-height: 44px;
    resize: vertical;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 10px 12px;
    font: inherit;
    font-size: 14px;
    outline: none;
    background: #fff;
}
.whatsapp-chat-input:focus {
    border-color: #25D366;
}
.whatsapp-chat-phone {
  margin-bottom: 9px;
}
.whatsapp-chat-send {
    width: 100%;
    margin-top: 9px;
    border: 0;
    border-radius: 10px;
    background: #25D366;
    color: #fff;
    padding: 11px 14px;
    font-weight: 600;
    cursor: pointer;
}
.whatsapp-chat-send:hover {
    background: #20bd5a;
}
#whatsappChatStatus {
  min-height: 18px;
  margin-top: 8px;
  color: #555;
  font-size: 12px;
}
@keyframes whatsappChatIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
@media (max-width: 575px) {
    .whatsapp-chat-widget {
        right: 14px;
        bottom: 14px;
    }
    .whatsapp-chat-box {
        width: min(320px, calc(100vw - 28px));
    }
}
</style>

<div class="whatsapp-chat-widget" aria-label="WhatsApp chat">
    <div class="whatsapp-chat-box" id="whatsappChatBox">
        <div class="whatsapp-chat-header">
            <div class="whatsapp-chat-header-icon"><i class="fa-brands fa-whatsapp"></i></div>
            <div>
                <strong><?php echo $brandName; ?></strong>
                <small>Typically replies quickly</small>
            </div>
        </div>
        <div class="whatsapp-chat-body">
            <div class="whatsapp-chat-message">
                Hello! How can we help you today?
            </div>
            <textarea
                id="whatsappChatMessage"
                class="whatsapp-chat-input"
                rows="2"
                placeholder="Type your message..."
            ></textarea>
              <input
                type="tel"
                id="whatsappChatRecipient"
                class="whatsapp-chat-input whatsapp-chat-phone"
                placeholder="WhatsApp number with country code"
                autocomplete="tel"
              >
              <div id="whatsappChatStatus" role="status" aria-live="polite"></div>
            <button type="button" class="whatsapp-chat-send" id="whatsappChatSend">
                <i class="fa-brands fa-whatsapp"></i> Send Message
            </button>
        </div>
    </div>

    <button
        type="button"
        class="whatsapp-chat-button"
        id="whatsappChatButton"
        aria-label="Open WhatsApp chat"
        aria-expanded="false"
    >
        <i class="fa-brands fa-whatsapp"></i>
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var chatButton = document.getElementById('whatsappChatButton');
    var chatBox = document.getElementById('whatsappChatBox');
    var messageInput = document.getElementById('whatsappChatMessage');
    var recipientInput = document.getElementById('whatsappChatRecipient');
    var sendButton = document.getElementById('whatsappChatSend');
    var status = document.getElementById('whatsappChatStatus');

    if (!chatButton || !chatBox || !messageInput || !recipientInput || !sendButton || !status) return;

    chatButton.addEventListener('click', function () {
        var isOpen = chatBox.classList.toggle('is-open');
        chatButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

        if (isOpen) {
            setTimeout(function () {
                messageInput.focus();
            }, 100);
        }
    });

    function startWhatsAppChat() {
        var message = messageInput.value.trim();
      var recipient = recipientInput.value.trim();

      if (!message || !recipient) {
        status.textContent = 'Enter your WhatsApp number and message.';
            return;
        }

      sendButton.disabled = true;
      status.textContent = 'Sending...';

      fetch('<?php echo e(BASE_URL . 'index.php'); ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
        body: new URLSearchParams({
          whatsapp_action: 'send_message',
          csrf_token: '<?php echo e(csrfToken()); ?>',
          recipient: recipient,
          message: message
        })
      })
      .then(function(response) { return response.json(); })
      .then(function(result) {
        status.textContent = result.ok ? 'Message sent successfully.' : (result.error || 'Message could not be sent.');
        if (result.ok) messageInput.value = '';
      })
      .catch(function() {
        status.textContent = 'Unable to connect to the WhatsApp service.';
      })
      .finally(function() {
        sendButton.disabled = false;
      });
    }

    sendButton.addEventListener('click', startWhatsAppChat);

    messageInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            startWhatsAppChat();
        }
    });
});
</script>

<?php $hideWhatsAppFloatingLink = true; include __DIR__ . '/inc/footer.php'; ?>
