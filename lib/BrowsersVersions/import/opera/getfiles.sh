#!/bin/sh
curl --head http://snapshot.opera.com/windows/latest >windows-preview.txt
curl --head http://snapshot.opera.com/unix/latest >linux-preview.txt
curl --head http://snapshot.opera.com/mac/latest >mac-preview.txt

curl http://get.opera.com/pub/opera/info/files.txt > release.txt