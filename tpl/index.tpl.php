<?php
$subtitle = isset($this->subtitle) ? $this->subtitle : $this->lib->t('The latest versions of major web browsers.');
header('Content-Language: '.$this->lib->t->language); 
?>
<!DOCTYPE html>
<html lang="<?=$this->lib->t->language?>">
<head>
	<meta charset="utf-8">
	<title>Fresh Browsers - <?=$subtitle?></title>
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<link href="<?=$this->link('/favicon.ico')?>" rel="shortcut icon">
    <link href="<?=$this->link('/css/bootstrap.min.css')?>" rel="stylesheet">
	<link href="<?=$this->link('/css/style.css')?>" rel="stylesheet">
	<link href="<?=$this->link('/rss')?>" title="Fresh Browsers RSS" type="application/rss+xml" rel="alternate">
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
	<script type="text/javascript" src="<?=$this->link('/js/common.js')?>"></script>
</head>

<body>

	<div class="container">
	
	<div class="hero-unit">
		<ul class="nav nav-pills" id="language">
		<?php foreach ($this->languages as $code => $name) { ?>
			<li<?=$this->lib->t->language==$code?' class="active"':''?>><a href="<?=$this->link($code.'/'.$this->rawAction)?>" title="<?=$name?>"><?=ucfirst($code)?></a></li>
		<?php } ?>
		</ul>
		<h2><a href="<?=$this->link('/'.$this->lib->t->language)?>">Fresh Browsers</a></h2>
		<h1><?=$subtitle?></h1>
	</div>
	
	<ul class="nav nav-tabs">
		<li<?=$this->action=='index'?' class="active"':''?>><a href="<?=$this->link('/'.$this->lib->t->language)?>"><?=$this->lib->t('Latest')?></a></li>
		<li<?=$this->action=='history'?' class="active"':''?>><a href="<?=$this->link('/'.$this->lib->t->language.'/history')?>"><?=$this->lib->t('History')?></a></li>
		<li<?=$this->action=='about'?' class="active"':''?>><a href="<?=$this->link('/'.$this->lib->t->language.'/about')?>"><?=$this->lib->t('About')?></a></li>
    </ul>
	
	<div id="content">
	
	<?=$this->out?>
	
	</div>

	<hr>
	
	<footer>
		<p>&copy; <a href="http://www.elfimov.ru"><?=$this->lib->t('Dmitry Elfimov')?></a> 2011&mdash;<?=date('Y')?></p>
	</footer>

    </div> <!-- /container -->
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-29600575-1']);
_gaq.push(['_setDomainName', 'fresh-browsers.com']);
_gaq.push(['_trackPageview']);
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
</body>
</html>