#!/bin/sh
curl http://www.mozilla.org/en-US/firefox/all/ > ./release.html
curl http://www.mozilla.org/en-US/firefox/all-aurora.html > ./aurora.html
curl http://www.mozilla.org/en-US/firefox/all-beta.html > ./beta.html
curl http://ftp.mozilla.org/pub/mozilla.org/mobile/releases/latest/android/en-US/ > ./android-release.html
curl http://ftp.mozilla.org/pub/mozilla.org/mobile/releases/latest-beta/android/en-US/ > ./android-beta.html
