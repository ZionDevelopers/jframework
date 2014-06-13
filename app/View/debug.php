<?php
/**
 * jFramework
 *
 * @version 2.0.0
 * @link https://github.com/ZionDevelopers/jframework/ The jFramework GitHub Project
 * @copyright 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
?>
Let's Debug!
<ul>
	<li><b>Session Debug</b>
		<?php Tools::debug($_SESSION); ?>
	</li>
	<li><b>Database Cache</b>
		<?php Tools::debug($cache); ?>
	</li>
	<li><b>Server</b>
		<?php Tools::debug($_SERVER); ?>
	</li>
	<li><b>Cookies (Hum hm)</b>
		<?php Tools::debug($_COOKIE); ?>
	</li>
</ul>