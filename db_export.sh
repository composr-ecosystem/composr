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
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}autosave
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}banner_clicks
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}cache
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}cache_on
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}cached_comcode_pages
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}captchas
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}catalogue_cat_treecache
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}catalogue_childcountcache
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}ce_fulltext_index
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}chat_active
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}chat_events
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}cpages_fulltext_index
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}cron_caching_requests
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}daily_visits
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}digestives_consumed
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}digestives_tin
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}download_logging
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}edit_pings
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}email_bounces
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}f_forum_intro_ip
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}f_forum_intro_member
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}f_group_join_log
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}f_password_history
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}f_posts_fulltext_index
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}f_pposts_fulltext_index
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}f_read_logs
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}failedlogins
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}feature_lifetime_monitor
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}ft_index_commonality
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}hackattack
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}import_id_remap
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}import_parts_done
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}import_session
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}incoming_uploads
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}ip_country
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}logged_mail_messages
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}member_tracking
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}messages_to_render
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}newsletter_drip_send
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}post_tokens
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}quiz_member_last_visit
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}revisions
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}searches_logged
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}sessions
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}shopping_logging
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}sitemap_cache
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}sms_log
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}staff_tips_dismissed
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}stats
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}stats_events
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}stats_link_tracker
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}task_queue
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}temp_block_permissions
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}theme_screen_tree
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}theme_template_relations
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}tutorial_links
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}url_title_cache
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}urls_checked
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}usersonline_track
--ignore-table=${DB_NAME}.${DB_TABLE_PREFIX}webstandards_checked_once
EOF
fi

mysqldump ${ARGS} ${NO_DATA_TABLES_A} --lock-tables=false > db.sql

NO_DATA_TABLES_B=
if [ "$1" == "simplified" ] ;
then
read -d '' NO_DATA_TABLES_B << EOF
${DB_TABLE_PREFIX}autosave
${DB_TABLE_PREFIX}banner_clicks
${DB_TABLE_PREFIX}cache
${DB_TABLE_PREFIX}cache_on
${DB_TABLE_PREFIX}cached_comcode_pages
${DB_TABLE_PREFIX}captchas
${DB_TABLE_PREFIX}catalogue_cat_treecache
${DB_TABLE_PREFIX}catalogue_childcountcache
${DB_TABLE_PREFIX}ce_fulltext_index
${DB_TABLE_PREFIX}chat_active
${DB_TABLE_PREFIX}chat_events
${DB_TABLE_PREFIX}cpages_fulltext_index
${DB_TABLE_PREFIX}cron_caching_requests
${DB_TABLE_PREFIX}daily_visits
${DB_TABLE_PREFIX}digestives_consumed
${DB_TABLE_PREFIX}digestives_tin
${DB_TABLE_PREFIX}download_logging
${DB_TABLE_PREFIX}edit_pings
${DB_TABLE_PREFIX}email_bounces
${DB_TABLE_PREFIX}f_forum_intro_ip
${DB_TABLE_PREFIX}f_forum_intro_member
${DB_TABLE_PREFIX}f_group_join_log
${DB_TABLE_PREFIX}f_password_history
${DB_TABLE_PREFIX}f_posts_fulltext_index
${DB_TABLE_PREFIX}f_pposts_fulltext_index
${DB_TABLE_PREFIX}f_read_logs
${DB_TABLE_PREFIX}failedlogins
${DB_TABLE_PREFIX}feature_lifetime_monitor
${DB_TABLE_PREFIX}ft_index_commonality
${DB_TABLE_PREFIX}hackattack
${DB_TABLE_PREFIX}import_id_remap
${DB_TABLE_PREFIX}import_parts_done
${DB_TABLE_PREFIX}import_session
${DB_TABLE_PREFIX}incoming_uploads
${DB_TABLE_PREFIX}ip_country
${DB_TABLE_PREFIX}logged_mail_messages
${DB_TABLE_PREFIX}member_tracking
${DB_TABLE_PREFIX}messages_to_render
${DB_TABLE_PREFIX}newsletter_drip_send
${DB_TABLE_PREFIX}post_tokens
${DB_TABLE_PREFIX}quiz_member_last_visit
${DB_TABLE_PREFIX}revisions
${DB_TABLE_PREFIX}searches_logged
${DB_TABLE_PREFIX}sessions
${DB_TABLE_PREFIX}shopping_logging
${DB_TABLE_PREFIX}sitemap_cache
${DB_TABLE_PREFIX}sms_log
${DB_TABLE_PREFIX}staff_tips_dismissed
${DB_TABLE_PREFIX}stats
${DB_TABLE_PREFIX}stats_events
${DB_TABLE_PREFIX}stats_link_tracker
${DB_TABLE_PREFIX}task_queue
${DB_TABLE_PREFIX}temp_block_permissions
${DB_TABLE_PREFIX}theme_screen_tree
${DB_TABLE_PREFIX}theme_template_relations
${DB_TABLE_PREFIX}tutorial_links
${DB_TABLE_PREFIX}url_title_cache
${DB_TABLE_PREFIX}urls_checked
${DB_TABLE_PREFIX}usersonline_track
${DB_TABLE_PREFIX}webstandards_checked_once
EOF
fi

mysqldump ${ARGS} ${NO_DATA_TABLES_B} --lock-tables=false --no-data >> db.sql
