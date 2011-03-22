<?php
	if (isset($errors) && is_array($errors) && !empty($errors)) {
		foreach ($errors as $k=>$error) {
			echo '<span syle="color:red">' . $error . '</span><br />';
		}
	} elseif (isset($success)) {
		echo '<span syle="color:green">' . $success . '</span><br />';
	}
?>