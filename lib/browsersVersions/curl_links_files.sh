#!/bin/sh
curl "http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_stable_software_release/Google_Chrome" > /var/www/elfimov.ru/browsers/lib/browsersVersions/chrome_stable.txt
curl "http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_preview_software_release/Google_Chrome" > /var/www/elfimov.ru/browsers/lib/browsersVersions/chrome_preview.txt
curl "http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_stable_software_release/Firefox" > /var/www/elfimov.ru/browsers/lib/browsersVersions/firefox_stable.txt
curl "http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_preview_software_release/Firefox" > /var/www/elfimov.ru/browsers/lib/browsersVersions/firefox_preview.txt
curl "http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_stable_software_release/Opera" > /var/www/elfimov.ru/browsers/lib/browsersVersions/opera_stable.txt
curl "http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_preview_software_release/Opera" > /var/www/elfimov.ru/browsers/lib/browsersVersions/opera_preview.txt
curl "http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_stable_software_release/Internet_Explorer" > /var/www/elfimov.ru/browsers/lib/browsersVersions/ie_stable.txt
curl "http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_preview_software_release/Internet_Explorer" > /var/www/elfimov.ru/browsers/lib/browsersVersions/ie_preview.txt
curl "http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_stable_software_release/Safari" > /var/www/elfimov.ru/browsers/lib/browsersVersions/safari_stable.txt
curl "http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_preview_software_release/Safari" > /var/www/elfimov.ru/browsers/lib/browsersVersions/safari_preview.txt
