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
