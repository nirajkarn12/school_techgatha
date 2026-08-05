<?php
ob_start();
session_start();
include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");
$csrf = new CSRF_Protect();
$error_message = '';
$success_message = '';
$error_message1 = '';
$success_message1 = '';

// Check if the user is logged in or not
if(!isset($_SESSION['user'])) {
	header('location: login.php');
	exit;
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Admin Panel</title>

	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<?php
	$adminFaviconFile = '';
	try {
		$adminFavRow = $pdo->query("SELECT favicon, logo FROM tbl_settings WHERE id=1 LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: array();
		$adminFaviconFile = trim((string) ($adminFavRow['favicon'] ?? ''));
		if ($adminFaviconFile === '') {
			$adminFaviconFile = trim((string) ($adminFavRow['logo'] ?? ''));
		}
	} catch (Exception $e) {
		$adminFaviconFile = '';
	}
	if ($adminFaviconFile !== '' && is_file('../assets/uploads/' . $adminFaviconFile)):
		$adminFaviconUrl = htmlspecialchars(adminUploadUrl($adminFaviconFile), ENT_QUOTES, 'UTF-8');
	?>
	<link rel="icon" href="<?php echo $adminFaviconUrl; ?>">
	<link rel="shortcut icon" href="<?php echo $adminFaviconUrl; ?>">
	<?php endif; ?>

	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/ionicons.min.css">
	<link rel="stylesheet" href="css/datepicker3.css">
	<link rel="stylesheet" href="css/all.css">
	<link rel="stylesheet" href="css/select2.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.css">
	<link rel="stylesheet" href="css/jquery.fancybox.css">
	<link rel="stylesheet" href="css/AdminLTE.min.css">
	<link rel="stylesheet" href="css/_all-skins.min.css">
	<link rel="stylesheet" href="css/on-off-switch.css"/>
	<link rel="stylesheet" href="css/summernote.css">
	<link rel="stylesheet" href="style.css">

</head>

<body class="hold-transition fixed skin-blue sidebar-mini">

	<div class="wrapper">

		<header class="main-header">

			<a href="index.php" class="logo">
				<span class="logo-lg"><strong>School Admin</strong></span>
			</a>

			<nav class="navbar navbar-static-top">
				
				<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
					<span class="sr-only">Toggle navigation</span>
				</a>

				<span style="float:left;line-height:50px;color:#fff;padding-left:15px;font-size:18px;">Admin Panel</span>
    <!-- Top Bar ... User Inforamtion .. Login/Log out Area -->
				<div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
						<li class="dropdown user user-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<img src="../assets/uploads/<?php echo $_SESSION['user']['photo']; ?>" class="user-image" alt="User Image">
								<span class="hidden-xs"><?php echo $_SESSION['user']['full_name']; ?></span>
							</a>
							<ul class="dropdown-menu">
								<li class="user-footer">
									<div>
										<a href="profile-edit.php" class="btn btn-default btn-flat">Edit Profile</a>
									</div>
									<div>
										<a href="logout.php" class="btn btn-default btn-flat">Log out</a>
									</div>
								</li>
							</ul>
						</li>
					</ul>
				</div>

			</nav>
		</header>

  		<?php $cur_page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); ?>
<!-- Side Bar to Manage Shop Activities -->
  		<aside class="main-sidebar">
    		<section class="sidebar">
      
      			<ul class="sidebar-menu">

			        <li class="treeview <?php if($cur_page == 'index.php') {echo 'active';} ?>">
			          <a href="index.php">
			            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
			          </a>
			        </li>

                    <li class="treeview <?php if(in_array($cur_page, ['settings.php', 'page.php', 'social-media.php', 'subscriber.php'], true)) {echo 'active';} ?>">
                        <a href="#">
                            <i class="fa fa-sliders"></i>
                            <span>Website Settings</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="settings.php"><i class="fa fa-circle-o"></i> General Settings</a></li>
                            <li><a href="page.php"><i class="fa fa-circle-o"></i> Page Settings</a></li>
                            <li><a href="social-media.php"><i class="fa fa-circle-o"></i> Social Media</a></li>
                            <li><a href="subscriber.php"><i class="fa fa-circle-o"></i> Subscribers</a></li>
                        </ul>
                    </li>

                    <li class="treeview <?php if(in_array($cur_page, ['slider.php', 'product.php', 'product-add.php', 'product-edit.php', 'client.php', 'client-add.php', 'client-edit.php', 'why-feature.php', 'why-feature-add.php', 'why-feature-edit.php', 'achiever.php', 'achiever-add.php', 'achiever-edit.php', 'brochure.php', 'brochure-add.php', 'brochure-edit.php', 'birthday-template.php', 'birthday-template-add.php', 'birthday-template-edit.php', 'birthday.php', 'birthday-add.php', 'birthday-edit.php', 'birthday-delete.php'], true)) {echo 'active';} ?>">
                        <a href="#">
                            <i class="fa fa-home"></i>
                            <span>Homepage</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="slider.php"><i class="fa fa-circle-o"></i> Sliders</a></li>
                            <li><a href="product.php"><i class="fa fa-circle-o"></i> Facilities</a></li>
                            <li><a href="why-feature.php"><i class="fa fa-circle-o"></i> Why Choose Features</a></li>
                            <li><a href="achiever.php"><i class="fa fa-circle-o"></i> High Achievers</a></li>
                            <li><a href="brochure.php"><i class="fa fa-circle-o"></i> Brochure & Prospectus</a></li>
                            <li><a href="client.php"><i class="fa fa-circle-o"></i> Partner Logos</a></li>
                            <li><a href="birthday-template.php"><i class="fa fa-circle-o"></i> Birthday Templates</a></li>
                            <li><a href="birthday.php"><i class="fa fa-circle-o"></i> Birthday Students</a></li>
                            <li><a href="election.php"><i class="fa fa-circle-o"></i> Student Election</a></li>
                        </ul>
                    </li>

                    <li class="treeview <?php if(in_array($cur_page, ['leadership.php', 'leadership-edit.php', 'calendar-event.php', 'calendar-event-add.php', 'calendar-event-edit.php', 'admission-list.php', 'admission-view.php'], true)) {echo 'active';} ?>">
                        <a href="#">
                            <i class="fa fa-university"></i>
                            <span>School</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="leadership.php"><i class="fa fa-circle-o"></i> Leadership Messages</a></li>
                            <li><a href="calendar-event.php"><i class="fa fa-circle-o"></i> School Calendar</a></li>
                            <li><a href="admission-list.php"><i class="fa fa-circle-o"></i> Admissions</a></li>
                        </ul>
                    </li>

                    <li class="treeview <?php if(in_array($cur_page, ['staff.php', 'staff-add.php', 'staff-edit.php', 'teacher-level.php', 'teacher-level-add.php', 'teacher-level-edit.php'], true)) {echo 'active';} ?>">
                        <a href="#">
                            <i class="fa fa-id-badge"></i>
                            <span>Teachers</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="staff.php"><i class="fa fa-circle-o"></i> All Teachers</a></li>
                            <li><a href="teacher-level.php"><i class="fa fa-circle-o"></i> Levels</a></li>
                        </ul>
                    </li>

                    <li class="treeview <?php if(in_array($cur_page, ['vacancy.php', 'vacancy-add.php', 'vacancy-edit.php', 'career-application.php', 'career-application-view.php'], true)) {echo 'active';} ?>">
                        <a href="#">
                            <i class="fa fa-briefcase"></i>
                            <span>Careers</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="vacancy.php"><i class="fa fa-circle-o"></i> Vacancies</a></li>
                            <li><a href="career-application.php"><i class="fa fa-circle-o"></i> Applications</a></li>
                        </ul>
                    </li>

                    <li class="treeview <?php if(in_array($cur_page, ['gallery-album.php', 'gallery-album-add.php', 'gallery-album-edit.php', 'gallery.php', 'gallery-add.php', 'gallery-edit.php'], true)) {echo 'active';} ?>">
                        <a href="#">
                            <i class="fa fa-camera"></i>
                            <span>Gallery</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="gallery-album.php"><i class="fa fa-circle-o"></i> Albums</a></li>
                            <li><a href="gallery-album-add.php"><i class="fa fa-circle-o"></i> Add Album</a></li>
                            <li><a href="gallery.php"><i class="fa fa-circle-o"></i> All Photos</a></li>
                        </ul>
                    </li>

                    <li class="treeview <?php if(in_array($cur_page, ['blog.php', 'blog-add.php', 'blog-edit.php', 'faq.php', 'faq-add.php', 'faq-edit.php', 'testimonial.php', 'testimonial-add.php', 'testimonial-edit.php'], true)) {echo 'active';} ?>">
                        <a href="#">
                            <i class="fa fa-newspaper-o"></i>
                            <span>Content</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="blog.php"><i class="fa fa-circle-o"></i> News & Events</a></li>
                            <li><a href="faq.php"><i class="fa fa-circle-o"></i> FAQ</a></li>
                            <li><a href="testimonial.php"><i class="fa fa-circle-o"></i> Testimonials</a></li>
                        </ul>
                    </li>

      			</ul>
    		</section>
  		</aside>

  		<div class="content-wrapper">