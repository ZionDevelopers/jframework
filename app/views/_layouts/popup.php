<?php
/**
 * jFramework
 *
 * @version 1.3.0
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

// Use View
use \jFramework\Core\View;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://www.w3.org/2005/10/profile">
<link rel="icon" type="image/png" href="<?php echo BASE_DIR;?>/img/favicon.png" />
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>" />
<title><?php echo $pageTitle?></title>
<link href="<?php echo BASE_DIR;?>/css/default.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body>
<?php echo View::$contents?>
</body>
</html>