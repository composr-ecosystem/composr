<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite_support_credits
 */

/**
 * Module page class.
 */
class Module_admin_customers
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        /* NB: Does not delete CPFs and multi-mods. But that doesn't actually matter */
        delete_config_option('support_credit_value');
        delete_config_option('support_priority_backburner_minutes');
        delete_config_option('support_priority_regular_minutes');
        $GLOBALS['SITE_DB']->drop_table_if_exists('credit_purchases');
        $GLOBALS['SITE_DB']->drop_table_if_exists('credit_charge_log');

        if (substr(get_db_type(), 0, 5) != 'mysql') {
            return;
        }

        // MANTIS TABLE DELETION

        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_api_token_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_bug_file_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_bug_history_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_bug_monitor_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_bug_relationship_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_bug_revision_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_bug_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_bug_tag_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_bug_text_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_bugnote_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_bugnote_text_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_category_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_config_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_custom_field_project_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_custom_field_string_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_custom_field_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_email_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_filters_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_news_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_plugin_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_project_file_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_project_hierarchy_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_project_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_project_user_list_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_project_version_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_sponsorship_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_tag_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_tokens_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_user_pref_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_user_print_pref_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_user_profile_table`");
        $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS `mantis_user_table`");
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install($upgrade_from = null, $upgrade_from_hack = null)
    {
        if (get_forum_type() != 'cns') {
            return; // Conversr only
        }

        require_lang('customers');

        // CPFs...

        require_code('cns_members_action');
        require_code('cns_members_action2');
        require_code('mantis');
        $cur_id = null;
        $cur_id = get_credits_profile_field_id('cms_currency');
        if (!is_null($cur_id)) {
            $GLOBALS['FORUM_DB']->query_update('f_custom_fields', array('cf_owner_view' => 1, 'cf_owner_set' => 1), array('id' => $cur_id), '', 1);
        }
        cns_make_custom_field('cms_support_credits', 1, '', '', 0, 1, 0, 0, 'integer', 0, 0, 0, null, '', 0, '', true);
        cns_make_custom_field('cms_ftp_host', 0, do_lang('ENCRYPTED_TO_WEBSITE'), '', 0, 1, 1, 1, 'short_text', 0, 0, 0, null, '', 0, '', true);
        cns_make_custom_field('cms_ftp_path', 0, do_lang('ENCRYPTED_TO_WEBSITE'), '', 0, 1, 1, 1, 'short_text', 0, 0, 0, null, '', 0, '', true);
        cns_make_custom_field('cms_ftp_username', 0, do_lang('ENCRYPTED_TO_WEBSITE'), '', 0, 1, 1, 1, 'short_text', 0, 0, 0, null, '', 0, '', true);
        cns_make_custom_field('cms_ftp_password', 0, do_lang('ENCRYPTED_TO_WEBSITE'), '', 0, 1, 1, 1, 'short_text', 0, 0, 0, null, '', 0, '', true);
        cns_make_custom_field('cms_profession', 0, '', do_lang('CUSTOMER_PROFESSION_CPF_LIST'), 0, 1, 1, 0, 'list', 0, 0, 0, null, '', 0, '', true);

        // Credit logging...

        $GLOBALS['SITE_DB']->create_table('credit_purchases', array(
            'purchase_id' => '*AUTO',
            'member_id' => 'MEMBER',
            'num_credits' => 'INTEGER',
            'date_and_time' => 'TIME',
            'purchase_validated' => 'BINARY',
            'is_manual' => 'BINARY'
        ));

        $GLOBALS['SITE_DB']->create_table('credit_charge_log', array(
            'id' => '*AUTO',
            'member_id' => 'MEMBER',
            'charging_member_id' => 'MEMBER',
            'num_credits' => 'INTEGER',
            'date_and_time' => 'TIME',
            'reason' => 'SHORT_TEXT',
        ));

        // Tracker...

        if (substr(get_db_type(), 0, 5) == 'mysql') {
            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_api_token_table` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                `name` varchar(128) NOT NULL,
                `hash` varchar(128) NOT NULL,
                `date_created` int(10) unsigned NOT NULL DEFAULT '1',
                `date_used` int(10) unsigned NOT NULL DEFAULT '1',
                PRIMARY KEY (`id`),
                UNIQUE KEY `idx_user_id_name` (`user_id`,`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_bug_file_table` (
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
                PRIMARY KEY (`id`),
                KEY `idx_bug_file_bug_id` (`bug_id`),
                KEY `idx_diskfile` (`diskfile`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_bug_history_table` (
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
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_bug_monitor_table` (
                `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                `bug_id` int(10) unsigned NOT NULL DEFAULT '0',
                PRIMARY KEY (`user_id`,`bug_id`),
                KEY `idx_bug_id` (`bug_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_bug_relationship_table` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `source_bug_id` int(10) unsigned NOT NULL DEFAULT '0',
                `destination_bug_id` int(10) unsigned NOT NULL DEFAULT '0',
                `relationship_type` smallint(6) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `idx_relationship_source` (`source_bug_id`),
                KEY `idx_relationship_destination` (`destination_bug_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_bug_revision_table` (
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
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_bug_table` (
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
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_bug_tag_table` (
                `bug_id` int(10) unsigned NOT NULL DEFAULT '0',
                `tag_id` int(10) unsigned NOT NULL DEFAULT '0',
                `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                `date_attached` int(10) unsigned NOT NULL DEFAULT '1',
                PRIMARY KEY (`bug_id`,`tag_id`),
                KEY `idx_bug_tag_tag_id` (`tag_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_bug_text_table` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `description` longtext NOT NULL,
                `steps_to_reproduce` longtext NOT NULL,
                `additional_information` longtext NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
                ");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_bugnote_table` (
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
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_bugnote_text_table` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `note` longtext NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_category_table` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `project_id` int(10) unsigned NOT NULL DEFAULT '0',
                `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                `name` varchar(128) NOT NULL DEFAULT '',
                `status` int(10) unsigned NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                UNIQUE KEY `idx_category_project_name` (`project_id`,`name`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("INSERT INTO `mantis_category_table` values('1','0','0','General','0')");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_config_table` (
                `config_id` varchar(64) NOT NULL,
                `project_id` int(11) NOT NULL DEFAULT '0',
                `user_id` int(11) NOT NULL DEFAULT '0',
                `access_reqd` int(11) DEFAULT '0',
                `type` int(11) DEFAULT '90',
                `value` longtext NOT NULL,
                PRIMARY KEY (`config_id`,`project_id`,`user_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("INSERT INTO `mantis_config_table` values('database_version','0','0','90','1','209')");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_custom_field_project_table` (
                `field_id` int(11) NOT NULL DEFAULT '0',
                `project_id` int(10) unsigned NOT NULL DEFAULT '0',
                `sequence` smallint(6) NOT NULL DEFAULT '0',
                PRIMARY KEY (`field_id`,`project_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("INSERT INTO `mantis_custom_field_project_table` values('1','1','0'),('2','1','0')");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_custom_field_string_table` (
                `field_id` int(11) NOT NULL DEFAULT '0',
                `bug_id` int(11) NOT NULL DEFAULT '0',
                `value` varchar(255) NOT NULL DEFAULT '',
                `text` longtext,
                PRIMARY KEY (`field_id`,`bug_id`),
                KEY `idx_custom_field_bug` (`bug_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_custom_field_table` (
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
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("INSERT INTO `mantis_custom_field_table` values('1','Time estimation (hours)','2','','0','','10','10','0','0','0','0','1','1','0','1','1','0','1'),('2','Sponsorship open','5','Open','0','','10','55','0','0','0','0','1','1','0','1','1','0','1')");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_email_table` (
                `email_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `email` varchar(64) NOT NULL DEFAULT '',
                `subject` varchar(250) NOT NULL DEFAULT '',
                `metadata` longtext NOT NULL,
                `body` longtext NOT NULL,
                `submitted` int(10) unsigned NOT NULL DEFAULT '1',
                PRIMARY KEY (`email_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_filters_table` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL DEFAULT '0',
                `project_id` int(11) NOT NULL DEFAULT '0',
                `is_public` tinyint(4) DEFAULT NULL,
                `name` varchar(64) NOT NULL DEFAULT '',
                `filter_string` longtext NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_news_table` (
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
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_plugin_table` (
                `basename` varchar(40) NOT NULL,
                `enabled` tinyint(4) NOT NULL DEFAULT '0',
                `protected` tinyint(4) NOT NULL DEFAULT '0',
                `priority` int(10) unsigned NOT NULL DEFAULT '3',
                PRIMARY KEY (`basename`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("INSERT INTO `mantis_plugin_table` values('MantisCoreFormatting','1','0','3')");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_project_file_table` (
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
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_project_hierarchy_table` (
                `child_id` int(10) unsigned NOT NULL,
                `parent_id` int(10) unsigned NOT NULL,
                `inherit_parent` tinyint(4) NOT NULL DEFAULT '0',
                UNIQUE KEY `idx_project_hierarchy` (`child_id`,`parent_id`),
                KEY `idx_project_hierarchy_child_id` (`child_id`),
                KEY `idx_project_hierarchy_parent_id` (`parent_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_project_table` (
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
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("INSERT INTO `mantis_project_table` values('1','All Projects','10','1','10','10','','','1','1')");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_project_user_list_table` (
                `project_id` int(10) unsigned NOT NULL DEFAULT '0',
                `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                `access_level` smallint(6) NOT NULL DEFAULT '10',
                PRIMARY KEY (`project_id`,`user_id`),
                KEY `idx_project_user` (`user_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_project_version_table` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `project_id` int(10) unsigned NOT NULL DEFAULT '0',
                `version` varchar(64) NOT NULL DEFAULT '',
                `description` longtext NOT NULL,
                `released` tinyint(4) NOT NULL DEFAULT '1',
                `obsolete` tinyint(4) NOT NULL DEFAULT '0',
                `date_order` int(10) unsigned NOT NULL DEFAULT '1',
                PRIMARY KEY (`id`),
                UNIQUE KEY `idx_project_version` (`project_id`,`version`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_sponsorship_table` (
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
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_tag_table` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                `name` varchar(100) NOT NULL DEFAULT '',
                `description` longtext NOT NULL,
                `date_created` int(10) unsigned NOT NULL DEFAULT '1',
                `date_updated` int(10) unsigned NOT NULL DEFAULT '1',
                PRIMARY KEY (`id`,`name`),
                KEY `idx_tag_name` (`name`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_tokens_table` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `owner` int(11) NOT NULL,
                `type` int(11) NOT NULL,
                `value` longtext NOT NULL,
                `timestamp` int(10) unsigned NOT NULL DEFAULT '1',
                `expiry` int(10) unsigned NOT NULL DEFAULT '1',
                PRIMARY KEY (`id`),
                KEY `idx_typeowner` (`type`,`owner`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_user_pref_table` (
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
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_user_print_pref_table` (
                `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                `print_pref` varchar(64) NOT NULL,
                PRIMARY KEY (`user_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_user_profile_table` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` int(10) unsigned NOT NULL DEFAULT '0',
                `platform` varchar(32) NOT NULL DEFAULT '',
                `os` varchar(32) NOT NULL DEFAULT '',
                `os_build` varchar(32) NOT NULL DEFAULT '',
                `description` longtext NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");

            $GLOBALS['SITE_DB']->query("CREATE TABLE `mantis_user_table` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `username` varchar(191) NOT NULL DEFAULT '',
                `realname` varchar(191) NOT NULL DEFAULT '',
                `email` varchar(191) NOT NULL DEFAULT '',
                `password` varchar(64) NOT NULL DEFAULT '',
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
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8");
        }

        // Multi-moderations...

        require_code('cns_moderation_action');
        cns_make_multi_moderation(do_lang('TICKET_MM_TAKE_OWNERSHIP'), do_lang('TICKET_MM_TAKE_OWNERSHIP_POST'), null, null, null, null, '*');
        cns_make_multi_moderation(do_lang('TICKET_MM_QUOTE'), do_lang('TICKET_MM_QUOTE_POST'), null, null, null, null, '*');
        cns_make_multi_moderation(do_lang('TICKET_MM_PRICE'), do_lang('TICKET_MM_PRICE_POST'), null, null, null, null, '*');
        cns_make_multi_moderation(do_lang('TICKET_MM_CLOSE'), do_lang('TICKET_MM_CLOSE_POST'), null, null, null, null, '*');
        cns_make_multi_moderation(do_lang('TICKET_MM_CHARGED'), do_lang('TICKET_MM_CHARGED_POST'), null, null, null, null, '*');
        cns_make_multi_moderation(do_lang('TICKET_MM_NOT_FOR_FREE'), do_lang('TICKET_MM_NOT_FOR_FREE_POST'), null, null, null, null, '*');
        cns_make_multi_moderation(do_lang('TICKET_MM_FREE_WORK'), do_lang('TICKET_MM_FREE_WORK_POST'), null, null, null, null, '*');
        cns_make_multi_moderation(do_lang('TICKET_MM_FREE_CREDITS'), do_lang('TICKET_MM_FREE_CREDITS_POST'), null, null, null, null, '*');
    }

    /**
     * Find entry-points available within this module.
     *
     * @param  boolean $check_perms Whether to check permissions.
     * @param  ?MEMBER $member_id The member to check permissions as (null: current user).
     * @param  boolean $support_crosslinks Whether to allow cross links to other modules (identifiable via a full-page-link rather than a screen-name).
     * @param  boolean $be_deferential Whether to avoid any entry-point (or even return null to disable the page in the Sitemap) if we know another module, or page_group, is going to link to that entry-point. Note that "!" and "browse" entry points are automatically merged with container page nodes (likely called by page-groupings) as appropriate.
     * @return ?array A map of entry points (screen-name=>language-code/string or screen-name=>[language-code/string, icon-theme-image]) (null: disabled).
     */
    public function get_entry_points($check_perms = true, $member_id = null, $support_crosslinks = true, $be_deferential = false)
    {
        return array(
            'browse' => array('CHARGE_CUSTOMER', 'menu/_generic_admin/tool'),
        );
    }

    public $title;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none).
     */
    public function pre_run()
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $type = get_param_string('type', 'browse');

        require_lang('customers');
        require_lang('points');

        $this->title = get_screen_title('CHARGE_CUSTOMER');

        return null;
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution.
     */
    public function run()
    {
        $type = get_param_string('type', 'browse');

        if ($type == 'charge') {
            return $this->charge();
        }
        if ($type == '_charge') {
            return $this->_charge();
        }
        if ($type == 'browse') {
            return $this->charge();
        }

        return new Tempcode();
    }

    /**
     * The UI to charge a customer.
     *
     * @return Tempcode The UI
     */
    public function charge()
    {
        require_code('form_templates');
        require_code('mantis');

        $post_url = build_url(array('page' => '_SELF', 'type' => '_charge'), '_SELF');
        $submit_name = do_lang_tempcode('CHARGE');

        $username = get_param_string('username', null);
        if (is_null($username)) {
            $member_id = get_param_integer('member_id', null);
            if (!is_null($member_id)) {
                $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id);
            } else {
                $username = '';
            }
        } else {
            $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($username);
        }

        $fields = new Tempcode();
        $fields->attach(form_input_username(do_lang_tempcode('USERNAME'), '', 'member_username', $username, true));
        $fields->attach(form_input_integer(do_lang_tempcode('AMOUNT'), do_lang_tempcode('CREDIT_AMOUNT_DESCRIPTION'), 'amount', get_param_integer('amount', 3), true));
        $fields->attach(form_input_tick(do_lang_tempcode('ALLOW_OVERDRAFT'), do_lang_tempcode('DESCRIPTION_ALLOW_OVERDRAFT'), 'allow_overdraft', true));
        $fields->attach(form_input_line(do_lang_tempcode('REASON'), 'If for a ticket, you can just paste in the ticket URL.', 'reason', '', true));

        if (!is_null($member_id)) {
            $cpf_id = get_credits_profile_field_id();
            if (is_null($cpf_id)) {
                $msg_tpl = warn_screen($this->title, do_lang_tempcode('INVALID_FIELD_ID'));
                return $msg_tpl;
            }
            $num_credits = 0;
            if (!is_null($cpf_id)) {
                require_code('cns_members_action2');
                $_fields = cns_get_custom_field_mappings($member_id);
                $num_credits = $_fields['field_' . strval($cpf_id)];
            }

            $text = paragraph(do_lang_tempcode('CUSTOMER_CURRENTLY_HAS', escape_html(number_format($num_credits))));
        } else {
            $text = new Tempcode();
        }

        require_code('templates_columned_table');
        $rows = new Tempcode();
        $logs = $GLOBALS['SITE_DB']->query_select('credit_charge_log', array('charging_member_id', 'num_credits', 'date_and_time', 'reason'), array('member_id' => $member_id), 'ORDER BY date_and_time DESC', 10);
        foreach ($logs as $log) {
            $charging_username = $GLOBALS['FORUM_DRIVER']->get_username($log['charging_member_id']);
            if (is_null($charging_username)) {
                $charging_username = do_lang('DELETED');
            }
            $_num_credits = integer_format($log['num_credits']);
            $date_and_time = get_timezoned_date($log['date_and_time']);
            $reason = $log['reason'];
            $rows->attach(columned_table_row(array($charging_username, $_num_credits, $date_and_time, $reason), true));
        }
        if (!$rows->is_empty()) {
            $_header_row = array(
                do_lang_tempcode('USERNAME'),
                do_lang_tempcode('AMOUNT'),
                do_lang_tempcode('DATE_TIME'),
                do_lang_tempcode('REASON'),
            );
            $header_row = columned_table_header_row($_header_row);
            $text->attach(do_template('COLUMNED_TABLE', array('_GUID' => '032e4dcb1d4224ed6633679154b6d827', 'HEADER_ROW' => $header_row, 'ROWS' => $rows)));
        }

        return do_template('FORM_SCREEN', array('_GUID' => 'f91185ee725f47ffa652d5fef8d85c0b', 'TITLE' => $this->title, 'HIDDEN' => '', 'TEXT' => $text, 'FIELDS' => $fields, 'SUBMIT_ICON' => 'buttons__proceed', 'SUBMIT_NAME' => $submit_name, 'URL' => $post_url));
    }

    /**
     * The actualiser to charge a customer.
     *
     * @return Tempcode The UI
     */
    public function _charge()
    {
        $username = post_param_string('member_username');
        $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($username);
        if (($member_id === null) || (is_guest($member_id))) {
            warn_exit(do_lang_tempcode('_MEMBER_NO_EXIST', escape_html($username)));
        }
        $amount = post_param_integer('amount');

        require_code('mantis');
        $cpf_id = get_credits_profile_field_id();
        if (is_null($cpf_id)) {
            $msg_tpl = warn_screen($this->title, do_lang_tempcode('INVALID_FIELD_ID'));
            return $msg_tpl;
        }

        // Increment the number of credits this customer has
        require_code('cns_members_action2');
        $fields = cns_get_custom_field_mappings($member_id);

        // Work out new total credits
        $new_amount = $fields['field_' . strval($cpf_id)] - $amount;
        if (post_param_integer('allow_overdraft', 0) == 0) {
            if ($new_amount < 0) {
                $new_amount = 0;
                $amount = $fields['field_' . strval($cpf_id)] - $new_amount;
            }
        }

        cns_set_custom_field($member_id, $cpf_id, strval($new_amount));

        $GLOBALS['SITE_DB']->query_insert('credit_charge_log', array(
            'member_id' => $member_id,
            'charging_member_id' => get_member(),
            'num_credits' => $amount,
            'date_and_time' => time(),
            'reason' => post_param_string('reason', ''),
        ));

        // Show it worked / Refresh
        $url = build_url(array('page' => '_SELF', 'type' => 'browse', 'username' => $username), '_SELF');
        return redirect_screen($this->title, $url, do_lang_tempcode('SUCCESS'));
    }
}
