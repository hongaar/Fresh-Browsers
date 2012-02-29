<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Fresh Browsers</title>
	<!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<link href="<?=$this->link('/favicon.ico')?>" rel="shortcut icon">
    <link href="<?=$this->link('/css/bootstrap.min.css')?>" rel="stylesheet">
	<link href="<?=$this->link('/css/style.css')?>" rel="stylesheet">
	<link href="<?=$this->link('/rss')?>" title="Fresh Browsers RSS" type="application/rss+xml" rel="alternate">
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
	
	<div id="content">
	
	<?=$this->out?>
	
	</div>

	<hr>
	
	<footer>
		<p>&copy; <a href="http://www.elfimov.ru">Dmitry Elfimov</a> 2011&mdash;<?=date('Y')?></p>
	</footer>

    </div> <!-- /container -->
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-29600575-1']);
_gaq.push(['_setDomainName', 'elfimov.ru']);
_gaq.push(['_trackPageview']);
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
</body>
</html>