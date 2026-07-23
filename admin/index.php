<?php require_once('header.php'); ?>

<?php
function dashboardCount($pdo, $sql, $params = []) {
	try {
		if ($params) {
			$stmt = $pdo->prepare($sql);
			$stmt->execute($params);
			return (int) $stmt->fetchColumn();
		}
		return (int) $pdo->query($sql)->fetchColumn();
	} catch (Throwable $e) {
		return 0;
	}
}

function dashboardRows($pdo, $sql, $params = [], $limit = 8) {
	try {
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	} catch (Throwable $e) {
		return [];
	}
}

$hour = (int) date('G');
if ($hour < 12) {
	$greeting = 'Good morning';
} elseif ($hour < 17) {
	$greeting = 'Good afternoon';
} else {
	$greeting = 'Good evening';
}

$totalTeachers = dashboardCount($pdo, "SELECT COUNT(*) FROM tbl_staff WHERE status = 'Active'");
$totalFacilities = dashboardCount($pdo, "SELECT COUNT(*) FROM tbl_product WHERE p_is_active = 1");
$totalNews = dashboardCount($pdo, "SELECT COUNT(*) FROM tbl_post");
$upcomingEvents = dashboardCount($pdo, "
	SELECT COUNT(*) FROM tbl_calendar_event
	WHERE status = 'Active'
	  AND (event_date >= CURDATE() OR (end_date IS NOT NULL AND end_date >= CURDATE()))
");
$newAdmissions = dashboardCount($pdo, "SELECT COUNT(*) FROM tbl_admission WHERE status = 'New'");
$newCareerApps = dashboardCount($pdo, "SELECT COUNT(*) FROM tbl_career_application WHERE status = 'New'");
$faqs = dashboardCount($pdo, "SELECT COUNT(*) FROM tbl_faq");
$testimonials = dashboardCount($pdo, "SELECT COUNT(*) FROM tbl_testimonial WHERE status = 'Active'");

$recentAdmissions = dashboardRows($pdo, "
	SELECT id, student_name, class_applied, phone, status, created_at
	FROM tbl_admission
	ORDER BY id DESC
	LIMIT 8
");
$recentEvents = dashboardRows($pdo, "
	SELECT id, title, event_date, end_date, event_time, location, status
	FROM tbl_calendar_event
	WHERE status = 'Active'
	ORDER BY event_date ASC, id ASC
	LIMIT 8
");
$recentApplications = dashboardRows($pdo, "
	SELECT a.id, a.full_name, a.email, a.status, a.created_at, v.title AS vacancy_title
	FROM tbl_career_application a
	LEFT JOIN tbl_vacancy v ON v.id = a.vacancy_id
	ORDER BY a.id DESC
	LIMIT 8
");
$recentNews = dashboardRows($pdo, "
	SELECT post_id, post_title, post_date
	FROM tbl_post
	ORDER BY post_id DESC
	LIMIT 6
");
?>

<style>
.school-dash { padding: 0 15px 25px; max-width: 1400px; }
.content-header { margin-bottom: 10px !important; padding-bottom: 0 !important; }
.school-dash-hero {
	background: linear-gradient(135deg, #0a4a9c 0%, #062a62 100%);
	color: #fff;
	border-radius: 12px;
	padding: 18px 22px;
	margin-bottom: 16px;
	display: flex;
	flex-wrap: wrap;
	justify-content: space-between;
	align-items: center;
	gap: 14px;
	box-shadow: 0 10px 28px rgba(6, 42, 98, .22);
}
.school-dash-hero h2 { margin: 0 0 4px; font-size: 26px; font-weight: 600; }
.school-dash-hero p { margin: 0; opacity: .9; font-size: 14px; }
.school-dash-actions { display: flex; flex-wrap: wrap; gap: 8px; }
.school-dash-actions a {
	color: #fff;
	background: rgba(255,255,255,.14);
	border: 1px solid rgba(255,255,255,.22);
	padding: 8px 14px;
	border-radius: 999px;
	font-size: 13px;
	text-decoration: none;
}
.school-dash-actions a:hover { background: rgba(255,255,255,.26); color: #fff; }
.school-stats {
	display: grid;
	grid-template-columns: repeat(6, minmax(0, 1fr));
	gap: 12px;
	margin-bottom: 18px;
}
.school-stat {
	background: #fff;
	border-radius: 12px;
	padding: 14px;
	box-shadow: 0 2px 12px rgba(0,0,0,.05);
	border-left: 4px solid #0a4a9c;
	position: relative;
	min-height: 78px;
}
.school-stat a { color: inherit; text-decoration: none; display: block; }
.school-stat .n { font-size: 28px; font-weight: 700; color: #062a62; line-height: 1.1; }
.school-stat .l { margin-top: 4px; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; color: #7888a3; }
.school-stat .i { position: absolute; right: 14px; top: 14px; font-size: 22px; color: #0a4a9c; opacity: .2; }
.school-stat.is-amber { border-left-color: #d97706; }
.school-stat.is-green { border-left-color: #159947; }
.school-stat.is-teal { border-left-color: #0f766e; }
.school-stat.is-rose { border-left-color: #be123c; }
.school-panels {
	display: grid;
	grid-template-columns: repeat(2, minmax(0, 1fr));
	gap: 16px;
	margin-bottom: 16px;
}
.school-panel {
	background: #fff;
	border-radius: 12px;
	box-shadow: 0 2px 12px rgba(0,0,0,.05);
	padding: 16px 18px;
}
.school-panel-head {
	display: flex;
	justify-content: space-between;
	align-items: center;
	gap: 10px;
	margin-bottom: 12px;
}
.school-panel-head h3 { margin: 0; font-size: 16px; font-weight: 700; color: #062a62; }
.school-panel-head a { font-size: 12px; font-weight: 600; }
.school-panel table { width: 100%; font-size: 13px; }
.school-panel th {
	text-align: left;
	padding: 8px 4px;
	border-bottom: 2px solid #eef2f7;
	color: #6b7c93;
	font-size: 11px;
	text-transform: uppercase;
}
.school-panel td {
	padding: 9px 4px;
	border-bottom: 1px solid #f4f7fb;
	vertical-align: top;
	color: #243553;
}
.school-panel tr:last-child td { border-bottom: 0; }
.school-empty { color: #8a9bb3; font-size: 13px; margin: 8px 0 0; }
.badge-soft {
	display: inline-block;
	padding: 2px 9px;
	border-radius: 999px;
	font-size: 11px;
	font-weight: 700;
	background: #e8eef8;
	color: #0a4a9c;
}
.badge-soft.new { background: #fff4e5; color: #b45309; }
.badge-soft.active { background: #e8f8ef; color: #15803d; }
.school-quick {
	background: #fff;
	border-radius: 12px;
	box-shadow: 0 2px 12px rgba(0,0,0,.05);
	padding: 16px 18px;
}
.school-quick h3 { margin: 0 0 12px; font-size: 16px; font-weight: 700; color: #062a62; }
.school-quick-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
	gap: 10px;
}
.school-quick-grid a {
	display: flex;
	align-items: center;
	gap: 8px;
	padding: 12px 12px;
	border: 1px solid #e8eef8;
	border-radius: 10px;
	text-decoration: none;
	color: #062a62;
	font-weight: 600;
	font-size: 13px;
	background: #f8fafc;
}
.school-quick-grid a:hover {
	background: #eef4ff;
	border-color: #c9d8f0;
	color: #0a4a9c;
}
.school-quick-grid i { width: 18px; text-align: center; color: #0a4a9c; }
@media (max-width: 1199px) {
	.school-stats { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 991px) {
	.school-panels { grid-template-columns: 1fr; }
}
@media (max-width: 575px) {
	.school-dash-hero { padding: 16px; }
	.school-stats { grid-template-columns: 1fr 1fr; }
}
</style>

<section class="content-header">
	<h1>Dashboard</h1>
</section>

<div class="school-dash">
	<div class="school-dash-hero">
		<div>
			<h2><?php echo htmlspecialchars($greeting); ?>, Admin</h2>
			<p>Techgatha School overview · <?php echo date('l, F j, Y'); ?></p>
		</div>
		<div class="school-dash-actions">
			<a href="admission-list.php"><i class="fa fa-file-text-o"></i> Admissions<?php echo $newAdmissions ? ' (' . $newAdmissions . ')' : ''; ?></a>
			<a href="calendar-event-add.php"><i class="fa fa-calendar-plus-o"></i> Add Event</a>
			<a href="staff-add.php"><i class="fa fa-user-plus"></i> Add Teacher</a>
			<a href="blog-add.php"><i class="fa fa-newspaper-o"></i> Add News</a>
		</div>
	</div>

	<div class="school-stats">
		<div class="school-stat is-amber">
			<a href="admission-list.php">
				<div class="n"><?php echo $newAdmissions; ?></div>
				<div class="l">New Admissions</div>
				<div class="i"><i class="fa fa-file-text-o"></i></div>
			</a>
		</div>
		<div class="school-stat">
			<a href="staff.php">
				<div class="n"><?php echo $totalTeachers; ?></div>
				<div class="l">Teachers</div>
				<div class="i"><i class="fa fa-id-badge"></i></div>
			</a>
		</div>
		<div class="school-stat">
			<a href="calendar-event.php">
				<div class="n"><?php echo $upcomingEvents; ?></div>
				<div class="l">Upcoming Events</div>
				<div class="i"><i class="fa fa-calendar"></i></div>
			</a>
		</div>
		<div class="school-stat is-rose">
			<a href="career-application.php">
				<div class="n"><?php echo $newCareerApps; ?></div>
				<div class="l">New Applications</div>
				<div class="i"><i class="fa fa-briefcase"></i></div>
			</a>
		</div>
		<div class="school-stat is-green">
			<a href="product.php">
				<div class="n"><?php echo $totalFacilities; ?></div>
				<div class="l">Facilities</div>
				<div class="i"><i class="fa fa-building"></i></div>
			</a>
		</div>
		<div class="school-stat is-teal">
			<a href="blog.php">
				<div class="n"><?php echo $totalNews; ?></div>
				<div class="l">News</div>
				<div class="i"><i class="fa fa-newspaper-o"></i></div>
			</a>
		</div>
	</div>

	<div class="school-panels">
		<div class="school-panel">
			<div class="school-panel-head">
				<h3>Recent Admissions</h3>
				<a href="admission-list.php">View all</a>
			</div>
			<?php if (!$recentAdmissions) { ?>
				<p class="school-empty">No admission applications yet.</p>
			<?php } else { ?>
				<table>
					<thead>
						<tr>
							<th>Student</th>
							<th>Class</th>
							<th>Status</th>
							<th>Date</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($recentAdmissions as $row) { ?>
							<tr>
								<td>
									<a href="admission-view.php?id=<?php echo (int) $row['id']; ?>">
										<?php echo htmlspecialchars($row['student_name']); ?>
									</a>
									<?php if (!empty($row['phone'])) { ?>
										<br><small class="text-muted"><?php echo htmlspecialchars($row['phone']); ?></small>
									<?php } ?>
								</td>
								<td><?php echo htmlspecialchars($row['class_applied'] ?: '—'); ?></td>
								<td><span class="badge-soft <?php echo strtolower($row['status']) === 'new' ? 'new' : ''; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
								<td><?php echo !empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : '—'; ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } ?>
		</div>

		<div class="school-panel">
			<div class="school-panel-head">
				<h3>School Calendar</h3>
				<a href="calendar-event.php">Manage</a>
			</div>
			<?php if (!$recentEvents) { ?>
				<p class="school-empty">No calendar events published yet.</p>
			<?php } else { ?>
				<table>
					<thead>
						<tr>
							<th>Event</th>
							<th>Date</th>
							<th>Location</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($recentEvents as $row) {
							$dateLabel = $row['event_date'];
							if (!empty($row['end_date']) && $row['end_date'] !== $row['event_date']) {
								$dateLabel .= ' – ' . $row['end_date'];
							}
							?>
							<tr>
								<td>
									<a href="calendar-event-edit.php?id=<?php echo (int) $row['id']; ?>">
										<?php echo htmlspecialchars($row['title']); ?>
									</a>
									<?php if (!empty($row['event_time'])) { ?>
										<br><small class="text-muted"><?php echo htmlspecialchars($row['event_time']); ?></small>
									<?php } ?>
								</td>
								<td><?php echo htmlspecialchars($dateLabel); ?></td>
								<td><?php echo htmlspecialchars($row['location'] ?: '—'); ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } ?>
		</div>

		<div class="school-panel">
			<div class="school-panel-head">
				<h3>Career Applications</h3>
				<a href="career-application.php">View all</a>
			</div>
			<?php if (!$recentApplications) { ?>
				<p class="school-empty">No career applications yet.</p>
			<?php } else { ?>
				<table>
					<thead>
						<tr>
							<th>Applicant</th>
							<th>Vacancy</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($recentApplications as $row) { ?>
							<tr>
								<td>
									<a href="career-application-view.php?id=<?php echo (int) $row['id']; ?>">
										<?php echo htmlspecialchars($row['full_name']); ?>
									</a>
									<br><small class="text-muted"><?php echo htmlspecialchars($row['email']); ?></small>
								</td>
								<td><?php echo htmlspecialchars($row['vacancy_title'] ?: '—'); ?></td>
								<td><span class="badge-soft <?php echo strtolower((string) $row['status']) === 'new' ? 'new' : ''; ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } ?>
		</div>

		<div class="school-panel">
			<div class="school-panel-head">
				<h3>Latest News</h3>
				<a href="blog.php">Manage</a>
			</div>
			<?php if (!$recentNews) { ?>
				<p class="school-empty">No news posts yet.</p>
			<?php } else { ?>
				<table>
					<thead>
						<tr>
							<th>Title</th>
							<th>Date</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($recentNews as $row) { ?>
							<tr>
								<td>
									<a href="blog-edit.php?id=<?php echo (int) $row['post_id']; ?>">
										<?php echo htmlspecialchars($row['post_title']); ?>
									</a>
								</td>
								<td><?php echo htmlspecialchars($row['post_date'] ?: '—'); ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } ?>
		</div>
	</div>

	<div class="school-quick">
		<h3>Quick Links</h3>
		<div class="school-quick-grid">
			<a href="leadership.php"><i class="fa fa-university"></i> Leadership</a>
			<a href="staff.php"><i class="fa fa-id-badge"></i> Teachers</a>
			<a href="teacher-level.php"><i class="fa fa-sitemap"></i> Levels</a>
			<a href="product.php"><i class="fa fa-building"></i> Facilities</a>
			<a href="calendar-event.php"><i class="fa fa-calendar"></i> Calendar</a>
			<a href="gallery.php"><i class="fa fa-camera"></i> Gallery</a>
			<a href="slider.php"><i class="fa fa-picture-o"></i> Sliders</a>
			<a href="faq.php"><i class="fa fa-question-circle"></i> FAQ (<?php echo $faqs; ?>)</a>
			<a href="testimonial.php"><i class="fa fa-star"></i> Testimonials (<?php echo $testimonials; ?>)</a>
			<a href="vacancy.php"><i class="fa fa-briefcase"></i> Vacancies</a>
			<a href="settings.php"><i class="fa fa-sliders"></i> Settings</a>
			<a href="page.php"><i class="fa fa-file-o"></i> Page Settings</a>
		</div>
	</div>
</div>

<?php require_once('footer.php'); ?>
