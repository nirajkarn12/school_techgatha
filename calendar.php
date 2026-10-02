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
        'event_color' => (string) ($event['event_color'] ?? '#e5262f'),
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

<div
  id="schoolNepaliCalendar"
  class="school-nepali-calendar school-nepali-calendar-year"
  data-lang="<?php echo e(getCurrentLang()); ?>"
></div>
</div>

<script src="https://unpkg.com/nepali-date-picker-converter@0.1.32/dist/bundle.umd.js"></script>
<script src="<?php echo ASSET_URL; ?>js/school-nepali-calendar.js?v=20260930h"></script>
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
    monthsToShow: 12,
    showAllMonths: true,
    showToolbar: false,
    showDayDetails: false,
    labels: {
      bs_label: <?php echo json_encode(loadLang('calendar_bs_label')); ?>,
      school_event: <?php echo json_encode(loadLang('calendar_school_event')); ?>,
      today: <?php echo json_encode(loadLang('calendar_today')); ?>,
      no_events_day: <?php echo json_encode(loadLang('calendar_no_events_day')); ?>,
      month_events_title: <?php echo json_encode(loadLang('calendar_month_events_title')); ?>,
      year_events_title: <?php echo json_encode(loadLang('calendar_year_events_title')); ?>,
      no_month_events: <?php echo json_encode(loadLang('no_calendar_events')); ?>
    }
  });
});
</script>

<?php include __DIR__ . '/inc/footer.php'; ?>
