<?php
/**
 * jFramework
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
?>
Let's Debug!
<ul>
	<li><b>Session Debug</b>
		<?php tools::debug($_SESSION); ?>
	</li>
	<li><b>Database Cache</b>
		<?php tools::debug($cache); ?>
	</li>
	<li><b>Server</b>
		<?php tools::debug($_SERVER); ?>
	</li>
	<li><b>Cookies (Hum hm)</b>
		<?php tools::debug($_COOKIE); ?>
	</li>
</ul>