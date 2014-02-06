<?php 
$flashMessage = view::getFlash();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /> 
        <title><?php echo $pageTitle?></title>
        <link rel="icon" type="image/png" href="<?php echo BASE_DIR;?>/img/favicon.png" />
        <link href="<?php echo BASE_DIR;?>/css/default.css" rel="stylesheet" type="text/css" media="screen" />
        <script type="text/javascript">
        var BASE_DIR = '<?php echo BASE_DIR;?>';
        var BASE_URL = '<?php echo BASE_URL;?>';
        </script>
    </head>
    <body>
    	<div id="mainContainer">
			<div id="contents" class="<?php echo (!$useSearch ? 'full' : '');?>">
				<?php if (!empty($flashMessage)){echo '<div id="flashMessage">', $flashMessage , '</div>'; }?>
				<?php echo view::$contents; ?>
			</div>
        </div>
        <?php 
        if (!empty($flashMessage)){ 
			view::delFlash();
        }
        ?>    
    </body>
</html>