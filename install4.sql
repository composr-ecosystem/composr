DROP TABLE IF EXISTS cms_member_privileges;
CREATE TABLE cms_member_privileges (
    category_name varchar(80) NOT NULL,
    module_the_name varchar(80) NOT NULL,
    the_page varchar(80) NOT NULL,
    active_until integer unsigned NULL,
    privilege varchar(80) NOT NULL,
    member_id integer NOT NULL,
    the_value tinyint(1) NOT NULL,
    PRIMARY KEY (category_name, module_the_name, the_page, privilege, member_id)
) CHARACTER SET=utf8 engine=MyISAM;
ALTER TABLE cms_member_privileges ADD INDEX active_until (active_until);

ALTER TABLE cms_member_privileges ADD INDEX member_privileges_member (member_id);

ALTER TABLE cms_member_privileges ADD INDEX member_privileges_name (privilege,the_page,module_the_name,category_name);
DROP TABLE IF EXISTS cms_member_tracking;
CREATE TABLE cms_member_tracking (
    mt_member_id integer NOT NULL,
    mt_cache_username varchar(80) NOT NULL,
    mt_time integer unsigned NOT NULL,
    mt_id varchar(80) NOT NULL,
    mt_type varchar(80) NOT NULL,
    mt_page varchar(80) NOT NULL,
    PRIMARY KEY (mt_member_id, mt_time, mt_id, mt_type, mt_page)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_member_tracking ADD INDEX mt_id (mt_page,mt_id,mt_type);

ALTER TABLE cms_member_tracking ADD INDEX mt_page (mt_page);

ALTER TABLE cms_member_tracking ADD INDEX mt_time (mt_time);
DROP TABLE IF EXISTS cms_member_zone_access;
CREATE TABLE cms_member_zone_access (
    active_until integer unsigned NULL,
    member_id integer NOT NULL,
    zone_name varchar(80) NOT NULL,
    PRIMARY KEY (member_id, zone_name)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_member_zone_access ADD INDEX active_until (active_until);

ALTER TABLE cms_member_zone_access ADD INDEX mzamember_id (member_id);

ALTER TABLE cms_member_zone_access ADD INDEX mzazone_name (zone_name);
DROP TABLE IF EXISTS cms_menu_items;
CREATE TABLE cms_menu_items (
    i_page_only varchar(80) NOT NULL,
    i_include_sitemap tinyint NOT NULL,
    i_new_window tinyint(1) NOT NULL,
    i_expanded tinyint(1) NOT NULL,
    i_check_permissions tinyint(1) NOT NULL,
    i_order integer NOT NULL,
    i_menu varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    i_caption longtext NOT NULL,
    i_caption_long longtext NOT NULL,
    i_theme_img_code varchar(80) NOT NULL,
    i_link varchar(255) NOT NULL,
    i_parent_id integer NULL,
    i_caption__text_parsed longtext NOT NULL,
    i_caption__source_user integer DEFAULT 1 NOT NULL,
    i_caption_long__text_parsed longtext NOT NULL,
    i_caption_long__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_menu_items ADD FULLTEXT i_caption (i_caption);

ALTER TABLE cms_menu_items ADD FULLTEXT i_caption_long (i_caption_long);

ALTER TABLE cms_menu_items ADD INDEX menu_extraction (i_menu);
DROP TABLE IF EXISTS cms_messages_to_render;
CREATE TABLE cms_messages_to_render (
    r_type varchar(80) NOT NULL,
    r_message longtext NOT NULL,
    r_session_id varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    r_time integer unsigned NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_messages_to_render ADD INDEX delete_old (r_time);

ALTER TABLE cms_messages_to_render ADD INDEX forsession (r_session_id);
DROP TABLE IF EXISTS cms_modules;
CREATE TABLE cms_modules (
    module_the_name varchar(80) NOT NULL,
    module_hacked_by varchar(80) NOT NULL,
    module_version integer NOT NULL,
    module_hack_version integer NULL,
    module_organisation varchar(80) NOT NULL,
    module_author varchar(80) NOT NULL,
    PRIMARY KEY (module_the_name)
) CHARACTER SET=utf8mb4 engine=MyISAM;
INSERT INTO cms_modules (module_the_name, module_author, module_organisation, module_hacked_by, module_hack_version, module_version) VALUES ('admin_permissions', 'Chris Graham', 'Composr', '', NULL, 10),
('admin_version', 'Chris Graham', 'Composr', '', NULL, 20),
('admin', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_actionlog', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_addons', 'Chris Graham', 'Composr', '', NULL, 5),
('admin_aggregate_types', 'Chris Graham', 'Composr', '', NULL, 1),
('admin_awards', 'Chris Graham', 'Composr', '', NULL, 6),
('admin_backup', 'Chris Graham', 'Composr', '', NULL, 3),
('admin_banners', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_broken_urls', 'Chris Graham', 'Composr', '', NULL, 1),
('admin_chat', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_cleanup', 'Chris Graham', 'Composr', '', NULL, 3),
('admin_cns_customprofilefields', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_cns_emoticons', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_cns_forum_groupings', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_cns_forums', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_cns_groups', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_cns_ldap', 'Chris Graham', 'Composr', '', NULL, 4),
('admin_cns_members', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_cns_merge_members', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_cns_multi_moderations', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_cns_post_templates', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_cns_welcome_emails', 'Chris Graham', 'Composr', '', NULL, 5),
('admin_commandr', 'Philip Withnall', 'Composr', '', NULL, 4),
('admin_config', 'Chris Graham', 'Composr', '', NULL, 15),
('admin_content_reviews', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_custom_comcode', 'Chris Graham', 'Composr', '', NULL, 3),
('admin_debrand', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_ecommerce', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_ecommerce_reports', 'Chris Graham', 'Composr', '', NULL, 1),
('admin_email_log', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_errorlog', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_group_member_timeouts', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_health_check', 'Chris Graham', 'Composr', '', NULL, 1),
('admin_import', 'Chris Graham', 'Composr', '', NULL, 8),
('admin_invoices', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_ip_ban', 'Chris Graham', 'Composr', '', NULL, 5),
('admin_lang', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_leader_board', 'Patrick Schmalstig', 'Composr', '', NULL, 1),
('admin_lookup', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_menus', 'Chris Graham', 'Composr', '', NULL, 2);
INSERT INTO cms_modules (module_the_name, module_author, module_organisation, module_hacked_by, module_hack_version, module_version) VALUES ('admin_newsletter', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_notifications', 'Chris Graham', 'Composr', '', NULL, 1),
('admin_oauth', 'Chris Graham', 'Composr', '', NULL, 1),
('admin_phpinfo', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_points', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_privacy', 'Chris Graham', 'Composr', '', NULL, 1),
('admin_quiz', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_realtime_rain', 'Chris Graham', 'Composr', '', NULL, 1),
('admin_redirects', 'Chris Graham', 'Composr', '', NULL, 4),
('admin_revisions', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_robots_txt', 'Chris Graham', 'Composr', '', NULL, 1),
('admin_security', 'Chris Graham', 'Composr', '', NULL, 5),
('admin_setupwizard', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_shopping', 'Manuprathap', 'Composr', '', NULL, 2),
('admin_site_messaging', 'Patrick Schmalstig', 'PDStig, LLC', '', NULL, 1),
('admin_sitemap', 'Chris Graham', 'Composr', '', NULL, 4),
('admin_stats', 'Chris Graham', 'Composr', '', NULL, 11),
('admin_svg_sprites', 'Salman Abbas', 'Composr', '', NULL, 1),
('admin_themes', 'Chris Graham', 'Composr', '', NULL, 6),
('admin_themewizard', 'Allen Ellis', 'Composr', '', NULL, 2),
('admin_tickets', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_trackbacks', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_validation', 'Chris Graham', 'Composr', '', NULL, 2),
('admin_wordfilter', 'Chris Graham', 'Composr', '', NULL, 5),
('admin_zones', 'Chris Graham', 'Composr', '', NULL, 2),
('authors', 'Chris Graham', 'Composr', '', NULL, 5),
('awards', 'Chris Graham', 'Composr', '', NULL, 2),
('banners', 'Chris Graham', 'Composr', '', NULL, 8),
('calendar', 'Chris Graham', 'Composr', '', NULL, 10),
('catalogues', 'Chris Graham', 'Composr', '', NULL, 12),
('chat', 'Philip Withnall', 'Composr', '', NULL, 14),
('contact_member', 'Chris Graham', 'Composr', '', NULL, 2),
('downloads', 'Chris Graham', 'Composr', '', NULL, 10),
('forums', 'Chris Graham', 'Composr', '', NULL, 2),
('galleries', 'Chris Graham', 'Composr', '', NULL, 11),
('groups', 'Chris Graham', 'Composr', '', NULL, 2),
('invoices', 'Chris Graham', 'Composr', '', NULL, 3),
('join', 'Chris Graham', 'Composr', '', NULL, 3),
('leader_board', 'Patrick Schmalstig', 'Composr', '', NULL, 2),
('login', 'Chris Graham', 'Composr', '', NULL, 3);
INSERT INTO cms_modules (module_the_name, module_author, module_organisation, module_hacked_by, module_hack_version, module_version) VALUES ('lost_password', 'Chris Graham', 'Composr', '', NULL, 2),
('mail', 'Patrick Schmalstig', 'Composr', '', NULL, 1),
('members', 'Chris Graham', 'Composr', '', NULL, 3),
('news', 'Chris Graham', 'Composr', '', NULL, 9),
('newsletter', 'Chris Graham', 'Composr', '', NULL, 14),
('notifications', 'Chris Graham', 'Composr', '', NULL, 1),
('points', 'Chris Graham', 'Composr', '', NULL, 11),
('polls', 'Chris Graham', 'Composr', '', NULL, 8),
('purchase', 'Chris Graham', 'Composr', '', NULL, 8),
('quiz', 'Chris Graham', 'Composr', '', NULL, 8),
('recommend', 'Chris Graham', 'Composr', '', NULL, 6),
('report_content', 'Chris Graham / Patrick Schmalstig', 'Composr', '', NULL, 4),
('search', 'Chris Graham', 'Composr', '', NULL, 6),
('shopping', 'Manuprathap', 'Composr', '', NULL, 9),
('subscriptions', 'Chris Graham', 'Composr', '', NULL, 6),
('tickets', 'Chris Graham', 'Composr', '', NULL, 7),
('users_online', 'Chris Graham', 'Composr', '', NULL, 2),
('warnings', 'Chris Graham', 'Composr', '', NULL, 2),
('wiki', 'Chris Graham', 'Composr', '', NULL, 10);
INSERT INTO cms_modules (module_the_name, module_author, module_organisation, module_hacked_by, module_hack_version, module_version) VALUES ('forumview', 'Chris Graham', 'Composr', '', NULL, 2),
('topics', 'Chris Graham', 'Composr', '', NULL, 2),
('topicview', 'Chris Graham', 'Composr', '', NULL, 2),
('vforums', 'Chris Graham', 'Composr', '', NULL, 2),
('cms', 'Chris Graham', 'Composr', '', NULL, 2),
('cms_authors', 'Chris Graham', 'Composr', '', NULL, 3),
('cms_banners', 'Chris Graham', 'Composr', '', NULL, 2),
('cms_blogs', 'Chris Graham', 'Composr', '', NULL, 2),
('cms_calendar', 'Chris Graham', 'Composr', '', NULL, 2),
('cms_catalogues', 'Chris Graham', 'Composr', '', NULL, 2),
('cms_chat', 'Philip Withnall', 'Composr', '', NULL, 3),
('cms_cns_groups', 'Chris Graham', 'Composr', '', NULL, 2),
('cms_comcode_pages', 'Chris Graham', 'Composr', '', NULL, 4),
('cms_downloads', 'Chris Graham', 'Composr', '', NULL, 2),
('cms_galleries', 'Chris Graham', 'Composr', '', NULL, 2),
('cms_news', 'Chris Graham', 'Composr', '', NULL, 2),
('cms_polls', 'Chris Graham', 'Composr', '', NULL, 2),
('cms_quiz', 'Chris Graham', 'Composr', '', NULL, 2),
('cms_wiki', 'Chris Graham', 'Composr', '', NULL, 4),
('filedump', 'Chris Graham', 'Composr', '', NULL, 5);

DROP TABLE IF EXISTS cms_news;
CREATE TABLE cms_news (
    submitter integer NOT NULL,
    validated tinyint(1) NOT NULL,
    edit_date integer unsigned NULL,
    news_category integer NOT NULL,
    news_views integer NOT NULL,
    news_image_url varchar(255) BINARY NOT NULL,
    date_and_time integer unsigned NOT NULL,
    allow_trackbacks tinyint(1) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    notes longtext NOT NULL,
    title longtext NOT NULL,
    news longtext NOT NULL,
    news_article longtext NOT NULL,
    allow_rating tinyint(1) NOT NULL,
    author varchar(80) NOT NULL,
    allow_comments tinyint NOT NULL,
    title__text_parsed longtext NOT NULL,
    title__source_user integer DEFAULT 1 NOT NULL,
    news__text_parsed longtext NOT NULL,
    news__source_user integer DEFAULT 1 NOT NULL,
    news_article__text_parsed longtext NOT NULL,
    news_article__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_news ADD FULLTEXT news (news);

ALTER TABLE cms_news ADD FULLTEXT news_article (news_article);

ALTER TABLE cms_news ADD FULLTEXT news_search__combined (title,news,news_article);

ALTER TABLE cms_news ADD FULLTEXT title (title);

ALTER TABLE cms_news ADD INDEX findnewscat (news_category);

ALTER TABLE cms_news ADD INDEX ftjoin_ititle (title(250));

ALTER TABLE cms_news ADD INDEX ftjoin_nnews (news(250));

ALTER TABLE cms_news ADD INDEX ftjoin_nnewsa (news_article(250));

ALTER TABLE cms_news ADD INDEX headlines (date_and_time,id);

ALTER TABLE cms_news ADD INDEX nes (submitter);

ALTER TABLE cms_news ADD INDEX news_views (news_views);

ALTER TABLE cms_news ADD INDEX newsauthor (author);

ALTER TABLE cms_news ADD INDEX nvalidated (validated);
DROP TABLE IF EXISTS cms_news_categories;
CREATE TABLE cms_news_categories (
    id integer unsigned auto_increment NOT NULL,
    nc_title longtext NOT NULL,
    nc_owner integer NULL,
    nc_img varchar(255) NOT NULL,
    notes longtext NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_news_categories ADD FULLTEXT nc_title (nc_title);

ALTER TABLE cms_news_categories ADD INDEX ncs (nc_owner);
INSERT INTO cms_news_categories (id, nc_title, nc_owner, nc_img, notes) VALUES (1, 'General', NULL, 'icons/news/general', ''),
(2, 'Technology', NULL, 'icons/news/technology', ''),
(3, 'Difficulties', NULL, 'icons/news/difficulties', ''),
(4, 'Community', NULL, 'icons/news/community', ''),
(5, 'Entertainment', NULL, 'icons/news/entertainment', ''),
(6, 'Business', NULL, 'icons/news/business', ''),
(7, 'Art', NULL, 'icons/news/art', '');

DROP TABLE IF EXISTS cms_news_category_entries;
CREATE TABLE cms_news_category_entries (
    news_entry integer NOT NULL,
    news_entry_category integer NOT NULL,
    PRIMARY KEY (news_entry, news_entry_category)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_news_category_entries ADD INDEX news_entry_category (news_entry_category);
DROP TABLE IF EXISTS cms_news_rss_cloud;
CREATE TABLE cms_news_rss_cloud (
    rem_procedure varchar(80) NOT NULL,
    rem_port integer NOT NULL,
    rem_path varchar(255) NOT NULL,
    rem_protocol varchar(80) NOT NULL,
    rem_ip_address varchar(40) NOT NULL,
    watch_channel_url varchar(255) BINARY NOT NULL,
    register_time integer unsigned NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
DROP TABLE IF EXISTS cms_newsletter_archive;
CREATE TABLE cms_newsletter_archive (
    language varchar(80) NOT NULL,
    date_and_time integer unsigned NOT NULL,
    subject varchar(255) NOT NULL,
    newsletter longtext NOT NULL,
    html_only tinyint(1) NOT NULL,
    from_email varchar(255) NOT NULL,
    from_name varchar(255) NOT NULL,
    priority integer NOT NULL,
    template varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
DROP TABLE IF EXISTS cms_newsletter_drip_send;
CREATE TABLE cms_newsletter_drip_send (
    d_to_email varchar(255) NOT NULL,
    d_inject_time integer unsigned NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    d_message_id integer NOT NULL,
    d_message_binding longtext NOT NULL,
    d_to_name varchar(255) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
DROP TABLE IF EXISTS cms_newsletter_periodic;
CREATE TABLE cms_newsletter_periodic (
    np_html_only tinyint(1) NOT NULL,
    np_from_name varchar(255) NOT NULL,
    np_priority tinyint NOT NULL,
    np_lang varchar(5) NOT NULL,
    np_send_details longtext NOT NULL,
    np_last_sent_time integer unsigned NOT NULL,
    np_template varchar(80) NOT NULL,
    np_message longtext NOT NULL,
    np_subject longtext NOT NULL,
    np_in_full tinyint(1) NOT NULL,
    np_day tinyint NOT NULL,
    np_from_email varchar(255) NOT NULL,
    np_frequency varchar(255) NOT NULL,
    np_spreadsheet_data longtext NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
DROP TABLE IF EXISTS cms_newsletter_subscribe;
CREATE TABLE cms_newsletter_subscribe (
    email varchar(255) NOT NULL,
    newsletter_id integer NOT NULL,
    PRIMARY KEY (email, newsletter_id)
) CHARACTER SET=utf8 engine=MyISAM;
DROP TABLE IF EXISTS cms_newsletter_subscribers;
CREATE TABLE cms_newsletter_subscribers (
    the_password varchar(255) NOT NULL,
    pass_salt varchar(255) NOT NULL,
    language varchar(80) NOT NULL,
    n_surname varchar(255) NOT NULL,
    n_forename varchar(255) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    email varchar(255) NOT NULL,
    join_time integer unsigned NOT NULL,
    code_confirm integer NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_newsletter_subscribers ADD INDEX code_confirm (code_confirm);

ALTER TABLE cms_newsletter_subscribers ADD INDEX email (email(250));

ALTER TABLE cms_newsletter_subscribers ADD INDEX welcomemails (join_time);
DROP TABLE IF EXISTS cms_newsletters;
CREATE TABLE cms_newsletters (
    id integer unsigned auto_increment NOT NULL,
    the_description longtext NOT NULL,
    title longtext NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_newsletters ADD FULLTEXT the_description (the_description);

ALTER TABLE cms_newsletters ADD FULLTEXT title (title);
INSERT INTO cms_newsletters (id, title, the_description) VALUES (1, 'General', 'General messages will be sent out in this newsletter.');

DROP TABLE IF EXISTS cms_notification_lockdown;
CREATE TABLE cms_notification_lockdown (
    l_setting integer NOT NULL,
    l_notification_code varchar(80) NOT NULL,
    PRIMARY KEY (l_notification_code)
) CHARACTER SET=utf8mb4 engine=MyISAM;
DROP TABLE IF EXISTS cms_notifications_enabled;
CREATE TABLE cms_notifications_enabled (
    l_setting integer NOT NULL,
    l_code_category varchar(255) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    l_member_id integer NOT NULL,
    l_notification_code varchar(80) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_notifications_enabled ADD INDEX l_code_category (l_code_category(250));

ALTER TABLE cms_notifications_enabled ADD INDEX l_member_id (l_member_id,l_notification_code,l_code_category(10));

ALTER TABLE cms_notifications_enabled ADD INDEX l_notification_code (l_notification_code);

ALTER TABLE cms_notifications_enabled ADD INDEX who_has (l_notification_code,l_code_category(10),l_setting);
DROP TABLE IF EXISTS cms_points_ledger;
CREATE TABLE cms_points_ledger (
    t_type varchar(80) NOT NULL,
    t_subtype varchar(80) NOT NULL,
    date_and_time integer unsigned NOT NULL,
    amount_gift_points integer NOT NULL,
    amount_points integer NOT NULL,
    sending_member integer NOT NULL,
    t_type_id varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    reason longtext NOT NULL,
    anonymous tinyint(1) NOT NULL,
    status integer NOT NULL,
    linked_ledger_id integer NULL,
    locked tinyint(1) NOT NULL,
    receiving_member integer NOT NULL,
    reason__text_parsed longtext NOT NULL,
    reason__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_points_ledger ADD FULLTEXT reason (reason);

ALTER TABLE cms_points_ledger ADD INDEX amount_gift_points (amount_gift_points);

ALTER TABLE cms_points_ledger ADD INDEX linked_ledger_id (linked_ledger_id);

ALTER TABLE cms_points_ledger ADD INDEX points (amount_points,amount_gift_points);

ALTER TABLE cms_points_ledger ADD INDEX receive_from (receiving_member,sending_member);

ALTER TABLE cms_points_ledger ADD INDEX send_to (sending_member,receiving_member);

ALTER TABLE cms_points_ledger ADD INDEX status (status);

ALTER TABLE cms_points_ledger ADD INDEX t_search_no_subtype (t_type,t_type_id);

ALTER TABLE cms_points_ledger ADD INDEX t_search_subtype (t_type,t_subtype,t_type_id);
DROP TABLE IF EXISTS cms_poll;
CREATE TABLE cms_poll (
    votes2 integer NOT NULL,
    votes3 integer NOT NULL,
    votes4 integer NOT NULL,
    votes5 integer NOT NULL,
    votes6 integer NOT NULL,
    votes7 integer NOT NULL,
    votes8 integer NOT NULL,
    votes9 integer NOT NULL,
    votes10 integer NOT NULL,
    allow_rating tinyint(1) NOT NULL,
    allow_comments tinyint NOT NULL,
    allow_trackbacks tinyint(1) NOT NULL,
    notes longtext NOT NULL,
    num_options tinyint NOT NULL,
    is_current tinyint(1) NOT NULL,
    date_and_time integer unsigned NULL,
    submitter integer NOT NULL,
    add_time integer unsigned NOT NULL,
    poll_views integer NOT NULL,
    edit_date integer unsigned NULL,
    option7 longtext NOT NULL,
    option6 longtext NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    question longtext NOT NULL,
    option1 longtext NOT NULL,
    option2 longtext NOT NULL,
    option3 longtext NOT NULL,
    option4 longtext NOT NULL,
    option5 longtext NOT NULL,
    option8 longtext NOT NULL,
    option9 longtext NOT NULL,
    option10 longtext NOT NULL,
    votes1 integer NOT NULL,
    option7__text_parsed longtext NOT NULL,
    option7__source_user integer DEFAULT 1 NOT NULL,
    option6__text_parsed longtext NOT NULL,
    option6__source_user integer DEFAULT 1 NOT NULL,
    question__text_parsed longtext NOT NULL,
    question__source_user integer DEFAULT 1 NOT NULL,
    option1__text_parsed longtext NOT NULL,
    option1__source_user integer DEFAULT 1 NOT NULL,
    option2__text_parsed longtext NOT NULL,
    option2__source_user integer DEFAULT 1 NOT NULL,
    option3__text_parsed longtext NOT NULL,
    option3__source_user integer DEFAULT 1 NOT NULL,
    option4__text_parsed longtext NOT NULL,
    option4__source_user integer DEFAULT 1 NOT NULL,
    option5__text_parsed longtext NOT NULL,
    option5__source_user integer DEFAULT 1 NOT NULL,
    option8__text_parsed longtext NOT NULL,
    option8__source_user integer DEFAULT 1 NOT NULL,
    option9__text_parsed longtext NOT NULL,
    option9__source_user integer DEFAULT 1 NOT NULL,
    option10__text_parsed longtext NOT NULL,
    option10__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_poll ADD FULLTEXT option1 (option1);

ALTER TABLE cms_poll ADD FULLTEXT option10 (option10);

ALTER TABLE cms_poll ADD FULLTEXT option2 (option2);

ALTER TABLE cms_poll ADD FULLTEXT option3 (option3);

ALTER TABLE cms_poll ADD FULLTEXT option4 (option4);

ALTER TABLE cms_poll ADD FULLTEXT option5 (option5);

ALTER TABLE cms_poll ADD FULLTEXT option6 (option6);

ALTER TABLE cms_poll ADD FULLTEXT option7 (option7);

ALTER TABLE cms_poll ADD FULLTEXT option8 (option8);

ALTER TABLE cms_poll ADD FULLTEXT option9 (option9);

ALTER TABLE cms_poll ADD FULLTEXT poll_search__combined (question,option1,option2,option3,option4,option5);

ALTER TABLE cms_poll ADD FULLTEXT question (question);

ALTER TABLE cms_poll ADD INDEX date_and_time (date_and_time);

ALTER TABLE cms_poll ADD INDEX ftjoin_po1 (option1(250));

ALTER TABLE cms_poll ADD INDEX ftjoin_po2 (option2(250));

ALTER TABLE cms_poll ADD INDEX ftjoin_po3 (option3(250));

ALTER TABLE cms_poll ADD INDEX ftjoin_po4 (option4(250));

ALTER TABLE cms_poll ADD INDEX ftjoin_po5 (option5(250));

ALTER TABLE cms_poll ADD INDEX ftjoin_pq (question(250));

ALTER TABLE cms_poll ADD INDEX get_current (is_current);

ALTER TABLE cms_poll ADD INDEX padd_time (add_time);

ALTER TABLE cms_poll ADD INDEX poll_views (poll_views);

ALTER TABLE cms_poll ADD INDEX ps (submitter);
DROP TABLE IF EXISTS cms_poll_votes;
CREATE TABLE cms_poll_votes (
    v_voting_member integer NULL,
    id integer unsigned auto_increment NOT NULL,
    v_vote_time integer unsigned NOT NULL,
    v_voting_ip_address varchar(40) NOT NULL,
    v_vote_for tinyint NULL,
    v_poll_id integer NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_poll_votes ADD INDEX v_vote_for (v_vote_for);

ALTER TABLE cms_poll_votes ADD INDEX v_voting_ip_address (v_voting_ip_address);

ALTER TABLE cms_poll_votes ADD INDEX v_voting_member (v_voting_member);
DROP TABLE IF EXISTS cms_post_tokens;
CREATE TABLE cms_post_tokens (
    token varchar(80) NOT NULL,
    generation_time integer unsigned NOT NULL,
    session_id varchar(80) NOT NULL,
    ip_address varchar(40) NOT NULL,
    member_id integer NOT NULL,
    usage_tally integer NOT NULL,
    PRIMARY KEY (token)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_post_tokens ADD INDEX generation_time (generation_time);
DROP TABLE IF EXISTS cms_privilege_list;
CREATE TABLE cms_privilege_list (
    p_section varchar(80) NOT NULL,
    the_name varchar(80) NOT NULL,
    the_default tinyint(1) NOT NULL,
    PRIMARY KEY (the_name, the_default)
) CHARACTER SET=utf8mb4 engine=MyISAM;
INSERT INTO cms_privilege_list (p_section, the_name, the_default) VALUES ('GENERAL_SETTINGS', 'may_enable_staff_notifications', 0),
('SUBMISSION', 'see_not_validated', 0),
('GENERAL_SETTINGS', 'see_software_docs', 0),
('GENERAL_SETTINGS', 'sees_javascript_error_alerts', 0),
('GENERAL_SETTINGS', 'open_virtual_roots', 0),
('SUBMISSION', 'draw_to_server', 0),
('SUBMISSION', 'exceed_filesize_limit', 0),
('SUBMISSION', 'mass_delete_from_ip', 0),
('SUBMISSION', 'scheduled_publication_times', 0),
('SUBMISSION', 'mass_import', 0),
('SUBMISSION', 'delete_own_cat_lowrange_content', 0),
('SUBMISSION', 'delete_own_cat_midrange_content', 0),
('SUBMISSION', 'delete_own_cat_highrange_content', 0),
('SUBMISSION', 'edit_own_cat_lowrange_content', 0),
('SUBMISSION', 'edit_own_cat_midrange_content', 0),
('SUBMISSION', 'edit_own_cat_highrange_content', 0),
('SUBMISSION', 'delete_cat_lowrange_content', 0),
('SUBMISSION', 'delete_cat_midrange_content', 0),
('SUBMISSION', 'delete_cat_highrange_content', 0),
('SUBMISSION', 'edit_cat_lowrange_content', 0),
('SUBMISSION', 'edit_cat_midrange_content', 0),
('SUBMISSION', 'edit_cat_highrange_content', 0),
('SUBMISSION', 'submit_cat_lowrange_content', 0),
('SUBMISSION', 'submit_cat_midrange_content', 0),
('SUBMISSION', 'submit_cat_highrange_content', 0),
('SUBMISSION', 'search_engine_links', 0),
('SUBMISSION', 'can_submit_to_others_categories', 0),
('SUBMISSION', 'delete_own_lowrange_content', 0),
('SUBMISSION', 'delete_own_midrange_content', 0),
('SUBMISSION', 'delete_own_highrange_content', 0),
('SUBMISSION', 'delete_lowrange_content', 0),
('SUBMISSION', 'delete_midrange_content', 0),
('SUBMISSION', 'delete_highrange_content', 0),
('SUBMISSION', 'edit_own_midrange_content', 0),
('SUBMISSION', 'edit_own_highrange_content', 0),
('SUBMISSION', 'edit_lowrange_content', 0),
('SUBMISSION', 'edit_midrange_content', 0),
('SUBMISSION', 'edit_highrange_content', 0),
('SUBMISSION', 'bypass_validation_midrange_content', 0),
('SUBMISSION', 'bypass_validation_highrange_content', 0),
('SUBMISSION', 'feature', 0);
INSERT INTO cms_privilege_list (p_section, the_name, the_default) VALUES ('STAFF_ACTIONS', 'access_overrun_site', 0),
('STAFF_ACTIONS', 'view_profiling_modes', 0),
('STAFF_ACTIONS', 'see_stack_trace', 0),
('STAFF_ACTIONS', 'bypass_bandwidth_restriction', 0),
('STAFF_ACTIONS', 'access_closed_site', 0),
('_COMCODE', 'use_very_dangerous_comcode', 0),
('_COMCODE', 'comcode_nuisance', 0),
('_COMCODE', 'comcode_dangerous', 0),
('_COMCODE', 'allow_html', 0),
('FORUMS_AND_MEMBERS', 'run_multi_moderations', 1),
('FORUMS_AND_MEMBERS', 'use_pt', 1),
('FORUMS_AND_MEMBERS', 'edit_private_topic_posts', 1),
('FORUMS_AND_MEMBERS', 'may_unblind_own_poll', 1),
('FORUMS_AND_MEMBERS', 'view_member_photos', 1),
('FORUMS_AND_MEMBERS', 'use_quick_reply', 1),
('FORUMS_AND_MEMBERS', 'view_profiles', 1),
('FORUMS_AND_MEMBERS', 'own_avatars', 1),
('FORUMS_AND_MEMBERS', 'double_post', 1),
('FORUMS_AND_MEMBERS', 'delete_account', 1),
('FORUMS_AND_MEMBERS', 'rename_self', 0),
('FORUMS_AND_MEMBERS', 'use_special_emoticons', 0),
('FORUMS_AND_MEMBERS', 'view_any_profile_field', 0),
('FORUMS_AND_MEMBERS', 'disable_lost_passwords', 0),
('FORUMS_AND_MEMBERS', 'close_own_topics', 0),
('FORUMS_AND_MEMBERS', 'edit_own_polls', 0),
('FORUMS_AND_MEMBERS', 'see_warnings', 0),
('FORUMS_AND_MEMBERS', 'see_ip', 0),
('FORUMS_AND_MEMBERS', 'may_choose_custom_title', 0),
('FORUMS_AND_MEMBERS', 'view_other_pt', 0),
('FORUMS_AND_MEMBERS', 'view_poll_results_before_voting', 0),
('FORUMS_AND_MEMBERS', 'moderate_private_topic', 0),
('FORUMS_AND_MEMBERS', 'member_maintenance', 0),
('FORUMS_AND_MEMBERS', 'probate_members', 0),
('FORUMS_AND_MEMBERS', 'warn_members', 0),
('FORUMS_AND_MEMBERS', 'control_usergroups', 0),
('FORUMS_AND_MEMBERS', 'multi_delete_topics', 0),
('FORUMS_AND_MEMBERS', 'show_user_browsing', 0),
('FORUMS_AND_MEMBERS', 'see_hidden_groups', 0),
('FORUMS_AND_MEMBERS', 'pt_anyone', 0),
('FORUMS_AND_MEMBERS', 'delete_private_topic_posts', 0);
INSERT INTO cms_privilege_list (p_section, the_name, the_default) VALUES ('FORUMS_AND_MEMBERS', 'exceed_post_edit_time_limit', 1),
('FORUMS_AND_MEMBERS', 'exceed_post_delete_time_limit', 1),
('FORUMS_AND_MEMBERS', 'bypass_required_cpfs', 0),
('FORUMS_AND_MEMBERS', 'bypass_required_cpfs_if_already_empty', 0),
('FORUMS_AND_MEMBERS', 'bypass_email_address', 0),
('FORUMS_AND_MEMBERS', 'bypass_email_address_if_already_empty', 0),
('FORUMS_AND_MEMBERS', 'bypass_dob', 0),
('FORUMS_AND_MEMBERS', 'bypass_dob_if_already_empty', 0),
('FORUMS_AND_MEMBERS', 'appear_under_birthdays', 1),
('SUBMISSION', 'edit_meta_fields', 0),
('SUBMISSION', 'perform_webstandards_check_by_default', 0),
('SUBMISSION', 'view_private_content', 0),
('GENERAL_SETTINGS', 'bypass_flood_control', 0),
('GENERAL_SETTINGS', 'remove_page_split', 0),
('GENERAL_SETTINGS', 'bypass_wordfilter', 0),
('SUBMISSION', 'perform_keyword_check', 0),
('SUBMISSION', 'have_personal_category', 0),
('STAFF_ACTIONS', 'assume_any_member', 0),
('SUBMISSION', 'edit_own_lowrange_content', 1);
INSERT INTO cms_privilege_list (p_section, the_name, the_default) VALUES ('SUBMISSION', 'submit_highrange_content', 1),
('SUBMISSION', 'submit_midrange_content', 1),
('SUBMISSION', 'submit_lowrange_content', 1),
('SUBMISSION', 'bypass_validation_lowrange_content', 1),
('_FEEDBACK', 'rate', 1),
('_FEEDBACK', 'comment', 1),
('VOTE', 'vote_in_polls', 1),
('SUBMISSION', 'jump_to_not_validated', 1),
('_COMCODE', 'reuse_others_attachments', 1),
('GENERAL_SETTINGS', 'see_php_errors', 1),
('SUBMISSION', 'unfiltered_input', 0),
('STAFF_ACTIONS', 'see_query_errors', 0),
('SUBMISSION', 'bypass_spam_heuristics', 0),
('SUBMISSION', 'avoid_captcha', 1),
('SUBMISSION', 'set_content_review_settings', 0),
('SUBMISSION', 'view_revisions', 0),
('SUBMISSION', 'undo_revisions', 0),
('SUBMISSION', 'delete_revisions', 0),
('GENERAL_SETTINGS', 'use_sms', 0),
('GENERAL_SETTINGS', 'sms_higher_limit', 0),
('GENERAL_SETTINGS', 'sms_higher_trigger_limit', 0),
('SUBMISSION', 'set_own_author_profile', 0),
('BANNERS', 'full_banner_setup', 0),
('BANNERS', 'view_anyones_banner_stats', 0),
('BANNERS', 'banner_free', 0),
('BANNERS', 'use_html_banner', 0),
('BANNERS', 'use_php_banner', 0),
('CALENDAR', 'view_calendar', 1),
('CALENDAR', 'add_public_events', 1),
('CALENDAR', 'sense_personal_conflicts', 0),
('CALENDAR', 'view_event_subscriptions', 0),
('CALENDAR', 'calendar_add_to_others', 1),
('SEARCH', 'autocomplete_keyword_event', 0),
('SEARCH', 'autocomplete_title_event', 0),
('CATALOGUES', 'high_catalogue_entry_timeout', 0),
('SEARCH', 'autocomplete_keyword_catalogue_category', 0),
('SEARCH', 'autocomplete_title_catalogue_category', 0),
('SECTION_CHAT', 'create_private_room', 1),
('SECTION_CHAT', 'start_im', 1),
('SECTION_CHAT', 'moderate_my_private_rooms', 1),
('SECTION_CHAT', 'ban_chatters_from_rooms', 0);
INSERT INTO cms_privilege_list (p_section, the_name, the_default) VALUES ('_SECTION_DOWNLOADS', 'download', 1),
('SEARCH', 'autocomplete_keyword_download_category', 0),
('SEARCH', 'autocomplete_title_download_category', 0),
('SEARCH', 'autocomplete_keyword_download', 0),
('SEARCH', 'autocomplete_title_download', 0),
('GALLERIES', 'may_download_gallery', 0),
('GALLERIES', 'high_personal_gallery_limit', 0),
('GALLERIES', 'no_personal_gallery_limit', 0),
('SEARCH', 'autocomplete_keyword_gallery', 0),
('SEARCH', 'autocomplete_title_gallery', 0),
('SEARCH', 'autocomplete_keyword_image', 0),
('SEARCH', 'autocomplete_title_image', 0),
('SEARCH', 'autocomplete_keyword_videos', 0),
('SEARCH', 'autocomplete_title_videos', 0),
('FORUMS_AND_MEMBERS', 'view_banned_members', 0),
('SEARCH', 'autocomplete_keyword_news', 0),
('SEARCH', 'autocomplete_title_news', 0),
('NEWSLETTER', 'change_newsletter_subscriptions', 0),
('POINTS', 'use_points', 1),
('POINTS', 'trace_anonymous_points_transactions', 0),
('POINTS', 'send_points_to_self', 0),
('POINTS', 'view_points_ledger', 0),
('POINTS', 'send_points', 1),
('POINTS', 'use_points_escrow', 1),
('POINTS', 'moderate_points_escrow', 0),
('POINTS', 'moderate_points', 0),
('POINTS', 'amend_point_transactions', 0),
('POLLS', 'choose_poll', 0),
('SEARCH', 'autocomplete_keyword_poll', 0),
('SEARCH', 'autocomplete_title_poll', 0),
('ECOMMERCE', 'access_ecommerce_in_test_mode', 0),
('QUIZZES', 'bypass_quiz_repeat_time_restriction', 0),
('QUIZZES', 'view_others_quiz_results', 0),
('QUIZZES', 'bypass_quiz_timer', 0),
('SEARCH', 'autocomplete_keyword_quiz', 0),
('SEARCH', 'autocomplete_title_quiz', 0),
('RECOMMEND', 'use_own_recommend_message', 0),
('GENERAL_SETTINGS', 'may_report_content', 1),
('SEARCH', 'autocomplete_past_search', 0),
('SEARCH', 'autocomplete_keyword_comcode_page', 0);
INSERT INTO cms_privilege_list (p_section, the_name, the_default) VALUES ('SEARCH', 'autocomplete_title_comcode_page', 0),
('SUPPORT_TICKETS', 'view_others_tickets', 0),
('SUPPORT_TICKETS', 'support_operator', 0),
('WIKI', 'wiki_manage_tree', 0),
('FILEDUMP', 'upload_anything_filedump', 0),
('FILEDUMP', 'upload_filedump', 1),
('FILEDUMP', 'delete_anything_filedump', 0);

DROP TABLE IF EXISTS cms_quiz_entries;
CREATE TABLE cms_quiz_entries (
    id integer unsigned auto_increment NOT NULL,
    q_time integer unsigned NOT NULL,
    q_member integer NOT NULL,
    q_quiz_id integer NOT NULL,
    q_results integer NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_quiz_entries ADD INDEX q_member (q_member);
DROP TABLE IF EXISTS cms_quiz_entry_answer;
CREATE TABLE cms_quiz_entry_answer (
    q_question_id integer NOT NULL,
    q_answer longtext NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    q_entry_id integer NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
DROP TABLE IF EXISTS cms_quiz_member_last_visit;
CREATE TABLE cms_quiz_member_last_visit (
    v_time integer unsigned NOT NULL,
    v_member_id integer NOT NULL,
    v_quiz_id integer NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_quiz_member_last_visit ADD INDEX member_id (v_member_id);
DROP TABLE IF EXISTS cms_quiz_question_answers;
CREATE TABLE cms_quiz_question_answers (
    id integer unsigned auto_increment NOT NULL,
    q_question_id integer NOT NULL,
    q_answer_text longtext NOT NULL,
    q_is_correct tinyint(1) NOT NULL,
    q_order integer NOT NULL,
    q_explanation longtext NOT NULL,
    q_answer_text__text_parsed longtext NOT NULL,
    q_answer_text__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_quiz_question_answers ADD FULLTEXT q_answer_text (q_answer_text);

ALTER TABLE cms_quiz_question_answers ADD FULLTEXT q_explanation (q_explanation);
DROP TABLE IF EXISTS cms_quiz_questions;
CREATE TABLE cms_quiz_questions (
    q_type varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    q_marked tinyint(1) NOT NULL,
    q_quiz_id integer NOT NULL,
    q_question_text longtext NOT NULL,
    q_question_extra_text longtext NOT NULL,
    q_required tinyint(1) NOT NULL,
    q_order integer NOT NULL,
    q_question_text__text_parsed longtext NOT NULL,
    q_question_text__source_user integer DEFAULT 1 NOT NULL,
    q_question_extra_text__text_parsed longtext NOT NULL,
    q_question_extra_text__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_quiz_questions ADD FULLTEXT q_question_extra_text (q_question_extra_text);

ALTER TABLE cms_quiz_questions ADD FULLTEXT q_question_text (q_question_text);
DROP TABLE IF EXISTS cms_quiz_winner;
CREATE TABLE cms_quiz_winner (
    q_winner_level integer NOT NULL,
    q_entry_id integer NOT NULL,
    q_quiz_id integer NOT NULL,
    PRIMARY KEY (q_entry_id, q_quiz_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
DROP TABLE IF EXISTS cms_quizzes;
CREATE TABLE cms_quizzes (
    q_shuffle_answers tinyint(1) NOT NULL,
    q_validated tinyint(1) NOT NULL,
    q_name longtext NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    q_open_time integer unsigned NOT NULL,
    q_shuffle_questions tinyint(1) NOT NULL,
    q_reveal_answers tinyint(1) NOT NULL,
    q_add_date integer unsigned NOT NULL,
    q_end_text_fail longtext NOT NULL,
    q_timeout integer NULL,
    q_newsletter_id integer NULL,
    q_points_for_passing integer NOT NULL,
    q_submitter integer NOT NULL,
    q_type varchar(80) NOT NULL,
    q_reattempt_hours integer NULL,
    q_num_winners integer NOT NULL,
    q_close_time integer unsigned NULL,
    q_percentage integer NOT NULL,
    q_notes longtext NOT NULL,
    q_end_text longtext NOT NULL,
    q_start_text longtext NOT NULL,
    q_end_text_fail__text_parsed longtext NOT NULL,
    q_end_text_fail__source_user integer DEFAULT 1 NOT NULL,
    q_end_text__text_parsed longtext NOT NULL,
    q_end_text__source_user integer DEFAULT 1 NOT NULL,
    q_start_text__text_parsed longtext NOT NULL,
    q_start_text__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_quizzes ADD FULLTEXT q_end_text (q_end_text);

ALTER TABLE cms_quizzes ADD FULLTEXT q_end_text_fail (q_end_text_fail);

ALTER TABLE cms_quizzes ADD FULLTEXT q_name (q_name);

ALTER TABLE cms_quizzes ADD FULLTEXT q_start_text (q_start_text);

ALTER TABLE cms_quizzes ADD FULLTEXT quiz_search__combined (q_start_text,q_name);

ALTER TABLE cms_quizzes ADD INDEX ftjoin_qstarttext (q_start_text(250));

ALTER TABLE cms_quizzes ADD INDEX q_validated (q_validated);
DROP TABLE IF EXISTS cms_rating;
CREATE TABLE cms_rating (
    rating tinyint NOT NULL,
    rating_time integer unsigned NOT NULL,
    rating_ip_address varchar(40) NOT NULL,
    rating_member integer NOT NULL,
    rating_for_id varchar(80) NOT NULL,
    rating_for_type varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_rating ADD INDEX alt_key (rating_for_type,rating_for_id);

ALTER TABLE cms_rating ADD INDEX rating_for_id (rating_for_id);

ALTER TABLE cms_rating ADD INDEX rating_member (rating_member);
DROP TABLE IF EXISTS cms_redirects;
CREATE TABLE cms_redirects (
    r_is_transparent tinyint(1) NOT NULL,
    r_to_zone varchar(80) NOT NULL,
    r_to_page varchar(80) NOT NULL,
    r_from_zone varchar(80) NOT NULL,
    r_from_page varchar(80) NOT NULL,
    PRIMARY KEY (r_from_zone, r_from_page)
) CHARACTER SET=utf8mb4 engine=MyISAM;
INSERT INTO cms_redirects (r_from_page, r_from_zone, r_to_page, r_to_zone, r_is_transparent) VALUES ('rules', 'site', 'rules', '', 1),
('rules', 'forum', 'rules', '', 1),
('panel_top', 'buildr', 'panel_top', '', 1),
('panel_top', 'docs', 'panel_top', '', 1),
('panel_top', 'forum', 'panel_top', '', 1),
('panel_top', 'site', 'panel_top', '', 1),
('panel_bottom', 'buildr', 'panel_bottom', '', 1),
('panel_bottom', 'docs', 'panel_bottom', '', 1),
('panel_bottom', 'forum', 'panel_bottom', '', 1),
('panel_bottom', 'site', 'panel_bottom', '', 1);

DROP TABLE IF EXISTS cms_reported_content;
CREATE TABLE cms_reported_content (
    r_counts tinyint(1) NOT NULL,
    r_content_type varchar(80) NOT NULL,
    r_content_id varchar(80) NOT NULL,
    r_session_id varchar(80) NOT NULL,
    PRIMARY KEY (r_content_type, r_content_id, r_session_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_reported_content ADD INDEX reported_already (r_content_type,r_content_id);
DROP TABLE IF EXISTS cms_review_supplement;
CREATE TABLE cms_review_supplement (
    r_post_id integer NOT NULL,
    r_rating_type varchar(80) NOT NULL,
    r_rating tinyint NOT NULL,
    r_topic_id integer NOT NULL,
    r_rating_for_type varchar(80) NOT NULL,
    r_rating_for_id varchar(80) NOT NULL,
    PRIMARY KEY (r_post_id, r_rating_type)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_review_supplement ADD INDEX rating_for_id (r_rating_for_id);
DROP TABLE IF EXISTS cms_revisions;
CREATE TABLE cms_revisions (
    r_actionlog_id integer NULL,
    r_original_resource_fs_record longtext NOT NULL,
    r_original_resource_fs_path longtext NOT NULL,
    r_original_text longtext NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    r_resource_type varchar(80) NOT NULL,
    r_resource_id varchar(80) NOT NULL,
    r_category_id varchar(80) NOT NULL,
    r_moderatorlog_id integer NULL,
    r_original_title varchar(255) NOT NULL,
    r_original_content_owner integer NOT NULL,
    r_original_content_timestamp integer unsigned NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_revisions ADD INDEX actionlog_link (r_actionlog_id);

ALTER TABLE cms_revisions ADD INDEX lookup_by_cat (r_resource_type,r_category_id);

ALTER TABLE cms_revisions ADD INDEX lookup_by_id (r_resource_type,r_resource_id);

ALTER TABLE cms_revisions ADD INDEX moderatorlog_link (r_moderatorlog_id);
DROP TABLE IF EXISTS cms_searches_logged;
CREATE TABLE cms_searches_logged (
    id integer unsigned auto_increment NOT NULL,
    s_member_id integer NOT NULL,
    s_time integer unsigned NOT NULL,
    s_primary varchar(255) NOT NULL,
    s_auxillary longtext NOT NULL,
    s_num_results integer NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_searches_logged ADD FULLTEXT past_search_ft (s_primary);

ALTER TABLE cms_searches_logged ADD INDEX member_id (s_member_id);

ALTER TABLE cms_searches_logged ADD INDEX past_search (s_primary(250));
DROP TABLE IF EXISTS cms_seo_meta;
CREATE TABLE cms_seo_meta (
    id integer unsigned auto_increment NOT NULL,
    meta_for_type varchar(80) NOT NULL,
    meta_for_id varchar(80) NOT NULL,
    meta_description longtext NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_seo_meta ADD FULLTEXT meta_description (meta_description);

ALTER TABLE cms_seo_meta ADD INDEX alt_key (meta_for_type,meta_for_id);

ALTER TABLE cms_seo_meta ADD INDEX ftjoin_dmeta_description (meta_description(250));
INSERT INTO cms_seo_meta (id, meta_for_type, meta_for_id, meta_description) VALUES (1, 'gallery', 'root', ''),
(2, 'gallery', 'homepage_hero_slider', 'Slides for the homepage hero slider');

DROP TABLE IF EXISTS cms_seo_meta_keywords;
CREATE TABLE cms_seo_meta_keywords (
    sort_order integer NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    meta_for_id varchar(80) NOT NULL,
    meta_keyword longtext NOT NULL,
    meta_for_type varchar(80) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_seo_meta_keywords ADD FULLTEXT meta_keyword (meta_keyword);

ALTER TABLE cms_seo_meta_keywords ADD INDEX ftjoin_dmeta_keywords (meta_keyword(250));

ALTER TABLE cms_seo_meta_keywords ADD INDEX keywords_alt_key (meta_for_type,meta_for_id);
INSERT INTO cms_seo_meta_keywords (id, sort_order, meta_for_type, meta_for_id, meta_keyword) VALUES (1, 0, 'gallery', 'root', ''),
(3, 0, 'gallery', 'homepage_hero_slider', 'slider'),
(4, 1, 'gallery', 'homepage_hero_slider', 'hero'),
(5, 2, 'gallery', 'homepage_hero_slider', 'homepage'),
(6, 3, 'gallery', 'homepage_hero_slider', 'Slides');

DROP TABLE IF EXISTS cms_sessions;
CREATE TABLE cms_sessions (
    session_confirmed tinyint(1) NOT NULL,
    ip varchar(40) NOT NULL,
    member_id integer NOT NULL,
    last_activity_time integer unsigned NOT NULL,
    the_session varchar(80) NOT NULL,
    the_title varchar(255) NOT NULL,
    the_id varchar(80) NOT NULL,
    the_type varchar(80) NOT NULL,
    the_page varchar(80) NOT NULL,
    the_zone varchar(80) NOT NULL,
    cache_username varchar(80) NOT NULL,
    session_invisible tinyint(1) NOT NULL,
    PRIMARY KEY (the_session)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_sessions ADD INDEX delete_old (last_activity_time);

ALTER TABLE cms_sessions ADD INDEX member_id (member_id);

ALTER TABLE cms_sessions ADD INDEX userat (the_zone,the_page,the_id);
DROP TABLE IF EXISTS cms_shopping_cart;
CREATE TABLE cms_shopping_cart (
    session_id varchar(80) NOT NULL,
    add_time integer unsigned NOT NULL,
    purchase_id varchar(80) NOT NULL,
    type_code varchar(80) NOT NULL,
    ordering_member integer NOT NULL,
    quantity integer NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_shopping_cart ADD INDEX ordering_member (ordering_member);

ALTER TABLE cms_shopping_cart ADD INDEX session_id (session_id);

ALTER TABLE cms_shopping_cart ADD INDEX type_code (type_code);
DROP TABLE IF EXISTS cms_shopping_logging;
CREATE TABLE cms_shopping_logging (
    l_last_action varchar(255) NOT NULL,
    l_date_and_time integer unsigned NOT NULL,
    l_session_id varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    l_member_id integer NOT NULL,
    l_ip_address varchar(40) NOT NULL,
    PRIMARY KEY (id, l_member_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_shopping_logging ADD INDEX cart_log (l_date_and_time);
DROP TABLE IF EXISTS cms_shopping_order_details;
CREATE TABLE cms_shopping_order_details (
    p_tax_code varchar(80) NOT NULL,
    p_tax real NOT NULL,
    p_dispatch_status varchar(255) NOT NULL,
    p_sku varchar(255) NOT NULL,
    p_quantity integer NOT NULL,
    p_price real NOT NULL,
    p_name varchar(255) NOT NULL,
    p_purchase_id varchar(80) NOT NULL,
    p_type_code varchar(80) NOT NULL,
    p_order_id integer NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_shopping_order_details ADD INDEX order_id (p_order_id);

ALTER TABLE cms_shopping_order_details ADD INDEX type_code (p_type_code);
DROP TABLE IF EXISTS cms_shopping_orders;
CREATE TABLE cms_shopping_orders (
    session_id varchar(80) NOT NULL,
    member_id integer NOT NULL,
    add_date integer unsigned NOT NULL,
    total_price real NOT NULL,
    total_tax_derivation longtext NOT NULL,
    total_tax real NOT NULL,
    total_tax_tracking longtext NOT NULL,
    total_shipping_cost real NOT NULL,
    total_shipping_tax real NOT NULL,
    total_product_width real NOT NULL,
    total_product_length real NOT NULL,
    total_product_weight real NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    purchase_through varchar(255) NOT NULL,
    txn_id varchar(255) NOT NULL,
    notes longtext NOT NULL,
    order_status varchar(80) NOT NULL,
    order_currency varchar(80) NOT NULL,
    total_product_height real NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_shopping_orders ADD INDEX finddispatchable (order_status);

ALTER TABLE cms_shopping_orders ADD INDEX soadd_date (add_date);

ALTER TABLE cms_shopping_orders ADD INDEX somember_id (member_id);

ALTER TABLE cms_shopping_orders ADD INDEX sosession_id (session_id);
DROP TABLE IF EXISTS cms_site_messages;
CREATE TABLE cms_site_messages (
    id integer unsigned auto_increment NOT NULL,
    m_start_date_time integer unsigned NULL,
    m_validated tinyint(1) NOT NULL,
    m_type varchar(255) NOT NULL,
    m_message longtext NOT NULL,
    m_end_date_time integer unsigned NULL,
    m_title varchar(255) NOT NULL,
    m_submitter integer NOT NULL,
    m_message__text_parsed longtext NOT NULL,
    m_message__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_site_messages ADD FULLTEXT m_message (m_message);
DROP TABLE IF EXISTS cms_site_messages_groups;
CREATE TABLE cms_site_messages_groups (
    id integer unsigned auto_increment NOT NULL,
    group_id integer NOT NULL,
    message_id integer NOT NULL,
    PRIMARY KEY (id, group_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_site_messages_groups ADD INDEX messagegroups (message_id,group_id);
DROP TABLE IF EXISTS cms_site_messages_pages;
CREATE TABLE cms_site_messages_pages (
    message_id integer NOT NULL,
    page_link varchar(255) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
DROP TABLE IF EXISTS cms_sitemap_cache;
CREATE TABLE cms_sitemap_cache (
    refreshfreq varchar(80) NOT NULL,
    guest_access tinyint(1) NOT NULL,
    set_number integer NOT NULL,
    page_link varchar(255) NOT NULL,
    priority real NOT NULL,
    is_deleted tinyint(1) NOT NULL,
    edit_date integer unsigned NULL,
    add_date integer unsigned NULL,
    last_updated integer unsigned NOT NULL,
    PRIMARY KEY (page_link)
) CHARACTER SET=utf8 engine=MyISAM;
ALTER TABLE cms_sitemap_cache ADD INDEX is_deleted (is_deleted);

ALTER TABLE cms_sitemap_cache ADD INDEX last_updated (last_updated);

ALTER TABLE cms_sitemap_cache ADD INDEX set_number (set_number,last_updated);
DROP TABLE IF EXISTS cms_sms_log;
CREATE TABLE cms_sms_log (
    id integer unsigned auto_increment NOT NULL,
    s_member_id integer NOT NULL,
    s_trigger_ip_address varchar(40) NOT NULL,
    s_time integer unsigned NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_sms_log ADD INDEX sms_log_for (s_member_id,s_time);

ALTER TABLE cms_sms_log ADD INDEX sms_trigger_ip_address (s_trigger_ip_address);
DROP TABLE IF EXISTS cms_staff_checklist_cus_tasks;
CREATE TABLE cms_staff_checklist_cus_tasks (
    recur_interval integer NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    task_title longtext NOT NULL,
    add_date integer unsigned NOT NULL,
    recur_every varchar(80) NOT NULL,
    done_time integer unsigned NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
INSERT INTO cms_staff_checklist_cus_tasks (id, task_title, add_date, recur_interval, recur_every, done_time) VALUES (1, 'Add your content', 1728671820, 0, '', NULL),
(2, '[page=\"adminzone:admin_health_check\"]Run/schedule Health Checks[/page]', 1728671820, 0, '', NULL),
(3, '[url=\"Set up up-time monitor\"]https://uptimerobot.com/[/url]', 1728671820, 0, '', NULL),
(4, '[html]<p style=\"margin: 0\">Facebook user? Like Composr on Facebook:</p><iframe src=\"https://composr.app/uploads/website_specific/cms_homesite/facebook.html\" scrolling=\"no\" frameborder=\"0\" style=\"border:none; overflow:hidden; width:330px; height:20px;\" allowTransparency=\"true\"></iframe>[/html]', 1728671820, 0, '', NULL),
(5, '[page=\"adminzone:admin_version\"]Consider helping out with the Composr project[/page]', 1728671820, 0, '', NULL);

DROP TABLE IF EXISTS cms_staff_links;
CREATE TABLE cms_staff_links (
    link varchar(255) BINARY NOT NULL,
    link_desc longtext NOT NULL,
    link_title varchar(255) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
INSERT INTO cms_staff_links (id, link, link_title, link_desc) VALUES (1, 'https://composr.app', 'Composr Homesite', 'Composr Homesite'),
(2, 'https://composr.app/forum/vforums/unread.htm', 'Composr Homesite (topics with unread posts)', 'Composr Homesite (topics with unread posts)');

DROP TABLE IF EXISTS cms_staff_tips_dismissed;
CREATE TABLE cms_staff_tips_dismissed (
    t_tip varchar(80) NOT NULL,
    t_member integer NOT NULL,
    PRIMARY KEY (t_tip, t_member)
) CHARACTER SET=utf8mb4 engine=MyISAM;
DROP TABLE IF EXISTS cms_stats;
CREATE TABLE cms_stats (
    ip varchar(40) NOT NULL,
    post longtext NOT NULL,
    member_id integer NOT NULL,
    session_id varchar(80) NOT NULL,
    browser varchar(255) NOT NULL,
    operating_system varchar(255) NOT NULL,
    requested_language varchar(80) NOT NULL,
    milliseconds integer NOT NULL,
    tracking_code varchar(80) NOT NULL,
    date_and_time integer unsigned NOT NULL,
    page_link varchar(255) NOT NULL,
    referer_url varchar(255) BINARY NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_stats ADD INDEX date_and_time (date_and_time);

ALTER TABLE cms_stats ADD INDEX session_id (session_id);
DROP TABLE IF EXISTS cms_stats_events;
CREATE TABLE cms_stats_events (
    e_country_code varchar(80) NOT NULL,
    e_date_and_time integer unsigned NOT NULL,
    e_event varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    e_session_id varchar(80) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_stats_events ADD INDEX e_date_and_time (e_date_and_time);

ALTER TABLE cms_stats_events ADD INDEX e_event (e_event,e_date_and_time);
DROP TABLE IF EXISTS cms_stats_known_events;
CREATE TABLE cms_stats_known_events (
    e_event varchar(80) NOT NULL,
    e_count_logged integer NOT NULL,
    PRIMARY KEY (e_event)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_stats_known_events ADD INDEX e_count_logged (e_count_logged);
DROP TABLE IF EXISTS cms_stats_known_links;
CREATE TABLE cms_stats_known_links (
    l_count_logged integer NOT NULL,
    l_url varchar(255) BINARY NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_stats_known_links ADD INDEX l_count_logged (l_count_logged);

ALTER TABLE cms_stats_known_links ADD INDEX l_url (l_url(250));
DROP TABLE IF EXISTS cms_stats_known_tracking;
CREATE TABLE cms_stats_known_tracking (
    t_tracking_code varchar(80) NOT NULL,
    t_count_logged integer NOT NULL,
    PRIMARY KEY (t_tracking_code)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_stats_known_tracking ADD INDEX t_count_logged (t_count_logged);
DROP TABLE IF EXISTS cms_stats_kpis;
CREATE TABLE cms_stats_kpis (
    k_filters longtext NOT NULL,
    k_graph_name varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    k_notes longtext NOT NULL,
    k_submitter integer NOT NULL,
    k_added_time integer unsigned NOT NULL,
    k_title varchar(255) NOT NULL,
    k_pivot varchar(80) NOT NULL,
    k_target real NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_stats_kpis ADD INDEX k_graph_name (k_graph_name);
DROP TABLE IF EXISTS cms_stats_link_tracker;
CREATE TABLE cms_stats_link_tracker (
    c_url varchar(255) BINARY NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    c_date_and_time integer unsigned NOT NULL,
    c_member_id integer NOT NULL,
    c_ip_address varchar(40) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_stats_link_tracker ADD INDEX c_date_and_time (c_date_and_time);

ALTER TABLE cms_stats_link_tracker ADD INDEX c_url (c_url(250));
DROP TABLE IF EXISTS cms_stats_preprocessed;
CREATE TABLE cms_stats_preprocessed (
    p_bucket varchar(80) NOT NULL,
    p_pivot varchar(80) NOT NULL,
    p_month integer NOT NULL,
    p_data longtext NOT NULL,
    PRIMARY KEY (p_bucket, p_pivot, p_month)
) CHARACTER SET=utf8mb4 engine=MyISAM;
DROP TABLE IF EXISTS cms_stats_preprocessed_flat;
CREATE TABLE cms_stats_preprocessed_flat (
    p_data longtext NOT NULL,
    p_bucket varchar(80) NOT NULL,
    PRIMARY KEY (p_bucket)
) CHARACTER SET=utf8mb4 engine=MyISAM;
DROP TABLE IF EXISTS cms_task_queue;
CREATE TABLE cms_task_queue (
    t_add_time integer unsigned NOT NULL,
    t_locked tinyint(1) NOT NULL,
    t_send_notification tinyint(1) NOT NULL,
    t_secure_ref varchar(80) NOT NULL,
    t_member_id integer NOT NULL,
    t_args longtext NOT NULL,
    t_hook varchar(80) NOT NULL,
    t_title varchar(255) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
DROP TABLE IF EXISTS cms_temp_block_permissions;
CREATE TABLE cms_temp_block_permissions (
    p_block_constraints longtext NOT NULL,
    p_time integer unsigned NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    p_session_id varchar(80) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_temp_block_permissions ADD INDEX p_session_id (p_session_id);
DROP TABLE IF EXISTS cms_theme_images;
CREATE TABLE cms_theme_images (
    theme varchar(40) NOT NULL,
    id varchar(255) NOT NULL,
    lang varchar(5) NOT NULL,
    url varchar(255) BINARY NOT NULL,
    PRIMARY KEY (theme, id, lang)
) CHARACTER SET=utf8 engine=MyISAM;
ALTER TABLE cms_theme_images ADD INDEX theme (theme,lang);
DROP TABLE IF EXISTS cms_theme_screen_tree;
CREATE TABLE cms_theme_screen_tree (
    json_tree longtext NOT NULL,
    page_link varchar(255) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_theme_screen_tree ADD INDEX page_link (page_link(250));
DROP TABLE IF EXISTS cms_theme_template_relations;
CREATE TABLE cms_theme_template_relations (
    rel_a varchar(80) NOT NULL,
    rel_b varchar(80) NOT NULL,
    PRIMARY KEY (rel_a, rel_b)
) CHARACTER SET=utf8mb4 engine=MyISAM;
DROP TABLE IF EXISTS cms_ticket_extra_access;
CREATE TABLE cms_ticket_extra_access (
    member_id integer NOT NULL,
    ticket_id varchar(255) NOT NULL,
    PRIMARY KEY (member_id, ticket_id)
) CHARACTER SET=utf8 engine=MyISAM;
DROP TABLE IF EXISTS cms_ticket_known_emailers;
CREATE TABLE cms_ticket_known_emailers (
    email_address varchar(255) NOT NULL,
    member_id integer NOT NULL,
    PRIMARY KEY (email_address)
) CHARACTER SET=utf8 engine=MyISAM;
ALTER TABLE cms_ticket_known_emailers ADD INDEX member_id (member_id);
DROP TABLE IF EXISTS cms_ticket_types;
CREATE TABLE cms_ticket_types (
    ticket_type_name longtext NOT NULL,
    cache_lead_time integer unsigned NULL,
    id integer unsigned auto_increment NOT NULL,
    search_faq tinyint(1) NOT NULL,
    guest_emails_mandatory tinyint(1) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_ticket_types ADD FULLTEXT ticket_type_name (ticket_type_name);
INSERT INTO cms_ticket_types (id, ticket_type_name, guest_emails_mandatory, search_faq, cache_lead_time) VALUES (1, 'Other', 0, 0, NULL),
(2, 'Complaint', 0, 0, NULL),
(3, 'Reported content', 0, 0, NULL);

DROP TABLE IF EXISTS cms_tickets;
CREATE TABLE cms_tickets (
    ticket_id varchar(255) NOT NULL,
    forum_id integer NOT NULL,
    ticket_type integer NOT NULL,
    topic_id integer NOT NULL,
    PRIMARY KEY (ticket_id)
) CHARACTER SET=utf8 engine=MyISAM;
DROP TABLE IF EXISTS cms_trackbacks;
CREATE TABLE cms_trackbacks (
    trackback_time integer unsigned NOT NULL,
    trackback_url varchar(255) BINARY NOT NULL,
    trackback_title varchar(255) NOT NULL,
    trackback_ip_address varchar(40) NOT NULL,
    trackback_for_id varchar(80) NOT NULL,
    trackback_for_type varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    trackback_excerpt longtext NOT NULL,
    trackback_name varchar(255) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_trackbacks ADD INDEX trackback_for_id (trackback_for_id);

ALTER TABLE cms_trackbacks ADD INDEX trackback_for_type (trackback_for_type);

ALTER TABLE cms_trackbacks ADD INDEX trackback_time (trackback_time);
DROP TABLE IF EXISTS cms_translate;
CREATE TABLE cms_translate (
    language varchar(5) NOT NULL,
    importance_level tinyint NOT NULL,
    text_original longtext NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    text_parsed longtext NOT NULL,
    broken tinyint(1) NOT NULL,
    source_user integer NOT NULL,
    PRIMARY KEY (language, id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_translate ADD FULLTEXT tsearch (text_original);

ALTER TABLE cms_translate ADD INDEX decache (text_parsed(2));

ALTER TABLE cms_translate ADD INDEX equiv_lang (text_original(4));

ALTER TABLE cms_translate ADD INDEX importance_level (importance_level);
DROP TABLE IF EXISTS cms_translation_cache;
CREATE TABLE cms_translation_cache (
    t_text_result longtext NOT NULL,
    t_context integer NOT NULL,
    t_text longtext NOT NULL,
    t_lang_to varchar(5) NOT NULL,
    t_lang_from varchar(5) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_translation_cache ADD INDEX lookup (t_lang_from,t_lang_to,t_text(100),t_context);
DROP TABLE IF EXISTS cms_tutorial_links;
CREATE TABLE cms_tutorial_links (
    the_name varchar(80) NOT NULL,
    the_value longtext NOT NULL,
    PRIMARY KEY (the_name)
) CHARACTER SET=utf8mb4 engine=MyISAM;
DROP TABLE IF EXISTS cms_unbannable_ip;
CREATE TABLE cms_unbannable_ip (
    ip varchar(40) NOT NULL,
    note varchar(255) NOT NULL,
    PRIMARY KEY (ip)
) CHARACTER SET=utf8mb4 engine=MyISAM;
DROP TABLE IF EXISTS cms_unsubscribed_emails;
CREATE TABLE cms_unsubscribed_emails (
    b_time integer unsigned NOT NULL,
    b_email_hashed varchar(255) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    b_ip_address varchar(40) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_unsubscribed_emails ADD INDEX b_time (b_time);
DROP TABLE IF EXISTS cms_url_id_monikers;
CREATE TABLE cms_url_id_monikers (
    m_manually_chosen tinyint(1) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    m_deprecated tinyint(1) NOT NULL,
    m_resource_id varchar(80) NOT NULL,
    m_resource_type varchar(80) NOT NULL,
    m_moniker_reversed varchar(255) NOT NULL,
    m_resource_page varchar(80) NOT NULL,
    m_moniker varchar(255) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_url_id_monikers ADD INDEX uim_moniker (m_moniker(250));

ALTER TABLE cms_url_id_monikers ADD INDEX uim_monrev (m_moniker_reversed(250));

ALTER TABLE cms_url_id_monikers ADD INDEX uim_page_link (m_resource_page,m_resource_type,m_resource_id);
DROP TABLE IF EXISTS cms_url_title_cache;
CREATE TABLE cms_url_title_cache (
    t_description longtext NOT NULL,
    t_image_url varchar(255) BINARY NOT NULL,
    t_discovery_url_json varchar(255) BINARY NOT NULL,
    t_discovery_url_xml varchar(255) BINARY NOT NULL,
    t_mime_type varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    t_url varchar(255) BINARY NOT NULL,
    t_title varchar(255) NOT NULL,
    t_meta_title longtext NOT NULL,
    t_keywords longtext NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_url_title_cache ADD INDEX t_url (t_url(250));
DROP TABLE IF EXISTS cms_urls_checked;
CREATE TABLE cms_urls_checked (
    id integer unsigned auto_increment NOT NULL,
    url_exists tinyint(1) NOT NULL,
    response_message varchar(255) NOT NULL,
    url_destination_url varchar(255) BINARY NOT NULL,
    url_check_time integer unsigned NOT NULL,
    url longtext NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_urls_checked ADD INDEX url (url(200));
DROP TABLE IF EXISTS cms_usersonline_track;
CREATE TABLE cms_usersonline_track (
    peak integer NOT NULL,
    date_and_time integer unsigned NOT NULL,
    PRIMARY KEY (date_and_time)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_usersonline_track ADD INDEX peak_track (peak);
DROP TABLE IF EXISTS cms_usersubmitban_member;
CREATE TABLE cms_usersubmitban_member (
    the_member integer NOT NULL,
    PRIMARY KEY (the_member)
) CHARACTER SET=utf8mb4 engine=MyISAM;
DROP TABLE IF EXISTS cms_values;
CREATE TABLE cms_values (
    the_name varchar(80) NOT NULL,
    the_value varchar(255) NOT NULL,
    date_and_time integer unsigned NOT NULL,
    PRIMARY KEY (the_name)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_values ADD INDEX date_and_time (date_and_time);
INSERT INTO cms_values (the_name, the_value, date_and_time) VALUES ('cns_topic_count', '1', 1728671790),
('cns_member_count', '1', 1728671791),
('cns_post_count', '1', 1728671791),
('version', '11.00', 1728671794),
('cns_version', '11.00', 1728671794);

DROP TABLE IF EXISTS cms_values_elective;
CREATE TABLE cms_values_elective (
    date_and_time integer unsigned NOT NULL,
    the_value longtext NOT NULL,
    the_name varchar(80) NOT NULL,
    PRIMARY KEY (the_name)
) CHARACTER SET=utf8mb4 engine=MyISAM;
INSERT INTO cms_values_elective (the_name, the_value, date_and_time) VALUES ('call_home', '0', 1728671794),
('setupwizard_completed', '0', 1728671797),
('db_version', '1728671740', 1728671821);

DROP TABLE IF EXISTS cms_video_transcoding;
CREATE TABLE cms_video_transcoding (
    t_url_field varchar(80) NOT NULL,
    t_table varchar(80) NOT NULL,
    t_url varchar(255) BINARY NOT NULL,
    t_error longtext NOT NULL,
    t_output_filename varchar(80) NOT NULL,
    t_height_field varchar(80) NOT NULL,
    t_local_id integer NULL,
    t_id varchar(80) NOT NULL,
    t_local_id_field varchar(80) NOT NULL,
    t_orig_filename_field varchar(80) NOT NULL,
    t_width_field varchar(80) NOT NULL,
    PRIMARY KEY (t_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_video_transcoding ADD INDEX t_local_id (t_local_id);
DROP TABLE IF EXISTS cms_videos;
CREATE TABLE cms_videos (
    video_views integer NOT NULL,
    video_width integer NOT NULL,
    video_height integer NOT NULL,
    video_length integer NOT NULL,
    title longtext NOT NULL,
    validated tinyint(1) NOT NULL,
    cat varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    thumb_url varchar(255) BINARY NOT NULL,
    closed_captions_url varchar(255) BINARY NOT NULL,
    the_description longtext NOT NULL,
    allow_rating tinyint(1) NOT NULL,
    allow_comments tinyint NOT NULL,
    add_date integer unsigned NOT NULL,
    allow_trackbacks tinyint(1) NOT NULL,
    url varchar(255) BINARY NOT NULL,
    notes longtext NOT NULL,
    submitter integer NOT NULL,
    edit_date integer unsigned NULL,
    the_description__text_parsed longtext NOT NULL,
    the_description__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_videos ADD FULLTEXT the_description (the_description);

ALTER TABLE cms_videos ADD FULLTEXT title (title);

ALTER TABLE cms_videos ADD FULLTEXT video_search__combined (the_description,title);

ALTER TABLE cms_videos ADD INDEX category_list (cat);

ALTER TABLE cms_videos ADD INDEX ftjoin_dtitle (title(250));

ALTER TABLE cms_videos ADD INDEX ftjoin_vdescription (the_description(250));

ALTER TABLE cms_videos ADD INDEX v_validated (validated);

ALTER TABLE cms_videos ADD INDEX vadd_date (add_date);

ALTER TABLE cms_videos ADD INDEX video_views (video_views);

ALTER TABLE cms_videos ADD INDEX vs (submitter);
DROP TABLE IF EXISTS cms_webstandards_checked_once;
CREATE TABLE cms_webstandards_checked_once (
    hash varchar(255) NOT NULL,
    PRIMARY KEY (hash)
) CHARACTER SET=utf8 engine=MyISAM;
DROP TABLE IF EXISTS cms_wiki_children;
CREATE TABLE cms_wiki_children (
    parent_id integer NOT NULL,
    child_id integer NOT NULL,
    the_order integer NOT NULL,
    title varchar(255) NOT NULL,
    PRIMARY KEY (parent_id, child_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
DROP TABLE IF EXISTS cms_wiki_pages;
CREATE TABLE cms_wiki_pages (
    wiki_views integer NOT NULL,
    show_posts tinyint(1) NOT NULL,
    submitter integer NOT NULL,
    add_date integer unsigned NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    title longtext NOT NULL,
    notes longtext NOT NULL,
    the_description longtext NOT NULL,
    edit_date integer unsigned NULL,
    the_description__text_parsed longtext NOT NULL,
    the_description__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_wiki_pages ADD FULLTEXT the_description (the_description);

ALTER TABLE cms_wiki_pages ADD FULLTEXT title (title);

ALTER TABLE cms_wiki_pages ADD FULLTEXT wiki_search__combined (title,the_description);

ALTER TABLE cms_wiki_pages ADD INDEX ftjoin_spd (the_description(250));

ALTER TABLE cms_wiki_pages ADD INDEX ftjoin_spt (title(250));

ALTER TABLE cms_wiki_pages ADD INDEX sadd_date (add_date);

ALTER TABLE cms_wiki_pages ADD INDEX sps (submitter);

ALTER TABLE cms_wiki_pages ADD INDEX wiki_views (wiki_views);
INSERT INTO cms_wiki_pages (id, title, notes, the_description, add_date, edit_date, wiki_views, show_posts, submitter, the_description__text_parsed, the_description__source_user) VALUES (1, 'Wiki+ home', '', '', 1728671818, NULL, 0, 1, 2, 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_6709703cb52217.07082683_31\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_6709703cb52217.07082683_31\\\";s:69:\\\"\\$tpl_funcs[\'string_attach_6709703cb52217.07082683_31\']=\\\"echo \\\\\\\"\\\\\\\";\\\";\\n\\\";}}\");\n', 2);

DROP TABLE IF EXISTS cms_wiki_posts;
CREATE TABLE cms_wiki_posts (
    edit_date integer unsigned NULL,
    member_id integer NOT NULL,
    wiki_views integer NOT NULL,
    validated tinyint(1) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    page_id integer NOT NULL,
    the_message longtext NOT NULL,
    date_and_time integer unsigned NOT NULL,
    the_message__text_parsed longtext NOT NULL,
    the_message__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_wiki_posts ADD FULLTEXT the_message (the_message);

ALTER TABLE cms_wiki_posts ADD INDEX cdate_and_time (date_and_time);

ALTER TABLE cms_wiki_posts ADD INDEX ftjoin_spm (the_message(250));

ALTER TABLE cms_wiki_posts ADD INDEX member_id (member_id);

ALTER TABLE cms_wiki_posts ADD INDEX posts_on_page (page_id);

ALTER TABLE cms_wiki_posts ADD INDEX spos (member_id);

ALTER TABLE cms_wiki_posts ADD INDEX svalidated (validated);

ALTER TABLE cms_wiki_posts ADD INDEX wiki_views (wiki_views);
DROP TABLE IF EXISTS cms_wordfilter;
CREATE TABLE cms_wordfilter (
    w_replacement varchar(255) NOT NULL,
    word varchar(255) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    w_match_type varchar(80) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
INSERT INTO cms_wordfilter (id, word, w_replacement, w_match_type) VALUES (1, 'arsehole', '%GRAWLIXES%', 'full'),
(2, 'asshole', '%GRAWLIXES%', 'full'),
(3, 'arse', '%GRAWLIXES%', 'full'),
(4, 'bastard', '%GRAWLIXES%', 'full'),
(5, 'cock', '%GRAWLIXES%', 'full'),
(6, 'cocked', '%GRAWLIXES%', 'full'),
(7, 'cocksucker', '%GRAWLIXES%', 'full'),
(8, 'cunt', '%GRAWLIXES%', 'full'),
(9, 'cum', '%GRAWLIXES%', 'full'),
(10, 'blowjob', '%GRAWLIXES%', 'full'),
(11, 'bollocks', '%GRAWLIXES%', 'full'),
(12, 'bondage', '%GRAWLIXES%', 'full'),
(13, 'bugger', '%GRAWLIXES%', 'full'),
(14, 'buggery', '%GRAWLIXES%', 'full'),
(15, 'dickhead', '%GRAWLIXES%', 'full'),
(16, 'dildo', '%GRAWLIXES%', 'full'),
(17, 'faggot', '%GRAWLIXES%', 'full'),
(18, 'fuck', '%GRAWLIXES%', 'full'),
(19, 'fucked', '%GRAWLIXES%', 'full'),
(20, 'fucking', '%GRAWLIXES%', 'full'),
(21, 'fucker', '%GRAWLIXES%', 'full'),
(22, 'gayboy', '%GRAWLIXES%', 'full'),
(23, 'jackoff', '%GRAWLIXES%', 'full'),
(24, 'jerk-off', '%GRAWLIXES%', 'full'),
(25, 'motherfucker', '%GRAWLIXES%', 'full'),
(26, 'nigger', '%GRAWLIXES%', 'full'),
(27, 'piss', '%GRAWLIXES%', 'full'),
(28, 'pissed', '%GRAWLIXES%', 'full'),
(29, 'puffter', '%GRAWLIXES%', 'full'),
(30, 'pussy', '%GRAWLIXES%', 'full'),
(31, 'queers', '%GRAWLIXES%', 'full'),
(32, 'retard', '%GRAWLIXES%', 'full'),
(33, 'shag', '%GRAWLIXES%', 'full'),
(34, 'shagged', '%GRAWLIXES%', 'full'),
(35, 'shat', '%GRAWLIXES%', 'full'),
(36, 'shit', '%GRAWLIXES%', 'full'),
(37, 'slut', '%GRAWLIXES%', 'full'),
(38, 'twat', '%GRAWLIXES%', 'full'),
(39, 'wank', '%GRAWLIXES%', 'full'),
(40, 'wanker', '%GRAWLIXES%', 'full'),
(41, 'whore', '%GRAWLIXES%', 'full');

DROP TABLE IF EXISTS cms_zones;
CREATE TABLE cms_zones (
    zone_name varchar(80) NOT NULL,
    zone_default_page varchar(80) NOT NULL,
    zone_title longtext NOT NULL,
    zone_require_session tinyint(1) NOT NULL,
    zone_theme varchar(80) NOT NULL,
    zone_header_text longtext NOT NULL,
    PRIMARY KEY (zone_name)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_zones ADD FULLTEXT zone_header_text (zone_header_text);

ALTER TABLE cms_zones ADD FULLTEXT zone_title (zone_title);
INSERT INTO cms_zones (zone_name, zone_title, zone_default_page, zone_header_text, zone_theme, zone_require_session) VALUES ('', 'Welcome', 'home', '', '-1', 0),
('adminzone', 'Admin Zone', 'home', 'Admin Zone', 'admin', 1),
('site', 'Site', 'home', '', '-1', 0),
('cms', 'Content Management', 'cms', 'Content Management', 'admin', 1),
('docs', 'Tutorials', 'tutorials', '', '-1', 0),
('buildr', 'buildr', 'home', '', '-1', 0),
('forum', 'Forums', 'forumview', 'Forum', '-1', 0);
