<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
<head>
<meta charset="utf-8" />
<link rel="stylesheet" href="css/install.css" type="text/css" media="all" />
<title>安装 B-studio</title>
</head>
<body>
	<h1>安装 B-studio</h1>

	<p class="error"><?php echo $this->error_message; ?></p>
	<fieldset>
		<legend>错误信息</legend>
		<?php echo $this->db_error_message; ?>
	</fieldset>

	<ul class="control">
		<li><input type="button" class="button" name="button" value="返回" onclick="history.back();"  /></li>
	</ul>

</body>
</html>