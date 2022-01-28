#!/bin/bash

rm -f themes/*/templates_cached/*/*.tcd
rm -f themes/*/templates_cached/*/*.tcp
rm -f themes/*/templates_cached/*/*.cache
rm -f themes/*/templates_cached/*/*.js
rm -f themes/*/templates_cached/*/*.css
rm -f themes/*/templates_cached/*/*.gz
rm -f safe_mode_temp/*.bin
rm -f caches/lang/*.lcd
rm -f caches/lang/*/*.lcd
find caches -name "*.gcd" -exec rm -f {} \;
find caches -name "*.xml" -exec rm -f {} \;
find caches -name "*.htm" -exec rm -f {} \;
find caches -name "*.gz" -exec rm -f {} \;
if [ -e "data_custom/failover_rewritemap.txt" ]; then
	echo > data_custom/failover_rewritemap.txt
	echo > data_custom/failover_rewritemap__mobile.txt
fi

if [ -e "_config.php" ]; then
	printf "\n\nif (!defined('DO_PLANNED_DECACHE')) define('DO_PLANNED_DECACHE', true);" >> _config.php
fi

if [ -e "sites" ]; then
   find sites -name "*.tcd" -exec rm -f {} \;
   find sites -name "*.tcp" -exec rm -f {} \;
   find sites -name "*.cache" -exec rm -f {} \;
   find sites -name "*.js" -path "*cache*" -exec rm -f {} \;
   find sites -name "*.css" -path "*cache*" -exec rm -f {} \;
   find sites -name "*.gz" -path "*cache*" -exec rm -f {} \;
   find sites -name "*.lcd" -exec rm -f {} \;
   find sites -name "*.gcd" -exec rm -f {} \;
   find sites -name "*.xml" -path "*cache*" -exec rm -f {} \;
   find sites -name "*.htm" -path "*cache*" -exec rm -f {} \;
fi

if [ -e "../decache.php" ]; then
    # Useful script, outside of web dir, for doing custom decaching
    php ../decache.php
fi
