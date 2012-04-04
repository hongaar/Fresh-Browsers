<div class="row">

<div class="span8">

<p><?=$this->lib->t('about_text')?></p>

<h3><?=$this->lib->t('Available formats')?></h3>

<ul>
	<li><a href="<?=$this->link('/export/browsers.xml')?>">XML</a></li>
	<li><a href="<?=$this->link('/export/browsers.yaml')?>">YAML</a></li>
	<li><a href="<?=$this->link('/export/browsers.json')?>">JSON</a></li>
	<li><a href="<?=$this->link('/export/browsers.serialized')?>">PHP serialized</a></li>
	<li><a href="<?=$this->link('/export/browsers.html')?>">HTML</a></li>
	<li><a href="<?=$this->link('/rss')?>">RSS</a></li>
	<li><a href="http://twitter.com/FreshBrowsers">Twitter</a></li>
</ul>

<br>

<p><?=$this->lib->t('get_sorce_text')?></p>

<p><a href="http://github.com/Groozly/Fresh-Browsers" class="btn btn-info"><?=$this->lib->t('Fresh Browsers on GitHub')?></a></p>

</div>

<div class="span4">
	<div class="alert alert-info">
		<strong>Heads up!</strong> 
		<br>
		Please help me to translate Fresh&nbsp;Browsers in your language.
		<br><br>
		<strong>Instructions for translators.</strong> 
		<br>
		If you want to contribute to the translations, please follow these steps:
		<ul>
			<li>Get the <a href="/download/messages.php.txt" style="text-decoration:underline">translation file</a>.</li>
			<li>Translate and specify language.</li>
			<li>Send <a href="mailto:elfimov@gmail.com" style="text-decoration:underline">me</a> the translated file.</li>
		</ul>
	</div>
</div>

</div>