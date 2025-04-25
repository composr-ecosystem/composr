<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite_tracker
 */

/**
 * Module page class.
 */
class Module_tracker
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'Composr';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 4;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'cms_homesite_tracker';
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        // MANTIS TABLE DELETION

        if (strpos(get_db_type(), 'mysql') === false) {
            return;
        }

        $tables = [
            'mantis_api_token_table',
            'mantis_bug_file_table',
            'mantis_bug_history_table',
            'mantis_bug_monitor_table',
            'mantis_bug_relationship_table',
            'mantis_bug_revision_table',
            'mantis_bug_table',
            'mantis_bug_tag_table',
            'mantis_bug_text_table',
            'mantis_bugnote_table',
            'mantis_bugnote_text_table',
            'mantis_category_table',
            'mantis_config_table',
            'mantis_custom_field_project_table',
            'mantis_custom_field_string_table',
            'mantis_custom_field_table',
            'mantis_email_table',
            'mantis_filters_table',
            'mantis_news_table',
            'mantis_plugin_table',
            'mantis_project_file_table',
            'mantis_project_hierarchy_table',
            'mantis_project_table',
            'mantis_project_user_list_table',
            'mantis_project_version_table',
            'mantis_sponsorship_table',
            'mantis_tag_table',
            'mantis_tokens_table',
            'mantis_user_pref_table',
            'mantis_user_print_pref_table',
            'mantis_user_profile_table',
            'mantis_user_table',
        ];
        $GLOBALS['SITE_DB']->query('DROP TABLE IF EXISTS ' . implode(',' , $tables));
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install(?int $upgrade_from = null, ?int $upgrade_from_hack = null)
    {
        if ($upgrade_from === null) {
            // Tracker...

            if (strpos(get_db_type(), 'mysql') !== false) {
                $table_type = (get_value('innodb') == '1') ? 'InnoDB' : 'MyISAM';

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_api_token_table` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `name` varchar(128) NOT NULL,
                    `hash` varchar(128) NOT NULL,
                    `date_created` int(10) unsigned NOT NULL DEFAULT '1',
                    `date_used` int(10) unsigned NOT NULL DEFAULT '1',
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `idx_user_id_name` (`user_id`,`name`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_bug_file_table` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `bug_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `title` varchar(250) NOT NULL DEFAULT '',
                    `description` varchar(250) NOT NULL DEFAULT '',
                    `diskfile` varchar(250) NOT NULL DEFAULT '',
                    `filename` varchar(250) NOT NULL DEFAULT '',
                    `folder` varchar(250) NOT NULL DEFAULT '',
                    `filesize` int(11) NOT NULL DEFAULT '0',
                    `file_type` varchar(250) NOT NULL DEFAULT '',
                    `content` longblob,
                    `date_added` int(10) unsigned NOT NULL DEFAULT '1',
                    `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `bugnote_id` int(10) unsigned NOT NULL DEFAULT '0',
                    PRIMARY KEY (`id`),
                    KEY `idx_bug_file_bug_id` (`bug_id`),
                    KEY `idx_diskfile` (`diskfile`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_bug_history_table` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `bug_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `field_name` varchar(64) NOT NULL,
                    `old_value` varchar(255) NOT NULL,
                    `new_value` varchar(255) NOT NULL,
                    `type` smallint(6) NOT NULL DEFAULT '0',
                    `date_modified` int(10) unsigned NOT NULL DEFAULT '1',
                    PRIMARY KEY (`id`),
                    KEY `idx_bug_history_bug_id` (`bug_id`),
                    KEY `idx_history_user_id` (`user_id`),
                    KEY `idx_bug_history_date_modified` (`date_modified`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_bug_monitor_table` (
                    `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `bug_id` int(10) unsigned NOT NULL DEFAULT '0',
                    PRIMARY KEY (`user_id`,`bug_id`),
                    KEY `idx_bug_id` (`bug_id`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_bug_relationship_table` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `source_bug_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `destination_bug_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `relationship_type` smallint(6) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`id`),
                    KEY `idx_relationship_source` (`source_bug_id`),
                    KEY `idx_relationship_destination` (`destination_bug_id`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_bug_revision_table` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `bug_id` int(10) unsigned NOT NULL,
                    `bugnote_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `user_id` int(10) unsigned NOT NULL,
                    `type` int(10) unsigned NOT NULL,
                    `value` longtext NOT NULL,
                    `timestamp` int(10) unsigned NOT NULL DEFAULT '1',
                    PRIMARY KEY (`id`),
                    KEY `idx_bug_rev_type` (`type`),
                    KEY `idx_bug_rev_id_time` (`bug_id`,`timestamp`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_bug_table` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `project_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `reporter_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `handler_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `duplicate_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `priority` smallint(6) NOT NULL DEFAULT '30',
                    `severity` smallint(6) NOT NULL DEFAULT '50',
                    `reproducibility` smallint(6) NOT NULL DEFAULT '10',
                    `status` smallint(6) NOT NULL DEFAULT '10',
                    `resolution` smallint(6) NOT NULL DEFAULT '10',
                    `projection` smallint(6) NOT NULL DEFAULT '10',
                    `eta` smallint(6) NOT NULL DEFAULT '10',
                    `bug_text_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `os` varchar(32) NOT NULL DEFAULT '',
                    `os_build` varchar(32) NOT NULL DEFAULT '',
                    `platform` varchar(32) NOT NULL DEFAULT '',
                    `version` varchar(64) NOT NULL DEFAULT '',
                    `fixed_in_version` varchar(64) NOT NULL DEFAULT '',
                    `build` varchar(32) NOT NULL DEFAULT '',
                    `profile_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `view_state` smallint(6) NOT NULL DEFAULT '10',
                    `summary` varchar(128) NOT NULL DEFAULT '',
                    `sponsorship_total` int(11) NOT NULL DEFAULT '0',
                    `sticky` tinyint(4) NOT NULL DEFAULT '0',
                    `target_version` varchar(64) NOT NULL DEFAULT '',
                    `category_id` int(10) unsigned NOT NULL DEFAULT '1',
                    `date_submitted` int(10) unsigned NOT NULL DEFAULT '1',
                    `due_date` int(10) unsigned NOT NULL DEFAULT '1',
                    `last_updated` int(10) unsigned NOT NULL DEFAULT '1',
                    PRIMARY KEY (`id`),
                    KEY `idx_bug_sponsorship_total` (`sponsorship_total`),
                    KEY `idx_bug_fixed_in_version` (`fixed_in_version`),
                    KEY `idx_bug_status` (`status`),
                    KEY `idx_project` (`project_id`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_bug_tag_table` (
                    `bug_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `tag_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `date_attached` int(10) unsigned NOT NULL DEFAULT '1',
                    PRIMARY KEY (`bug_id`,`tag_id`),
                    KEY `idx_bug_tag_tag_id` (`tag_id`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_bug_text_table` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `description` longtext NOT NULL,
                    `steps_to_reproduce` longtext NOT NULL,
                    `additional_information` longtext NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8;
                    ");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_bugnote_table` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `bug_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `reporter_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `bugnote_text_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `view_state` smallint(6) NOT NULL DEFAULT '10',
                    `note_type` int(11) DEFAULT '0',
                    `note_attr` varchar(250) DEFAULT '',
                    `time_tracking` int(10) unsigned NOT NULL DEFAULT '0',
                    `last_modified` int(10) unsigned NOT NULL DEFAULT '1',
                    `date_submitted` int(10) unsigned NOT NULL DEFAULT '1',
                    PRIMARY KEY (`id`),
                    KEY `idx_bug` (`bug_id`),
                    KEY `idx_last_mod` (`last_modified`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_bugnote_text_table` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `note` longtext NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_category_table` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `project_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `name` varchar(128) NOT NULL DEFAULT '',
                    `status` int(10) unsigned NOT NULL DEFAULT '0',
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `idx_category_project_name` (`project_id`,`name`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("INSERT INTO `mantis_category_table` values('1','0','0','General','0')");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_config_table` (
                    `config_id` varchar(64) NOT NULL,
                    `project_id` int(11) NOT NULL DEFAULT '0',
                    `user_id` int(11) NOT NULL DEFAULT '0',
                    `access_reqd` int(11) DEFAULT '0',
                    `type` int(11) DEFAULT '90',
                    `value` longtext NOT NULL,
                    PRIMARY KEY (`config_id`,`project_id`,`user_id`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("INSERT INTO `mantis_config_table` values('database_version','0','0','90','1','209')");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_custom_field_project_table` (
                    `field_id` int(11) NOT NULL DEFAULT '0',
                    `project_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `sequence` smallint(6) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`field_id`,`project_id`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("INSERT INTO `mantis_custom_field_project_table` values('1','1','0'),('2','1','0')");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_custom_field_string_table` (
                    `field_id` int(11) NOT NULL DEFAULT '0',
                    `bug_id` int(11) NOT NULL DEFAULT '0',
                    `value` varchar(255) NOT NULL DEFAULT '',
                    `text` longtext,
                    PRIMARY KEY (`field_id`,`bug_id`),
                    KEY `idx_custom_field_bug` (`bug_id`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_custom_field_table` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(64) NOT NULL DEFAULT '',
                    `type` smallint(6) NOT NULL DEFAULT '0',
                    `possible_values` text NOT NULL,
                    `default_value` varchar(255) NOT NULL DEFAULT '',
                    `valid_regexp` varchar(255) NOT NULL DEFAULT '',
                    `access_level_r` smallint(6) NOT NULL DEFAULT '0',
                    `access_level_rw` smallint(6) NOT NULL DEFAULT '0',
                    `length_min` int(11) NOT NULL DEFAULT '0',
                    `length_max` int(11) NOT NULL DEFAULT '0',
                    `require_report` tinyint(4) NOT NULL DEFAULT '0',
                    `require_update` tinyint(4) NOT NULL DEFAULT '0',
                    `display_report` tinyint(4) NOT NULL DEFAULT '0',
                    `display_update` tinyint(4) NOT NULL DEFAULT '1',
                    `require_resolved` tinyint(4) NOT NULL DEFAULT '0',
                    `display_resolved` tinyint(4) NOT NULL DEFAULT '0',
                    `display_closed` tinyint(4) NOT NULL DEFAULT '0',
                    `require_closed` tinyint(4) NOT NULL DEFAULT '0',
                    `filter_by` tinyint(4) NOT NULL DEFAULT '1',
                    PRIMARY KEY (`id`),
                    KEY `idx_custom_field_name` (`name`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("INSERT INTO `mantis_custom_field_table` values('1','Time estimation (hours)','2','','0','','10','10','0','0','0','0','1','1','0','1','1','0','1'),('2','Sponsorship open','5','Open','0','','10','55','0','0','0','0','1','1','0','1','1','0','1')");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_email_table` (
                    `email_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `email` varchar(64) NOT NULL DEFAULT '',
                    `subject` varchar(250) NOT NULL DEFAULT '',
                    `metadata` longtext NOT NULL,
                    `body` longtext NOT NULL,
                    `submitted` int(10) unsigned NOT NULL DEFAULT '1',
                    PRIMARY KEY (`email_id`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_filters_table` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `user_id` int(11) NOT NULL DEFAULT '0',
                    `project_id` int(11) NOT NULL DEFAULT '0',
                    `is_public` tinyint(4) DEFAULT NULL,
                    `name` varchar(64) NOT NULL DEFAULT '',
                    `filter_string` longtext NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_news_table` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `project_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `poster_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `view_state` smallint(6) NOT NULL DEFAULT '10',
                    `announcement` tinyint(4) NOT NULL DEFAULT '0',
                    `headline` varchar(64) NOT NULL DEFAULT '',
                    `body` longtext NOT NULL,
                    `last_modified` int(10) unsigned NOT NULL DEFAULT '1',
                    `date_posted` int(10) unsigned NOT NULL DEFAULT '1',
                    PRIMARY KEY (`id`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_plugin_table` (
                    `basename` varchar(40) NOT NULL,
                    `enabled` tinyint(4) NOT NULL DEFAULT '0',
                    `protected` tinyint(4) NOT NULL DEFAULT '0',
                    `priority` int(10) unsigned NOT NULL DEFAULT '3',
                    PRIMARY KEY (`basename`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("INSERT INTO `mantis_plugin_table` values('MantisCoreFormatting','1','0','3')");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_project_file_table` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `project_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `title` varchar(250) NOT NULL DEFAULT '',
                    `description` varchar(250) NOT NULL DEFAULT '',
                    `diskfile` varchar(250) NOT NULL DEFAULT '',
                    `filename` varchar(250) NOT NULL DEFAULT '',
                    `folder` varchar(250) NOT NULL DEFAULT '',
                    `filesize` int(11) NOT NULL DEFAULT '0',
                    `file_type` varchar(250) NOT NULL DEFAULT '',
                    `content` longblob,
                    `date_added` int(10) unsigned NOT NULL DEFAULT '1',
                    `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                    PRIMARY KEY (`id`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_project_hierarchy_table` (
                    `child_id` int(10) unsigned NOT NULL,
                    `parent_id` int(10) unsigned NOT NULL,
                    `inherit_parent` tinyint(4) NOT NULL DEFAULT '0',
                    UNIQUE KEY `idx_project_hierarchy` (`child_id`,`parent_id`),
                    KEY `idx_project_hierarchy_child_id` (`child_id`),
                    KEY `idx_project_hierarchy_parent_id` (`parent_id`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_project_table` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `name` varchar(128) NOT NULL DEFAULT '',
                    `status` smallint(6) NOT NULL DEFAULT '10',
                    `enabled` tinyint(4) NOT NULL DEFAULT '1',
                    `view_state` smallint(6) NOT NULL DEFAULT '10',
                    `access_min` smallint(6) NOT NULL DEFAULT '10',
                    `file_path` varchar(250) NOT NULL DEFAULT '',
                    `description` longtext NOT NULL,
                    `category_id` int(10) unsigned NOT NULL DEFAULT '1',
                    `inherit_global` tinyint(4) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `idx_project_name` (`name`),
                    KEY `idx_project_view` (`view_state`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("INSERT INTO `mantis_project_table` values('1','All Projects','10','1','10','10','','','1','1')");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_project_user_list_table` (
                    `project_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `access_level` smallint(6) NOT NULL DEFAULT '10',
                    PRIMARY KEY (`project_id`,`user_id`),
                    KEY `idx_project_user` (`user_id`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_project_version_table` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `project_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `version` varchar(64) NOT NULL DEFAULT '',
                    `description` longtext NOT NULL,
                    `released` tinyint(4) NOT NULL DEFAULT '1',
                    `obsolete` tinyint(4) NOT NULL DEFAULT '0',
                    `date_order` int(10) unsigned NOT NULL DEFAULT '1',
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `idx_project_version` (`project_id`,`version`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_sponsorship_table` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `bug_id` int(11) NOT NULL DEFAULT '0',
                    `user_id` int(11) NOT NULL DEFAULT '0',
                    `amount` int(11) NOT NULL DEFAULT '0',
                    `logo` varchar(128) NOT NULL DEFAULT '',
                    `url` varchar(128) NOT NULL DEFAULT '',
                    `paid` tinyint(4) NOT NULL DEFAULT '0',
                    `date_submitted` int(10) unsigned NOT NULL DEFAULT '1',
                    `last_updated` int(10) unsigned NOT NULL DEFAULT '1',
                    PRIMARY KEY (`id`),
                    KEY `idx_sponsorship_bug_id` (`bug_id`),
                    KEY `idx_sponsorship_user_id` (`user_id`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_tag_table` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `name` varchar(100) NOT NULL DEFAULT '',
                    `description` longtext NOT NULL,
                    `date_created` int(10) unsigned NOT NULL DEFAULT '1',
                    `date_updated` int(10) unsigned NOT NULL DEFAULT '1',
                    PRIMARY KEY (`id`,`name`),
                    KEY `idx_tag_name` (`name`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_tokens_table` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `owner` int(11) NOT NULL,
                    `type` int(11) NOT NULL,
                    `value` longtext NOT NULL,
                    `timestamp` int(10) unsigned NOT NULL DEFAULT '1',
                    `expiry` int(10) unsigned NOT NULL DEFAULT '1',
                    PRIMARY KEY (`id`),
                    KEY `idx_typeowner` (`type`,`owner`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_user_pref_table` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `project_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `default_profile` int(10) unsigned NOT NULL DEFAULT '0',
                    `default_project` int(10) unsigned NOT NULL DEFAULT '0',
                    `refresh_delay` int(11) NOT NULL DEFAULT '0',
                    `redirect_delay` int(11) NOT NULL DEFAULT '0',
                    `bugnote_order` varchar(4) NOT NULL DEFAULT 'ASC',
                    `email_on_new` tinyint(4) NOT NULL DEFAULT '0',
                    `email_on_assigned` tinyint(4) NOT NULL DEFAULT '0',
                    `email_on_feedback` tinyint(4) NOT NULL DEFAULT '0',
                    `email_on_resolved` tinyint(4) NOT NULL DEFAULT '0',
                    `email_on_closed` tinyint(4) NOT NULL DEFAULT '0',
                    `email_on_reopened` tinyint(4) NOT NULL DEFAULT '0',
                    `email_on_bugnote` tinyint(4) NOT NULL DEFAULT '0',
                    `email_on_status` tinyint(4) NOT NULL DEFAULT '0',
                    `email_on_priority` tinyint(4) NOT NULL DEFAULT '0',
                    `email_on_priority_min_severity` smallint(6) NOT NULL DEFAULT '10',
                    `email_on_status_min_severity` smallint(6) NOT NULL DEFAULT '10',
                    `email_on_bugnote_min_severity` smallint(6) NOT NULL DEFAULT '10',
                    `email_on_reopened_min_severity` smallint(6) NOT NULL DEFAULT '10',
                    `email_on_closed_min_severity` smallint(6) NOT NULL DEFAULT '10',
                    `email_on_resolved_min_severity` smallint(6) NOT NULL DEFAULT '10',
                    `email_on_feedback_min_severity` smallint(6) NOT NULL DEFAULT '10',
                    `email_on_assigned_min_severity` smallint(6) NOT NULL DEFAULT '10',
                    `email_on_new_min_severity` smallint(6) NOT NULL DEFAULT '10',
                    `email_bugnote_limit` smallint(6) NOT NULL DEFAULT '0',
                    `language` varchar(32) NOT NULL DEFAULT 'english',
                    `timezone` varchar(32) NOT NULL DEFAULT '',
                    PRIMARY KEY (`id`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_user_print_pref_table` (
                    `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `print_pref` varchar(64) NOT NULL,
                    PRIMARY KEY (`user_id`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_user_profile_table` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                    `platform` varchar(32) NOT NULL DEFAULT '',
                    `os` varchar(32) NOT NULL DEFAULT '',
                    `os_build` varchar(32) NOT NULL DEFAULT '',
                    `description` longtext NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");

                $GLOBALS['SITE_DB']->query("CREATE TABLE IF NOT EXISTS `mantis_user_table` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `username` varchar(191) NOT NULL DEFAULT '',
                    `realname` varchar(191) NOT NULL DEFAULT '',
                    `email` varchar(191) NOT NULL DEFAULT '',
                    `password` varchar(255) NOT NULL DEFAULT '',
                    `enabled` tinyint(4) NOT NULL DEFAULT '1',
                    `protected` tinyint(4) NOT NULL DEFAULT '0',
                    `access_level` smallint(6) NOT NULL DEFAULT '10',
                    `login_count` int(11) NOT NULL DEFAULT '0',
                    `lost_password_request_count` smallint(6) NOT NULL DEFAULT '0',
                    `failed_login_count` smallint(6) NOT NULL DEFAULT '0',
                    `cookie_string` varchar(64) NOT NULL DEFAULT '',
                    `last_visit` int(10) unsigned NOT NULL DEFAULT '1',
                    `date_created` int(10) unsigned NOT NULL DEFAULT '1',
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `idx_user_cookie_string` (`cookie_string`),
                    UNIQUE KEY `idx_user_username` (`username`),
                    KEY `idx_enable` (`enabled`),
                    KEY `idx_access` (`access_level`),
                    KEY `idx_email` (`email`)
                ) ENGINE=" . $table_type . " DEFAULT CHARSET=utf8");
            }
        }

        if (($upgrade_from !== null) && ($upgrade_from < 3)) { // LEGACY
            if (strpos(get_db_type(), 'mysql') !== false) {
                $GLOBALS['SITE_DB']->query("ALTER TABLE mantis_bug_file_table ADD bugnote_id int(10) unsigned NOT NULL DEFAULT '0'");
            }
        }
    }

    /**
     * Find entry-points available within this module.
     *
     * @param  boolean $check_perms Whether to check permissions
     * @param  ?MEMBER $member_id The member to check permissions as (null: current user)
     * @param  boolean $support_crosslinks Whether to allow cross links to other modules (identifiable via a full-page-link rather than a screen-name)
     * @param  boolean $be_deferential Whether to avoid any entry-point (or even return null to disable the page in the Sitemap) if we know another module, or page_group, is going to link to that entry-point. Note that "!" and "browse" entry points are automatically merged with container page nodes (likely called by page-groupings) as appropriate.
     * @return ?array A map of entry points (screen-name=>language-code/string or screen-name=>[language-code/string, icon-theme-image]) (null: disabled)
     */
    public function get_entry_points(bool $check_perms = true, ?int $member_id = null, bool $support_crosslinks = true, bool $be_deferential = false) : ?array
    {
        if (!addon_installed('booking')) {
            return null;
        }

        return [
            'browse' => ['CREATE_BOOKING', 'booking/book'],
        ];
    }

    public $title;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none)
     */
    public function pre_run() : ?object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('cms_homesite_tracker', $error_msg)) {
            return $error_msg;
        }
        if (!addon_installed__messaged('calendar', $error_msg)) {
            return $error_msg;
        }
        if (!addon_installed__messaged('ecommerce', $error_msg)) {
            return $error_msg;
        }

        if (strpos(get_db_type(), 'mysql') === false) {
            warn_exit('This works with MySQL only');
        }

        require_lang('tracker');

        $type = get_param_string('type', 'browse');

        if ($type == 'browse') {
            require_lang('tracker');
            $this->title = get_screen_title('MANTIS_TRACKER_PAGE_TITLE');
        }

        return null;
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution
     */
    public function run() : object
    {
        $type = get_param_string('type', 'browse');

        // Decide what to do
        if ($type == 'browse') {
            return $this->tracker(); // NB: This may be skipped, if blocks were used to access
        }

        return new Tempcode();
    }

    /**
     * UI for showing the issue tracker.
     *
     * @return Tempcode The result of execution
     */
    public function tracker() : object
    {
        if (!addon_installed('cms_homesite_tracker')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('cms_homesite_tracker')));
        }

        require_lang('tracker');

        $GLOBALS['SITE_INFO']['block_url_schemes'] = '1';

        $content = paragraph(do_lang_tempcode('MANTIS_TRACKER_PAGE_TEXT'));
        $content->attach(do_block('main_mantis_tracker'));

        // Display
        return do_template('STANDALONE_HTML_WRAP', ['_GUID' => '11f8625ed8fc9cf4fa6f466f08b7be57', 'FRAME' => true, 'NOINDEX' => true, 'TARGET' => '_blank', 'TITLE' => 'Tracker', 'CONTENT' => $content]);
    }
}
