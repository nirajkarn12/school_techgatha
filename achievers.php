<?php
require_once __DIR__ . '/inc/functions.php';

$pageTitle = loadLang('high_achievers');
$metaDescription = loadLang('high_achievers_meta');
include __DIR__ . '/inc/header.php';

$homeAchievers = getActiveAchievers();
$hideAchieversSeeMore = true;
$achieversTotal = count($homeAchievers);

if ($homeAchievers) {
    include __DIR__ . '/inc/partials/achievers-section.php';
} else {
    echo '<div class="container page-wrap pt-5"><div class="alert alert-light rounded-4">' . t('no_achievers_yet') . '</div></div>';
}

include __DIR__ . '/inc/footer.php';
