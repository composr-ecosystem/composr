#!/bin/bash

# Export a db.sql file from the MySQL database configured in _config.php
# Primarily designed for synching databases within the context of a revision control system

DB_HOST=$(cat _config.php|sed -n "s/\$SITE_INFO\['db_site_host'] = '\(.*\)';/\1/p")
DB_USER=$(cat _config.php|sed -n "s/\$SITE_INFO\['db_site_user'] = '\(.*\)';/\1/p")
DB_PASS=$(cat _config.php|sed -n "s/\$SITE_INFO\['db_site_password'] = '\(.*\)';/\1/p")
DB_NAME=$(cat _config.php|sed -n "s/\$SITE_INFO\['db_site'] = '\(.*\)';/\1/p")
DB_TABLE_PREFIX=$(cat _config.php|sed -n "s/\$SITE_INFO\['table_prefix'] = '\(.*\)';/\1/p")

if [ -z "${DB_NAME}" ] && [ ! -f ~/.my.cnf ];
then
	echo "_config.php has not been configured. You must install Composr, or grab an install's _config.php"
	exit 0
fi

ARGS=
if [ ! -z "${DB_NAME}" ] ;
then
	if [ -z "${DB_PASS}" ] ;
	then
		ARGS="-h${DB_HOST} -u${DB_USER} ${DB_NAME}"
	else
		ARGS="-h${DB_HOST} -u${DB_USER} -p${DB_PASS} ${DB_NAME}"
	fi
fi

NO_DATA_TABLES_A=
if [ "$1" == "simplified" ] ;
then
read -d '' NO_DATA_TABLES_A << EOF
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}video_transcoding
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}stats
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}f_moderator_logs
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}adminlogs
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}import_id_remap
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}import_parts_done
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}import_session
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}incoming_uploads
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}hackattack
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}link_tracker
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}logged_mail_messages
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}searches_logged
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}sessions
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}failedlogins
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}autosave
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}edit_pings
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}f_read_logs
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}f_post_history
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}member_tracking
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}messages_to_render
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}captchas
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}sms_log
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}temp_block_permissions
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}url_title_cache
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}webstandards_checked_once
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}cache
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}banner_clicks
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}cached_comcode_pages
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}cached_weather_codes
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}download_logging
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}email_bounces
EOF
fi

mysqldump ${ARGS} ${NO_DATA_TABLES_A} --lock-tables=false > db.sql

NO_DATA_TABLES_B=
if [ "$1" == "simplified" ] ;
then
read -d '' NO_DATA_TABLES_B << EOF
${DB_TABLE_PREFIX}video_transcoding
${DB_TABLE_PREFIX}stats
${DB_TABLE_PREFIX}stats_events
${DB_TABLE_PREFIX}stats_preprocessed
${DB_TABLE_PREFIX}stats_preprocessed_flat
${DB_TABLE_PREFIX}f_moderator_logs
${DB_TABLE_PREFIX}adminlogs
${DB_TABLE_PREFIX}import_id_remap
${DB_TABLE_PREFIX}import_parts_done
${DB_TABLE_PREFIX}import_session
${DB_TABLE_PREFIX}incoming_uploads
${DB_TABLE_PREFIX}hackattack
${DB_TABLE_PREFIX}logged_mail_messages
${DB_TABLE_PREFIX}searches_logged
${DB_TABLE_PREFIX}sessions
${DB_TABLE_PREFIX}failedlogins
${DB_TABLE_PREFIX}autosave
${DB_TABLE_PREFIX}edit_pings
${DB_TABLE_PREFIX}f_read_logs
${DB_TABLE_PREFIX}f_post_history
${DB_TABLE_PREFIX}member_tracking
${DB_TABLE_PREFIX}messages_to_render
${DB_TABLE_PREFIX}captchas
${DB_TABLE_PREFIX}sms_log
${DB_TABLE_PREFIX}temp_block_permissions
${DB_TABLE_PREFIX}url_title_cache
${DB_TABLE_PREFIX}webstandards_checked_once
${DB_TABLE_PREFIX}cache
${DB_TABLE_PREFIX}banner_clicks
${DB_TABLE_PREFIX}cached_comcode_pages
${DB_TABLE_PREFIX}cached_weather_codes
${DB_TABLE_PREFIX}download_logging
${DB_TABLE_PREFIX}email_bounces
EOF
fi

mysqldump ${ARGS} ${NO_DATA_TABLES_B} --lock-tables=false --no-data >> db.sql
