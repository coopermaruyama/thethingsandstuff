#!/bin/bash
rsync -rP root@205.186.146.64:/var/www/thethingsandstuff.com/html/app/* ./app/
rsync -rP root@205.186.146.64:/var/www/thethingsandstuff.com/html/skin/* ./skin/
rsync -rP root@205.186.146.64:/var/www/thethingsandstuff.com/html/js/* ./js/
