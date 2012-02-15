<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Fresh Browsers</title>
	<!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
</head>

<body>

	<div class="container">
	
	<div class="hero-unit">
		<h1>Fresh Browsers</h1>
		<p>Latest versions of major web browsers.</p>
	</div>
	
	<ul class="nav nav-tabs">
		<li<?=$this->action=='index'?' class="active"':''?>><a href="<?=$this->link('/')?>">Latest</a></li>
		<li<?=$this->action=='history'?' class="active"':''?>><a href="<?=$this->link('/history')?>">History</a></li>
		<li<?=$this->action=='about'?' class="active"':''?>><a href="<?=$this->link('/about')?>">About</a></li>
    </ul>

	<?=$this->out?>

	<hr>
	
	<footer>
		<p>&copy; <a href="http://www.elfimov.ru">Dmitry Elfimov</a> 2011&mdash;<?=date('Y')?></p>
	</footer>

    </div> <!-- /container -->

</body>
</html>