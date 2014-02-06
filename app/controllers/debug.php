<?php
view::autoRender(false);

if(DEBUG){
	$cache = $db->cacheGet ();
	$sql_archive = $_SESSION ['SQL_HISTORY'];
?>
<div style="position:absolute;">
<br /><b>SQL History:</b><br /><pre><?php print_r($sql_archive);?></pre>
<br /><br /><b>Debug Sessão:</b>
<?php tools::debug($_SESSION); ?>
<br /><br /><b>Database Cache:</b>
<?php tools::debug($cache); ?>
<br /><br /><b>Server:</b>
<?php tools::debug($_SERVER); ?>
<br /><br /><b>Cookies (Hum hm):</b>
<?php tools::debug($_COOKIE); ?>
</div>
<?php } ?>