#!/bin/sh

days=$1
re='^[0-9]+$'
if ! [[ $days =~ $re ]] ; then
	days="10"
fi

echo "Creating $(basename `pwd`)-changes-$(date +%Y%m%d).tar with files from the last $days days"

find * -mtime "-$days" -type f -print0 | xargs -0 tar rvf $(basename `pwd`)-changes-$(date +%Y%m%d).tar --exclude .git --exclude "*.tar" --exclude "caches" --exclude "*_cached"  --exclude _config.php --exclude _config.php.bak* --exclude "*.tmproj" --exclude "*~*" --exclude "Thumbs.db*" --exclude .DS_Store --exclude errorlog.php --exclude "*.log" --exclude critical_errors --exclude data_custom/latest_activity.bin --exclude uploads/galleries --exclude uploads/galleries_thumbs --exclude uploads/incoming --exclude uploads/auto_*
