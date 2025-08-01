DROP TABLE IF EXISTS cms_f_polls;
CREATE TABLE cms_f_polls (
    id integer unsigned auto_increment NOT NULL,
    po_question varchar(255) NOT NULL,
    po_cache_total_votes integer NOT NULL,
    po_is_private tinyint(1) NOT NULL,
    po_is_open tinyint(1) NOT NULL,
    po_minimum_selections integer NOT NULL,
    po_maximum_selections integer NOT NULL,
    po_requires_reply tinyint(1) NOT NULL,
    po_closing_time integer unsigned NULL,
    po_view_member_votes tinyint(1) NOT NULL,
    po_vote_revocation tinyint(1) NOT NULL,
    po_guests_can_vote tinyint(1) NOT NULL,
    po_point_weighting tinyint(1) NOT NULL,
    po_cache_voting_power real NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_f_post_templates;
CREATE TABLE cms_f_post_templates (
    id integer unsigned auto_increment NOT NULL,
    t_title varchar(255) NOT NULL,
    t_text longtext NOT NULL,
    t_forum_multi_code varchar(255) NOT NULL,
    t_use_default_forums tinyint(1) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
INSERT INTO cms_f_post_templates (id, t_title, t_text, t_forum_multi_code, t_use_default_forums) VALUES (1, 'Bug report', 'Version: ?\nSupport software environment (operating system, etc.):\n?\n\nAssigned to: ?\nSeverity: ?\nExample URL: ?\nDescription:\n?\n\nSteps for reproduction:\n?\n\n', '', 0),
(2, 'Task', 'Assigned to: ?\nPriority/Timescale: ?\nDescription:\n?\n\n', '', 0),
(3, 'Fault', 'Version: ?\nAssigned to: ?\nSeverity/Timescale: ?\nDescription:\n?\n\nSteps for reproduction:\n?\n\n', '', 0);

DROP TABLE IF EXISTS cms_f_posts;
CREATE TABLE cms_f_posts (
    id integer unsigned auto_increment NOT NULL,
    p_title varchar(255) NOT NULL,
    p_post longtext NOT NULL,
    p_time integer unsigned NOT NULL,
    p_ip_address varchar(40) NOT NULL,
    p_posting_member integer NOT NULL,
    p_whisper_to_member integer NULL,
    p_poster_name_if_guest varchar(80) NOT NULL,
    p_validated tinyint(1) NOT NULL,
    p_topic_id integer NOT NULL,
    p_cache_forum_id integer NULL,
    p_last_edit_time integer unsigned NULL,
    p_last_edit_member integer NULL,
    p_is_emphasised tinyint(1) NOT NULL,
    p_skip_sig tinyint(1) NOT NULL,
    p_parent_id integer NULL,
    p_post__text_parsed longtext NOT NULL,
    p_post__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_f_posts ADD FULLTEXT posts_search__combined (p_post,p_title);

ALTER TABLE cms_f_posts ADD FULLTEXT p_post (p_post);

ALTER TABLE cms_f_posts ADD FULLTEXT p_title (p_title);

ALTER TABLE cms_f_posts ADD INDEX deletebyip (p_ip_address);

ALTER TABLE cms_f_posts ADD INDEX find_pp (p_whisper_to_member);

ALTER TABLE cms_f_posts ADD INDEX in_topic (p_topic_id,p_time,id);

ALTER TABLE cms_f_posts ADD INDEX in_topic_change_order (p_topic_id,p_last_edit_time,p_time,id);

ALTER TABLE cms_f_posts ADD INDEX last_edit_member (p_last_edit_member);

ALTER TABLE cms_f_posts ADD INDEX postsinforum (p_cache_forum_id);

ALTER TABLE cms_f_posts ADD INDEX posts_by (p_posting_member,p_time);

ALTER TABLE cms_f_posts ADD INDEX posts_by_in_forum (p_posting_member,p_cache_forum_id);

ALTER TABLE cms_f_posts ADD INDEX posts_by_in_topic (p_posting_member,p_topic_id);

ALTER TABLE cms_f_posts ADD INDEX posts_since (p_time,p_cache_forum_id);

ALTER TABLE cms_f_posts ADD INDEX post_order_time (p_time,id);

ALTER TABLE cms_f_posts ADD INDEX p_last_edit_time (p_last_edit_time);

ALTER TABLE cms_f_posts ADD INDEX p_validated (p_validated);

ALTER TABLE cms_f_posts ADD INDEX search_join (p_post(250));
INSERT INTO cms_f_posts (id, p_title, p_post, p_time, p_ip_address, p_posting_member, p_whisper_to_member, p_poster_name_if_guest, p_validated, p_topic_id, p_cache_forum_id, p_last_edit_time, p_last_edit_member, p_is_emphasised, p_skip_sig, p_parent_id, p_post__text_parsed, p_post__source_user) VALUES (1, 'Welcome to the forums', 'This is the inbuilt forum system (known as Conversr).\n\nA forum system is a tool for communication between members; it consists of posts, organised into topics: each topic is a line of conversation.\n\nComposr provides support for a number of different forum systems, and each forum handles authentication of members: Conversr is the built-in forum, which provides seamless integration between the main website, the forums, and the inbuilt member accounts system.', 1754013158, '127.0.0.1', 1, NULL, 'System', 1, 1, 5, NULL, NULL, 0, 0, NULL, 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:7:{i:0;a:5:{i:0;s:39:\\\"string_attach_688c1de67f7798.77840356_1\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}i:1;a:5:{i:0;s:39:\\\"string_attach_688c1de67f7798.77840356_2\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}i:2;a:5:{i:0;s:39:\\\"string_attach_688c1de67f7798.77840356_3\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}i:3;a:5:{i:0;s:39:\\\"string_attach_688c1de67f7798.77840356_4\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}i:4;a:5:{i:0;s:39:\\\"string_attach_688c1de67f7798.77840356_5\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}i:5;a:5:{i:0;s:39:\\\"string_attach_688c1de67f7798.77840356_6\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}i:6;a:5:{i:0;s:39:\\\"string_attach_688c1de67f7798.77840356_7\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:7:{s:39:\\\"string_attach_688c1de67f7798.77840356_1\\\";s:121:\\\"\\$tpl_funcs[\'string_attach_688c1de67f7798.77840356_1\']=\\\"echo \\\\\\\"This is the inbuilt forum system (known as Conversr).\\\\\\\";\\\";\\n\\\";s:39:\\\"string_attach_688c1de67f7798.77840356_2\\\";s:74:\\\"\\$tpl_funcs[\'string_attach_688c1de67f7798.77840356_2\']=\\\"echo \\\\\\\"<br />\\\\\\\";\\\";\\n\\\";s:39:\\\"string_attach_688c1de67f7798.77840356_3\\\";s:74:\\\"\\$tpl_funcs[\'string_attach_688c1de67f7798.77840356_3\']=\\\"echo \\\\\\\"<br />\\\\\\\";\\\";\\n\\\";s:39:\\\"string_attach_688c1de67f7798.77840356_4\\\";s:210:\\\"\\$tpl_funcs[\'string_attach_688c1de67f7798.77840356_4\']=\\\"echo \\\\\\\"A forum system is a tool for communication between members; it consists of posts, organised into topics: each topic is a line of conversation.\\\\\\\";\\\";\\n\\\";s:39:\\\"string_attach_688c1de67f7798.77840356_5\\\";s:74:\\\"\\$tpl_funcs[\'string_attach_688c1de67f7798.77840356_5\']=\\\"echo \\\\\\\"<br />\\\\\\\";\\\";\\n\\\";s:39:\\\"string_attach_688c1de67f7798.77840356_6\\\";s:74:\\\"\\$tpl_funcs[\'string_attach_688c1de67f7798.77840356_6\']=\\\"echo \\\\\\\"<br />\\\\\\\";\\\";\\n\\\";s:39:\\\"string_attach_688c1de67f7798.77840356_7\\\";s:329:\\\"\\$tpl_funcs[\'string_attach_688c1de67f7798.77840356_7\']=\\\"echo \\\\\\\"Composr provides support for a number of different forum systems, and each forum handles authentication of members: Conversr is the built-in forum, which provides seamless integration between the main website, the forums, and the inbuilt member accounts system.\\\\\\\";\\\";\\n\\\";}}\");\n', 1);

DROP TABLE IF EXISTS cms_f_posts_fulltext_index;
CREATE TABLE cms_f_posts_fulltext_index (
    i_post_id integer NOT NULL,
    i_lang varchar(5) NOT NULL,
    i_ngram integer NOT NULL,
    i_ac integer NOT NULL,
    i_occurrence_rate real NOT NULL,
    i_add_time integer unsigned NOT NULL,
    i_forum_id integer NOT NULL,
    i_posting_member integer NOT NULL,
    i_open tinyint(1) NOT NULL,
    i_pinned tinyint(1) NOT NULL,
    i_starter tinyint(1) NOT NULL,
    PRIMARY KEY (i_post_id, i_lang, i_ngram, i_ac)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_f_posts_fulltext_index ADD INDEX content_id (i_post_id);

ALTER TABLE cms_f_posts_fulltext_index ADD INDEX main_10 (i_lang,i_ngram,i_add_time,i_starter,i_occurrence_rate);

ALTER TABLE cms_f_posts_fulltext_index ADD INDEX main_11 (i_lang,i_ngram,i_forum_id,i_open,i_occurrence_rate);

ALTER TABLE cms_f_posts_fulltext_index ADD INDEX main_12 (i_lang,i_ngram,i_forum_id,i_starter,i_occurrence_rate);

ALTER TABLE cms_f_posts_fulltext_index ADD INDEX main_13 (i_lang,i_ngram,i_open,i_starter,i_occurrence_rate);

ALTER TABLE cms_f_posts_fulltext_index ADD INDEX main_14 (i_lang,i_ngram,i_add_time,i_forum_id,i_open,i_occurrence_rate);

ALTER TABLE cms_f_posts_fulltext_index ADD INDEX main_15 (i_lang,i_ngram,i_add_time,i_forum_id,i_starter,i_occurrence_rate);

ALTER TABLE cms_f_posts_fulltext_index ADD INDEX main_16 (i_lang,i_ngram,i_add_time,i_open,i_starter,i_occurrence_rate);

ALTER TABLE cms_f_posts_fulltext_index ADD INDEX main_17 (i_lang,i_ngram,i_forum_id,i_open,i_starter,i_occurrence_rate);

ALTER TABLE cms_f_posts_fulltext_index ADD INDEX main_18 (i_lang,i_ngram,i_add_time,i_forum_id,i_open,i_starter,i_occurrence_rate);

ALTER TABLE cms_f_posts_fulltext_index ADD INDEX main_19 (i_lang,i_ngram,i_occurrence_rate);

ALTER TABLE cms_f_posts_fulltext_index ADD INDEX main_2 (i_lang,i_ngram,i_add_time,i_occurrence_rate);

ALTER TABLE cms_f_posts_fulltext_index ADD INDEX main_3 (i_lang,i_ngram,i_forum_id,i_occurrence_rate);

ALTER TABLE cms_f_posts_fulltext_index ADD INDEX main_4 (i_lang,i_ngram,i_posting_member,i_occurrence_rate);

ALTER TABLE cms_f_posts_fulltext_index ADD INDEX main_5 (i_lang,i_ngram,i_open,i_occurrence_rate);

ALTER TABLE cms_f_posts_fulltext_index ADD INDEX main_6 (i_lang,i_ngram,i_pinned,i_occurrence_rate);

ALTER TABLE cms_f_posts_fulltext_index ADD INDEX main_7 (i_lang,i_ngram,i_starter,i_occurrence_rate);

ALTER TABLE cms_f_posts_fulltext_index ADD INDEX main_8 (i_lang,i_ngram,i_add_time,i_forum_id,i_occurrence_rate);

ALTER TABLE cms_f_posts_fulltext_index ADD INDEX main_9 (i_lang,i_ngram,i_add_time,i_open,i_occurrence_rate);

DROP TABLE IF EXISTS cms_f_pposts_fulltext_index;
CREATE TABLE cms_f_pposts_fulltext_index (
    i_post_id integer NOT NULL,
    i_for integer NOT NULL,
    i_lang varchar(5) NOT NULL,
    i_ngram integer NOT NULL,
    i_ac integer NOT NULL,
    i_occurrence_rate real NOT NULL,
    i_add_time integer unsigned NOT NULL,
    i_posting_member integer NOT NULL,
    i_starter tinyint(1) NOT NULL,
    PRIMARY KEY (i_post_id, i_for, i_lang, i_ngram, i_ac)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX content_id (i_post_id);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_10 (i_lang,i_ngram,i_ac,i_for,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_11 (i_lang,i_ngram,i_add_time,i_posting_member,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_12 (i_lang,i_ngram,i_add_time,i_starter,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_13 (i_lang,i_ngram,i_add_time,i_for,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_14 (i_lang,i_ngram,i_posting_member,i_starter,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_15 (i_lang,i_ngram,i_posting_member,i_for,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_16 (i_lang,i_ngram,i_starter,i_for,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_17 (i_lang,i_ngram,i_ac,i_add_time,i_posting_member,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_18 (i_lang,i_ngram,i_ac,i_add_time,i_starter,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_19 (i_lang,i_ngram,i_ac,i_add_time,i_for,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_2 (i_lang,i_ngram,i_ac,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_20 (i_lang,i_ngram,i_ac,i_posting_member,i_starter,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_21 (i_lang,i_ngram,i_ac,i_posting_member,i_for,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_22 (i_lang,i_ngram,i_ac,i_starter,i_for,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_23 (i_lang,i_ngram,i_add_time,i_posting_member,i_starter,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_24 (i_lang,i_ngram,i_add_time,i_posting_member,i_for,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_25 (i_lang,i_ngram,i_add_time,i_starter,i_for,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_26 (i_lang,i_ngram,i_posting_member,i_starter,i_for,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_27 (i_lang,i_ngram,i_ac,i_add_time,i_posting_member,i_starter,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_28 (i_lang,i_ngram,i_ac,i_add_time,i_posting_member,i_for,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_29 (i_lang,i_ngram,i_ac,i_add_time,i_starter,i_for,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_3 (i_lang,i_ngram,i_add_time,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_30 (i_lang,i_ngram,i_ac,i_posting_member,i_starter,i_for,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_31 (i_lang,i_ngram,i_add_time,i_posting_member,i_starter,i_for,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_32 (i_lang,i_ngram,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_4 (i_lang,i_ngram,i_posting_member,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_5 (i_lang,i_ngram,i_starter,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_6 (i_lang,i_ngram,i_for,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_7 (i_lang,i_ngram,i_ac,i_add_time,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_8 (i_lang,i_ngram,i_ac,i_posting_member,i_occurrence_rate);

ALTER TABLE cms_f_pposts_fulltext_index ADD INDEX main_9 (i_lang,i_ngram,i_ac,i_starter,i_occurrence_rate);

DROP TABLE IF EXISTS cms_f_read_logs;
CREATE TABLE cms_f_read_logs (
    l_member_id integer NOT NULL,
    l_topic_id integer NOT NULL,
    l_time integer unsigned NOT NULL,
    PRIMARY KEY (l_member_id, l_topic_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_f_read_logs ADD INDEX erase_old_read_logs (l_time);

DROP TABLE IF EXISTS cms_f_saved_warnings;
CREATE TABLE cms_f_saved_warnings (
    s_title varchar(255) NOT NULL,
    s_explanation longtext NOT NULL,
    s_message longtext NOT NULL,
    PRIMARY KEY (s_title)
) CHARACTER SET=utf8 engine=MyISAM;

DROP TABLE IF EXISTS cms_f_special_pt_access;
CREATE TABLE cms_f_special_pt_access (
    s_member_id integer NOT NULL,
    s_topic_id integer NOT NULL,
    PRIMARY KEY (s_member_id, s_topic_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_f_special_pt_access ADD INDEX sp_member (s_member_id);

ALTER TABLE cms_f_special_pt_access ADD INDEX sp_topic (s_topic_id);

DROP TABLE IF EXISTS cms_f_topics;
CREATE TABLE cms_f_topics (
    id integer unsigned auto_increment NOT NULL,
    t_pinned tinyint(1) NOT NULL,
    t_cascading tinyint(1) NOT NULL,
    t_forum_id integer NULL,
    t_pt_from_member integer NULL,
    t_pt_to_member integer NULL,
    t_pt_from_category varchar(255) NOT NULL,
    t_pt_to_category varchar(255) NOT NULL,
    t_description varchar(255) NOT NULL,
    t_description_link varchar(255) NOT NULL,
    t_emoticon varchar(255) NOT NULL,
    t_num_views integer NOT NULL,
    t_validated tinyint(1) NOT NULL,
    t_validation_time integer unsigned NULL,
    t_is_open tinyint(1) NOT NULL,
    t_poll_id integer NULL,
    t_cache_first_post_id integer NULL,
    t_cache_first_time integer unsigned NULL,
    t_cache_first_title varchar(255) NOT NULL,
    t_cache_first_post longtext NOT NULL,
    t_cache_first_username varchar(80) NOT NULL,
    t_cache_first_member_id integer NULL,
    t_cache_last_post_id integer NULL,
    t_cache_last_time integer unsigned NULL,
    t_cache_last_title varchar(255) NOT NULL,
    t_cache_last_username varchar(80) NOT NULL,
    t_cache_last_member_id integer NULL,
    t_cache_num_posts integer NOT NULL,
    t_cache_first_post__text_parsed longtext NOT NULL,
    t_cache_first_post__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_f_topics ADD FULLTEXT t_cache_first_post (t_cache_first_post);

ALTER TABLE cms_f_topics ADD FULLTEXT t_description (t_description);

ALTER TABLE cms_f_topics ADD INDEX descriptionsearch (t_description(250));

ALTER TABLE cms_f_topics ADD INDEX forumlayer (t_cache_first_title(250));

ALTER TABLE cms_f_topics ADD INDEX in_forum (t_forum_id);

ALTER TABLE cms_f_topics ADD INDEX ownedtopics (t_cache_first_member_id);

ALTER TABLE cms_f_topics ADD INDEX topic_order (t_cascading,t_pinned,t_cache_last_time);

ALTER TABLE cms_f_topics ADD INDEX topic_order_forum (t_forum_id,t_cascading,t_pinned,t_cache_last_time);

ALTER TABLE cms_f_topics ADD INDEX topic_order_time (t_cache_last_time);

ALTER TABLE cms_f_topics ADD INDEX topic_order_time_2 (t_cache_first_time);

ALTER TABLE cms_f_topics ADD INDEX t_cache_first_post_id (t_cache_first_post_id);

ALTER TABLE cms_f_topics ADD INDEX t_cache_last_member_id (t_cache_last_member_id);

ALTER TABLE cms_f_topics ADD INDEX t_cache_last_post_id (t_cache_last_post_id);

ALTER TABLE cms_f_topics ADD INDEX t_cache_num_posts (t_cache_num_posts);

ALTER TABLE cms_f_topics ADD INDEX t_cascading (t_cascading);

ALTER TABLE cms_f_topics ADD INDEX t_cascading_or_forum (t_cascading,t_forum_id);

ALTER TABLE cms_f_topics ADD INDEX t_num_views (t_num_views);

ALTER TABLE cms_f_topics ADD INDEX t_pt_from_member (t_pt_from_member);

ALTER TABLE cms_f_topics ADD INDEX t_pt_to_member (t_pt_to_member);

ALTER TABLE cms_f_topics ADD INDEX t_validated (t_validated);

ALTER TABLE cms_f_topics ADD INDEX unread_forums (t_forum_id,t_cache_last_time);
INSERT INTO cms_f_topics (id, t_pinned, t_cascading, t_forum_id, t_pt_from_member, t_pt_to_member, t_pt_from_category, t_pt_to_category, t_description, t_description_link, t_emoticon, t_num_views, t_validated, t_validation_time, t_is_open, t_poll_id, t_cache_first_post_id, t_cache_first_time, t_cache_first_title, t_cache_first_post, t_cache_first_username, t_cache_first_member_id, t_cache_last_post_id, t_cache_last_time, t_cache_last_title, t_cache_last_username, t_cache_last_member_id, t_cache_num_posts, t_cache_first_post__text_parsed, t_cache_first_post__source_user) VALUES (1, 0, 0, 5, NULL, NULL, '', '', '', '', '', 0, 1, NULL, 1, NULL, 1, 1754013158, 'Welcome to the forums', '', 'System', 1, 1, 1754013158, 'Welcome to the forums', 'System', 1, 1, '', 1);

DROP TABLE IF EXISTS cms_f_usergroup_sub_mails;
CREATE TABLE cms_f_usergroup_sub_mails (
    id integer unsigned auto_increment NOT NULL,
    m_usergroup_sub_id integer NOT NULL,
    m_ref_point varchar(80) NOT NULL,
    m_ref_point_offset integer NOT NULL,
    m_subject longtext NOT NULL,
    m_body longtext NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_f_usergroup_sub_mails ADD FULLTEXT m_body (m_body);

ALTER TABLE cms_f_usergroup_sub_mails ADD FULLTEXT m_subject (m_subject);

DROP TABLE IF EXISTS cms_f_usergroup_subs;
CREATE TABLE cms_f_usergroup_subs (
    id integer unsigned auto_increment NOT NULL,
    s_title longtext NOT NULL,
    s_description longtext NOT NULL,
    s_price real NOT NULL,
    s_tax_code varchar(80) NOT NULL,
    s_length integer NOT NULL,
    s_length_units varchar(255) NOT NULL,
    s_auto_recur tinyint(1) NOT NULL,
    s_group_id integer NOT NULL,
    s_enabled tinyint(1) NOT NULL,
    s_mail_start longtext NOT NULL,
    s_mail_end longtext NOT NULL,
    s_mail_uhoh longtext NOT NULL,
    s_uses_primary tinyint(1) NOT NULL,
    s_description__text_parsed longtext NOT NULL,
    s_description__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_f_usergroup_subs ADD FULLTEXT s_description (s_description);

ALTER TABLE cms_f_usergroup_subs ADD FULLTEXT s_mail_end (s_mail_end);

ALTER TABLE cms_f_usergroup_subs ADD FULLTEXT s_mail_start (s_mail_start);

ALTER TABLE cms_f_usergroup_subs ADD FULLTEXT s_mail_uhoh (s_mail_uhoh);

ALTER TABLE cms_f_usergroup_subs ADD FULLTEXT s_title (s_title);

DROP TABLE IF EXISTS cms_f_warnings;
CREATE TABLE cms_f_warnings (
    id integer unsigned auto_increment NOT NULL,
    w_member_id integer NOT NULL,
    w_time integer unsigned NOT NULL,
    w_explanation longtext NOT NULL,
    w_issuing_member integer NOT NULL,
    w_is_warning tinyint(1) NOT NULL,
    w_topic_id integer NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_f_warnings ADD INDEX warningsmemberid (w_member_id);

DROP TABLE IF EXISTS cms_f_warnings_punitive;
CREATE TABLE cms_f_warnings_punitive (
    id integer unsigned auto_increment NOT NULL,
    p_warning_id integer NOT NULL,
    p_member_id integer NOT NULL,
    p_ip_address varchar(40) NOT NULL,
    p_email_address varchar(255) NOT NULL,
    p_hook varchar(80) NOT NULL,
    p_action varchar(80) NOT NULL,
    p_param_a varchar(255) NOT NULL,
    p_param_b varchar(255) NOT NULL,
    p_reversed tinyint(1) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_f_warnings_punitive ADD INDEX warninghook (p_hook);

ALTER TABLE cms_f_warnings_punitive ADD INDEX warningsid (p_warning_id);

DROP TABLE IF EXISTS cms_f_welcome_emails;
CREATE TABLE cms_f_welcome_emails (
    id integer unsigned auto_increment NOT NULL,
    w_name varchar(255) NOT NULL,
    w_subject longtext NOT NULL,
    w_text longtext NOT NULL,
    w_send_after_hours integer NOT NULL,
    w_newsletter_id integer NULL,
    w_usergroup integer NULL,
    w_usergroup_type varchar(80) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_f_welcome_emails ADD FULLTEXT w_subject (w_subject);

ALTER TABLE cms_f_welcome_emails ADD FULLTEXT w_text (w_text);

DROP TABLE IF EXISTS cms_failedlogins;
CREATE TABLE cms_failedlogins (
    failed_account varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    date_and_time integer unsigned NOT NULL,
    ip varchar(40) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_failedlogins ADD INDEX failedlogins_by_ip (ip);

DROP TABLE IF EXISTS cms_feature_lifetime_monitor;
CREATE TABLE cms_feature_lifetime_monitor (
    content_id varchar(80) NOT NULL,
    block_cache_id varchar(80) NOT NULL,
    run_period integer NOT NULL,
    running_now tinyint(1) NOT NULL,
    last_update integer unsigned NOT NULL,
    PRIMARY KEY (content_id, block_cache_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_filedump;
CREATE TABLE cms_filedump (
    id integer unsigned auto_increment NOT NULL,
    name varchar(80) NOT NULL,
    subpath varchar(255) NOT NULL,
    the_description longtext NOT NULL,
    the_member integer NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_filedump ADD FULLTEXT the_description (the_description);

DROP TABLE IF EXISTS cms_ft_index_commonality;
CREATE TABLE cms_ft_index_commonality (
    id integer unsigned auto_increment NOT NULL,
    c_ngram varchar(255) NOT NULL,
    c_commonality real NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_ft_index_commonality ADD INDEX c_commonality (c_commonality);

ALTER TABLE cms_ft_index_commonality ADD INDEX c_ngram (c_ngram(250));

DROP TABLE IF EXISTS cms_galleries;
CREATE TABLE cms_galleries (
    name varchar(80) NOT NULL,
    the_description longtext NOT NULL,
    fullname longtext NOT NULL,
    add_date integer unsigned NOT NULL,
    rep_image varchar(255) BINARY NOT NULL,
    parent_id varchar(80) NOT NULL,
    watermark_top_left varchar(255) BINARY NOT NULL,
    watermark_top_right varchar(255) BINARY NOT NULL,
    watermark_bottom_left varchar(255) BINARY NOT NULL,
    watermark_bottom_right varchar(255) BINARY NOT NULL,
    accept_images tinyint(1) NOT NULL,
    accept_videos tinyint(1) NOT NULL,
    allow_rating tinyint(1) NOT NULL,
    allow_comments tinyint NOT NULL,
    notes longtext NOT NULL,
    is_member_synched tinyint(1) NOT NULL,
    layout_mode varchar(80) NOT NULL,
    gallery_views integer NOT NULL,
    g_owner integer NULL,
    gallery_sort varchar(80) NOT NULL,
    media_sort varchar(80) NOT NULL,
    the_description__text_parsed longtext NOT NULL,
    the_description__source_user integer DEFAULT 1 NOT NULL,
    fullname__text_parsed longtext NOT NULL,
    fullname__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (name)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_galleries ADD FULLTEXT fullname (fullname);

ALTER TABLE cms_galleries ADD FULLTEXT gallery_search__combined (fullname,the_description);

ALTER TABLE cms_galleries ADD FULLTEXT the_description (the_description);

ALTER TABLE cms_galleries ADD INDEX ftjoin_gdescrip (the_description(250));

ALTER TABLE cms_galleries ADD INDEX ftjoin_gfullname (fullname(250));

ALTER TABLE cms_galleries ADD INDEX gadd_date (add_date);

ALTER TABLE cms_galleries ADD INDEX parent_id (parent_id);

ALTER TABLE cms_galleries ADD INDEX watermark_bottom_left (watermark_bottom_left(250));

ALTER TABLE cms_galleries ADD INDEX watermark_bottom_right (watermark_bottom_right(250));

ALTER TABLE cms_galleries ADD INDEX watermark_top_left (watermark_top_left(250));

ALTER TABLE cms_galleries ADD INDEX watermark_top_right (watermark_top_right(250));
INSERT INTO cms_galleries (name, the_description, fullname, add_date, rep_image, parent_id, watermark_top_left, watermark_top_right, watermark_bottom_left, watermark_bottom_right, accept_images, accept_videos, allow_rating, allow_comments, notes, is_member_synched, layout_mode, gallery_views, g_owner, gallery_sort, media_sort, the_description__text_parsed, the_description__source_user, fullname__text_parsed, fullname__source_user) VALUES ('root', '', 'Galleries home', 1754013168, '', '', '', '', '', '', 1, 1, 0, 0, '', 0, 'grid', 0, NULL, '', '', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_23\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_23\\\";s:69:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_23\']=\\\"echo \\\\\\\"\\\\\\\";\\\";\\n\\\";}}\");\n', 2, 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_24\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_24\\\";s:83:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_24\']=\\\"echo \\\\\\\"Galleries home\\\\\\\";\\\";\\n\\\";}}\");\n', 2),
('homepage_hero_slider', 'Slides for the homepage hero slider', 'Homepage Hero Slider', 1754013168, '', 'root', '', '', '', '', 1, 1, 0, 0, '', 0, 'grid', 0, NULL, '', '', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_25\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_25\\\";s:104:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_25\']=\\\"echo \\\\\\\"Slides for the homepage hero slider\\\\\\\";\\\";\\n\\\";}}\");\n', 2, 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_26\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_26\\\";s:89:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_26\']=\\\"echo \\\\\\\"Homepage Hero Slider\\\\\\\";\\\";\\n\\\";}}\");\n', 2);

DROP TABLE IF EXISTS cms_group_category_access;
CREATE TABLE cms_group_category_access (
    module_the_name varchar(80) NOT NULL,
    category_name varchar(80) NOT NULL,
    group_id integer NOT NULL,
    PRIMARY KEY (module_the_name, category_name, group_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
INSERT INTO cms_group_category_access (module_the_name, category_name, group_id) VALUES ('banners', 'advertise_here', 1),
('banners', 'advertise_here', 3),
('banners', 'advertise_here', 4),
('banners', 'advertise_here', 5),
('banners', 'advertise_here', 6),
('banners', 'advertise_here', 7),
('banners', 'advertise_here', 8),
('banners', 'advertise_here', 9),
('banners', 'donate', 1),
('banners', 'donate', 3),
('banners', 'donate', 4),
('banners', 'donate', 5),
('banners', 'donate', 6),
('banners', 'donate', 7),
('banners', 'donate', 8),
('banners', 'donate', 9),
('calendar', '2', 1),
('calendar', '2', 3),
('calendar', '2', 4),
('calendar', '2', 5),
('calendar', '2', 6),
('calendar', '2', 7),
('calendar', '2', 8),
('calendar', '2', 9),
('calendar', '3', 1),
('calendar', '3', 3),
('calendar', '3', 4),
('calendar', '3', 5),
('calendar', '3', 6),
('calendar', '3', 7),
('calendar', '3', 8),
('calendar', '3', 9),
('calendar', '4', 1),
('calendar', '4', 3),
('calendar', '4', 4),
('calendar', '4', 5),
('calendar', '4', 6),
('calendar', '4', 7),
('calendar', '4', 8),
('calendar', '4', 9),
('calendar', '5', 1);
INSERT INTO cms_group_category_access (module_the_name, category_name, group_id) VALUES ('calendar', '5', 3),
('calendar', '5', 4),
('calendar', '5', 5),
('calendar', '5', 6),
('calendar', '5', 7),
('calendar', '5', 8),
('calendar', '5', 9),
('calendar', '6', 1),
('calendar', '6', 3),
('calendar', '6', 4),
('calendar', '6', 5),
('calendar', '6', 6),
('calendar', '6', 7),
('calendar', '6', 8),
('calendar', '6', 9),
('calendar', '7', 1),
('calendar', '7', 3),
('calendar', '7', 4),
('calendar', '7', 5),
('calendar', '7', 6),
('calendar', '7', 7),
('calendar', '7', 8),
('calendar', '7', 9),
('calendar', '8', 1),
('calendar', '8', 3),
('calendar', '8', 4),
('calendar', '8', 5),
('calendar', '8', 6),
('calendar', '8', 7),
('calendar', '8', 8),
('calendar', '8', 9),
('catalogues_catalogue', 'faqs', 1),
('catalogues_catalogue', 'faqs', 3),
('catalogues_catalogue', 'faqs', 4),
('catalogues_catalogue', 'faqs', 5),
('catalogues_catalogue', 'faqs', 6),
('catalogues_catalogue', 'faqs', 7),
('catalogues_catalogue', 'faqs', 8),
('catalogues_catalogue', 'faqs', 9),
('catalogues_catalogue', 'links', 1);
INSERT INTO cms_group_category_access (module_the_name, category_name, group_id) VALUES ('catalogues_catalogue', 'links', 3),
('catalogues_catalogue', 'links', 4),
('catalogues_catalogue', 'links', 5),
('catalogues_catalogue', 'links', 6),
('catalogues_catalogue', 'links', 7),
('catalogues_catalogue', 'links', 8),
('catalogues_catalogue', 'links', 9),
('catalogues_catalogue', 'products', 1),
('catalogues_catalogue', 'products', 3),
('catalogues_catalogue', 'products', 4),
('catalogues_catalogue', 'products', 5),
('catalogues_catalogue', 'products', 6),
('catalogues_catalogue', 'products', 7),
('catalogues_catalogue', 'products', 8),
('catalogues_catalogue', 'products', 9),
('catalogues_catalogue', 'projects', 1),
('catalogues_catalogue', 'projects', 3),
('catalogues_catalogue', 'projects', 4),
('catalogues_catalogue', 'projects', 5);
INSERT INTO cms_group_category_access (module_the_name, category_name, group_id) VALUES ('catalogues_catalogue', 'projects', 6),
('catalogues_catalogue', 'projects', 7),
('catalogues_catalogue', 'projects', 8),
('catalogues_catalogue', 'projects', 9),
('catalogues_category', '1', 1),
('catalogues_category', '1', 3),
('catalogues_category', '1', 4),
('catalogues_category', '1', 5),
('catalogues_category', '1', 6),
('catalogues_category', '1', 7),
('catalogues_category', '1', 8),
('catalogues_category', '1', 9),
('catalogues_category', '2', 1),
('catalogues_category', '2', 3),
('catalogues_category', '2', 4),
('catalogues_category', '2', 5),
('catalogues_category', '2', 6),
('catalogues_category', '2', 7),
('catalogues_category', '2', 8),
('catalogues_category', '2', 9),
('catalogues_category', '3', 1),
('catalogues_category', '3', 3),
('catalogues_category', '3', 4),
('catalogues_category', '3', 5),
('catalogues_category', '3', 6),
('catalogues_category', '3', 7),
('catalogues_category', '3', 8),
('catalogues_category', '3', 9),
('catalogues_category', '5', 1),
('catalogues_category', '5', 3),
('catalogues_category', '5', 4),
('catalogues_category', '5', 5),
('catalogues_category', '5', 6),
('catalogues_category', '5', 7),
('catalogues_category', '5', 8),
('catalogues_category', '5', 9),
('chat', '1', 1),
('chat', '1', 3),
('chat', '1', 4),
('chat', '1', 5),
('chat', '1', 6);
INSERT INTO cms_group_category_access (module_the_name, category_name, group_id) VALUES ('chat', '1', 7),
('chat', '1', 8),
('chat', '1', 9),
('downloads', '1', 1),
('downloads', '1', 3),
('downloads', '1', 4),
('downloads', '1', 5),
('downloads', '1', 6),
('downloads', '1', 7),
('downloads', '1', 8),
('downloads', '1', 9),
('forums', '1', 1),
('forums', '1', 2),
('forums', '1', 3),
('forums', '1', 4),
('forums', '1', 5),
('forums', '1', 6),
('forums', '1', 7),
('forums', '1', 8),
('forums', '1', 9),
('forums', '2', 1),
('forums', '2', 2),
('forums', '2', 3),
('forums', '2', 4),
('forums', '2', 5),
('forums', '2', 6),
('forums', '2', 7),
('forums', '2', 8),
('forums', '2', 9),
('forums', '3', 2),
('forums', '3', 3),
('forums', '4', 1),
('forums', '4', 2),
('forums', '4', 3),
('forums', '4', 4),
('forums', '4', 5),
('forums', '4', 6),
('forums', '4', 7),
('forums', '4', 8),
('forums', '4', 9);
INSERT INTO cms_group_category_access (module_the_name, category_name, group_id) VALUES ('forums', '5', 2),
('forums', '5', 3),
('forums', '6', 3),
('galleries', 'homepage_hero_slider', 1),
('galleries', 'homepage_hero_slider', 3),
('galleries', 'homepage_hero_slider', 4),
('galleries', 'homepage_hero_slider', 5),
('galleries', 'homepage_hero_slider', 6),
('galleries', 'homepage_hero_slider', 7),
('galleries', 'homepage_hero_slider', 8),
('galleries', 'homepage_hero_slider', 9),
('galleries', 'root', 1),
('galleries', 'root', 3),
('galleries', 'root', 4),
('galleries', 'root', 5),
('galleries', 'root', 6),
('galleries', 'root', 7),
('galleries', 'root', 8),
('galleries', 'root', 9);
INSERT INTO cms_group_category_access (module_the_name, category_name, group_id) VALUES ('news', '1', 1),
('news', '1', 3),
('news', '1', 4),
('news', '1', 5),
('news', '1', 6),
('news', '1', 7),
('news', '1', 8),
('news', '1', 9),
('news', '2', 1),
('news', '2', 3),
('news', '2', 4),
('news', '2', 5),
('news', '2', 6),
('news', '2', 7),
('news', '2', 8),
('news', '2', 9),
('news', '3', 1),
('news', '3', 3),
('news', '3', 4),
('news', '3', 5),
('news', '3', 6),
('news', '3', 7),
('news', '3', 8),
('news', '3', 9),
('news', '4', 1),
('news', '4', 3),
('news', '4', 4),
('news', '4', 5),
('news', '4', 6),
('news', '4', 7),
('news', '4', 8),
('news', '4', 9),
('news', '5', 1),
('news', '5', 3),
('news', '5', 4),
('news', '5', 5),
('news', '5', 6),
('news', '5', 7),
('news', '5', 8),
('news', '5', 9),
('news', '6', 1);
INSERT INTO cms_group_category_access (module_the_name, category_name, group_id) VALUES ('news', '6', 3),
('news', '6', 4),
('news', '6', 5),
('news', '6', 6),
('news', '6', 7),
('news', '6', 8),
('news', '6', 9),
('news', '7', 1),
('news', '7', 3),
('news', '7', 4),
('news', '7', 5),
('news', '7', 6),
('news', '7', 7),
('news', '7', 8),
('news', '7', 9),
('tickets', '1', 1),
('tickets', '1', 3),
('tickets', '1', 4),
('tickets', '1', 5),
('tickets', '1', 6),
('tickets', '1', 7),
('tickets', '1', 8),
('tickets', '1', 9),
('tickets', '2', 1),
('tickets', '2', 3),
('tickets', '2', 4),
('tickets', '2', 5),
('tickets', '2', 6),
('tickets', '2', 7),
('tickets', '2', 8),
('tickets', '2', 9),
('tickets', '3', 1),
('tickets', '3', 3),
('tickets', '3', 4),
('tickets', '3', 5),
('tickets', '3', 6),
('tickets', '3', 7),
('tickets', '3', 8),
('tickets', '3', 9),
('wiki_page', '1', 1);
INSERT INTO cms_group_category_access (module_the_name, category_name, group_id) VALUES ('wiki_page', '1', 3),
('wiki_page', '1', 4),
('wiki_page', '1', 5),
('wiki_page', '1', 6),
('wiki_page', '1', 7),
('wiki_page', '1', 8),
('wiki_page', '1', 9);

DROP TABLE IF EXISTS cms_group_page_access;
CREATE TABLE cms_group_page_access (
    page_name varchar(80) NOT NULL,
    zone_name varchar(80) NOT NULL,
    group_id integer NOT NULL,
    PRIMARY KEY (page_name, zone_name, group_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_group_page_access ADD INDEX group_id (group_id);
INSERT INTO cms_group_page_access (page_name, zone_name, group_id) VALUES ('admin_addons', 'adminzone', 1),
('admin_addons', 'adminzone', 2),
('admin_addons', 'adminzone', 3),
('admin_addons', 'adminzone', 4),
('admin_addons', 'adminzone', 5),
('admin_addons', 'adminzone', 6),
('admin_addons', 'adminzone', 7),
('admin_addons', 'adminzone', 8),
('admin_addons', 'adminzone', 9),
('admin_cns_groups', 'adminzone', 1),
('admin_cns_groups', 'adminzone', 2),
('admin_cns_groups', 'adminzone', 3),
('admin_cns_groups', 'adminzone', 4),
('admin_cns_groups', 'adminzone', 5),
('admin_cns_groups', 'adminzone', 6),
('admin_cns_groups', 'adminzone', 7),
('admin_cns_groups', 'adminzone', 8),
('admin_cns_groups', 'adminzone', 9),
('admin_cns_ldap', 'adminzone', 1),
('admin_cns_ldap', 'adminzone', 2),
('admin_cns_ldap', 'adminzone', 3),
('admin_cns_ldap', 'adminzone', 4),
('admin_cns_ldap', 'adminzone', 5),
('admin_cns_ldap', 'adminzone', 6),
('admin_cns_ldap', 'adminzone', 7),
('admin_cns_ldap', 'adminzone', 8),
('admin_cns_ldap', 'adminzone', 9),
('admin_commandr', 'adminzone', 1),
('admin_commandr', 'adminzone', 2),
('admin_commandr', 'adminzone', 3),
('admin_commandr', 'adminzone', 4),
('admin_commandr', 'adminzone', 5),
('admin_commandr', 'adminzone', 6),
('admin_commandr', 'adminzone', 7),
('admin_commandr', 'adminzone', 8),
('admin_commandr', 'adminzone', 9),
('admin_ecommerce', 'adminzone', 1),
('admin_ecommerce', 'adminzone', 2),
('admin_ecommerce', 'adminzone', 3),
('admin_ecommerce', 'adminzone', 4),
('admin_ecommerce', 'adminzone', 5);
INSERT INTO cms_group_page_access (page_name, zone_name, group_id) VALUES ('admin_ecommerce', 'adminzone', 6),
('admin_ecommerce', 'adminzone', 7),
('admin_ecommerce', 'adminzone', 8),
('admin_ecommerce', 'adminzone', 9),
('admin_email_log', 'adminzone', 1),
('admin_email_log', 'adminzone', 2),
('admin_email_log', 'adminzone', 3),
('admin_email_log', 'adminzone', 4),
('admin_email_log', 'adminzone', 5),
('admin_email_log', 'adminzone', 6),
('admin_email_log', 'adminzone', 7),
('admin_email_log', 'adminzone', 8),
('admin_email_log', 'adminzone', 9),
('admin_group_member_timeouts', 'adminzone', 1),
('admin_group_member_timeouts', 'adminzone', 2),
('admin_group_member_timeouts', 'adminzone', 3),
('admin_group_member_timeouts', 'adminzone', 4),
('admin_group_member_timeouts', 'adminzone', 5),
('admin_group_member_timeouts', 'adminzone', 6),
('admin_group_member_timeouts', 'adminzone', 7),
('admin_group_member_timeouts', 'adminzone', 8),
('admin_group_member_timeouts', 'adminzone', 9),
('admin_import', 'adminzone', 1),
('admin_import', 'adminzone', 2),
('admin_import', 'adminzone', 3),
('admin_import', 'adminzone', 4),
('admin_import', 'adminzone', 5),
('admin_import', 'adminzone', 6),
('admin_import', 'adminzone', 7),
('admin_import', 'adminzone', 8),
('admin_import', 'adminzone', 9),
('admin_permissions', 'adminzone', 1),
('admin_permissions', 'adminzone', 2),
('admin_permissions', 'adminzone', 3),
('admin_permissions', 'adminzone', 4),
('admin_permissions', 'adminzone', 5),
('admin_permissions', 'adminzone', 6),
('admin_permissions', 'adminzone', 7),
('admin_permissions', 'adminzone', 8),
('admin_permissions', 'adminzone', 9);
INSERT INTO cms_group_page_access (page_name, zone_name, group_id) VALUES ('admin_redirects', 'adminzone', 1),
('admin_redirects', 'adminzone', 2),
('admin_redirects', 'adminzone', 3),
('admin_redirects', 'adminzone', 4),
('admin_redirects', 'adminzone', 5),
('admin_redirects', 'adminzone', 6),
('admin_redirects', 'adminzone', 7),
('admin_redirects', 'adminzone', 8),
('admin_redirects', 'adminzone', 9),
('cms_chat', 'cms', 1),
('cms_chat', 'cms', 2),
('cms_chat', 'cms', 3),
('cms_chat', 'cms', 4),
('cms_chat', 'cms', 5),
('cms_chat', 'cms', 6),
('cms_chat', 'cms', 7),
('cms_chat', 'cms', 8),
('cms_chat', 'cms', 9),
('contact_member', 'site', 2);
INSERT INTO cms_group_page_access (page_name, zone_name, group_id) VALUES ('contact_member', 'site', 3),
('contact_member', 'site', 4),
('contact_member', 'site', 5),
('contact_member', 'site', 6),
('contact_member', 'site', 7),
('contact_member', 'site', 8),
('contact_member', 'site', 9),
('filedump', 'cms', 1),
('filedump', 'cms', 2),
('filedump', 'cms', 3),
('filedump', 'cms', 4),
('filedump', 'cms', 5),
('filedump', 'cms', 6),
('filedump', 'cms', 7),
('filedump', 'cms', 8),
('filedump', 'cms', 9);

DROP TABLE IF EXISTS cms_group_points;
CREATE TABLE cms_group_points (
    p_group_id integer NOT NULL,
    p_points_one_off integer NOT NULL,
    p_points_per_month integer NOT NULL,
    PRIMARY KEY (p_group_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_group_privileges;
CREATE TABLE cms_group_privileges (
    group_id integer NOT NULL,
    privilege varchar(80) NOT NULL,
    the_page varchar(80) NOT NULL,
    module_the_name varchar(80) NOT NULL,
    category_name varchar(80) NOT NULL,
    the_value tinyint(1) NOT NULL,
    PRIMARY KEY (group_id, privilege, the_page, module_the_name, category_name)
) CHARACTER SET=utf8 engine=MyISAM;
ALTER TABLE cms_group_privileges ADD INDEX by_privilege (privilege);

ALTER TABLE cms_group_privileges ADD INDEX group_id (group_id);
INSERT INTO cms_group_privileges (group_id, privilege, the_page, module_the_name, category_name, the_value) VALUES (3, 'edit_cat_highrange_content', '', '', '', 1),
(2, 'edit_cat_highrange_content', '', '', '', 1),
(3, 'submit_cat_lowrange_content', '', '', '', 1),
(2, 'submit_cat_lowrange_content', '', '', '', 1),
(3, 'submit_cat_midrange_content', '', '', '', 1),
(2, 'submit_cat_midrange_content', '', '', '', 1),
(3, 'submit_cat_highrange_content', '', '', '', 1),
(2, 'submit_cat_highrange_content', '', '', '', 1),
(3, 'search_engine_links', '', '', '', 1),
(2, 'search_engine_links', '', '', '', 1),
(3, 'can_submit_to_others_categories', '', '', '', 1),
(2, 'can_submit_to_others_categories', '', '', '', 1),
(3, 'delete_own_lowrange_content', '', '', '', 1),
(2, 'delete_own_lowrange_content', '', '', '', 1),
(3, 'delete_own_midrange_content', '', '', '', 1),
(2, 'delete_own_midrange_content', '', '', '', 1),
(3, 'delete_own_highrange_content', '', '', '', 1),
(2, 'delete_own_highrange_content', '', '', '', 1),
(3, 'delete_lowrange_content', '', '', '', 1),
(2, 'delete_lowrange_content', '', '', '', 1),
(3, 'delete_midrange_content', '', '', '', 1),
(2, 'delete_midrange_content', '', '', '', 1),
(3, 'delete_highrange_content', '', '', '', 1),
(2, 'delete_highrange_content', '', '', '', 1),
(3, 'edit_own_midrange_content', '', '', '', 1),
(2, 'edit_own_midrange_content', '', '', '', 1),
(3, 'edit_own_highrange_content', '', '', '', 1),
(2, 'edit_own_highrange_content', '', '', '', 1),
(3, 'edit_lowrange_content', '', '', '', 1),
(2, 'edit_lowrange_content', '', '', '', 1),
(3, 'edit_midrange_content', '', '', '', 1),
(2, 'edit_midrange_content', '', '', '', 1),
(3, 'edit_highrange_content', '', '', '', 1),
(2, 'edit_highrange_content', '', '', '', 1),
(3, 'bypass_validation_midrange_content', '', '', '', 1),
(2, 'bypass_validation_midrange_content', '', '', '', 1),
(3, 'bypass_validation_highrange_content', '', '', '', 1),
(2, 'bypass_validation_highrange_content', '', '', '', 1),
(3, 'feature', '', '', '', 1),
(2, 'feature', '', '', '', 1),
(3, 'access_overrun_site', '', '', '', 1);
INSERT INTO cms_group_privileges (group_id, privilege, the_page, module_the_name, category_name, the_value) VALUES (2, 'access_overrun_site', '', '', '', 1),
(3, 'view_profiling_modes', '', '', '', 1),
(2, 'view_profiling_modes', '', '', '', 1),
(3, 'see_stack_trace', '', '', '', 1),
(2, 'see_stack_trace', '', '', '', 1),
(3, 'bypass_bandwidth_restriction', '', '', '', 1),
(2, 'bypass_bandwidth_restriction', '', '', '', 1),
(3, 'access_closed_site', '', '', '', 1),
(2, 'access_closed_site', '', '', '', 1),
(3, 'use_very_dangerous_comcode', '', '', '', 1),
(2, 'use_very_dangerous_comcode', '', '', '', 1),
(3, 'comcode_nuisance', '', '', '', 1),
(2, 'comcode_nuisance', '', '', '', 1),
(3, 'comcode_dangerous', '', '', '', 1),
(2, 'comcode_dangerous', '', '', '', 1),
(3, 'allow_html', '', '', '', 1),
(2, 'allow_html', '', '', '', 1),
(1, 'use_pt', '', '', '', 1),
(2, 'use_pt', '', '', '', 1),
(3, 'use_pt', '', '', '', 1),
(4, 'use_pt', '', '', '', 1),
(5, 'use_pt', '', '', '', 1),
(6, 'use_pt', '', '', '', 1),
(7, 'use_pt', '', '', '', 1),
(8, 'use_pt', '', '', '', 1),
(9, 'use_pt', '', '', '', 1),
(1, 'edit_private_topic_posts', '', '', '', 1),
(2, 'edit_private_topic_posts', '', '', '', 1),
(3, 'edit_private_topic_posts', '', '', '', 1),
(4, 'edit_private_topic_posts', '', '', '', 1),
(5, 'edit_private_topic_posts', '', '', '', 1),
(6, 'edit_private_topic_posts', '', '', '', 1),
(7, 'edit_private_topic_posts', '', '', '', 1),
(8, 'edit_private_topic_posts', '', '', '', 1),
(9, 'edit_private_topic_posts', '', '', '', 1),
(1, 'may_unblind_own_poll', '', '', '', 1),
(2, 'may_unblind_own_poll', '', '', '', 1),
(3, 'may_unblind_own_poll', '', '', '', 1),
(4, 'may_unblind_own_poll', '', '', '', 1),
(5, 'may_unblind_own_poll', '', '', '', 1);
INSERT INTO cms_group_privileges (group_id, privilege, the_page, module_the_name, category_name, the_value) VALUES (6, 'may_unblind_own_poll', '', '', '', 1),
(7, 'may_unblind_own_poll', '', '', '', 1),
(8, 'may_unblind_own_poll', '', '', '', 1),
(9, 'may_unblind_own_poll', '', '', '', 1),
(1, 'view_member_photos', '', '', '', 1),
(2, 'view_member_photos', '', '', '', 1),
(3, 'view_member_photos', '', '', '', 1),
(4, 'view_member_photos', '', '', '', 1),
(5, 'view_member_photos', '', '', '', 1),
(6, 'view_member_photos', '', '', '', 1),
(7, 'view_member_photos', '', '', '', 1),
(8, 'view_member_photos', '', '', '', 1),
(9, 'view_member_photos', '', '', '', 1),
(1, 'use_quick_reply', '', '', '', 1),
(2, 'use_quick_reply', '', '', '', 1),
(3, 'use_quick_reply', '', '', '', 1),
(4, 'use_quick_reply', '', '', '', 1),
(5, 'use_quick_reply', '', '', '', 1),
(6, 'use_quick_reply', '', '', '', 1);
INSERT INTO cms_group_privileges (group_id, privilege, the_page, module_the_name, category_name, the_value) VALUES (7, 'use_quick_reply', '', '', '', 1),
(8, 'use_quick_reply', '', '', '', 1),
(9, 'use_quick_reply', '', '', '', 1),
(1, 'view_profiles', '', '', '', 1),
(2, 'view_profiles', '', '', '', 1),
(3, 'view_profiles', '', '', '', 1),
(4, 'view_profiles', '', '', '', 1),
(5, 'view_profiles', '', '', '', 1),
(6, 'view_profiles', '', '', '', 1),
(7, 'view_profiles', '', '', '', 1),
(8, 'view_profiles', '', '', '', 1),
(9, 'view_profiles', '', '', '', 1),
(1, 'own_avatars', '', '', '', 1),
(2, 'own_avatars', '', '', '', 1),
(3, 'own_avatars', '', '', '', 1),
(4, 'own_avatars', '', '', '', 1),
(5, 'own_avatars', '', '', '', 1),
(6, 'own_avatars', '', '', '', 1),
(7, 'own_avatars', '', '', '', 1),
(8, 'own_avatars', '', '', '', 1),
(9, 'own_avatars', '', '', '', 1),
(1, 'double_post', '', '', '', 1),
(2, 'double_post', '', '', '', 1),
(3, 'double_post', '', '', '', 1),
(4, 'double_post', '', '', '', 1),
(5, 'double_post', '', '', '', 1),
(6, 'double_post', '', '', '', 1),
(7, 'double_post', '', '', '', 1),
(8, 'double_post', '', '', '', 1),
(9, 'double_post', '', '', '', 1),
(1, 'delete_account', '', '', '', 1),
(2, 'delete_account', '', '', '', 1),
(3, 'delete_account', '', '', '', 1),
(4, 'delete_account', '', '', '', 1),
(5, 'delete_account', '', '', '', 1),
(6, 'delete_account', '', '', '', 1),
(7, 'delete_account', '', '', '', 1),
(8, 'delete_account', '', '', '', 1),
(9, 'delete_account', '', '', '', 1),
(1, 'exceed_post_edit_time_limit', '', '', '', 1),
(2, 'exceed_post_edit_time_limit', '', '', '', 1);
INSERT INTO cms_group_privileges (group_id, privilege, the_page, module_the_name, category_name, the_value) VALUES (3, 'exceed_post_edit_time_limit', '', '', '', 1),
(4, 'exceed_post_edit_time_limit', '', '', '', 1),
(5, 'exceed_post_edit_time_limit', '', '', '', 1),
(6, 'exceed_post_edit_time_limit', '', '', '', 1),
(7, 'exceed_post_edit_time_limit', '', '', '', 1),
(8, 'exceed_post_edit_time_limit', '', '', '', 1),
(9, 'exceed_post_edit_time_limit', '', '', '', 1),
(1, 'exceed_post_delete_time_limit', '', '', '', 1),
(2, 'exceed_post_delete_time_limit', '', '', '', 1),
(3, 'exceed_post_delete_time_limit', '', '', '', 1),
(4, 'exceed_post_delete_time_limit', '', '', '', 1),
(5, 'exceed_post_delete_time_limit', '', '', '', 1),
(6, 'exceed_post_delete_time_limit', '', '', '', 1),
(7, 'exceed_post_delete_time_limit', '', '', '', 1),
(8, 'exceed_post_delete_time_limit', '', '', '', 1),
(9, 'exceed_post_delete_time_limit', '', '', '', 1),
(1, 'appear_under_birthdays', '', '', '', 1),
(2, 'appear_under_birthdays', '', '', '', 1),
(3, 'appear_under_birthdays', '', '', '', 1),
(4, 'appear_under_birthdays', '', '', '', 1),
(5, 'appear_under_birthdays', '', '', '', 1),
(6, 'appear_under_birthdays', '', '', '', 1),
(7, 'appear_under_birthdays', '', '', '', 1),
(8, 'appear_under_birthdays', '', '', '', 1),
(9, 'appear_under_birthdays', '', '', '', 1),
(2, 'edit_meta_fields', '', '', '', 1),
(3, 'edit_meta_fields', '', '', '', 1),
(2, 'perform_webstandards_check_by_default', '', '', '', 1),
(3, 'perform_webstandards_check_by_default', '', '', '', 1),
(2, 'edit_cat_midrange_content', '', '', '', 1),
(3, 'edit_cat_midrange_content', '', '', '', 1),
(2, 'edit_cat_lowrange_content', '', '', '', 1),
(3, 'edit_cat_lowrange_content', '', '', '', 1),
(2, 'delete_cat_highrange_content', '', '', '', 1),
(3, 'delete_cat_highrange_content', '', '', '', 1),
(2, 'delete_cat_midrange_content', '', '', '', 1),
(3, 'delete_cat_midrange_content', '', '', '', 1),
(2, 'delete_cat_lowrange_content', '', '', '', 1),
(3, 'delete_cat_lowrange_content', '', '', '', 1),
(2, 'edit_own_cat_highrange_content', '', '', '', 1);
INSERT INTO cms_group_privileges (group_id, privilege, the_page, module_the_name, category_name, the_value) VALUES (3, 'edit_own_cat_highrange_content', '', '', '', 1),
(2, 'edit_own_cat_midrange_content', '', '', '', 1),
(3, 'edit_own_cat_midrange_content', '', '', '', 1),
(2, 'edit_own_cat_lowrange_content', '', '', '', 1),
(3, 'edit_own_cat_lowrange_content', '', '', '', 1),
(2, 'delete_own_cat_highrange_content', '', '', '', 1),
(3, 'delete_own_cat_highrange_content', '', '', '', 1),
(2, 'delete_own_cat_midrange_content', '', '', '', 1),
(3, 'delete_own_cat_midrange_content', '', '', '', 1),
(2, 'delete_own_cat_lowrange_content', '', '', '', 1),
(3, 'delete_own_cat_lowrange_content', '', '', '', 1),
(2, 'mass_import', '', '', '', 1),
(3, 'mass_import', '', '', '', 1),
(2, 'scheduled_publication_times', '', '', '', 1),
(3, 'scheduled_publication_times', '', '', '', 1),
(2, 'mass_delete_from_ip', '', '', '', 1),
(3, 'mass_delete_from_ip', '', '', '', 1),
(2, 'exceed_filesize_limit', '', '', '', 1),
(3, 'exceed_filesize_limit', '', '', '', 1);
INSERT INTO cms_group_privileges (group_id, privilege, the_page, module_the_name, category_name, the_value) VALUES (2, 'draw_to_server', '', '', '', 1),
(3, 'draw_to_server', '', '', '', 1),
(2, 'open_virtual_roots', '', '', '', 1),
(3, 'open_virtual_roots', '', '', '', 1),
(2, 'sees_javascript_error_alerts', '', '', '', 1),
(3, 'sees_javascript_error_alerts', '', '', '', 1),
(2, 'see_software_docs', '', '', '', 1),
(3, 'see_software_docs', '', '', '', 1),
(2, 'may_enable_staff_notifications', '', '', '', 1),
(3, 'may_enable_staff_notifications', '', '', '', 1),
(2, 'bypass_flood_control', '', '', '', 1),
(3, 'bypass_flood_control', '', '', '', 1),
(2, 'remove_page_split', '', '', '', 1),
(3, 'remove_page_split', '', '', '', 1),
(2, 'bypass_wordfilter', '', '', '', 1),
(3, 'bypass_wordfilter', '', '', '', 1),
(2, 'perform_keyword_check', '', '', '', 1),
(3, 'perform_keyword_check', '', '', '', 1),
(2, 'have_personal_category', '', '', '', 1),
(3, 'have_personal_category', '', '', '', 1),
(1, 'edit_own_lowrange_content', '', '', '', 1),
(2, 'edit_own_lowrange_content', '', '', '', 1),
(3, 'edit_own_lowrange_content', '', '', '', 1),
(4, 'edit_own_lowrange_content', '', '', '', 1),
(5, 'edit_own_lowrange_content', '', '', '', 1),
(6, 'edit_own_lowrange_content', '', '', '', 1),
(7, 'edit_own_lowrange_content', '', '', '', 1),
(8, 'edit_own_lowrange_content', '', '', '', 1),
(9, 'edit_own_lowrange_content', '', '', '', 1),
(1, 'submit_highrange_content', '', '', '', 1),
(2, 'submit_highrange_content', '', '', '', 1),
(3, 'submit_highrange_content', '', '', '', 1),
(4, 'submit_highrange_content', '', '', '', 1),
(5, 'submit_highrange_content', '', '', '', 1),
(6, 'submit_highrange_content', '', '', '', 1),
(7, 'submit_highrange_content', '', '', '', 1),
(8, 'submit_highrange_content', '', '', '', 1),
(9, 'submit_highrange_content', '', '', '', 1),
(1, 'submit_midrange_content', '', '', '', 1),
(2, 'submit_midrange_content', '', '', '', 1),
(3, 'submit_midrange_content', '', '', '', 1);
INSERT INTO cms_group_privileges (group_id, privilege, the_page, module_the_name, category_name, the_value) VALUES (4, 'submit_midrange_content', '', '', '', 1),
(5, 'submit_midrange_content', '', '', '', 1),
(6, 'submit_midrange_content', '', '', '', 1),
(7, 'submit_midrange_content', '', '', '', 1),
(8, 'submit_midrange_content', '', '', '', 1),
(9, 'submit_midrange_content', '', '', '', 1),
(1, 'submit_lowrange_content', '', '', '', 1),
(2, 'submit_lowrange_content', '', '', '', 1),
(3, 'submit_lowrange_content', '', '', '', 1),
(4, 'submit_lowrange_content', '', '', '', 1),
(5, 'submit_lowrange_content', '', '', '', 1),
(6, 'submit_lowrange_content', '', '', '', 1),
(7, 'submit_lowrange_content', '', '', '', 1),
(8, 'submit_lowrange_content', '', '', '', 1),
(9, 'submit_lowrange_content', '', '', '', 1),
(1, 'bypass_validation_lowrange_content', '', '', '', 1),
(2, 'bypass_validation_lowrange_content', '', '', '', 1),
(3, 'bypass_validation_lowrange_content', '', '', '', 1),
(4, 'bypass_validation_lowrange_content', '', '', '', 1),
(5, 'bypass_validation_lowrange_content', '', '', '', 1),
(6, 'bypass_validation_lowrange_content', '', '', '', 1),
(7, 'bypass_validation_lowrange_content', '', '', '', 1),
(8, 'bypass_validation_lowrange_content', '', '', '', 1),
(9, 'bypass_validation_lowrange_content', '', '', '', 1),
(1, 'rate', '', '', '', 1),
(2, 'rate', '', '', '', 1),
(3, 'rate', '', '', '', 1),
(4, 'rate', '', '', '', 1),
(5, 'rate', '', '', '', 1),
(6, 'rate', '', '', '', 1),
(7, 'rate', '', '', '', 1),
(8, 'rate', '', '', '', 1),
(9, 'rate', '', '', '', 1),
(1, 'comment', '', '', '', 1),
(2, 'comment', '', '', '', 1),
(3, 'comment', '', '', '', 1),
(4, 'comment', '', '', '', 1),
(5, 'comment', '', '', '', 1),
(6, 'comment', '', '', '', 1),
(7, 'comment', '', '', '', 1);
INSERT INTO cms_group_privileges (group_id, privilege, the_page, module_the_name, category_name, the_value) VALUES (8, 'comment', '', '', '', 1),
(9, 'comment', '', '', '', 1),
(1, 'vote_in_polls', '', '', '', 1),
(2, 'vote_in_polls', '', '', '', 1),
(3, 'vote_in_polls', '', '', '', 1),
(4, 'vote_in_polls', '', '', '', 1),
(5, 'vote_in_polls', '', '', '', 1),
(6, 'vote_in_polls', '', '', '', 1),
(7, 'vote_in_polls', '', '', '', 1),
(8, 'vote_in_polls', '', '', '', 1),
(9, 'vote_in_polls', '', '', '', 1),
(1, 'reuse_others_attachments', '', '', '', 1),
(2, 'reuse_others_attachments', '', '', '', 1),
(3, 'reuse_others_attachments', '', '', '', 1),
(4, 'reuse_others_attachments', '', '', '', 1),
(5, 'reuse_others_attachments', '', '', '', 1),
(6, 'reuse_others_attachments', '', '', '', 1),
(7, 'reuse_others_attachments', '', '', '', 1),
(8, 'reuse_others_attachments', '', '', '', 1);
INSERT INTO cms_group_privileges (group_id, privilege, the_page, module_the_name, category_name, the_value) VALUES (9, 'reuse_others_attachments', '', '', '', 1),
(1, 'see_php_errors', '', '', '', 1),
(2, 'see_php_errors', '', '', '', 1),
(3, 'see_php_errors', '', '', '', 1),
(4, 'see_php_errors', '', '', '', 1),
(5, 'see_php_errors', '', '', '', 1),
(6, 'see_php_errors', '', '', '', 1),
(7, 'see_php_errors', '', '', '', 1),
(8, 'see_php_errors', '', '', '', 1),
(9, 'see_php_errors', '', '', '', 1),
(2, 'unfiltered_input', '', '', '', 1),
(3, 'unfiltered_input', '', '', '', 1),
(2, 'see_query_errors', '', '', '', 1),
(3, 'see_query_errors', '', '', '', 1),
(2, 'bypass_spam_heuristics', '', '', '', 1),
(3, 'bypass_spam_heuristics', '', '', '', 1),
(1, 'avoid_captcha', '', '', '', 1),
(2, 'avoid_captcha', '', '', '', 1),
(3, 'avoid_captcha', '', '', '', 1),
(4, 'avoid_captcha', '', '', '', 1),
(5, 'avoid_captcha', '', '', '', 1),
(6, 'avoid_captcha', '', '', '', 1),
(7, 'avoid_captcha', '', '', '', 1),
(8, 'avoid_captcha', '', '', '', 1),
(2, 'view_banned_members', '', '', '', 1),
(3, 'view_banned_members', '', '', '', 1),
(2, 'view_revisions', '', '', '', 1),
(3, 'view_revisions', '', '', '', 1),
(2, 'undo_revisions', '', '', '', 1),
(3, 'undo_revisions', '', '', '', 1),
(2, 'delete_revisions', '', '', '', 1),
(3, 'delete_revisions', '', '', '', 1),
(2, 'set_own_author_profile', '', '', '', 1),
(3, 'set_own_author_profile', '', '', '', 1),
(2, 'full_banner_setup', '', '', '', 1),
(3, 'full_banner_setup', '', '', '', 1),
(2, 'view_anyones_banner_stats', '', '', '', 1),
(3, 'view_anyones_banner_stats', '', '', '', 1),
(2, 'banner_free', '', '', '', 1),
(3, 'banner_free', '', '', '', 1),
(2, 'use_html_banner', '', '', '', 1);
INSERT INTO cms_group_privileges (group_id, privilege, the_page, module_the_name, category_name, the_value) VALUES (3, 'use_html_banner', '', '', '', 1),
(1, 'view_calendar', '', '', '', 1),
(2, 'view_calendar', '', '', '', 1),
(3, 'view_calendar', '', '', '', 1),
(4, 'view_calendar', '', '', '', 1),
(5, 'view_calendar', '', '', '', 1),
(6, 'view_calendar', '', '', '', 1),
(7, 'view_calendar', '', '', '', 1),
(8, 'view_calendar', '', '', '', 1),
(9, 'view_calendar', '', '', '', 1),
(1, 'add_public_events', '', '', '', 1),
(2, 'add_public_events', '', '', '', 1),
(3, 'add_public_events', '', '', '', 1),
(4, 'add_public_events', '', '', '', 1),
(5, 'add_public_events', '', '', '', 1),
(6, 'add_public_events', '', '', '', 1),
(7, 'add_public_events', '', '', '', 1),
(8, 'add_public_events', '', '', '', 1),
(9, 'add_public_events', '', '', '', 1),
(2, 'sense_personal_conflicts', '', '', '', 1),
(3, 'sense_personal_conflicts', '', '', '', 1),
(2, 'view_event_subscriptions', '', '', '', 1),
(3, 'view_event_subscriptions', '', '', '', 1),
(1, 'calendar_add_to_others', '', '', '', 1),
(2, 'calendar_add_to_others', '', '', '', 1),
(3, 'calendar_add_to_others', '', '', '', 1),
(4, 'calendar_add_to_others', '', '', '', 1),
(5, 'calendar_add_to_others', '', '', '', 1),
(6, 'calendar_add_to_others', '', '', '', 1),
(7, 'calendar_add_to_others', '', '', '', 1),
(8, 'calendar_add_to_others', '', '', '', 1),
(9, 'calendar_add_to_others', '', '', '', 1),
(2, 'autocomplete_keyword_event', '', '', '', 1),
(3, 'autocomplete_keyword_event', '', '', '', 1),
(2, 'autocomplete_title_event', '', '', '', 1),
(3, 'autocomplete_title_event', '', '', '', 1),
(2, 'high_catalogue_entry_timeout', '', '', '', 1),
(3, 'high_catalogue_entry_timeout', '', '', '', 1),
(2, 'autocomplete_keyword_catalogue_category', '', '', '', 1),
(3, 'autocomplete_keyword_catalogue_category', '', '', '', 1);
INSERT INTO cms_group_privileges (group_id, privilege, the_page, module_the_name, category_name, the_value) VALUES (2, 'autocomplete_title_catalogue_category', '', '', '', 1),
(3, 'autocomplete_title_catalogue_category', '', '', '', 1),
(1, 'create_private_room', '', '', '', 1),
(2, 'create_private_room', '', '', '', 1),
(3, 'create_private_room', '', '', '', 1),
(4, 'create_private_room', '', '', '', 1),
(5, 'create_private_room', '', '', '', 1),
(6, 'create_private_room', '', '', '', 1),
(7, 'create_private_room', '', '', '', 1),
(8, 'create_private_room', '', '', '', 1),
(9, 'create_private_room', '', '', '', 1),
(1, 'start_im', '', '', '', 1),
(2, 'start_im', '', '', '', 1),
(3, 'start_im', '', '', '', 1),
(4, 'start_im', '', '', '', 1),
(5, 'start_im', '', '', '', 1),
(6, 'start_im', '', '', '', 1),
(7, 'start_im', '', '', '', 1),
(8, 'start_im', '', '', '', 1);
INSERT INTO cms_group_privileges (group_id, privilege, the_page, module_the_name, category_name, the_value) VALUES (9, 'start_im', '', '', '', 1),
(1, 'moderate_my_private_rooms', '', '', '', 1),
(2, 'moderate_my_private_rooms', '', '', '', 1),
(3, 'moderate_my_private_rooms', '', '', '', 1),
(4, 'moderate_my_private_rooms', '', '', '', 1),
(5, 'moderate_my_private_rooms', '', '', '', 1),
(6, 'moderate_my_private_rooms', '', '', '', 1),
(7, 'moderate_my_private_rooms', '', '', '', 1),
(8, 'moderate_my_private_rooms', '', '', '', 1),
(9, 'moderate_my_private_rooms', '', '', '', 1),
(2, 'ban_chatters_from_rooms', '', '', '', 1),
(3, 'ban_chatters_from_rooms', '', '', '', 1),
(1, 'run_multi_moderations', '', '', '', 1),
(2, 'run_multi_moderations', '', '', '', 1),
(3, 'run_multi_moderations', '', '', '', 1),
(4, 'run_multi_moderations', '', '', '', 1),
(5, 'run_multi_moderations', '', '', '', 1),
(6, 'run_multi_moderations', '', '', '', 1),
(7, 'run_multi_moderations', '', '', '', 1),
(8, 'run_multi_moderations', '', '', '', 1),
(9, 'run_multi_moderations', '', '', '', 1),
(2, 'see_warnings', '', '', '', 1),
(3, 'see_warnings', '', '', '', 1),
(2, 'warn_members', '', '', '', 1),
(3, 'warn_members', '', '', '', 1),
(1, 'download', '', '', '', 1),
(2, 'download', '', '', '', 1),
(3, 'download', '', '', '', 1),
(4, 'download', '', '', '', 1),
(5, 'download', '', '', '', 1),
(6, 'download', '', '', '', 1),
(7, 'download', '', '', '', 1),
(8, 'download', '', '', '', 1),
(9, 'download', '', '', '', 1),
(2, 'autocomplete_keyword_download_category', '', '', '', 1),
(3, 'autocomplete_keyword_download_category', '', '', '', 1),
(2, 'autocomplete_title_download_category', '', '', '', 1),
(3, 'autocomplete_title_download_category', '', '', '', 1),
(2, 'autocomplete_keyword_download', '', '', '', 1),
(3, 'autocomplete_keyword_download', '', '', '', 1),
(2, 'autocomplete_title_download', '', '', '', 1);
INSERT INTO cms_group_privileges (group_id, privilege, the_page, module_the_name, category_name, the_value) VALUES (3, 'autocomplete_title_download', '', '', '', 1),
(2, 'access_ecommerce_in_test_mode', '', '', '', 1),
(3, 'access_ecommerce_in_test_mode', '', '', '', 1),
(2, 'upload_anything_filedump', '', '', '', 1),
(3, 'upload_anything_filedump', '', '', '', 1),
(1, 'upload_filedump', '', '', '', 1),
(2, 'upload_filedump', '', '', '', 1),
(3, 'upload_filedump', '', '', '', 1),
(4, 'upload_filedump', '', '', '', 1),
(5, 'upload_filedump', '', '', '', 1),
(6, 'upload_filedump', '', '', '', 1),
(7, 'upload_filedump', '', '', '', 1),
(8, 'upload_filedump', '', '', '', 1),
(9, 'upload_filedump', '', '', '', 1),
(2, 'delete_anything_filedump', '', '', '', 1),
(3, 'delete_anything_filedump', '', '', '', 1),
(2, 'may_download_gallery', '', '', '', 1),
(3, 'may_download_gallery', '', '', '', 1),
(2, 'high_personal_gallery_limit', '', '', '', 1),
(3, 'high_personal_gallery_limit', '', '', '', 1),
(2, 'no_personal_gallery_limit', '', '', '', 1),
(3, 'no_personal_gallery_limit', '', '', '', 1),
(2, 'autocomplete_keyword_gallery', '', '', '', 1),
(3, 'autocomplete_keyword_gallery', '', '', '', 1),
(2, 'autocomplete_title_gallery', '', '', '', 1),
(3, 'autocomplete_title_gallery', '', '', '', 1),
(2, 'autocomplete_keyword_image', '', '', '', 1),
(3, 'autocomplete_keyword_image', '', '', '', 1),
(2, 'autocomplete_title_image', '', '', '', 1),
(3, 'autocomplete_title_image', '', '', '', 1),
(2, 'autocomplete_keyword_videos', '', '', '', 1),
(3, 'autocomplete_keyword_videos', '', '', '', 1),
(2, 'autocomplete_title_videos', '', '', '', 1),
(3, 'autocomplete_title_videos', '', '', '', 1),
(2, 'change_newsletter_subscriptions', '', '', '', 1),
(3, 'change_newsletter_subscriptions', '', '', '', 1),
(1, 'use_points', '', '', '', 1),
(2, 'use_points', '', '', '', 1),
(3, 'use_points', '', '', '', 1),
(4, 'use_points', '', '', '', 1);
INSERT INTO cms_group_privileges (group_id, privilege, the_page, module_the_name, category_name, the_value) VALUES (5, 'use_points', '', '', '', 1),
(6, 'use_points', '', '', '', 1),
(7, 'use_points', '', '', '', 1),
(8, 'use_points', '', '', '', 1),
(9, 'use_points', '', '', '', 1),
(2, 'trace_anonymous_points_transactions', '', '', '', 1),
(3, 'trace_anonymous_points_transactions', '', '', '', 1),
(2, 'send_points_to_self', '', '', '', 1),
(3, 'send_points_to_self', '', '', '', 1),
(2, 'view_points_ledger', '', '', '', 1),
(3, 'view_points_ledger', '', '', '', 1),
(1, 'send_points', '', '', '', 1),
(2, 'send_points', '', '', '', 1),
(3, 'send_points', '', '', '', 1),
(4, 'send_points', '', '', '', 1),
(5, 'send_points', '', '', '', 1),
(6, 'send_points', '', '', '', 1),
(7, 'send_points', '', '', '', 1),
(8, 'send_points', '', '', '', 1);
INSERT INTO cms_group_privileges (group_id, privilege, the_page, module_the_name, category_name, the_value) VALUES (9, 'send_points', '', '', '', 1),
(1, 'use_points_escrow', '', '', '', 1),
(2, 'use_points_escrow', '', '', '', 1),
(3, 'use_points_escrow', '', '', '', 1),
(4, 'use_points_escrow', '', '', '', 1),
(5, 'use_points_escrow', '', '', '', 1),
(6, 'use_points_escrow', '', '', '', 1),
(7, 'use_points_escrow', '', '', '', 1),
(8, 'use_points_escrow', '', '', '', 1),
(9, 'use_points_escrow', '', '', '', 1),
(2, 'moderate_points_escrow', '', '', '', 1),
(3, 'moderate_points_escrow', '', '', '', 1),
(2, 'moderate_points', '', '', '', 1),
(3, 'moderate_points', '', '', '', 1),
(2, 'amend_point_transactions', '', '', '', 1),
(3, 'amend_point_transactions', '', '', '', 1),
(2, 'choose_poll', '', '', '', 1),
(3, 'choose_poll', '', '', '', 1),
(2, 'autocomplete_keyword_poll', '', '', '', 1),
(3, 'autocomplete_keyword_poll', '', '', '', 1),
(2, 'autocomplete_title_poll', '', '', '', 1),
(3, 'autocomplete_title_poll', '', '', '', 1),
(2, 'bypass_validation_midrange_content', 'cms_polls', '', '', 0),
(2, 'edit_own_midrange_content', 'cms_polls', '', '', 0),
(3, 'bypass_validation_midrange_content', 'cms_polls', '', '', 0),
(3, 'edit_own_midrange_content', 'cms_polls', '', '', 0),
(4, 'bypass_validation_midrange_content', 'cms_polls', '', '', 0),
(4, 'edit_own_midrange_content', 'cms_polls', '', '', 0),
(5, 'bypass_validation_midrange_content', 'cms_polls', '', '', 0),
(5, 'edit_own_midrange_content', 'cms_polls', '', '', 0),
(6, 'bypass_validation_midrange_content', 'cms_polls', '', '', 0),
(6, 'edit_own_midrange_content', 'cms_polls', '', '', 0),
(7, 'bypass_validation_midrange_content', 'cms_polls', '', '', 0),
(7, 'edit_own_midrange_content', 'cms_polls', '', '', 0),
(8, 'bypass_validation_midrange_content', 'cms_polls', '', '', 0),
(8, 'edit_own_midrange_content', 'cms_polls', '', '', 0),
(9, 'bypass_validation_midrange_content', 'cms_polls', '', '', 0),
(9, 'edit_own_midrange_content', 'cms_polls', '', '', 0),
(2, 'bypass_quiz_repeat_time_restriction', '', '', '', 1),
(3, 'bypass_quiz_repeat_time_restriction', '', '', '', 1),
(2, 'view_others_quiz_results', '', '', '', 1);
INSERT INTO cms_group_privileges (group_id, privilege, the_page, module_the_name, category_name, the_value) VALUES (3, 'view_others_quiz_results', '', '', '', 1),
(2, 'bypass_quiz_timer', '', '', '', 1),
(3, 'bypass_quiz_timer', '', '', '', 1),
(2, 'autocomplete_keyword_quiz', '', '', '', 1),
(3, 'autocomplete_keyword_quiz', '', '', '', 1),
(2, 'autocomplete_title_quiz', '', '', '', 1),
(3, 'autocomplete_title_quiz', '', '', '', 1),
(2, 'use_own_recommend_message', '', '', '', 1),
(3, 'use_own_recommend_message', '', '', '', 1),
(2, 'autocomplete_past_search', '', '', '', 1),
(3, 'autocomplete_past_search', '', '', '', 1),
(2, 'autocomplete_keyword_comcode_page', '', '', '', 1),
(3, 'autocomplete_keyword_comcode_page', '', '', '', 1),
(2, 'autocomplete_title_comcode_page', '', '', '', 1),
(3, 'autocomplete_title_comcode_page', '', '', '', 1),
(2, 'use_sms', '', '', '', 1),
(3, 'use_sms', '', '', '', 1),
(2, 'sms_higher_limit', '', '', '', 1),
(3, 'sms_higher_limit', '', '', '', 1),
(2, 'sms_higher_trigger_limit', '', '', '', 1),
(3, 'sms_higher_trigger_limit', '', '', '', 1),
(1, 'may_report_content', '', '', '', 1),
(2, 'may_report_content', '', '', '', 1),
(3, 'may_report_content', '', '', '', 1),
(4, 'may_report_content', '', '', '', 1),
(5, 'may_report_content', '', '', '', 1),
(6, 'may_report_content', '', '', '', 1),
(7, 'may_report_content', '', '', '', 1),
(8, 'may_report_content', '', '', '', 1),
(9, 'may_report_content', '', '', '', 1),
(2, 'view_others_tickets', '', '', '', 1),
(3, 'view_others_tickets', '', '', '', 1),
(2, 'support_operator', '', '', '', 1),
(3, 'support_operator', '', '', '', 1),
(3, 'bypass_validation_lowrange_content', '', 'forums', '6', 1),
(3, 'bypass_validation_midrange_content', '', 'forums', '6', 1),
(2, 'see_not_validated', '', '', '', 1),
(3, 'see_not_validated', '', '', '', 1),
(2, 'jump_to_not_validated', '', '', '', 1),
(3, 'jump_to_not_validated', '', '', '', 1);
INSERT INTO cms_group_privileges (group_id, privilege, the_page, module_the_name, category_name, the_value) VALUES (2, 'wiki_manage_tree', '', '', '', 1),
(3, 'wiki_manage_tree', '', '', '', 1),
(2, 'set_content_review_settings', '', '', '', 1),
(3, 'set_content_review_settings', '', '', '', 1),
(2, 'autocomplete_keyword_news', '', '', '', 1),
(3, 'autocomplete_keyword_news', '', '', '', 1),
(2, 'autocomplete_title_news', '', '', '', 1),
(3, 'autocomplete_title_news', '', '', '', 1);

DROP TABLE IF EXISTS cms_group_zone_access;
CREATE TABLE cms_group_zone_access (
    zone_name varchar(80) NOT NULL,
    group_id integer NOT NULL,
    PRIMARY KEY (zone_name, group_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_group_zone_access ADD INDEX group_id (group_id);
INSERT INTO cms_group_zone_access (zone_name, group_id) VALUES ('', 1),
('', 2),
('', 3),
('', 4),
('', 5),
('', 6),
('', 7),
('', 8),
('', 9),
('adminzone', 2),
('adminzone', 3),
('cms', 2),
('cms', 3),
('cms', 4),
('cms', 5),
('cms', 6),
('cms', 7),
('cms', 8),
('cms', 9),
('forum', 1),
('forum', 2),
('forum', 3),
('forum', 4),
('forum', 5),
('forum', 6),
('forum', 7),
('forum', 8),
('forum', 9),
('site', 2),
('site', 3),
('site', 4),
('site', 5),
('site', 6),
('site', 7),
('site', 8),
('site', 9);

DROP TABLE IF EXISTS cms_hackattack;
CREATE TABLE cms_hackattack (
    id integer unsigned auto_increment NOT NULL,
    url varchar(255) BINARY NOT NULL,
    data_post longtext NOT NULL,
    user_agent varchar(255) NOT NULL,
    referer_url varchar(255) BINARY NOT NULL,
    user_os varchar(255) NOT NULL,
    member_id integer NOT NULL,
    date_and_time integer unsigned NOT NULL,
    ip varchar(40) NOT NULL,
    reason varchar(80) NOT NULL,
    reason_param_a varchar(255) NOT NULL,
    reason_param_b varchar(255) NOT NULL,
    risk_score integer NOT NULL,
    silent_to_staff_log tinyint(1) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_hackattack ADD INDEX h_date_and_time (date_and_time);

ALTER TABLE cms_hackattack ADD INDEX otherhacksby (ip);

DROP TABLE IF EXISTS cms_hybridauth_content_map;
CREATE TABLE cms_hybridauth_content_map (
    h_content_type varchar(80) NOT NULL,
    h_content_id varchar(80) NOT NULL,
    h_provider varchar(80) NOT NULL,
    h_provider_id varchar(255) NOT NULL,
    h_sync_time integer unsigned NOT NULL,
    PRIMARY KEY (h_content_type, h_content_id, h_provider)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_images;
CREATE TABLE cms_images (
    id integer unsigned auto_increment NOT NULL,
    cat varchar(80) NOT NULL,
    url varchar(255) BINARY NOT NULL,
    the_description longtext NOT NULL,
    allow_rating tinyint(1) NOT NULL,
    allow_comments tinyint NOT NULL,
    allow_trackbacks tinyint(1) NOT NULL,
    notes longtext NOT NULL,
    submitter integer NOT NULL,
    validated tinyint(1) NOT NULL,
    validation_time integer unsigned NULL,
    add_date integer unsigned NOT NULL,
    edit_date integer unsigned NULL,
    image_views integer NOT NULL,
    title longtext NOT NULL,
    the_description__text_parsed longtext NOT NULL,
    the_description__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_images ADD FULLTEXT image_search__combined (the_description,title);

ALTER TABLE cms_images ADD FULLTEXT the_description (the_description);

ALTER TABLE cms_images ADD FULLTEXT title (title);

ALTER TABLE cms_images ADD INDEX category_list (cat);

ALTER TABLE cms_images ADD INDEX ftjoin_dtitle (title(250));

ALTER TABLE cms_images ADD INDEX ftjoin_idescription (the_description(250));

ALTER TABLE cms_images ADD INDEX iadd_date (add_date);

ALTER TABLE cms_images ADD INDEX image_views (image_views);

ALTER TABLE cms_images ADD INDEX i_validated (validated);

ALTER TABLE cms_images ADD INDEX xis (submitter);
INSERT INTO cms_images (id, cat, url, the_description, allow_rating, allow_comments, allow_trackbacks, notes, submitter, validated, validation_time, add_date, edit_date, image_views, title, the_description__text_parsed, the_description__source_user) VALUES (1, 'homepage_hero_slider', 'uploads/galleries/root/homepage_hero_slider/bastei_bridge_8.jpg', '{+START,INCLUDE,GALLERY_HOMEPAGE_HERO_SLIDE}\nHEADLINE=Content Management System for Next-Gen Websites\nSUBLINE=Tired of primitive web systems that don\'t meet your requirements?\nTEXT=With tons of features at your fingertips, let your creativity loose.<br />Welcome your visitors with elegance and flexibility.\nLINK1_URL=https://composr.app/features.htm\nLINK1_TEXT=Discover features\nLINK2_URL=https://composr.app/forum/forumview.htm\nLINK2_TEXT=Join the community\n{+END}\n{$,page hint: no_wysiwyg}', 0, 0, 0, '', 2, 1, NULL, 1754013168, NULL, 0, 'Slider 1', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:3:{i:0;a:5:{i:0;s:33:\\\"tcpfunc_688c1df0637431.74551176_1\\\";i:1;a:1:{s:19:\\\"DIRECTIVE_EMBEDMENT\\\";O:8:\\\"Tempcode\\\":3:{s:18:\\\"code_to_preexecute\\\";a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_27\\\";s:501:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_27\']=\\\"echo \\\\\\\"\\\\\\\\nHEADLINE=Content Management System for Next-Gen Websites\\\\\\\\nSUBLINE=Tired of primitive web systems that don\'t meet your requirements?\\\\\\\\nTEXT=With tons of features at your fingertips, let your creativity loose.<br />Welcome your visitors with elegance and flexibility.\\\\\\\\nLINK1_URL=https://composr.app/features.htm\\\\\\\\nLINK1_TEXT=Discover features\\\\\\\\nLINK2_URL=https://composr.app/forum/forumview.htm\\\\\\\\nLINK2_TEXT=Join the community\\\\\\\\n\\\\\\\";\\\";\\n\\\";}s:9:\\\"seq_parts\\\";a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_27\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}s:8:\\\"codename\\\";s:10:\\\":container\\\";}}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}i:1;a:5:{i:0;s:39:\\\"string_attach_688c1df0637602.34822131_1\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}i:2;a:5:{i:0;s:33:\\\"tcpfunc_688c1df0637977.53866852_1\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:1:{i:0;a:4:{i:0;a:0:{}i:1;i:4;i:2;s:7:\\\"INCLUDE\\\";i:3;a:1:{i:0;s:27:\\\"GALLERY_HOMEPAGE_HERO_SLIDE\\\";}}}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:3:{s:33:\\\"tcpfunc_688c1df0637431.74551176_1\\\";s:700:\\\"\\$tpl_funcs[\'tcpfunc_688c1df0637431.74551176_1\']=\\$KEEP_TPL_FUNCS[\'tcpfunc_688c1df0637431.74551176_1\']=recall_named_function(\'688c1df0637452.35439541\',\'\\$parameters,\\$cl\',\\\"echo ecv(\\\\\\$cl,[],4,\\\\\\\"INCLUDE\\\\\\\",[\\\\\\\"GALLERY_HOMEPAGE_HERO_SLIDE\\\\\\\",\\\\\\\"\\\\\\\\nHEADLINE=Content Management System for Next-Gen Websites\\\\\\\\nSUBLINE=Tired of primitive web systems that don\'t meet your requirements?\\\\\\\\nTEXT=With tons of features at your fingertips, let your creativity loose.<br />Welcome your visitors with elegance and flexibility.\\\\\\\\nLINK1_URL=https://composr.app/features.htm\\\\\\\\nLINK1_TEXT=Discover features\\\\\\\\nLINK2_URL=https://composr.app/forum/forumview.htm\\\\\\\\nLINK2_TEXT=Join the community\\\\\\\\n\\\\\\\",\'vars\'=>\\\\\\$parameters],\\\\\\\"\\\\\\\");\\\");\\\";s:39:\\\"string_attach_688c1df0637602.34822131_1\\\";s:74:\\\"\\$tpl_funcs[\'string_attach_688c1df0637602.34822131_1\']=\\\"echo \\\\\\\"<br />\\\\\\\";\\\";\\n\\\";s:33:\\\"tcpfunc_688c1df0637977.53866852_1\\\";s:181:\\\"\\$tpl_funcs[\'tcpfunc_688c1df0637977.53866852_1\']=\\$KEEP_TPL_FUNCS[\'tcpfunc_688c1df0637977.53866852_1\']=recall_named_function(\'688c1df0637992.96360751\',\'\\$parameters,\\$cl\',\\\"echo \\\\\\\"\\\\\\\";\\\");\\\";}}\");\n', 2),
(2, 'homepage_hero_slider', 'uploads/galleries/root/homepage_hero_slider/rustic_8.jpg', '{+START,INCLUDE,GALLERY_HOMEPAGE_HERO_SLIDE}\nHEADLINE=Leader In Design\nSUBLINE=Form and Function Revolutionised!\nTEXT=Our awesome bundled theme will help users, designers, developers, and companies create websites for their startups quickly and easily.\nLINK1_URL=https://composr.app/features.htm\nLINK1_TEXT=Discover features\nLINK2_URL=https://composr.app/forum/forumview.htm\nLINK2_TEXT=Join the community\n{+END}\n{$,page hint: no_wysiwyg}', 0, 0, 0, '', 2, 1, NULL, 1754013168, NULL, 0, 'Slider 2', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:3:{i:0;a:5:{i:0;s:33:\\\"tcpfunc_688c1df0d5edd1.91535130_1\\\";i:1;a:1:{s:19:\\\"DIRECTIVE_EMBEDMENT\\\";O:8:\\\"Tempcode\\\":3:{s:18:\\\"code_to_preexecute\\\";a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_28\\\";s:446:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_28\']=\\\"echo \\\\\\\"\\\\\\\\nHEADLINE=Leader In Design\\\\\\\\nSUBLINE=Form and Function Revolutionised!\\\\\\\\nTEXT=Our awesome bundled theme will help users, designers, developers, and companies create websites for their startups quickly and easily.\\\\\\\\nLINK1_URL=https://composr.app/features.htm\\\\\\\\nLINK1_TEXT=Discover features\\\\\\\\nLINK2_URL=https://composr.app/forum/forumview.htm\\\\\\\\nLINK2_TEXT=Join the community\\\\\\\\n\\\\\\\";\\\";\\n\\\";}s:9:\\\"seq_parts\\\";a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_28\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}s:8:\\\"codename\\\";s:10:\\\":container\\\";}}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}i:1;a:5:{i:0;s:39:\\\"string_attach_688c1df0637602.34822131_2\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}i:2;a:5:{i:0;s:33:\\\"tcpfunc_688c1df0d5f384.19855817_1\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:1:{i:0;a:4:{i:0;a:0:{}i:1;i:4;i:2;s:7:\\\"INCLUDE\\\";i:3;a:1:{i:0;s:27:\\\"GALLERY_HOMEPAGE_HERO_SLIDE\\\";}}}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:3:{s:33:\\\"tcpfunc_688c1df0d5edd1.91535130_1\\\";s:645:\\\"\\$tpl_funcs[\'tcpfunc_688c1df0d5edd1.91535130_1\']=\\$KEEP_TPL_FUNCS[\'tcpfunc_688c1df0d5edd1.91535130_1\']=recall_named_function(\'688c1df0d5ee20.88147771\',\'\\$parameters,\\$cl\',\\\"echo ecv(\\\\\\$cl,[],4,\\\\\\\"INCLUDE\\\\\\\",[\\\\\\\"GALLERY_HOMEPAGE_HERO_SLIDE\\\\\\\",\\\\\\\"\\\\\\\\nHEADLINE=Leader In Design\\\\\\\\nSUBLINE=Form and Function Revolutionised!\\\\\\\\nTEXT=Our awesome bundled theme will help users, designers, developers, and companies create websites for their startups quickly and easily.\\\\\\\\nLINK1_URL=https://composr.app/features.htm\\\\\\\\nLINK1_TEXT=Discover features\\\\\\\\nLINK2_URL=https://composr.app/forum/forumview.htm\\\\\\\\nLINK2_TEXT=Join the community\\\\\\\\n\\\\\\\",\'vars\'=>\\\\\\$parameters],\\\\\\\"\\\\\\\");\\\");\\\";s:39:\\\"string_attach_688c1df0637602.34822131_2\\\";s:74:\\\"\\$tpl_funcs[\'string_attach_688c1df0637602.34822131_2\']=\\\"echo \\\\\\\"<br />\\\\\\\";\\\";\\n\\\";s:33:\\\"tcpfunc_688c1df0d5f384.19855817_1\\\";s:181:\\\"\\$tpl_funcs[\'tcpfunc_688c1df0d5f384.19855817_1\']=\\$KEEP_TPL_FUNCS[\'tcpfunc_688c1df0d5f384.19855817_1\']=recall_named_function(\'688c1df0d5f3b2.15627974\',\'\\$parameters,\\$cl\',\\\"echo \\\\\\\"\\\\\\\";\\\");\\\";}}\");\n', 2),
(3, 'homepage_hero_slider', 'uploads/galleries/root/homepage_hero_slider/waterfall_8.jpg', '{+START,INCLUDE,GALLERY_HOMEPAGE_HERO_SLIDE}\nHEADLINE=Think Ahead\nSUBLINE=Boost your online business growth!\nTEXT=Fully Open Source, Composr is built on a tradition of software freedom and empowering regular people.\nLINK1_URL=https://composr.app/features.htm\nLINK1_TEXT=Discover features\nLINK2_URL=https://composr.app/forum/forumview.htm\nLINK2_TEXT=Join the community\n{+END}\n{$,page hint: no_wysiwyg}', 0, 0, 0, '', 2, 1, NULL, 1754013169, NULL, 0, 'Slider 3', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:3:{i:0;a:5:{i:0;s:33:\\\"tcpfunc_688c1df13e9197.23917540_1\\\";i:1;a:1:{s:19:\\\"DIRECTIVE_EMBEDMENT\\\";O:8:\\\"Tempcode\\\":3:{s:18:\\\"code_to_preexecute\\\";a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_29\\\";s:409:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_29\']=\\\"echo \\\\\\\"\\\\\\\\nHEADLINE=Think Ahead\\\\\\\\nSUBLINE=Boost your online business growth!\\\\\\\\nTEXT=Fully Open Source, Composr is built on a tradition of software freedom and empowering regular people.\\\\\\\\nLINK1_URL=https://composr.app/features.htm\\\\\\\\nLINK1_TEXT=Discover features\\\\\\\\nLINK2_URL=https://composr.app/forum/forumview.htm\\\\\\\\nLINK2_TEXT=Join the community\\\\\\\\n\\\\\\\";\\\";\\n\\\";}s:9:\\\"seq_parts\\\";a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_29\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}s:8:\\\"codename\\\";s:10:\\\":container\\\";}}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}i:1;a:5:{i:0;s:39:\\\"string_attach_688c1df0637602.34822131_3\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}i:2;a:5:{i:0;s:33:\\\"tcpfunc_688c1df13e9773.37055152_1\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:1:{i:0;a:4:{i:0;a:0:{}i:1;i:4;i:2;s:7:\\\"INCLUDE\\\";i:3;a:1:{i:0;s:27:\\\"GALLERY_HOMEPAGE_HERO_SLIDE\\\";}}}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:3:{s:33:\\\"tcpfunc_688c1df13e9197.23917540_1\\\";s:608:\\\"\\$tpl_funcs[\'tcpfunc_688c1df13e9197.23917540_1\']=\\$KEEP_TPL_FUNCS[\'tcpfunc_688c1df13e9197.23917540_1\']=recall_named_function(\'688c1df13e91d7.78192520\',\'\\$parameters,\\$cl\',\\\"echo ecv(\\\\\\$cl,[],4,\\\\\\\"INCLUDE\\\\\\\",[\\\\\\\"GALLERY_HOMEPAGE_HERO_SLIDE\\\\\\\",\\\\\\\"\\\\\\\\nHEADLINE=Think Ahead\\\\\\\\nSUBLINE=Boost your online business growth!\\\\\\\\nTEXT=Fully Open Source, Composr is built on a tradition of software freedom and empowering regular people.\\\\\\\\nLINK1_URL=https://composr.app/features.htm\\\\\\\\nLINK1_TEXT=Discover features\\\\\\\\nLINK2_URL=https://composr.app/forum/forumview.htm\\\\\\\\nLINK2_TEXT=Join the community\\\\\\\\n\\\\\\\",\'vars\'=>\\\\\\$parameters],\\\\\\\"\\\\\\\");\\\");\\\";s:39:\\\"string_attach_688c1df0637602.34822131_3\\\";s:74:\\\"\\$tpl_funcs[\'string_attach_688c1df0637602.34822131_3\']=\\\"echo \\\\\\\"<br />\\\\\\\";\\\";\\n\\\";s:33:\\\"tcpfunc_688c1df13e9773.37055152_1\\\";s:181:\\\"\\$tpl_funcs[\'tcpfunc_688c1df13e9773.37055152_1\']=\\$KEEP_TPL_FUNCS[\'tcpfunc_688c1df13e9773.37055152_1\']=recall_named_function(\'688c1df13e97a3.57146744\',\'\\$parameters,\\$cl\',\\\"echo \\\\\\\"\\\\\\\";\\\");\\\";}}\");\n', 2),
(4, 'root', 'uploads/galleries/root/funny-pictures-cat-has-trophy-wife_8.jpg', 'TROPHY WIFE', 1, 1, 1, '', 1, 1, NULL, 1754013172, NULL, 0, 'TROPHY WIFE', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_33\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_33\\\";s:80:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_33\']=\\\"echo \\\\\\\"TROPHY WIFE\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(5, 'root', 'uploads/galleries/root/funny-pictures-cat-is-on-steroids_8.jpg', 'STREROIDS', 1, 1, 1, '', 1, 1, NULL, 1754013172, NULL, 0, 'STREROIDS', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_34\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_34\\\";s:78:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_34\']=\\\"echo \\\\\\\"STREROIDS\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(6, 'root', 'uploads/galleries/root/funny-pictures-cat-is-a-hoarder_8.jpg', 'Tonight on A&E', 1, 1, 1, '', 1, 1, NULL, 1754013172, NULL, 0, 'Tonight on A&E', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:39:\\\"string_attach_688c1df0637602.34822131_4\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:39:\\\"string_attach_688c1df0637602.34822131_4\\\";s:86:\\\"\\$tpl_funcs[\'string_attach_688c1df0637602.34822131_4\']=\\\"echo \\\\\\\"Tonight on A&amp;E\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(7, 'root', 'uploads/galleries/root/funny-pictures-cat-winks-at-you_8.jpg', 'Hey there', 1, 1, 1, '', 1, 1, NULL, 1754013172, NULL, 0, 'Hey there', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_35\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_35\\\";s:78:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_35\']=\\\"echo \\\\\\\"Hey there\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(8, 'root', 'uploads/galleries/root/funny-pictures-cat-does-not-see-your-point_8.jpg', '...your point?', 1, 1, 1, '', 1, 1, NULL, 1754013172, NULL, 0, '...your point?', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_36\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_36\\\";s:83:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_36\']=\\\"echo \\\\\\\"...your point?\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(9, 'root', 'uploads/galleries/root/funny-pictures-cat-asks-you-to-pay-fine_8.jpg', 'just pay the fine', 1, 1, 1, '', 1, 1, NULL, 1754013172, NULL, 0, 'just pay the fine', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_37\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_37\\\";s:86:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_37\']=\\\"echo \\\\\\\"just pay the fine\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(10, 'root', 'uploads/galleries/root/funny-pictures-cat-is-a-people-lady_8.jpg', 'Walter never showed up ...', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'Walter never showed up ...', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_38\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_38\\\";s:95:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_38\']=\\\"echo \\\\\\\"Walter never showed up ...\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(11, 'root', 'uploads/galleries/root/funny-pictures-cat-looks-like-a-vase_8.jpg', 'Feline Dynasty', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'Feline Dynasty', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_39\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_39\\\";s:83:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_39\']=\\\"echo \\\\\\\"Feline Dynasty\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(12, 'root', 'uploads/galleries/root/funny-pictures-cat-can-poop-rainbows_8.jpg', 'And I can poop dem too ...', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'And I can poop dem too ...', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_40\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_40\\\";s:95:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_40\']=\\\"echo \\\\\\\"And I can poop dem too ...\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(13, 'root', 'uploads/galleries/root/funny-pictures-cat-does-math_8.jpg', 'You and your wife have 16 kittens ...', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'You and your wife have 16 kittens ...', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_41\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_41\\\";s:106:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_41\']=\\\"echo \\\\\\\"You and your wife have 16 kittens ...\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(14, 'root', 'uploads/galleries/root/funny-pictures-kittens-dispose-of-boyfriend_8.jpg', 'Itteh Bitteh Kitteh Boyfriend Disposal Committeh', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'Itteh Bitteh Kitteh Boyfriend Disposal Committeh', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_42\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_42\\\";s:117:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_42\']=\\\"echo \\\\\\\"Itteh Bitteh Kitteh Boyfriend Disposal Committeh\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(15, 'root', 'uploads/galleries/root/funny-pictures-cat-has-had-fun_8.jpg', 'Now DAT', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'Now DAT', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_43\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_43\\\";s:76:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_43\']=\\\"echo \\\\\\\"Now DAT\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(16, 'root', 'uploads/galleries/root/funny-pictures-kitten-tries-to-stay-neutral_8.jpg', 'Mah bottom is twyin to take ovuh', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'Mah bottom is twyin to take ovuh', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_44\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_44\\\";s:101:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_44\']=\\\"echo \\\\\\\"Mah bottom is twyin to take ovuh\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(17, 'root', 'uploads/galleries/root/funny-pictures-cat-decides-what-to-do_8.jpg', 'Crap! Here he comes...!', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'Crap! Here he comes...!', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_45\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_45\\\";s:92:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_45\']=\\\"echo \\\\\\\"Crap! Here he comes...!\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(18, 'root', 'uploads/galleries/root/funny-pictures-cat-looks-like-boots_8.jpg', 'GET GLASSES!', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'GET GLASSES!', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_46\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_46\\\";s:81:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_46\']=\\\"echo \\\\\\\"GET GLASSES!\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(19, 'root', 'uploads/galleries/root/funny-pictures-cats-have-war_8.jpg', 'How wars start.', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'How wars start.', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_47\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_47\\\";s:84:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_47\']=\\\"echo \\\\\\\"How wars start.\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(20, 'root', 'uploads/galleries/root/funny-pictures-cat-is-stuck-in-drawer_8.jpg', 'Dog can\'t take a joke.', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'Dog can\'t take a joke.', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:39:\\\"string_attach_688c1df0637602.34822131_5\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:39:\\\"string_attach_688c1df0637602.34822131_5\\\";s:95:\\\"\\$tpl_funcs[\'string_attach_688c1df0637602.34822131_5\']=\\\"echo \\\\\\\"Dog can&#039;t take a joke.\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(21, 'root', 'uploads/galleries/root/funny-pictures-kitten-drops-a-nickel-under-couch_8.jpg', 'I drop a nikel under der.', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'I drop a nikel under der.', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_48\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_48\\\";s:94:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_48\']=\\\"echo \\\\\\\"I drop a nikel under der.\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(22, 'root', 'uploads/galleries/root/funny-pictures-cat-asks-you-for-a-favor_8.jpg', 'Do me a favor', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'Do me a favor', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_49\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_49\\\";s:82:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_49\']=\\\"echo \\\\\\\"Do me a favor\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(23, 'root', 'uploads/galleries/root/funny-pictures-kitten-fixes-puppy_8.jpg', 'I fix puppy so now he listen.', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'I fix puppy so now he listen.', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_50\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_50\\\";s:98:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_50\']=\\\"echo \\\\\\\"I fix puppy so now he listen.\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(24, 'root', 'uploads/galleries/root/funny-pictures-cat-is-very-comfortable_8.jpg', 'i is sooooooooo comfurbals...', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'i is sooooooooo comfurbals...', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_51\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_51\\\";s:98:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_51\']=\\\"echo \\\\\\\"i is sooooooooo comfurbals...\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(25, 'root', 'uploads/galleries/root/funny-pictures-kitten-ends-meeting2_8.jpg', 'This meeting is over.', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'This meeting is over.', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_52\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_52\\\";s:90:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_52\']=\\\"echo \\\\\\\"This meeting is over.\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(26, 'root', 'uploads/galleries/root/funny-pictures-cats-are-in-a-musical_8.jpg', 'When you\'re a cat ...', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'When you\'re a cat ...', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:2:{i:0;a:5:{i:0;s:39:\\\"string_attach_688c1df0637602.34822131_6\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}i:1;a:5:{i:0;s:39:\\\"string_attach_688c1df0637602.34822131_7\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:2:{s:39:\\\"string_attach_688c1df0637602.34822131_6\\\";s:91:\\\"\\$tpl_funcs[\'string_attach_688c1df0637602.34822131_6\']=\\\"echo \\\\\\\"When you&#039;re a cat \\\\\\\";\\\";\\n\\\";s:39:\\\"string_attach_688c1df0637602.34822131_7\\\";s:76:\\\"\\$tpl_funcs[\'string_attach_688c1df0637602.34822131_7\']=\\\"echo \\\\\\\"&hellip;\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(27, 'root', 'uploads/galleries/root/funny-pictures-cat-hates-your-tablecloth_8.jpg', 'No, thanks.', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'No, thanks.', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_53\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_53\\\";s:80:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_53\']=\\\"echo \\\\\\\"No, thanks.\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(28, 'root', 'uploads/galleries/root/funny-pictures-cat-eyes-steak_8.jpg', 'is it dun yet?', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'is it dun yet?', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_54\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_54\\\";s:83:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_54\']=\\\"echo \\\\\\\"is it dun yet?\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(29, 'root', 'uploads/galleries/root/funny-pictures-basement-cat-has-pink-sheets_8.jpg', 'PINK??!', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'PINK??!', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_55\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_55\\\";s:76:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_55\']=\\\"echo \\\\\\\"PINK??!\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(30, 'root', 'uploads/galleries/root/funny-pictures-kittens-yell-at-eachother_8.jpg', 'WAIT YOUR TURN!', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'WAIT YOUR TURN!', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_56\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_56\\\";s:84:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_56\']=\\\"echo \\\\\\\"WAIT YOUR TURN!\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(31, 'root', 'uploads/galleries/root/funny-pictures-cat-sits-in-box_8.jpg', 'Sittin in ur mails', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'Sittin in ur mails', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_57\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_57\\\";s:87:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_57\']=\\\"echo \\\\\\\"Sittin in ur mails\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(32, 'root', 'uploads/galleries/root/funny-pictures-cat-has-a-beatle_8.jpg', 'GEORGE', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'GEORGE', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_58\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_58\\\";s:75:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_58\']=\\\"echo \\\\\\\"GEORGE\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(33, 'root', 'uploads/galleries/root/funny-pictures-cat-sits-on-your-laptop_8.jpg', 'Rebutting ...', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'Rebutting ...', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_59\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_59\\\";s:82:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_59\']=\\\"echo \\\\\\\"Rebutting ...\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(34, 'root', 'uploads/galleries/root/funny-pictures-cat-has-a-close-encounter_8.jpg', 'CLOSE  ENCOUNTRES ...', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'CLOSE  ENCOUNTRES ...', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:2:{i:0;a:5:{i:0;s:39:\\\"string_attach_688c1df0637602.34822131_8\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}i:1;a:5:{i:0;s:39:\\\"string_attach_688c1df0637602.34822131_9\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:2:{s:39:\\\"string_attach_688c1df0637602.34822131_8\\\";s:91:\\\"\\$tpl_funcs[\'string_attach_688c1df0637602.34822131_8\']=\\\"echo \\\\\\\"CLOSE &nbsp;ENCOUNTRES \\\\\\\";\\\";\\n\\\";s:39:\\\"string_attach_688c1df0637602.34822131_9\\\";s:76:\\\"\\$tpl_funcs[\'string_attach_688c1df0637602.34822131_9\']=\\\"echo \\\\\\\"&hellip;\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(35, 'root', 'uploads/galleries/root/funny-pictures-ridiculous-poses-moddles_8.jpg', 'Ridiculous poses', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'Ridiculous poses', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_60\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_60\\\";s:85:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_60\']=\\\"echo \\\\\\\"Ridiculous poses\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(36, 'root', 'uploads/galleries/root/funny-pictures-cat-is-a-doctor_8.jpg', 'Dr. House cat...', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'Dr. House cat...', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_61\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_61\\\";s:85:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_61\']=\\\"echo \\\\\\\"Dr. House cat...\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(37, 'root', 'uploads/galleries/root/funny-pictures-cat-pounces-on-deer_8.jpg', 'National Geographic', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'National Geographic', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_62\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_62\\\";s:88:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_62\']=\\\"echo \\\\\\\"National Geographic\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(38, 'root', 'uploads/galleries/root/funny-pictures-fish-and-cat-judge-your-outfit_8.jpg', 'Bad outfit', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'Bad outfit', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_63\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_63\\\";s:79:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_63\']=\\\"echo \\\\\\\"Bad outfit\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(39, 'root', 'uploads/galleries/root/funny-pictures-cat-comes-to-save-day_8.jpg', 'Here I come to save the day!!', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'Here I come to save the day!!', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_64\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_64\\\";s:98:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_64\']=\\\"echo \\\\\\\"Here I come to save the day!!\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(40, 'root', 'uploads/galleries/root/funny-pictures-cat-ok-captain-obvious_8.jpg', 'Okay, Captain Obvious', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'Okay, Captain Obvious', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_65\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_65\\\";s:90:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_65\']=\\\"echo \\\\\\\"Okay, Captain Obvious\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(41, 'root', 'uploads/galleries/root/funny-pictures-cat-kermit-was-about_8.jpg', 'Kermit makes a discovery', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'Kermit makes a discovery', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_66\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_66\\\";s:93:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_66\']=\\\"echo \\\\\\\"Kermit makes a discovery\\\\\\\";\\\";\\n\\\";}}\");\n', 1);
INSERT INTO cms_images (id, cat, url, the_description, allow_rating, allow_comments, allow_trackbacks, notes, submitter, validated, validation_time, add_date, edit_date, image_views, title, the_description__text_parsed, the_description__source_user) VALUES (42, 'root', 'uploads/galleries/root/funny-pictures-cat-ai-calld-jenny-craig_8.jpg', 'Jenny Craig', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'Jenny Craig', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_67\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_67\\\";s:80:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_67\']=\\\"echo \\\\\\\"Jenny Craig\\\\\\\";\\\";\\n\\\";}}\");\n', 1),
(43, 'root', 'uploads/galleries/root/funny-pictures-cat-special-delivery_8.jpg', 'Special Delivery', 1, 1, 1, '', 1, 1, NULL, 1754013173, NULL, 0, 'Special Delivery', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_688c1deca186b1.77623203_68\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;b:0;i:4;a:1:{s:40:\\\"string_attach_688c1deca186b1.77623203_68\\\";s:85:\\\"\\$tpl_funcs[\'string_attach_688c1deca186b1.77623203_68\']=\\\"echo \\\\\\\"Special Delivery\\\\\\\";\\\";\\n\\\";}}\");\n', 1);

DROP TABLE IF EXISTS cms_import_id_remap;
CREATE TABLE cms_import_id_remap (
    id_old varchar(80) NOT NULL,
    id_new integer NOT NULL,
    id_type varchar(80) NOT NULL,
    id_session varchar(80) NOT NULL,
    PRIMARY KEY (id_old, id_type, id_session)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_import_parts_done;
CREATE TABLE cms_import_parts_done (
    id integer unsigned auto_increment NOT NULL,
    imp_id varchar(255) NOT NULL,
    imp_session varchar(80) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_import_session;
CREATE TABLE cms_import_session (
    imp_session varchar(80) NOT NULL,
    imp_old_base_dir varchar(255) NOT NULL,
    imp_db_host varchar(80) NOT NULL,
    imp_db_name varchar(80) NOT NULL,
    imp_db_user varchar(80) NOT NULL,
    imp_db_table_prefix varchar(80) NOT NULL,
    imp_hook varchar(80) NOT NULL,
    imp_refresh_interval integer NOT NULL,
    PRIMARY KEY (imp_session)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_incoming_uploads;
CREATE TABLE cms_incoming_uploads (
    id integer unsigned auto_increment NOT NULL,
    i_submitter integer NOT NULL,
    i_date_and_time integer unsigned NOT NULL,
    i_orig_filename varchar(255) NOT NULL,
    i_save_url varchar(255) BINARY NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_ip_country;
CREATE TABLE cms_ip_country (
    id integer unsigned auto_increment NOT NULL,
    begin_num integer unsigned NOT NULL,
    end_num integer unsigned NOT NULL,
    country varchar(255) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_ip_country ADD INDEX begin_num (begin_num);

ALTER TABLE cms_ip_country ADD INDEX end_num (end_num);

DROP TABLE IF EXISTS cms_leader_board;
CREATE TABLE cms_leader_board (
    lb_leader_board_id integer NOT NULL,
    lb_member integer NOT NULL,
    lb_date_and_time integer unsigned NOT NULL,
    lb_points integer NOT NULL,
    lb_rank integer NOT NULL,
    lb_voting_power real NULL,
    lb_voting_control real NULL,
    PRIMARY KEY (lb_leader_board_id, lb_member, lb_date_and_time)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_leader_board ADD INDEX lb_date_and_time (lb_date_and_time);

ALTER TABLE cms_leader_board ADD INDEX lb_leader_board_id (lb_leader_board_id);

ALTER TABLE cms_leader_board ADD INDEX lb_rank (lb_leader_board_id,lb_date_and_time,lb_rank);

DROP TABLE IF EXISTS cms_leader_boards;
CREATE TABLE cms_leader_boards (
    id integer unsigned auto_increment NOT NULL,
    lb_title varchar(255) NOT NULL,
    lb_creation_date_and_time integer unsigned NOT NULL,
    lb_type varchar(255) NOT NULL,
    lb_member_count integer NOT NULL,
    lb_timeframe varchar(255) NOT NULL,
    lb_rolling tinyint(1) NOT NULL,
    lb_include_staff tinyint(1) NOT NULL,
    lb_calculate_voting_power tinyint(1) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_leader_boards_groups;
CREATE TABLE cms_leader_boards_groups (
    lb_leader_board_id integer NOT NULL,
    lb_group integer NOT NULL,
    PRIMARY KEY (lb_leader_board_id, lb_group)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_logged_mail_messages;
CREATE TABLE cms_logged_mail_messages (
    id integer unsigned auto_increment NOT NULL,
    m_subject longtext NOT NULL,
    m_message longtext NOT NULL,
    m_message_extended longtext NOT NULL,
    m_to_email longtext NOT NULL,
    m_extra_cc_addresses longtext NOT NULL,
    m_extra_bcc_addresses longtext NOT NULL,
    m_join_time integer unsigned NULL,
    m_to_name longtext NOT NULL,
    m_from_email varchar(255) NOT NULL,
    m_from_name varchar(255) NOT NULL,
    m_priority tinyint NOT NULL,
    m_attachments longtext NOT NULL,
    m_no_cc tinyint(1) NOT NULL,
    m_as_member integer NOT NULL,
    m_as_admin tinyint(1) NOT NULL,
    m_in_html tinyint(1) NOT NULL,
    m_date_and_time integer unsigned NOT NULL,
    m_member_id integer NOT NULL,
    m_url varchar(255) BINARY NOT NULL,
    m_queued tinyint(1) NOT NULL,
    m_template varchar(80) NOT NULL,
    m_sender_email varchar(255) NOT NULL,
    m_plain_subject tinyint(1) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_logged_mail_messages ADD INDEX combo (m_date_and_time,m_queued);

ALTER TABLE cms_logged_mail_messages ADD INDEX m_as_member (m_as_member);

ALTER TABLE cms_logged_mail_messages ADD INDEX queued (m_queued);

ALTER TABLE cms_logged_mail_messages ADD INDEX recentmessages (m_date_and_time);

DROP TABLE IF EXISTS cms_match_key_messages;
CREATE TABLE cms_match_key_messages (
    id integer unsigned auto_increment NOT NULL,
    k_message longtext NOT NULL,
    k_match_key varchar(255) NOT NULL,
    k_message__text_parsed longtext NOT NULL,
    k_message__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_match_key_messages ADD FULLTEXT k_message (k_message);

DROP TABLE IF EXISTS cms_member_category_access;
CREATE TABLE cms_member_category_access (
    module_the_name varchar(80) NOT NULL,
    category_name varchar(80) NOT NULL,
    member_id integer NOT NULL,
    active_until integer unsigned NULL,
    PRIMARY KEY (module_the_name, category_name, member_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_member_category_access ADD INDEX active_until (active_until);

ALTER TABLE cms_member_category_access ADD INDEX mcamember_id (member_id);

ALTER TABLE cms_member_category_access ADD INDEX mcaname (module_the_name,category_name);

DROP TABLE IF EXISTS cms_member_page_access;
CREATE TABLE cms_member_page_access (
    page_name varchar(80) NOT NULL,
    zone_name varchar(80) NOT NULL,
    member_id integer NOT NULL,
    active_until integer unsigned NULL,
    PRIMARY KEY (page_name, zone_name, member_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;
ALTER TABLE cms_member_page_access ADD INDEX active_until (active_until);

ALTER TABLE cms_member_page_access ADD INDEX mzamember_id (member_id);

ALTER TABLE cms_member_page_access ADD INDEX mzaname (page_name,zone_name);
