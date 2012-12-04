#!/bin/sh
curl http://omahaproxy.appspot.com/all.json > ./all.json
curl "http://en.wikipedia.org/w/index.php?action=raw&title=Template:Latest_stable_software_release/Google_Chrome_for_Android" > ./stable_android.txt
