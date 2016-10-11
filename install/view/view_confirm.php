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
<title>Install B-studio</title>
</head>
<body>

	<form method="post" action="">

		<h1>Install B-studio</h1>

		<?php if($error_message) { ?>
			<div class="error">
				<fieldset>
					<legend>エラー</legend>
					<?php echo $error_message; ?>
				</fieldset>
			</div>
		<?php } ?>

		<h2>MySQL</h2>

		<fieldset>
			<legend>Configuration DataBase</legend>
			<?php echo $db_install_form->getHtml('confirm'); ?>
		</fieldset>

		<h2>Basic authentication of admin page</h2>

		<fieldset>
			<legend>Basic authentication of admin page</legend>
			<?php echo $admin_basic_auth_form->getHtml('confirm'); ?>
		</fieldset>

		<h2>Site admin</h2>

		<fieldset>
			<legend>Site admin</legend>
			<?php echo $admin_user_form->getHtml('confirm'); ?>
		</fieldset>

		<h2>htaccess</h2>

		<fieldset>
			<legend>htaccess</legend>
			<?php echo $root_htaccess->getHtml('confirm'); ?>
		</fieldset>

		<h2>Install</h2>


		<fieldset>
			<legend>The following files will be created after install.</legend>
			<ul>
				<li>(install-directory)/.htaccess</li>
				<li>(install-directory)/bs-admin/user/users.php</li>
				<li>(install-directory)/bs-admin/config/core_config.php</li>
				<li>(install-directory)/bs-admin/db/db_connect.php</li>
			</ul>
			<p><span class="caution">※</span>The files will be overwriten when files are already exist.</p>
		</fieldset>

		<p>Click "Install" button to start install in the contents above.</p>

		<div class="confirm">
			<input name="action" value="install" type="hidden" />
			<input type="button" class="button" value="Back" onclick="location.href='index.php'"/>
			<input type="submit" class="button" value="Install" />
		</div>

	</form>
</body>
</html>