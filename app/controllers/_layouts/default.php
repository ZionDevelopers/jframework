<?php

// Setting layout view vars
view::set ( array (
		'pageTitle' => SITE_NAME . (isset ( $pageTitle ) ? ' :: ' . $pageTitle : ' :: jFramework')
), '_layouts/' . LAYOUT_PAGE );

// Put view contents inside Layout
echo view::renderLayout ( $contents );
?>