<?php
require_once __DIR__ . '/inc/functions.php';

$events = getCalendarEvents(false);
$pageTitle = loadLang('school_calendar');
$metaDescription = seoCleanText(loadLang('school_calendar_subtitle'), 160);

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
}, $events);

include __DIR__ . '/inc/header.php';
$breadcrumbs = [
    ['label' => t('home'), 'url' => BASE_URL],
    ['label' => t('categories'), 'url' => ''],
    ['label' => t('school_calendar'), 'url' => ''],
];
echo renderBreadcrumbs($breadcrumbs);
?>
<div class="section-head mb-4">
  <div class="section-kicker"><?php echo t('categories'); ?></div>
  <h1 class="section-title mb-2"><?php echo t('school_calendar'); ?></h1>
  <p class="text-muted mb-0"><?php echo t('school_calendar_subtitle'); ?></p>
</div>

<div class="row g-4 school-calendar-layout">
  <div class="col-lg-7">
    <div
      id="schoolNepaliCalendar"
      class="school-nepali-calendar"
      data-lang="<?php echo e(getCurrentLang()); ?>"
    ></div>
  </div>
  <div class="col-lg-5">
    <div class="school-cal-side">
      <div class="section-head section-head--tight mb-3">
        <div>
          <div class="section-kicker"><?php echo t('upcoming_events'); ?></div>
          <h2 class="section-title mb-0 h4"><?php echo t('all_school_events'); ?></h2>
        </div>
      </div>
      <?php if (!$events) { ?>
        <div class="alert alert-light border rounded-4 mb-0"><?php echo t('no_calendar_events'); ?></div>
      <?php } else { ?>
        <div class="school-cal-event-list">
          <?php foreach ($events as $event) {
              $start = $event['event_date'] ?? '';
              $end = $event['end_date'] ?? '';
              $dateLabel = $start;
              if ($end && $end !== $start) {
                  $dateLabel .= ' – ' . $end;
              }
              ?>
            <article class="school-cal-side-item" data-event-start="<?php echo e($start); ?>">
              <div class="school-cal-side-date"><?php echo e($dateLabel); ?><?php echo !empty($event['event_time']) ? ' · ' . e($event['event_time']) : ''; ?></div>
              <h3 class="h6 fw-bold mb-1"><?php echo e($event['title']); ?></h3>
              <?php if (!empty($event['location'])) { ?>
                <p class="small text-muted mb-1"><i class="fa fa-map-marker me-1"></i><?php echo e($event['location']); ?></p>
              <?php } ?>
              <?php if (!empty($event['description'])) { ?>
                <p class="small mb-0"><?php echo nl2br(e($event['description'])); ?></p>
              <?php } ?>
            </article>
          <?php } ?>
        </div>
      <?php } ?>
    </div>
  </div>
</div>

<script src="https://unpkg.com/nepali-date-picker-converter@0.1.32/dist/bundle.umd.js"></script>
<script src="<?php echo ASSET_URL; ?>js/school-nepali-calendar.js?v=20260723b"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var root = document.getElementById('schoolNepaliCalendar');
  if (!root || typeof SchoolNepaliCalendar === 'undefined') return;
  if (!window.NepaliDatePickerConverter || !window.NepaliDatePickerConverter.adToBs) {
    root.innerHTML = '<div class="alert alert-warning"><?php echo e(loadLang('calendar_loader_error')); ?></div>';
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

<?php include __DIR__ . '/inc/footer.php'; ?>
