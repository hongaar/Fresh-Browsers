<?php
header('Content-Type: text/xml; charset=utf-8');
?>
<<??>?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
<title>Fresh Browsers</title>
<link><?=$this->link('/', true);?></link>
<atom:link href="<?=$this->link('/rss/', true);?>" rel="self" type="application/rss+xml" />
<description>Latest versions of major web browsers</description>
<docs>http://blogs.law.harvard.edu/tech/rss</docs>
<webMaster>dmitry@elfimov.ru (Dmitry Elfimov)</webMaster>
<ttl>600</ttl>
<?=$this->out?>
</channel>
</rss>

