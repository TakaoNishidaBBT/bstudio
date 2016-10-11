<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja">
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="Thu, 01 Dec 1994 16:00:00 GMT"> 
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="css/install.css" type="text/css" media="all" />
<link rel="stylesheet" href="../bs-admin/css/selectbox_white.css" type="text/css" media="all" />
<script src="../bs-admin/js/bframe.js" type="text/javascript"></script>
<script src="../bs-admin/js/bframe_context_menu.js" type="text/javascript"></script>
<script src="../bs-admin/js/bframe_popup.js" type="text/javascript"></script>
<script src="../bs-admin/js/bframe_selectbox.js" type="text/javascript"></script>
<title>Install B-studio</title>
</head>
<body>


	<h1>Install B-studio</h1>

	<form method="post" action="index.php">
		<div id="select-language">
			<?php echo $select_language->getHtml(); ?>
			<input name="action" value="select-language" type="hidden" />
		</div>
	</form>

	<?php
		if($error_message) {
			echo '<p class="error-message-top">' . $error_message . '</p>';
		}
	?>

	<form method="post" action="index.php">
		<p>Setting up MySQL DataBase and Site admin configurations.</p>

		<h2>MySQL</h2>

		<p>Please enter the following field to set up connecting to MySQL DataBase.</p>
		<fieldset>
			<legend>Configuration DataBase</legend>
			<?php echo $db_install_form->getHtml(); ?>
		</fieldset>

		<h2>Basic authentication of admin page</h2>

		<p>Setting the basic authentication to the admin page. This is neccessary for preventing from unauthorized access.</p>
		<fieldset>
			<legend>Basic authentication of admin page</legend>
			<?php echo $admin_basic_auth_form->getHtml(); ?>
		</fieldset>

		<h2>Site admin</h2>

		<p>Please enter the following field to set up configuration of the site admin</p>
		<fieldset>
			<legend>Site admin</legend>
			<?php echo $admin_user_form->getHtml(); ?>
		</fieldset>

		<h2>htaccess</h2>

		<p>The htaccess file will be set at B-stuio's root directory.</p>
		<fieldset>
			<legend>htaccess</legend>
			<?php echo $root_htaccess->getHtml(); ?>
		</fieldset>

		<h2>Cofirmation of permission</h2>
			<?php echo $perm_message; ?>

		<h2>Confirm the contents</h2>

		<div class="confirm">
			<input name="action" value="confirm" type="hidden" />
			<input type="submit" class="button" value="Confirm" />
		</div>

	</form>
</body>
</html>