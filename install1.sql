DROP TABLE IF EXISTS cms_actionlogs;

CREATE TABLE cms_actionlogs (
    date_and_time integer unsigned NOT NULL,
    ip varchar(40) NOT NULL,
    member_id integer NOT NULL,
    param_b varchar(255) NOT NULL,
    param_a varchar(80) NOT NULL,
    the_type varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_actionlogs ADD INDEX aip (ip);

ALTER TABLE cms_actionlogs ADD INDEX athe_type (the_type);

ALTER TABLE cms_actionlogs ADD INDEX ts (date_and_time);

ALTER TABLE cms_actionlogs ADD INDEX xas (member_id);

DROP TABLE IF EXISTS cms_addons;

CREATE TABLE cms_addons (
    addon_copyright_attribution varchar(255) NOT NULL,
    addon_install_time integer unsigned NOT NULL,
    addon_description longtext NOT NULL,
    addon_licence varchar(255) NOT NULL,
    addon_category varchar(255) NOT NULL,
    addon_name varchar(255) NOT NULL,
    addon_author varchar(255) NOT NULL,
    addon_organisation varchar(255) NOT NULL,
    addon_version varchar(255) NOT NULL,
    PRIMARY KEY (addon_name)
) CHARACTER SET=utf8mb4 engine=MyISAM;

INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('actionlog', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Audit-trail functionality.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('aggregate_types', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Define complex aggregate types in XML, and spawn them.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('apache_config_files', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Sample .htaccess files to help achieve optimal configuration on the Apache web server.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('authors', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Certain kinds of content can have authors instead of submitters (e.g. \'ocProducts\'). The authors may be independently described and searched under.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('awards', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Pick out content for featuring.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('backup', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Perform incremental or full backups of files and the database. Supports scheduling.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('banners', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'An advanced banner system, with support for multiple banner rotations, commercial banner campaigns, and webring-style systems. Support for graphical, text, and flash banners. Hotword activation support.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('bookmarks', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Allow members to bookmark screens of the website. As the bookmarks are tied to their member profile they can access them from any computer they can log in on.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('breadcrumbs', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Advanced breadcrumb editing.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('calendar', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'An advanced community calendar.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('captcha', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Stop spam-bots from performing actions on the website.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('catalogues', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Describe your own custom data record types (by choosing and configuring fields) and populate with records. Supports tree structures, and most standard Composr features (e.g. ratings).', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('chat', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Chatrooms and instant messaging.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('cns_avatars', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'A selection of avatars for Conversr', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('cns_cartoon_avatars', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'A selection of avatars for Conversr (sketched characters)', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('cns_clubs', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Clubs for members, each of which comes with a forum.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('cns_contact_member', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Off-site e-mailing of members (more private, and may be used by guests).', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('cns_cpfs', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Custom profile fields, so members may save additional details. If this is uninstalled any existing custom profile fields will remain in the system.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('cns_forum', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'The Conversr forum- a modern advanced forum for members to interact on.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('cns_member_avatars', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Member avatars.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('cns_member_photos', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Member photos.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('cns_member_titles', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Member titles.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('cns_multi_moderations', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Multi-moderations for the Conversr forum.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('cns_post_templates', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Post templates for the Conversr forum.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('cns_reported_posts', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Post reporting with the Conversr forum.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('cns_signatures', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Member signatures.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('cns_thematic_avatars', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'A selection of avatars for Conversr', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('cns_warnings', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Member warnings and punishment.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('code_editor', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'For programmers- A simple editor for editing Composr code files, with support for override support and saving via FTP.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('collaboration_zone', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Collaboration Zone.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('commandr', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'A command-line environment for managing your website, designed for Linux/Unix lovers.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('content_privacy', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Allows users to specify privacy level for their content.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('content_reviews', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Regularly review content for accuracy.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', '(Core Composr code)', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_abstract_components', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Core rendering functionality.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_abstract_interfaces', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Core rendering functionality.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_addon_management', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Install or uninstall addons.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_adminzone_dashboard', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'The dashboard tools shown in the Admin Zone.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_cleanup_tools', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Behind-the-scenes maintenance tasks.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_cns', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'The Composr member/usergroup system.', 1554920594);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_comcode_pages', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Manage new pages on the website, known as Comcode pages.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_configuration', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Set configuration options.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_database_drivers', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'The code layer that binds the software to one of various different kinds of database software.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_feedback_features', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Features for user interaction with content.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_fields', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', '(Core fields API)', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_form_interfaces', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Core rendering functionality for forms.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_forum_drivers', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'The code layer that binds the software to one of various different forum/member systems.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_graphic_text', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Core rendering functionality for imagery.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_html_abstractions', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Core rendering functionality.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_language_editing', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Translate the software, or just change what it says for stylistic reasons.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_menus', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Edit menus.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_notifications', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Sends out action-triggered notifications to members listening to them.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_permission_management', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Manage permissions.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_primary_layout', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Core rendering functionality.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_rich_media', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Comcode and attachments.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_themeing', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Themeing the website, via CSS, HTML, and images.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_upgrader', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'The upgrader code.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_webstandards', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Web Standards checking tools.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('core_zone_editor', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Manage zones (sub-sites).', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('counting_blocks', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Blocks for hit counters, and count-downs.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('custom_comcode', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Create new Comcode tags.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('debrand', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Allow easy debranding of the website software.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('downloads', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Host a downloads directory.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('ecommerce', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'eCommerce infrastructure, with support for digital purchase and usergroup subscriptions. Accounting functionality.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('errorlog', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Log of errors that have happened on the website.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('failover', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Advanced system to detect if the site goes down, and provide an automatic fallback.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('filedump', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'File/media library, for use in attachments or for general ad-hoc sharing.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('forum_blocks', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Blocks to draw forum posts and topics into the main website.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('galleries', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Galleries, including support for video galleries, and member personal galleries.', 1554920595);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('google_appengine', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Support for deploying to Google App Engine (for developers).', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('help_page', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'A simple website help page. Note that removing this will not remove the menu link automatically.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('hphp_buildkit', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Scripts for supporting Facebook\'s HHVM.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('import', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Switch to Composr from other software. This addon provides the architecture for importing, and a number of prewritten importers.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('installer', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'The installer files (can be removed immediately after installing; in fact Composr makes you remove install.php manually).', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('language_block', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'A block to allow visitors to choose their language.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('ldap', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Support for integrating Conversr with an LDAP server, so usergroup and members can be the same as those already on the network', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('linux_helper_scripts', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Bash shell scripts to help configure permissions on Linux/Unix servers.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('match_key_permissions', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Match-key-permissions allow advanced setting of permissions on a screen-by-screen basis.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('msn', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Features to support multi-site-networks (networks of linked sites that usually share a common member system).', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('news', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'News and blogging.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('news_shared', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', '(Common files needed for RSS and News addons)', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('newsletter', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Support for users to join newsletters, and for the staff to send out newsletters to subscribers, and to specific usergroups.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('page_management', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Manage pages on the website.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('phpinfo', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Access PHP configurational information from inside Composr.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('points', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Allow members to accumulate points via a number of configurable activities, as well as exchange points with each other. Points act as a ranking system as well as a virtual currency.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('pointstore', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Provide a number of virtual products to your members in exchange for the points they have accumulated by their activity', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('polls', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'A poll (voting) system.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('printer_friendly_block', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'A block to provide a link for the current screen to be turned into it\'s printer-friendly equivalent.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('quizzes', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Construct competitions, surveys, and tests, for members to perform. Highly configurable, and comes with administrative tools to handle the results.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('random_quotes', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'A block to display random quotes on your website, and an administrative tool to choose them.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('realtime_rain', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Real-time/historic display of website activity.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('recommend', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Allow members to easily recommend the website to others.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('redirects_editor', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Manage redirects between pages.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('rootkit_detector', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'A tool to help power-user webmasters identify if a \"rootkit\" has been placed on the server.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('search', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Multi-content search engine.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('securitylogging', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Log/display security alerts.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('setupwizard', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Quick-start setup wizard.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('shopping', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Online store functionality.', 1554920596);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('sms', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Provides an option for the software to send SMS messages, via the commercial Clickatell web service. By default this is only used by the notifications system.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('ssl', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Choose which pages of your website run over HTTPS (SSL). Requires an SSL certificate to be installed on the webserver, and a dedicated IP address.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('staff', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Choose and display a selection of staff from the super-administator/super-moderator usergroups. This is useful for multi-site networks, where members with privileges may not be considered staff on all websites on the network.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('staff_messaging', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Blocks to allow visitors to contact the staff, either via e-mail, or via a special administrative interface which the staff may use to comment on the messages and assign task ownership.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('stats', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Show advanced graphs (analytics) and dumps of raw data relating to your website activity.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('stats_block', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'A block to show a selection of your website statistics to your visitors.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('supermember_directory', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Show a list of all members in the configured \"Super member\" usergroup. Useful for communities that need to provide a list of VIPs.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('syndication', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Syndicate RSS/Atom feeds of your content.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('syndication_blocks', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Show RSS and Atom feeds from other websites.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('textbased_persistent_caching', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'A persistent data cache, using disk files for data storage.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('themewizard', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Automatically generate your own colour schemes using the default theme as a base. Uses the sophisticated chromagraphic equations built into Composr.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('tickets', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'A support ticket system.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('uninstaller', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'The uninstaller.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('unvalidated', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Subject member\'s to validation (approval) of their content submissions, and enable/disable content.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('users_online_block', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'A block to show which users who are currently visiting the website, and birthdays.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('welcome_emails', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Welcome e-mails for new members.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('wiki', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Collaborative/encyclopaedic database interface. A wiki-like community database with rich media capabilities.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('windows_helper_scripts', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'A .bat script to help configure permissions on Windows servers.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('wordfilter', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Block rude/offensive/inappropriate words.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('xml_fields', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Advanced form field filtering.', 1554920597);
INSERT INTO cms_addons (addon_name, addon_author, addon_organisation, addon_version, addon_category, addon_copyright_attribution, addon_licence, addon_description, addon_install_time) VALUES ('zone_logos', 'Core Team', 'ocProducts', '10', 'Uncategorised/Alpha', '', '(Unstated)', 'Support for having different logos for individual zones.', 1554920597);

DROP TABLE IF EXISTS cms_addons_dependencies;

CREATE TABLE cms_addons_dependencies (
    addon_name_incompatibility tinyint(1) NOT NULL,
    addon_name_dependant_upon varchar(255) NOT NULL,
    addon_name varchar(255) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (1, 'aggregate_types', 'commandr', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (2, 'aggregate_types', 'import', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (3, 'cns_avatars', 'cns_member_avatars', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (4, 'cns_cartoon_avatars', 'cns_member_avatars', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (5, 'cns_forum', 'polls', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (6, 'cns_multi_moderations', 'cns_forum', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (7, 'cns_post_templates', 'cns_forum', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (8, 'cns_reported_posts', 'cns_forum', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (9, 'cns_thematic_avatars', 'cns_member_avatars', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (10, 'commandr', 'import', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (11, 'content_privacy', 'cns_cpfs', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (12, 'content_reviews', 'unvalidated', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (13, 'content_reviews', 'commandr', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (14, 'forum_blocks', 'news_shared', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (15, 'news', 'news_shared', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (16, 'pointstore', 'points', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (17, 'realtime_rain', 'stats', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (18, 'shopping', 'ecommerce', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (19, 'shopping', 'catalogues', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (20, 'staff_messaging', 'cns_forum', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (21, 'supermember_directory', 'collaboration_zone', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (22, 'syndication', 'syndication_blocks', 0);
INSERT INTO cms_addons_dependencies (id, addon_name, addon_name_dependant_upon, addon_name_incompatibility) VALUES (23, 'syndication_blocks', 'news', 0);

DROP TABLE IF EXISTS cms_addons_files;

CREATE TABLE cms_addons_files (
    id integer unsigned auto_increment NOT NULL,
    addon_name varchar(255) NOT NULL,
    filename varchar(255) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1, 'actionlog', 'themes/default/images/icons/24x24/menu/adminzone/audit/actionlog.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2, 'actionlog', 'themes/default/images/icons/48x48/menu/adminzone/audit/actionlog.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3, 'actionlog', 'sources/hooks/systems/notifications/actionlog.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4, 'actionlog', 'sources/hooks/systems/realtime_rain/actionlog.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5, 'actionlog', 'sources/hooks/systems/addon_registry/actionlog.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6, 'actionlog', 'adminzone/pages/modules/admin_actionlog.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (7, 'actionlog', 'sources/hooks/systems/rss/admin_recent_actions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (8, 'actionlog', 'lang/EN/actionlog.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (9, 'actionlog', 'sources/hooks/systems/config/store_revisions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (10, 'actionlog', 'sources/revisions_engine_files.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (11, 'actionlog', 'sources/revisions_engine_database.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (12, 'actionlog', 'adminzone/pages/modules/admin_revisions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (13, 'actionlog', 'themes/default/images/icons/24x24/buttons/revisions.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (14, 'actionlog', 'themes/default/images/icons/48x48/buttons/revisions.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (15, 'actionlog', 'themes/default/images/icons/24x24/buttons/undo.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (16, 'actionlog', 'themes/default/images/icons/48x48/buttons/undo.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (17, 'actionlog', 'themes/default/templates/REVISIONS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (18, 'actionlog', 'themes/default/templates/REVISIONS_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (19, 'actionlog', 'themes/default/templates/REVISIONS_DIFF_ICON.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (20, 'actionlog', 'themes/default/templates/REVISION_UNDO.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (21, 'aggregate_types', 'themes/default/images/icons/24x24/menu/adminzone/structure/aggregate_types.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (22, 'aggregate_types', 'themes/default/images/icons/48x48/menu/adminzone/structure/aggregate_types.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (23, 'aggregate_types', 'sources/hooks/systems/addon_registry/aggregate_types.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (24, 'aggregate_types', 'sources/hooks/systems/resource_meta_aware/aggregate_type_instance.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (25, 'aggregate_types', 'lang/EN/aggregate_types.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (26, 'aggregate_types', 'adminzone/pages/modules/admin_aggregate_types.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (27, 'aggregate_types', 'data/xml_config/aggregate_types.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (28, 'aggregate_types', 'sources/hooks/systems/commandr_fs/aggregate_type_instances.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (29, 'aggregate_types', 'sources/aggregate_types.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (30, 'aggregate_types', 'sources/hooks/modules/admin_import_types/aggregate_types.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (31, 'aggregate_types', 'sources/hooks/systems/page_groupings/aggregate_types.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (32, 'apache_config_files', 'sources/hooks/systems/addon_registry/apache_config_files.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (33, 'apache_config_files', 'plain.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (34, 'apache_config_files', 'recommended.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (35, 'authors', 'themes/default/images/icons/24x24/menu/cms/author_set_own_profile.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (36, 'authors', 'themes/default/images/icons/48x48/menu/cms/author_set_own_profile.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (37, 'authors', 'themes/default/images/icons/24x24/menu/rich_content/authors.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (38, 'authors', 'themes/default/images/icons/48x48/menu/rich_content/authors.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (39, 'authors', 'themes/default/css/authors.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (40, 'authors', 'sources/hooks/systems/attachments/author.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (41, 'authors', 'sources/hooks/systems/meta/authors.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (42, 'authors', 'sources/hooks/systems/addon_registry/authors.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (43, 'authors', 'themes/default/templates/AUTHOR_MANAGE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (44, 'authors', 'themes/default/templates/AUTHOR_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (45, 'authors', 'themes/default/templates/AUTHOR_POPUP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (46, 'authors', 'themes/default/templates/AUTHOR_SCREEN_POTENTIAL_ACTION_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (47, 'authors', 'data/authors.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (48, 'authors', 'cms/pages/modules/cms_authors.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (49, 'authors', 'lang/EN/authors.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (50, 'authors', 'site/pages/modules/authors.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (51, 'authors', 'sources/authors.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (52, 'authors', 'sources/hooks/systems/page_groupings/authors.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (53, 'authors', 'sources/hooks/systems/rss/authors.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (54, 'authors', 'sources/hooks/systems/content_meta_aware/author.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (55, 'authors', 'sources/hooks/systems/commandr_fs/authors.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (56, 'authors', 'sources/hooks/systems/sitemap/author.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (57, 'awards', 'themes/default/images/icons/24x24/menu/adminzone/setup/awards.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (58, 'awards', 'themes/default/images/icons/48x48/menu/adminzone/setup/awards.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (59, 'awards', 'sources/hooks/systems/addon_registry/awards.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (60, 'awards', 'sources/hooks/systems/resource_meta_aware/award_type.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (61, 'awards', 'themes/default/templates/AWARDED_CONTENT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (62, 'awards', 'themes/default/templates/BLOCK_MAIN_AWARDS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (63, 'awards', 'adminzone/pages/modules/admin_awards.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (64, 'awards', 'sources/blocks/main_awards.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (65, 'awards', 'sources/awards.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (66, 'awards', 'sources/awards2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (67, 'awards', 'site/pages/modules/awards.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (68, 'awards', 'lang/EN/awards.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (69, 'awards', 'sources/hooks/blocks/main_staff_checklist/awards.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (70, 'awards', 'themes/default/css/awards.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (71, 'awards', 'themes/default/images/awarded.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (72, 'awards', 'sources/hooks/modules/admin_import_types/awards.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (73, 'awards', 'sources/hooks/systems/block_ui_renderers/awards.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (74, 'awards', 'sources/hooks/systems/commandr_fs/award_types.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (75, 'awards', 'sources/hooks/systems/config/awarded_items_per_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (76, 'backup', 'themes/default/images/icons/24x24/menu/adminzone/tools/bulk_content_actions/backups.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (77, 'backup', 'themes/default/images/icons/48x48/menu/adminzone/tools/bulk_content_actions/backups.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (78, 'backup', 'sources/hooks/systems/config/backup_overwrite.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (79, 'backup', 'sources/hooks/systems/config/backup_server_hostname.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (80, 'backup', 'sources/hooks/systems/config/backup_server_password.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (81, 'backup', 'sources/hooks/systems/config/backup_server_path.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (82, 'backup', 'sources/hooks/systems/config/backup_server_port.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (83, 'backup', 'sources/hooks/systems/config/backup_server_user.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (84, 'backup', 'sources/hooks/systems/config/backup_time.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (85, 'backup', 'data/modules/admin_backup/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (86, 'backup', 'data_custom/modules/admin_backup/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (87, 'backup', 'sources/hooks/systems/addon_registry/backup.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (88, 'backup', 'themes/default/templates/RESTORE_HTML_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (89, 'backup', 'exports/backups/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (90, 'backup', 'themes/default/templates/BACKUP_LAUNCH_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (91, 'backup', 'adminzone/pages/modules/admin_backup.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (92, 'backup', 'data/modules/admin_backup/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (93, 'backup', 'data/modules/admin_backup/restore.php.pre');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (94, 'backup', 'data_custom/modules/admin_backup/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (95, 'backup', 'lang/EN/backups.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (96, 'backup', 'sources/backup.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (97, 'backup', 'sources/hooks/blocks/main_staff_checklist/backup.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (98, 'backup', 'sources/hooks/systems/cron/backups.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (99, 'backup', 'sources/hooks/systems/page_groupings/backup.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (100, 'backup', 'sources/hooks/systems/snippets/backup_size.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (101, 'backup', 'sources/hooks/systems/tasks/make_backup.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (102, 'banners', 'themes/default/images/icons/24x24/menu/cms/banners.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (103, 'banners', 'themes/default/images/icons/48x48/menu/cms/banners.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (104, 'banners', 'themes/default/css/banners.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (105, 'banners', 'sources/hooks/systems/snippets/exists_banner.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (106, 'banners', 'sources/hooks/systems/snippets/exists_banner_type.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (107, 'banners', 'sources/hooks/systems/config/admin_banners.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (108, 'banners', 'sources/hooks/systems/config/banner_autosize.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (109, 'banners', 'sources/hooks/systems/config/points_ADD_BANNER.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (110, 'banners', 'sources/hooks/systems/config/use_banner_permissions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (111, 'banners', 'sources/hooks/systems/realtime_rain/banners.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (112, 'banners', 'adminzone/pages/modules/admin_banners.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (113, 'banners', 'uploads/banners/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (114, 'banners', 'themes/default/templates/BANNER_PREVIEW.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (115, 'banners', 'themes/default/templates/BANNERS_NONE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (116, 'banners', 'sources/hooks/systems/preview/banner.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (117, 'banners', 'sources/hooks/modules/admin_import_types/banners.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (118, 'banners', 'sources/hooks/systems/addon_registry/banners.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (119, 'banners', 'themes/default/templates/BANNER_FLASH.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (120, 'banners', 'themes/default/templates/BANNER_TEXT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (121, 'banners', 'themes/default/templates/BANNER_VIEW_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (122, 'banners', 'themes/default/templates/BANNER_IFRAME.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (123, 'banners', 'themes/default/templates/BANNER_IMAGE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (124, 'banners', 'themes/default/templates/BANNER_SHOW_CODE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (125, 'banners', 'themes/default/templates/BANNER_ADDED_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (126, 'banners', 'themes/default/templates/BLOCK_MAIN_TOP_SITES.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (127, 'banners', 'themes/default/templates/BLOCK_MAIN_BANNER_WAVE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (128, 'banners', 'themes/default/templates/BLOCK_MAIN_BANNER_WAVE_BWRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (129, 'banners', 'sources/hooks/systems/sitemap/banner.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (130, 'banners', 'banner.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (131, 'banners', 'uploads/banners/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (132, 'banners', 'cms/pages/modules/cms_banners.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (133, 'banners', 'lang/EN/banners.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (134, 'banners', 'site/pages/modules/banners.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (135, 'banners', 'sources/banners.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (136, 'banners', 'sources/banners2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (137, 'banners', 'sources/blocks/main_top_sites.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (138, 'banners', 'sources/blocks/main_banner_wave.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (139, 'banners', 'sources/hooks/modules/admin_setupwizard/banners.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (140, 'banners', 'sources/hooks/modules/admin_unvalidated/banners.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (141, 'banners', 'sources/hooks/modules/pointstore/banners.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (142, 'banners', 'themes/default/templates/POINTSTORE_BANNERS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (143, 'banners', 'themes/default/templates/POINTSTORE_BANNERS_2.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (144, 'banners', 'themes/default/templates/POINTSTORE_BANNERS_ACTIVATE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (145, 'banners', 'themes/default/templates/POINTSTORE_BANNERS_UPGRADE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (146, 'banners', 'sources/hooks/systems/page_groupings/banners.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (147, 'banners', 'sources/hooks/systems/content_meta_aware/banner.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (148, 'banners', 'sources/hooks/systems/content_meta_aware/banner_type.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (149, 'banners', 'sources/hooks/systems/commandr_fs/banners.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (150, 'banners', 'data/images/advertise_here.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (151, 'banners', 'data/images/donate.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (152, 'banners', 'data/images/placeholder_leaderboard.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (153, 'banners', 'sources/hooks/systems/block_ui_renderers/banners.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (154, 'banners', 'sources/hooks/systems/reorganise_uploads/banners.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (155, 'bookmarks', 'themes/default/images/icons/24x24/menu/site_meta/bookmarks.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (156, 'bookmarks', 'themes/default/images/icons/48x48/menu/site_meta/bookmarks.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (157, 'bookmarks', 'sources/bookmarks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (158, 'bookmarks', 'data/bookmarks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (159, 'bookmarks', 'sources/hooks/systems/addon_registry/bookmarks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (160, 'bookmarks', 'sources/hooks/modules/admin_import_types/bookmarks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (161, 'bookmarks', 'themes/default/templates/BOOKMARKS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (162, 'bookmarks', 'themes/default/javascript/bookmarks.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (163, 'bookmarks', 'lang/EN/bookmarks.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (164, 'bookmarks', 'site/pages/modules/bookmarks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (165, 'bookmarks', 'themes/default/css/bookmarks.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (166, 'bookmarks', 'sources/hooks/systems/sitemap/bookmarks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (167, 'bookmarks', 'sources/hooks/systems/page_groupings/bookmarks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (168, 'bookmarks', 'sources/hooks/systems/commandr_fs_extended_member/bookmarks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (169, 'breadcrumbs', 'themes/default/images/icons/24x24/menu/adminzone/structure/breadcrumbs.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (170, 'breadcrumbs', 'themes/default/images/icons/48x48/menu/adminzone/structure/breadcrumbs.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (171, 'breadcrumbs', 'sources/hooks/systems/addon_registry/breadcrumbs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (172, 'breadcrumbs', 'data/xml_config/breadcrumbs.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (173, 'breadcrumbs', 'sources/breadcrumbs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (174, 'calendar', 'themes/default/images/icons/24x24/menu/rich_content/calendar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (175, 'calendar', 'themes/default/images/icons/48x48/menu/rich_content/calendar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (176, 'calendar', 'sources/hooks/systems/snippets/calendar_recurrence_suggest.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (177, 'calendar', 'sources/hooks/systems/notifications/calendar_reminder.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (178, 'calendar', 'sources/hooks/systems/notifications/calendar_event.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (179, 'calendar', 'sources/hooks/systems/config/calendar_show_stats_count_events.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (180, 'calendar', 'sources/hooks/systems/config/calendar_show_stats_count_events_this_month.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (181, 'calendar', 'sources/hooks/systems/config/calendar_show_stats_count_events_this_week.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (182, 'calendar', 'sources/hooks/systems/config/calendar_show_stats_count_events_this_year.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (183, 'calendar', 'sources/hooks/systems/realtime_rain/calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (184, 'calendar', 'sources/hooks/systems/content_meta_aware/event.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (185, 'calendar', 'sources/hooks/systems/content_meta_aware/calendar_type.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (186, 'calendar', 'sources/hooks/systems/commandr_fs/calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (187, 'calendar', 'sources/hooks/systems/meta/events.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (188, 'calendar', 'sources/hooks/blocks/side_stats/stats_calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (189, 'calendar', 'sources/hooks/systems/preview/calendar_type.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (190, 'calendar', 'sources/hooks/modules/admin_import_types/calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (191, 'calendar', 'sources/hooks/modules/admin_setupwizard/calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (192, 'calendar', 'sources/hooks/modules/admin_themewizard/calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (193, 'calendar', 'sources/hooks/systems/addon_registry/calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (194, 'calendar', 'themes/default/templates/CALENDAR_MAIN_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (195, 'calendar', 'themes/default/templates/CALENDAR_DAY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (196, 'calendar', 'themes/default/templates/CALENDAR_DAY_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (197, 'calendar', 'themes/default/templates/CALENDAR_DAY_ENTRY_FREE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (198, 'calendar', 'themes/default/templates/CALENDAR_DAY_HOUR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (199, 'calendar', 'themes/default/templates/CALENDAR_DAY_STREAM_HOUR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (200, 'calendar', 'themes/default/templates/CALENDAR_EVENT_CONFLICT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (201, 'calendar', 'themes/default/templates/CALENDAR_EVENT_TYPE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (202, 'calendar', 'themes/default/templates/CALENDAR_MONTH.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (203, 'calendar', 'themes/default/templates/CALENDAR_MONTH_DAY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (204, 'calendar', 'themes/default/templates/CALENDAR_MONTH_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (205, 'calendar', 'themes/default/templates/CALENDAR_MONTH_ENTRY_FREE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (206, 'calendar', 'themes/default/templates/CALENDAR_MONTH_WEEK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (207, 'calendar', 'themes/default/templates/CALENDAR_EVENT_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (208, 'calendar', 'themes/default/templates/CALENDAR_WEEK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (209, 'calendar', 'themes/default/templates/CALENDAR_WEEK_HOUR_DAY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (210, 'calendar', 'themes/default/templates/CALENDAR_WEEK_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (211, 'calendar', 'themes/default/templates/CALENDAR_WEEK_ENTRY_FREE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (212, 'calendar', 'themes/default/templates/CALENDAR_WEEK_HOUR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (213, 'calendar', 'themes/default/templates/CALENDAR_YEAR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (214, 'calendar', 'themes/default/templates/CALENDAR_YEAR_MONTH.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (215, 'calendar', 'themes/default/templates/CALENDAR_YEAR_MONTH_DAY_ACTIVE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (216, 'calendar', 'themes/default/templates/CALENDAR_YEAR_MONTH_DAY_FREE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (217, 'calendar', 'themes/default/templates/CALENDAR_YEAR_MONTH_DAY_ROW.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (218, 'calendar', 'themes/default/templates/CALENDAR_YEAR_MONTH_DAY_SPACER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (219, 'calendar', 'themes/default/templates/CALENDAR_YEAR_MONTH_ROW.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (220, 'calendar', 'themes/default/templates/BLOCK_SIDE_CALENDAR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (221, 'calendar', 'themes/default/templates/BLOCK_SIDE_CALENDAR_LISTING.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (222, 'calendar', 'themes/default/templates/CALENDAR_EVENT_BOX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (223, 'calendar', 'sources/hooks/systems/trackback/events.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (224, 'calendar', 'cms/pages/modules/cms_calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (225, 'calendar', 'lang/EN/calendar.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (226, 'calendar', 'site/pages/modules/calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (227, 'calendar', 'sources/blocks/side_calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (228, 'calendar', 'sources/hooks/systems/sitemap/calendar_type.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (229, 'calendar', 'sources/hooks/systems/sitemap/event.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (230, 'calendar', 'sources/calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (231, 'calendar', 'sources/calendar2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (232, 'calendar', 'sources/calendar_ical.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (233, 'calendar', 'sources/hooks/modules/admin_import/icalendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (234, 'calendar', 'sources/hooks/modules/admin_newsletter/calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (235, 'calendar', 'sources/hooks/modules/admin_unvalidated/calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (236, 'calendar', 'sources/hooks/modules/members/calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (237, 'calendar', 'sources/hooks/modules/search/calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (238, 'calendar', 'sources/hooks/systems/attachments/calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (239, 'calendar', 'sources/hooks/systems/cron/calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (240, 'calendar', 'sources/hooks/systems/page_groupings/calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (241, 'calendar', 'sources/hooks/systems/preview/calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (242, 'calendar', 'sources/hooks/systems/rss/calendar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (243, 'calendar', 'themes/default/css/calendar.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (244, 'calendar', 'themes/default/images/calendar/activity.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (245, 'calendar', 'themes/default/images/calendar/anniversary.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (246, 'calendar', 'themes/default/images/calendar/appointment.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (247, 'calendar', 'themes/default/images/calendar/birthday.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (248, 'calendar', 'themes/default/images/calendar/commitment.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (249, 'calendar', 'themes/default/images/calendar/duty.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (250, 'calendar', 'themes/default/images/calendar/festival.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (251, 'calendar', 'themes/default/images/calendar/general.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (252, 'calendar', 'themes/default/images/calendar/public_holiday.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (253, 'calendar', 'themes/default/images/calendar/vacation.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (254, 'calendar', 'themes/default/images/calendar/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (255, 'calendar', 'themes/default/images/calendar/priority_1.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (256, 'calendar', 'themes/default/images/calendar/priority_2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (257, 'calendar', 'themes/default/images/calendar/priority_3.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (258, 'calendar', 'themes/default/images/calendar/priority_4.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (259, 'calendar', 'themes/default/images/calendar/priority_5.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (260, 'calendar', 'themes/default/images/calendar/priority_na.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (261, 'calendar', 'themes/default/images/calendar/rss.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (262, 'calendar', 'themes/default/images/calendar/system_command.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (263, 'calendar', 'sources/hooks/systems/notifications/member_calendar_changes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (264, 'calendar', 'sources/hooks/systems/commandr_fs_extended_member/calendar_interests.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (265, 'captcha', 'sources/hooks/systems/snippets/captcha_wrong.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (266, 'captcha', 'sources/hooks/systems/addon_registry/captcha.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (267, 'captcha', 'themes/default/templates/FORM_SCREEN_INPUT_CAPTCHA.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (268, 'captcha', 'data/captcha.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (269, 'captcha', 'sources/captcha.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (270, 'captcha', 'lang/EN/captcha.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (271, 'captcha', 'data/sounds/0.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (272, 'captcha', 'data/sounds/1.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (273, 'captcha', 'data/sounds/2.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (274, 'captcha', 'data/sounds/3.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (275, 'captcha', 'data/sounds/4.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (276, 'captcha', 'data/sounds/5.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (277, 'captcha', 'data/sounds/6.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (278, 'captcha', 'data/sounds/7.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (279, 'captcha', 'data/sounds/8.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (280, 'captcha', 'data/sounds/9.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (281, 'captcha', 'data/sounds/a.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (282, 'captcha', 'data/sounds/b.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (283, 'captcha', 'data/sounds/c.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (284, 'captcha', 'data/sounds/d.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (285, 'captcha', 'data/sounds/e.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (286, 'captcha', 'data/sounds/f.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (287, 'captcha', 'data/sounds/g.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (288, 'captcha', 'data/sounds/h.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (289, 'captcha', 'data/sounds/i.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (290, 'captcha', 'data/sounds/j.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (291, 'captcha', 'data/sounds/k.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (292, 'captcha', 'data/sounds/l.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (293, 'captcha', 'data/sounds/m.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (294, 'captcha', 'data/sounds/n.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (295, 'captcha', 'data/sounds/o.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (296, 'captcha', 'data/sounds/p.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (297, 'captcha', 'data/sounds/q.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (298, 'captcha', 'data/sounds/r.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (299, 'captcha', 'data/sounds/s.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (300, 'captcha', 'data/sounds/t.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (301, 'captcha', 'data/sounds/u.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (302, 'captcha', 'data/sounds/v.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (303, 'captcha', 'data/sounds/w.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (304, 'captcha', 'data/sounds/x.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (305, 'captcha', 'data/sounds/y.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (306, 'captcha', 'data/sounds/z.wav');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (307, 'captcha', 'sources/hooks/systems/config/use_captchas.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (308, 'captcha', 'sources/hooks/systems/config/captcha_single_guess.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (309, 'captcha', 'sources/hooks/systems/config/css_captcha.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (310, 'captcha', 'sources/hooks/systems/config/captcha_noise.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (311, 'captcha', 'sources/hooks/systems/config/captcha_on_feedback.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (312, 'captcha', 'sources/hooks/systems/config/audio_captcha.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (313, 'captcha', 'sources/hooks/systems/config/js_captcha.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (314, 'catalogues', 'themes/default/images/icons/24x24/menu/rich_content/catalogues/catalogues.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (315, 'catalogues', 'themes/default/images/icons/48x48/menu/rich_content/catalogues/catalogues.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (316, 'catalogues', 'themes/default/images/icons/24x24/menu/cms/catalogues/add_one_catalogue.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (317, 'catalogues', 'themes/default/images/icons/24x24/menu/cms/catalogues/edit_one_catalogue.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (318, 'catalogues', 'themes/default/images/icons/48x48/menu/cms/catalogues/add_one_catalogue.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (319, 'catalogues', 'themes/default/images/icons/48x48/menu/cms/catalogues/edit_one_catalogue.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (320, 'catalogues', 'themes/default/images/icons/48x48/menu/cms/catalogues/edit_this_catalogue.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (321, 'catalogues', 'themes/default/images/icons/48x48/menu/cms/catalogues/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (322, 'catalogues', 'themes/default/images/icons/24x24/menu/cms/catalogues/edit_this_catalogue.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (323, 'catalogues', 'themes/default/images/icons/24x24/menu/cms/catalogues/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (324, 'catalogues', 'themes/default/images/icons/24x24/menu/rich_content/catalogues/classifieds.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (325, 'catalogues', 'themes/default/images/icons/24x24/menu/rich_content/catalogues/contacts.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (326, 'catalogues', 'themes/default/images/icons/24x24/menu/rich_content/catalogues/faqs.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (327, 'catalogues', 'themes/default/images/icons/24x24/menu/rich_content/catalogues/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (328, 'catalogues', 'themes/default/images/icons/24x24/menu/rich_content/catalogues/links.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (329, 'catalogues', 'themes/default/images/icons/24x24/menu/rich_content/catalogues/products.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (330, 'catalogues', 'themes/default/images/icons/24x24/menu/rich_content/catalogues/projects.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (331, 'catalogues', 'themes/default/images/icons/48x48/menu/rich_content/catalogues/classifieds.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (332, 'catalogues', 'themes/default/images/icons/48x48/menu/rich_content/catalogues/contacts.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (333, 'catalogues', 'themes/default/images/icons/48x48/menu/rich_content/catalogues/faqs.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (334, 'catalogues', 'themes/default/images/icons/48x48/menu/rich_content/catalogues/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (335, 'catalogues', 'themes/default/images/icons/48x48/menu/rich_content/catalogues/links.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (336, 'catalogues', 'themes/default/images/icons/48x48/menu/rich_content/catalogues/products.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (337, 'catalogues', 'themes/default/images/icons/48x48/menu/rich_content/catalogues/projects.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (338, 'catalogues', 'sources/hooks/systems/reorganise_uploads/catalogues.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (339, 'catalogues', 'sources/hooks/systems/snippets/exists_catalogue.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (340, 'catalogues', 'sources/hooks/systems/module_permissions/catalogues_catalogue.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (341, 'catalogues', 'sources/hooks/systems/module_permissions/catalogues_category.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (342, 'catalogues', 'sources/hooks/systems/rss/catalogues.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (343, 'catalogues', 'sources/hooks/systems/page_groupings/catalogues.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (344, 'catalogues', 'sources/hooks/systems/trackback/catalogues.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (345, 'catalogues', 'sources/hooks/modules/search/catalogue_categories.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (346, 'catalogues', 'sources/hooks/modules/search/catalogue_entries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (347, 'catalogues', 'sources/hooks/systems/ajax_tree/choose_catalogue_category.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (348, 'catalogues', 'sources/hooks/systems/ajax_tree/choose_catalogue_entry.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (349, 'catalogues', 'sources/hooks/systems/cron/catalogue_entry_timeouts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (350, 'catalogues', 'sources/hooks/systems/cron/catalogue_view_reports.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (351, 'catalogues', 'sources/hooks/systems/meta/catalogue_category.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (352, 'catalogues', 'sources/hooks/systems/meta/catalogue_entry.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (353, 'catalogues', 'themes/default/javascript/catalogues.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (354, 'catalogues', 'sources/hooks/modules/admin_import_types/catalogues.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (355, 'catalogues', 'sources/hooks/systems/content_meta_aware/catalogue.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (356, 'catalogues', 'sources/hooks/systems/content_meta_aware/catalogue_category.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (357, 'catalogues', 'sources/hooks/systems/content_meta_aware/catalogue_entry.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (358, 'catalogues', 'sources/hooks/systems/commandr_fs/catalogues.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (359, 'catalogues', 'sources/hooks/systems/addon_registry/catalogues.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (360, 'catalogues', 'themes/default/templates/CATALOGUE_ADDING_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (361, 'catalogues', 'themes/default/templates/CATALOGUE_EDITING_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (362, 'catalogues', 'themes/default/templates/CATALOGUE_CATEGORIES_LIST_LINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (363, 'catalogues', 'themes/default/templates/CATALOGUE_DEFAULT_CATEGORY_EMBED.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (364, 'catalogues', 'themes/default/templates/CATALOGUE_DEFAULT_CATEGORY_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (365, 'catalogues', 'themes/default/templates/CATALOGUE_DEFAULT_FIELDMAP_ENTRY_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (366, 'catalogues', 'themes/default/templates/CATALOGUE_DEFAULT_FIELDMAP_ENTRY_FIELD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (367, 'catalogues', 'themes/default/templates/CATALOGUE_DEFAULT_GRID_ENTRY_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (368, 'catalogues', 'themes/default/templates/CATALOGUE_DEFAULT_GRID_ENTRY_FIELD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (369, 'catalogues', 'themes/default/templates/CATALOGUE_DEFAULT_ENTRY_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (370, 'catalogues', 'themes/default/templates/CATALOGUE_DEFAULT_TITLELIST_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (371, 'catalogues', 'themes/default/templates/CATALOGUE_DEFAULT_TITLELIST_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (372, 'catalogues', 'themes/default/templates/CATALOGUE_ENTRIES_LIST_LINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (373, 'catalogues', 'themes/default/templates/SEARCH_RESULT_CATALOGUE_ENTRIES.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (374, 'catalogues', 'themes/default/templates/CATALOGUE_DEFAULT_TABULAR_ENTRY_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (375, 'catalogues', 'themes/default/templates/CATALOGUE_DEFAULT_TABULAR_ENTRY_FIELD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (376, 'catalogues', 'themes/default/templates/CATALOGUE_DEFAULT_TABULAR_HEADCELL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (377, 'catalogues', 'themes/default/templates/CATALOGUE_DEFAULT_TABULAR_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (378, 'catalogues', 'themes/default/templates/CATALOGUE_links_TABULAR_ENTRY_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (379, 'catalogues', 'themes/default/templates/CATALOGUE_links_TABULAR_ENTRY_FIELD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (380, 'catalogues', 'themes/default/templates/CATALOGUE_links_TABULAR_HEADCELL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (381, 'catalogues', 'themes/default/templates/CATALOGUE_links_TABULAR_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (382, 'catalogues', 'themes/default/templates/CATALOGUE_CATEGORY_HEADING.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (383, 'catalogues', 'sources/hooks/systems/sitemap/catalogue.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (384, 'catalogues', 'sources/hooks/systems/sitemap/catalogue_category.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (385, 'catalogues', 'sources/hooks/systems/sitemap/catalogue_entry.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (386, 'catalogues', 'uploads/catalogues/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (387, 'catalogues', 'uploads/catalogues/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (388, 'catalogues', 'cms/pages/modules/cms_catalogues.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (389, 'catalogues', 'lang/EN/catalogues.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (390, 'catalogues', 'site/pages/modules/catalogues.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (391, 'catalogues', 'sources/hooks/systems/notifications/catalogue_view_reports.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (392, 'catalogues', 'sources/hooks/systems/notifications/catalogue_entry.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (393, 'catalogues', 'sources/catalogues.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (394, 'catalogues', 'sources/hooks/modules/admin_import/catalogues.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (395, 'catalogues', 'sources/catalogues2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (396, 'catalogues', 'sources/hooks/modules/admin_newsletter/catalogues.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (397, 'catalogues', 'sources/hooks/modules/admin_setupwizard/catalogues.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (398, 'catalogues', 'sources/hooks/modules/admin_unvalidated/catalogue_entry.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (399, 'catalogues', 'sources/hooks/systems/attachments/catalogue_entry.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (400, 'catalogues', 'sources/blocks/main_cc_embed.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (401, 'catalogues', 'themes/default/css/catalogues.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (402, 'catalogues', 'sources/hooks/systems/symbols/CATALOGUE_ENTRY_BACKREFS.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (403, 'catalogues', 'sources/hooks/systems/symbols/CATALOGUE_ENTRY_FIELD_VALUE.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (404, 'catalogues', 'sources/hooks/systems/symbols/CATALOGUE_ENTRY_FIELD_VALUE_PLAIN.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (405, 'catalogues', 'sources/blocks/main_contact_catalogues.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (406, 'catalogues', 'sources/hooks/systems/symbols/CATALOGUE_ENTRY_ALL_FIELD_VALUES.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (407, 'catalogues', 'sources/hooks/systems/block_ui_renderers/catalogues.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (408, 'catalogues', 'sources/hooks/systems/config/catalogue_entries_per_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (409, 'catalogues', 'sources/hooks/systems/config/catalogue_subcats_per_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (410, 'catalogues', 'sources/hooks/systems/config/catalogues_subcat_narrowin.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (411, 'catalogues', 'sources/hooks/systems/tasks/export_catalogue.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (412, 'catalogues', 'sources/hooks/systems/tasks/import_catalogue.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (413, 'chat', 'themes/default/images/icons/14x14/sound_effects.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (414, 'chat', 'themes/default/images/icons/28x28/sound_effects.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (415, 'chat', 'themes/default/images/icons/24x24/menu/social/chat/chat.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (416, 'chat', 'themes/default/images/icons/48x48/menu/social/chat/chat.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (417, 'chat', 'themes/default/images/icons/24x24/menu/social/chat/chatroom_add.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (418, 'chat', 'themes/default/images/icons/48x48/menu/social/chat/chatroom_add.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (419, 'chat', 'themes/default/images/icons/24x24/menu/social/chat/member_blocking.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (420, 'chat', 'themes/default/images/icons/48x48/menu/social/chat/member_blocking.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (421, 'chat', 'themes/default/images/icons/24x24/tabs/member_account/friends.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (422, 'chat', 'themes/default/images/icons/48x48/tabs/member_account/friends.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (423, 'chat', 'themes/default/images/icons/24x24/menu/social/chat/sound.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (424, 'chat', 'themes/default/images/icons/48x48/menu/social/chat/sound.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (425, 'chat', 'themes/default/images/icons/24x24/menu/social/chat/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (426, 'chat', 'themes/default/images/icons/48x48/menu/social/chat/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (427, 'chat', 'sources/chat_shoutbox.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (428, 'chat', 'sources/chat_sounds.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (429, 'chat', 'sources/chat_lobby.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (430, 'chat', 'sources/chat_logs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (431, 'chat', 'sources/hooks/systems/snippets/im_friends_rejig.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (432, 'chat', 'site/pages/comcode/EN/popup_blockers.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (433, 'chat', 'sources/blocks/side_friends.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (434, 'chat', 'themes/default/templates/BLOCK_SIDE_FRIENDS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (435, 'chat', 'themes/default/templates/CHAT_FRIENDS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (436, 'chat', 'sources/hooks/systems/startup/im.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (437, 'chat', 'sources/hooks/systems/notifications/im_invited.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (438, 'chat', 'sources/hooks/systems/notifications/new_friend.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (439, 'chat', 'sources/hooks/systems/notifications/member_entered_chatroom.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (440, 'chat', 'sources/hooks/systems/notifications/cns_friend_birthday.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (441, 'chat', 'sources/hooks/systems/config/chat_default_post_colour.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (442, 'chat', 'sources/hooks/systems/config/chat_default_post_font.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (443, 'chat', 'sources/hooks/systems/config/chat_flood_timelimit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (444, 'chat', 'sources/hooks/systems/config/chat_private_room_deletion_time.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (445, 'chat', 'sources/hooks/systems/config/chat_show_stats_count_messages.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (446, 'chat', 'sources/hooks/systems/config/chat_show_stats_count_rooms.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (447, 'chat', 'sources/hooks/systems/config/chat_show_stats_count_users.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (448, 'chat', 'sources/hooks/systems/config/group_private_chatrooms.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (449, 'chat', 'sources/hooks/systems/config/chat_max_messages_to_show.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (450, 'chat', 'sources/hooks/systems/config/points_chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (451, 'chat', 'sources/hooks/systems/config/sitewide_im.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (452, 'chat', 'sources/hooks/systems/config/username_click_im.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (453, 'chat', 'sources/hooks/systems/realtime_rain/chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (454, 'chat', 'sources/hooks/systems/symbols/CHAT_IM.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (455, 'chat', 'sources/hooks/systems/profiles_tabs/friends.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (456, 'chat', 'sources/hooks/systems/sitemap/chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (457, 'chat', 'uploads/personal_sound_effects/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (458, 'chat', 'uploads/personal_sound_effects/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (459, 'chat', 'data/sounds/contact_off.mp3');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (460, 'chat', 'data/sounds/contact_on.mp3');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (461, 'chat', 'data/sounds/error.mp3');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (462, 'chat', 'data/sounds/invited.mp3');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (463, 'chat', 'data/sounds/message_initial.mp3');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (464, 'chat', 'data/sounds/message_sent.mp3');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (465, 'chat', 'data/sounds/you_connect.mp3');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (466, 'chat', 'sources/hooks/modules/chat_bots/default.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (467, 'chat', 'sources/hooks/modules/chat_bots/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (468, 'chat', 'sources_custom/hooks/modules/chat_bots/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (469, 'chat', 'themes/default/templates/CHAT_SET_EFFECTS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (470, 'chat', 'themes/default/templates/CHAT_SET_EFFECTS_SETTING_BLOCK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (471, 'chat', 'themes/default/templates/CHAT_SITEWIDE_IM.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (472, 'chat', 'themes/default/templates/CHAT_SITEWIDE_IM_POPUP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (473, 'chat', 'themes/default/templates/CHAT_SOUND.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (474, 'chat', 'themes/default/templates/CHAT_MODERATE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (475, 'chat', 'sources/hooks/modules/admin_import_types/chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (476, 'chat', 'sources/hooks/modules/admin_setupwizard/chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (477, 'chat', 'sources/hooks/modules/admin_themewizard/chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (478, 'chat', 'sources/hooks/systems/content_meta_aware/chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (479, 'chat', 'sources/hooks/systems/commandr_fs/chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (480, 'chat', 'sources/hooks/systems/addon_registry/chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (481, 'chat', 'sources/hooks/systems/cns_cpf_filter/points_chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (482, 'chat', 'themes/default/templates/BLOCK_SIDE_SHOUTBOX_MESSAGE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (483, 'chat', 'themes/default/templates/BLOCK_SIDE_SHOUTBOX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (484, 'chat', 'themes/default/templates/CHAT_ROOM_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (485, 'chat', 'themes/default/templates/CHATCODE_EDITOR_BUTTON.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (486, 'chat', 'themes/default/templates/CHATCODE_EDITOR_MICRO_BUTTON.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (487, 'chat', 'themes/default/templates/CHAT_INVITE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (488, 'chat', 'themes/default/templates/CHAT_MESSAGE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (489, 'chat', 'themes/default/templates/CHAT_PRIVATE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (490, 'chat', 'themes/default/templates/CHAT_STAFF_ACTIONS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (491, 'chat', 'themes/default/javascript/chat.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (492, 'chat', 'themes/default/templates/BLOCK_MAIN_FRIENDS_LIST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (493, 'chat', 'sources/blocks/main_friends_list.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (494, 'chat', 'themes/default/templates/CHAT_LOBBY_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (495, 'chat', 'themes/default/templates/CHAT_LOBBY_IM_AREA.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (496, 'chat', 'themes/default/templates/CHAT_LOBBY_IM_PARTICIPANT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (497, 'chat', 'sources/hooks/modules/chat_bots/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (498, 'chat', 'sources_custom/hooks/modules/chat_bots/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (499, 'chat', 'adminzone/pages/modules/admin_chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (500, 'chat', 'themes/default/css/chat.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (501, 'chat', 'themes/default/images/EN/chatcodeeditor/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (502, 'chat', 'themes/default/images/EN/chatcodeeditor/invite.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (503, 'chat', 'themes/default/images/EN/chatcodeeditor/new_room.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (504, 'chat', 'themes/default/images/EN/chatcodeeditor/private_message.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (505, 'chat', 'cms/pages/modules/cms_chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (506, 'chat', 'data_custom/modules/chat/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (507, 'chat', 'data_custom/modules/chat/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (508, 'chat', 'lang/EN/chat.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (509, 'chat', 'site/pages/comcode/EN/userguide_chatcode.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (510, 'chat', 'site/pages/modules/chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (511, 'chat', 'sources/chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (512, 'chat', 'sources/chat_stats.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (513, 'chat', 'sources/chat_poller.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (514, 'chat', 'sources/chat2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (515, 'chat', 'sources/hooks/blocks/side_stats/stats_chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (516, 'chat', 'sources/hooks/systems/commandr_commands/send_chatmessage.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (517, 'chat', 'sources/hooks/systems/commandr_commands/watch_chatroom.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (518, 'chat', 'sources/hooks/systems/commandr_notifications/chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (519, 'chat', 'sources/hooks/modules/members/chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (520, 'chat', 'sources/hooks/systems/page_groupings/chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (521, 'chat', 'sources/hooks/systems/rss/chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (522, 'chat', 'site/download_chat_logs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (523, 'chat', 'site/messages.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (524, 'chat', 'sources/blocks/side_shoutbox.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (525, 'chat', 'themes/default/templates/CNS_MEMBER_PROFILE_FRIENDS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (526, 'chat', 'sources/hooks/systems/block_ui_renderers/chat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (527, 'chat', 'sources/hooks/systems/config/chat_message_check_interval.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (528, 'chat', 'sources/hooks/systems/config/chat_transitory_alert_time.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (529, 'chat', 'sources/hooks/systems/config/max_chat_lobby_friends.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (530, 'chat', 'sources/hooks/systems/commandr_fs_extended_member/chat_blocking.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (531, 'chat', 'sources/hooks/systems/commandr_fs_extended_member/chat_friends.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (532, 'chat', 'sources/hooks/systems/commandr_fs_extended_member/chat_sound_effects.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (533, 'cns_avatars', 'sources/hooks/systems/addon_registry/cns_avatars.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (534, 'cns_avatars', 'themes/default/images/cns_default_avatars/default_set/airplane.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (535, 'cns_avatars', 'themes/default/images/cns_default_avatars/default_set/bird.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (536, 'cns_avatars', 'themes/default/images/cns_default_avatars/default_set/bonfire.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (537, 'cns_avatars', 'themes/default/images/cns_default_avatars/default_set/cool_flare.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (538, 'cns_avatars', 'themes/default/images/cns_default_avatars/default_set/dog.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (539, 'cns_avatars', 'themes/default/images/cns_default_avatars/default_set/eagle.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (540, 'cns_avatars', 'themes/default/images/cns_default_avatars/default_set/forks.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (541, 'cns_avatars', 'themes/default/images/cns_default_avatars/default_set/horse.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (542, 'cns_avatars', 'themes/default/images/cns_default_avatars/default_set/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (543, 'cns_avatars', 'themes/default/images/cns_default_avatars/default_set/music.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (544, 'cns_avatars', 'themes/default/images/cns_default_avatars/default_set/trees.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (545, 'cns_avatars', 'themes/default/images/cns_default_avatars/default_set/chess.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (546, 'cns_avatars', 'themes/default/images/cns_default_avatars/default_set/fireman.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (547, 'cns_avatars', 'themes/default/images/cns_default_avatars/default_set/berries.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (548, 'cns_cartoon_avatars', 'sources/hooks/systems/addon_registry/cns_cartoon_avatars.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (549, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/caveman.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (550, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/crazy.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (551, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/dance.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (552, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/emo.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (553, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/footy.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (554, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/half-life.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (555, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (556, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/matrix.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (557, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/ninja.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (558, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/plane.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (559, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/posh.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (560, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/rabbit.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (561, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/snorkler.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (562, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/western.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (563, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/anchor.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (564, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/boating.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (565, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/chillin.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (566, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/dude.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (567, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/dudette.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (568, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/guyinahat.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (569, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/kingfish.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (570, 'cns_cartoon_avatars', 'themes/default/images/cns_default_avatars/default_set/cartoons/worm.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (571, 'cns_clubs', 'themes/default/images/icons/24x24/menu/cms/clubs.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (572, 'cns_clubs', 'themes/default/images/icons/48x48/menu/cms/clubs.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (573, 'cns_clubs', 'sources/hooks/systems/notifications/cns_club.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (574, 'cns_clubs', 'sources/hooks/systems/addon_registry/cns_clubs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (575, 'cns_clubs', 'sources/hooks/modules/search/cns_clubs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (576, 'cns_clubs', 'cms/pages/modules/cms_cns_groups.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (577, 'cns_clubs', 'sources/hooks/systems/config/club_forum_parent_forum_grouping.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (578, 'cns_clubs', 'sources/hooks/systems/config/club_forum_parent_forum.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (579, 'cns_contact_member', 'themes/default/images/icons/24x24/links/contact_member.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (580, 'cns_contact_member', 'themes/default/images/icons/48x48/links/contact_member.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (581, 'cns_contact_member', 'site/pages/modules/contact_member.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (582, 'cns_contact_member', 'sources/hooks/systems/addon_registry/cns_contact_member.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (583, 'cns_cpfs', 'themes/default/images/icons/24x24/menu/adminzone/tools/users/custom_profile_fields.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (584, 'cns_cpfs', 'themes/default/images/icons/48x48/menu/adminzone/tools/users/custom_profile_fields.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (585, 'cns_cpfs', 'sources/hooks/systems/resource_meta_aware/cpf.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (586, 'cns_cpfs', 'adminzone/pages/modules/admin_cns_customprofilefields.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (587, 'cns_cpfs', 'themes/default/templates/CNS_CPF_STATS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (588, 'cns_cpfs', 'uploads/cns_cpf_upload/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (589, 'cns_cpfs', 'uploads/cns_cpf_upload/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (590, 'cns_cpfs', 'themes/default/templates/CNS_CPF_PERMISSIONS_TAB.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (591, 'cns_cpfs', 'lang/EN/cns_privacy.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (592, 'cns_cpfs', 'sources/hooks/systems/profiles_tabs_edit/privacy.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (593, 'cns_cpfs', 'sources/hooks/systems/addon_registry/cns_cpfs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (594, 'cns_cpfs', 'sources/hooks/systems/commandr_fs/cpfs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (595, 'cns_cpfs', 'sources/hooks/systems/commandr_fs_extended_member/cpf_perms.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (596, 'cns_forum', 'themes/default/images/icons/24x24/menu/social/forum/inline_personal_posts.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (597, 'cns_forum', 'themes/default/images/icons/48x48/menu/social/forum/inline_personal_posts.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (598, 'cns_forum', 'themes/default/images/icons/24x24/menu/adminzone/audit/statistics/posting_rates.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (599, 'cns_forum', 'themes/default/images/icons/48x48/menu/adminzone/audit/statistics/posting_rates.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (600, 'cns_forum', 'themes/default/images/icons/24x24/menu/social/forum/forums.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (601, 'cns_forum', 'themes/default/images/icons/48x48/menu/social/forum/forums.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (602, 'cns_forum', 'themes/default/images/icons/24x24/buttons/new_topic.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (603, 'cns_forum', 'themes/default/images/icons/48x48/buttons/new_topic.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (604, 'cns_forum', 'themes/default/images/icons/24x24/menu/social/forum/vforums/involved_topics.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (605, 'cns_forum', 'themes/default/images/icons/24x24/menu/social/forum/vforums/posts_since_last_visit.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (606, 'cns_forum', 'themes/default/images/icons/24x24/menu/social/forum/vforums/recently_read_topics.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (607, 'cns_forum', 'themes/default/images/icons/24x24/menu/social/forum/vforums/unanswered_topics.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (608, 'cns_forum', 'themes/default/images/icons/24x24/menu/social/forum/vforums/unread_topics.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (609, 'cns_forum', 'themes/default/images/icons/48x48/menu/social/forum/vforums/involved_topics.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (610, 'cns_forum', 'themes/default/images/icons/48x48/menu/social/forum/vforums/posts_since_last_visit.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (611, 'cns_forum', 'themes/default/images/icons/48x48/menu/social/forum/vforums/recently_read_topics.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (612, 'cns_forum', 'themes/default/images/icons/48x48/menu/social/forum/vforums/unanswered_topics.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (613, 'cns_forum', 'themes/default/images/icons/48x48/menu/social/forum/vforums/unread_topics.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (614, 'cns_forum', 'themes/default/images/icons/24x24/buttons/mark_read.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (615, 'cns_forum', 'themes/default/images/icons/24x24/buttons/mark_unread.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (616, 'cns_forum', 'themes/default/images/icons/48x48/buttons/mark_read.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (617, 'cns_forum', 'themes/default/images/icons/48x48/buttons/mark_unread.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (618, 'cns_forum', 'themes/default/images/icons/24x24/buttons/forum.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (619, 'cns_forum', 'themes/default/images/icons/48x48/buttons/forum.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (620, 'cns_forum', 'themes/default/images/icons/24x24/buttons/linear.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (621, 'cns_forum', 'themes/default/images/icons/48x48/buttons/linear.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (622, 'cns_forum', 'themes/default/images/icons/24x24/buttons/threaded.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (623, 'cns_forum', 'themes/default/images/icons/48x48/buttons/threaded.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (624, 'cns_forum', 'themes/default/images/icons/24x24/buttons/whisper.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (625, 'cns_forum', 'themes/default/images/icons/48x48/buttons/whisper.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (626, 'cns_forum', 'themes/default/images/icons/24x24/buttons/new_quote.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (627, 'cns_forum', 'themes/default/images/icons/48x48/buttons/new_quote.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (628, 'cns_forum', 'themes/default/images/icons/24x24/menu/adminzone/structure/forum/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (629, 'cns_forum', 'themes/default/images/icons/24x24/menu/social/forum/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (630, 'cns_forum', 'themes/default/images/icons/24x24/menu/social/forum/vforums/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (631, 'cns_forum', 'themes/default/images/icons/48x48/menu/adminzone/structure/forum/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (632, 'cns_forum', 'themes/default/images/icons/48x48/menu/social/forum/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (633, 'cns_forum', 'themes/default/images/icons/48x48/menu/social/forum/vforums/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (634, 'cns_forum', 'sources/hooks/systems/cns_cpf_filter/notifications.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (635, 'cns_forum', 'sources/hooks/systems/resource_meta_aware/forum_grouping.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (636, 'cns_forum', 'sources/hooks/systems/commandr_fs/forum_groupings.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (637, 'cns_forum', 'sources/blocks/main_cns_involved_topics.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (638, 'cns_forum', 'sources/hooks/systems/notifications/cns_topic_invite.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (639, 'cns_forum', 'sources/hooks/systems/notifications/cns_new_pt.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (640, 'cns_forum', 'sources/hooks/systems/notifications/cns_topic.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (641, 'cns_forum', 'sources/hooks/systems/content_meta_aware/forum.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (642, 'cns_forum', 'sources/hooks/systems/commandr_fs/forums.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (643, 'cns_forum', 'sources/hooks/modules/admin_stats/cns_posting_rates.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (644, 'cns_forum', 'sources/hooks/systems/sitemap/topic.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (645, 'cns_forum', 'sources/hooks/systems/sitemap/forum.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (646, 'cns_forum', 'themes/default/templates/CNS_FORUM_INTRO_QUESTION_POPUP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (647, 'cns_forum', 'themes/default/templates/CNS_MEMBER_PT_RULES_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (648, 'cns_forum', 'themes/default/templates/CNS_PT_BETWEEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (649, 'cns_forum', 'themes/default/javascript/cns_forum.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (650, 'cns_forum', 'themes/default/templates/BLOCK_MAIN_CNS_INVOLVED_TOPICS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (651, 'cns_forum', 'themes/default/templates/CNS_VFORUM_FILTERING.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (652, 'cns_forum', 'forum/rules.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (653, 'cns_forum', 'themes/default/images/cns_general/redirect.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (654, 'cns_forum', 'sources/hooks/modules/search/cns_within_topic.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (655, 'cns_forum', 'sources/hooks/systems/addon_registry/cns_forum.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (656, 'cns_forum', 'sources/hooks/systems/page_groupings/cns_forum.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (657, 'cns_forum', 'sources/hooks/modules/admin_themewizard/cns_forum.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (658, 'cns_forum', 'sources/hooks/modules/admin_import_types/cns_forum.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (659, 'cns_forum', 'sources/hooks/systems/profiles_tabs/posts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (660, 'cns_forum', 'sources/hooks/systems/profiles_tabs/pts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (661, 'cns_forum', 'themes/default/templates/CNS_EDIT_FORUM_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (662, 'cns_forum', 'themes/default/templates/CNS_EDIT_FORUM_SCREEN_GROUPING.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (663, 'cns_forum', 'themes/default/templates/CNS_EDIT_FORUM_SCREEN_FORUM.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (664, 'cns_forum', 'themes/default/templates/CNS_FORUM.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (665, 'cns_forum', 'themes/default/templates/CNS_FORUM_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (666, 'cns_forum', 'themes/default/templates/CNS_FORUM_GROUPING.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (667, 'cns_forum', 'themes/default/templates/CNS_FORUM_INTRO_QUESTION_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (668, 'cns_forum', 'themes/default/templates/CNS_FORUM_IN_GROUPING.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (669, 'cns_forum', 'themes/default/templates/CNS_FORUM_LATEST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (670, 'cns_forum', 'themes/default/templates/CNS_FORUM_TOPIC_ROW.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (671, 'cns_forum', 'themes/default/templates/CNS_FORUM_TOPIC_ROW_LAST_POST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (672, 'cns_forum', 'themes/default/templates/CNS_FORUM_TOPIC_WRAPPER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (673, 'cns_forum', 'themes/default/templates/CNS_GUEST_BAR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (674, 'cns_forum', 'themes/default/templates/CNS_GUEST_DETAILS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (675, 'cns_forum', 'themes/default/templates/CNS_POST_BOX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (676, 'cns_forum', 'themes/default/templates/CNS_MEMBER_BAR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (677, 'cns_forum', 'themes/default/templates/MEMBER_BAR_SEARCH.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (678, 'cns_forum', 'themes/default/templates/CNS_NOTIFICATION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (679, 'cns_forum', 'themes/default/templates/BLOCK_MAIN_PT_NOTIFICATIONS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (680, 'cns_forum', 'themes/default/templates/CNS_MEMBER_PROFILE_PTS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (681, 'cns_forum', 'themes/default/templates/CNS_PINNED_DIVIDER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (682, 'cns_forum', 'themes/default/templates/CNS_POSTER_GUEST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (683, 'cns_forum', 'themes/default/templates/CNS_POSTER_MEMBER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (684, 'cns_forum', 'themes/default/templates/CNS_POSTING_SCREEN_POSTS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (685, 'cns_forum', 'themes/default/text/CNS_QUOTE_FCOMCODE.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (686, 'cns_forum', 'themes/default/templates/BLOCK_MAIN_BOTTOM_BAR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (687, 'cns_forum', 'themes/default/templates/CNS_TOPIC_FIRST_UNREAD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (688, 'cns_forum', 'themes/default/templates/CNS_TOPIC_MARKER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (689, 'cns_forum', 'themes/default/templates/CNS_TOPIC_POLL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (690, 'cns_forum', 'themes/default/templates/CNS_TOPIC_POLL_ANSWER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (691, 'cns_forum', 'themes/default/templates/CNS_TOPIC_POLL_ANSWER_RADIO.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (692, 'cns_forum', 'themes/default/templates/CNS_TOPIC_POLL_ANSWER_RESULTS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (693, 'cns_forum', 'themes/default/templates/CNS_TOPIC_POLL_BUTTON.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (694, 'cns_forum', 'themes/default/templates/CNS_TOPIC_POLL_VIEW_RESULTS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (695, 'cns_forum', 'themes/default/templates/CNS_TOPIC_POST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (696, 'cns_forum', 'themes/default/templates/CNS_TOPIC_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (697, 'cns_forum', 'themes/default/templates/CNS_WHISPER_CHOICE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (698, 'cns_forum', 'themes/default/templates/BLOCK_SIDE_CNS_PRIVATE_TOPICS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (699, 'cns_forum', 'themes/default/templates/CNS_TOPIC_POST_LAST_EDITED.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (700, 'cns_forum', 'themes/default/templates/CNS_FORUM_LIST_LINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (701, 'cns_forum', 'themes/default/templates/CNS_FORUM_TOPIC_LIST_LINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (702, 'cns_forum', 'themes/default/templates/CNS_VFORUM_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (703, 'cns_forum', 'forum/index.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (704, 'cns_forum', 'forum/pages/comcode/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (705, 'cns_forum', 'forum/pages/comcode/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (706, 'cns_forum', 'forum/pages/comcode/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (707, 'cns_forum', 'forum/pages/comcode/EN/panel_right.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (708, 'cns_forum', 'forum/pages/comcode/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (709, 'cns_forum', 'forum/pages/comcode_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (710, 'cns_forum', 'forum/pages/comcode_custom/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (711, 'cns_forum', 'forum/pages/comcode_custom/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (712, 'cns_forum', 'forum/pages/comcode_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (713, 'cns_forum', 'forum/pages/html/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (714, 'cns_forum', 'forum/pages/html/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (715, 'cns_forum', 'forum/pages/html/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (716, 'cns_forum', 'forum/pages/html/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (717, 'cns_forum', 'forum/pages/html_custom/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (718, 'cns_forum', 'forum/pages/html_custom/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (719, 'cns_forum', 'forum/pages/html_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (720, 'cns_forum', 'forum/pages/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (721, 'cns_forum', 'forum/pages/minimodules/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (722, 'cns_forum', 'forum/pages/minimodules/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (723, 'cns_forum', 'forum/pages/minimodules_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (724, 'cns_forum', 'forum/pages/minimodules_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (725, 'cns_forum', 'forum/pages/modules/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (726, 'cns_forum', 'forum/pages/modules/forumview.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (727, 'cns_forum', 'forum/pages/modules/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (728, 'cns_forum', 'forum/pages/modules/topics.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (729, 'cns_forum', 'forum/pages/modules/topicview.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (730, 'cns_forum', 'forum/pages/modules/vforums.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (731, 'cns_forum', 'forum/pages/modules_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (732, 'cns_forum', 'forum/pages/modules_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (733, 'cns_forum', 'adminzone/pages/modules/admin_cns_forum_groupings.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (734, 'cns_forum', 'adminzone/pages/modules/admin_cns_forums.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (735, 'cns_forum', 'themes/default/images/cns_general/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (736, 'cns_forum', 'themes/default/images/cns_general/new_posts.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (737, 'cns_forum', 'themes/default/images/cns_general/new_posts_redirect.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (738, 'cns_forum', 'themes/default/images/cns_general/no_new_posts.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (739, 'cns_forum', 'themes/default/images/cns_general/no_new_posts_redirect.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (740, 'cns_forum', 'themes/default/images/icons/14x14/cns_topic_modifiers/announcement.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (741, 'cns_forum', 'themes/default/images/icons/14x14/cns_topic_modifiers/closed.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (742, 'cns_forum', 'themes/default/images/icons/14x14/cns_topic_modifiers/hot.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (743, 'cns_forum', 'themes/default/images/icons/14x14/cns_topic_modifiers/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (744, 'cns_forum', 'themes/default/images/icons/14x14/cns_topic_modifiers/involved.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (745, 'cns_forum', 'themes/default/images/icons/14x14/cns_topic_modifiers/pinned.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (746, 'cns_forum', 'themes/default/images/icons/14x14/cns_topic_modifiers/poll.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (747, 'cns_forum', 'themes/default/images/icons/14x14/cns_topic_modifiers/sunk.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (748, 'cns_forum', 'themes/default/images/icons/14x14/cns_topic_modifiers/unread.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (749, 'cns_forum', 'themes/default/images/icons/14x14/cns_topic_modifiers/unvalidated.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (750, 'cns_forum', 'themes/default/images/icons/28x28/cns_topic_modifiers/announcement.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (751, 'cns_forum', 'themes/default/images/icons/28x28/cns_topic_modifiers/closed.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (752, 'cns_forum', 'themes/default/images/icons/28x28/cns_topic_modifiers/hot.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (753, 'cns_forum', 'themes/default/images/icons/28x28/cns_topic_modifiers/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (754, 'cns_forum', 'themes/default/images/icons/28x28/cns_topic_modifiers/involved.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (755, 'cns_forum', 'themes/default/images/icons/28x28/cns_topic_modifiers/pinned.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (756, 'cns_forum', 'themes/default/images/icons/28x28/cns_topic_modifiers/poll.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (757, 'cns_forum', 'themes/default/images/icons/28x28/cns_topic_modifiers/sunk.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (758, 'cns_forum', 'themes/default/images/icons/28x28/cns_topic_modifiers/unread.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (759, 'cns_forum', 'themes/default/images/icons/28x28/cns_topic_modifiers/unvalidated.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (760, 'cns_forum', 'sources/blocks/side_cns_private_topics.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (761, 'cns_forum', 'sources/hooks/systems/cleanup/cns_topics.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (762, 'cns_forum', 'sources/hooks/modules/admin_newsletter/cns_forumview.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (763, 'cns_forum', 'sources/hooks/modules/admin_unvalidated/cns_posts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (764, 'cns_forum', 'sources/hooks/modules/admin_unvalidated/cns_topics.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (765, 'cns_forum', 'sources/hooks/modules/search/cns_own_pt.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (766, 'cns_forum', 'sources/hooks/modules/search/cns_posts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (767, 'cns_forum', 'sources/hooks/systems/attachments/cns_post.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (768, 'cns_forum', 'sources/hooks/systems/preview/cns_post.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (769, 'cns_forum', 'sources/hooks/systems/rss/cns_forumview.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (770, 'cns_forum', 'sources/hooks/systems/rss/cns_topicview.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (771, 'cns_forum', 'sources/blocks/main_bottom_bar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (772, 'cns_forum', 'sources/blocks/main_member_bar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (773, 'cns_forum', 'sources/blocks/main_pt_notifications.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (774, 'cns_forum', 'themes/default/templates/BLOCK_MAIN_MEMBER_BAR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (775, 'cns_forum', 'sources/cns_forumview.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (776, 'cns_forum', 'sources/cns_forumview_pt.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (777, 'cns_forum', 'sources/cns_topicview.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (778, 'cns_forum', 'sources/hooks/modules/topicview/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (779, 'cns_forum', 'sources_custom/hooks/modules/topicview/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (780, 'cns_forum', 'sources/hooks/modules/topicview/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (781, 'cns_forum', 'sources_custom/hooks/modules/topicview/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (782, 'cns_forum', 'sources/hooks/systems/ajax_tree/choose_topic.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (783, 'cns_forum', 'sources/hooks/systems/ajax_tree/choose_forum.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (784, 'cns_forum', 'sources/hooks/systems/rss/cns_unread_topics.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (785, 'cns_forum', 'sources/hooks/systems/rss/cns_private_topics.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (786, 'cns_forum', 'themes/default/templates/CNS_PRIVATE_TOPIC_LINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (787, 'cns_forum', 'themes/default/templates/CNS_PT_FILTERS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (788, 'cns_forum', 'themes/default/templates/CNS_MEMBER_PROFILE_POSTS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (789, 'cns_forum', 'sources/hooks/systems/cleanup/cns.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (790, 'cns_forum', 'sources/hooks/systems/config/edit_time_limit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (791, 'cns_forum', 'sources/hooks/systems/config/delete_time_limit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (792, 'cns_forum', 'sources/hooks/systems/config/enable_add_topic_btn_in_topic.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (793, 'cns_forum', 'sources/hooks/systems/config/enable_forum_dupe_buttons.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (794, 'cns_forum', 'sources/hooks/systems/config/enable_mark_forum_read.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (795, 'cns_forum', 'sources/hooks/systems/config/enable_mark_topic_unread.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (796, 'cns_forum', 'sources/hooks/systems/config/enable_multi_quote.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (797, 'cns_forum', 'sources/hooks/systems/config/enable_post_emphasis.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (798, 'cns_forum', 'sources/hooks/systems/config/enable_pt_filtering.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (799, 'cns_forum', 'sources/hooks/systems/config/enable_pt_restrict.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (800, 'cns_forum', 'sources/hooks/systems/config/enable_sunk.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (801, 'cns_forum', 'sources/hooks/systems/config/inline_pp_advertise.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (802, 'cns_forum', 'sources/hooks/systems/config/intro_forum_id.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (803, 'cns_forum', 'sources/hooks/systems/config/is_on_show_online.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (804, 'cns_forum', 'sources/hooks/systems/config/max_forum_detail.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (805, 'cns_forum', 'sources/hooks/systems/config/max_forum_inspect.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (806, 'cns_forum', 'sources/hooks/systems/config/private_topics_per_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (807, 'cns_forum', 'sources/hooks/systems/config/seq_post_ids.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (808, 'cns_forum', 'sources/hooks/systems/config/threaded_buttons.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (809, 'cns_forum', 'sources/hooks/systems/config/overt_whisper_suggestion.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (810, 'cns_forum', 'sources/hooks/systems/config/post_read_history_days.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (811, 'cns_forum', 'sources/hooks/systems/config/is_on_topic_descriptions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (812, 'cns_forum', 'sources/hooks/systems/config/is_on_topic_emoticons.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (813, 'cns_forum', 'sources/hooks/systems/config/is_on_post_titles.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (814, 'cns_forum', 'sources/hooks/systems/config/is_on_anonymous_posts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (815, 'cns_forum', 'sources/hooks/systems/config/force_guest_names.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (816, 'cns_forum', 'sources/hooks/systems/config/forum_posts_per_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (817, 'cns_forum', 'sources/hooks/systems/config/forum_topics_per_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (818, 'cns_forum', 'sources/hooks/systems/config/delete_trashed_pts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (819, 'cns_forum', 'sources/hooks/systems/tasks/cns_recache.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (820, 'cns_forum', 'sources/hooks/systems/tasks/cns_topics_recache.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (821, 'cns_forum', 'sources/hooks/systems/tasks/notify_topics_moved.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (822, 'cns_member_avatars', 'themes/default/images/icons/24x24/tabs/member_account/edit/avatar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (823, 'cns_member_avatars', 'themes/default/images/icons/48x48/tabs/member_account/edit/avatar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (824, 'cns_member_avatars', 'sources/hooks/systems/notifications/cns_choose_avatar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (825, 'cns_member_avatars', 'sources/hooks/systems/addon_registry/cns_member_avatars.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (826, 'cns_member_avatars', 'themes/default/templates/CNS_EDIT_AVATAR_TAB.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (827, 'cns_member_avatars', 'uploads/cns_avatars/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (828, 'cns_member_avatars', 'uploads/cns_avatars/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (829, 'cns_member_avatars', 'sources/hooks/systems/profiles_tabs_edit/avatar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (830, 'cns_member_avatars', 'themes/default/images/cns_default_avatars/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (831, 'cns_member_avatars', 'themes/default/images/cns_default_avatars/system.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (832, 'cns_member_avatars', 'sources/hooks/systems/config/random_avatars.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (833, 'cns_member_photos', 'themes/default/images/icons/24x24/tabs/member_account/edit/photo.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (834, 'cns_member_photos', 'themes/default/images/icons/48x48/tabs/member_account/edit/photo.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (835, 'cns_member_photos', 'sources/hooks/systems/addon_registry/cns_member_photos.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (836, 'cns_member_photos', 'uploads/cns_photos/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (837, 'cns_member_photos', 'uploads/cns_photos_thumbs/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (838, 'cns_member_photos', 'uploads/cns_photos/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (839, 'cns_member_photos', 'uploads/cns_photos_thumbs/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (840, 'cns_member_photos', 'sources/hooks/systems/profiles_tabs_edit/photo.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (841, 'cns_member_photos', 'sources/hooks/systems/notifications/cns_choose_photo.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (842, 'cns_member_photos', 'themes/default/templates/CNS_EDIT_PHOTO_TAB.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (843, 'cns_member_titles', 'themes/default/images/icons/24x24/tabs/member_account/edit/title.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (844, 'cns_member_titles', 'themes/default/images/icons/48x48/tabs/member_account/edit/title.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (845, 'cns_member_titles', 'sources/hooks/systems/addon_registry/cns_member_titles.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (846, 'cns_member_titles', 'sources/hooks/systems/profiles_tabs_edit/title.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (847, 'cns_member_titles', 'sources/hooks/systems/config/max_member_title_length.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (848, 'cns_multi_moderations', 'themes/default/images/icons/24x24/menu/adminzone/structure/forum/multi_moderations.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (849, 'cns_multi_moderations', 'themes/default/images/icons/48x48/menu/adminzone/structure/forum/multi_moderations.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (850, 'cns_multi_moderations', 'sources/hooks/systems/resource_meta_aware/multi_moderation.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (851, 'cns_multi_moderations', 'sources/hooks/systems/commandr_fs/multi_moderations.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (852, 'cns_multi_moderations', 'sources/hooks/systems/addon_registry/cns_multi_moderations.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (853, 'cns_multi_moderations', 'adminzone/pages/modules/admin_cns_multi_moderations.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (854, 'cns_multi_moderations', 'lang/EN/cns_multi_moderations.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (855, 'cns_post_templates', 'themes/default/images/icons/24x24/menu/adminzone/structure/forum/post_templates.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (856, 'cns_post_templates', 'themes/default/images/icons/48x48/menu/adminzone/structure/forum/post_templates.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (857, 'cns_post_templates', 'sources/hooks/systems/resource_meta_aware/post_template.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (858, 'cns_post_templates', 'sources/hooks/systems/commandr_fs/post_templates.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (859, 'cns_post_templates', 'sources/hooks/systems/addon_registry/cns_post_templates.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (860, 'cns_post_templates', 'themes/default/templates/CNS_POST_TEMPLATE_SELECT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (861, 'cns_post_templates', 'adminzone/pages/modules/admin_cns_post_templates.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (862, 'cns_post_templates', 'lang/EN/cns_post_templates.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (863, 'cns_reported_posts', 'sources/hooks/systems/addon_registry/cns_reported_posts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (864, 'cns_reported_posts', 'themes/default/text/CNS_REPORTED_POST_FCOMCODE.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (865, 'cns_reported_posts', 'sources/hooks/blocks/main_staff_checklist/reported_posts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (866, 'cns_reported_posts', 'sources/hooks/systems/config/reported_posts_forum.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (867, 'cns_signatures', 'themes/default/images/icons/24x24/tabs/member_account/edit/signature.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (868, 'cns_signatures', 'themes/default/images/icons/48x48/tabs/member_account/edit/signature.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (869, 'cns_signatures', 'sources/hooks/systems/addon_registry/cns_signatures.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (870, 'cns_signatures', 'themes/default/templates/CNS_EDIT_SIGNATURE_TAB.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (871, 'cns_signatures', 'sources/hooks/systems/attachments/cns_signature.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (872, 'cns_signatures', 'sources/hooks/systems/preview/cns_signature.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (873, 'cns_signatures', 'sources/hooks/systems/profiles_tabs_edit/signature.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (874, 'cns_signatures', 'sources/hooks/systems/notifications/cns_choose_signature.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (875, 'cns_signatures', 'sources/hooks/systems/config/enable_skip_sig.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (876, 'cns_signatures', 'sources/hooks/systems/config/enable_views_sigs_option.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (877, 'cns_thematic_avatars', 'sources/hooks/systems/addon_registry/cns_thematic_avatars.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (878, 'cns_thematic_avatars', 'themes/default/images/cns_default_avatars/default_set/thematic/animals.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (879, 'cns_thematic_avatars', 'themes/default/images/cns_default_avatars/default_set/thematic/books.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (880, 'cns_thematic_avatars', 'themes/default/images/cns_default_avatars/default_set/thematic/business.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (881, 'cns_thematic_avatars', 'themes/default/images/cns_default_avatars/default_set/thematic/chess.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (882, 'cns_thematic_avatars', 'themes/default/images/cns_default_avatars/default_set/thematic/food.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (883, 'cns_thematic_avatars', 'themes/default/images/cns_default_avatars/default_set/thematic/games.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (884, 'cns_thematic_avatars', 'themes/default/images/cns_default_avatars/default_set/thematic/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (885, 'cns_thematic_avatars', 'themes/default/images/cns_default_avatars/default_set/thematic/money.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (886, 'cns_thematic_avatars', 'themes/default/images/cns_default_avatars/default_set/thematic/music.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (887, 'cns_thematic_avatars', 'themes/default/images/cns_default_avatars/default_set/thematic/nature.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (888, 'cns_thematic_avatars', 'themes/default/images/cns_default_avatars/default_set/thematic/outdoors.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (889, 'cns_thematic_avatars', 'themes/default/images/cns_default_avatars/default_set/thematic/space.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (890, 'cns_thematic_avatars', 'themes/default/images/cns_default_avatars/default_set/thematic/sports.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (891, 'cns_thematic_avatars', 'themes/default/images/cns_default_avatars/default_set/thematic/tech.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (892, 'cns_warnings', 'themes/default/images/icons/24x24/tabs/member_account/warnings.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (893, 'cns_warnings', 'themes/default/images/icons/48x48/tabs/member_account/warnings.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (894, 'cns_warnings', 'themes/default/images/icons/24x24/links/warning_add.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (895, 'cns_warnings', 'themes/default/images/icons/48x48/links/warning_add.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (896, 'cns_warnings', 'themes/default/images/icons/24x24/buttons/warn.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (897, 'cns_warnings', 'themes/default/images/icons/48x48/buttons/warn.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (898, 'cns_warnings', 'sources/hooks/systems/addon_registry/cns_warnings.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (899, 'cns_warnings', 'site/pages/modules/warnings.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (900, 'cns_warnings', 'themes/default/templates/CNS_SAVED_WARNING.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (901, 'cns_warnings', 'themes/default/templates/CNS_WARNING_HISTORY_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (902, 'cns_warnings', 'lang/EN/cns_warnings.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (903, 'cns_warnings', 'site/warnings_browse.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (904, 'cns_warnings', 'sources/hooks/systems/profiles_tabs/warnings.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (905, 'cns_warnings', 'themes/default/templates/CNS_MEMBER_PROFILE_WARNINGS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (906, 'cns_warnings', 'themes/default/templates/CNS_WARN_SPAM_URLS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (907, 'cns_warnings', 'sources/hooks/systems/commandr_fs_extended_member/warnings.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (908, 'code_editor', 'themes/default/images/icons/24x24/menu/adminzone/tools/code_editor.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (909, 'code_editor', 'themes/default/images/icons/48x48/menu/adminzone/tools/code_editor.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (910, 'code_editor', 'sources/hooks/systems/addon_registry/code_editor.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (911, 'code_editor', 'code_editor.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (912, 'code_editor', 'exports/file_backups/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (913, 'collaboration_zone', 'themes/default/images/icons/24x24/menu/collaboration.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (914, 'collaboration_zone', 'themes/default/images/icons/48x48/menu/collaboration.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (915, 'collaboration_zone', 'themes/default/images/icons/24x24/menu/collaboration/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (916, 'collaboration_zone', 'themes/default/images/icons/48x48/menu/collaboration/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (917, 'collaboration_zone', 'themes/default/images/icons/24x24/menu/collaboration/start.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (918, 'collaboration_zone', 'themes/default/images/icons/48x48/menu/collaboration/start.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (919, 'collaboration_zone', 'sources/hooks/systems/addon_registry/collaboration_zone.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (920, 'collaboration_zone', 'sources/hooks/modules/admin_themewizard/collaboration_zone.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (921, 'collaboration_zone', 'collaboration/index.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (922, 'collaboration_zone', 'collaboration/pages/comcode/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (923, 'collaboration_zone', 'collaboration/pages/comcode/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (924, 'collaboration_zone', 'collaboration/pages/comcode/EN/about.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (925, 'collaboration_zone', 'collaboration/pages/comcode/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (926, 'collaboration_zone', 'collaboration/pages/comcode/EN/panel_right.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (927, 'collaboration_zone', 'collaboration/pages/comcode/EN/start.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (928, 'collaboration_zone', 'collaboration/pages/comcode/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (929, 'collaboration_zone', 'collaboration/pages/comcode_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (930, 'collaboration_zone', 'collaboration/pages/comcode_custom/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (931, 'collaboration_zone', 'collaboration/pages/comcode_custom/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (932, 'collaboration_zone', 'collaboration/pages/comcode_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (933, 'collaboration_zone', 'collaboration/pages/html/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (934, 'collaboration_zone', 'collaboration/pages/html/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (935, 'collaboration_zone', 'collaboration/pages/html/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (936, 'collaboration_zone', 'collaboration/pages/html/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (937, 'collaboration_zone', 'collaboration/pages/html_custom/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (938, 'collaboration_zone', 'collaboration/pages/html_custom/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (939, 'collaboration_zone', 'collaboration/pages/html_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (940, 'collaboration_zone', 'collaboration/pages/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (941, 'collaboration_zone', 'collaboration/pages/minimodules/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (942, 'collaboration_zone', 'collaboration/pages/minimodules/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (943, 'collaboration_zone', 'collaboration/pages/minimodules_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (944, 'collaboration_zone', 'collaboration/pages/minimodules_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (945, 'collaboration_zone', 'collaboration/pages/modules/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (946, 'collaboration_zone', 'collaboration/pages/modules/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (947, 'collaboration_zone', 'collaboration/pages/modules_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (948, 'collaboration_zone', 'collaboration/pages/modules_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (949, 'collaboration_zone', 'sources/hooks/systems/page_groupings/collaboration_zone.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (950, 'commandr', 'themes/default/images/icons/24x24/menu/adminzone/tools/commandr.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (951, 'commandr', 'themes/default/images/icons/48x48/menu/adminzone/tools/commandr.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (952, 'commandr', 'themes/default/images/icons/24x24/tool_buttons/commandr_off.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (953, 'commandr', 'themes/default/images/icons/24x24/tool_buttons/commandr_on.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (954, 'commandr', 'themes/default/images/icons/48x48/tool_buttons/commandr_off.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (955, 'commandr', 'themes/default/images/icons/48x48/tool_buttons/commandr_on.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (956, 'commandr', 'sources/hooks/systems/commandr_commands/find_guid_via_id.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (957, 'commandr', 'sources/hooks/systems/commandr_commands/find_id_via_guid.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (958, 'commandr', 'sources/hooks/systems/commandr_commands/find_id_via_label.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (959, 'commandr', 'sources/hooks/systems/commandr_commands/find_id_via_commandr_fs_filename.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (960, 'commandr', 'sources/hooks/systems/commandr_commands/find_label_via_id.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (961, 'commandr', 'sources/hooks/systems/commandr_commands/find_commandr_fs_filename_via_id.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (962, 'commandr', 'sources/hooks/systems/commandr_fs_extended_config/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (963, 'commandr', 'sources_custom/hooks/systems/commandr_fs_extended_config/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (964, 'commandr', 'sources/hooks/systems/commandr_fs_extended_config/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (965, 'commandr', 'sources_custom/hooks/systems/commandr_fs_extended_config/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (966, 'commandr', 'sources/hooks/systems/commandr_fs_extended_member/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (967, 'commandr', 'sources_custom/hooks/systems/commandr_fs_extended_member/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (968, 'commandr', 'sources/hooks/systems/commandr_fs_extended_member/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (969, 'commandr', 'sources_custom/hooks/systems/commandr_fs_extended_member/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (970, 'commandr', 'sources/hooks/systems/commandr_fs_extended_config/privileges.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (971, 'commandr', 'sources/hooks/systems/config/bottom_show_commandr_button.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (972, 'commandr', 'sources/hooks/systems/config/commandr_chat_announce.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (973, 'commandr', 'sources/hooks/systems/commandr_fs/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (974, 'commandr', 'sources_custom/hooks/systems/commandr_fs/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (975, 'commandr', 'sources/hooks/systems/commandr_notifications/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (976, 'commandr', 'sources_custom/hooks/systems/commandr_notifications/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (977, 'commandr', 'sources/hooks/systems/addon_registry/commandr.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (978, 'commandr', 'sources/hooks/systems/commandr_commands/antispam_check.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (979, 'commandr', 'sources/hooks/systems/commandr_commands/set_comment_forum.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (980, 'commandr', 'sources/resource_fs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (981, 'commandr', 'sources/resource_fs_base_class.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (982, 'commandr', 'data/modules/admin_commandr/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (983, 'commandr', 'themes/default/templates/COMMANDR_HELP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (984, 'commandr', 'themes/default/templates/COMMANDR_LS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (985, 'commandr', 'themes/default/templates/COMMANDR_MAIN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (986, 'commandr', 'themes/default/javascript/commandr.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (987, 'commandr', 'themes/default/templates/COMMANDR_ARRAY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (988, 'commandr', 'themes/default/templates/COMMANDR_BOX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (989, 'commandr', 'themes/default/templates/COMMANDR_CHAT_NOTIFICATION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (990, 'commandr', 'themes/default/templates/COMMANDR_COMMAND.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (991, 'commandr', 'themes/default/templates/COMMANDR_COMMANDS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (992, 'commandr', 'themes/default/templates/COMMANDR_EDIT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (993, 'commandr', 'themes/default/templates/COMMANDR_ENTRY_POINTS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (994, 'commandr', 'themes/default/templates/COMMANDR_FIND_CODES.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (995, 'commandr', 'themes/default/templates/COMMANDR_MAIN_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (996, 'commandr', 'themes/default/templates/COMMANDR_NOTIFICATION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (997, 'commandr', 'themes/default/templates/COMMANDR_COMMANDRCHAT_NOTIFICATION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (998, 'commandr', 'themes/default/templates/COMMANDR_CNS_NOTIFICATION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (999, 'commandr', 'themes/default/templates/COMMANDR_PT_NOTIFICATION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1000, 'commandr', 'themes/default/templates/COMMANDR_RSS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1001, 'commandr', 'themes/default/templates/COMMANDR_USERS_ONLINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1002, 'commandr', 'themes/default/templates/COMMANDR_WHOIS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1003, 'commandr', 'themes/default/javascript/button_commandr.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1004, 'commandr', 'adminzone/pages/modules/admin_commandr.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1005, 'commandr', 'themes/default/css/commandr.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1006, 'commandr', 'data/modules/admin_commandr/admin_commandrsample_script');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1007, 'commandr', 'data/modules/admin_commandr/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1008, 'commandr', 'data/modules/admin_commandr/sample_script');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1009, 'commandr', 'data/modules/admin_commandr/test_script');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1010, 'commandr', 'data/commandr.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1011, 'commandr', 'lang/EN/commandr.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1012, 'commandr', 'sources/hooks/systems/commandr_commands/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1013, 'commandr', 'sources_custom/hooks/systems/commandr_commands/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1014, 'commandr', 'sources/hooks/systems/commandr_commands/alien_check.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1015, 'commandr', 'sources/hooks/systems/commandr_commands/directory_sizes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1016, 'commandr', 'sources/hooks/systems/commandr_commands/db_table_sizes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1017, 'commandr', 'sources/hooks/systems/commandr_commands/sql_dump.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1018, 'commandr', 'sources/hooks/systems/commandr_commands/db_search.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1019, 'commandr', 'sources/hooks/systems/commandr_commands/append.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1020, 'commandr', 'sources/hooks/systems/commandr_commands/ban_ip.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1021, 'commandr', 'sources/hooks/systems/commandr_commands/ban_member.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1022, 'commandr', 'sources/hooks/systems/commandr_commands/bsod.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1023, 'commandr', 'sources/hooks/systems/commandr_commands/phpinfo.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1024, 'commandr', 'sources/hooks/systems/commandr_commands/call.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1025, 'commandr', 'sources/hooks/systems/commandr_commands/cat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1026, 'commandr', 'sources/hooks/systems/commandr_commands/cd.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1027, 'commandr', 'sources/hooks/systems/commandr_commands/clear.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1028, 'commandr', 'sources/hooks/systems/commandr_commands/clear_caches.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1029, 'commandr', 'sources/hooks/systems/commandr_commands/closed.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1030, 'commandr', 'sources/hooks/systems/commandr_commands/commands.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1031, 'commandr', 'sources/hooks/systems/commandr_commands/cp.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1032, 'commandr', 'sources/hooks/systems/commandr_commands/cpdir.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1033, 'commandr', 'sources/hooks/systems/commandr_commands/date.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1034, 'commandr', 'sources/hooks/systems/commandr_commands/echo.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1035, 'commandr', 'sources/hooks/systems/commandr_commands/edit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1036, 'commandr', 'sources/hooks/systems/commandr_commands/exit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1037, 'commandr', 'sources/hooks/systems/commandr_commands/feedback.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1038, 'commandr', 'sources/hooks/systems/commandr_commands/find.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1039, 'commandr', 'sources/hooks/systems/commandr_commands/find_codes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1040, 'commandr', 'sources/hooks/systems/commandr_commands/find_entry_points.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1041, 'commandr', 'sources/hooks/systems/commandr_commands/fix_perms.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1042, 'commandr', 'sources/hooks/systems/commandr_commands/grep.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1043, 'commandr', 'sources/hooks/systems/commandr_commands/help.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1044, 'commandr', 'sources/hooks/systems/commandr_commands/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1045, 'commandr', 'sources_custom/hooks/systems/commandr_commands/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1046, 'commandr', 'sources/hooks/systems/commandr_commands/ls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1047, 'commandr', 'sources/hooks/systems/commandr_commands/mkdir.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1048, 'commandr', 'sources/hooks/systems/commandr_commands/mv.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1049, 'commandr', 'sources/hooks/systems/commandr_commands/mvdir.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1050, 'commandr', 'sources/hooks/systems/commandr_commands/commandrchat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1051, 'commandr', 'sources/hooks/systems/commandr_commands/passwd.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1052, 'commandr', 'sources/hooks/systems/commandr_commands/pwd.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1053, 'commandr', 'sources/hooks/systems/commandr_commands/read.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1054, 'commandr', 'sources/hooks/systems/commandr_commands/reset.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1055, 'commandr', 'sources/hooks/systems/commandr_commands/rm.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1056, 'commandr', 'sources/hooks/systems/commandr_commands/rmdir.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1057, 'commandr', 'sources/hooks/systems/commandr_commands/time.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1058, 'commandr', 'sources/hooks/systems/commandr_commands/untar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1059, 'commandr', 'sources/hooks/systems/commandr_commands/users_online.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1060, 'commandr', 'sources/hooks/systems/commandr_commands/version.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1061, 'commandr', 'sources/hooks/systems/commandr_commands/whoami.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1062, 'commandr', 'sources/hooks/systems/commandr_commands/whois.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1063, 'commandr', 'sources/hooks/systems/commandr_commands/write.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1064, 'commandr', 'sources/hooks/systems/commandr_commands/database_upgrade.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1065, 'commandr', 'sources/hooks/systems/commandr_commands/check_perms.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1066, 'commandr', 'sources/hooks/systems/commandr_commands/integrity_check.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1067, 'commandr', 'sources/hooks/systems/commandr_commands/deep_clean.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1068, 'commandr', 'sources/hooks/systems/commandr_fs/bin.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1069, 'commandr', 'sources/hooks/systems/commandr_fs/database.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1070, 'commandr', 'sources/hooks/systems/commandr_fs/etc.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1071, 'commandr', 'sources/hooks/systems/commandr_fs/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1072, 'commandr', 'sources_custom/hooks/systems/commandr_fs/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1073, 'commandr', 'sources/hooks/systems/commandr_fs/raw.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1074, 'commandr', 'sources/hooks/systems/commandr_fs/root.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1075, 'commandr', 'sources/hooks/systems/commandr_notifications/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1076, 'commandr', 'sources_custom/hooks/systems/commandr_notifications/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1077, 'commandr', 'sources/hooks/systems/commandr_notifications/commandrchat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1078, 'commandr', 'sources/hooks/systems/page_groupings/commandr.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1079, 'commandr', 'sources/hooks/systems/snippets/commandr.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1080, 'commandr', 'sources/commandr.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1081, 'commandr', 'sources/commandr_fs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1082, 'content_privacy', 'sources/hooks/systems/addon_registry/content_privacy.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1083, 'content_privacy', 'sources/content_privacy2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1084, 'content_privacy', 'sources/content_privacy.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1085, 'content_privacy', 'sources/hooks/systems/notifications/invited_content.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1086, 'content_privacy', 'lang/EN/content_privacy.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1087, 'content_reviews', 'sources/hooks/systems/addon_registry/content_reviews.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1088, 'content_reviews', 'sources/hooks/systems/cron/content_reviews.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1089, 'content_reviews', 'sources/hooks/blocks/main_staff_checklist/content_reviews.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1090, 'content_reviews', 'sources/hooks/systems/notifications/content_reviews.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1091, 'content_reviews', 'adminzone/pages/modules/admin_content_reviews.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1092, 'content_reviews', 'sources/content_reviews.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1093, 'content_reviews', 'sources/content_reviews2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1094, 'content_reviews', 'lang/EN/content_reviews.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1095, 'content_reviews', 'themes/default/templates/REVIEW_STATUS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1096, 'content_reviews', 'themes/default/images/icons/24x24/menu/adminzone/audit/content_reviews.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1097, 'content_reviews', 'themes/default/images/icons/48x48/menu/adminzone/audit/content_reviews.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1098, 'core', 'data/empty.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1099, 'core', 'adminzone/pages/comcode/EN/_modsecurity.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1100, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/merge.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1101, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/merge.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1102, 'core', 'themes/default/images/icons/24x24/menu/rich_content.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1103, 'core', 'themes/default/images/icons/48x48/menu/rich_content.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1104, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/add_one_category.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1105, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/add_one_category.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1106, 'core', 'themes/default/images/icons/24x24/menu/pages.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1107, 'core', 'themes/default/images/icons/48x48/menu/pages.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1108, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/edit_one_category.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1109, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/edit_one_category.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1110, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/export.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1111, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/export.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1112, 'core', 'themes/default/images/icons/24x24/menu/rich_content/atoz.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1113, 'core', 'themes/default/images/icons/48x48/menu/rich_content/atoz.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1114, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/import_csv.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1115, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/import_csv.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1116, 'core', 'themes/default/images/icons/24x24/menu/social.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1117, 'core', 'themes/default/images/icons/24x24/menu/social/members.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1118, 'core', 'themes/default/images/icons/48x48/menu/social.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1119, 'core', 'themes/default/images/icons/48x48/menu/social/members.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1120, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/delete.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1121, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/delete.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1122, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/tool.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1123, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/tool.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1124, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1125, 'core', 'themes/default/images/icons/24x24/menu/_generic_spare/1.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1126, 'core', 'themes/default/images/icons/24x24/menu/_generic_spare/2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1127, 'core', 'themes/default/images/icons/24x24/menu/_generic_spare/3.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1128, 'core', 'themes/default/images/icons/24x24/menu/_generic_spare/4.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1129, 'core', 'themes/default/images/icons/24x24/menu/_generic_spare/5.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1130, 'core', 'themes/default/images/icons/24x24/menu/_generic_spare/6.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1131, 'core', 'themes/default/images/icons/24x24/menu/_generic_spare/7.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1132, 'core', 'themes/default/images/icons/24x24/menu/_generic_spare/8.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1133, 'core', 'themes/default/images/icons/24x24/menu/_generic_spare/features.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1134, 'core', 'themes/default/images/icons/24x24/menu/_generic_spare/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1135, 'core', 'themes/default/images/icons/24x24/menu/_generic_spare/page.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1136, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1137, 'core', 'themes/default/images/icons/48x48/menu/_generic_spare/1.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1138, 'core', 'themes/default/images/icons/48x48/menu/_generic_spare/2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1139, 'core', 'themes/default/images/icons/48x48/menu/_generic_spare/3.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1140, 'core', 'themes/default/images/icons/48x48/menu/_generic_spare/4.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1141, 'core', 'themes/default/images/icons/48x48/menu/_generic_spare/5.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1142, 'core', 'themes/default/images/icons/48x48/menu/_generic_spare/6.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1143, 'core', 'themes/default/images/icons/48x48/menu/_generic_spare/7.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1144, 'core', 'themes/default/images/icons/48x48/menu/_generic_spare/8.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1145, 'core', 'themes/default/images/icons/48x48/menu/_generic_spare/features.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1146, 'core', 'themes/default/images/icons/48x48/menu/_generic_spare/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1147, 'core', 'themes/default/images/icons/48x48/menu/_generic_spare/page.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1148, 'core', 'themes/default/images/icons/24x24/tabs/edit.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1149, 'core', 'themes/default/images/icons/24x24/tabs/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1150, 'core', 'themes/default/images/icons/48x48/tabs/edit.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1151, 'core', 'themes/default/images/icons/48x48/tabs/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1152, 'core', 'themes/default/images/icons/24x24/menu/pages/about_us.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1153, 'core', 'themes/default/images/icons/48x48/menu/pages/about_us.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1154, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/add_one.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1155, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/add_one.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1156, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/add_to_category.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1157, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/add_to_category.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1158, 'core', 'themes/default/images/icons/24x24/menu/adminzone/adminzone.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1159, 'core', 'themes/default/images/icons/48x48/menu/adminzone/adminzone.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1160, 'core', 'themes/default/images/icons/24x24/buttons/advanced.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1161, 'core', 'themes/default/images/icons/48x48/buttons/advanced.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1162, 'core', 'themes/default/images/icons/24x24/buttons/help.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1163, 'core', 'themes/default/images/icons/48x48/buttons/help.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1164, 'core', 'themes/default/images/icons/24x24/buttons/simple.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1165, 'core', 'themes/default/images/icons/48x48/buttons/simple.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1166, 'core', 'themes/default/images/icons/24x24/buttons/all.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1167, 'core', 'themes/default/images/icons/48x48/buttons/all.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1168, 'core', 'themes/default/images/icons/24x24/buttons/all2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1169, 'core', 'themes/default/images/icons/48x48/buttons/all2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1170, 'core', 'themes/default/images/icons/24x24/menu/adminzone/audit.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1171, 'core', 'themes/default/images/icons/48x48/menu/adminzone/audit.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1172, 'core', 'themes/default/images/icons/32x32/menu/adminzone/audit.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1173, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/back.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1174, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/back.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1175, 'core', 'themes/default/images/icons/24x24/buttons/back.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1176, 'core', 'themes/default/images/icons/48x48/buttons/back.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1177, 'core', 'themes/default/images/icons/24x24/menu/adminzone/setup/config/base_config.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1178, 'core', 'themes/default/images/icons/48x48/menu/adminzone/setup/config/base_config.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1179, 'core', 'themes/default/images/icons/24x24/buttons/cancel.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1180, 'core', 'themes/default/images/icons/48x48/buttons/cancel.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1181, 'core', 'themes/default/images/icons/24x24/buttons/choose.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1182, 'core', 'themes/default/images/icons/48x48/buttons/choose.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1183, 'core', 'themes/default/images/icons/24x24/buttons/clear.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1184, 'core', 'themes/default/images/icons/48x48/buttons/clear.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1185, 'core', 'themes/default/images/icons/24x24/buttons/closed.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1186, 'core', 'themes/default/images/icons/48x48/buttons/closed.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1187, 'core', 'themes/default/images/icons/24x24/buttons/save_and_stay.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1188, 'core', 'themes/default/images/icons/48x48/buttons/save_and_stay.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1189, 'core', 'themes/default/images/icons/24x24/buttons/filter.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1190, 'core', 'themes/default/images/icons/48x48/buttons/filter.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1191, 'core', 'themes/default/images/icons/24x24/buttons/new_comment.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1192, 'core', 'themes/default/images/icons/48x48/buttons/new_comment.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1193, 'core', 'themes/default/images/icons/24x24/menu/cms/cms.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1194, 'core', 'themes/default/images/icons/48x48/menu/cms/cms.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1195, 'core', 'themes/default/images/icons/32x32/menu/cms/cms.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1196, 'core', 'themes/default/images/icons/64x64/menu/cms/cms.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1197, 'core', 'themes/default/images/icons/64x64/menu/adminzone/help.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1198, 'core', 'themes/default/images/icons/24x24/links/download_as_archive.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1199, 'core', 'themes/default/images/icons/48x48/links/download_as_archive.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1200, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/download_csv.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1201, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/download_csv.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1202, 'core', 'themes/default/images/icons/24x24/buttons/edit.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1203, 'core', 'themes/default/images/icons/48x48/buttons/edit.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1204, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/edit_one.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1205, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/edit_one.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1206, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/edit_this.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1207, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/edit_this.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1208, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/edit_this_category.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1209, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/edit_this_category.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1210, 'core', 'themes/default/images/icons/24x24/menu/adminzone/audit/email_log.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1211, 'core', 'themes/default/images/icons/48x48/menu/adminzone/audit/email_log.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1212, 'core', 'themes/default/images/icons/24x24/menu/adminzone/help.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1213, 'core', 'themes/default/images/icons/48x48/menu/adminzone/help.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1214, 'core', 'themes/default/images/icons/32x32/menu/adminzone/help.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1215, 'core', 'themes/default/images/icons/24x24/menu/site_meta/user_actions/join.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1216, 'core', 'themes/default/images/icons/48x48/menu/site_meta/user_actions/join.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1217, 'core', 'themes/default/images/icons/24x24/menu/pages/keymap.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1218, 'core', 'themes/default/images/icons/48x48/menu/pages/keymap.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1219, 'core', 'themes/default/images/icons/24x24/menu/site_meta/user_actions/login.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1220, 'core', 'themes/default/images/icons/48x48/menu/site_meta/user_actions/login.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1221, 'core', 'themes/default/images/icons/24x24/menu/site_meta/user_actions/logout.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1222, 'core', 'themes/default/images/icons/48x48/menu/site_meta/user_actions/logout.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1223, 'core', 'themes/default/images/icons/24x24/menu/site_meta/user_actions/concede.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1224, 'core', 'themes/default/images/icons/48x48/menu/site_meta/user_actions/concede.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1225, 'core', 'themes/default/images/icons/24x24/menu/site_meta/user_actions/invisible.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1226, 'core', 'themes/default/images/icons/48x48/menu/site_meta/user_actions/invisible.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1227, 'core', 'themes/default/images/icons/24x24/buttons/more.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1228, 'core', 'themes/default/images/icons/48x48/buttons/more.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1229, 'core', 'themes/default/images/icons/24x24/buttons/move.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1230, 'core', 'themes/default/images/icons/48x48/buttons/move.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1231, 'core', 'themes/default/images/icons/24x24/buttons/new_post_full.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1232, 'core', 'themes/default/images/icons/48x48/buttons/new_post_full.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1233, 'core', 'themes/default/images/icons/24x24/buttons/next.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1234, 'core', 'themes/default/images/icons/48x48/buttons/next.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1235, 'core', 'themes/default/images/icons/24x24/buttons/no.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1236, 'core', 'themes/default/images/icons/48x48/buttons/no.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1237, 'core', 'themes/default/images/icons/24x24/buttons/next_none.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1238, 'core', 'themes/default/images/icons/48x48/buttons/next_none.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1239, 'core', 'themes/default/images/icons/24x24/buttons/previous_none.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1240, 'core', 'themes/default/images/icons/48x48/buttons/previous_none.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1241, 'core', 'themes/default/images/icons/24x24/tabs/preview.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1242, 'core', 'themes/default/images/icons/48x48/tabs/preview.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1243, 'core', 'themes/default/images/icons/24x24/buttons/previous.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1244, 'core', 'themes/default/images/icons/48x48/buttons/previous.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1245, 'core', 'themes/default/images/icons/24x24/menu/pages/privacy_policy.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1246, 'core', 'themes/default/images/icons/48x48/menu/pages/privacy_policy.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1247, 'core', 'themes/default/images/icons/24x24/buttons/convert.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1248, 'core', 'themes/default/images/icons/48x48/buttons/convert.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1249, 'core', 'themes/default/images/icons/24x24/buttons/fullsize.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1250, 'core', 'themes/default/images/icons/48x48/buttons/fullsize.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1251, 'core', 'themes/default/images/icons/24x24/buttons/thumbnail.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1252, 'core', 'themes/default/images/icons/48x48/buttons/thumbnail.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1253, 'core', 'themes/default/images/icons/24x24/buttons/redirect.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1254, 'core', 'themes/default/images/icons/48x48/buttons/redirect.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1255, 'core', 'themes/default/images/icons/24x24/buttons/new_reply.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1256, 'core', 'themes/default/images/icons/48x48/buttons/new_reply.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1257, 'core', 'themes/default/images/icons/24x24/buttons/report.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1258, 'core', 'themes/default/images/icons/48x48/buttons/report.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1259, 'core', 'themes/default/images/icons/24x24/menu/pages/rules.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1260, 'core', 'themes/default/images/icons/48x48/menu/pages/rules.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1261, 'core', 'themes/default/images/icons/24x24/buttons/search.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1262, 'core', 'themes/default/images/icons/48x48/buttons/search.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1263, 'core', 'themes/default/images/icons/24x24/menu/adminzone/security.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1264, 'core', 'themes/default/images/icons/48x48/menu/adminzone/security.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1265, 'core', 'themes/default/images/icons/32x32/menu/adminzone/security.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1266, 'core', 'themes/default/images/icons/64x64/menu/adminzone/security.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1267, 'core', 'themes/default/images/icons/24x24/menu/adminzone/setup.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1268, 'core', 'themes/default/images/icons/48x48/menu/adminzone/setup.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1269, 'core', 'themes/default/images/icons/32x32/menu/adminzone/setup.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1270, 'core', 'themes/default/images/icons/64x64/menu/adminzone/setup.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1271, 'core', 'themes/default/images/icons/24x24/menu/site_meta.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1272, 'core', 'themes/default/images/icons/48x48/menu/site_meta.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1273, 'core', 'themes/default/images/icons/24x24/tool_buttons/software_chat.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1274, 'core', 'themes/default/images/icons/48x48/tool_buttons/software_chat.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1275, 'core', 'themes/default/images/icons/24x24/menu/start.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1276, 'core', 'themes/default/images/icons/48x48/menu/start.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1277, 'core', 'themes/default/images/icons/24x24/menu/welcome.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1278, 'core', 'themes/default/images/icons/48x48/menu/welcome.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1279, 'core', 'themes/default/images/icons/24x24/menu/adminzone/start.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1280, 'core', 'themes/default/images/icons/48x48/menu/adminzone/start.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1281, 'core', 'themes/default/images/icons/32x32/menu/adminzone/start.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1282, 'core', 'themes/default/images/icons/64x64/menu/adminzone/start.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1283, 'core', 'themes/default/images/icons/24x24/menu/adminzone/structure.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1284, 'core', 'themes/default/images/icons/48x48/menu/adminzone/structure.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1285, 'core', 'themes/default/images/icons/32x32/menu/adminzone/structure.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1286, 'core', 'themes/default/images/icons/64x64/menu/adminzone/structure.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1287, 'core', 'themes/default/images/icons/24x24/menu/adminzone/style.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1288, 'core', 'themes/default/images/icons/48x48/menu/adminzone/style.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1289, 'core', 'themes/default/images/icons/32x32/menu/adminzone/style.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1290, 'core', 'themes/default/images/icons/64x64/menu/adminzone/style.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1291, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/sync.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1292, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/sync.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1293, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/xml.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1294, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/xml.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1295, 'core', 'themes/default/images/icons/24x24/menu/adminzone/tools.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1296, 'core', 'themes/default/images/icons/48x48/menu/adminzone/tools.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1297, 'core', 'themes/default/images/icons/32x32/menu/adminzone/tools.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1298, 'core', 'themes/default/images/icons/64x64/menu/adminzone/tools.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1299, 'core', 'themes/default/images/icons/24x24/tool_buttons/top.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1300, 'core', 'themes/default/images/icons/48x48/tool_buttons/top.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1301, 'core', 'themes/default/images/icons/24x24/buttons/upload.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1302, 'core', 'themes/default/images/icons/48x48/buttons/upload.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1303, 'core', 'themes/default/images/icons/64x64/menu/adminzone/audit.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1304, 'core', 'themes/default/images/icons/24x24/menu/social/users_online.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1305, 'core', 'themes/default/images/icons/48x48/menu/social/users_online.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1306, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/view_archive.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1307, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/view_archive.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1308, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/view_this.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1309, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/view_this.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1310, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/view_this_category.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1311, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/view_this_category.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1312, 'core', 'themes/default/images/icons/24x24/tabs/settings.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1313, 'core', 'themes/default/images/icons/48x48/tabs/settings.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1314, 'core', 'themes/default/images/icons/24x24/menu/_generic_admin/import.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1315, 'core', 'themes/default/images/icons/48x48/menu/_generic_admin/import.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1316, 'core', 'themes/default/images/icons/24x24/buttons/yes.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1317, 'core', 'themes/default/images/icons/48x48/buttons/yes.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1318, 'core', 'themes/default/images/icons/24x24/links/print.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1319, 'core', 'themes/default/images/icons/48x48/links/print.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1320, 'core', 'themes/default/images/icons/24x24/links/rss.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1321, 'core', 'themes/default/images/icons/48x48/links/rss.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1322, 'core', 'themes/default/images/icons/24x24/menu/pages/help.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1323, 'core', 'themes/default/images/icons/48x48/menu/pages/help.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1324, 'core', 'themes/default/images/icons/24x24/menu/adminzone/structure/zones/zones.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1325, 'core', 'themes/default/images/icons/48x48/menu/adminzone/structure/zones/zones.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1326, 'core', 'themes/default/images/icons/24x24/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1327, 'core', 'themes/default/images/icons/32x32/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1328, 'core', 'themes/default/images/icons/48x48/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1329, 'core', 'themes/default/images/icons/64x64/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1330, 'core', 'themes/default/images/icons/24x24/buttons/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1331, 'core', 'themes/default/images/icons/24x24/feedback/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1332, 'core', 'themes/default/images/icons/24x24/links/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1333, 'core', 'themes/default/images/icons/24x24/menu/adminzone/audit/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1334, 'core', 'themes/default/images/icons/24x24/menu/adminzone/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1335, 'core', 'themes/default/images/icons/24x24/menu/adminzone/security/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1336, 'core', 'themes/default/images/icons/24x24/menu/adminzone/setup/config/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1337, 'core', 'themes/default/images/icons/24x24/menu/adminzone/setup/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1338, 'core', 'themes/default/images/icons/24x24/menu/adminzone/structure/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1339, 'core', 'themes/default/images/icons/24x24/menu/adminzone/structure/sitemap/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1340, 'core', 'themes/default/images/icons/24x24/menu/adminzone/structure/zones/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1341, 'core', 'themes/default/images/icons/24x24/menu/adminzone/style/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1342, 'core', 'themes/default/images/icons/24x24/menu/adminzone/style/themes/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1343, 'core', 'themes/default/images/icons/24x24/menu/adminzone/tools/bulk_content_actions/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1344, 'core', 'themes/default/images/icons/24x24/menu/adminzone/tools/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1345, 'core', 'themes/default/images/icons/24x24/menu/adminzone/tools/users/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1346, 'core', 'themes/default/images/icons/24x24/menu/cms/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1347, 'core', 'themes/default/images/icons/24x24/menu/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1348, 'core', 'themes/default/images/icons/24x24/menu/pages/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1349, 'core', 'themes/default/images/icons/24x24/menu/rich_content/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1350, 'core', 'themes/default/images/icons/24x24/menu/site_meta/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1351, 'core', 'themes/default/images/icons/24x24/menu/site_meta/user_actions/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1352, 'core', 'themes/default/images/icons/24x24/menu/social/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1353, 'core', 'themes/default/images/icons/24x24/status/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1354, 'core', 'themes/default/images/icons/24x24/tool_buttons/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1355, 'core', 'themes/default/images/icons/48x48/buttons/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1356, 'core', 'themes/default/images/icons/48x48/feedback/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1357, 'core', 'themes/default/images/icons/48x48/links/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1358, 'core', 'themes/default/images/icons/48x48/menu/adminzone/audit/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1359, 'core', 'themes/default/images/icons/48x48/menu/adminzone/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1360, 'core', 'themes/default/images/icons/48x48/menu/adminzone/security/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1361, 'core', 'themes/default/images/icons/48x48/menu/adminzone/setup/config/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1362, 'core', 'themes/default/images/icons/48x48/menu/adminzone/setup/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1363, 'core', 'themes/default/images/icons/48x48/menu/adminzone/structure/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1364, 'core', 'themes/default/images/icons/48x48/menu/adminzone/structure/sitemap/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1365, 'core', 'themes/default/images/icons/48x48/menu/adminzone/structure/zones/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1366, 'core', 'themes/default/images/icons/48x48/menu/adminzone/style/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1367, 'core', 'themes/default/images/icons/48x48/menu/adminzone/style/themes/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1368, 'core', 'themes/default/images/icons/48x48/menu/adminzone/tools/bulk_content_actions/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1369, 'core', 'themes/default/images/icons/48x48/menu/adminzone/tools/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1370, 'core', 'themes/default/images/icons/48x48/menu/adminzone/tools/users/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1371, 'core', 'themes/default/images/icons/48x48/menu/cms/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1372, 'core', 'themes/default/images/icons/48x48/menu/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1373, 'core', 'themes/default/images/icons/48x48/menu/pages/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1374, 'core', 'themes/default/images/icons/48x48/menu/rich_content/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1375, 'core', 'themes/default/images/icons/48x48/menu/site_meta/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1376, 'core', 'themes/default/images/icons/48x48/menu/site_meta/user_actions/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1377, 'core', 'themes/default/images/icons/48x48/menu/social/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1378, 'core', 'themes/default/images/icons/48x48/status/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1379, 'core', 'themes/default/images/icons/48x48/tool_buttons/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1380, 'core', 'themes/default/images/icons/24x24/buttons/calculate.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1381, 'core', 'themes/default/images/icons/24x24/buttons/save.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1382, 'core', 'themes/default/images/icons/24x24/buttons/send.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1383, 'core', 'themes/default/images/icons/24x24/buttons/sort.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1384, 'core', 'themes/default/images/icons/24x24/buttons/proceed.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1385, 'core', 'themes/default/images/icons/24x24/buttons/copy.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1386, 'core', 'themes/default/images/icons/24x24/buttons/vote.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1387, 'core', 'themes/default/images/icons/48x48/buttons/calculate.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1388, 'core', 'themes/default/images/icons/48x48/buttons/save.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1389, 'core', 'themes/default/images/icons/48x48/buttons/send.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1390, 'core', 'themes/default/images/icons/48x48/buttons/sort.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1391, 'core', 'themes/default/images/icons/48x48/buttons/proceed.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1392, 'core', 'themes/default/images/icons/48x48/buttons/copy.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1393, 'core', 'themes/default/images/icons/48x48/buttons/vote.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1394, 'core', 'themes/default/images/icons/24x24/buttons/gotopage.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1395, 'core', 'themes/default/images/icons/24x24/buttons/perpage.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1396, 'core', 'themes/default/images/icons/24x24/buttons/skip.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1397, 'core', 'themes/default/images/icons/48x48/buttons/gotopage.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1398, 'core', 'themes/default/images/icons/48x48/buttons/perpage.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1399, 'core', 'themes/default/images/icons/48x48/buttons/skip.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1400, 'core', 'themes/default/images/icons/32x32/menu/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1401, 'core', 'themes/default/images/icons/32x32/menu/adminzone/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1402, 'core', 'themes/default/images/icons/32x32/menu/cms/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1403, 'core', 'themes/default/images/icons/64x64/menu/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1404, 'core', 'themes/default/images/icons/64x64/menu/adminzone/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1405, 'core', 'themes/default/images/icons/64x64/menu/cms/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1406, 'core', 'themes/default/images/icons/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1407, 'core', 'themes/default/images/1x/boxless_title_leadin_leftcomp.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1408, 'core', 'themes/default/images/1x/boxless_title_leadin_rightcomp.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1409, 'core', 'themes/default/images/1x/breadcrumbs.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1410, 'core', 'themes/default/images/icons/16x16/close.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1411, 'core', 'themes/default/images/icons/16x16/help.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1412, 'core', 'themes/default/images/1x/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1413, 'core', 'themes/default/images/icons/14x14/rating.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1414, 'core', 'themes/default/images/icons/28x28/rating.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1415, 'core', 'themes/default/images/1x/trays/contract.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1416, 'core', 'themes/default/images/1x/trays/expand.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1417, 'core', 'themes/default/images/1x/trays/expcon.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1418, 'core', 'themes/default/images/1x/trays/contract2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1419, 'core', 'themes/default/images/1x/trays/expand2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1420, 'core', 'themes/default/images/1x/trays/expcon2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1421, 'core', 'themes/default/images/1x/trays/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1422, 'core', 'themes/default/images/2x/trays/contract.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1423, 'core', 'themes/default/images/2x/trays/expand.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1424, 'core', 'themes/default/images/2x/trays/expcon.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1425, 'core', 'themes/default/images/2x/trays/contract2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1426, 'core', 'themes/default/images/2x/trays/expand2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1427, 'core', 'themes/default/images/2x/trays/expcon2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1428, 'core', 'themes/default/images/2x/trays/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1429, 'core', 'themes/default/images/2x/boxless_title_leadin_leftcomp.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1430, 'core', 'themes/default/images/2x/boxless_title_leadin_rightcomp.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1431, 'core', 'themes/default/images/2x/breadcrumbs.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1432, 'core', 'themes/default/images/icons/32x32/close.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1433, 'core', 'themes/default/images/icons/32x32/help.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1434, 'core', 'themes/default/images/2x/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1435, 'core', 'themes/default/images/banner_frame.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1436, 'core', 'themes/default/images/icons/14x14/add.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1437, 'core', 'themes/default/images/icons/14x14/edit2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1438, 'core', 'themes/default/images/icons/14x14/export.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1439, 'core', 'themes/default/images/icons/14x14/proceed.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1440, 'core', 'themes/default/images/icons/14x14/remove.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1441, 'core', 'themes/default/images/icons/14x14/remove_manage.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1442, 'core', 'themes/default/images/icons/14x14/tools.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1443, 'core', 'themes/default/images/icons/28x28/add.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1444, 'core', 'themes/default/images/icons/28x28/edit2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1445, 'core', 'themes/default/images/icons/28x28/export.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1446, 'core', 'themes/default/images/icons/28x28/proceed.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1447, 'core', 'themes/default/images/icons/28x28/remove.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1448, 'core', 'themes/default/images/icons/28x28/remove_manage.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1449, 'core', 'themes/default/images/icons/28x28/tools.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1450, 'core', 'sources/hooks/systems/resource_meta_aware/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1451, 'core', 'sources_custom/hooks/systems/resource_meta_aware/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1452, 'core', 'sources/hooks/systems/resource_meta_aware/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1453, 'core', 'sources_custom/hooks/systems/resource_meta_aware/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1454, 'core', 'sources/hooks/systems/commandr_fs_extended_member/group_timeouts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1455, 'core', 'sources/block_add.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1456, 'core', 'themes/default/css/carousels.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1457, 'core', 'themes/default/css/adminzone.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1458, 'core', 'themes/default/templates/BLOCK_MAIN_CONTENT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1459, 'core', 'themes/default/templates/BLOCK_MAIN_MULTI_CONTENT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1460, 'core', 'sources/blocks/main_content.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1461, 'core', 'sources/blocks/main_multi_content.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1462, 'core', 'themes/default/images/icons/14x14/action_small.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1463, 'core', 'themes/default/images/icons/28x28/action_small.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1464, 'core', 'sources/hooks/systems/symbols/BETA_CSS_PROPERTY.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1465, 'core', 'sources/antispam.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1466, 'core', 'sources/static_cache.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1467, 'core', 'sources/hooks/systems/notifications/spam_check_block.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1468, 'core', 'sources/hooks/systems/notifications/low_disk_space.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1469, 'core', 'sources/hooks/systems/notifications/hack_attack.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1470, 'core', 'sources/hooks/systems/notifications/auto_ban.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1471, 'core', 'sources/hooks/systems/notifications/error_occurred.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1472, 'core', 'sources/hooks/systems/notifications/error_occurred_missing_resource.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1473, 'core', 'sources/hooks/systems/notifications/error_occurred_missing_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1474, 'core', 'sources/hooks/systems/notifications/error_occurred_cron.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1475, 'core', 'sources/hooks/systems/notifications/error_occurred_missing_reference.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1476, 'core', 'sources/hooks/systems/notifications/error_occurred_missing_reference_important.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1477, 'core', 'sources/hooks/systems/notifications/adminzone_dashboard_accessed.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1478, 'core', 'sources/hooks/systems/disposable_values/page_views.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1479, 'core', 'sources/password_strength.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1480, 'core', 'sources/hooks/systems/snippets/password_strength.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1481, 'core', 'sources/hooks/blocks/main_staff_checklist/version.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1482, 'core', 'sources/hooks/systems/sitemap/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1483, 'core', 'sources_custom/hooks/systems/sitemap/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1484, 'core', 'sources/hooks/systems/sitemap/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1485, 'core', 'sources_custom/hooks/systems/sitemap/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1486, 'core', 'sources/hooks/systems/sitemap/root.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1487, 'core', 'sources/hooks/systems/sitemap/page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1488, 'core', 'sources/hooks/systems/sitemap/page_grouping.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1489, 'core', 'sources/hooks/systems/sitemap/zone.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1490, 'core', 'sources/hooks/systems/sitemap/entry_point.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1491, 'core', 'web.config');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1492, 'core', 'data/html5.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1493, 'core', 'data/external_url_proxy.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1494, 'core', 'adminzone/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1495, 'core', 'themes/default/images/no_image.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1496, 'core', 'themes/default/css/install.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1497, 'core', 'lang/EN/installer.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1498, 'core', 'lang/EN/encryption.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1499, 'core', 'sources/json.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1500, 'core', 'sources/json_inner.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1501, 'core', 'sources/locations.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1502, 'core', 'sources/locations_geocoding.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1503, 'core', 'sources/locations_cpfs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1504, 'core', 'sources/hooks/systems/cns_cpf_filter/options.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1505, 'core', 'sources/cpf_install.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1506, 'core', 'sources/hooks/systems/symbols/COUNTRY.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1507, 'core', 'sources/hooks/systems/symbols/COUNTRY_CODE_TO_NAME.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1508, 'core', 'sources/hooks/systems/symbols/COUNTRY_NAME_TO_CODE.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1509, 'core', 'sources/hooks/systems/symbols/CURRENCY.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1510, 'core', 'sources/hooks/systems/symbols/REGION.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1511, 'core', 'lang/EN/locations.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1512, 'core', 'data/geocode.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1513, 'core', 'sources/web_resources.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1514, 'core', 'sources/hooks/systems/cron/git_autopull.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1515, 'core', 'sources/hooks/systems/cron/dynamic_firewall.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1516, 'core', 'data_custom/firewall_rules.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1517, 'core', 'sources/hooks/systems/cron/group_member_timeouts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1518, 'core', 'sources/group_member_timeouts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1519, 'core', 'adminzone/pages/modules/admin_group_member_timeouts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1520, 'core', 'lang/EN/group_member_timeouts.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1521, 'core', 'themes/default/templates/GROUP_MEMBER_TIMEOUT_MANAGE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1522, 'core', 'sources/inst_special.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1523, 'core', 'sources/actionlog.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1524, 'core', 'themes/admin/javascript/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1525, 'core', 'themes/admin/javascript/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1526, 'core', 'themes/admin/text/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1527, 'core', 'themes/admin/text/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1528, 'core', 'themes/admin/xml/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1529, 'core', 'themes/admin/xml/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1530, 'core', 'themes/default/javascript/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1531, 'core', 'themes/default/javascript/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1532, 'core', 'themes/default/text/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1533, 'core', 'themes/default/text/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1534, 'core', 'themes/default/xml/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1535, 'core', 'themes/default/xml/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1536, 'core', 'data/no_banning.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1537, 'core', 'data/editarea/edit_area.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1538, 'core', 'data/editarea/edit_area_compressor.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1539, 'core', 'data/editarea/edit_area_full.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1540, 'core', 'data/editarea/images/autocompletion.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1541, 'core', 'data/editarea/images/close.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1542, 'core', 'data/editarea/images/fullscreen.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1543, 'core', 'data/editarea/images/go_to_line.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1544, 'core', 'data/editarea/images/help.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1545, 'core', 'data/editarea/images/highlight.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1546, 'core', 'data/editarea/images/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1547, 'core', 'data/editarea/images/load.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1548, 'core', 'data/editarea/images/move.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1549, 'core', 'data/editarea/images/newdocument.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1550, 'core', 'data/editarea/images/opacity.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1551, 'core', 'data/editarea/images/processing.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1552, 'core', 'data/editarea/images/redo.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1553, 'core', 'data/editarea/images/reset_highlight.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1554, 'core', 'data/editarea/images/save.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1555, 'core', 'data/editarea/images/search.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1556, 'core', 'data/editarea/images/smooth_selection.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1557, 'core', 'data/editarea/images/spacer.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1558, 'core', 'data/editarea/images/statusbar_resize.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1559, 'core', 'data/editarea/images/undo.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1560, 'core', 'data/editarea/images/word_wrap.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1561, 'core', 'data/editarea/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1562, 'core', 'data/editarea/langs/bg.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1563, 'core', 'data/editarea/langs/cs.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1564, 'core', 'data/editarea/langs/de.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1565, 'core', 'data/editarea/langs/dk.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1566, 'core', 'data/editarea/langs/en.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1567, 'core', 'data/editarea/langs/eo.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1568, 'core', 'data/editarea/langs/es.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1569, 'core', 'data/editarea/langs/fi.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1570, 'core', 'data/editarea/langs/fr.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1571, 'core', 'data/editarea/langs/hr.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1572, 'core', 'data/editarea/langs/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1573, 'core', 'data/editarea/langs/it.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1574, 'core', 'data/editarea/langs/ja.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1575, 'core', 'data/editarea/langs/mk.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1576, 'core', 'data/editarea/langs/nl.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1577, 'core', 'data/editarea/langs/pl.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1578, 'core', 'data/editarea/langs/pt.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1579, 'core', 'data/editarea/langs/ru.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1580, 'core', 'data/editarea/langs/sk.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1581, 'core', 'data/editarea/langs/zh.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1582, 'core', 'data/editarea/license_bsd.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1583, 'core', 'data/editarea/reg_syntax/css.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1584, 'core', 'data/editarea/reg_syntax/html.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1585, 'core', 'data/editarea/reg_syntax/js.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1586, 'core', 'data/editarea/reg_syntax/php.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1587, 'core', 'data/editarea/reg_syntax/xml.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1588, 'core', 'data/editarea/template.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1589, 'core', 'sources/hooks/systems/meta/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1590, 'core', 'sources_custom/hooks/systems/meta/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1591, 'core', 'uploads/website_specific/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1592, 'core', 'sources/hooks/systems/upon_page_load/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1593, 'core', 'sources_custom/hooks/systems/upon_page_load/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1594, 'core', 'sources/hooks/systems/upon_access_denied/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1595, 'core', 'sources_custom/hooks/systems/upon_access_denied/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1596, 'core', 'sources/hooks/systems/upon_query/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1597, 'core', 'sources_custom/hooks/systems/upon_query/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1598, 'core', 'sources/hooks/systems/upon_login/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1599, 'core', 'sources_custom/hooks/systems/upon_login/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1600, 'core', 'sources/hooks/systems/login_providers/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1601, 'core', 'sources_custom/hooks/systems/login_providers/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1602, 'core', 'sources/hooks/systems/login_providers/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1603, 'core', 'sources_custom/hooks/systems/login_providers/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1604, 'core', 'sources/hooks/systems/login_providers/httpauth.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1605, 'core', 'data/question_ui.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1606, 'core', 'data/crossdomain.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1607, 'core', 'data/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1608, 'core', 'data_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1609, 'core', 'data/xml_config/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1610, 'core', 'data/xml_config/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1611, 'core', 'data_custom/xml_config/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1612, 'core', 'data_custom/xml_config/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1613, 'core', 'sources/firephp.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1614, 'core', 'sources/content.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1615, 'core', 'sources/content2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1616, 'core', 'lang/EN/metadata.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1617, 'core', 'adminzone/find_permissions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1618, 'core', 'themes/default/javascript/sound.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1619, 'core', 'data/sounds/message_background.mp3');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1620, 'core', 'data/sounds/message_received.mp3');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1621, 'core', 'themes/default/templates/INLINE_WIP_MESSAGE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1622, 'core', 'themes/default/images_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1623, 'core', 'themes/default/templates_cached/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1624, 'core', 'themes/default/images_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1625, 'core', 'themes/default/images/help_jumpout.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1626, 'core', 'themes/default/images/messageicons/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1627, 'core', 'themes/default/images/messageicons/inform_large.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1628, 'core', 'themes/default/images/messageicons/warn_large.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1629, 'core', 'themes/default/images/arrow_ruler.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1630, 'core', 'themes/default/images/arrow_ruler_small.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1631, 'core', 'themes/default/images/outer_background.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1632, 'core', 'themes/default/images/inner_background.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1633, 'core', 'themes/default/images/block_background.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1634, 'core', 'pages/comcode/EN/panel_top.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1635, 'core', 'pages/comcode/EN/panel_bottom.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1636, 'core', 'lang/EN/dearchive.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1637, 'core', 'sources/selectcode.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1638, 'core', 'sources/filtercode.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1639, 'core', 'lang/EN/filtercode.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1640, 'core', 'sources/mail_dkim.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1641, 'core', 'sources/blocks/main_content_filtering.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1642, 'core', 'sources/lang_stemmer_EN.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1643, 'core', 'sources/lang_filter_EN.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1644, 'core', 'themes/default/templates/MISSING_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1645, 'core', 'themes/default/templates/PARAM_INFO.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1646, 'core', 'sources/profiler.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1647, 'core', 'sources/temporal.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1648, 'core', 'sources/temporal2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1649, 'core', 'sources/blocks/main_comcode_page_children.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1650, 'core', 'sources/blocks/main_include_module.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1651, 'core', 'themes/default/images/icons/16x16/filetypes/email_link.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1652, 'core', 'themes/default/images/icons/16x16/filetypes/external_link.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1653, 'core', 'themes/default/images/icons/16x16/filetypes/feed.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1654, 'core', 'themes/default/images/icons/16x16/filetypes/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1655, 'core', 'themes/default/images/icons/16x16/filetypes/page_archive.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1656, 'core', 'themes/default/images/icons/16x16/filetypes/page_doc.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1657, 'core', 'themes/default/images/icons/16x16/filetypes/page_media.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1658, 'core', 'themes/default/images/icons/16x16/filetypes/page_odp.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1659, 'core', 'themes/default/images/icons/16x16/filetypes/page_ods.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1660, 'core', 'themes/default/images/icons/16x16/filetypes/page_odt.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1661, 'core', 'themes/default/images/icons/16x16/filetypes/page_pdf.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1662, 'core', 'themes/default/images/icons/16x16/filetypes/page_ppt.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1663, 'core', 'themes/default/images/icons/16x16/filetypes/page_torrent.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1664, 'core', 'themes/default/images/icons/16x16/filetypes/page_txt.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1665, 'core', 'themes/default/images/icons/16x16/filetypes/page_xls.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1666, 'core', 'themes/default/images/icons/32x32/filetypes/email_link.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1667, 'core', 'themes/default/images/icons/32x32/filetypes/external_link.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1668, 'core', 'themes/default/images/icons/32x32/filetypes/feed.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1669, 'core', 'themes/default/images/icons/32x32/filetypes/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1670, 'core', 'themes/default/images/icons/32x32/filetypes/page_archive.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1671, 'core', 'themes/default/images/icons/32x32/filetypes/page_doc.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1672, 'core', 'themes/default/images/icons/32x32/filetypes/page_media.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1673, 'core', 'themes/default/images/icons/32x32/filetypes/page_odp.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1674, 'core', 'themes/default/images/icons/32x32/filetypes/page_ods.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1675, 'core', 'themes/default/images/icons/32x32/filetypes/page_odt.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1676, 'core', 'themes/default/images/icons/32x32/filetypes/page_pdf.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1677, 'core', 'themes/default/images/icons/32x32/filetypes/page_ppt.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1678, 'core', 'themes/default/images/icons/32x32/filetypes/page_torrent.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1679, 'core', 'themes/default/images/icons/32x32/filetypes/page_txt.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1680, 'core', 'themes/default/images/icons/32x32/filetypes/page_xls.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1681, 'core', 'data/autosave.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1682, 'core', 'sources/hooks/systems/addon_registry/core.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1683, 'core', 'sources/hooks/modules/video_syndication/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1684, 'core', 'sources_custom/hooks/modules/video_syndication/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1685, 'core', 'sources/hooks/modules/video_syndication/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1686, 'core', 'sources_custom/hooks/modules/video_syndication/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1687, 'core', 'sources/hooks/systems/activities/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1688, 'core', 'sources_custom/hooks/systems/activities/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1689, 'core', 'sources/hooks/systems/activities/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1690, 'core', 'sources_custom/hooks/systems/activities/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1691, 'core', 'sources/hooks/systems/cdn_transfer/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1692, 'core', 'sources_custom/hooks/systems/cdn_transfer/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1693, 'core', 'sources/hooks/systems/cdn_transfer/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1694, 'core', 'sources_custom/hooks/systems/cdn_transfer/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1695, 'core', 'sources/hooks/systems/login_providers_direct_auth/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1696, 'core', 'sources_custom/hooks/systems/login_providers_direct_auth/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1697, 'core', 'sources/hooks/systems/login_providers_direct_auth/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1698, 'core', 'sources_custom/hooks/systems/login_providers_direct_auth/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1699, 'core', 'sources/hooks/systems/referrals/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1700, 'core', 'sources_custom/hooks/systems/referrals/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1701, 'core', 'sources/hooks/systems/referrals/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1702, 'core', 'sources_custom/hooks/systems/referrals/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1703, 'core', 'sources/hooks/systems/syndication/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1704, 'core', 'sources_custom/hooks/systems/syndication/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1705, 'core', 'sources/hooks/systems/syndication/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1706, 'core', 'sources_custom/hooks/systems/syndication/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1707, 'core', 'sources/hooks/systems/upload_syndication/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1708, 'core', 'sources_custom/hooks/systems/upload_syndication/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1709, 'core', 'sources/hooks/systems/upload_syndication/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1710, 'core', 'sources_custom/hooks/systems/upload_syndication/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1711, 'core', 'sources/hooks/systems/non_active_urls/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1712, 'core', 'sources_custom/hooks/systems/non_active_urls/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1713, 'core', 'sources/hooks/systems/non_active_urls/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1714, 'core', 'sources_custom/hooks/systems/non_active_urls/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1715, 'core', 'sources/hooks/systems/addon_registry/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1716, 'core', 'sources_custom/hooks/systems/addon_registry/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1717, 'core', 'sources/hooks/blocks/main_notes/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1718, 'core', 'sources_custom/hooks/blocks/main_notes/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1719, 'core', 'sources/hooks/blocks/main_notes/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1720, 'core', 'sources_custom/hooks/blocks/main_notes/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1721, 'core', 'sources/hooks/systems/chmod/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1722, 'core', 'sources_custom/hooks/systems/chmod/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1723, 'core', 'sources/hooks/systems/chmod/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1724, 'core', 'sources_custom/hooks/systems/chmod/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1725, 'core', 'sources/hooks/systems/disposable_values/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1726, 'core', 'sources_custom/hooks/systems/disposable_values/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1727, 'core', 'sources/hooks/systems/disposable_values/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1728, 'core', 'sources_custom/hooks/systems/disposable_values/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1729, 'core', 'sources/hooks/systems/symbols/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1730, 'core', 'sources_custom/hooks/systems/symbols/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1731, 'core', 'sources/hooks/systems/symbols/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1732, 'core', 'sources_custom/hooks/systems/symbols/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1733, 'core', 'sources/url_remappings.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1734, 'core', 'sources/hooks/systems/addon_registry/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1735, 'core', 'sources_custom/hooks/systems/addon_registry/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1736, 'core', 'sources/activities.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1737, 'core', 'sources/crypt.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1738, 'core', 'sources/crypt_master.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1739, 'core', 'data_custom/sitemaps/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1740, 'core', 'themes/default/templates/JS_BLOCK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1741, 'core', 'themes/default/javascript/modernizr.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1742, 'core', 'themes/default/javascript/jquery.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1743, 'core', 'themes/default/javascript/jquery_ui.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1744, 'core', 'themes/default/images/jquery_ui/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1745, 'core', 'themes/default/images/jquery_ui/ui-bg_flat_0_aaaaaa_40x100.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1746, 'core', 'themes/default/images/jquery_ui/ui-bg_flat_75_ffffff_40x100.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1747, 'core', 'themes/default/images/jquery_ui/ui-bg_glass_55_fbf9ee_1x400.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1748, 'core', 'themes/default/images/jquery_ui/ui-bg_glass_75_dadada_1x400.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1749, 'core', 'themes/default/images/jquery_ui/ui-bg_glass_75_e6e6e6_1x400.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1750, 'core', 'themes/default/images/jquery_ui/ui-bg_glass_95_fef1ec_1x400.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1751, 'core', 'themes/default/images/jquery_ui/ui-bg_glass_65_ffffff_1x400.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1752, 'core', 'themes/default/images/jquery_ui/ui-icons_222222_256x240.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1753, 'core', 'themes/default/images/jquery_ui/ui-bg_highlight-soft_75_cccccc_1x100.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1754, 'core', 'themes/default/images/jquery_ui/ui-icons_2e83ff_256x240.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1755, 'core', 'themes/default/images/jquery_ui/ui-icons_454545_256x240.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1756, 'core', 'themes/default/images/jquery_ui/ui-icons_888888_256x240.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1757, 'core', 'themes/default/images/jquery_ui/ui-icons_cd0a0a_256x240.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1758, 'core', 'themes/default/css/jquery_ui.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1759, 'core', 'themes/default/javascript/global.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1760, 'core', 'themes/default/javascript/ajax.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1761, 'core', 'themes/default/templates/JAVASCRIPT_NEED.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1762, 'core', 'themes/default/templates/JAVASCRIPT_NEED_FULL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1763, 'core', 'themes/default/templates/JAVASCRIPT_NEED_INLINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1764, 'core', 'themes/default/templates/CSS_NEED.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1765, 'core', 'themes/default/templates/CSS_NEED_FULL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1766, 'core', 'themes/default/templates/CSS_NEED_INLINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1767, 'core', 'themes/default/javascript/staff.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1768, 'core', 'themes/default/javascript/transitions.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1769, 'core', 'themes/default/javascript/tree_list.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1770, 'core', 'themes/default/javascript/xsl_mopup.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1771, 'core', 'themes/default/templates/BLOCK_SIDE_PERSONAL_STATS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1772, 'core', 'themes/default/templates/BLOCK_SIDE_PERSONAL_STATS_LINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1773, 'core', 'themes/default/templates/BLOCK_SIDE_PERSONAL_STATS_LINE_COMPLEX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1774, 'core', 'themes/default/templates/BLOCK_SIDE_PERSONAL_STATS_LINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1775, 'core', 'themes/default/templates/BLOCK_SIDE_PERSONAL_STATS_LINK_2.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1776, 'core', 'themes/default/templates/BLOCK_SIDE_PERSONAL_STATS_LOGOUT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1777, 'core', 'themes/default/templates/BLOCK_SIDE_PERSONAL_STATS_NO.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1778, 'core', 'themes/default/templates/BLOCK_TOP_LOGIN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1779, 'core', 'themes/default/templates/BLOCK_TOP_PERSONAL_STATS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1780, 'core', 'themes/default/templates/BLOCK_MAIN_CONTENT_FILTERING.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1781, 'core', 'themes/default/templates/REDIRECT_POST_METHOD_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1782, 'core', 'themes/default/templates/LOGIN_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1783, 'core', 'themes/default/css/login.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1784, 'core', 'themes/default/templates/BROKEN_LANG_STRINGS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1785, 'core', 'themes/default/templates/BROKEN_URLS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1786, 'core', 'themes/default/templates/FORUMS_EMBED.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1787, 'core', 'themes/default/templates/ACTIONLOGS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1788, 'core', 'themes/default/templates/ACTIONLOGS_TOGGLE_LINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1789, 'core', 'themes/default/templates/FORUM_ATTACHMENT_IMAGE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1790, 'core', 'themes/default/templates/FORUM_ATTACHMENT_IMAGE_THUMB.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1791, 'core', 'themes/default/templates/FORUM_ATTACHMENT_LINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1792, 'core', 'themes/default/templates/LOOKUP_IP_LIST_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1793, 'core', 'themes/default/templates/LOOKUP_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1794, 'core', 'themes/default/templates/FATAL_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1795, 'core', 'themes/default/templates/STACK_TRACE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1796, 'core', 'themes/default/templates/BLOCK_MAIN_EMOTICON_CODES.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1797, 'core', 'themes/default/templates/BLOCK_NO_ENTRIES.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1798, 'core', 'adminzone/index.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1799, 'core', 'adminzone/pages/comcode/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1800, 'core', 'adminzone/pages/comcode/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1801, 'core', 'adminzone/pages/comcode/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1802, 'core', 'adminzone/pages/comcode/EN/start.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1803, 'core', 'adminzone/pages/comcode/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1804, 'core', 'adminzone/pages/comcode_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1805, 'core', 'adminzone/pages/comcode_custom/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1806, 'core', 'adminzone/pages/comcode_custom/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1807, 'core', 'adminzone/pages/comcode_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1808, 'core', 'adminzone/pages/html/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1809, 'core', 'adminzone/pages/html/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1810, 'core', 'adminzone/pages/html/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1811, 'core', 'adminzone/pages/html/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1812, 'core', 'adminzone/pages/html_custom/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1813, 'core', 'adminzone/pages/html_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1814, 'core', 'adminzone/pages/html_custom/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1815, 'core', 'adminzone/pages/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1816, 'core', 'adminzone/pages/minimodules/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1817, 'core', 'adminzone/pages/minimodules/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1818, 'core', 'adminzone/pages/minimodules_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1819, 'core', 'adminzone/pages/minimodules_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1820, 'core', 'adminzone/pages/modules/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1821, 'core', 'adminzone/pages/modules/admin.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1822, 'core', 'themes/default/images/keyboard.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1823, 'core', 'sources/sitemap.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1824, 'core', 'sources/sitemap_ajax.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1825, 'core', 'sources/sitemap_xml.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1826, 'core', 'sources/hooks/systems/tasks/sitemap.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1827, 'core', 'sources/hooks/systems/cron/sitemap.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1828, 'core', 'themes/default/templates/IP_BAN_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1829, 'core', 'themes/default/templates/LOOKUP_IP_LIST_GROUP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1830, 'core', 'themes/default/templates/BLOCK_MAIN_COMCODE_PAGE_CHILDREN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1831, 'core', 'adminzone/pages/modules/admin_version.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1832, 'core', 'adminzone/pages/modules/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1833, 'core', 'adminzone/pages/modules_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1834, 'core', 'adminzone/pages/modules_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1835, 'core', 'text/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1836, 'core', 'text/bots.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1837, 'core', 'text/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1838, 'core', 'text/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1839, 'core', 'text/EN/licence.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1840, 'core', 'text/EN/too_common_words.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1841, 'core', 'text/EN/word_characters.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1842, 'core', 'text/EN/synonyms.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1843, 'core', 'text/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1844, 'core', 'text_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1845, 'core', 'text_custom/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1846, 'core', 'text_custom/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1847, 'core', 'text_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1848, 'core', 'safe_mode_temp/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1849, 'core', 'safe_mode_temp/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1850, 'core', 'config_editor.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1851, 'core', 'index.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1852, 'core', '_config.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1853, 'core', 'uploads/auto_thumbs/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1854, 'core', 'uploads/auto_thumbs/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1855, 'core', 'exports/file_backups/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1856, 'core', 'uploads/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1857, 'core', 'sources/xml.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1858, 'core', 'sources_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1859, 'core', 'sources_custom/blocks/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1860, 'core', 'sources_custom/blocks/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1861, 'core', 'sources_custom/hooks/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1862, 'core', 'sources_custom/hooks/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1863, 'core', 'sources_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1864, 'core', 'sources_custom/miniblocks/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1865, 'core', 'sources_custom/miniblocks/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1866, 'core', 'lang/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1867, 'core', 'lang/langs.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1868, 'core', 'lang/map.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1869, 'core', 'caches/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1870, 'core', 'caches/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1871, 'core', 'caches/lang/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1872, 'core', 'caches/lang/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1873, 'core', 'caches/lang/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1874, 'core', 'caches/lang/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1875, 'core', 'caches/guest_pages/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1876, 'core', 'caches/guest_pages/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1877, 'core', 'caches/self_learning/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1878, 'core', 'caches/self_learning/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1879, 'core', 'lang_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1880, 'core', 'lang_custom/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1881, 'core', 'lang_custom/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1882, 'core', 'lang_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1883, 'core', 'themes/default/css/sitemap_editor.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1884, 'core', 'themes/default/css/global.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1885, 'core', 'themes/default/css/email.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1886, 'core', 'themes/default/css/font_sizer.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1887, 'core', 'themes/default/templates/FONT_SIZER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1888, 'core', 'themes/default/css/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1889, 'core', 'themes/default/css/no_cache.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1890, 'core', 'data/editarea/reg_syntax/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1891, 'core', 'themes/default/css_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1892, 'core', 'themes/default/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1893, 'core', 'themes/default/templates/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1894, 'core', 'themes/default/templates/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1895, 'core', 'themes/default/templates_cached/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1896, 'core', 'themes/default/templates_cached/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1897, 'core', 'themes/default/templates_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1898, 'core', 'themes/default/templates_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1899, 'core', 'themes/default/javascript_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1900, 'core', 'themes/default/javascript_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1901, 'core', 'themes/default/xml_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1902, 'core', 'themes/default/xml_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1903, 'core', 'themes/default/text_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1904, 'core', 'themes/default/text_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1905, 'core', 'themes/default/theme.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1906, 'core', 'themes/admin/theme.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1907, 'core', 'themes/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1908, 'core', 'themes/map.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1909, 'core', 'sources/hooks/systems/module_permissions/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1910, 'core', 'sources_custom/hooks/systems/module_permissions/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1911, 'core', 'sources/hooks/systems/module_permissions/forums.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1912, 'core', 'sources/hooks/systems/module_permissions/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1913, 'core', 'sources_custom/hooks/systems/module_permissions/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1914, 'core', 'lang/EN/permissions.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1915, 'core', 'sources/permissions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1916, 'core', 'sources/permissions2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1917, 'core', 'sources/hooks/systems/block_ui_renderers/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1918, 'core', 'sources_custom/hooks/systems/block_ui_renderers/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1919, 'core', 'sources/hooks/systems/block_ui_renderers/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1920, 'core', 'sources_custom/hooks/systems/block_ui_renderers/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1921, 'core', 'themes/default/images/1x/arrow_box.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1922, 'core', 'themes/default/images/1x/arrow_box_hover.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1923, 'core', 'themes/default/images/2x/arrow_box.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1924, 'core', 'themes/default/images/2x/arrow_box_hover.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1925, 'core', 'themes/default/images/background_image.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1926, 'core', 'themes/default/images/blank.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1927, 'core', 'themes/default/images/loading.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1928, 'core', 'themes/default/images/1x/edited.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1929, 'core', 'themes/default/images/2x/edited.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1930, 'core', 'themes/default/images/icons/14x14/action_link.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1931, 'core', 'themes/default/images/icons/28x28/action_link.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1932, 'core', 'themes/default/images/1x/cannot_show.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1933, 'core', 'themes/default/images/2x/cannot_show.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1934, 'core', 'themes/default/images/icons/16x16/editor/comcode.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1935, 'core', 'themes/default/images/icons/32x32/editor/comcode.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1936, 'core', 'themes/default/images/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1937, 'core', 'themes/default/images/EN/logo/-logo.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1938, 'core', 'themes/default/images/EN/logo/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1939, 'core', 'themes/default/images/EN/logo/standalone_logo.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1940, 'core', 'themes/default/images/icons/14x14/helper_panel_hide.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1941, 'core', 'themes/default/images/icons/14x14/helper_panel_show.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1942, 'core', 'themes/default/images/icons/28x28/helper_panel_hide.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1943, 'core', 'themes/default/images/icons/28x28/helper_panel_show.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1944, 'core', 'themes/default/images/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1945, 'core', 'themes/default/images/led_off.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1946, 'core', 'themes/default/images/led_on.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1947, 'core', 'themes/default/images/na.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1948, 'core', 'themes/default/images/poll/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1949, 'core', 'themes/default/images/poll/poll_l.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1950, 'core', 'themes/default/images/poll/poll_m.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1951, 'core', 'themes/default/images/poll/poll_r.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1952, 'core', 'themes/default/images/1x/box_arrow.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1953, 'core', 'themes/default/images/2x/box_arrow.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1954, 'core', 'themes/default/images/results/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1955, 'core', 'themes/default/images/results/sortablefield_asc.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1956, 'core', 'themes/default/images/results/sortablefield_desc.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1957, 'core', 'themes/default/images/results/sortablefield_asc_nonselected.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1958, 'core', 'themes/default/images/results/sortablefield_desc_nonselected.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1959, 'core', 'themes/default/images/gradient.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1960, 'core', 'themes/default/images/tab.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1961, 'core', 'themes/default/images/icons/14x14/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1962, 'core', 'themes/default/images/icons/14x14/delete.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1963, 'core', 'themes/default/images/icons/14x14/download.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1964, 'core', 'themes/default/images/icons/14x14/edit.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1965, 'core', 'themes/default/images/icons/28x28/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1966, 'core', 'themes/default/images/icons/28x28/delete.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1967, 'core', 'themes/default/images/icons/28x28/download.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1968, 'core', 'themes/default/images/icons/28x28/edit.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1969, 'core', 'sources/hooks/systems/startup/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1970, 'core', 'sources_custom/hooks/systems/startup/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1971, 'core', 'data/confirm_session.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1972, 'core', 'data/cron_bridge.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1973, 'core', 'data/curl-ca-bundle.crt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1974, 'core', 'data/db_meta.bin');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1975, 'core', 'data/empty.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1976, 'core', 'data/files.bin');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1977, 'core', 'data/files_previous.bin');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1978, 'core', 'data/guids.bin');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1979, 'core', 'data/iframe.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1980, 'core', 'data/images/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1981, 'core', 'data/images/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1982, 'core', 'data_custom/images/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1983, 'core', 'data_custom/images/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1984, 'core', 'data/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1985, 'core', 'data/javascript.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1986, 'core', 'data/modules/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1987, 'core', 'data/page_link_redirect.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1988, 'core', 'data/quash_referer.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1989, 'core', 'data/sheet.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1990, 'core', 'data/script.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1991, 'core', 'data/sitemap.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1992, 'core', 'data/snippet.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1993, 'core', 'data/soundmanager/soundmanager2.swf');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1994, 'core', 'data/soundmanager/soundmanager2_flash9.swf');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1995, 'core', 'data/soundmanager/soundmanager2_flash9_debug.swf');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1996, 'core', 'data/soundmanager/soundmanager2_debug.swf');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1997, 'core', 'data/soundmanager/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1998, 'core', 'data/sounds/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (1999, 'core', 'data_custom/execute_temp.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2000, 'core', 'data_custom/functions.bin');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2001, 'core', 'data_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2002, 'core', 'data_custom/modules/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2003, 'core', 'cms/index.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2004, 'core', 'cms/pages/comcode/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2005, 'core', 'cms/pages/comcode/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2006, 'core', 'cms/pages/comcode/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2007, 'core', 'cms/pages/comcode/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2008, 'core', 'cms/pages/comcode_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2009, 'core', 'cms/pages/comcode_custom/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2010, 'core', 'cms/pages/comcode_custom/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2011, 'core', 'cms/pages/comcode_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2012, 'core', 'cms/pages/html/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2013, 'core', 'cms/pages/html/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2014, 'core', 'cms/pages/html/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2015, 'core', 'cms/pages/html/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2016, 'core', 'cms/pages/html_custom/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2017, 'core', 'cms/pages/html_custom/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2018, 'core', 'cms/pages/html_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2019, 'core', 'cms/pages/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2020, 'core', 'cms/pages/minimodules/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2021, 'core', 'cms/pages/minimodules/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2022, 'core', 'cms/pages/minimodules_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2023, 'core', 'cms/pages/minimodules_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2024, 'core', 'cms/pages/modules/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2025, 'core', 'cms/pages/modules/cms.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2026, 'core', 'cms/pages/modules/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2027, 'core', 'cms/pages/modules_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2028, 'core', 'cms/pages/modules_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2029, 'core', 'lang/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2030, 'core', 'lang/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2031, 'core', 'lang/EN/abstract_file_manager.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2032, 'core', 'lang/EN/blocks.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2033, 'core', 'lang/EN/critical_error.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2034, 'core', 'lang/EN/dates.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2035, 'core', 'lang/EN/debrand.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2036, 'core', 'lang/EN/do_next.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2037, 'core', 'lang/EN/global.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2038, 'core', 'lang/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2039, 'core', 'lang/EN/javascript.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2040, 'core', 'lang/EN/lang.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2041, 'core', 'lang/EN/mail.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2042, 'core', 'lang/EN/profiling.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2043, 'core', 'lang/EN/version.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2044, 'core', 'lang/EN/zones.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2045, 'core', 'pages/comcode/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2046, 'core', 'pages/comcode/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2047, 'core', 'pages/comcode/EN/404.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2048, 'core', 'pages/comcode/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2049, 'core', 'pages/comcode/EN/keymap.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2050, 'core', 'pages/comcode/EN/panel_left.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2051, 'core', 'pages/comcode/EN/panel_right.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2052, 'core', 'pages/comcode/EN/privacy.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2053, 'core', 'pages/comcode/EN/_rules.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2054, 'core', 'pages/comcode/EN/rules.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2055, 'core', 'pages/comcode/EN/sitemap.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2056, 'core', 'pages/comcode/EN/start.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2057, 'core', 'pages/comcode/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2058, 'core', 'pages/comcode_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2059, 'core', 'pages/comcode_custom/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2060, 'core', 'pages/comcode_custom/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2061, 'core', 'pages/comcode_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2062, 'core', 'pages/html/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2063, 'core', 'pages/html/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2064, 'core', 'pages/html/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2065, 'core', 'pages/html/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2066, 'core', 'pages/html_custom/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2067, 'core', 'pages/html_custom/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2068, 'core', 'pages/html_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2069, 'core', 'pages/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2070, 'core', 'pages/minimodules/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2071, 'core', 'pages/minimodules/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2072, 'core', 'pages/minimodules_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2073, 'core', 'pages/minimodules_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2074, 'core', 'pages/modules/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2075, 'core', 'pages/modules/forums.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2076, 'core', 'pages/modules/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2077, 'core', 'pages/modules/login.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2078, 'core', 'pages/modules_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2079, 'core', 'pages/modules_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2080, 'core', 'site/index.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2081, 'core', 'site/pages/comcode/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2082, 'core', 'site/pages/comcode/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2083, 'core', 'site/pages/comcode/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2084, 'core', 'site/pages/comcode/EN/panel_left.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2085, 'core', 'site/pages/comcode/EN/panel_right.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2086, 'core', 'site/pages/comcode/EN/start.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2087, 'core', 'site/pages/comcode/EN/userguide_comcode.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2088, 'core', 'site/pages/comcode/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2089, 'core', 'site/pages/comcode_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2090, 'core', 'site/pages/comcode_custom/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2091, 'core', 'site/pages/comcode_custom/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2092, 'core', 'site/pages/comcode_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2093, 'core', 'site/pages/html/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2094, 'core', 'site/pages/html/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2095, 'core', 'site/pages/html/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2096, 'core', 'site/pages/html/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2097, 'core', 'site/pages/html_custom/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2098, 'core', 'site/pages/html_custom/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2099, 'core', 'site/pages/html_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2100, 'core', 'site/pages/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2101, 'core', 'site/pages/minimodules/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2102, 'core', 'site/pages/minimodules/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2103, 'core', 'site/pages/minimodules_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2104, 'core', 'site/pages/minimodules_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2105, 'core', 'site/pages/modules/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2106, 'core', 'site/pages/modules/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2107, 'core', 'site/pages/modules_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2108, 'core', 'site/pages/modules_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2109, 'core', 'sources/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2110, 'core', 'sources/abstract_file_manager.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2111, 'core', 'sources/crud_module.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2112, 'core', 'sources/ajax.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2113, 'core', 'sources/blocks/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2114, 'core', 'sources/blocks/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2115, 'core', 'sources/blocks/main_db_notes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2116, 'core', 'sources/blocks/main_notes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2117, 'core', 'sources/blocks/main_only_if_match.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2118, 'core', 'sources/blocks/side_personal_stats.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2119, 'core', 'sources/blocks/top_login.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2120, 'core', 'sources/blocks/top_personal_stats.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2121, 'core', 'sources/caches.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2122, 'core', 'sources/caches2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2123, 'core', 'sources/caches3.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2124, 'core', 'sources/persistent_caching/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2125, 'core', 'sources/persistent_caching/filesystem.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2126, 'core', 'sources/persistent_caching/eaccelerator.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2127, 'core', 'sources/persistent_caching/apc.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2128, 'core', 'sources/persistent_caching/xcache.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2129, 'core', 'sources/persistent_caching/wincache.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2130, 'core', 'sources/persistent_caching/memcache.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2131, 'core', 'sources/persistent_caching/memcached.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2132, 'core', 'sources/config.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2133, 'core', 'sources/config2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2134, 'core', 'sources/critical_errors.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2135, 'core', 'sources/database.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2136, 'core', 'sources/database_security_filter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2137, 'core', 'sources/database_helper.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2138, 'core', 'sources/database_repair.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2139, 'core', 'sources/database_action.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2140, 'core', 'sources/database_search.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2141, 'core', 'sources/diff.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2142, 'core', 'sources/encryption.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2143, 'core', 'sources/files.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2144, 'core', 'sources/files2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2145, 'core', 'sources/forum_stub.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2146, 'core', 'sources/global.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2147, 'core', 'sources/shared_installs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2148, 'core', 'sources/override_api.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2149, 'core', 'sources/global2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2150, 'core', 'sources/debug_fs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2151, 'core', 'sources/character_sets.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2152, 'core', 'sources/css_and_js.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2153, 'core', 'sources/jsmin.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2154, 'core', 'sources/database_relations.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2155, 'core', 'sources/wordfilter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2156, 'core', 'sources/input_filter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2157, 'core', 'sources/csrf_filter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2158, 'core', 'sources/input_filter_2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2159, 'core', 'sources/hooks/systems/checks/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2160, 'core', 'sources_custom/hooks/systems/checks/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2161, 'core', 'sources/hooks/systems/checks/base_url.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2162, 'core', 'sources/hooks/systems/checks/no_ad_script.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2163, 'core', 'data/blank.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2164, 'core', 'sources/hooks/systems/checks/disk_space.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2165, 'core', 'sources/hooks/systems/checks/file_uploads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2166, 'core', 'sources/hooks/systems/checks/functions_needed.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2167, 'core', 'sources/hooks/systems/checks/gd.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2168, 'core', 'sources/hooks/systems/checks/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2169, 'core', 'sources_custom/hooks/systems/checks/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2170, 'core', 'sources/hooks/systems/checks/max_execution_time.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2171, 'core', 'sources/hooks/systems/checks/max_input_vars.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2172, 'core', 'sources/hooks/systems/checks/mbstring_overload.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2173, 'core', 'sources/hooks/systems/checks/memory.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2174, 'core', 'sources/hooks/systems/checks/mysql_version.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2175, 'core', 'sources/hooks/systems/checks/php_version.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2176, 'core', 'sources/hooks/systems/checks/suhosin_eval.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2177, 'core', 'sources/hooks/systems/checks/unzip.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2178, 'core', 'sources/hooks/systems/checks/xml.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2179, 'core', 'sources/hooks/systems/checks/directory_name.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2180, 'core', 'sources/hooks/systems/checks/normative_performance.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2181, 'core', 'sources/hooks/systems/checks/utf.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2182, 'core', 'sources/hooks/systems/checks/modsecurity.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2183, 'core', 'sources/failure.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2184, 'core', 'sources/failure_spammers.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2185, 'core', 'sources/images.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2186, 'core', 'sources/images_cleanup_pipeline.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2187, 'core', 'sources/images2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2188, 'core', 'sources/exif.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2189, 'core', 'sources/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2190, 'core', 'sources/integrator.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2191, 'core', 'sources/lang.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2192, 'core', 'sources/lang_compile.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2193, 'core', 'sources/lang2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2194, 'core', 'sources/lang3.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2195, 'core', 'sources/mail.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2196, 'core', 'sources/mail2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2197, 'core', 'sources/miniblocks/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2198, 'core', 'sources/miniblocks/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2199, 'core', 'sources/minikernel.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2200, 'core', 'sources/misc_scripts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2201, 'core', 'sources/hooks/systems/preview/block_comcode.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2202, 'core', 'sources/m_zip.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2203, 'core', 'sources/seo2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2204, 'core', 'sources/site.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2205, 'core', 'sources/site_adminzone.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2206, 'core', 'sources/site_html_pages.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2207, 'core', 'sources/site2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2208, 'core', 'sources/submit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2209, 'core', 'sources/global3.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2210, 'core', 'sources/global4.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2211, 'core', 'sources/mime_types.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2212, 'core', 'sources/obfuscate.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2213, 'core', 'sources/xhtml.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2214, 'core', 'sources/xmlrpc.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2215, 'core', 'sources/textfiles.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2216, 'core', 'sources/autosave.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2217, 'core', 'sources/symbols.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2218, 'core', 'sources/symbols2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2219, 'core', 'sources/tar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2220, 'core', 'sources/tar2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2221, 'core', 'sources/tempcode.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2222, 'core', 'sources/tempcode_compiler.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2223, 'core', 'sources/tempcode_optimiser.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2224, 'core', 'sources/templates.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2225, 'core', 'sources/themes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2226, 'core', 'sources/type_sanitisation.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2227, 'core', 'sources/uploads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2228, 'core', 'sources/urls_simplifier.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2229, 'core', 'sources/uploads2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2230, 'core', 'sources/upload_syndication.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2231, 'core', 'data/upload_syndication_auth.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2232, 'core', 'lang/EN/upload_syndication.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2233, 'core', 'themes/default/templates/UPLOAD_SYNDICATION_SETUP_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2234, 'core', 'sources/urls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2235, 'core', 'sources/urls2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2236, 'core', 'sources/users.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2237, 'core', 'sources/users2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2238, 'core', 'sources/users_active_actions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2239, 'core', 'sources/users_inactive_occasionals.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2240, 'core', 'sources/version.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2241, 'core', 'sources/version2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2242, 'core', 'sources/view_modes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2243, 'core', 'sources/zip.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2244, 'core', 'sources/zones.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2245, 'core', 'sources/zones2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2246, 'core', 'sources/zones3.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2247, 'core', 'sources/developer_tools.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2248, 'core', 'sources/hooks/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2249, 'core', 'sources/hooks/blocks/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2250, 'core', 'sources/hooks/blocks/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2251, 'core', 'sources_custom/hooks/blocks/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2252, 'core', 'sources_custom/hooks/blocks/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2253, 'core', 'sources/hooks/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2254, 'core', 'sources/hooks/modules/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2255, 'core', 'sources/hooks/modules/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2256, 'core', 'sources_custom/hooks/modules/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2257, 'core', 'sources_custom/hooks/modules/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2258, 'core', 'sources/hooks/systems/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2259, 'core', 'sources/hooks/systems/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2260, 'core', 'sources_custom/hooks/systems/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2261, 'core', 'sources_custom/hooks/systems/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2262, 'core', 'data/ajax_tree.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2263, 'core', 'sources/hooks/systems/ajax_tree/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2264, 'core', 'sources_custom/hooks/systems/ajax_tree/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2265, 'core', 'sources/hooks/systems/ajax_tree/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2266, 'core', 'sources_custom/hooks/systems/ajax_tree/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2267, 'core', 'sources/hooks/systems/cron/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2268, 'core', 'sources_custom/hooks/systems/cron/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2269, 'core', 'sources/hooks/systems/cron/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2270, 'core', 'sources_custom/hooks/systems/cron/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2271, 'core', 'sources/hooks/systems/page_groupings/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2272, 'core', 'sources_custom/hooks/systems/page_groupings/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2273, 'core', 'sources/hooks/systems/page_groupings/core.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2274, 'core', 'sources/hooks/systems/page_groupings/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2275, 'core', 'sources_custom/hooks/systems/page_groupings/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2276, 'core', 'sources/hooks/systems/preview/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2277, 'core', 'sources_custom/hooks/systems/preview/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2278, 'core', 'sources/hooks/systems/preview/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2279, 'core', 'sources_custom/hooks/systems/preview/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2280, 'core', 'sources/hooks/systems/snippets/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2281, 'core', 'sources_custom/hooks/systems/snippets/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2282, 'core', 'sources/hooks/systems/snippets/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2283, 'core', 'sources_custom/hooks/systems/snippets/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2284, 'core', 'sources/hooks/systems/snippets/css_compile__text.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2285, 'core', 'sources/hooks/systems/snippets/block.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2286, 'core', 'sources/blocks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2287, 'core', 'sources/hooks/systems/cron/block_caching.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2288, 'core', 'themes/default/text/tempcode_test.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2289, 'core', 'themes/default/templates/QUERY_LOG.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2290, 'core', 'themes/default/templates/QUERY_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2291, 'core', 'themes/default/javascript/custom_globals.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2292, 'core', 'themes/default/javascript/modalwindow.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2293, 'core', 'themes/default/images/product_logo.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2294, 'core', 'themes/default/images/button_lightbox_close.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2295, 'core', 'sources/blocks/main_greeting.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2296, 'core', 'themes/default/images/iphone.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2297, 'core', 'themes/default/images/quote_gradient.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2298, 'core', 'sources/hooks/systems/content_meta_aware/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2299, 'core', 'sources_custom/hooks/systems/content_meta_aware/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2300, 'core', 'sources/hooks/systems/content_meta_aware/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2301, 'core', 'sources_custom/hooks/systems/content_meta_aware/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2302, 'core', 'sources/hooks/systems/meta/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2303, 'core', 'sources_custom/hooks/systems/meta/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2304, 'core', 'sources/hooks/systems/cns_auth/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2305, 'core', 'sources_custom/hooks/systems/cns_auth/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2306, 'core', 'sources/hooks/systems/startup/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2307, 'core', 'sources_custom/hooks/systems/startup/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2308, 'core', 'sources/hooks/systems/upon_access_denied/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2309, 'core', 'sources_custom/hooks/systems/upon_access_denied/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2310, 'core', 'sources/hooks/systems/upon_login/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2311, 'core', 'sources_custom/hooks/systems/upon_login/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2312, 'core', 'sources/hooks/systems/upon_page_load/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2313, 'core', 'sources_custom/hooks/systems/upon_page_load/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2314, 'core', 'sources/hooks/systems/upon_query/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2315, 'core', 'sources_custom/hooks/systems/upon_query/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2316, 'core', 'sources_custom/database/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2317, 'core', 'sources_custom/database/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2318, 'core', 'sources/decision_tree.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2319, 'core', 'lang/EN/decision_tree.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2320, 'core', 'sources/rss.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2321, 'core', 'lang/EN/rss.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2322, 'core', 'data/incoming_uploads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2323, 'core', 'sources/incoming_uploads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2324, 'core', 'themes/default/images/uploader/cancelbutton.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2325, 'core', 'themes/default/images/uploader/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2326, 'core', 'themes/default/images/icons/24x24/links/facebook.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2327, 'core', 'themes/default/images/icons/24x24/links/twitter.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2328, 'core', 'themes/default/images/icons/48x48/links/facebook.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2329, 'core', 'themes/default/images/icons/48x48/links/twitter.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2330, 'core', 'uploads/repimages/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2331, 'core', 'uploads/repimages/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2332, 'core', 'uploads/incoming/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2333, 'core', 'uploads/incoming/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2334, 'core', 'uploads/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2335, 'core', 'data/plupload/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2336, 'core', 'data/plupload/plupload.flash.swf');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2337, 'core', 'data/plupload/plupload.silverlight.xap');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2338, 'core', 'themes/default/css/widget_plupload.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2339, 'core', 'themes/default/javascript/plupload.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2340, 'core', 'themes/admin/templates/ADMIN_ZONE_SEARCH.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2341, 'core', 'themes/default/text/NEWSLETTER_DEFAULT_FCOMCODE.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2342, 'core', 'themes/default/images/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2343, 'core', 'themes/default/images/favicon.ico');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2344, 'core', 'themes/default/images/webclipicon.ico');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2345, 'core', 'themes/default/javascript/menu_mobile.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2346, 'core', 'adminzone/pages/modules/admin_email_log.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2347, 'core', 'sources/hooks/systems/cron/mail_queue.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2348, 'core', 'lang/EN/email_log.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2349, 'core', 'themes/default/templates/EMAIL_LOG_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2350, 'core', 'sources/hooks/systems/cron/implicit_usergroup_sync.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2351, 'core', 'sources/hooks/systems/cns_implicit_usergroups/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2352, 'core', 'sources_custom/hooks/systems/cns_implicit_usergroups/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2353, 'core', 'sources/hooks/systems/cns_implicit_usergroups/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2354, 'core', 'sources_custom/hooks/systems/cns_implicit_usergroups/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2355, 'core', 'themes/default/css/personal_stats.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2356, 'core', 'themes/default/templates/MASS_SELECT_MARKER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2357, 'core', 'themes/default/templates/MASS_SELECT_DELETE_FORM.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2358, 'core', 'themes/default/templates/MASS_SELECT_FORM_BUTTONS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2359, 'core', 'data/tasks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2360, 'core', 'sources/tasks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2361, 'core', 'lang/EN/tasks.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2362, 'core', 'sources/hooks/systems/cron/tasks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2363, 'core', 'sources/hooks/systems/tasks/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2364, 'core', 'sources_custom/hooks/systems/tasks/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2365, 'core', 'sources/hooks/systems/notifications/task_completed.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2366, 'core', 'sources/hooks/systems/tasks/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2367, 'core', 'sources_custom/hooks/systems/tasks/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2368, 'core', 'themes/default/images/EN/1x/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2369, 'core', 'themes/default/images/EN/2x/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2370, 'core', 'themes/default/images/icons/16x16/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2371, 'core', 'sources/persistent_caching/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2372, 'core', 'sources/deep_clean.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2373, 'core', 'sources/hooks/systems/symbols/DEEP_CLEAN.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2374, 'core', 'themes/default/templates/PASSWORD_CHECK_JS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2375, 'core', 'themes/default/templates/SPONSORS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2376, 'core', 'data/endpoint.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2377, 'core', 'sources/endpoints.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2378, 'core', 'sources/hooks/endpoints/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2379, 'core', 'sources/hooks/endpoints/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2380, 'core', 'sources/hooks/endpoints/account/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2381, 'core', 'sources/hooks/endpoints/account/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2382, 'core', 'sources/hooks/endpoints/content/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2383, 'core', 'sources/hooks/endpoints/content/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2384, 'core', 'sources/hooks/endpoints/misc/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2385, 'core', 'sources/hooks/endpoints/misc/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2386, 'core', 'sources_custom/hooks/endpoints/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2387, 'core', 'sources_custom/hooks/endpoints/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2388, 'core', 'sources_custom/hooks/endpoints/account/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2389, 'core', 'sources_custom/hooks/endpoints/account/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2390, 'core', 'sources_custom/hooks/endpoints/content/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2391, 'core', 'sources_custom/hooks/endpoints/content/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2392, 'core', 'sources_custom/hooks/endpoints/misc/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2393, 'core', 'sources_custom/hooks/endpoints/misc/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2394, 'core', 'themes/admin/css/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2395, 'core', 'themes/admin/css/adminzone.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2396, 'core', 'themes/admin/css_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2397, 'core', 'themes/admin/css_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2398, 'core', 'themes/admin/images/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2399, 'core', 'themes/admin/images_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2400, 'core', 'themes/admin/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2401, 'core', 'themes/admin/templates/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2402, 'core', 'themes/admin/templates/GLOBAL_HTML_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2403, 'core', 'themes/admin/templates/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2404, 'core', 'themes/admin/templates_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2405, 'core', 'themes/admin/templates_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2406, 'core', 'themes/admin/javascript_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2407, 'core', 'themes/admin/javascript_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2408, 'core', 'themes/admin/xml_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2409, 'core', 'themes/admin/xml_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2410, 'core', 'themes/admin/text_custom/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2411, 'core', 'themes/admin/text_custom/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2412, 'core', 'themes/admin/templates_cached/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2413, 'core', 'themes/admin/templates_cached/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2414, 'core', 'themes/admin/templates_cached/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2415, 'core', 'themes/default/images/icons/24x24/tool_buttons/version_desktop.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2416, 'core', 'themes/default/images/icons/48x48/tool_buttons/version_desktop.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2417, 'core', 'themes/default/images/icons/24x24/tool_buttons/version_mobile.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2418, 'core', 'themes/default/images/icons/48x48/tool_buttons/version_mobile.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2419, 'core', 'themes/default/images/icons/24x24/tool_buttons/sitemap.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2420, 'core', 'themes/default/images/icons/48x48/tool_buttons/sitemap.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2421, 'core_abstract_components', 'sources/hooks/systems/addon_registry/core_abstract_components.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2422, 'core_abstract_components', 'themes/default/templates/CROP_TEXT_MOUSE_OVER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2423, 'core_abstract_components', 'themes/default/templates/CROP_TEXT_MOUSE_OVER_INLINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2424, 'core_abstract_components', 'themes/default/templates/IMG_THUMB.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2425, 'core_abstract_components', 'themes/default/templates/POST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2426, 'core_abstract_components', 'themes/default/templates/POST_CHILD_LOAD_LINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2427, 'core_abstract_components', 'themes/default/templates/BUTTON_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2428, 'core_abstract_components', 'themes/default/templates/BUTTON_SCREEN_ITEM.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2429, 'core_abstract_components', 'themes/default/templates/STANDARDBOX_default.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2430, 'core_abstract_components', 'themes/default/templates/STANDARDBOX_accordion.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2431, 'core_abstract_components', 'themes/default/templates/HANDLE_CONFLICT_RESOLUTION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2432, 'core_abstract_components', 'themes/default/templates/FRACTIONAL_EDIT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2433, 'core_abstract_components', 'themes/default/javascript/fractional_edit.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2434, 'core_abstract_components', 'data/fractional_edit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2435, 'core_abstract_components', 'data/edit_ping.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2436, 'core_abstract_components', 'data/change_detection.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2437, 'core_abstract_components', 'themes/default/templates/STAFF_ACTIONS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2438, 'core_abstract_components', 'sources/hooks/systems/change_detection/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2439, 'core_abstract_components', 'sources_custom/hooks/systems/change_detection/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2440, 'core_abstract_components', 'sources/hooks/systems/change_detection/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2441, 'core_abstract_components', 'sources_custom/hooks/systems/change_detection/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2442, 'core_abstract_interfaces', 'themes/default/images/icons/24x24/status/inform.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2443, 'core_abstract_interfaces', 'themes/default/images/icons/48x48/status/inform.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2444, 'core_abstract_interfaces', 'themes/default/images/icons/24x24/status/notice.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2445, 'core_abstract_interfaces', 'themes/default/images/icons/48x48/status/notice.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2446, 'core_abstract_interfaces', 'themes/default/images/icons/24x24/status/warn.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2447, 'core_abstract_interfaces', 'themes/default/images/icons/48x48/status/warn.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2448, 'core_abstract_interfaces', 'sources/hooks/systems/addon_registry/core_abstract_interfaces.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2449, 'core_abstract_interfaces', 'themes/default/templates/QUESTION_UI_BUTTONS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2450, 'core_abstract_interfaces', 'themes/default/templates/PAGINATION_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2451, 'core_abstract_interfaces', 'themes/default/templates/AJAX_PAGINATION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2452, 'core_abstract_interfaces', 'themes/default/templates/CONFIRM_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2453, 'core_abstract_interfaces', 'themes/default/templates/WARN_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2454, 'core_abstract_interfaces', 'themes/default/templates/FULL_MESSAGE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2455, 'core_abstract_interfaces', 'themes/default/templates/INFORM_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2456, 'core_abstract_interfaces', 'themes/default/templates/REDIRECT_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2457, 'core_abstract_interfaces', 'themes/default/templates/WARNING_BOX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2458, 'core_abstract_interfaces', 'themes/default/templates/DO_NEXT_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2459, 'core_abstract_interfaces', 'themes/default/templates/DO_NEXT_ITEM.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2460, 'core_abstract_interfaces', 'themes/default/templates/DO_NEXT_SECTION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2461, 'core_abstract_interfaces', 'themes/default/css/do_next.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2462, 'core_abstract_interfaces', 'themes/default/templates/INDEX_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2463, 'core_abstract_interfaces', 'themes/default/templates/INDEX_SCREEN_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2464, 'core_abstract_interfaces', 'themes/default/templates/INDEX_SCREEN_FANCIER_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2465, 'core_abstract_interfaces', 'themes/default/templates/INDEX_SCREEN_FANCIER_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2466, 'core_abstract_interfaces', 'themes/default/templates/MAP_TABLE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2467, 'core_abstract_interfaces', 'themes/default/templates/MAP_TABLE_FIELD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2468, 'core_abstract_interfaces', 'themes/default/templates/MAP_TABLE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2469, 'core_abstract_interfaces', 'themes/default/templates/COLUMNED_TABLE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2470, 'core_abstract_interfaces', 'themes/default/templates/COLUMNED_TABLE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2471, 'core_abstract_interfaces', 'themes/default/templates/COLUMNED_TABLE_ROW_CELL_TICK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2472, 'core_abstract_interfaces', 'themes/default/templates/COLUMNED_TABLE_ROW_CELL_LINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2473, 'core_abstract_interfaces', 'themes/default/templates/COLUMNED_TABLE_ROW_CELL_SELECT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2474, 'core_abstract_interfaces', 'themes/default/templates/COLUMNED_TABLE_ACTION_DELETE_CATEGORY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2475, 'core_abstract_interfaces', 'themes/default/templates/COLUMNED_TABLE_ACTION_DELETE_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2476, 'core_abstract_interfaces', 'themes/default/templates/COLUMNED_TABLE_ACTION_INSTALL_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2477, 'core_abstract_interfaces', 'themes/default/templates/COLUMNED_TABLE_ACTION_REINSTALL_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2478, 'core_abstract_interfaces', 'themes/default/templates/COLUMNED_TABLE_ACTION_TRANSLATE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2479, 'core_abstract_interfaces', 'themes/default/templates/COLUMNED_TABLE_ACTION_UPGRADE_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2480, 'core_abstract_interfaces', 'themes/default/templates/COLUMNED_TABLE_ACTION_DOWNLOAD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2481, 'core_abstract_interfaces', 'themes/default/templates/COLUMNED_TABLE_HEADER_ROW.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2482, 'core_abstract_interfaces', 'themes/default/templates/COLUMNED_TABLE_HEADER_ROW_CELL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2483, 'core_abstract_interfaces', 'themes/default/templates/COLUMNED_TABLE_ROW.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2484, 'core_abstract_interfaces', 'themes/default/templates/COLUMNED_TABLE_ROW_CELL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2485, 'core_abstract_interfaces', 'themes/default/templates/PAGINATION_CONTINUE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2486, 'core_abstract_interfaces', 'themes/default/templates/PAGINATION_CONTINUE_LAST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2487, 'core_abstract_interfaces', 'themes/default/templates/PAGINATION_CONTINUE_FIRST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2488, 'core_abstract_interfaces', 'themes/default/templates/PAGINATION_LIST_PAGES.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2489, 'core_abstract_interfaces', 'themes/default/templates/PAGINATION_NEXT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2490, 'core_abstract_interfaces', 'themes/default/templates/PAGINATION_NEXT_LINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2491, 'core_abstract_interfaces', 'themes/default/templates/PAGINATION_PAGE_NUMBER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2492, 'core_abstract_interfaces', 'themes/default/templates/PAGINATION_PAGE_NUMBER_LINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2493, 'core_abstract_interfaces', 'themes/default/templates/PAGINATION_PER_PAGE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2494, 'core_abstract_interfaces', 'themes/default/templates/PAGINATION_PER_PAGE_OPTION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2495, 'core_abstract_interfaces', 'themes/default/templates/PAGINATION_PREVIOUS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2496, 'core_abstract_interfaces', 'themes/default/templates/PAGINATION_PREVIOUS_LINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2497, 'core_abstract_interfaces', 'themes/default/templates/PAGINATION_SORT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2498, 'core_abstract_interfaces', 'themes/default/templates/PAGINATION_SORTER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2499, 'core_abstract_interfaces', 'themes/default/templates/PAGINATION_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2500, 'core_abstract_interfaces', 'themes/default/templates/RESULTS_LAUNCHER_CONTINUE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2501, 'core_abstract_interfaces', 'themes/default/templates/RESULTS_LAUNCHER_PAGE_NUMBER_LINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2502, 'core_abstract_interfaces', 'themes/default/templates/RESULTS_LAUNCHER_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2503, 'core_abstract_interfaces', 'themes/default/templates/RESULTS_TABLE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2504, 'core_abstract_interfaces', 'themes/default/templates/RESULTS_TABLE_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2505, 'core_abstract_interfaces', 'themes/default/templates/RESULTS_TABLE_FIELD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2506, 'core_abstract_interfaces', 'themes/default/templates/RESULTS_TABLE_FIELD_TITLE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2507, 'core_abstract_interfaces', 'themes/default/templates/RESULTS_TABLE_FIELD_TITLE_SORTABLE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2508, 'core_abstract_interfaces', 'themes/default/templates/RESULTS_TABLE_TICK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2509, 'core_abstract_interfaces', 'themes/default/javascript/pagination.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2510, 'core_abstract_interfaces', 'themes/default/templates/INTERNALISED_AJAX_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2511, 'core_abstract_interfaces', 'themes/default/templates/MEMBER_TOOLTIP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2512, 'core_abstract_interfaces', 'themes/default/templates/SIMPLE_PREVIEW_BOX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2513, 'core_abstract_interfaces', 'themes/default/javascript/internalised_ajax_screen.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2514, 'core_abstract_interfaces', 'themes/default/templates/RESULTS_TABLE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2515, 'core_abstract_interfaces', 'sources/templates_interfaces.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2516, 'core_abstract_interfaces', 'sources/templates_redirect_screen.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2517, 'core_abstract_interfaces', 'sources/templates_confirm_screen.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2518, 'core_abstract_interfaces', 'sources/templates_internalise_screen.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2519, 'core_abstract_interfaces', 'sources/templates_results_table.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2520, 'core_abstract_interfaces', 'sources/templates_result_launcher.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2521, 'core_abstract_interfaces', 'sources/templates_pagination.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2522, 'core_abstract_interfaces', 'sources/templates_columned_table.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2523, 'core_abstract_interfaces', 'sources/templates_map_table.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2524, 'core_abstract_interfaces', 'sources/templates_donext.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2525, 'core_abstract_interfaces', 'themes/default/images/progress_indicator/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2526, 'core_abstract_interfaces', 'themes/default/images/progress_indicator/stage_future.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2527, 'core_abstract_interfaces', 'themes/default/images/progress_indicator/stage_past.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2528, 'core_abstract_interfaces', 'themes/default/images/progress_indicator/stage_present.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2529, 'core_addon_management', 'themes/default/images/icons/24x24/menu/adminzone/structure/addons.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2530, 'core_addon_management', 'themes/default/images/icons/48x48/menu/adminzone/structure/addons.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2531, 'core_addon_management', 'themes/default/images/icons/24x24/menu/_generic_admin/component.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2532, 'core_addon_management', 'themes/default/images/icons/48x48/menu/_generic_admin/component.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2533, 'core_addon_management', 'themes/default/css/addons_editor.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2534, 'core_addon_management', 'sources/hooks/systems/addon_registry/core_addon_management.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2535, 'core_addon_management', 'sources/addons.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2536, 'core_addon_management', 'sources/addons2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2537, 'core_addon_management', 'themes/default/templates/ADDON_SCREEN_ADDON.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2538, 'core_addon_management', 'themes/default/templates/ADDON_MULTI_CONFIRM_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2539, 'core_addon_management', 'themes/default/templates/ADDON_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2540, 'core_addon_management', 'themes/default/templates/ADDON_EXPORT_FILE_CHOICE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2541, 'core_addon_management', 'themes/default/templates/ADDON_EXPORT_LINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2542, 'core_addon_management', 'themes/default/templates/ADDON_EXPORT_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2543, 'core_addon_management', 'themes/default/templates/ADDON_INSTALL_CONFIRM_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2544, 'core_addon_management', 'themes/default/templates/ADDON_INSTALL_FILES.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2545, 'core_addon_management', 'themes/default/templates/ADDON_INSTALL_FILES_WARNING.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2546, 'core_addon_management', 'themes/default/templates/ADDON_INSTALL_WARNING.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2547, 'core_addon_management', 'themes/default/templates/ADDON_UNINSTALL_CONFIRM_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2548, 'core_addon_management', 'themes/default/templates/MODULE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2549, 'core_addon_management', 'themes/default/templates/ADDON_NAME.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2550, 'core_addon_management', 'adminzone/pages/modules/admin_addons.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2551, 'core_addon_management', 'exports/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2552, 'core_addon_management', 'exports/addons/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2553, 'core_addon_management', 'imports/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2554, 'core_addon_management', 'imports/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2555, 'core_addon_management', 'imports/addons/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2556, 'core_addon_management', 'imports/addons/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2557, 'core_addon_management', 'lang/EN/addons.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2558, 'core_addon_management', 'sources/hooks/systems/ajax_tree/choose_composr_homesite_addon.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2559, 'core_addon_management', 'themes/default/images/icons/14x14/install.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2560, 'core_addon_management', 'themes/default/images/icons/14x14/reinstall.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2561, 'core_addon_management', 'themes/default/images/icons/14x14/upgrade.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2562, 'core_addon_management', 'themes/default/images/icons/28x28/install.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2563, 'core_addon_management', 'themes/default/images/icons/28x28/reinstall.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2564, 'core_addon_management', 'themes/default/images/icons/28x28/upgrade.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2565, 'core_adminzone_dashboard', 'themes/default/css/adminzone_dashboard.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2566, 'core_adminzone_dashboard', 'sources/hooks/systems/addon_registry/core_adminzone_dashboard.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2567, 'core_adminzone_dashboard', 'themes/default/templates/BLOCK_MAIN_STAFF_NEW_VERSION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2568, 'core_adminzone_dashboard', 'themes/default/templates/BLOCK_MAIN_STAFF_TIPS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2569, 'core_adminzone_dashboard', 'themes/default/templates/BLOCK_MAIN_STAFF_CHECKLIST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2570, 'core_adminzone_dashboard', 'themes/default/templates/BLOCK_MAIN_STAFF_CHECKLIST_CUSTOM_TASK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2571, 'core_adminzone_dashboard', 'themes/default/templates/BLOCK_MAIN_STAFF_CHECKLIST_ITEM.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2572, 'core_adminzone_dashboard', 'themes/default/templates/BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_NA.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2573, 'core_adminzone_dashboard', 'themes/default/templates/BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_0.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2574, 'core_adminzone_dashboard', 'themes/default/templates/BLOCK_MAIN_STAFF_CHECKLIST_ITEM_STATUS_1.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2575, 'core_adminzone_dashboard', 'themes/default/templates/BLOCK_MAIN_NOTES.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2576, 'core_adminzone_dashboard', 'lang/EN/staff_checklist.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2577, 'core_adminzone_dashboard', 'sources/hooks/systems/cron/staff_checklist_notify.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2578, 'core_adminzone_dashboard', 'sources/hooks/systems/notifications/staff_checklist.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2579, 'core_adminzone_dashboard', 'themes/default/images/checklist/checklist-.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2580, 'core_adminzone_dashboard', 'themes/default/images/checklist/checklist0.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2581, 'core_adminzone_dashboard', 'themes/default/images/checklist/checklist1.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2582, 'core_adminzone_dashboard', 'themes/default/images/checklist/toggleicon.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2583, 'core_adminzone_dashboard', 'themes/default/images/checklist/toggleicon2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2584, 'core_adminzone_dashboard', 'themes/default/images/checklist/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2585, 'core_adminzone_dashboard', 'themes/default/images/checklist/not_completed.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2586, 'core_adminzone_dashboard', 'lang/EN/tips.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2587, 'core_adminzone_dashboard', 'sources/hooks/systems/snippets/checklist_task_manage.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2588, 'core_adminzone_dashboard', 'sources/hooks/blocks/main_staff_checklist/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2589, 'core_adminzone_dashboard', 'sources_custom/hooks/blocks/main_staff_checklist/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2590, 'core_adminzone_dashboard', 'sources/hooks/blocks/main_staff_checklist/forum.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2591, 'core_adminzone_dashboard', 'sources/hooks/blocks/main_staff_checklist/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2592, 'core_adminzone_dashboard', 'sources_custom/hooks/blocks/main_staff_checklist/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2593, 'core_adminzone_dashboard', 'sources/hooks/blocks/main_staff_checklist/copyright.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2594, 'core_adminzone_dashboard', 'sources/hooks/blocks/main_staff_checklist/cron.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2595, 'core_adminzone_dashboard', 'sources/hooks/blocks/main_staff_checklist/open_site.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2596, 'core_adminzone_dashboard', 'sources/hooks/blocks/main_staff_checklist/profile.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2597, 'core_adminzone_dashboard', 'sources/blocks/main_staff_actions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2598, 'core_adminzone_dashboard', 'sources/blocks/main_staff_checklist.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2599, 'core_adminzone_dashboard', 'sources/blocks/main_staff_new_version.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2600, 'core_adminzone_dashboard', 'sources/blocks/main_staff_tips.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2601, 'core_adminzone_dashboard', 'sources/blocks/main_staff_website_monitoring.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2602, 'core_adminzone_dashboard', 'sources/blocks/main_staff_links.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2603, 'core_adminzone_dashboard', 'themes/default/templates/BLOCK_MAIN_STAFF_LINKS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2604, 'core_adminzone_dashboard', 'themes/default/templates/BLOCK_MAIN_STAFF_WEBSITE_MONITORING.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2605, 'core_adminzone_dashboard', 'themes/default/images/checklist/cross.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2606, 'core_adminzone_dashboard', 'themes/default/images/checklist/cross2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2607, 'core_adminzone_dashboard', 'sources/hooks/systems/notifications/checklist_task.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2608, 'core_adminzone_dashboard', 'themes/default/templates/BLOCK_MAIN_STAFF_ACTIONS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2609, 'core_adminzone_dashboard', 'sources/hooks/systems/commandr_fs_extended_config/checklist_tasks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2610, 'core_adminzone_dashboard', 'sources/hooks/systems/commandr_fs_extended_config/staff_links.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2611, 'core_adminzone_dashboard', 'sources/hooks/systems/commandr_fs_extended_config/staff_monitoring_sites.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2612, 'core_cleanup_tools', 'themes/default/images/icons/24x24/menu/adminzone/tools/cleanup.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2613, 'core_cleanup_tools', 'themes/default/images/icons/48x48/menu/adminzone/tools/cleanup.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2614, 'core_cleanup_tools', 'sources/hooks/systems/config/is_on_block_cache.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2615, 'core_cleanup_tools', 'sources/hooks/systems/config/is_on_comcode_page_cache.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2616, 'core_cleanup_tools', 'sources/hooks/systems/config/is_on_lang_cache.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2617, 'core_cleanup_tools', 'sources/hooks/systems/config/is_on_template_cache.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2618, 'core_cleanup_tools', 'data/modules/admin_cleanup/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2619, 'core_cleanup_tools', 'data/modules/admin_cleanup/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2620, 'core_cleanup_tools', 'sources/hooks/systems/addon_registry/core_cleanup_tools.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2621, 'core_cleanup_tools', 'themes/default/templates/CLEANUP_ORPHANED_UPLOADS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2622, 'core_cleanup_tools', 'themes/default/templates/CLEANUP_COMPLETED_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2623, 'core_cleanup_tools', 'themes/default/templates/CLEANUP_PAGE_STATS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2624, 'core_cleanup_tools', 'adminzone/pages/modules/admin_cleanup.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2625, 'core_cleanup_tools', 'sources/hooks/systems/cleanup/comcode.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2626, 'core_cleanup_tools', 'lang/EN/cleanup.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2627, 'core_cleanup_tools', 'sources/hooks/systems/cleanup/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2628, 'core_cleanup_tools', 'sources_custom/hooks/systems/cleanup/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2629, 'core_cleanup_tools', 'sources/hooks/systems/cleanup/lost_disk_content.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2630, 'core_cleanup_tools', 'sources/hooks/systems/cleanup/admin_theme_images.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2631, 'core_cleanup_tools', 'sources/hooks/systems/cleanup/blocks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2632, 'core_cleanup_tools', 'sources/hooks/systems/cleanup/broken_urls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2633, 'core_cleanup_tools', 'sources/hooks/systems/cleanup/image_thumbs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2634, 'core_cleanup_tools', 'sources/hooks/systems/cleanup/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2635, 'core_cleanup_tools', 'sources_custom/hooks/systems/cleanup/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2636, 'core_cleanup_tools', 'sources/hooks/systems/cleanup/language.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2637, 'core_cleanup_tools', 'sources/hooks/systems/cleanup/mysql_optimise.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2638, 'core_cleanup_tools', 'sources/hooks/systems/cleanup/orphaned_lang_strings.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2639, 'core_cleanup_tools', 'sources/hooks/systems/cleanup/orphaned_uploads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2640, 'core_cleanup_tools', 'sources/hooks/systems/cleanup/templates.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2641, 'core_cleanup_tools', 'sources/hooks/systems/cleanup/criticise_mysql_fields.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2642, 'core_cleanup_tools', 'sources/hooks/systems/cleanup/page_backups.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2643, 'core_cleanup_tools', 'sources/hooks/systems/cleanup/tags.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2644, 'core_cleanup_tools', 'sources/hooks/systems/cleanup/urls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2645, 'core_cleanup_tools', 'sources/hooks/systems/cleanup/self_learning.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2646, 'core_cleanup_tools', 'sources/hooks/systems/tasks/find_broken_urls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2647, 'core_cleanup_tools', 'sources/hooks/systems/tasks/find_orphaned_lang_strings.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2648, 'core_cleanup_tools', 'sources/hooks/systems/tasks/find_orphaned_uploads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2649, 'core_cleanup_tools', 'sources/hooks/systems/cleanup/reorganise_uploads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2650, 'core_cleanup_tools', 'sources/hooks/systems/tasks/reorganise_uploads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2651, 'core_cleanup_tools', 'sources/hooks/systems/reorganise_uploads/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2652, 'core_cns', 'themes/default/images/cns_default_avatars/default.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2653, 'core_cns', 'themes/default/images/icons/24x24/tool_buttons/inbox.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2654, 'core_cns', 'themes/default/images/icons/48x48/tool_buttons/inbox.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2655, 'core_cns', 'themes/default/images/icons/24x24/tool_buttons/inbox2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2656, 'core_cns', 'themes/default/images/icons/48x48/tool_buttons/inbox2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2657, 'core_cns', 'themes/default/images/icons/24x24/menu/adminzone/tools/users/member_add.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2658, 'core_cns', 'themes/default/images/icons/48x48/menu/adminzone/tools/users/member_add.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2659, 'core_cns', 'themes/default/images/icons/24x24/menu/adminzone/security/usergroups_temp.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2660, 'core_cns', 'themes/default/images/icons/24x24/menu/adminzone/tools/users/delete_lurkers.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2661, 'core_cns', 'themes/default/images/icons/24x24/menu/adminzone/tools/users/member_edit.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2662, 'core_cns', 'themes/default/images/icons/24x24/menu/adminzone/tools/users/merge_members.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2663, 'core_cns', 'themes/default/images/icons/48x48/menu/adminzone/security/usergroups_temp.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2664, 'core_cns', 'themes/default/images/icons/48x48/menu/adminzone/tools/users/delete_lurkers.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2665, 'core_cns', 'themes/default/images/icons/48x48/menu/adminzone/tools/users/member_edit.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2666, 'core_cns', 'themes/default/images/icons/48x48/menu/adminzone/tools/users/merge_members.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2667, 'core_cns', 'themes/default/images/icons/24x24/tabs/member_account/profile.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2668, 'core_cns', 'themes/default/images/icons/48x48/tabs/member_account/profile.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2669, 'core_cns', 'themes/default/images/icons/24x24/tabs/member_account/profile2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2670, 'core_cns', 'themes/default/images/icons/48x48/tabs/member_account/profile2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2671, 'core_cns', 'themes/default/images/icons/24x24/tabs/member_account/edit/profile.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2672, 'core_cns', 'themes/default/images/icons/48x48/tabs/member_account/edit/profile.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2673, 'core_cns', 'themes/default/images/icons/24x24/tabs/member_account/edit/delete.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2674, 'core_cns', 'themes/default/images/icons/48x48/tabs/member_account/edit/delete.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2675, 'core_cns', 'themes/default/images/icons/24x24/tabs/member_account/edit/settings.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2676, 'core_cns', 'themes/default/images/icons/48x48/tabs/member_account/edit/settings.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2677, 'core_cns', 'themes/default/images/icons/24x24/menu/adminzone/audit/statistics/demographics.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2678, 'core_cns', 'themes/default/images/icons/48x48/menu/adminzone/audit/statistics/demographics.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2679, 'core_cns', 'themes/default/images/icons/24x24/tabs/member_account/edit/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2680, 'core_cns', 'themes/default/images/icons/24x24/tabs/member_account/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2681, 'core_cns', 'themes/default/images/icons/48x48/tabs/member_account/edit/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2682, 'core_cns', 'themes/default/images/icons/48x48/tabs/member_account/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2683, 'core_cns', 'themes/default/images/icons/14x14/birthday.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2684, 'core_cns', 'themes/default/images/icons/28x28/birthday.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2685, 'core_cns', 'themes/default/images/icons/24x24/menu/adminzone/style/emoticons.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2686, 'core_cns', 'themes/default/images/icons/48x48/menu/adminzone/style/emoticons.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2687, 'core_cns', 'themes/default/images/icons/24x24/menu/social/groups.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2688, 'core_cns', 'themes/default/images/icons/48x48/menu/social/groups.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2689, 'core_cns', 'themes/default/images/icons/24x24/buttons/ignore.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2690, 'core_cns', 'themes/default/images/icons/48x48/buttons/ignore.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2691, 'core_cns', 'themes/default/images/icons/24x24/menu/site_meta/user_actions/lost_password.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2692, 'core_cns', 'themes/default/images/icons/48x48/menu/site_meta/user_actions/lost_password.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2693, 'core_cns', 'themes/default/images/1x/cns_general/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2694, 'core_cns', 'themes/default/images/2x/cns_general/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2695, 'core_cns', 'themes/default/images/1x/cns_general/isoff.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2696, 'core_cns', 'themes/default/images/1x/cns_general/ison.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2697, 'core_cns', 'themes/default/images/2x/cns_general/isoff.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2698, 'core_cns', 'themes/default/images/2x/cns_general/ison.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2699, 'core_cns', 'sources/cns_forum_driver_helper_auth.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2700, 'core_cns', 'sources/hooks/modules/admin_setupwizard/core_cns.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2701, 'core_cns', 'sources/hooks/systems/content_meta_aware/topic.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2702, 'core_cns', 'sources/hooks/systems/content_meta_aware/post.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2703, 'core_cns', 'sources/hooks/modules/admin_import/emoticons.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2704, 'core_cns', 'sources/hooks/systems/notifications/cns_password_changed.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2705, 'core_cns', 'sources/hooks/systems/snippets/member_tooltip.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2706, 'core_cns', 'sources/hooks/systems/notifications/cns_rank_promoted.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2707, 'core_cns', 'sources/hooks/systems/snippets/exists_email.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2708, 'core_cns', 'sources/hooks/systems/snippets/profile_tab.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2709, 'core_cns', 'sources/hooks/systems/snippets/invite_missing.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2710, 'core_cns', 'sources/hooks/systems/snippets/exists_usergroup.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2711, 'core_cns', 'sources/hooks/systems/snippets/exists_emoticon.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2712, 'core_cns', 'sources/hooks/systems/sitemap/group.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2713, 'core_cns', 'sources/hooks/systems/sitemap/member.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2714, 'core_cns', 'sources/hooks/systems/config/allow_alpha_search.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2715, 'core_cns', 'sources/hooks/systems/config/allow_email_disable.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2716, 'core_cns', 'sources/hooks/systems/config/allow_email_from_staff_disable.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2717, 'core_cns', 'sources/hooks/systems/config/allow_international.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2718, 'core_cns', 'sources/hooks/systems/config/decryption_key.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2719, 'core_cns', 'sources/hooks/systems/config/encryption_key.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2720, 'core_cns', 'sources/hooks/systems/config/hot_topic_definition.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2721, 'core_cns', 'sources/hooks/systems/config/httpauth_is_enabled.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2722, 'core_cns', 'sources/hooks/systems/config/invites_per_day.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2723, 'core_cns', 'sources/hooks/systems/config/is_on_coppa.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2724, 'core_cns', 'sources/hooks/systems/config/is_on_invisibility.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2725, 'core_cns', 'sources/hooks/systems/config/is_on_invites.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2726, 'core_cns', 'sources/hooks/systems/config/is_on_timezone_detection.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2727, 'core_cns', 'sources/hooks/systems/config/maximum_password_length.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2728, 'core_cns', 'sources/hooks/systems/config/maximum_username_length.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2729, 'core_cns', 'sources/hooks/systems/config/minimum_password_length.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2730, 'core_cns', 'sources/hooks/systems/config/minimum_username_length.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2731, 'core_cns', 'sources/hooks/systems/config/dobs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2732, 'core_cns', 'sources/hooks/systems/config/one_per_email_address.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2733, 'core_cns', 'sources/hooks/systems/config/privacy_fax.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2734, 'core_cns', 'sources/hooks/systems/config/privacy_postal_address.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2735, 'core_cns', 'sources/hooks/systems/config/probation_usergroup.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2736, 'core_cns', 'sources/hooks/systems/config/prohibit_password_whitespace.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2737, 'core_cns', 'sources/hooks/systems/config/prohibit_username_whitespace.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2738, 'core_cns', 'sources/hooks/systems/config/require_new_member_validation.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2739, 'core_cns', 'sources/hooks/systems/config/restricted_usernames.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2740, 'core_cns', 'sources/hooks/systems/config/show_first_join_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2741, 'core_cns', 'sources/hooks/systems/notifications/cns_username_changed.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2742, 'core_cns', 'sources/hooks/systems/notifications/cns_group_join_request.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2743, 'core_cns', 'sources/hooks/systems/notifications/cns_group_declined.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2744, 'core_cns', 'sources/hooks/systems/notifications/cns_birthday.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2745, 'core_cns', 'sources/hooks/systems/notifications/cns_member_joined_group.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2746, 'core_cns', 'sources/hooks/systems/notifications/cns_new_member.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2747, 'core_cns', 'sources/hooks/systems/notifications/cns_member_needs_validation.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2748, 'core_cns', 'sources/hooks/systems/notifications/cns_username_changed_staff.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2749, 'core_cns', 'sources/hooks/systems/notifications/cns_group_join_request_staff.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2750, 'core_cns', 'sources/hooks/systems/realtime_rain/cns.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2751, 'core_cns', 'sources/hooks/systems/cron/cns_birthdays.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2752, 'core_cns', 'sources/hooks/systems/content_meta_aware/member.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2753, 'core_cns', 'sources/hooks/systems/content_meta_aware/group.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2754, 'core_cns', 'sources/hooks/systems/commandr_fs/groups.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2755, 'core_cns', 'sources/hooks/systems/disposable_values/cns_member_count.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2756, 'core_cns', 'sources/hooks/systems/disposable_values/cns_topic_count.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2757, 'core_cns', 'sources/hooks/systems/disposable_values/cns_post_count.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2758, 'core_cns', 'sources/hooks/systems/disposable_values/cns_newest_member_id.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2759, 'core_cns', 'sources/hooks/systems/disposable_values/cns_newest_member_username.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2760, 'core_cns', 'sources/hooks/modules/members/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2761, 'core_cns', 'sources_custom/hooks/modules/members/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2762, 'core_cns', 'sources/hooks/modules/members/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2763, 'core_cns', 'sources_custom/hooks/modules/members/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2764, 'core_cns', 'sources/hooks/modules/search/cns_members.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2765, 'core_cns', 'sources/hooks/systems/addon_registry/core_cns.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2766, 'core_cns', 'sources/hooks/blocks/main_staff_checklist/usergroup_membership.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2767, 'core_cns', 'adminzone/pages/modules/admin_cns_emoticons.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2768, 'core_cns', 'adminzone/pages/modules/admin_cns_groups.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2769, 'core_cns', 'adminzone/pages/modules/admin_cns_merge_members.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2770, 'core_cns', 'adminzone/pages/modules/admin_cns_members.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2771, 'core_cns', 'themes/default/images/cns_rank_images/0.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2772, 'core_cns', 'themes/default/images/cns_rank_images/1.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2773, 'core_cns', 'themes/default/images/cns_rank_images/2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2774, 'core_cns', 'themes/default/images/cns_rank_images/3.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2775, 'core_cns', 'themes/default/images/cns_rank_images/4.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2776, 'core_cns', 'themes/default/images/cns_rank_images/admin.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2777, 'core_cns', 'themes/default/images/cns_rank_images/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2778, 'core_cns', 'themes/default/images/cns_rank_images/mod.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2779, 'core_cns', 'sources/hooks/modules/admin_import/csv_members.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2780, 'core_cns', 'themes/default/templates/CNS_EMOTICON_TABLE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2781, 'core_cns', 'themes/default/templates/CNS_JOIN_STEP1_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2782, 'core_cns', 'themes/default/templates/CNS_USERS_ONLINE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2783, 'core_cns', 'themes/default/templates/CNS_MEMBER_ACTION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2784, 'core_cns', 'themes/default/templates/CNS_MEMBER_DIRECTORY_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2785, 'core_cns', 'themes/default/templates/CNS_MEMBER_PROFILE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2786, 'core_cns', 'themes/default/templates/CNS_MEMBER_PROFILE_ABOUT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2787, 'core_cns', 'themes/default/templates/CNS_MEMBER_PROFILE_EDIT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2788, 'core_cns', 'themes/default/javascript/profile.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2789, 'core_cns', 'themes/default/templates/CNS_MEMBER_BOX_CUSTOM_FIELD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2790, 'core_cns', 'themes/default/templates/CNS_USER_MEMBER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2791, 'core_cns', 'themes/default/templates/CNS_VIEW_GROUP_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2792, 'core_cns', 'themes/default/templates/CNS_VIEW_GROUP_MEMBER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2793, 'core_cns', 'themes/default/templates/CNS_VIEW_GROUP_MEMBER_PROSPECTIVE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2794, 'core_cns', 'themes/default/templates/CNS_VIEW_GROUP_MEMBER_SECONDARY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2795, 'core_cns', 'data/approve_ip.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2796, 'core_cns', 'themes/default/images/cns_emoticons/birthday.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2797, 'core_cns', 'themes/default/images/cns_emoticons/angry.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2798, 'core_cns', 'themes/default/images/cns_emoticons/blink.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2799, 'core_cns', 'themes/default/images/cns_emoticons/blush.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2800, 'core_cns', 'themes/default/images/cns_emoticons/cheeky.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2801, 'core_cns', 'themes/default/images/cns_emoticons/christmas.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2802, 'core_cns', 'themes/default/images/cns_emoticons/confused.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2803, 'core_cns', 'themes/default/images/cns_emoticons/constipated.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2804, 'core_cns', 'themes/default/images/cns_emoticons/cool.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2805, 'core_cns', 'themes/default/images/cns_emoticons/cry.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2806, 'core_cns', 'themes/default/images/cns_emoticons/cyborg.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2807, 'core_cns', 'themes/default/images/cns_emoticons/depressed.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2808, 'core_cns', 'themes/default/images/cns_emoticons/devil.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2809, 'core_cns', 'themes/default/images/cns_emoticons/drool.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2810, 'core_cns', 'themes/default/images/cns_emoticons/dry.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2811, 'core_cns', 'themes/default/images/cns_emoticons/glee.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2812, 'core_cns', 'themes/default/images/cns_emoticons/grin.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2813, 'core_cns', 'themes/default/images/cns_emoticons/guitar.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2814, 'core_cns', 'themes/default/images/cns_emoticons/hand.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2815, 'core_cns', 'themes/default/images/cns_emoticons/hippie.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2816, 'core_cns', 'themes/default/images/cns_emoticons/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2817, 'core_cns', 'themes/default/images/cns_emoticons/king.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2818, 'core_cns', 'themes/default/images/cns_emoticons/kiss.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2819, 'core_cns', 'themes/default/images/cns_emoticons/lol.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2820, 'core_cns', 'themes/default/images/cns_emoticons/mellow.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2821, 'core_cns', 'themes/default/images/cns_emoticons/nerd.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2822, 'core_cns', 'themes/default/images/cns_emoticons/ninja2.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2823, 'core_cns', 'themes/default/images/cns_emoticons/nod.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2824, 'core_cns', 'themes/default/images/cns_emoticons/offtopic.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2825, 'core_cns', 'themes/default/images/cns_emoticons/party.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2826, 'core_cns', 'themes/default/images/cns_emoticons/ph34r.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2827, 'core_cns', 'themes/default/images/cns_emoticons/puppyeyes.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2828, 'core_cns', 'themes/default/images/cns_emoticons/rockon.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2829, 'core_cns', 'themes/default/images/cns_emoticons/rolleyes.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2830, 'core_cns', 'themes/default/images/cns_emoticons/sad.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2831, 'core_cns', 'themes/default/images/cns_emoticons/sarcy.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2832, 'core_cns', 'themes/default/images/cns_emoticons/shake.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2833, 'core_cns', 'themes/default/images/cns_emoticons/shocked.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2834, 'core_cns', 'themes/default/images/cns_emoticons/shutup.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2835, 'core_cns', 'themes/default/images/cns_emoticons/sick.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2836, 'core_cns', 'themes/default/images/cns_emoticons/sinner.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2837, 'core_cns', 'themes/default/images/cns_emoticons/smile.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2838, 'core_cns', 'themes/default/images/cns_emoticons/thumbs.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2839, 'core_cns', 'themes/default/images/cns_emoticons/upsidedown.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2840, 'core_cns', 'themes/default/images/cns_emoticons/whistle.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2841, 'core_cns', 'themes/default/images/cns_emoticons/wink.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2842, 'core_cns', 'themes/default/images/cns_emoticons/wub.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2843, 'core_cns', 'themes/default/images/cns_emoticons/zzz.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2844, 'core_cns', 'themes/default/images/cns_emoticons/none.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2845, 'core_cns', 'themes/default/images/cns_emoticons/angel.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2846, 'core_cns', 'themes/default/images/cns_emoticons/cowboy.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2847, 'core_cns', 'themes/default/images/cns_emoticons/fight.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2848, 'core_cns', 'themes/default/images/cns_emoticons/goodbye.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2849, 'core_cns', 'themes/default/images/cns_emoticons/idea.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2850, 'core_cns', 'themes/default/images/cns_emoticons/boat.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2851, 'core_cns', 'themes/default/images/cns_emoticons/fishing.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2852, 'core_cns', 'themes/default/images/cns_emoticons/reallybadday.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2853, 'core_cns', 'lang/EN/cns.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2854, 'core_cns', 'lang/EN/cns_special_cpf.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2855, 'core_cns', 'lang/EN/cns_components.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2856, 'core_cns', 'lang/EN/cns_config.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2857, 'core_cns', 'sources/forum/cns.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2858, 'core_cns', 'sources/cns_forum_driver_helper.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2859, 'core_cns', 'sources/cns_forum_driver_helper_install.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2860, 'core_cns', 'sources/hooks/systems/cleanup/cns_members.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2861, 'core_cns', 'sources/hooks/modules/admin_unvalidated/cns_members.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2862, 'core_cns', 'sources/hooks/systems/cns_cpf_filter/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2863, 'core_cns', 'sources_custom/hooks/systems/cns_cpf_filter/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2864, 'core_cns', 'sources/hooks/systems/cns_cpf_filter/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2865, 'core_cns', 'sources_custom/hooks/systems/cns_cpf_filter/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2866, 'core_cns', 'sources/hooks/systems/rss/cns_birthdays.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2867, 'core_cns', 'sources/hooks/systems/rss/cns_members.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2868, 'core_cns', 'sources/cns_forums.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2869, 'core_cns', 'sources/cns_forums2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2870, 'core_cns', 'sources/cns_forums_action.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2871, 'core_cns', 'sources/cns_forums_action2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2872, 'core_cns', 'sources/cns_general.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2873, 'core_cns', 'sources/cns_notifications.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2874, 'core_cns', 'sources/cns_general_action.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2875, 'core_cns', 'sources/cns_general_action2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2876, 'core_cns', 'sources/cns_groups.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2877, 'core_cns', 'sources/cns_groups2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2878, 'core_cns', 'sources/cns_groups_action.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2879, 'core_cns', 'sources/cns_groups_action2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2880, 'core_cns', 'sources/cns_install.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2881, 'core_cns', 'sources/cns_members.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2882, 'core_cns', 'sources/cns_members2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2883, 'core_cns', 'sources/cns_members_action.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2884, 'core_cns', 'sources/cns_members_action2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2885, 'core_cns', 'sources/cns_moderation.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2886, 'core_cns', 'sources/cns_moderation_action.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2887, 'core_cns', 'sources/cns_moderation_action2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2888, 'core_cns', 'sources/cns_polls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2889, 'core_cns', 'sources/cns_polls_action.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2890, 'core_cns', 'sources/cns_polls_action2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2891, 'core_cns', 'sources/cns_posts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2892, 'core_cns', 'sources/cns_posts2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2893, 'core_cns', 'sources/cns_posts_action.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2894, 'core_cns', 'sources/cns_posts_action2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2895, 'core_cns', 'sources/cns_posts_action3.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2896, 'core_cns', 'sources/cns_topics.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2897, 'core_cns', 'sources/cns_topics_action.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2898, 'core_cns', 'sources/cns_topics_action2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2899, 'core_cns', 'site/pages/modules/groups.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2900, 'core_cns', 'site/pages/modules/members.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2901, 'core_cns', 'site/pages/modules/users_online.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2902, 'core_cns', 'sources/hooks/systems/profiles_tabs/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2903, 'core_cns', 'sources_custom/hooks/systems/profiles_tabs/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2904, 'core_cns', 'sources/hooks/systems/profiles_tabs_edit/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2905, 'core_cns', 'sources_custom/hooks/systems/profiles_tabs_edit/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2906, 'core_cns', 'sources/hooks/systems/commandr_fs/members.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2907, 'core_cns', 'sources/hooks/modules/admin_stats/cns_demographics.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2908, 'core_cns', 'pages/modules/join.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2909, 'core_cns', 'sources/cns_join.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2910, 'core_cns', 'pages/modules/lost_password.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2911, 'core_cns', 'themes/default/templates/CNS_AUTO_TIME_ZONE_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2912, 'core_cns', 'themes/default/templates/CNS_DELURK_CONFIRM.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2913, 'core_cns', 'themes/default/templates/CNS_JOIN_STEP2_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2914, 'core_cns', 'lang/EN/cns_lurkers.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2915, 'core_cns', 'sources/cns_profiles.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2916, 'core_cns', 'sources/cns_lost_password.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2917, 'core_cns', 'sources/hooks/systems/profiles_tabs/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2918, 'core_cns', 'sources_custom/hooks/systems/profiles_tabs/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2919, 'core_cns', 'sources/hooks/systems/profiles_tabs/about.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2920, 'core_cns', 'sources/hooks/systems/profiles_tabs/edit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2921, 'core_cns', 'sources/hooks/systems/profiles_tabs_edit/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2922, 'core_cns', 'sources_custom/hooks/systems/profiles_tabs_edit/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2923, 'core_cns', 'sources/hooks/systems/profiles_tabs_edit/profile.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2924, 'core_cns', 'sources/hooks/systems/profiles_tabs_edit/settings.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2925, 'core_cns', 'sources/hooks/systems/profiles_tabs_edit/delete.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2926, 'core_cns', 'sources/cns_popups.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2927, 'core_cns', 'sources/hooks/systems/commandr_fs/emoticons.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2928, 'core_cns', 'sources/hooks/systems/resource_meta_aware/emoticon.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2929, 'core_cns', 'sources/hooks/systems/commandr_fs_extended_member/known_login_ips.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2930, 'core_cns', 'sources/hooks/systems/preview/cns_emoticon.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2931, 'core_cns', 'sources/hooks/systems/cron/cns_confirm_reminder.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2932, 'core_cns', 'themes/default/templates/CNS_TOPIC_POST_AVATAR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2933, 'core_cns', 'themes/default/templates/CNS_GROUP_DIRECTORY_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2934, 'core_cns', 'themes/default/css/cns.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2935, 'core_cns', 'themes/default/css/cns_editor.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2936, 'core_cns', 'lang/EN/cns_member_directory.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2937, 'core_cns', 'data/username_check.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2938, 'core_cns', 'sources/hooks/systems/cns_auth/aef.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2939, 'core_cns', 'sources/hooks/systems/cns_auth/converge.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2940, 'core_cns', 'sources/hooks/systems/cns_auth/phpbb3.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2941, 'core_cns', 'sources/hooks/systems/cns_auth/smf.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2942, 'core_cns', 'sources/hooks/systems/cns_auth/vb3.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2943, 'core_cns', 'sources/hooks/systems/cns_auth/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2944, 'core_cns', 'sources_custom/hooks/systems/cns_auth/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2945, 'core_cns', 'themes/default/templates/POSTING_FIELD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2946, 'core_cns', 'themes/default/templates/CNS_MEMBER_BOX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2947, 'core_cns', 'themes/default/templates/CNS_RANK_IMAGE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2948, 'core_cns', 'sources/password_rules.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2949, 'core_cns', 'lang/EN/password_rules.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2950, 'core_cns', 'sources/hooks/systems/config/complex_privacy_options.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2951, 'core_cns', 'sources/hooks/systems/config/display_name_generator.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2952, 'core_cns', 'sources/hooks/systems/config/email_confirm_join.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2953, 'core_cns', 'sources/hooks/systems/config/enable_highlight_name.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2954, 'core_cns', 'sources/hooks/systems/config/enable_privacy_tab.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2955, 'core_cns', 'sources/hooks/systems/config/enable_user_online_groups.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2956, 'core_cns', 'sources/hooks/systems/config/finish_profile.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2957, 'core_cns', 'sources/hooks/systems/config/important_groups_per_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2958, 'core_cns', 'sources/hooks/systems/config/md_default_sort_order.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2959, 'core_cns', 'sources/hooks/systems/config/members_per_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2960, 'core_cns', 'sources/hooks/systems/config/minimum_password_strength.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2961, 'core_cns', 'sources/hooks/systems/config/normal_groups_per_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2962, 'core_cns', 'sources/hooks/systems/config/password_change_days.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2963, 'core_cns', 'sources/hooks/systems/config/password_expiry_days.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2964, 'core_cns', 'sources/hooks/systems/config/password_reset_process.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2965, 'core_cns', 'sources/hooks/systems/config/primary_members_per_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2966, 'core_cns', 'sources/hooks/systems/config/secondary_members_per_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2967, 'core_cns', 'sources/hooks/systems/config/show_empty_cpfs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2968, 'core_cns', 'sources/hooks/systems/config/use_joindate.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2969, 'core_cns', 'sources/hooks/systems/config/use_lastondate.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2970, 'core_cns', 'sources/hooks/systems/config/username_profile_links.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2971, 'core_cns', 'sources/hooks/systems/config/valid_email_domains.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2972, 'core_cns', 'sources/hooks/systems/config/enable_birthdays.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2973, 'core_cns', 'sources/cns_field_editability.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2974, 'core_cns', 'sources/hooks/systems/tasks/download_member_csv.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2975, 'core_cns', 'sources/hooks/systems/tasks/cns_members_recache.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2976, 'core_cns', 'sources/hooks/systems/tasks/import_member_csv.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2977, 'core_cns', 'sources/blocks/main_members.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2978, 'core_cns', 'themes/default/templates/BLOCK_MAIN_MEMBERS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2979, 'core_cns', 'themes/default/templates/BLOCK_MAIN_MEMBERS_COMPLEX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2980, 'core_cns', 'themes/default/templates/CNS_MEMBER_DIRECTORY_SCREEN_FILTERS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2981, 'core_cns', 'themes/default/templates/CNS_MEMBER_DIRECTORY_SCREEN_FILTER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2982, 'core_cns', 'themes/default/templates/CNS_MEMBER_DIRECTORY_USERNAME.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2983, 'core_cns', 'sources/hooks/systems/symbols/CPF_LIST.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2984, 'core_cns', 'themes/default/css/cns_member_profiles.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2985, 'core_cns', 'themes/default/css/cns_member_directory.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2986, 'core_cns', 'themes/default/css/cns_admin.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2987, 'core_cns', 'themes/default/css/cns_header.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2988, 'core_cns', 'themes/default/css/cns_footer.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2989, 'core_cns', 'themes/default/templates/CNS_POST_MAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2990, 'core_cns', 'themes/default/templates/CNS_POST_MAP_ITEM.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2991, 'core_cns', 'themes/default/images/cns_post_map/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2992, 'core_cns', 'themes/default/images/cns_post_map/last_mesg_level.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2993, 'core_cns', 'themes/default/images/cns_post_map/mesg_level.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2994, 'core_cns', 'themes/default/images/cns_post_map/middle_mesg_level.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2995, 'core_cns', 'sources/hooks/systems/config/is_on_post_map.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2996, 'core_cns', 'sources/hooks/systems/config/is_on_automatic_mark_topic_read.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2997, 'core_comcode_pages', 'themes/default/images/icons/24x24/menu/cms/comcode_page_edit.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2998, 'core_comcode_pages', 'themes/default/images/icons/48x48/menu/cms/comcode_page_edit.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (2999, 'core_comcode_pages', 'sources/hooks/systems/config/points_COMCODE_PAGE_ADD.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3000, 'core_comcode_pages', 'sources/hooks/systems/addon_registry/core_comcode_pages.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3001, 'core_comcode_pages', 'themes/default/templates/COMCODE_PAGE_EDIT_ACTIONS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3002, 'core_comcode_pages', 'themes/default/templates/COMCODE_PAGE_BOX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3003, 'core_comcode_pages', 'themes/default/templates/GENERATE_PAGE_SITEMAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3004, 'core_comcode_pages', 'themes/default/templates/GENERATE_PAGE_SITEMAP_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3005, 'core_comcode_pages', 'sources/hooks/modules/search/comcode_pages.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3006, 'core_comcode_pages', 'sources/hooks/systems/content_meta_aware/comcode_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3007, 'core_comcode_pages', 'sources/hooks/systems/commandr_fs/comcode_pages.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3008, 'core_comcode_pages', 'themes/default/templates/COMCODE_PAGE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3009, 'core_comcode_pages', 'sources/hooks/systems/rss/comcode_pages.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3010, 'core_comcode_pages', 'sources/hooks/systems/cleanup/comcode_pages.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3011, 'core_comcode_pages', 'cms/pages/modules/cms_comcode_pages.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3012, 'core_comcode_pages', 'sources/hooks/systems/preview/comcode_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3013, 'core_comcode_pages', 'sources/hooks/systems/attachments/comcode_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3014, 'core_comcode_pages', 'sources/hooks/modules/admin_unvalidated/comcode_pages.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3015, 'core_comcode_pages', 'sources/hooks/modules/admin_newsletter/comcode_pages.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3016, 'core_comcode_pages', 'sources/hooks/systems/config/comcode_page_default_review_freq.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3017, 'core_comcode_pages', 'sources/hooks/systems/config/is_on_comcode_page_children.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3018, 'core_comcode_pages', 'sources/hooks/systems/sitemap/comcode_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3019, 'core_comcode_pages', 'themes/default/templates/COMCODE_PAGE_MANAGE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3020, 'core_comcode_pages', 'data/modules/cms_comcode_pages/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3021, 'core_comcode_pages', 'data/modules/cms_comcode_pages/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3022, 'core_comcode_pages', 'data/modules/cms_comcode_pages/EN/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3023, 'core_comcode_pages', 'data/modules/cms_comcode_pages/EN/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3024, 'core_comcode_pages', 'data/modules/cms_comcode_pages/EN/about_us.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3025, 'core_comcode_pages', 'data/modules/cms_comcode_pages/EN/advertise.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3026, 'core_comcode_pages', 'data/modules/cms_comcode_pages/EN/article.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3027, 'core_comcode_pages', 'data/modules/cms_comcode_pages/EN/competitor_comparison.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3028, 'core_comcode_pages', 'data/modules/cms_comcode_pages/EN/contact_us.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3029, 'core_comcode_pages', 'data/modules/cms_comcode_pages/EN/donate.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3030, 'core_comcode_pages', 'data/modules/cms_comcode_pages/EN/guestbook.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3031, 'core_comcode_pages', 'data/modules/cms_comcode_pages/EN/press_release.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3032, 'core_comcode_pages', 'data/modules/cms_comcode_pages/EN/pricing.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3033, 'core_comcode_pages', 'data/modules/cms_comcode_pages/EN/landing_page.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3034, 'core_comcode_pages', 'data/modules/cms_comcode_pages/EN/two_column_layout.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3035, 'core_comcode_pages', 'data/modules/cms_comcode_pages/EN/under_construction.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3036, 'core_comcode_pages', 'data/modules/cms_comcode_pages/EN/rules_balanced.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3037, 'core_comcode_pages', 'data/modules/cms_comcode_pages/EN/rules_corporate.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3038, 'core_comcode_pages', 'data/modules/cms_comcode_pages/EN/rules_liberal.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3039, 'core_comcode_pages', 'themes/default/images/under_construction_animated.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3040, 'core_comcode_pages', 'themes/default/images/icons/24x24/menu/pages/guestbook.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3041, 'core_comcode_pages', 'themes/default/images/icons/48x48/menu/pages/guestbook.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3042, 'core_comcode_pages', 'themes/default/images/icons/24x24/menu/pages/advertise.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3043, 'core_comcode_pages', 'themes/default/images/icons/24x24/menu/pages/donate.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3044, 'core_comcode_pages', 'themes/default/images/icons/48x48/menu/pages/donate.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3045, 'core_comcode_pages', 'themes/default/images/icons/48x48/menu/pages/advertise.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3046, 'core_comcode_pages', 'themes/default/images/icons/24x24/contact_methods/address.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3047, 'core_comcode_pages', 'themes/default/images/icons/24x24/contact_methods/email.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3048, 'core_comcode_pages', 'themes/default/images/icons/24x24/contact_methods/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3049, 'core_comcode_pages', 'themes/default/images/icons/24x24/contact_methods/telephone.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3050, 'core_comcode_pages', 'themes/default/images/icons/48x48/contact_methods/address.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3051, 'core_comcode_pages', 'themes/default/images/icons/48x48/contact_methods/email.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3052, 'core_comcode_pages', 'themes/default/images/icons/48x48/contact_methods/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3053, 'core_comcode_pages', 'themes/default/images/icons/48x48/contact_methods/telephone.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3054, 'core_comcode_pages', 'themes/default/images/icons/24x24/links/google_plus.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3055, 'core_comcode_pages', 'themes/default/images/icons/24x24/links/skype.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3056, 'core_comcode_pages', 'themes/default/images/icons/24x24/links/xmpp.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3057, 'core_comcode_pages', 'themes/default/images/icons/48x48/links/google_plus.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3058, 'core_comcode_pages', 'themes/default/images/icons/48x48/links/skype.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3059, 'core_comcode_pages', 'themes/default/images/icons/48x48/links/xmpp.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3060, 'core_comcode_pages', 'themes/default/images/icons/24x24/tiers/bronze.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3061, 'core_comcode_pages', 'themes/default/images/icons/24x24/tiers/gold.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3062, 'core_comcode_pages', 'themes/default/images/icons/24x24/tiers/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3063, 'core_comcode_pages', 'themes/default/images/icons/24x24/tiers/platinum.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3064, 'core_comcode_pages', 'themes/default/images/icons/24x24/tiers/silver.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3065, 'core_comcode_pages', 'themes/default/images/icons/48x48/tiers/bronze.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3066, 'core_comcode_pages', 'themes/default/images/icons/48x48/tiers/gold.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3067, 'core_comcode_pages', 'themes/default/images/icons/48x48/tiers/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3068, 'core_comcode_pages', 'themes/default/images/icons/48x48/tiers/platinum.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3069, 'core_comcode_pages', 'themes/default/images/icons/48x48/tiers/silver.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3070, 'core_configuration', 'themes/default/images/icons/24x24/menu/adminzone/setup/config/config.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3071, 'core_configuration', 'themes/default/images/icons/48x48/menu/adminzone/setup/config/config.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3072, 'core_configuration', 'sources/hooks/systems/sitemap/config_category.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3073, 'core_configuration', 'sources/hooks/systems/config_categories/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3074, 'core_configuration', 'sources/hooks/systems/config_categories/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3075, 'core_configuration', 'sources/hooks/systems/config_categories/accessibility.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3076, 'core_configuration', 'sources/hooks/systems/config_categories/admin.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3077, 'core_configuration', 'sources/hooks/systems/config_categories/blocks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3078, 'core_configuration', 'sources/hooks/systems/config_categories/composr_apis.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3079, 'core_configuration', 'sources/hooks/systems/config_categories/feature.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3080, 'core_configuration', 'sources/hooks/systems/config_categories/forums.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3081, 'core_configuration', 'sources/hooks/systems/config_categories/performance.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3082, 'core_configuration', 'sources/hooks/systems/config_categories/privacy.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3083, 'core_configuration', 'sources/hooks/systems/config_categories/security.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3084, 'core_configuration', 'sources/hooks/systems/config_categories/server.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3085, 'core_configuration', 'sources/hooks/systems/config_categories/site.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3086, 'core_configuration', 'sources/hooks/systems/config_categories/theme.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3087, 'core_configuration', 'sources/hooks/systems/config_categories/users.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3088, 'core_configuration', 'sources/hooks/systems/config/csrf_token_expire_fresh.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3089, 'core_configuration', 'sources/hooks/systems/config/csrf_token_expire_new.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3090, 'core_configuration', 'sources/hooks/systems/config/header_menu_call_string.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3091, 'core_configuration', 'sources/hooks/systems/config/max_moniker_length.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3092, 'core_configuration', 'sources/hooks/systems/config/enable_seo_fields.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3093, 'core_configuration', 'sources/hooks/systems/config/enable_staff_notes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3094, 'core_configuration', 'sources/hooks/systems/config/filetype_icons.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3095, 'core_configuration', 'sources/hooks/systems/config/force_local_temp_dir.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3096, 'core_configuration', 'sources/hooks/systems/config/general_safety_listing_limit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3097, 'core_configuration', 'sources/hooks/systems/config/hack_ban_threshold.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3098, 'core_configuration', 'sources/hooks/systems/config/honeypot_phrase.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3099, 'core_configuration', 'sources/hooks/systems/config/honeypot_url.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3100, 'core_configuration', 'sources/hooks/systems/config/implied_spammer_confidence.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3101, 'core_configuration', 'sources/hooks/systems/config/edit_under.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3102, 'core_configuration', 'sources/hooks/systems/config/enable_animations.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3103, 'core_configuration', 'sources/hooks/systems/config/breadcrumb_crop_length.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3104, 'core_configuration', 'sources/hooks/systems/config/brute_force_instant_ban.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3105, 'core_configuration', 'sources/hooks/systems/config/brute_force_login_minutes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3106, 'core_configuration', 'sources/hooks/systems/config/brute_force_threshold.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3107, 'core_configuration', 'sources/hooks/systems/config/call_home.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3108, 'core_configuration', 'sources/hooks/systems/config/cleanup_files.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3109, 'core_configuration', 'sources/hooks/systems/config/jpeg_quality.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3110, 'core_configuration', 'sources/hooks/systems/config/repair_images.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3111, 'core_configuration', 'sources/hooks/systems/config/mail_queue.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3112, 'core_configuration', 'sources/hooks/systems/config/mail_queue_debug.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3113, 'core_configuration', 'sources/hooks/systems/config/modal_user.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3114, 'core_configuration', 'sources/hooks/systems/config/password_cookies.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3115, 'core_configuration', 'sources/hooks/systems/config/proxy.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3116, 'core_configuration', 'sources/hooks/systems/config/proxy_password.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3117, 'core_configuration', 'sources/hooks/systems/config/proxy_port.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3118, 'core_configuration', 'sources/hooks/systems/config/proxy_user.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3119, 'core_configuration', 'sources/hooks/systems/config/session_prudence.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3120, 'core_configuration', 'sources/hooks/systems/config/tornevall_api_password.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3121, 'core_configuration', 'sources/hooks/systems/config/tornevall_api_username.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3122, 'core_configuration', 'sources/hooks/systems/config/message_received_emails.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3123, 'core_configuration', 'sources/hooks/systems/config/use_true_from.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3124, 'core_configuration', 'sources/hooks/systems/config/email_log_days.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3125, 'core_configuration', 'sources/hooks/systems/config/block_top_login.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3126, 'core_configuration', 'sources/hooks/systems/config/block_top_personal_stats.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3127, 'core_configuration', 'sources/hooks/systems/config/fixed_width.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3128, 'core_configuration', 'sources/hooks/systems/config/collapse_user_zones.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3129, 'core_configuration', 'sources/hooks/systems/config/url_monikers_enabled.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3130, 'core_configuration', 'sources/hooks/systems/config/tasks_background.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3131, 'core_configuration', 'sources/hooks/systems/config/moniker_transliteration.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3132, 'core_configuration', 'sources/hooks/systems/config/vote_member_ip_restrict.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3133, 'core_configuration', 'sources/hooks/systems/config/spam_approval_threshold.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3134, 'core_configuration', 'sources/hooks/systems/config/spam_ban_threshold.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3135, 'core_configuration', 'sources/hooks/systems/config/spam_blackhole_detection.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3136, 'core_configuration', 'sources/hooks/systems/config/forced_preview_option.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3137, 'core_configuration', 'sources/hooks/systems/config/default_preview_guests.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3138, 'core_configuration', 'sources/hooks/systems/config/spam_block_lists.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3139, 'core_configuration', 'sources/hooks/systems/config/spam_block_threshold.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3140, 'core_configuration', 'sources/hooks/systems/config/spam_cache_time.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3141, 'core_configuration', 'sources/hooks/systems/config/spam_check_exclusions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3142, 'core_configuration', 'sources/hooks/systems/config/spam_check_level.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3143, 'core_configuration', 'sources/hooks/systems/config/spam_check_usernames.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3144, 'core_configuration', 'sources/hooks/systems/config/spam_stale_threshold.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3145, 'core_configuration', 'sources/hooks/systems/config/stopforumspam_api_key.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3146, 'core_configuration', 'sources/hooks/systems/config/login_error_secrecy.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3147, 'core_configuration', 'sources/hooks/systems/config/cdn.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3148, 'core_configuration', 'sources/hooks/systems/config/allow_theme_image_selector.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3149, 'core_configuration', 'sources/hooks/systems/config/infinite_scrolling.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3150, 'core_configuration', 'sources/hooks/systems/config/check_broken_urls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3151, 'core_configuration', 'sources/hooks/systems/config/google_analytics.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3152, 'core_configuration', 'sources/hooks/systems/config/show_personal_sub_links.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3153, 'core_configuration', 'sources/hooks/systems/config/show_content_tagging.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3154, 'core_configuration', 'sources/hooks/systems/config/show_content_tagging_inline.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3155, 'core_configuration', 'sources/hooks/systems/config/show_screen_actions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3156, 'core_configuration', 'sources/hooks/systems/config/allow_audio_videos.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3157, 'core_configuration', 'sources/hooks/systems/config/allow_ext_images.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3158, 'core_configuration', 'sources/hooks/systems/config/allowed_post_submitters.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3159, 'core_configuration', 'sources/hooks/systems/config/anti_leech.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3160, 'core_configuration', 'sources/hooks/systems/config/auto_submit_sitemap.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3161, 'core_configuration', 'sources/hooks/systems/config/automatic_meta_extraction.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3162, 'core_configuration', 'sources/hooks/systems/config/bcc.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3163, 'core_configuration', 'sources/hooks/systems/config/bottom_show_feedback_link.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3164, 'core_configuration', 'sources/hooks/systems/config/autogrow.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3165, 'core_configuration', 'sources/hooks/systems/config/bottom_show_rules_link.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3166, 'core_configuration', 'sources/hooks/systems/config/bottom_show_privacy_link.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3167, 'core_configuration', 'sources/hooks/systems/config/bottom_show_sitemap_button.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3168, 'core_configuration', 'sources/hooks/systems/config/bottom_show_top_button.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3169, 'core_configuration', 'sources/hooks/systems/config/dkim_private_key.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3170, 'core_configuration', 'sources/hooks/systems/config/dkim_selector.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3171, 'core_configuration', 'sources/hooks/systems/config/crypt_ratchet.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3172, 'core_configuration', 'sources/hooks/systems/config/cc_address.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3173, 'core_configuration', 'sources/hooks/systems/config/security_token_exceptions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3174, 'core_configuration', 'sources/hooks/systems/config/closed.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3175, 'core_configuration', 'sources/hooks/systems/config/comment_text.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3176, 'core_configuration', 'sources/hooks/systems/config/comments_forum_name.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3177, 'core_configuration', 'sources/hooks/systems/config/copyright.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3178, 'core_configuration', 'sources/hooks/systems/config/deeper_admin_breadcrumbs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3179, 'core_configuration', 'sources/hooks/systems/config/description.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3180, 'core_configuration', 'sources/hooks/systems/config/detect_lang_browser.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3181, 'core_configuration', 'sources/hooks/systems/config/detect_lang_forum.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3182, 'core_configuration', 'sources/hooks/systems/config/display_php_errors.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3183, 'core_configuration', 'sources/hooks/systems/config/eager_wysiwyg.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3184, 'core_configuration', 'sources/hooks/systems/config/enable_keyword_density_check.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3185, 'core_configuration', 'sources/hooks/systems/config/enable_markup_webstandards.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3186, 'core_configuration', 'sources/hooks/systems/config/enable_previews.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3187, 'core_configuration', 'sources/hooks/systems/config/enable_spell_check.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3188, 'core_configuration', 'sources/hooks/systems/config/enveloper_override.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3189, 'core_configuration', 'sources/hooks/systems/config/force_meta_refresh.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3190, 'core_configuration', 'sources/hooks/systems/config/forum_in_portal.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3191, 'core_configuration', 'sources/hooks/systems/config/forum_show_personal_stats_posts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3192, 'core_configuration', 'sources/hooks/systems/config/forum_show_personal_stats_topics.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3193, 'core_configuration', 'sources/hooks/systems/config/global_donext_icons.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3194, 'core_configuration', 'sources/hooks/systems/config/gzip_output.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3195, 'core_configuration', 'sources/hooks/systems/config/has_low_memory_limit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3196, 'core_configuration', 'sources/hooks/systems/config/url_scheme.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3197, 'core_configuration', 'sources/hooks/systems/config/ip_forwarding.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3198, 'core_configuration', 'sources/hooks/systems/config/ip_strict_for_sessions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3199, 'core_configuration', 'sources/hooks/systems/config/is_on_emoticon_choosers.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3200, 'core_configuration', 'sources/hooks/systems/config/is_on_strong_forum_tie.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3201, 'core_configuration', 'sources/hooks/systems/config/keywords.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3202, 'core_configuration', 'sources/hooks/systems/config/dynamic_firewall.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3203, 'core_configuration', 'sources/hooks/systems/config/google_geocode_api_key.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3204, 'core_configuration', 'sources/hooks/systems/config/low_space_check.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3205, 'core_configuration', 'sources/hooks/systems/config/main_forum_name.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3206, 'core_configuration', 'sources/hooks/systems/config/max_download_size.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3207, 'core_configuration', 'sources/hooks/systems/config/maximum_users.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3208, 'core_configuration', 'sources/hooks/systems/config/stats_when_closed.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3209, 'core_configuration', 'sources/hooks/systems/config/cpf_enable_street_address.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3210, 'core_configuration', 'sources/hooks/systems/config/cpf_enable_city.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3211, 'core_configuration', 'sources/hooks/systems/config/cpf_enable_country.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3212, 'core_configuration', 'sources/hooks/systems/config/cpf_enable_name.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3213, 'core_configuration', 'sources/hooks/systems/config/cpf_enable_phone.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3214, 'core_configuration', 'sources/hooks/systems/config/cpf_enable_post_code.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3215, 'core_configuration', 'sources/hooks/systems/config/cpf_enable_county.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3216, 'core_configuration', 'sources/hooks/systems/config/cpf_enable_state.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3217, 'core_configuration', 'sources/hooks/systems/config/filter_regions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3218, 'core_configuration', 'sources/hooks/systems/config/cns_show_profile_link.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3219, 'core_configuration', 'sources/hooks/systems/config/show_avatar.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3220, 'core_configuration', 'sources/hooks/systems/config/show_conceded_mode_link.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3221, 'core_configuration', 'sources/hooks/systems/config/show_personal_adminzone_link.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3222, 'core_configuration', 'sources/hooks/systems/config/show_personal_last_visit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3223, 'core_configuration', 'sources/hooks/systems/config/show_personal_usergroup.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3224, 'core_configuration', 'sources/hooks/systems/config/show_staff_page_actions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3225, 'core_configuration', 'sources/hooks/systems/config/show_su.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3226, 'core_configuration', 'sources/hooks/systems/config/root_zone_login_theme.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3227, 'core_configuration', 'sources/hooks/systems/config/send_error_emails_ocproducts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3228, 'core_configuration', 'sources/hooks/systems/config/session_expiry_time.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3229, 'core_configuration', 'sources/hooks/systems/config/show_docs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3230, 'core_configuration', 'sources/hooks/systems/config/keyset_pagination.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3231, 'core_configuration', 'sources/hooks/systems/config/show_inline_stats.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3232, 'core_configuration', 'sources/hooks/systems/config/show_post_validation.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3233, 'core_configuration', 'sources/hooks/systems/config/simplified_donext.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3234, 'core_configuration', 'sources/hooks/systems/config/site_closed.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3235, 'core_configuration', 'sources/hooks/systems/config/site_name.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3236, 'core_configuration', 'sources/hooks/systems/config/site_scope.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3237, 'core_configuration', 'sources/hooks/systems/config/smtp_from_address.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3238, 'core_configuration', 'sources/hooks/systems/config/smtp_sockets_host.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3239, 'core_configuration', 'sources/hooks/systems/config/smtp_sockets_password.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3240, 'core_configuration', 'sources/hooks/systems/config/smtp_sockets_port.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3241, 'core_configuration', 'sources/hooks/systems/config/smtp_sockets_use.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3242, 'core_configuration', 'sources/hooks/systems/config/smtp_sockets_username.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3243, 'core_configuration', 'sources/hooks/systems/config/ssw.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3244, 'core_configuration', 'sources/hooks/systems/config/yeehaw.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3245, 'core_configuration', 'sources/hooks/systems/config/cookie_notice.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3246, 'core_configuration', 'sources/hooks/systems/config/staff_address.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3247, 'core_configuration', 'sources/hooks/systems/config/thumb_width.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3248, 'core_configuration', 'sources/hooks/systems/config/unzip_cmd.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3249, 'core_configuration', 'sources/hooks/systems/config/unzip_dir.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3250, 'core_configuration', 'sources/hooks/systems/config/use_contextual_dates.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3251, 'core_configuration', 'sources/hooks/systems/config/user_postsize_errors.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3252, 'core_configuration', 'sources/hooks/systems/config/users_online_time.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3253, 'core_configuration', 'sources/hooks/systems/config/valid_images.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3254, 'core_configuration', 'sources/hooks/systems/config/valid_videos.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3255, 'core_configuration', 'sources/hooks/systems/config/valid_audios.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3256, 'core_configuration', 'sources/hooks/systems/config/valid_types.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3257, 'core_configuration', 'sources/hooks/systems/config/website_email.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3258, 'core_configuration', 'sources/hooks/systems/config/long_google_cookies.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3259, 'core_configuration', 'sources/hooks/systems/config/detect_javascript.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3260, 'core_configuration', 'sources/hooks/systems/config/welcome_message.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3261, 'core_configuration', 'sources/hooks/systems/config/remember_me_by_default.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3262, 'core_configuration', 'sources/hooks/systems/config/mobile_support.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3263, 'core_configuration', 'sources/hooks/systems/config/complex_uploader.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3264, 'core_configuration', 'sources/hooks/systems/config/complex_lists.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3265, 'core_configuration', 'sources/hooks/systems/config/wysiwyg.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3266, 'core_configuration', 'sources/hooks/systems/config/editarea.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3267, 'core_configuration', 'sources/hooks/systems/config/autoban.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3268, 'core_configuration', 'sources/hooks/systems/config/js_overlays.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3269, 'core_configuration', 'sources/hooks/systems/config/likes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3270, 'core_configuration', 'sources/hooks/systems/config/tree_lists.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3271, 'core_configuration', 'sources/hooks/systems/config/lax_comcode.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3272, 'core_configuration', 'sources/hooks/systems/config/output_streaming.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3273, 'core_configuration', 'sources/hooks/systems/config/imap_folder.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3274, 'core_configuration', 'sources/hooks/systems/config/imap_host.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3275, 'core_configuration', 'sources/hooks/systems/config/imap_password.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3276, 'core_configuration', 'sources/hooks/systems/config/imap_port.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3277, 'core_configuration', 'sources/hooks/systems/config/imap_username.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3278, 'core_configuration', 'sources/hooks/systems/config/fractional_editing.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3279, 'core_configuration', 'sources/hooks/systems/addon_registry/core_configuration.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3280, 'core_configuration', 'themes/default/templates/CONFIG_CATEGORY_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3281, 'core_configuration', 'adminzone/pages/modules/admin_config.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3282, 'core_configuration', 'lang/EN/config.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3283, 'core_configuration', 'sources/hooks/systems/config/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3284, 'core_configuration', 'sources_custom/hooks/systems/config/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3285, 'core_configuration', 'sources/hooks/systems/config/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3286, 'core_configuration', 'sources_custom/hooks/systems/config/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3287, 'core_configuration', 'themes/default/templates/XML_CONFIG_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3288, 'core_database_drivers', 'sources/hooks/systems/addon_registry/core_database_drivers.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3289, 'core_database_drivers', 'sources/database/shared/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3290, 'core_database_drivers', 'sources/database/shared/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3291, 'core_database_drivers', 'sources/database/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3292, 'core_database_drivers', 'sources/database/access.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3293, 'core_database_drivers', 'sources/database/database.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3294, 'core_database_drivers', 'sources/database/ibm.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3295, 'core_database_drivers', 'sources/database/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3296, 'core_database_drivers', 'sources/database/mysql.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3297, 'core_database_drivers', 'sources/database/mysqli.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3298, 'core_database_drivers', 'sources/database/mysql_pdo.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3299, 'core_database_drivers', 'sources/database/mysql_dbx.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3300, 'core_database_drivers', 'sources/database/oracle.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3301, 'core_database_drivers', 'sources/database/postgresql.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3302, 'core_database_drivers', 'sources/database/xml.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3303, 'core_database_drivers', 'sources/database/shared/mysql.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3304, 'core_database_drivers', 'sources/database/sqlite.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3305, 'core_database_drivers', 'sources/database/shared/sqlserver.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3306, 'core_database_drivers', 'sources/database/sqlserver.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3307, 'core_database_drivers', 'sources/database/sqlserver_odbc.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3308, 'core_database_drivers', 'sources/hooks/systems/cron/oracle.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3309, 'core_feedback_features', 'themes/default/images/icons/24x24/feedback/comment.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3310, 'core_feedback_features', 'themes/default/images/icons/48x48/feedback/comment.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3311, 'core_feedback_features', 'themes/default/images/icons/24x24/feedback/comments_topic.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3312, 'core_feedback_features', 'themes/default/images/icons/48x48/feedback/comments_topic.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3313, 'core_feedback_features', 'themes/default/images/icons/24x24/feedback/rate.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3314, 'core_feedback_features', 'themes/default/images/icons/48x48/feedback/rate.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3315, 'core_feedback_features', 'themes/default/images/icons/24x24/menu/adminzone/audit/trackbacks.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3316, 'core_feedback_features', 'themes/default/images/icons/48x48/menu/adminzone/audit/trackbacks.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3317, 'core_feedback_features', 'sources/topics.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3318, 'core_feedback_features', 'sources/hooks/systems/notifications/like.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3319, 'core_feedback_features', 'sources/hooks/systems/notifications/comment_posted.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3320, 'core_feedback_features', 'themes/default/templates/TRACKBACK_DELETE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3321, 'core_feedback_features', 'sources/hooks/systems/page_groupings/trackbacks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3322, 'core_feedback_features', 'lang/EN/trackbacks.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3323, 'core_feedback_features', 'sources/hooks/systems/trackback/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3324, 'core_feedback_features', 'sources_custom/hooks/systems/trackback/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3325, 'core_feedback_features', 'sources/hooks/systems/trackback/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3326, 'core_feedback_features', 'sources_custom/hooks/systems/trackback/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3327, 'core_feedback_features', 'data/trackback.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3328, 'core_feedback_features', 'adminzone/pages/modules/admin_trackbacks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3329, 'core_feedback_features', 'sources/hooks/systems/addon_registry/core_feedback_features.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3330, 'core_feedback_features', 'sources/hooks/systems/snippets/rating.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3331, 'core_feedback_features', 'sources/hooks/systems/snippets/comments.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3332, 'core_feedback_features', 'sources/hooks/systems/preview/comments.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3333, 'core_feedback_features', 'themes/default/images/1x/like.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3334, 'core_feedback_features', 'themes/default/images/1x/dislike.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3335, 'core_feedback_features', 'themes/default/images/2x/like.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3336, 'core_feedback_features', 'themes/default/images/2x/dislike.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3337, 'core_feedback_features', 'sources/hooks/systems/rss/comments.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3338, 'core_feedback_features', 'themes/default/templates/COMMENTS_POSTING_FORM.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3339, 'core_feedback_features', 'themes/default/templates/COMMENTS_WRAPPER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3340, 'core_feedback_features', 'themes/default/templates/COMMENTS_DEFAULT_TEXT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3341, 'core_feedback_features', 'themes/default/templates/RATING_BOX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3342, 'core_feedback_features', 'themes/default/templates/RATING_INLINE_STATIC.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3343, 'core_feedback_features', 'themes/default/templates/RATING_INLINE_DYNAMIC.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3344, 'core_feedback_features', 'themes/default/templates/RATING_DISPLAY_SHARED.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3345, 'core_feedback_features', 'themes/default/templates/RATING_FORM.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3346, 'core_feedback_features', 'themes/default/templates/TRACKBACK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3347, 'core_feedback_features', 'themes/default/templates/TRACKBACK_WRAPPER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3348, 'core_feedback_features', 'themes/default/xml/TRACKBACK_XML.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3349, 'core_feedback_features', 'themes/default/xml/TRACKBACK_XML_ERROR.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3350, 'core_feedback_features', 'themes/default/xml/TRACKBACK_XML_LISTING.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3351, 'core_feedback_features', 'themes/default/xml/TRACKBACK_XML_NO_ERROR.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3352, 'core_feedback_features', 'themes/default/xml/TRACKBACK_XML_WRAPPER.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3353, 'core_feedback_features', 'sources/feedback.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3354, 'core_feedback_features', 'sources/feedback2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3355, 'core_feedback_features', 'pages/comcode/EN/feedback.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3356, 'core_feedback_features', 'sources/blocks/main_comments.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3357, 'core_feedback_features', 'sources/blocks/main_trackback.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3358, 'core_feedback_features', 'sources/blocks/main_rating.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3359, 'core_feedback_features', 'themes/default/templates/COMMENT_AJAX_HANDLER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3360, 'core_feedback_features', 'data/post_comment.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3361, 'core_feedback_features', 'sources/hooks/systems/config/max_thread_depth.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3362, 'core_feedback_features', 'sources/hooks/systems/config/comment_topic_subject.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3363, 'core_feedback_features', 'sources/hooks/systems/config/default_comment_sort_order.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3364, 'core_feedback_features', 'sources/hooks/systems/config/comments_to_show_in_thread.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3365, 'core_feedback_features', 'sources/hooks/systems/config/simplify_wysiwyg_by_permissions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3366, 'core_feedback_features', 'sources/hooks/systems/config/allow_own_rate.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3367, 'core_feedback_features', 'sources/hooks/systems/config/enable_feedback.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3368, 'core_feedback_features', 'sources/hooks/systems/config/is_on_comments.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3369, 'core_feedback_features', 'sources/hooks/systems/config/is_on_rating.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3370, 'core_feedback_features', 'sources/hooks/systems/config/is_on_trackbacks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3371, 'core_feedback_features', 'sources/hooks/systems/symbols/SHOW_RATINGS.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3372, 'core_feedback_features', 'themes/default/templates/RATINGS_SHOW.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3373, 'core_fields', 'lang/EN/fields.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3374, 'core_fields', 'sources/fields.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3375, 'core_fields', 'sources/hooks/systems/addon_registry/core_fields.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3376, 'core_fields', 'sources/hooks/systems/fields/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3377, 'core_fields', 'sources_custom/hooks/systems/fields/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3378, 'core_fields', 'sources/hooks/systems/fields/video.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3379, 'core_fields', 'sources/hooks/systems/fields/video_multi.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3380, 'core_fields', 'sources/hooks/systems/fields/content_link_multi.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3381, 'core_fields', 'sources/hooks/systems/fields/picture_multi.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3382, 'core_fields', 'sources/hooks/systems/fields/reference_multi.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3383, 'core_fields', 'sources/hooks/systems/fields/upload_multi.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3384, 'core_fields', 'sources/hooks/systems/fields/url_multi.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3385, 'core_fields', 'sources/hooks/systems/fields/page_link.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3386, 'core_fields', 'sources/hooks/systems/fields/date.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3387, 'core_fields', 'sources/hooks/systems/fields/email.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3388, 'core_fields', 'sources/hooks/systems/fields/float.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3389, 'core_fields', 'sources/hooks/systems/fields/guid.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3390, 'core_fields', 'sources/hooks/systems/fields/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3391, 'core_fields', 'sources_custom/hooks/systems/fields/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3392, 'core_fields', 'sources/hooks/systems/fields/integer.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3393, 'core_fields', 'sources/hooks/systems/fields/isbn.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3394, 'core_fields', 'sources/hooks/systems/fields/list.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3395, 'core_fields', 'sources/hooks/systems/fields/long_text.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3396, 'core_fields', 'sources/hooks/systems/fields/long_trans.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3397, 'core_fields', 'sources/hooks/systems/fields/picture.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3398, 'core_fields', 'sources/hooks/systems/fields/reference.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3399, 'core_fields', 'sources/hooks/systems/fields/short_text.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3400, 'core_fields', 'sources/hooks/systems/fields/short_trans.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3401, 'core_fields', 'sources/hooks/systems/fields/tick.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3402, 'core_fields', 'sources/hooks/systems/fields/upload.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3403, 'core_fields', 'sources/hooks/systems/fields/url.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3404, 'core_fields', 'sources/hooks/systems/fields/member.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3405, 'core_fields', 'sources/hooks/systems/fields/posting_field.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3406, 'core_fields', 'sources/hooks/systems/fields/codename.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3407, 'core_fields', 'sources/hooks/systems/fields/author.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3408, 'core_fields', 'sources/hooks/systems/fields/color.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3409, 'core_fields', 'sources/hooks/systems/fields/password.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3410, 'core_fields', 'sources/hooks/systems/fields/just_time.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3411, 'core_fields', 'sources/hooks/systems/fields/just_date.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3412, 'core_fields', 'sources/hooks/systems/fields/theme_image.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3413, 'core_fields', 'sources/hooks/systems/fields/content_link.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3414, 'core_fields', 'sources/hooks/systems/fields/short_text_multi.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3415, 'core_fields', 'sources/hooks/systems/fields/short_trans_multi.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3416, 'core_fields', 'sources/hooks/systems/fields/member_multi.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3417, 'core_fields', 'sources/hooks/systems/fields/list_multi.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3418, 'core_fields', 'themes/default/templates/CATALOGUE_DEFAULT_FIELD_MULTILIST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3419, 'core_fields', 'themes/default/templates/CATALOGUE_DEFAULT_FIELD_PICTURE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3420, 'core_fields', 'sources/hooks/systems/symbols/CATALOGUE_ENTRY_FOR.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3421, 'core_fields', 'site/catalogue_file.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3422, 'core_form_interfaces', 'themes/default/images/EN/1x/editor/off.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3423, 'core_form_interfaces', 'themes/default/images/EN/1x/editor/on.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3424, 'core_form_interfaces', 'themes/default/images/EN/2x/editor/off.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3425, 'core_form_interfaces', 'themes/default/images/EN/2x/editor/on.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3426, 'core_form_interfaces', 'themes/default/images/1x/treefield/category.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3427, 'core_form_interfaces', 'themes/default/images/1x/treefield/entry.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3428, 'core_form_interfaces', 'themes/default/images/2x/treefield/category.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3429, 'core_form_interfaces', 'themes/default/images/2x/treefield/entry.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3430, 'core_form_interfaces', 'themes/default/images/1x/treefield/collapse.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3431, 'core_form_interfaces', 'themes/default/images/1x/treefield/expand.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3432, 'core_form_interfaces', 'themes/default/images/1x/treefield/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3433, 'core_form_interfaces', 'themes/default/images/2x/treefield/collapse.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3434, 'core_form_interfaces', 'themes/default/images/2x/treefield/expand.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3435, 'core_form_interfaces', 'themes/default/images/2x/treefield/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3436, 'core_form_interfaces', 'sources/hooks/systems/addon_registry/core_form_interfaces.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3437, 'core_form_interfaces', 'themes/default/templates/POSTING_FORM.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3438, 'core_form_interfaces', 'themes/default/templates/POSTING_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3439, 'core_form_interfaces', 'themes/default/templates/WYSIWYG_LOAD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3440, 'core_form_interfaces', 'themes/default/javascript/posting.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3441, 'core_form_interfaces', 'themes/default/javascript/editing.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3442, 'core_form_interfaces', 'themes/default/javascript/WYSIWYG_SETTINGS.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3443, 'core_form_interfaces', 'themes/default/javascript/ATTACHMENT_UI_DEFAULTS.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3444, 'core_form_interfaces', 'themes/default/javascript/multi.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3445, 'core_form_interfaces', 'themes/default/javascript/checking.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3446, 'core_form_interfaces', 'themes/default/templates/FORM_FIELD_SET_GROUPER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3447, 'core_form_interfaces', 'themes/default/templates/FORM.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3448, 'core_form_interfaces', 'themes/default/templates/FORM_SINGLE_FIELD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3449, 'core_form_interfaces', 'themes/default/templates/FORM_DESCRIP_SEP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3450, 'core_form_interfaces', 'themes/default/templates/FORM_GROUP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3451, 'core_form_interfaces', 'themes/default/templates/FORM_GROUPED.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3452, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3453, 'core_form_interfaces', 'themes/default/templates/COMCODE_EDITOR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3454, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_FIELD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3455, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_FIELDS_SET_ITEM.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3456, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_FIELDS_SET.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3457, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_ALL_AND_NOT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3458, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_DATE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3459, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_FLOAT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3460, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_HIDDEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3461, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_HIDDEN_2.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3462, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_HUGE_COMCODE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3463, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_HUGE_LIST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3464, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_INTEGER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3465, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_DIMENSIONS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3466, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_LINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3467, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_URL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3468, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_CODENAME.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3469, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_LINE_MULTI.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3470, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_TEXT_MULTI.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3471, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_LIST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3472, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_LIST_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3473, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_LIST_GROUP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3474, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_MULTI_LIST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3475, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_HUGE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3476, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_PERMISSION_MATRIX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3477, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_PERMISSION_MATRIX_OUTER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3478, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_PERMISSION_OVERRIDE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3479, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_USERNAME_MULTI.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3480, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_PASSWORD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3481, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_RADIO_LIST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3482, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_RADIO_LIST_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3483, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_RADIO_LIST_COMBO_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3484, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_RADIO_LIST_ENTRY_PICTURE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3485, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_THEME_IMAGE_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3486, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_THEME_IMAGE_CATEGORY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3487, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_TEXT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3488, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_TICK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3489, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_TIME.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3490, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_TREE_LIST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3491, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_UPLOAD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3492, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_UPLOAD_MULTI.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3493, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_FIELD_SPACER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3494, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_AUTHOR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3495, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_COLOUR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3496, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_EMAIL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3497, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_PERMISSION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3498, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_PERMISSION_ADMIN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3499, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_USERNAME.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3500, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_VARIOUS_TICKS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3501, 'core_form_interfaces', 'themes/default/templates/FORM_STANDARD_END.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3502, 'core_form_interfaces', 'themes/default/templates/BLOCK_HELPER_DONE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3503, 'core_form_interfaces', 'themes/default/templates/BLOCK_HELPER_START.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3504, 'core_form_interfaces', 'themes/default/templates/BLOCK_HELPER_BLOCK_GROUP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3505, 'core_form_interfaces', 'themes/default/templates/BLOCK_HELPER_BLOCK_CHOICE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3506, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_INPUT_COMBO.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3507, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_FIELD_DESCRIPTION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3508, 'core_form_interfaces', 'themes/default/templates/FORM_SCREEN_ARE_REQUIRED.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3509, 'core_form_interfaces', 'data/block_helper.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3510, 'core_form_interfaces', 'themes/default/javascript/ajax_people_lists.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3511, 'core_form_interfaces', 'themes/default/templates/PREVIEW_SCRIPT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3512, 'core_form_interfaces', 'themes/default/templates/PREVIEW_SCRIPT_CODE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3513, 'core_form_interfaces', 'themes/default/templates/PREVIEW_SCRIPT_KEYWORD_DENSITY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3514, 'core_form_interfaces', 'themes/default/templates/PREVIEW_SCRIPT_SPELLING.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3515, 'core_form_interfaces', 'data/preview.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3516, 'core_form_interfaces', 'sources/preview.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3517, 'core_form_interfaces', 'data/ckeditor/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3518, 'core_form_interfaces', 'data/ckeditor/build-config.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3519, 'core_form_interfaces', 'data/ckeditor/ckeditor.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3520, 'core_form_interfaces', 'data/ckeditor/contents.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3521, 'core_form_interfaces', 'data/ckeditor/lang/af.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3522, 'core_form_interfaces', 'data/ckeditor/lang/ar.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3523, 'core_form_interfaces', 'data/ckeditor/lang/bg.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3524, 'core_form_interfaces', 'data/ckeditor/lang/bn.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3525, 'core_form_interfaces', 'data/ckeditor/lang/bs.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3526, 'core_form_interfaces', 'data/ckeditor/lang/ca.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3527, 'core_form_interfaces', 'data/ckeditor/lang/cs.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3528, 'core_form_interfaces', 'data/ckeditor/lang/cy.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3529, 'core_form_interfaces', 'data/ckeditor/lang/da.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3530, 'core_form_interfaces', 'data/ckeditor/lang/de.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3531, 'core_form_interfaces', 'data/ckeditor/lang/el.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3532, 'core_form_interfaces', 'data/ckeditor/lang/en-au.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3533, 'core_form_interfaces', 'data/ckeditor/lang/en-ca.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3534, 'core_form_interfaces', 'data/ckeditor/lang/en-gb.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3535, 'core_form_interfaces', 'data/ckeditor/lang/en.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3536, 'core_form_interfaces', 'data/ckeditor/lang/eo.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3537, 'core_form_interfaces', 'data/ckeditor/lang/es.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3538, 'core_form_interfaces', 'data/ckeditor/lang/et.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3539, 'core_form_interfaces', 'data/ckeditor/lang/eu.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3540, 'core_form_interfaces', 'data/ckeditor/lang/fa.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3541, 'core_form_interfaces', 'data/ckeditor/lang/fi.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3542, 'core_form_interfaces', 'data/ckeditor/lang/fo.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3543, 'core_form_interfaces', 'data/ckeditor/lang/fr-ca.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3544, 'core_form_interfaces', 'data/ckeditor/lang/fr.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3545, 'core_form_interfaces', 'data/ckeditor/lang/gl.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3546, 'core_form_interfaces', 'data/ckeditor/lang/gu.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3547, 'core_form_interfaces', 'data/ckeditor/lang/he.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3548, 'core_form_interfaces', 'data/ckeditor/lang/hi.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3549, 'core_form_interfaces', 'data/ckeditor/lang/hr.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3550, 'core_form_interfaces', 'data/ckeditor/lang/hu.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3551, 'core_form_interfaces', 'data/ckeditor/lang/id.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3552, 'core_form_interfaces', 'data/ckeditor/lang/is.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3553, 'core_form_interfaces', 'data/ckeditor/lang/it.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3554, 'core_form_interfaces', 'data/ckeditor/lang/ja.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3555, 'core_form_interfaces', 'data/ckeditor/lang/ka.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3556, 'core_form_interfaces', 'data/ckeditor/lang/km.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3557, 'core_form_interfaces', 'data/ckeditor/lang/ko.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3558, 'core_form_interfaces', 'data/ckeditor/lang/ku.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3559, 'core_form_interfaces', 'data/ckeditor/lang/lt.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3560, 'core_form_interfaces', 'data/ckeditor/lang/lv.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3561, 'core_form_interfaces', 'data/ckeditor/lang/mk.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3562, 'core_form_interfaces', 'data/ckeditor/lang/mn.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3563, 'core_form_interfaces', 'data/ckeditor/lang/ms.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3564, 'core_form_interfaces', 'data/ckeditor/lang/nb.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3565, 'core_form_interfaces', 'data/ckeditor/lang/nl.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3566, 'core_form_interfaces', 'data/ckeditor/lang/no.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3567, 'core_form_interfaces', 'data/ckeditor/lang/pl.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3568, 'core_form_interfaces', 'data/ckeditor/lang/pt-br.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3569, 'core_form_interfaces', 'data/ckeditor/lang/pt.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3570, 'core_form_interfaces', 'data/ckeditor/lang/ro.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3571, 'core_form_interfaces', 'data/ckeditor/lang/ru.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3572, 'core_form_interfaces', 'data/ckeditor/lang/si.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3573, 'core_form_interfaces', 'data/ckeditor/lang/sk.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3574, 'core_form_interfaces', 'data/ckeditor/lang/sl.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3575, 'core_form_interfaces', 'data/ckeditor/lang/sq.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3576, 'core_form_interfaces', 'data/ckeditor/lang/sr-latn.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3577, 'core_form_interfaces', 'data/ckeditor/lang/sr.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3578, 'core_form_interfaces', 'data/ckeditor/lang/sv.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3579, 'core_form_interfaces', 'data/ckeditor/lang/ta.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3580, 'core_form_interfaces', 'data/ckeditor/lang/th.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3581, 'core_form_interfaces', 'data/ckeditor/lang/tr.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3582, 'core_form_interfaces', 'data/ckeditor/lang/ug.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3583, 'core_form_interfaces', 'data/ckeditor/lang/uk.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3584, 'core_form_interfaces', 'data/ckeditor/lang/vi.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3585, 'core_form_interfaces', 'data/ckeditor/lang/zh-cn.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3586, 'core_form_interfaces', 'data/ckeditor/lang/zh.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3587, 'core_form_interfaces', 'data/ckeditor/LICENSE.md');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3588, 'core_form_interfaces', 'data/ckeditor/composr_patch.sh');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3589, 'core_form_interfaces', 'data/ckeditor/lang/tt.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3590, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/en-gb.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3591, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/tt.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3592, 'core_form_interfaces', 'data/ckeditor/plugins/magicline/images/hidpi/icon-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3593, 'core_form_interfaces', 'data/ckeditor/plugins/magicline/images/icon-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3594, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/en-gb.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3595, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/tt.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3596, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/a11yhelp.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3597, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/_translationstatus.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3598, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/ar.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3599, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/bg.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3600, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/ca.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3601, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/cs.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3602, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/cy.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3603, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/da.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3604, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/de.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3605, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/el.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3606, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/en.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3607, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/eo.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3608, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/es.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3609, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/et.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3610, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/fa.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3611, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/fi.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3612, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/fr-ca.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3613, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/fr.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3614, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/gl.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3615, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/gu.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3616, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/he.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3617, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/hi.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3618, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/hr.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3619, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/hu.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3620, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/id.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3621, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/it.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3622, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/ja.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3623, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/km.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3624, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/ko.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3625, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/ku.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3626, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/lt.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3627, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/lv.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3628, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/mk.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3629, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/mn.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3630, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/nb.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3631, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/nl.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3632, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/no.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3633, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/pl.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3634, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/pt-br.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3635, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/pt.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3636, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/ro.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3637, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/ru.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3638, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/si.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3639, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/sk.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3640, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/sl.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3641, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/sq.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3642, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/sr-latn.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3643, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/sr.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3644, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/sv.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3645, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/th.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3646, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/tr.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3647, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/ug.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3648, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/uk.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3649, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/vi.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3650, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/zh-cn.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3651, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/zh.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3652, 'core_form_interfaces', 'data/ckeditor/plugins/base64image/dialogs/base64image.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3653, 'core_form_interfaces', 'data/ckeditor/plugins/base64image/LICENSE.md');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3654, 'core_form_interfaces', 'data/ckeditor/plugins/base64image/README.md');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3655, 'core_form_interfaces', 'data/ckeditor/plugins/clipboard/dialogs/paste.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3656, 'core_form_interfaces', 'data/ckeditor/plugins/colordialog/dialogs/colordialog.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3657, 'core_form_interfaces', 'data/ckeditor/plugins/dialog/dialogDefinition.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3658, 'core_form_interfaces', 'data/ckeditor/plugins/find/dialogs/find.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3659, 'core_form_interfaces', 'data/ckeditor/plugins/icons.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3660, 'core_form_interfaces', 'data/ckeditor/plugins/icons_hidpi.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3661, 'core_form_interfaces', 'data/ckeditor/plugins/image/dialogs/image.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3662, 'core_form_interfaces', 'data/ckeditor/plugins/image/images/noimage.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3663, 'core_form_interfaces', 'data/ckeditor/plugins/link/dialogs/anchor.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3664, 'core_form_interfaces', 'data/ckeditor/plugins/link/dialogs/link.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3665, 'core_form_interfaces', 'data/ckeditor/plugins/link/images/anchor.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3666, 'core_form_interfaces', 'data/ckeditor/plugins/link/images/hidpi/anchor.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3667, 'core_form_interfaces', 'data/ckeditor/plugins/liststyle/dialogs/liststyle.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3668, 'core_form_interfaces', 'data/ckeditor/plugins/magicline/images/hidpi/icon.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3669, 'core_form_interfaces', 'data/ckeditor/plugins/magicline/images/icon.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3670, 'core_form_interfaces', 'data/ckeditor/plugins/showcomcodeblocks/plugin.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3671, 'core_form_interfaces', 'data/ckeditor/plugins/composr/images/composr_block.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3672, 'core_form_interfaces', 'data/ckeditor/plugins/composr/images/composr_box.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3673, 'core_form_interfaces', 'data/ckeditor/plugins/composr/images/composr_code.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3674, 'core_form_interfaces', 'data/ckeditor/plugins/composr/images/composr_comcode.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3675, 'core_form_interfaces', 'data/ckeditor/plugins/composr/images/composr_page.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3676, 'core_form_interfaces', 'data/ckeditor/plugins/composr/images/composr_quote.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3677, 'core_form_interfaces', 'data/ckeditor/plugins/composr/images/composr_image.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3678, 'core_form_interfaces', 'data/ckeditor/plugins/composr/images/composr_image__moono.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3679, 'core_form_interfaces', 'data/ckeditor/plugins/composr/images/hidpi/composr_block.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3680, 'core_form_interfaces', 'data/ckeditor/plugins/composr/images/hidpi/composr_box.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3681, 'core_form_interfaces', 'data/ckeditor/plugins/composr/images/hidpi/composr_code.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3682, 'core_form_interfaces', 'data/ckeditor/plugins/composr/images/hidpi/composr_comcode.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3683, 'core_form_interfaces', 'data/ckeditor/plugins/composr/images/hidpi/composr_image.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3684, 'core_form_interfaces', 'data/ckeditor/plugins/composr/images/hidpi/composr_image__moono.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3685, 'core_form_interfaces', 'data/ckeditor/plugins/composr/images/hidpi/composr_page.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3686, 'core_form_interfaces', 'data/ckeditor/plugins/composr/images/hidpi/composr_quote.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3687, 'core_form_interfaces', 'data/ckeditor/plugins/spellchecktoggle/images/hidpi/spellchecktoggle.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3688, 'core_form_interfaces', 'data/ckeditor/plugins/spellchecktoggle/images/hidpi/spellchecktoggle__moono.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3689, 'core_form_interfaces', 'data/ckeditor/plugins/spellchecktoggle/images/spellchecktoggle__moono.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3690, 'core_form_interfaces', 'data/ckeditor/plugins/composr/plugin.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3691, 'core_form_interfaces', 'data/ckeditor/plugins/spellchecktoggle/plugin.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3692, 'core_form_interfaces', 'data/ckeditor/plugins/spellchecktoggle/images/spellchecktoggle.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3693, 'core_form_interfaces', 'data/ckeditor/plugins/imagepaste/plugin.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3694, 'core_form_interfaces', 'data/ckeditor/plugins/pastefromword/filter/default.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3695, 'core_form_interfaces', 'data/ckeditor/plugins/showblocks/images/block_address.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3696, 'core_form_interfaces', 'data/ckeditor/plugins/showblocks/images/block_blockquote.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3697, 'core_form_interfaces', 'data/ckeditor/plugins/showblocks/images/block_div.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3698, 'core_form_interfaces', 'data/ckeditor/plugins/showblocks/images/block_h1.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3699, 'core_form_interfaces', 'data/ckeditor/plugins/showblocks/images/block_h2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3700, 'core_form_interfaces', 'data/ckeditor/plugins/showblocks/images/block_h3.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3701, 'core_form_interfaces', 'data/ckeditor/plugins/showblocks/images/block_h4.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3702, 'core_form_interfaces', 'data/ckeditor/plugins/showblocks/images/block_h5.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3703, 'core_form_interfaces', 'data/ckeditor/plugins/showblocks/images/block_h6.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3704, 'core_form_interfaces', 'data/ckeditor/plugins/showblocks/images/block_p.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3705, 'core_form_interfaces', 'data/ckeditor/plugins/showblocks/images/block_pre.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3706, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/_translationstatus.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3707, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/ar.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3708, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/bg.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3709, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/ca.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3710, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/cs.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3711, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/cy.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3712, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/de.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3713, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/el.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3714, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/en.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3715, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/eo.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3716, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/es.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3717, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/et.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3718, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/fa.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3719, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/fi.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3720, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/fr-ca.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3721, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/fr.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3722, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/gl.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3723, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/he.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3724, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/hr.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3725, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/hu.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3726, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/id.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3727, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/it.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3728, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/ja.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3729, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/km.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3730, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/ku.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3731, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/lv.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3732, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/nb.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3733, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/nl.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3734, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/no.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3735, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/pl.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3736, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/pt-br.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3737, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/pt.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3738, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/ru.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3739, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/si.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3740, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/sk.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3741, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/sl.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3742, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/sq.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3743, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/sv.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3744, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/th.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3745, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/tr.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3746, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/ug.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3747, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/uk.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3748, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/vi.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3749, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/zh-cn.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3750, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/zh.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3751, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/specialchar.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3752, 'core_form_interfaces', 'data/ckeditor/lang/az.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3753, 'core_form_interfaces', 'data/ckeditor/lang/de-ch.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3754, 'core_form_interfaces', 'data/ckeditor/lang/es-mx.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3755, 'core_form_interfaces', 'data/ckeditor/lang/oc.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3756, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/az.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3757, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/de-ch.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3758, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/en-au.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3759, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/es-mx.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3760, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/oc.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3761, 'core_form_interfaces', 'data/ckeditor/plugins/colordialog/dialogs/colordialog.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3762, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/az.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3763, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/de-ch.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3764, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/en-au.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3765, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/en-ca.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3766, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/es-mx.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3767, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/oc.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3768, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/ro.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3769, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/showblocks.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3770, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/smiley.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3771, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/source-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3772, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/source.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3773, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/specialchar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3774, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/spellchecker.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3775, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/strike.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3776, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/subscript.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3777, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/superscript.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3778, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/table.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3779, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/templates-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3780, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/templates.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3781, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/textarea-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3782, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/textarea.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3783, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/textcolor.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3784, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/textfield-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3785, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/textfield.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3786, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/underline.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3787, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/undo-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3788, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/undo.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3789, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/unlink.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3790, 'core_form_interfaces', 'data/ckeditor/skins/moono/icons.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3791, 'core_form_interfaces', 'data/ckeditor/skins/moono/icons_hidpi.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3792, 'core_form_interfaces', 'data/ckeditor/skins/moono/images/anchor.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3793, 'core_form_interfaces', 'data/ckeditor/skins/moono/images/hidpi/anchor.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3794, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/dialog_opera.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3795, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3796, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/images/mini.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3797, 'core_form_interfaces', 'data/ckeditor/plugins/table/dialogs/table.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3798, 'core_form_interfaces', 'data/ckeditor/plugins/tabletools/dialogs/tableCell.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3799, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/about.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3800, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/anchor-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3801, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/anchor.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3802, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/bgcolor.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3803, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/bidiltr.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3804, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/bidirtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3805, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/blockquote.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3806, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/bold.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3807, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/bulletedlist-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3808, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/bulletedlist.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3809, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/button.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3810, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/checkbox.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3811, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/copy-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3812, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/copy.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3813, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/creatediv.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3814, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/cut-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3815, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/cut.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3816, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/find-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3817, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/find.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3818, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/flash.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3819, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/form.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3820, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/hiddenfield.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3821, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/horizontalrule.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3822, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/iframe.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3823, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/image.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3824, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/imagebutton.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3825, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/indent-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3826, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/indent.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3827, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/italic.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3828, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/justifyblock.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3829, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/justifycenter.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3830, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/justifyleft.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3831, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/justifyright.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3832, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/link.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3833, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/maximize.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3834, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/newpage-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3835, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/newpage.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3836, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/numberedlist-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3837, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/numberedlist.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3838, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/outdent-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3839, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/outdent.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3840, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/pagebreak-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3841, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/pagebreak.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3842, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/paste-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3843, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/paste.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3844, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/pastefromword-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3845, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/pastefromword.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3846, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/pastetext-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3847, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/pastetext.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3848, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/preview-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3849, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/preview.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3850, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/print.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3851, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/radio.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3852, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/redo-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3853, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/redo.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3854, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/removeformat.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3855, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/replace.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3856, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/save.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3857, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/scayt.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3858, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/select-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3859, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/select.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3860, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/selectall.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3861, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons/showblocks-rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3862, 'core_form_interfaces', 'data/ckeditor/skins/moono/dev/icons16.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3863, 'core_form_interfaces', 'data/ckeditor/skins/moono/dev/icons16.svg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3864, 'core_form_interfaces', 'data/ckeditor/skins/moono/dev/icons32.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3865, 'core_form_interfaces', 'data/ckeditor/skins/moono/dev/icons32.svg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3866, 'core_form_interfaces', 'data/ckeditor/skins/moono/dev/locations.json');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3867, 'core_form_interfaces', 'data/ckeditor/skins/moono/dialog.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3868, 'core_form_interfaces', 'data/ckeditor/skins/moono/dialog_ie.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3869, 'core_form_interfaces', 'data/ckeditor/skins/moono/dialog_ie7.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3870, 'core_form_interfaces', 'data/ckeditor/skins/moono/dialog_ie8.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3871, 'core_form_interfaces', 'data/ckeditor/skins/moono/dialog_iequirks.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3872, 'core_form_interfaces', 'data/ckeditor/skins/moono/editor.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3873, 'core_form_interfaces', 'data/ckeditor/skins/moono/editor_gecko.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3874, 'core_form_interfaces', 'data/ckeditor/skins/moono/editor_ie.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3875, 'core_form_interfaces', 'data/ckeditor/skins/moono/editor_ie7.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3876, 'core_form_interfaces', 'data/ckeditor/skins/moono/editor_ie8.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3877, 'core_form_interfaces', 'data/ckeditor/skins/moono/editor_iequirks.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3878, 'core_form_interfaces', 'data/ckeditor/skins/moono/images/arrow.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3879, 'core_form_interfaces', 'data/ckeditor/skins/moono/images/close.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3880, 'core_form_interfaces', 'data/ckeditor/skins/moono/images/hidpi/close.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3881, 'core_form_interfaces', 'data/ckeditor/skins/moono/images/hidpi/lock-open.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3882, 'core_form_interfaces', 'data/ckeditor/skins/moono/images/hidpi/lock.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3883, 'core_form_interfaces', 'data/ckeditor/skins/moono/images/hidpi/refresh.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3884, 'core_form_interfaces', 'data/ckeditor/skins/moono/images/lock-open.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3885, 'core_form_interfaces', 'data/ckeditor/skins/moono/images/lock.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3886, 'core_form_interfaces', 'data/ckeditor/skins/moono/images/refresh.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3887, 'core_form_interfaces', 'data/ckeditor/skins/moono/readme.md');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3888, 'core_form_interfaces', 'data/ckeditor/skins/moono/skin.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3889, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/dialog.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3890, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/dialog_ie.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3891, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/dialog_ie7.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3892, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/dialog_ie8.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3893, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/dialog_iequirks.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3894, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/editor.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3895, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/editor_gecko.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3896, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/editor_ie.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3897, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/editor_ie7.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3898, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/editor_ie8.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3899, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/editor_iequirks.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3900, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/icons.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3901, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/images/arrow.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3902, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/images/close.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3903, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/readme.md');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3904, 'core_form_interfaces', 'data/ckeditor/skins/moonocolor/skin.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3905, 'core_form_interfaces', 'data/ckeditor/skins/kama/dialog.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3906, 'core_form_interfaces', 'data/ckeditor/skins/kama/dialog_ie.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3907, 'core_form_interfaces', 'data/ckeditor/skins/kama/dialog_ie7.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3908, 'core_form_interfaces', 'data/ckeditor/skins/kama/dialog_ie8.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3909, 'core_form_interfaces', 'data/ckeditor/skins/kama/dialog_iequirks.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3910, 'core_form_interfaces', 'data/ckeditor/skins/kama/editor.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3911, 'core_form_interfaces', 'data/ckeditor/skins/kama/editor_ie.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3912, 'core_form_interfaces', 'data/ckeditor/skins/kama/editor_ie7.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3913, 'core_form_interfaces', 'data/ckeditor/skins/kama/editor_ie8.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3914, 'core_form_interfaces', 'data/ckeditor/skins/kama/editor_iequirks.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3915, 'core_form_interfaces', 'data/ckeditor/skins/kama/icons.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3916, 'core_form_interfaces', 'data/ckeditor/skins/kama/icons_hidpi.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3917, 'core_form_interfaces', 'data/ckeditor/skins/kama/images/dialog_sides.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3918, 'core_form_interfaces', 'data/ckeditor/skins/kama/images/dialog_sides.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3919, 'core_form_interfaces', 'data/ckeditor/skins/kama/images/dialog_sides_rtl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3920, 'core_form_interfaces', 'data/ckeditor/skins/kama/images/mini.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3921, 'core_form_interfaces', 'data/ckeditor/skins/kama/images/sprites.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3922, 'core_form_interfaces', 'data/ckeditor/skins/kama/images/sprites_ie6.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3923, 'core_form_interfaces', 'data/ckeditor/skins/kama/images/toolbar_start.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3924, 'core_form_interfaces', 'data/ckeditor/skins/kama/readme.md');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3925, 'core_form_interfaces', 'data/ckeditor/styles.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3926, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/af.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3927, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/eu.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3928, 'core_form_interfaces', 'data/ckeditor/plugins/a11yhelp/dialogs/lang/fo.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3929, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/af.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3930, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/da.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3931, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/eu.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3932, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/ko.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3933, 'core_form_interfaces', 'data/ckeditor/plugins/specialchar/dialogs/lang/lt.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3934, 'core_form_interfaces', 'data/ckeditor/skins/kama/images/spinner.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3935, 'core_form_interfaces', 'data/ckeditor/skins/moono/images/spinner.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3936, 'core_form_interfaces', 'data/ckeditor/plugins/image2/dialogs/image2.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3937, 'core_form_interfaces', 'data/ckeditor/plugins/imageresponsive/README.md');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3938, 'core_form_interfaces', 'data/ckeditor/plugins/pastecode/CHANGES.md');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3939, 'core_form_interfaces', 'data/ckeditor/plugins/widget/images/handle.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3940, 'core_form_interfaces', 'sources/hooks/systems/symbols/CKEDITOR_PATH.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3941, 'core_form_interfaces', 'sources/hooks/systems/symbols/COMCODE_TAGS.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3942, 'core_form_interfaces', 'themes/default/css/widget_date.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3943, 'core_form_interfaces', 'themes/default/javascript/widget_date.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3944, 'core_form_interfaces', 'themes/default/css/widget_color.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3945, 'core_form_interfaces', 'themes/default/javascript/widget_color.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3946, 'core_form_interfaces', 'themes/default/css/widget_select2.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3947, 'core_form_interfaces', 'themes/default/images/select2/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3948, 'core_form_interfaces', 'themes/default/images/select2/select2-spinner.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3949, 'core_form_interfaces', 'themes/default/images/select2/select2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3950, 'core_form_interfaces', 'themes/default/images/select2/select2x2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3951, 'core_form_interfaces', 'themes/default/javascript/select2.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3952, 'core_form_interfaces', 'themes/default/css/forms.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3953, 'core_form_interfaces', 'sources/spelling.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3954, 'core_form_interfaces', 'data_custom/spelling/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3955, 'core_form_interfaces', 'data_custom/spelling/personal_dicts/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3956, 'core_form_interfaces', 'data_custom/spelling/personal_dicts/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3957, 'core_form_interfaces', 'themes/default/images/EN/comcodeeditor/apply_changes.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3958, 'core_form_interfaces', 'themes/default/images/EN/comcodeeditor/b.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3959, 'core_form_interfaces', 'themes/default/images/EN/comcodeeditor/quote.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3960, 'core_form_interfaces', 'themes/default/images/EN/comcodeeditor/block.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3961, 'core_form_interfaces', 'themes/default/images/EN/comcodeeditor/box.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3962, 'core_form_interfaces', 'themes/default/images/EN/comcodeeditor/code.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3963, 'core_form_interfaces', 'themes/default/images/EN/comcodeeditor/comcode.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3964, 'core_form_interfaces', 'themes/default/images/EN/comcodeeditor/email.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3965, 'core_form_interfaces', 'themes/default/images/EN/comcodeeditor/hide.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3966, 'core_form_interfaces', 'themes/default/images/EN/comcodeeditor/html.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3967, 'core_form_interfaces', 'themes/default/images/EN/comcodeeditor/i.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3968, 'core_form_interfaces', 'themes/default/images/EN/comcodeeditor/img.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3969, 'core_form_interfaces', 'themes/default/images/EN/comcodeeditor/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3970, 'core_form_interfaces', 'themes/default/images/EN/comcodeeditor/list.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3971, 'core_form_interfaces', 'themes/default/images/EN/comcodeeditor/page.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3972, 'core_form_interfaces', 'themes/default/images/EN/comcodeeditor/s.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3973, 'core_form_interfaces', 'themes/default/images/EN/comcodeeditor/thumb.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3974, 'core_form_interfaces', 'themes/default/images/EN/comcodeeditor/u.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3975, 'core_form_interfaces', 'themes/default/images/EN/comcodeeditor/url.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3976, 'core_form_interfaces', 'sources/form_templates.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3977, 'core_form_interfaces', 'data/namelike.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3978, 'core_form_interfaces', 'data/username_exists.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3979, 'core_forum_drivers', 'sources/hooks/systems/addon_registry/core_forum_drivers.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3980, 'core_forum_drivers', 'sources/forum/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3981, 'core_forum_drivers', 'sources/forum/forums.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3982, 'core_forum_drivers', 'sources/forum/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3983, 'core_forum_drivers', 'sources/forum/none.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3984, 'core_forum_drivers', 'sources/forum/ipb1.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3985, 'core_forum_drivers', 'sources/forum/ipb2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3986, 'core_forum_drivers', 'sources/forum/ipb3.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3987, 'core_forum_drivers', 'sources/forum/phpbb2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3988, 'core_forum_drivers', 'sources/forum/phpbb3.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3989, 'core_forum_drivers', 'sources/forum/shared/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3990, 'core_forum_drivers', 'sources/forum/shared/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3991, 'core_forum_drivers', 'sources/forum/shared/ipb.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3992, 'core_forum_drivers', 'sources/forum/shared/vb.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3993, 'core_forum_drivers', 'sources/forum/shared/wbb.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3994, 'core_forum_drivers', 'sources/forum/smf.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3995, 'core_forum_drivers', 'sources/forum/smf2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3996, 'core_forum_drivers', 'sources/forum/vb22.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3997, 'core_forum_drivers', 'sources/forum/vb3.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3998, 'core_forum_drivers', 'sources/forum/wbb2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (3999, 'core_forum_drivers', 'sources/forum/wbb22.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4000, 'core_forum_drivers', 'sources/forum/wowbb.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4001, 'core_forum_drivers', 'sources/forum/aef.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4002, 'core_forum_drivers', 'sources/forum/mybb.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4003, 'core_forum_drivers', 'sources_custom/forum/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4004, 'core_forum_drivers', 'sources_custom/forum/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4005, 'core_graphic_text', 'sources/hooks/systems/addon_registry/core_graphic_text.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4006, 'core_graphic_text', 'sources/fonts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4007, 'core_graphic_text', 'themes/default/css/fonts.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4008, 'core_graphic_text', 'data/fonts/FreeMono.ttf');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4009, 'core_graphic_text', 'data/fonts/FreeMonoBold.ttf');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4010, 'core_graphic_text', 'data/fonts/FreeMonoBoldOblique.ttf');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4011, 'core_graphic_text', 'data/fonts/FreeMonoOblique.ttf');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4012, 'core_graphic_text', 'data/fonts/FreeSans.ttf');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4013, 'core_graphic_text', 'data/fonts/FreeSansBold.ttf');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4014, 'core_graphic_text', 'data/fonts/FreeSansBoldOblique.ttf');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4015, 'core_graphic_text', 'data/fonts/FreeSansOblique.ttf');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4016, 'core_graphic_text', 'data/fonts/FreeSerif.ttf');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4017, 'core_graphic_text', 'data/fonts/FreeSerifBold.ttf');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4018, 'core_graphic_text', 'data/fonts/FreeSerifBoldItalic.ttf');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4019, 'core_graphic_text', 'data/fonts/FreeSerifItalic.ttf');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4020, 'core_graphic_text', 'data/fonts/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4021, 'core_graphic_text', 'data/gd_text.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4022, 'core_graphic_text', 'data_custom/fonts/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4023, 'core_html_abstractions', 'sources/hooks/systems/addon_registry/core_html_abstractions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4024, 'core_html_abstractions', 'themes/default/templates/JS_REFRESH.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4025, 'core_html_abstractions', 'themes/default/templates/META_REFRESH_LINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4026, 'core_html_abstractions', 'themes/default/templates/ANCHOR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4027, 'core_html_abstractions', 'themes/default/templates/HYPERLINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4028, 'core_html_abstractions', 'themes/default/templates/HYPERLINK_POPUP_WINDOW.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4029, 'core_html_abstractions', 'themes/default/templates/HYPERLINK_TOOLTIP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4030, 'core_html_abstractions', 'themes/default/templates/HYPERLINK_BUTTON.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4031, 'core_html_abstractions', 'themes/default/templates/HYPERLINK_EMAIL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4032, 'core_html_abstractions', 'themes/default/templates/DIV.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4033, 'core_html_abstractions', 'themes/default/templates/SPAN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4034, 'core_html_abstractions', 'themes/default/templates/PARAGRAPH.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4035, 'core_html_abstractions', 'themes/default/templates/FLOATER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4036, 'core_html_abstractions', 'themes/default/templates/BASIC_HTML_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4037, 'core_html_abstractions', 'themes/default/templates/STANDALONE_HTML_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4038, 'core_html_abstractions', 'themes/default/templates/HTML_HEAD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4039, 'core_html_abstractions', 'themes/default/templates/POOR_XHTML_WRAPPER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4040, 'core_html_abstractions', 'themes/default/templates/WITH_WHITESPACE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4041, 'core_language_editing', 'themes/default/images/icons/24x24/menu/adminzone/style/language/language.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4042, 'core_language_editing', 'themes/default/images/icons/48x48/menu/adminzone/style/language/language.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4043, 'core_language_editing', 'themes/default/images/icons/24x24/menu/adminzone/style/language/language_content.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4044, 'core_language_editing', 'themes/default/images/icons/48x48/menu/adminzone/style/language/language_content.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4045, 'core_language_editing', 'themes/default/images/icons/24x24/menu/adminzone/style/language/criticise_language.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4046, 'core_language_editing', 'themes/default/images/icons/48x48/menu/adminzone/style/language/criticise_language.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4047, 'core_language_editing', 'themes/default/images/icons/24x24/menu/adminzone/style/language/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4048, 'core_language_editing', 'themes/default/images/icons/48x48/menu/adminzone/style/language/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4049, 'core_language_editing', 'themes/default/css/translations_editor.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4050, 'core_language_editing', 'sources/hooks/systems/addon_registry/core_language_editing.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4051, 'core_language_editing', 'sources/hooks/blocks/main_staff_checklist/translations.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4052, 'core_language_editing', 'themes/default/javascript/translate.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4053, 'core_language_editing', 'themes/default/templates/TRANSLATE_ACTION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4054, 'core_language_editing', 'themes/default/templates/TRANSLATE_LINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4055, 'core_language_editing', 'themes/default/templates/TRANSLATE_LINE_CONTENT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4056, 'core_language_editing', 'themes/default/templates/TRANSLATE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4057, 'core_language_editing', 'themes/default/templates/TRANSLATE_SCREEN_CONTENT_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4058, 'core_language_editing', 'themes/default/templates/TRANSLATE_LANGUAGE_CRITICISE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4059, 'core_language_editing', 'themes/default/templates/TRANSLATE_LANGUAGE_CRITICISE_FILE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4060, 'core_language_editing', 'themes/default/templates/TRANSLATE_LANGUAGE_CRITICISM.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4061, 'core_language_editing', 'adminzone/pages/modules/admin_lang.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4062, 'core_language_editing', 'sources/hooks/systems/page_groupings/language.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4063, 'core_language_editing', 'themes/default/images/icons/14x14/translate.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4064, 'core_language_editing', 'themes/default/images/icons/28x28/translate.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4065, 'core_language_editing', 'sources/hooks/systems/config/google_translate_api_key.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4066, 'core_language_editing', 'sources/database_multi_lang_conv.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4067, 'core_menus', 'themes/default/images/icons/24x24/menu/adminzone/structure/menus.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4068, 'core_menus', 'themes/default/images/icons/48x48/menu/adminzone/structure/menus.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4069, 'core_menus', 'sources/hooks/systems/resource_meta_aware/menu.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4070, 'core_menus', 'sources/hooks/systems/resource_meta_aware/menu_item.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4071, 'core_menus', 'sources/hooks/systems/commandr_fs/menus.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4072, 'core_menus', 'themes/default/css/menu_editor.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4073, 'core_menus', 'sources/hooks/systems/addon_registry/core_menus.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4074, 'core_menus', 'themes/admin/templates/MENU_mobile.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4075, 'core_menus', 'themes/default/images/mobile_menu.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4076, 'core_menus', 'themes/default/css/menu__mobile.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4077, 'core_menus', 'themes/default/css/menu__sitemap.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4078, 'core_menus', 'themes/default/css/menu__dropdown.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4079, 'core_menus', 'themes/default/css/menu__popup.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4080, 'core_menus', 'themes/default/css/menu__embossed.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4081, 'core_menus', 'themes/default/css/menu__select.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4082, 'core_menus', 'themes/default/css/menu__tree.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4083, 'core_menus', 'themes/admin/css/menu__dropdown.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4084, 'core_menus', 'themes/admin/templates/MENU_BRANCH_dropdown.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4085, 'core_menus', 'themes/admin/templates/MENU_dropdown.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4086, 'core_menus', 'themes/default/templates/MENU_dropdown.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4087, 'core_menus', 'themes/default/templates/MENU_embossed.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4088, 'core_menus', 'themes/default/templates/MENU_popup.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4089, 'core_menus', 'themes/default/templates/MENU_select.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4090, 'core_menus', 'themes/default/templates/MENU_sitemap.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4091, 'core_menus', 'themes/default/templates/MENU_tree.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4092, 'core_menus', 'themes/default/templates/MENU_mobile.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4093, 'core_menus', 'themes/default/templates/MENU_BRANCH_dropdown.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4094, 'core_menus', 'themes/default/templates/MENU_BRANCH_embossed.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4095, 'core_menus', 'themes/default/templates/MENU_BRANCH_popup.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4096, 'core_menus', 'themes/default/templates/MENU_BRANCH_select.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4097, 'core_menus', 'themes/default/templates/MENU_BRANCH_sitemap.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4098, 'core_menus', 'themes/default/templates/MENU_BRANCH_tree.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4099, 'core_menus', 'themes/default/templates/MENU_BRANCH_mobile.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4100, 'core_menus', 'themes/default/templates/MENU_SPACER_dropdown.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4101, 'core_menus', 'themes/default/templates/MENU_SPACER_embossed.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4102, 'core_menus', 'themes/default/templates/MENU_SPACER_popup.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4103, 'core_menus', 'themes/default/templates/MENU_SPACER_select.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4104, 'core_menus', 'themes/default/templates/MENU_SPACER_sitemap.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4105, 'core_menus', 'themes/default/templates/MENU_SPACER_tree.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4106, 'core_menus', 'themes/default/templates/MENU_SPACER_mobile.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4107, 'core_menus', 'themes/default/javascript/menu_popup.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4108, 'core_menus', 'themes/default/javascript/menu_sitemap.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4109, 'core_menus', 'themes/default/templates/MENU_STAFF_LINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4110, 'core_menus', 'themes/default/templates/MENU_EDITOR_BRANCH.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4111, 'core_menus', 'themes/default/templates/MENU_EDITOR_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4112, 'core_menus', 'themes/default/templates/MENU_EDITOR_BRANCH_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4113, 'core_menus', 'themes/default/javascript/menu_editor.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4114, 'core_menus', 'themes/default/templates/BLOCK_MENU.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4115, 'core_menus', 'themes/default/templates/MENU_LINK_PROPERTIES.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4116, 'core_menus', 'adminzone/pages/modules/admin_menus.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4117, 'core_menus', 'adminzone/menu_management.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4118, 'core_menus', 'themes/default/images/1x/menus/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4119, 'core_menus', 'themes/default/images/1x/menus/menu.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4120, 'core_menus', 'themes/default/images/1x/menus/menu_bullet.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4121, 'core_menus', 'themes/default/images/1x/menus/menu_bullet_hover.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4122, 'core_menus', 'themes/default/images/1x/menus/menu_bullet_expand.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4123, 'core_menus', 'themes/default/images/1x/menus/menu_bullet_expand_hover.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4124, 'core_menus', 'themes/default/images/1x/menus/menu_bullet_current.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4125, 'core_menus', 'themes/default/images/2x/menus/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4126, 'core_menus', 'themes/default/images/2x/menus/menu.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4127, 'core_menus', 'themes/default/images/2x/menus/menu_bullet.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4128, 'core_menus', 'themes/default/images/2x/menus/menu_bullet_hover.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4129, 'core_menus', 'themes/default/images/2x/menus/menu_bullet_expand.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4130, 'core_menus', 'themes/default/images/2x/menus/menu_bullet_expand_hover.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4131, 'core_menus', 'themes/default/images/2x/menus/menu_bullet_current.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4132, 'core_menus', 'lang/EN/menus.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4133, 'core_menus', 'sources/blocks/menu.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4134, 'core_menus', 'sources/hooks/systems/snippets/management_menu.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4135, 'core_menus', 'sources/menus.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4136, 'core_menus', 'sources/menus_comcode.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4137, 'core_menus', 'sources/menus2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4138, 'core_menus', 'themes/default/templates/PAGE_LINK_CHOOSER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4139, 'core_menus', 'data/page_link_chooser.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4140, 'core_menus', 'sources/hooks/systems/preview/menu.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4141, 'core_notifications', 'themes/default/images/icons/24x24/tool_buttons/notifications.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4142, 'core_notifications', 'themes/default/images/icons/48x48/tool_buttons/notifications.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4143, 'core_notifications', 'themes/default/images/icons/24x24/tool_buttons/notifications2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4144, 'core_notifications', 'themes/default/images/icons/48x48/tool_buttons/notifications2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4145, 'core_notifications', 'themes/default/images/icons/24x24/menu/adminzone/setup/notifications.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4146, 'core_notifications', 'themes/default/images/icons/48x48/menu/adminzone/setup/notifications.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4147, 'core_notifications', 'themes/default/images/icons/24x24/buttons/disable_notifications.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4148, 'core_notifications', 'themes/default/images/icons/24x24/buttons/enable_notifications.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4149, 'core_notifications', 'themes/default/images/icons/48x48/buttons/disable_notifications.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4150, 'core_notifications', 'themes/default/images/icons/48x48/buttons/enable_notifications.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4151, 'core_notifications', 'sources/hooks/systems/addon_registry/core_notifications.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4152, 'core_notifications', 'sources/hooks/systems/commandr_fs_extended_config/notification_lockdown.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4153, 'core_notifications', 'sources/notifications.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4154, 'core_notifications', 'sources/notifications2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4155, 'core_notifications', 'lang/EN/notifications.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4156, 'core_notifications', 'sources/hooks/systems/cron/notification_digests.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4157, 'core_notifications', 'sources/hooks/systems/notifications/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4158, 'core_notifications', 'sources_custom/hooks/systems/notifications/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4159, 'core_notifications', 'sources/hooks/systems/notifications/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4160, 'core_notifications', 'sources_custom/hooks/systems/notifications/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4161, 'core_notifications', 'sources/hooks/systems/profiles_tabs_edit/notifications.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4162, 'core_notifications', 'themes/default/css/notifications.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4163, 'core_notifications', 'themes/default/javascript/notifications.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4164, 'core_notifications', 'themes/default/templates/NOTIFICATIONS_MANAGE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4165, 'core_notifications', 'themes/default/templates/NOTIFICATIONS_MANAGE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4166, 'core_notifications', 'themes/default/templates/NOTIFICATIONS_MANAGE_ADVANCED_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4167, 'core_notifications', 'themes/default/templates/NOTIFICATIONS_TREE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4168, 'core_notifications', 'themes/default/templates/NOTIFICATION_TYPES.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4169, 'core_notifications', 'themes/default/templates/NOTIFICATION_BUTTONS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4170, 'core_notifications', 'site/pages/modules/notifications.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4171, 'core_notifications', 'adminzone/pages/modules/admin_notifications.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4172, 'core_notifications', 'sources/hooks/systems/page_groupings/notifications.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4173, 'core_notifications', 'sources/hooks/systems/config/allow_auto_notifications.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4174, 'core_notifications', 'sources/hooks/systems/config/pt_notifications_as_web.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4175, 'core_notifications', 'sources/hooks/systems/config/notification_keep_days.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4176, 'core_notifications', 'sources/hooks/systems/config/web_notifications_enabled.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4177, 'core_notifications', 'sources/hooks/systems/config/notification_poll_frequency.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4178, 'core_notifications', 'sources/hooks/systems/config/notification_desktop_alerts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4179, 'core_notifications', 'sources/hooks/systems/config/notification_enable_digests.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4180, 'core_notifications', 'sources/hooks/systems/config/notification_enable_private_topics.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4181, 'core_notifications', 'data/notifications.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4182, 'core_notifications', 'sources/blocks/top_notifications.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4183, 'core_notifications', 'sources/hooks/systems/startup/notification_poller_init.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4184, 'core_notifications', 'sources/notification_poller.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4185, 'core_notifications', 'themes/default/javascript/notification_poller.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4186, 'core_notifications', 'themes/default/templates/NOTIFICATION_POLLER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4187, 'core_notifications', 'themes/default/templates/NOTIFICATION_WEB.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4188, 'core_notifications', 'themes/default/templates/NOTIFICATION_WEB_DESKTOP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4189, 'core_notifications', 'themes/default/templates/NOTIFICATION_PT_DESKTOP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4190, 'core_notifications', 'themes/default/templates/BLOCK_TOP_NOTIFICATIONS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4191, 'core_notifications', 'themes/default/templates/NOTIFICATION_BROWSE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4192, 'core_notifications', 'themes/default/templates/NOTIFICATION_VIEW_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4193, 'core_notifications', 'themes/default/images/notifications/notifications.ico');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4194, 'core_notifications', 'themes/default/images/notifications/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4195, 'core_notifications', 'data_custom/modules/web_notifications/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4196, 'core_notifications', 'data_custom/modules/web_notifications/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4197, 'core_notifications', 'sources/hooks/systems/tasks/dispatch_notification.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4198, 'core_notifications', 'sources/hooks/systems/notification_types_extended/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4199, 'core_notifications', 'sources/hooks/systems/notification_types_extended/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4200, 'core_notifications', 'sources_custom/hooks/systems/notification_types_extended/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4201, 'core_notifications', 'sources_custom/hooks/systems/notification_types_extended/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4202, 'core_notifications', 'sources/hooks/systems/rss/web_notifications.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4203, 'core_notifications', 'sources/hooks/systems/commandr_fs_extended_member/notifications_enabled.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4204, 'core_notifications', 'sources/hooks/systems/config/block_top_notifications.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4205, 'core_permission_management', 'themes/default/images/icons/24x24/menu/adminzone/security/permissions/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4206, 'core_permission_management', 'themes/default/images/icons/48x48/menu/adminzone/security/permissions/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4207, 'core_permission_management', 'themes/default/images/icons/24x24/menu/adminzone/security/permissions/privileges.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4208, 'core_permission_management', 'themes/default/images/icons/48x48/menu/adminzone/security/permissions/privileges.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4209, 'core_permission_management', 'themes/default/images/icons/24x24/menu/adminzone/security/permissions/permission_tree_editor.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4210, 'core_permission_management', 'themes/default/images/icons/48x48/menu/adminzone/security/permissions/permission_tree_editor.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4211, 'core_permission_management', 'themes/default/css/permissions_editor.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4212, 'core_permission_management', 'themes/default/templates/PERMISSION_COLUMN_SIZER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4213, 'core_permission_management', 'sources/hooks/systems/addon_registry/core_permission_management.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4214, 'core_permission_management', 'sources/hooks/systems/sitemap/privilege_category.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4215, 'core_permission_management', 'themes/default/templates/PERMISSION_KEYS_PERMISSIONS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4216, 'core_permission_management', 'themes/default/templates/PERMISSION_KEYS_PERMISSION_ROW.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4217, 'core_permission_management', 'themes/default/templates/PERMISSION_SCREEN_PERMISSIONS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4218, 'core_permission_management', 'themes/default/templates/PERMISSION_PRIVILEGES_SECTION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4219, 'core_permission_management', 'themes/default/templates/PERMISSION_PRIVILEGES_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4220, 'core_permission_management', 'themes/default/templates/PERMISSION_CELL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4221, 'core_permission_management', 'themes/default/templates/PERMISSION_HEADER_CELL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4222, 'core_permission_management', 'themes/default/templates/PERMISSION_ROW.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4223, 'core_permission_management', 'themes/default/javascript/permissions.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4224, 'core_permission_management', 'themes/default/templates/PERMISSIONS_TREE_EDITOR_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4225, 'core_permission_management', 'themes/default/templates/PERMISSION_KEYS_MESSAGE_ROW.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4226, 'core_permission_management', 'adminzone/pages/modules/admin_permissions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4227, 'core_permission_management', 'themes/default/images/permlevels/0.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4228, 'core_permission_management', 'themes/default/images/permlevels/1.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4229, 'core_permission_management', 'themes/default/images/permlevels/2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4230, 'core_permission_management', 'themes/default/images/permlevels/3.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4231, 'core_permission_management', 'themes/default/images/permlevels/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4232, 'core_permission_management', 'themes/default/images/permlevels/inherit.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4233, 'core_permission_management', 'themes/default/images/pte_view_help.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4234, 'core_primary_layout', 'sources/hooks/systems/addon_registry/core_primary_layout.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4235, 'core_primary_layout', 'themes/default/templates/MESSAGE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4236, 'core_primary_layout', 'themes/default/css/helper_panel.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4237, 'core_primary_layout', 'themes/default/css/messages.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4238, 'core_primary_layout', 'themes/default/templates/GLOBAL_HTML_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4239, 'core_primary_layout', 'themes/default/templates/GLOBAL_HTML_WRAP_mobile.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4240, 'core_primary_layout', 'themes/default/templates/GLOBAL_HELPER_PANEL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4241, 'core_primary_layout', 'themes/default/templates/CLOSED_SITE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4242, 'core_primary_layout', 'themes/default/templates/SCREEN_TITLE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4243, 'core_primary_layout', 'themes/default/templates/MAIL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4244, 'core_primary_layout', 'themes/default/text/MAIL.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4245, 'core_primary_layout', 'themes/default/text/MAIL_SUBJECT.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4246, 'core_primary_layout', 'themes/default/templates/BREADCRUMB_SEPARATOR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4247, 'core_primary_layout', 'themes/default/templates/BREADCRUMB_LONE_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4248, 'core_primary_layout', 'themes/default/templates/BREADCRUMB_LINK_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4249, 'core_rich_media', 'themes/default/images/EN/1x/editor/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4250, 'core_rich_media', 'themes/default/images/EN/2x/editor/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4251, 'core_rich_media', 'themes/default/images/icons/16x16/editor/insert_emoticons.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4252, 'core_rich_media', 'themes/default/images/icons/16x16/editor/wysiwyg_off.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4253, 'core_rich_media', 'themes/default/images/icons/16x16/editor/wysiwyg_on.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4254, 'core_rich_media', 'themes/default/images/icons/32x32/editor/insert_emoticons.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4255, 'core_rich_media', 'themes/default/images/icons/32x32/editor/wysiwyg_off.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4256, 'core_rich_media', 'themes/default/images/icons/32x32/editor/wysiwyg_on.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4257, 'core_rich_media', 'themes/default/images/icons/16x16/editor/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4258, 'core_rich_media', 'themes/default/images/icons/32x32/editor/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4259, 'core_rich_media', 'sources/hooks/systems/addon_registry/core_rich_media.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4260, 'core_rich_media', 'themes/default/javascript/dyn_comcode.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4261, 'core_rich_media', 'themes/default/templates/EMOTICON_CLICK_CODE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4262, 'core_rich_media', 'themes/default/templates/EMOTICON_IMG_CODE_DIR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4263, 'core_rich_media', 'themes/default/templates/EMOTICON_IMG_CODE_THEMED.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4264, 'core_rich_media', 'themes/default/templates/ATTACHMENT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4265, 'core_rich_media', 'themes/default/templates/ATTACHMENTS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4266, 'core_rich_media', 'themes/default/templates/ATTACHMENTS_BROWSER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4267, 'core_rich_media', 'themes/default/templates/COMCODE_ABBR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4268, 'core_rich_media', 'themes/default/templates/COMCODE_ADDRESS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4269, 'core_rich_media', 'themes/default/templates/COMCODE_ALIGN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4270, 'core_rich_media', 'themes/default/templates/COMCODE_BOLD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4271, 'core_rich_media', 'themes/default/templates/COMCODE_CITE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4272, 'core_rich_media', 'themes/default/templates/COMCODE_CODE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4273, 'core_rich_media', 'themes/default/templates/COMCODE_CODE_SCROLL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4274, 'core_rich_media', 'themes/default/templates/COMCODE_CONCEPT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4275, 'core_rich_media', 'themes/default/templates/COMCODE_CONCEPTS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4276, 'core_rich_media', 'themes/default/templates/COMCODE_CONTENTS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4277, 'core_rich_media', 'themes/default/templates/COMCODE_CONTENTS_LEVEL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4278, 'core_rich_media', 'themes/default/templates/COMCODE_CRITICAL_PARSE_ERROR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4279, 'core_rich_media', 'themes/default/templates/COMCODE_DEL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4280, 'core_rich_media', 'themes/default/templates/COMCODE_DFN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4281, 'core_rich_media', 'themes/default/templates/COMCODE_EDITOR_BUTTON.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4282, 'core_rich_media', 'themes/default/templates/COMCODE_EDITOR_MICRO_BUTTON.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4283, 'core_rich_media', 'themes/default/templates/COMCODE_PAGE_EDIT_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4284, 'core_rich_media', 'themes/default/templates/COMCODE_EMAIL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4285, 'core_rich_media', 'themes/default/templates/COMCODE_FONT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4286, 'core_rich_media', 'themes/default/templates/COMCODE_TELETYPE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4287, 'core_rich_media', 'themes/default/templates/COMCODE_SAMP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4288, 'core_rich_media', 'themes/default/templates/COMCODE_Q.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4289, 'core_rich_media', 'themes/default/templates/COMCODE_VAR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4290, 'core_rich_media', 'themes/default/templates/COMCODE_SHOCKER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4291, 'core_rich_media', 'themes/default/templates/COMCODE_HIDE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4292, 'core_rich_media', 'themes/default/templates/COMCODE_HIGHLIGHT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4293, 'core_rich_media', 'themes/default/templates/COMCODE_IMG.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4294, 'core_rich_media', 'themes/default/templates/COMCODE_INDENT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4295, 'core_rich_media', 'themes/default/templates/COMCODE_INS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4296, 'core_rich_media', 'themes/default/templates/COMCODE_ITALICS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4297, 'core_rich_media', 'themes/default/templates/COMCODE_JUMPING.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4298, 'core_rich_media', 'themes/default/templates/COMCODE_MESSAGE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4299, 'core_rich_media', 'themes/default/templates/COMCODE_SUBTITLE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4300, 'core_rich_media', 'themes/default/templates/COMCODE_MISTAKE_ERROR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4301, 'core_rich_media', 'themes/default/templates/COMCODE_MISTAKE_LINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4302, 'core_rich_media', 'themes/default/templates/COMCODE_MISTAKE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4303, 'core_rich_media', 'themes/default/templates/COMCODE_QUOTE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4304, 'core_rich_media', 'themes/default/templates/COMCODE_QUOTE_BY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4305, 'core_rich_media', 'themes/default/templates/COMCODE_RANDOM.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4306, 'core_rich_media', 'themes/default/templates/COMCODE_REFERENCE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4307, 'core_rich_media', 'themes/default/templates/COMCODE_STRIKE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4308, 'core_rich_media', 'themes/default/templates/COMCODE_SUB.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4309, 'core_rich_media', 'themes/default/templates/COMCODE_SUP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4310, 'core_rich_media', 'themes/default/templates/COMCODE_THUMB.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4311, 'core_rich_media', 'themes/default/templates/COMCODE_TICKER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4312, 'core_rich_media', 'themes/default/templates/COMCODE_UNDERLINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4313, 'core_rich_media', 'themes/default/templates/COMCODE_URL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4314, 'core_rich_media', 'themes/default/templates/COMCODE_SURROUND.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4315, 'core_rich_media', 'themes/default/templates/COMCODE_TEXTCODE_LINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4316, 'core_rich_media', 'themes/default/templates/COMCODE_TEXTCODE_TAB.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4317, 'core_rich_media', 'themes/default/templates/COMCODE_WIKI_LINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4318, 'core_rich_media', 'themes/default/templates/COMCODE_CONCEPT_INLINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4319, 'core_rich_media', 'themes/default/templates/COMCODE_CURRENCY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4320, 'core_rich_media', 'themes/default/templates/COMCODE_FAKE_TABLE_WRAP_START.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4321, 'core_rich_media', 'themes/default/templates/COMCODE_FAKE_TABLE_START_CELL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4322, 'core_rich_media', 'themes/default/templates/COMCODE_FAKE_TABLE_WIDE_START_CELL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4323, 'core_rich_media', 'themes/default/templates/COMCODE_FAKE_TABLE_WIDE_CELL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4324, 'core_rich_media', 'themes/default/templates/COMCODE_FAKE_TABLE_CELL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4325, 'core_rich_media', 'themes/default/templates/COMCODE_FAKE_TABLE_END_CELL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4326, 'core_rich_media', 'themes/default/templates/COMCODE_FAKE_TABLE_WRAP_END.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4327, 'core_rich_media', 'themes/default/templates/COMCODE_IF_IN_GROUP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4328, 'core_rich_media', 'themes/default/templates/COMCODE_OVERLAY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4329, 'core_rich_media', 'themes/default/templates/COMCODE_CAROUSEL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4330, 'core_rich_media', 'themes/default/templates/COMCODE_SECTION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4331, 'core_rich_media', 'themes/default/templates/COMCODE_SECTION_CONTROLLER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4332, 'core_rich_media', 'themes/default/templates/COMCODE_TAB_CONTROLLER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4333, 'core_rich_media', 'themes/default/templates/COMCODE_TAB_HEAD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4334, 'core_rich_media', 'themes/default/templates/COMCODE_TAB_BODY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4335, 'core_rich_media', 'themes/default/templates/COMCODE_SNAPBACK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4336, 'core_rich_media', 'themes/default/templates/COMCODE_TOOLTIP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4337, 'core_rich_media', 'themes/default/templates/COMCODE_REAL_TABLE_CELL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4338, 'core_rich_media', 'themes/default/templates/COMCODE_REAL_TABLE_END.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4339, 'core_rich_media', 'themes/default/templates/COMCODE_REAL_TABLE_ROW_END.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4340, 'core_rich_media', 'themes/default/templates/COMCODE_REAL_TABLE_ROW_START.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4341, 'core_rich_media', 'themes/default/templates/COMCODE_REAL_TABLE_START.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4342, 'core_rich_media', 'themes/default/templates/COMCODE_PULSE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4343, 'core_rich_media', 'themes/default/javascript/pulse.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4344, 'core_rich_media', 'themes/default/templates/COMCODE_BIG_TABS_CONTROLLER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4345, 'core_rich_media', 'themes/default/templates/COMCODE_BIG_TABS_TAB.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4346, 'core_rich_media', 'themes/default/templates/MEDIA_AUDIO_WEBSAFE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4347, 'core_rich_media', 'themes/default/templates/MEDIA_FLASH.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4348, 'core_rich_media', 'themes/default/templates/MEDIA_IMAGE_WEBSAFE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4349, 'core_rich_media', 'themes/default/templates/MEDIA_PDF.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4350, 'core_rich_media', 'themes/default/templates/MEDIA_QUICKTIME.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4351, 'core_rich_media', 'themes/default/templates/MEDIA_REALMEDIA.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4352, 'core_rich_media', 'themes/default/templates/MEDIA_SVG.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4353, 'core_rich_media', 'themes/default/templates/MEDIA_VIDEO_FACEBOOK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4354, 'core_rich_media', 'themes/default/templates/MEDIA_VIDEO_GENERAL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4355, 'core_rich_media', 'themes/default/templates/MEDIA_VIDEO_WEBSAFE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4356, 'core_rich_media', 'themes/default/templates/MEDIA_VIMEO.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4357, 'core_rich_media', 'themes/default/templates/MEDIA_WEBPAGE_OEMBED_RICH.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4358, 'core_rich_media', 'themes/default/templates/MEDIA_WEBPAGE_OEMBED_VIDEO.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4359, 'core_rich_media', 'themes/default/templates/MEDIA_WEBPAGE_SEMANTIC.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4360, 'core_rich_media', 'themes/default/templates/MEDIA_YOUTUBE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4361, 'core_rich_media', 'themes/default/templates/MEDIA_DOWNLOAD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4362, 'core_rich_media', 'themes/default/templates/MEDIA__DOWNLOAD_LINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4363, 'core_rich_media', 'themes/default/css/big_tabs.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4364, 'core_rich_media', 'themes/default/css/comcode_mistakes.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4365, 'core_rich_media', 'themes/default/images/icons/14x14/wiki_link.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4366, 'core_rich_media', 'themes/default/images/icons/14x14/wiki_link_hover.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4367, 'core_rich_media', 'themes/default/images/icons/28x28/wiki_link.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4368, 'core_rich_media', 'themes/default/images/icons/28x28/wiki_link_hover.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4369, 'core_rich_media', 'themes/default/images/carousel/button_left_hover.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4370, 'core_rich_media', 'themes/default/images/carousel/button_left.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4371, 'core_rich_media', 'themes/default/images/carousel/button_right_hover.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4372, 'core_rich_media', 'themes/default/images/carousel/button_right.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4373, 'core_rich_media', 'themes/default/images/carousel/fade_left.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4374, 'core_rich_media', 'themes/default/images/carousel/fade_right.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4375, 'core_rich_media', 'themes/default/images/carousel/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4376, 'core_rich_media', 'themes/default/images/big_tabs_controller_button_active.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4377, 'core_rich_media', 'themes/default/images/big_tabs_controller_button_top_active.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4378, 'core_rich_media', 'themes/default/images/big_tabs_controller_button_top.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4379, 'core_rich_media', 'themes/default/images/big_tabs_controller_button.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4380, 'core_rich_media', 'data/jwplayer.flash.swf');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4381, 'core_rich_media', 'themes/default/javascript/jwplayer.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4382, 'core_rich_media', 'sources/hooks/systems/notifications/member_mention.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4383, 'core_rich_media', 'sources/member_mentions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4384, 'core_rich_media', 'data/attachment.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4385, 'core_rich_media', 'data/attachment_popup.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4386, 'core_rich_media', 'data/emoticons.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4387, 'core_rich_media', 'data/comcode_helper.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4388, 'core_rich_media', 'data/thumb.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4389, 'core_rich_media', 'data/comcode_convert.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4390, 'core_rich_media', 'lang/EN/comcode.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4391, 'core_rich_media', 'sources/comcode.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4392, 'core_rich_media', 'sources/comcode_tools.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4393, 'core_rich_media', 'sources/comcode_cleanup.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4394, 'core_rich_media', 'sources/comcode_check.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4395, 'core_rich_media', 'sources/comcode_from_html.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4396, 'core_rich_media', 'sources/comcode_renderer.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4397, 'core_rich_media', 'sources/comcode_compiler.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4398, 'core_rich_media', 'sources/attachments.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4399, 'core_rich_media', 'sources/attachments2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4400, 'core_rich_media', 'sources/attachments3.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4401, 'core_rich_media', 'sources/comcode_add.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4402, 'core_rich_media', 'sources/hooks/systems/attachments/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4403, 'core_rich_media', 'sources_custom/hooks/systems/attachments/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4404, 'core_rich_media', 'sources/hooks/systems/attachments/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4405, 'core_rich_media', 'sources_custom/hooks/systems/attachments/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4406, 'core_rich_media', 'sources/hooks/systems/attachments/null.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4407, 'core_rich_media', 'sources/blocks/main_emoticon_codes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4408, 'core_rich_media', 'sources/hooks/systems/comcode_link_handlers/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4409, 'core_rich_media', 'sources_custom/hooks/systems/comcode_link_handlers/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4410, 'core_rich_media', 'sources/hooks/systems/comcode_link_handlers/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4411, 'core_rich_media', 'sources_custom/hooks/systems/comcode_link_handlers/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4412, 'core_rich_media', 'sources/hooks/systems/preview/comcode_tag.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4413, 'core_rich_media', 'sources/hooks/systems/config/attachment_cleanup.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4414, 'core_rich_media', 'sources/hooks/systems/config/attachment_default_height.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4415, 'core_rich_media', 'sources/hooks/systems/config/attachment_default_width.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4416, 'core_rich_media', 'sources/hooks/systems/config/simplified_attachments_ui.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4417, 'core_rich_media', 'sources/hooks/systems/config/oembed_html_whitelist.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4418, 'core_rich_media', 'sources/hooks/systems/config/oembed_manual_patterns.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4419, 'core_rich_media', 'sources/hooks/systems/config/oembed_max_size.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4420, 'core_rich_media', 'sources/hooks/systems/media_rendering/audio_general.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4421, 'core_rich_media', 'sources/hooks/systems/media_rendering/audio_microsoft.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4422, 'core_rich_media', 'sources/hooks/systems/media_rendering/audio_websafe.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4423, 'core_rich_media', 'sources/hooks/systems/media_rendering/code.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4424, 'core_rich_media', 'sources/hooks/systems/media_rendering/flash.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4425, 'core_rich_media', 'sources/hooks/systems/media_rendering/hyperlink.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4426, 'core_rich_media', 'sources/hooks/systems/media_rendering/image_websafe.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4427, 'core_rich_media', 'sources/hooks/systems/media_rendering/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4428, 'core_rich_media', 'sources_custom/hooks/systems/media_rendering/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4429, 'core_rich_media', 'sources_custom/hooks/systems/media_rendering/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4430, 'core_rich_media', 'sources/hooks/systems/comcode_preparse/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4431, 'core_rich_media', 'sources_custom/hooks/systems/comcode_preparse/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4432, 'core_rich_media', 'sources/hooks/systems/comcode_preparse/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4433, 'core_rich_media', 'sources_custom/hooks/systems/comcode_preparse/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4434, 'core_rich_media', 'sources/hooks/systems/media_rendering/email.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4435, 'core_rich_media', 'sources/hooks/systems/media_rendering/oembed.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4436, 'core_rich_media', 'sources/hooks/systems/media_rendering/pdf.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4437, 'core_rich_media', 'sources/hooks/systems/media_rendering/quicktime.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4438, 'core_rich_media', 'sources/hooks/systems/media_rendering/realaudio.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4439, 'core_rich_media', 'sources/hooks/systems/media_rendering/realmedia.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4440, 'core_rich_media', 'sources/hooks/systems/media_rendering/svg.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4441, 'core_rich_media', 'sources/hooks/systems/media_rendering/video_facebook.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4442, 'core_rich_media', 'sources/hooks/systems/media_rendering/video_general.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4443, 'core_rich_media', 'sources/hooks/systems/media_rendering/video_microsoft.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4444, 'core_rich_media', 'sources/hooks/systems/media_rendering/video_websafe.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4445, 'core_rich_media', 'sources/hooks/systems/media_rendering/vimeo.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4446, 'core_rich_media', 'sources/hooks/systems/media_rendering/youtube.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4447, 'core_rich_media', 'sources/hooks/systems/media_rendering/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4448, 'core_rich_media', 'sources/hooks/systems/comcode_link_handlers/media_rendering.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4449, 'core_rich_media', 'sources/media_renderer.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4450, 'core_rich_media', 'uploads/attachments/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4451, 'core_rich_media', 'uploads/attachments_thumbs/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4452, 'core_rich_media', 'uploads/attachments/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4453, 'core_rich_media', 'uploads/attachments_thumbs/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4454, 'core_rich_media', 'themes/default/templates/COMCODE_MEMBER_LINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4455, 'core_rich_media', 'themes/default/templates/COMCODE_MEDIA_SET.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4456, 'core_rich_media', 'themes/default/javascript/jquery_autocomplete.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4457, 'core_rich_media', 'themes/default/css/autocomplete.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4458, 'core_rich_media', 'themes/default/javascript/AUTOCOMPLETE_LOAD.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4459, 'core_rich_media', 'themes/default/images/mediaset_next.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4460, 'core_rich_media', 'themes/default/images/mediaset_previous.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4461, 'core_themeing', 'themes/default/images/icons/24x24/menu/adminzone/style/themes/themes.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4462, 'core_themeing', 'themes/default/images/icons/48x48/menu/adminzone/style/themes/themes.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4463, 'core_themeing', 'themes/default/images/icons/24x24/menu/adminzone/style/themes/css.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4464, 'core_themeing', 'themes/default/images/icons/24x24/menu/adminzone/style/themes/templates.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4465, 'core_themeing', 'themes/default/images/icons/24x24/menu/adminzone/style/themes/theme_images.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4466, 'core_themeing', 'themes/default/images/icons/48x48/menu/adminzone/style/themes/css.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4467, 'core_themeing', 'themes/default/images/icons/48x48/menu/adminzone/style/themes/templates.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4468, 'core_themeing', 'themes/default/images/icons/48x48/menu/adminzone/style/themes/theme_images.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4469, 'core_themeing', 'themes/default/css/themes_editor.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4470, 'core_themeing', 'sources/hooks/systems/snippets/exists_theme.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4471, 'core_themeing', 'adminzone/load_template.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4472, 'core_themeing', 'adminzone/tempcode_tester.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4473, 'core_themeing', 'sources/hooks/systems/addon_registry/core_themeing.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4474, 'core_themeing', 'themes/default/javascript/theme_colours.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4475, 'core_themeing', 'themes/default/templates/THEME_IMAGE_MANAGE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4476, 'core_themeing', 'themes/default/templates/THEME_IMAGE_PREVIEW.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4477, 'core_themeing', 'themes/default/templates/THEME_MANAGE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4478, 'core_themeing', 'themes/default/templates/THEME_COLOUR_CHOOSER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4479, 'core_themeing', 'themes/default/templates/THEME_EDIT_CSS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4480, 'core_themeing', 'adminzone/pages/modules/admin_themes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4481, 'core_themeing', 'themes/default/templates/TEMPLATE_EDIT_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4482, 'core_themeing', 'themes/default/templates/TEMPLATE_EDIT_SCREEN_DROPDOWN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4483, 'core_themeing', 'themes/default/templates/TEMPLATE_LIST_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4484, 'core_themeing', 'themes/default/templates/TEMPLATE_LIST_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4485, 'core_themeing', 'themes/default/templates/TEMPLATE_MANAGE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4486, 'core_themeing', 'themes/default/templates/TEMPLATE_EDIT_LINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4487, 'core_themeing', 'themes/default/templates/TEMPLATE_EDIT_SCREEN_EDITOR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4488, 'core_themeing', 'themes/default/templates/TEMPLATE_TREE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4489, 'core_themeing', 'themes/default/templates/TEMPLATE_TREE_ITEM.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4490, 'core_themeing', 'themes/default/templates/TEMPLATE_TREE_ITEM_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4491, 'core_themeing', 'themes/default/templates/TEMPLATE_TREE_NODE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4492, 'core_themeing', 'themes/default/templates/TEMPLATE_LIST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4493, 'core_themeing', 'themes/default/templates/TEMPLATE_LIST_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4494, 'core_themeing', 'themes/default/javascript/themeing.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4495, 'core_themeing', 'themes/default/templates/TEMPCODE_TESTER_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4496, 'core_themeing', 'sources/themes2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4497, 'core_themeing', 'sources/themes3.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4498, 'core_themeing', 'lang/EN/themes.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4499, 'core_themeing', 'sources/lorem.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4500, 'core_themeing', 'sources/hooks/systems/config/enable_theme_img_buttons.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4501, 'core_upgrader', 'themes/default/images/icons/24x24/menu/adminzone/tools/upgrade.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4502, 'core_upgrader', 'themes/default/images/icons/48x48/menu/adminzone/tools/upgrade.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4503, 'core_upgrader', 'sources/hooks/systems/addon_registry/core_upgrader.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4504, 'core_upgrader', 'upgrader.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4505, 'core_upgrader', 'sources/upgrade.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4506, 'core_upgrader', 'lang/EN/upgrade.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4507, 'core_upgrader', 'data/upgrader2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4508, 'core_webstandards', 'themes/default/css/webstandards.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4509, 'core_webstandards', 'sources/hooks/systems/addon_registry/core_webstandards.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4510, 'core_webstandards', 'themes/default/templates/WEBSTANDARDS_ATTRIBUTE_END.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4511, 'core_webstandards', 'themes/default/templates/WEBSTANDARDS_ATTRIBUTE_START.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4512, 'core_webstandards', 'themes/default/templates/WEBSTANDARDS_ERROR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4513, 'core_webstandards', 'themes/default/templates/WEBSTANDARDS_ERROR_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4514, 'core_webstandards', 'themes/default/templates/WEBSTANDARDS_LINE_START.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4515, 'core_webstandards', 'themes/default/templates/WEBSTANDARDS_LINE_END.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4516, 'core_webstandards', 'themes/default/templates/WEBSTANDARDS_LINE_ERROR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4517, 'core_webstandards', 'themes/default/templates/WEBSTANDARDS_MARKER_END.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4518, 'core_webstandards', 'themes/default/templates/WEBSTANDARDS_MARKER_START.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4519, 'core_webstandards', 'themes/default/templates/WEBSTANDARDS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4520, 'core_webstandards', 'themes/default/templates/WEBSTANDARDS_SCREEN_END.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4521, 'core_webstandards', 'themes/default/templates/WEBSTANDARDS_TAG_END.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4522, 'core_webstandards', 'themes/default/templates/WEBSTANDARDS_TAG_NAME_END.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4523, 'core_webstandards', 'themes/default/templates/WEBSTANDARDS_TAG_NAME_START.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4524, 'core_webstandards', 'themes/default/templates/WEBSTANDARDS_TAG_START.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4525, 'core_webstandards', 'themes/default/templates/WEBSTANDARDS_MARKER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4526, 'core_webstandards', 'sources/webstandards_js_lex.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4527, 'core_webstandards', 'sources/webstandards_js_parse.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4528, 'core_webstandards', 'sources/webstandards_js_lint.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4529, 'core_webstandards', 'lang/EN/webstandards.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4530, 'core_webstandards', 'sources/webstandards.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4531, 'core_webstandards', 'sources/webstandards2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4532, 'core_webstandards', 'sources/hooks/systems/config/webstandards_compat.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4533, 'core_webstandards', 'sources/hooks/systems/config/webstandards_css.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4534, 'core_webstandards', 'sources/hooks/systems/config/webstandards_ext_files.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4535, 'core_webstandards', 'sources/hooks/systems/config/webstandards_javascript.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4536, 'core_webstandards', 'sources/hooks/systems/config/webstandards_wcag.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4537, 'core_webstandards', 'sources/hooks/systems/config/webstandards_xhtml.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4538, 'core_zone_editor', 'themes/default/images/icons/24x24/menu/adminzone/structure/zones/zone_editor.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4539, 'core_zone_editor', 'themes/default/images/icons/48x48/menu/adminzone/structure/zones/zone_editor.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4540, 'core_zone_editor', 'sources/hooks/systems/resource_meta_aware/zone.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4541, 'core_zone_editor', 'themes/default/css/zone_editor.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4542, 'core_zone_editor', 'sources/hooks/systems/snippets/exists_zone.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4543, 'core_zone_editor', 'sources/hooks/systems/addon_registry/core_zone_editor.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4544, 'core_zone_editor', 'themes/default/templates/ZONE_EDITOR_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4545, 'core_zone_editor', 'themes/default/templates/ZONE_EDITOR_PANEL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4546, 'core_zone_editor', 'themes/default/javascript/zone_editor.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4547, 'core_zone_editor', 'adminzone/pages/modules/admin_zones.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4548, 'counting_blocks', 'sources/hooks/systems/addon_registry/counting_blocks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4549, 'counting_blocks', 'sources/blocks/main_count.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4550, 'counting_blocks', 'sources/blocks/main_countdown.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4551, 'counting_blocks', 'sources/hooks/systems/snippets/count.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4552, 'counting_blocks', 'themes/default/css/counting_blocks.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4553, 'counting_blocks', 'themes/default/templates/BLOCK_MAIN_COUNTDOWN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4554, 'counting_blocks', 'themes/default/templates/BLOCK_MAIN_COUNT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4555, 'custom_comcode', 'themes/default/images/icons/24x24/menu/adminzone/setup/custom_comcode.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4556, 'custom_comcode', 'themes/default/images/icons/48x48/menu/adminzone/setup/custom_comcode.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4557, 'custom_comcode', 'sources/hooks/systems/resource_meta_aware/custom_comcode_tag.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4558, 'custom_comcode', 'sources/custom_comcode.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4559, 'custom_comcode', 'sources/hooks/systems/snippets/exists_tag.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4560, 'custom_comcode', 'sources/hooks/systems/preview/custom_comcode.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4561, 'custom_comcode', 'sources/hooks/systems/meta/comcode_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4562, 'custom_comcode', 'sources/hooks/systems/addon_registry/custom_comcode.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4563, 'custom_comcode', 'adminzone/pages/modules/admin_custom_comcode.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4564, 'custom_comcode', 'themes/default/templates/BLOCK_MAIN_CUSTOM_COMCODE_TAGS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4565, 'custom_comcode', 'lang/EN/custom_comcode.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4566, 'custom_comcode', 'sources/blocks/main_custom_comcode_tags.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4567, 'custom_comcode', 'sources/hooks/systems/page_groupings/custom_comcode.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4568, 'custom_comcode', 'sources/hooks/blocks/main_custom_gfx/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4569, 'custom_comcode', 'sources_custom/hooks/blocks/main_custom_gfx/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4570, 'custom_comcode', 'sources/hooks/blocks/main_custom_gfx/text_overlay.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4571, 'custom_comcode', 'sources/hooks/blocks/main_custom_gfx/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4572, 'custom_comcode', 'sources_custom/hooks/blocks/main_custom_gfx/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4573, 'custom_comcode', 'themes/default/images/button1.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4574, 'custom_comcode', 'themes/default/images/button2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4575, 'custom_comcode', 'sources/blocks/main_custom_gfx.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4576, 'custom_comcode', 'sources/hooks/blocks/main_custom_gfx/rollover_button.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4577, 'custom_comcode', 'sources/hooks/systems/comcode/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4578, 'custom_comcode', 'sources_custom/hooks/systems/comcode/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4579, 'custom_comcode', 'sources/hooks/systems/comcode/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4580, 'custom_comcode', 'sources_custom/hooks/systems/comcode/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4581, 'custom_comcode', 'sources/hooks/systems/commandr_fs/custom_comcode_tags.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4582, 'debrand', 'themes/default/images/icons/24x24/menu/adminzone/style/debrand.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4583, 'debrand', 'themes/default/images/icons/48x48/menu/adminzone/style/debrand.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4584, 'debrand', 'sources/hooks/systems/addon_registry/debrand.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4585, 'debrand', 'adminzone/pages/modules/admin_debrand.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4586, 'debrand', 'sources/hooks/systems/page_groupings/debrand.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4587, 'downloads', 'themes/default/images/icons/24x24/menu/rich_content/downloads.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4588, 'downloads', 'themes/default/images/icons/48x48/menu/rich_content/downloads.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4589, 'downloads', 'themes/default/images/icons/24x24/menu/cms/downloads/add_one_licence.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4590, 'downloads', 'themes/default/images/icons/24x24/menu/cms/downloads/edit_one_licence.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4591, 'downloads', 'themes/default/images/icons/48x48/menu/cms/downloads/add_one_licence.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4592, 'downloads', 'themes/default/images/icons/48x48/menu/cms/downloads/edit_one_licence.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4593, 'downloads', 'themes/default/images/icons/24x24/menu/cms/downloads/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4594, 'downloads', 'themes/default/images/icons/48x48/menu/cms/downloads/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4595, 'downloads', 'sources/hooks/systems/reorganise_uploads/downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4596, 'downloads', 'sources/hooks/systems/resource_meta_aware/download_licence.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4597, 'downloads', 'sources/hooks/systems/commandr_fs/download_licences.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4598, 'downloads', 'sources/hooks/systems/preview/download.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4599, 'downloads', 'sources/hooks/modules/admin_import/downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4600, 'downloads', 'sources/hooks/systems/notifications/download.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4601, 'downloads', 'sources/hooks/systems/config/download_gallery_root.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4602, 'downloads', 'sources/hooks/systems/config/downloads_show_stats_count_archive.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4603, 'downloads', 'sources/hooks/systems/config/downloads_show_stats_count_bandwidth.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4604, 'downloads', 'sources/hooks/systems/config/downloads_show_stats_count_downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4605, 'downloads', 'sources/hooks/systems/config/downloads_show_stats_count_total.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4606, 'downloads', 'sources/hooks/systems/config/immediate_downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4607, 'downloads', 'sources/hooks/systems/config/maximum_download.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4608, 'downloads', 'sources/hooks/systems/config/points_ADD_DOWNLOAD.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4609, 'downloads', 'sources/hooks/systems/content_meta_aware/download.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4610, 'downloads', 'sources/hooks/systems/content_meta_aware/download_category.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4611, 'downloads', 'sources/hooks/systems/commandr_fs/downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4612, 'downloads', 'sources/hooks/systems/meta/downloads_category.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4613, 'downloads', 'sources/hooks/systems/meta/downloads_download.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4614, 'downloads', 'sources/hooks/systems/disposable_values/archive_size.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4615, 'downloads', 'sources/hooks/modules/admin_import_types/downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4616, 'downloads', 'sources/hooks/modules/admin_setupwizard/downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4617, 'downloads', 'sources/hooks/modules/admin_stats/downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4618, 'downloads', 'sources/hooks/systems/addon_registry/downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4619, 'downloads', 'sources/hooks/systems/disposable_values/download_bandwidth.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4620, 'downloads', 'sources/hooks/systems/disposable_values/num_archive_downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4621, 'downloads', 'sources/hooks/systems/disposable_values/num_downloads_downloaded.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4622, 'downloads', 'themes/default/templates/DOWNLOAD_GALLERY_IMAGE_CELL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4623, 'downloads', 'themes/default/templates/DOWNLOAD_GALLERY_ROW.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4624, 'downloads', 'themes/default/templates/DOWNLOAD_CATEGORY_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4625, 'downloads', 'themes/default/templates/DOWNLOAD_SCREEN_IMAGE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4626, 'downloads', 'themes/default/templates/DOWNLOAD_BOX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4627, 'downloads', 'themes/default/templates/DOWNLOAD_LIST_LINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4628, 'downloads', 'themes/default/templates/DOWNLOAD_LIST_LINE_2.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4629, 'downloads', 'themes/default/templates/DOWNLOAD_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4630, 'downloads', 'themes/default/templates/DOWNLOAD_ALL_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4631, 'downloads', 'themes/default/templates/DOWNLOAD_AND_IMAGES_SIMPLE_BOX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4632, 'downloads', 'uploads/downloads/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4633, 'downloads', 'uploads/downloads/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4634, 'downloads', 'themes/default/css/downloads.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4635, 'downloads', 'cms/pages/modules/cms_downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4636, 'downloads', 'lang/EN/downloads.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4637, 'downloads', 'site/pages/modules/downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4638, 'downloads', 'sources/hooks/systems/sitemap/download.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4639, 'downloads', 'sources/hooks/systems/sitemap/download_category.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4640, 'downloads', 'sources/downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4641, 'downloads', 'sources/downloads2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4642, 'downloads', 'sources/downloads_stats.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4643, 'downloads', 'sources/hooks/blocks/side_stats/stats_downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4644, 'downloads', 'sources/hooks/modules/admin_newsletter/downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4645, 'downloads', 'sources/hooks/modules/admin_unvalidated/downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4646, 'downloads', 'sources/hooks/modules/galleries_users/downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4647, 'downloads', 'sources/hooks/modules/search/downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4648, 'downloads', 'sources/hooks/modules/search/download_categories.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4649, 'downloads', 'sources/hooks/systems/page_groupings/downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4650, 'downloads', 'sources/hooks/systems/module_permissions/downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4651, 'downloads', 'sources/hooks/systems/rss/downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4652, 'downloads', 'sources/hooks/systems/trackback/downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4653, 'downloads', 'sources/hooks/systems/ajax_tree/choose_download.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4654, 'downloads', 'sources/hooks/systems/ajax_tree/choose_download_category.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4655, 'downloads', 'site/dload.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4656, 'downloads', 'site/download_licence.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4657, 'downloads', 'sources/hooks/systems/config/dload_search_index.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4658, 'downloads', 'sources/hooks/systems/config/download_entries_per_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4659, 'downloads', 'sources/hooks/systems/config/download_subcats_per_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4660, 'downloads', 'sources/hooks/systems/config/downloads_default_sort_order.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4661, 'downloads', 'sources/hooks/systems/config/downloads_subcat_narrowin.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4662, 'downloads', 'sources/hooks/systems/tasks/import_filesystem_downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4663, 'downloads', 'sources/hooks/systems/tasks/import_ftp_downloads.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4664, 'downloads', 'sources/hooks/systems/tasks/index_download.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4665, 'downloads', 'site/download_gateway.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4666, 'downloads', 'themes/default/templates/DOWNLOAD_GATEWAY_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4667, 'ecommerce', 'themes/default/images/icons/24x24/menu/adminzone/audit/ecommerce/ecommerce.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4668, 'ecommerce', 'themes/default/images/icons/48x48/menu/adminzone/audit/ecommerce/ecommerce.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4669, 'ecommerce', 'themes/default/images/icons/24x24/menu/adminzone/audit/ecommerce/subscriptions.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4670, 'ecommerce', 'themes/default/images/icons/48x48/menu/adminzone/audit/ecommerce/subscriptions.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4671, 'ecommerce', 'themes/default/images/icons/24x24/menu/rich_content/ecommerce/purchase.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4672, 'ecommerce', 'themes/default/images/icons/48x48/menu/rich_content/ecommerce/purchase.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4673, 'ecommerce', 'themes/default/images/icons/24x24/menu/adminzone/audit/ecommerce/cash_flow.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4674, 'ecommerce', 'themes/default/images/icons/24x24/menu/adminzone/audit/ecommerce/invoices.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4675, 'ecommerce', 'themes/default/images/icons/24x24/menu/adminzone/audit/ecommerce/profit_loss.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4676, 'ecommerce', 'themes/default/images/icons/24x24/menu/adminzone/audit/ecommerce/transactions.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4677, 'ecommerce', 'themes/default/images/icons/48x48/menu/adminzone/audit/ecommerce/cash_flow.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4678, 'ecommerce', 'themes/default/images/icons/48x48/menu/adminzone/audit/ecommerce/invoices.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4679, 'ecommerce', 'themes/default/images/icons/48x48/menu/adminzone/audit/ecommerce/profit_loss.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4680, 'ecommerce', 'themes/default/images/icons/48x48/menu/adminzone/audit/ecommerce/transactions.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4681, 'ecommerce', 'themes/default/images/icons/24x24/menu/adminzone/audit/ecommerce/create_invoice.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4682, 'ecommerce', 'themes/default/images/icons/24x24/menu/adminzone/audit/ecommerce/outstanding_invoices.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4683, 'ecommerce', 'themes/default/images/icons/24x24/menu/adminzone/audit/ecommerce/undelivered_invoices.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4684, 'ecommerce', 'themes/default/images/icons/48x48/menu/adminzone/audit/ecommerce/create_invoice.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4685, 'ecommerce', 'themes/default/images/icons/48x48/menu/adminzone/audit/ecommerce/outstanding_invoices.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4686, 'ecommerce', 'themes/default/images/icons/48x48/menu/adminzone/audit/ecommerce/undelivered_invoices.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4687, 'ecommerce', 'themes/default/images/icons/24x24/menu/adminzone/audit/ecommerce/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4688, 'ecommerce', 'themes/default/images/icons/24x24/menu/rich_content/ecommerce/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4689, 'ecommerce', 'themes/default/images/icons/48x48/menu/adminzone/audit/ecommerce/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4690, 'ecommerce', 'themes/default/images/icons/48x48/menu/rich_content/ecommerce/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4691, 'ecommerce', 'sources/hooks/systems/config_categories/ecommerce.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4692, 'ecommerce', 'sources/hooks/systems/resource_meta_aware/usergroup_subscription.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4693, 'ecommerce', 'sources/hooks/systems/commandr_fs/usergroup_subscriptions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4694, 'ecommerce', 'sources/hooks/systems/config/callback_password.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4695, 'ecommerce', 'sources/hooks/systems/config/currency.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4696, 'ecommerce', 'sources/hooks/systems/config/currency_api_key.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4697, 'ecommerce', 'sources/hooks/systems/config/ecommerce_test_mode.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4698, 'ecommerce', 'sources/hooks/systems/config/ipn.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4699, 'ecommerce', 'sources/hooks/systems/config/ipn_digest.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4700, 'ecommerce', 'sources/hooks/systems/config/ipn_password.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4701, 'ecommerce', 'sources/hooks/systems/config/ipn_test.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4702, 'ecommerce', 'sources/hooks/systems/config/payment_gateway.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4703, 'ecommerce', 'sources/hooks/systems/config/pd_address.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4704, 'ecommerce', 'sources/hooks/systems/config/pd_email.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4705, 'ecommerce', 'sources/hooks/systems/config/pd_number.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4706, 'ecommerce', 'sources/hooks/systems/config/use_local_payment.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4707, 'ecommerce', 'sources/hooks/systems/config/vpn_password.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4708, 'ecommerce', 'sources/hooks/systems/config/vpn_username.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4709, 'ecommerce', 'sources/hooks/systems/realtime_rain/ecommerce.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4710, 'ecommerce', 'sources/hooks/systems/addon_registry/ecommerce.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4711, 'ecommerce', 'sources/hooks/modules/admin_import_types/ecommerce.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4712, 'ecommerce', 'themes/default/templates/ECOM_CASH_FLOW_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4713, 'ecommerce', 'themes/default/templates/ECOM_INVOICES_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4714, 'ecommerce', 'themes/default/templates/ECOM_OUTSTANDING_INVOICES_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4715, 'ecommerce', 'themes/default/templates/ECOM_SUBSCRIPTIONS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4716, 'ecommerce', 'themes/default/templates/PURCHASE_WIZARD_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4717, 'ecommerce', 'themes/default/templates/PURCHASE_WIZARD_STAGE_CHOOSE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4718, 'ecommerce', 'themes/default/templates/PURCHASE_WIZARD_STAGE_DETAILS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4719, 'ecommerce', 'themes/default/templates/PURCHASE_WIZARD_STAGE_FINISH.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4720, 'ecommerce', 'themes/default/templates/PURCHASE_WIZARD_STAGE_GUEST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4721, 'ecommerce', 'themes/default/templates/PURCHASE_WIZARD_STAGE_TERMS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4722, 'ecommerce', 'themes/default/templates/PURCHASE_WIZARD_STAGE_MESSAGE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4723, 'ecommerce', 'themes/default/templates/PURCHASE_WIZARD_STAGE_PAY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4724, 'ecommerce', 'themes/default/templates/PURCHASE_WIZARD_STAGE_SUBSCRIBE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4725, 'ecommerce', 'themes/default/templates/PURCHASE_WIZARD_STAGE_TRANSACT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4726, 'ecommerce', 'themes/default/templates/ECOM_BUTTON_VIA_PAYPAL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4727, 'ecommerce', 'themes/default/templates/ECOM_BUTTON_VIA_SECPAY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4728, 'ecommerce', 'themes/default/templates/ECOM_BUTTON_VIA_WORLDPAY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4729, 'ecommerce', 'themes/default/templates/ECOM_BUTTON_VIA_CCBILL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4730, 'ecommerce', 'themes/default/templates/ECOM_CANCEL_BUTTON_VIA_PAYPAL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4731, 'ecommerce', 'themes/default/templates/ECOM_CANCEL_BUTTON_VIA_SECPAY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4732, 'ecommerce', 'themes/default/templates/ECOM_CANCEL_BUTTON_VIA_WORLDPAY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4733, 'ecommerce', 'themes/default/templates/ECOM_CANCEL_BUTTON_VIA_CCBILL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4734, 'ecommerce', 'themes/default/templates/ECOM_LOGOS_WORLDPAY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4735, 'ecommerce', 'themes/default/templates/ECOM_SUBSCRIPTION_BUTTON_VIA_PAYPAL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4736, 'ecommerce', 'themes/default/templates/ECOM_SUBSCRIPTION_BUTTON_VIA_SECPAY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4737, 'ecommerce', 'themes/default/templates/ECOM_SUBSCRIPTION_BUTTON_VIA_WORLDPAY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4738, 'ecommerce', 'themes/default/templates/ECOM_SUBSCRIPTION_BUTTON_VIA_CCBILL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4739, 'ecommerce', 'themes/default/templates/ECOM_TRANSACTION_LOGS_MANUAL_TRIGGER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4740, 'ecommerce', 'themes/default/templates/ECOM_TRANSACTION_LOGS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4741, 'ecommerce', 'themes/default/templates/ECOM_VIEW_MANUAL_TRANSACTIONS_LINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4742, 'ecommerce', 'themes/default/templates/ECOM_VIEW_MANUAL_TRANSACTIONS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4743, 'ecommerce', 'themes/default/templates/MEMBER_SUBSCRIPTION_STATUS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4744, 'ecommerce', 'sources/hooks/systems/cron/manual_subscription_notification.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4745, 'ecommerce', 'sources/hooks/systems/cron/subscription_mails.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4746, 'ecommerce', 'adminzone/pages/modules/admin_ecommerce.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4747, 'ecommerce', 'adminzone/pages/modules/admin_ecommerce_logs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4748, 'ecommerce', 'adminzone/pages/modules/admin_invoices.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4749, 'ecommerce', 'themes/default/css/ecommerce.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4750, 'ecommerce', 'data/ecommerce.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4751, 'ecommerce', 'lang/EN/ecommerce.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4752, 'ecommerce', 'sources/hooks/systems/notifications/paid_subscription_messages.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4753, 'ecommerce', 'sources/hooks/systems/notifications/payment_received.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4754, 'ecommerce', 'sources/hooks/systems/notifications/invoice.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4755, 'ecommerce', 'sources/hooks/systems/notifications/subscription_cancelled_staff.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4756, 'ecommerce', 'sources/hooks/systems/notifications/service_cancelled_staff.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4757, 'ecommerce', 'sources/hooks/systems/notifications/service_paid_for_staff.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4758, 'ecommerce', 'sources/ecommerce.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4759, 'ecommerce', 'sources/ecommerce2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4760, 'ecommerce', 'sources/ecommerce_subscriptions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4761, 'ecommerce', 'sources/hooks/systems/config/manual_subscription_expiry_notice.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4762, 'ecommerce', 'sources/hooks/modules/members/ecommerce.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4763, 'ecommerce', 'sources/hooks/systems/page_groupings/ecommerce.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4764, 'ecommerce', 'sources/hooks/systems/ecommerce/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4765, 'ecommerce', 'sources_custom/hooks/systems/ecommerce/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4766, 'ecommerce', 'sources/hooks/systems/ecommerce/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4767, 'ecommerce', 'sources_custom/hooks/systems/ecommerce/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4768, 'ecommerce', 'sources/hooks/systems/ecommerce/interest.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4769, 'ecommerce', 'sources/hooks/systems/ecommerce/other.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4770, 'ecommerce', 'sources/hooks/systems/ecommerce/tax.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4771, 'ecommerce', 'sources/hooks/systems/ecommerce/usergroup.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4772, 'ecommerce', 'sources/hooks/systems/ecommerce/wage.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4773, 'ecommerce', 'sources/hooks/systems/ecommerce/work.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4774, 'ecommerce', 'sources/hooks/systems/ecommerce_via/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4775, 'ecommerce', 'sources_custom/hooks/systems/ecommerce_via/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4776, 'ecommerce', 'sources/hooks/systems/ecommerce_via/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4777, 'ecommerce', 'sources_custom/hooks/systems/ecommerce_via/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4778, 'ecommerce', 'sources/hooks/systems/ecommerce_via/paypal.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4779, 'ecommerce', 'sources/hooks/systems/ecommerce_via/secpay.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4780, 'ecommerce', 'sources/hooks/systems/ecommerce_via/worldpay.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4781, 'ecommerce', 'sources/hooks/systems/ecommerce_via/ccbill.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4782, 'ecommerce', 'sources/hooks/systems/cns_cpf_filter/ecommerce.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4783, 'ecommerce', 'site/pages/modules/purchase.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4784, 'ecommerce', 'site/pages/modules/subscriptions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4785, 'ecommerce', 'site/pages/modules/invoices.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4786, 'ecommerce', 'sources/currency.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4787, 'ecommerce', 'sources/hooks/systems/config/primary_paypal_email.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4788, 'ecommerce', 'sources/hooks/systems/config/max_ip_addresses_per_subscriber.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4789, 'ecommerce', 'sources/hooks/systems/notifications/ip_address_sharing.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4790, 'ecommerce', 'sources/hooks/systems/cron/ip_address_sharing.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4791, 'ecommerce', 'sources/hooks/systems/symbols/CURRENCY_SYMBOL.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4792, 'ecommerce', 'sources/hooks/systems/commandr_fs_extended_member/invoices.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4793, 'ecommerce', 'sources/hooks/systems/commandr_fs_extended_member/subscriptions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4794, 'errorlog', 'themes/default/images/icons/24x24/menu/adminzone/audit/errorlog.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4795, 'errorlog', 'themes/default/images/icons/48x48/menu/adminzone/audit/errorlog.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4796, 'errorlog', 'themes/default/css/errorlog.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4797, 'errorlog', 'sources/hooks/systems/addon_registry/errorlog.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4798, 'errorlog', 'lang/EN/errorlog.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4799, 'errorlog', 'data_custom/errorlog.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4800, 'errorlog', 'adminzone/pages/modules/admin_errorlog.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4801, 'errorlog', 'themes/default/templates/ERRORLOG_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4802, 'failover', 'sources/hooks/systems/addon_registry/failover.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4803, 'failover', 'data/failover_script.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4804, 'filedump', 'themes/default/images/icons/24x24/menu/cms/filedump.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4805, 'filedump', 'themes/default/images/icons/48x48/menu/cms/filedump.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4806, 'filedump', 'sources/hooks/systems/notifications/filedump.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4807, 'filedump', 'sources/hooks/systems/config/filedump_show_stats_count_total_files.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4808, 'filedump', 'sources/hooks/systems/config/filedump_show_stats_count_total_space.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4809, 'filedump', 'sources/hooks/blocks/side_stats/stats_filedump.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4810, 'filedump', 'sources/hooks/systems/addon_registry/filedump.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4811, 'filedump', 'sources/hooks/systems/ajax_tree/choose_filedump_file.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4812, 'filedump', 'sources/hooks/systems/page_groupings/filedump.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4813, 'filedump', 'sources/hooks/modules/admin_import_types/filedump.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4814, 'filedump', 'themes/default/templates/FILEDUMP_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4815, 'filedump', 'themes/default/templates/FILEDUMP_EMBED_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4816, 'filedump', 'themes/default/templates/FILEDUMP_FOOTER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4817, 'filedump', 'themes/default/templates/FILEDUMP_SEARCH.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4818, 'filedump', 'uploads/filedump/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4819, 'filedump', 'cms/pages/modules/filedump.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4820, 'filedump', 'lang/EN/filedump.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4821, 'filedump', 'sources/hooks/systems/config/is_on_folder_create.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4822, 'filedump', 'sources/hooks/modules/search/filedump.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4823, 'filedump', 'sources/hooks/systems/rss/filedump.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4824, 'filedump', 'sources/hooks/systems/commandr_fs/home.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4825, 'filedump', 'uploads/filedump/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4826, 'filedump', 'themes/default/css/filedump.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4827, 'filedump', 'sources/filedump.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4828, 'filedump', 'sources/hooks/systems/commandr_fs/filedump.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4829, 'forum_blocks', 'themes/default/templates/BLOCK_MAIN_FORUM_NEWS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4830, 'forum_blocks', 'themes/default/templates/BLOCK_MAIN_FORUM_TOPICS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4831, 'forum_blocks', 'themes/default/templates/BLOCK_SIDE_FORUM_NEWS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4832, 'forum_blocks', 'sources/blocks/bottom_forum_news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4833, 'forum_blocks', 'sources/blocks/main_forum_news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4834, 'forum_blocks', 'sources/blocks/main_forum_topics.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4835, 'forum_blocks', 'sources/blocks/side_forum_news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4836, 'forum_blocks', 'sources/hooks/systems/addon_registry/forum_blocks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4837, 'forum_blocks', 'sources/hooks/modules/admin_setupwizard/forum_blocks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4838, 'galleries', 'themes/default/images/icons/24x24/menu/rich_content/galleries.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4839, 'galleries', 'themes/default/images/icons/48x48/menu/rich_content/galleries.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4840, 'galleries', 'themes/default/images/icons/24x24/menu/cms/galleries/add_one_image.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4841, 'galleries', 'themes/default/images/icons/24x24/menu/cms/galleries/add_one_video.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4842, 'galleries', 'themes/default/images/icons/24x24/menu/cms/galleries/edit_one_image.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4843, 'galleries', 'themes/default/images/icons/24x24/menu/cms/galleries/edit_one_video.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4844, 'galleries', 'themes/default/images/icons/48x48/menu/cms/galleries/add_one_image.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4845, 'galleries', 'themes/default/images/icons/48x48/menu/cms/galleries/add_one_video.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4846, 'galleries', 'themes/default/images/icons/48x48/menu/cms/galleries/edit_one_image.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4847, 'galleries', 'themes/default/images/icons/48x48/menu/cms/galleries/edit_one_video.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4848, 'galleries', 'themes/default/images/icons/24x24/menu/cms/galleries/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4849, 'galleries', 'themes/default/images/icons/48x48/menu/cms/galleries/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4850, 'galleries', 'themes/default/images/icons/24x24/buttons/slideshow.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4851, 'galleries', 'themes/default/images/icons/48x48/buttons/slideshow.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4852, 'galleries', 'sources/hooks/systems/config_categories/gallery.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4853, 'galleries', 'sources/hooks/systems/reorganise_uploads/galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4854, 'galleries', 'data/zencoder_receive.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4855, 'galleries', 'sources/hooks/systems/notifications/gallery_entry.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4856, 'galleries', 'sources/hooks/systems/snippets/exists_gallery.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4857, 'galleries', 'sources/hooks/modules/admin_setupwizard_installprofiles/portfolio.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4858, 'galleries', 'sources/hooks/systems/config/audio_bitrate.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4859, 'galleries', 'sources/hooks/systems/config/gallery_media_title_required.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4860, 'galleries', 'sources/hooks/systems/config/default_video_height.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4861, 'galleries', 'sources/hooks/systems/config/default_video_width.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4862, 'galleries', 'sources/hooks/systems/config/ffmpeg_path.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4863, 'galleries', 'sources/hooks/systems/config/transcoding_zencoder_api_key.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4864, 'galleries', 'sources/hooks/systems/config/transcoding_zencoder_ftp_path.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4865, 'galleries', 'sources/hooks/systems/config/transcoding_server.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4866, 'galleries', 'sources/hooks/systems/config/galleries_show_stats_count_galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4867, 'galleries', 'sources/hooks/systems/config/galleries_show_stats_count_images.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4868, 'galleries', 'sources/hooks/systems/config/galleries_show_stats_count_videos.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4869, 'galleries', 'sources/hooks/systems/config/gallery_name_order.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4870, 'galleries', 'sources/hooks/systems/config/gallery_selectors.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4871, 'galleries', 'sources/hooks/systems/config/max_personal_gallery_images_high.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4872, 'galleries', 'sources/hooks/systems/config/max_personal_gallery_images_low.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4873, 'galleries', 'sources/hooks/systems/config/max_personal_gallery_videos_high.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4874, 'galleries', 'sources/hooks/systems/config/max_personal_gallery_videos_low.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4875, 'galleries', 'sources/hooks/systems/config/maximum_image_size.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4876, 'galleries', 'sources/hooks/systems/config/points_ADD_IMAGE.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4877, 'galleries', 'sources/hooks/systems/config/points_ADD_VIDEO.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4878, 'galleries', 'sources/hooks/systems/config/reverse_thumb_order.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4879, 'galleries', 'sources/hooks/systems/config/show_empty_galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4880, 'galleries', 'sources/hooks/systems/config/show_gallery_counts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4881, 'galleries', 'sources/hooks/systems/config/video_bitrate.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4882, 'galleries', 'sources/hooks/systems/config/video_height_setting.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4883, 'galleries', 'sources/hooks/systems/config/video_width_setting.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4884, 'galleries', 'sources/hooks/systems/content_meta_aware/image.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4885, 'galleries', 'sources/hooks/systems/content_meta_aware/video.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4886, 'galleries', 'sources/hooks/systems/content_meta_aware/gallery.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4887, 'galleries', 'sources/hooks/systems/commandr_fs/galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4888, 'galleries', 'sources/hooks/systems/meta/image.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4889, 'galleries', 'sources/hooks/systems/meta/video.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4890, 'galleries', 'sources/hooks/systems/meta/gallery.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4891, 'galleries', 'sources/hooks/blocks/side_stats/stats_galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4892, 'galleries', 'sources/hooks/systems/addon_registry/galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4893, 'galleries', 'sources/hooks/modules/admin_import_types/galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4894, 'galleries', 'sources/hooks/systems/symbols/GALLERY_VIDEO_FOR_URL.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4895, 'galleries', 'sources/hooks/systems/profiles_tabs/galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4896, 'galleries', 'sources/hooks/systems/sitemap/gallery.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4897, 'galleries', 'sources/hooks/systems/sitemap/image.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4898, 'galleries', 'sources/hooks/systems/sitemap/video.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4899, 'galleries', 'themes/default/templates/GALLERY_POPULAR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4900, 'galleries', 'themes/default/templates/GALLERY_ENTRY_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4901, 'galleries', 'themes/default/templates/BLOCK_MAIN_GALLERY_EMBED.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4902, 'galleries', 'themes/default/templates/GALLERY_BOX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4903, 'galleries', 'themes/default/templates/GALLERY_IMAGE_BOX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4904, 'galleries', 'themes/default/templates/GALLERY_VIDEO_BOX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4905, 'galleries', 'themes/default/templates/GALLERY_ENTRY_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4906, 'galleries', 'themes/default/templates/GALLERY_FLOW_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4907, 'galleries', 'themes/default/templates/GALLERY_FLOW_MODE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4908, 'galleries', 'themes/default/templates/GALLERY_ENTRY_LIST_LINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4909, 'galleries', 'themes/default/templates/GALLERY_NAV.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4910, 'galleries', 'themes/default/templates/GALLERY_IMAGE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4911, 'galleries', 'themes/default/templates/GALLERY_FLOW_MODE_IMAGE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4912, 'galleries', 'themes/default/templates/GALLERY_FLOW_MODE_VIDEO.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4913, 'galleries', 'themes/default/templates/GALLERY_VIDEO.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4914, 'galleries', 'themes/default/templates/GALLERY_VIDEO_INFO.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4915, 'galleries', 'themes/default/templates/GALLERY_REGULAR_MODE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4916, 'galleries', 'themes/default/templates/BLOCK_SIDE_GALLERIES.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4917, 'galleries', 'themes/default/templates/BLOCK_SIDE_GALLERIES_LINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4918, 'galleries', 'themes/default/templates/BLOCK_SIDE_GALLERIES_LINE_CONTAINER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4919, 'galleries', 'themes/default/templates/BLOCK_SIDE_GALLERIES_LINE_DEPTH.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4920, 'galleries', 'themes/default/templates/BLOCK_MAIN_IMAGE_FADER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4921, 'galleries', 'themes/default/templates/GALLERY_IMPORT_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4922, 'galleries', 'uploads/galleries/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4923, 'galleries', 'uploads/galleries_thumbs/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4924, 'galleries', 'uploads/watermarks/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4925, 'galleries', 'themes/default/css/galleries.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4926, 'galleries', 'cms/pages/modules/cms_galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4927, 'galleries', 'lang/EN/galleries.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4928, 'galleries', 'site/pages/modules/galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4929, 'galleries', 'sources/blocks/side_galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4930, 'galleries', 'uploads/galleries/pre_transcoding/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4931, 'galleries', 'sources/transcoding.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4932, 'galleries', 'sources/galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4933, 'galleries', 'sources/galleries2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4934, 'galleries', 'sources/galleries3.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4935, 'galleries', 'sources/hooks/modules/admin_import/galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4936, 'galleries', 'sources/hooks/modules/admin_newsletter/galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4937, 'galleries', 'sources/hooks/modules/admin_setupwizard/galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4938, 'galleries', 'sources/hooks/modules/galleries_users/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4939, 'galleries', 'sources_custom/hooks/modules/galleries_users/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4940, 'galleries', 'sources/hooks/modules/galleries_users/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4941, 'galleries', 'sources_custom/hooks/modules/galleries_users/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4942, 'galleries', 'sources/hooks/modules/search/galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4943, 'galleries', 'sources/hooks/systems/page_groupings/galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4944, 'galleries', 'sources/hooks/systems/module_permissions/galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4945, 'galleries', 'sources/hooks/systems/rss/galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4946, 'galleries', 'sources/hooks/modules/admin_unvalidated/videos.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4947, 'galleries', 'sources/hooks/modules/search/videos.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4948, 'galleries', 'sources/hooks/systems/ajax_tree/choose_video.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4949, 'galleries', 'sources/hooks/systems/preview/video.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4950, 'galleries', 'sources/hooks/systems/trackback/videos.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4951, 'galleries', 'sources/hooks/modules/admin_unvalidated/images.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4952, 'galleries', 'sources/hooks/modules/search/images.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4953, 'galleries', 'sources/hooks/systems/trackback/images.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4954, 'galleries', 'sources/hooks/systems/ajax_tree/choose_gallery.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4955, 'galleries', 'sources/hooks/systems/ajax_tree/choose_image.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4956, 'galleries', 'site/download_gallery.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4957, 'galleries', 'sources/hooks/systems/preview/image.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4958, 'galleries', 'sources/blocks/main_gallery_embed.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4959, 'galleries', 'sources/blocks/main_image_fader.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4960, 'galleries', 'sources/blocks/main_personal_galleries_list.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4961, 'galleries', 'themes/default/templates/BLOCK_MAIN_PERSONAL_GALLERIES_LIST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4962, 'galleries', 'uploads/galleries/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4963, 'galleries', 'uploads/galleries_thumbs/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4964, 'galleries', 'uploads/watermarks/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4965, 'galleries', 'themes/default/images/audio_thumb.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4966, 'galleries', 'themes/default/images/video_thumb.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4967, 'galleries', 'themes/default/javascript/galleries.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4968, 'galleries', 'themes/default/templates/CNS_MEMBER_PROFILE_GALLERIES.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4969, 'galleries', 'sources/hooks/systems/block_ui_renderers/galleries.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4970, 'galleries', 'sources/hooks/systems/config/galleries_default_sort_order.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4971, 'galleries', 'sources/hooks/systems/config/galleries_subcat_narrowin.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4972, 'galleries', 'sources/hooks/systems/config/gallery_entries_flow_per_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4973, 'galleries', 'sources/hooks/systems/config/gallery_entries_regular_per_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4974, 'galleries', 'sources/hooks/systems/config/gallery_feedback_fields.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4975, 'galleries', 'sources/hooks/systems/config/gallery_member_synced.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4976, 'galleries', 'sources/hooks/systems/config/gallery_mode_is.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4977, 'galleries', 'sources/hooks/systems/config/gallery_permissions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4978, 'galleries', 'sources/hooks/systems/config/gallery_rep_image.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4979, 'galleries', 'sources/hooks/systems/config/gallery_watermarks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4980, 'galleries', 'sources/hooks/systems/config/subgallery_link_limit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4981, 'galleries', 'sources/hooks/systems/config/personal_under_members.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4982, 'galleries', 'sources/hooks/systems/config/manual_gallery_codename.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4983, 'galleries', 'sources/hooks/systems/config/manual_gallery_media_types.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4984, 'galleries', 'sources/hooks/systems/config/manual_gallery_parent.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4985, 'galleries', 'sources/hooks/systems/config/enable_ecards.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4986, 'galleries', 'sources/hooks/systems/tasks/download_gallery.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4987, 'google_appengine', 'sources/hooks/systems/addon_registry/google_appengine.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4988, 'google_appengine', 'app.yaml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4989, 'google_appengine', 'data/modules/google_appengine/cron.yaml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4990, 'google_appengine', 'data/modules/google_appengine/dos.yaml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4991, 'google_appengine', 'data/modules/google_appengine/queue.yaml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4992, 'google_appengine', 'data/modules/google_appengine/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4993, 'google_appengine', 'data/modules/google_appengine/php.gae.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4994, 'google_appengine', 'data/modules/google_appengine/cloud_storage_proxy.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4995, 'google_appengine', 'data_custom/modules/google_appengine/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4996, 'google_appengine', 'sources/google_appengine.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4997, 'help_page', 'sources/hooks/systems/addon_registry/help_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4998, 'help_page', 'site/pages/comcode/EN/help.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (4999, 'help_page', 'sources/hooks/systems/page_groupings/help_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5000, 'hphp_buildkit', 'sources/hooks/systems/addon_registry/hphp_buildkit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5001, 'hphp_buildkit', 'hphp.sh');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5002, 'hphp_buildkit', 'hphp_debug.sh');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5003, 'hphp_buildkit', 'cms.hdf');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5004, 'import', 'themes/default/css/importing.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5005, 'import', 'sources/hooks/modules/admin_import/html_site.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5006, 'import', 'sources/hooks/modules/admin_import/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5007, 'import', 'sources_custom/hooks/modules/admin_import/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5008, 'import', 'sources/hooks/modules/admin_import/shared/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5009, 'import', 'sources/hooks/modules/admin_import/shared/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5010, 'import', 'sources/hooks/systems/addon_registry/import.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5011, 'import', 'sources/hooks/modules/admin_import_types/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5012, 'import', 'sources_custom/hooks/modules/admin_import_types/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5013, 'import', 'sources/hooks/modules/admin_import_types/core.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5014, 'import', 'sources/hooks/modules/admin_import_types/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5015, 'import', 'sources_custom/hooks/modules/admin_import_types/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5016, 'import', 'themes/default/templates/IMPORT_ACTION_LINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5017, 'import', 'themes/default/templates/IMPORT_ACTION_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5018, 'import', 'themes/default/templates/IMPORT_MESSAGE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5019, 'import', 'themes/default/text/IMPORT_PHPNUKE_FCOMCODEPAGE.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5020, 'import', 'themes/default/text/IMPORT_MKPORTAL_FCOMCODEPAGE.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5021, 'import', 'adminzone/pages/modules/admin_import.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5022, 'import', 'lang/EN/import.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5023, 'import', 'sources/hooks/modules/admin_import/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5024, 'import', 'sources_custom/hooks/modules/admin_import/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5025, 'import', 'sources/hooks/modules/admin_import/ipb1.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5026, 'import', 'sources/hooks/modules/admin_import/ipb2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5027, 'import', 'sources/hooks/modules/admin_import/cms_merge.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5028, 'import', 'sources/hooks/modules/admin_import/phpbb2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5029, 'import', 'sources/hooks/modules/admin_import/shared/ipb.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5030, 'import', 'sources/hooks/modules/admin_import/vb3.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5031, 'import', 'sources/hooks/modules/admin_import/mybb.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5032, 'import', 'sources/hooks/modules/admin_import/wowbb.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5033, 'import', 'sources/hooks/modules/admin_import/phpbb3.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5034, 'import', 'sources/hooks/modules/admin_import/aef.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5035, 'import', 'sources/hooks/modules/admin_import/smf.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5036, 'import', 'sources/hooks/modules/admin_import/smf2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5037, 'import', 'sources/hooks/systems/page_groupings/import.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5038, 'import', 'sources/hooks/modules/admin_import/wordpress.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5039, 'import', 'sources/hooks/systems/cns_auth/wordpress.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5040, 'import', 'sources/import.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5041, 'import', 'sources/hooks/systems/commandr_commands/continue_import.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5042, 'installer', 'sources/hooks/systems/addon_registry/installer.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5043, 'installer', 'themes/default/templates/INSTALLER_FORUM_CHOICE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5044, 'installer', 'themes/default/templates/INSTALLER_FORUM_CHOICE_VERSION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5045, 'installer', 'themes/default/templates/INSTALLER_STEP_4_SECTION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5046, 'installer', 'themes/default/templates/INSTALLER_STEP_4_SECTION_HIDE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5047, 'installer', 'themes/default/templates/INSTALLER_STEP_4_SECTION_OPTION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5048, 'installer', 'themes/default/templates/INSTALLER_HTML_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5049, 'installer', 'themes/default/templates/INSTALLER_WARNING_LONG.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5050, 'installer', 'themes/default/templates/INSTALLER_DONE_SOMETHING.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5051, 'installer', 'themes/default/templates/INSTALLER_INPUT_LINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5052, 'installer', 'themes/default/templates/INSTALLER_INPUT_PASSWORD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5053, 'installer', 'themes/default/templates/INSTALLER_INPUT_TICK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5054, 'installer', 'themes/default/templates/INSTALLER_STEP_1.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5055, 'installer', 'themes/default/templates/INSTALLER_STEP_2.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5056, 'installer', 'themes/default/templates/INSTALLER_STEP_3.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5057, 'installer', 'themes/default/templates/INSTALLER_STEP_4.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5058, 'installer', 'themes/default/templates/INSTALLER_STEP_LOG.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5059, 'installer', 'themes/default/templates/INSTALLER_STEP_10.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5060, 'installer', 'themes/default/templates/INSTALLER_WARNING.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5061, 'installer', 'themes/default/templates/INSTALLER_NOTICE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5062, 'language_block', 'sources/hooks/systems/addon_registry/language_block.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5063, 'language_block', 'themes/default/templates/BLOCK_SIDE_LANGUAGE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5064, 'language_block', 'sources/blocks/side_language.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5065, 'ldap', 'themes/default/images/icons/24x24/menu/adminzone/security/ldap.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5066, 'ldap', 'themes/default/images/icons/48x48/menu/adminzone/security/ldap.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5067, 'ldap', 'sources/hooks/systems/config/ldap_allow_joining.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5068, 'ldap', 'sources/hooks/systems/config/ldap_base_dn.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5069, 'ldap', 'sources/hooks/systems/config/ldap_bind_password.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5070, 'ldap', 'sources/hooks/systems/config/ldap_bind_rdn.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5071, 'ldap', 'sources/hooks/systems/config/ldap_group_class.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5072, 'ldap', 'sources/hooks/systems/config/ldap_group_search_qualifier.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5073, 'ldap', 'sources/hooks/systems/config/ldap_hostname.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5074, 'ldap', 'sources/hooks/systems/config/ldap_is_enabled.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5075, 'ldap', 'sources/hooks/systems/config/ldap_is_windows.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5076, 'ldap', 'sources/hooks/systems/config/ldap_login_qualifier.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5077, 'ldap', 'sources/hooks/systems/config/ldap_member_class.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5078, 'ldap', 'sources/hooks/systems/config/ldap_member_property.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5079, 'ldap', 'sources/hooks/systems/config/ldap_member_search_qualifier.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5080, 'ldap', 'sources/hooks/systems/config/ldap_none_bind_logins.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5081, 'ldap', 'sources/hooks/systems/config/ldap_version.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5082, 'ldap', 'sources/hooks/systems/addon_registry/ldap.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5083, 'ldap', 'themes/default/templates/CNS_LDAP_LIST_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5084, 'ldap', 'themes/default/templates/CNS_LDAP_SYNC_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5085, 'ldap', 'adminzone/pages/modules/admin_cns_ldap.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5086, 'ldap', 'sources/cns_ldap.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5087, 'ldap', 'sources/hooks/systems/page_groupings/ldap.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5088, 'linux_helper_scripts', 'sources/hooks/systems/addon_registry/linux_helper_scripts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5089, 'linux_helper_scripts', 'decache.sh');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5090, 'linux_helper_scripts', 'fixperms.sh');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5091, 'linux_helper_scripts', 'themechanges.sh');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5092, 'linux_helper_scripts', 'recentchanges.sh');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5093, 'linux_helper_scripts', 'db_init.sh');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5094, 'linux_helper_scripts', 'db_export.sh');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5095, 'linux_helper_scripts', 'db_import.sh');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5096, 'match_key_permissions', 'themes/default/images/icons/24x24/menu/adminzone/security/permissions/match_keys.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5097, 'match_key_permissions', 'themes/default/images/icons/48x48/menu/adminzone/security/permissions/match_keys.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5098, 'match_key_permissions', 'sources/hooks/systems/commandr_fs_extended_config/match_key_messages.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5099, 'match_key_permissions', 'sources/hooks/systems/addon_registry/match_key_permissions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5100, 'msn', 'themes/default/images/icons/24x24/menu/adminzone/structure/multi_site_network.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5101, 'msn', 'themes/default/images/icons/48x48/menu/adminzone/structure/multi_site_network.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5102, 'msn', 'sources/hooks/systems/config/network_links.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5103, 'msn', 'sources/hooks/systems/addon_registry/msn.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5104, 'msn', 'sources/hooks/blocks/main_notes/msn.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5105, 'msn', 'themes/default/templates/BLOCK_SIDE_NETWORK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5106, 'msn', 'themes/default/templates/NETLINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5107, 'msn', 'adminzone/pages/comcode/EN/netlink.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5108, 'msn', 'text/netlink.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5109, 'msn', 'data/netlink.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5110, 'msn', 'sources/hooks/systems/page_groupings/msn.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5111, 'msn', 'sources/multi_site_networks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5112, 'msn', 'sources/blocks/side_network.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5113, 'news', 'themes/default/images/icons/24x24/menu/rich_content/news.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5114, 'news', 'themes/default/images/icons/24x24/tabs/member_account/blog.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5115, 'news', 'themes/default/images/icons/48x48/menu/rich_content/news.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5116, 'news', 'themes/default/images/icons/48x48/tabs/member_account/blog.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5117, 'news', 'sources/hooks/systems/reorganise_uploads/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5118, 'news', 'sources/hooks/systems/notifications/news_entry.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5119, 'news', 'sources/hooks/modules/admin_setupwizard_installprofiles/blog.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5120, 'news', 'sources/hooks/systems/realtime_rain/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5121, 'news', 'sources/hooks/systems/content_meta_aware/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5122, 'news', 'sources/hooks/systems/content_meta_aware/news_category.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5123, 'news', 'sources/hooks/systems/commandr_fs/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5124, 'news', 'sources/hooks/systems/meta/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5125, 'news', 'sources/hooks/blocks/side_stats/stats_news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5126, 'news', 'sources/hooks/systems/addon_registry/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5127, 'news', 'sources/hooks/modules/admin_import_types/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5128, 'news', 'sources/hooks/systems/config/news_summary_required.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5129, 'news', 'sources/hooks/systems/config/blog_update_time.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5130, 'news', 'sources/hooks/systems/config/news_show_stats_count_blogs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5131, 'news', 'sources/hooks/systems/config/news_show_stats_count_total_posts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5132, 'news', 'sources/hooks/systems/config/news_update_time.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5133, 'news', 'sources/hooks/systems/config/ping_url.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5134, 'news', 'sources/hooks/systems/config/points_ADD_NEWS.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5135, 'news', 'sources/hooks/systems/profiles_tabs/blog.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5136, 'news', 'sources/hooks/systems/sitemap/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5137, 'news', 'sources/hooks/systems/sitemap/news_category.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5138, 'news', 'themes/default/templates/NEWS_ARCHIVE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5139, 'news', 'themes/default/templates/NEWS_ENTRY_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5140, 'news', 'themes/default/templates/BLOCK_BOTTOM_NEWS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5141, 'news', 'themes/default/templates/BLOCK_MAIN_NEWS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5142, 'news', 'themes/default/templates/BLOCK_SIDE_NEWS_ARCHIVE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5143, 'news', 'themes/default/templates/BLOCK_SIDE_NEWS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5144, 'news', 'themes/default/templates/BLOCK_SIDE_NEWS_SUMMARY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5145, 'news', 'themes/default/templates/BLOCK_SIDE_NEWS_CATEGORIES.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5146, 'news', 'themes/default/templates/NEWS_CHICKLETS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5147, 'news', 'themes/default/templates/NEWS_WORDPRESS_IMPORT_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5148, 'news', 'themes/default/images/newscats/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5149, 'news', 'themes/default/images/newscats/art.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5150, 'news', 'themes/default/images/newscats/business.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5151, 'news', 'themes/default/images/newscats/community.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5152, 'news', 'themes/default/images/newscats/difficulties.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5153, 'news', 'themes/default/images/newscats/entertainment.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5154, 'news', 'themes/default/images/newscats/general.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5155, 'news', 'themes/default/images/newscats/technology.jpg');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5156, 'news', 'cms/pages/modules/cms_news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5157, 'news', 'cms/pages/modules/cms_blogs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5158, 'news', 'site/pages/modules/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5159, 'news', 'sources/blocks/bottom_news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5160, 'news', 'sources/blocks/main_news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5161, 'news', 'sources/blocks/side_news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5162, 'news', 'sources/blocks/side_news_archive.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5163, 'news', 'sources/blocks/side_news_categories.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5164, 'news', 'sources/hooks/blocks/main_staff_checklist/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5165, 'news', 'sources/hooks/modules/admin_setupwizard/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5166, 'news', 'sources/hooks/modules/admin_unvalidated/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5167, 'news', 'sources/hooks/modules/members/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5168, 'news', 'sources/hooks/modules/search/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5169, 'news', 'sources/hooks/systems/attachments/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5170, 'news', 'sources/hooks/systems/page_groupings/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5171, 'news', 'sources/hooks/systems/module_permissions/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5172, 'news', 'sources/hooks/systems/preview/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5173, 'news', 'sources/hooks/systems/rss/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5174, 'news', 'sources/hooks/systems/trackback/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5175, 'news', 'sources/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5176, 'news', 'sources/news2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5177, 'news', 'sources/hooks/modules/admin_import/rss.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5178, 'news', 'sources/hooks/modules/admin_newsletter/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5179, 'news', 'sources/hooks/blocks/main_staff_checklist/blog.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5180, 'news', 'themes/default/templates/CNS_MEMBER_PROFILE_BLOG.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5181, 'news', 'sources/hooks/systems/block_ui_renderers/news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5182, 'news', 'sources/hooks/systems/config/news_categories_per_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5183, 'news', 'sources/hooks/systems/config/news_entries_per_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5184, 'news', 'sources/hooks/systems/config/enable_secondary_news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5185, 'news', 'themes/default/templates/BLOCK_MAIN_IMAGE_FADER_NEWS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5186, 'news', 'sources/blocks/main_image_fader_news.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5187, 'news', 'sources/news_sitemap.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5188, 'news', 'sources/hooks/systems/tasks/import_rss.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5189, 'news', 'sources/hooks/systems/tasks/import_wordpress.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5190, 'news_shared', 'sources/hooks/systems/addon_registry/news_shared.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5191, 'news_shared', 'themes/default/templates/NEWS_BOX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5192, 'news_shared', 'themes/default/templates/NEWS_BRIEF.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5193, 'news_shared', 'themes/default/css/news.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5194, 'news_shared', 'lang/EN/news.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5195, 'newsletter', 'themes/default/images/icons/24x24/menu/adminzone/tools/newsletter/import_subscribers.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5196, 'newsletter', 'themes/default/images/icons/24x24/menu/adminzone/tools/newsletter/newsletter_email_bounce.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5197, 'newsletter', 'themes/default/images/icons/24x24/menu/adminzone/tools/newsletter/newsletter_from_changes.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5198, 'newsletter', 'themes/default/images/icons/24x24/menu/adminzone/tools/newsletter/subscribers.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5199, 'newsletter', 'themes/default/images/icons/24x24/menu/site_meta/newsletters.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5200, 'newsletter', 'themes/default/images/icons/48x48/menu/adminzone/tools/newsletter/import_subscribers.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5201, 'newsletter', 'themes/default/images/icons/48x48/menu/adminzone/tools/newsletter/newsletter_email_bounce.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5202, 'newsletter', 'themes/default/images/icons/48x48/menu/adminzone/tools/newsletter/newsletter_from_changes.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5203, 'newsletter', 'themes/default/images/icons/48x48/menu/adminzone/tools/newsletter/subscribers.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5204, 'newsletter', 'themes/default/images/icons/48x48/menu/site_meta/newsletters.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5205, 'newsletter', 'themes/default/images/icons/24x24/menu/adminzone/tools/newsletter/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5206, 'newsletter', 'themes/default/images/icons/48x48/menu/adminzone/tools/newsletter/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5207, 'newsletter', 'sources/hooks/systems/block_ui_renderers/newsletters.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5208, 'newsletter', 'sources/hooks/modules/admin_setupwizard/newsletter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5209, 'newsletter', 'sources/hooks/systems/tasks/import_newsletter_subscribers.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5210, 'newsletter', 'sources/hooks/systems/config/interest_levels.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5211, 'newsletter', 'sources/hooks/systems/config/newsletter_text.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5212, 'newsletter', 'sources/hooks/systems/config/newsletter_title.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5213, 'newsletter', 'sources/hooks/systems/addon_registry/newsletter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5214, 'newsletter', 'sources/hooks/systems/cron/newsletter_drip_send.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5215, 'newsletter', 'sources/hooks/systems/cron/newsletter_periodic.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5216, 'newsletter', 'sources/hooks/modules/admin_import_types/newsletter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5217, 'newsletter', 'themes/default/text/NEWSLETTER_WHATSNEW_RESOURCE_FCOMCODE.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5218, 'newsletter', 'themes/default/text/NEWSLETTER_WHATSNEW_SECTION_FCOMCODE.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5219, 'newsletter', 'themes/default/text/NEWSLETTER_WHATSNEW_FCOMCODE.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5220, 'newsletter', 'themes/default/templates/NEWSLETTER_CONFIRM_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5221, 'newsletter', 'themes/default/templates/NEWSLETTER_SUBSCRIBER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5222, 'newsletter', 'themes/default/templates/NEWSLETTER_SUBSCRIBERS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5223, 'newsletter', 'adminzone/pages/modules/admin_newsletter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5224, 'newsletter', 'lang/EN/newsletter.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5225, 'newsletter', 'sources/hooks/systems/config/newsletter_update_time.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5226, 'newsletter', 'sources/hooks/systems/commandr_fs/newsletters.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5227, 'newsletter', 'sources/hooks/systems/commandr_fs/periodic_newsletters.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5228, 'newsletter', 'sources/hooks/systems/commandr_fs/newsletter_subscribers.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5229, 'newsletter', 'sources/hooks/systems/resource_meta_aware/newsletter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5230, 'newsletter', 'sources/hooks/systems/resource_meta_aware/periodic_newsletter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5231, 'newsletter', 'sources/hooks/systems/resource_meta_aware/newsletter_subscriber.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5232, 'newsletter', 'site/pages/modules/newsletter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5233, 'newsletter', 'sources/blocks/main_newsletter_signup.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5234, 'newsletter', 'sources/hooks/modules/admin_import/newsletter_subscribers.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5235, 'newsletter', 'sources/hooks/blocks/main_staff_checklist/newsletter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5236, 'newsletter', 'sources/hooks/modules/admin_newsletter/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5237, 'newsletter', 'sources_custom/hooks/modules/admin_newsletter/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5238, 'newsletter', 'sources/hooks/systems/page_groupings/newsletter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5239, 'newsletter', 'sources/newsletter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5240, 'newsletter', 'sources/hooks/systems/config/max_newsletter_whatsnew.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5241, 'newsletter', 'sources/hooks/modules/admin_newsletter/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5242, 'newsletter', 'sources_custom/hooks/modules/admin_newsletter/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5243, 'newsletter', 'themes/default/css/newsletter.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5244, 'newsletter', 'themes/default/templates/BLOCK_MAIN_NEWSLETTER_SIGNUP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5245, 'newsletter', 'themes/default/templates/BLOCK_MAIN_NEWSLETTER_SIGNUP_DONE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5246, 'newsletter', 'sources/hooks/systems/config/dual_format_newsletters.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5247, 'newsletter', 'sources/hooks/systems/config/mails_per_send.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5248, 'newsletter', 'sources/hooks/systems/config/minutes_between_sends.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5249, 'newsletter', 'themes/default/templates/PERIODIC_NEWSLETTER_REMOVE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5250, 'newsletter', 'sources/hooks/systems/tasks/send_newsletter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5251, 'newsletter', 'data/incoming_bounced_email.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5252, 'page_management', 'themes/default/images/icons/24x24/menu/adminzone/structure/sitemap/page_delete.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5253, 'page_management', 'themes/default/images/icons/48x48/menu/adminzone/structure/sitemap/page_delete.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5254, 'page_management', 'themes/default/images/icons/24x24/menu/adminzone/structure/sitemap/page_move.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5255, 'page_management', 'themes/default/images/icons/48x48/menu/adminzone/structure/sitemap/page_move.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5256, 'page_management', 'themes/default/images/icons/24x24/menu/adminzone/structure/sitemap/sitemap_editor.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5257, 'page_management', 'themes/default/images/icons/48x48/menu/adminzone/structure/sitemap/sitemap_editor.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5258, 'page_management', 'sources/hooks/systems/addon_registry/page_management.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5259, 'page_management', 'themes/default/templates/WEBSTANDARDS_CHECK_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5260, 'page_management', 'themes/default/templates/WEBSTANDARDS_CHECK_ERROR.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5261, 'page_management', 'adminzone/pages/modules/admin_sitemap.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5262, 'page_management', 'themes/default/javascript/sitemap_editor.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5263, 'page_management', 'themes/default/templates/SITEMAP_EDITOR_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5264, 'phpinfo', 'themes/default/images/icons/24x24/menu/adminzone/tools/phpinfo.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5265, 'phpinfo', 'themes/default/images/icons/48x48/menu/adminzone/tools/phpinfo.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5266, 'phpinfo', 'themes/default/css/phpinfo.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5267, 'phpinfo', 'sources/hooks/systems/page_groupings/phpinfo.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5268, 'phpinfo', 'sources/hooks/systems/addon_registry/phpinfo.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5269, 'phpinfo', 'adminzone/pages/modules/admin_phpinfo.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5270, 'points', 'themes/default/images/icons/24x24/menu/social/points.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5271, 'points', 'themes/default/images/icons/48x48/menu/social/points.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5272, 'points', 'themes/default/images/icons/24x24/menu/adminzone/audit/points_log.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5273, 'points', 'themes/default/images/icons/24x24/menu/social/leader_board.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5274, 'points', 'themes/default/images/icons/48x48/menu/adminzone/audit/points_log.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5275, 'points', 'themes/default/images/icons/48x48/menu/social/leader_board.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5276, 'points', 'themes/default/images/icons/24x24/buttons/points.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5277, 'points', 'themes/default/images/icons/48x48/buttons/points.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5278, 'points', 'themes/default/templates/POINTS_PROFILE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5279, 'points', 'sources/hooks/systems/notifications/received_points.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5280, 'points', 'sources/hooks/systems/notifications/receive_points_staff.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5281, 'points', 'sources/hooks/systems/config/leader_board_start_date.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5282, 'points', 'sources/hooks/systems/config/leader_board_show_staff.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5283, 'points', 'sources/hooks/systems/config/leader_board_size.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5284, 'points', 'sources/hooks/systems/config/points_joining.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5285, 'points', 'sources/hooks/systems/config/points_per_daily_visit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5286, 'points', 'sources/hooks/systems/config/points_per_day.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5287, 'points', 'sources/hooks/systems/config/points_posting.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5288, 'points', 'sources/hooks/systems/config/points_rating.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5289, 'points', 'sources/hooks/systems/config/points_show_personal_stats_gift_points_left.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5290, 'points', 'sources/hooks/systems/config/points_show_personal_stats_gift_points_used.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5291, 'points', 'sources/hooks/systems/config/points_show_personal_stats_points_left.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5292, 'points', 'sources/hooks/systems/config/points_show_personal_stats_points_used.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5293, 'points', 'sources/hooks/systems/config/points_show_personal_stats_total_points.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5294, 'points', 'sources/hooks/systems/config/points_voting.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5295, 'points', 'sources/hooks/systems/realtime_rain/points.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5296, 'points', 'sources/hooks/systems/config_categories/points.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5297, 'points', 'sources/hooks/modules/admin_setupwizard/leader_board.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5298, 'points', 'sources/hooks/systems/addon_registry/points.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5299, 'points', 'sources/hooks/modules/admin_import_types/points.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5300, 'points', 'sources/hooks/systems/profiles_tabs/points.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5301, 'points', 'sources/points3.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5302, 'points', 'themes/default/templates/POINTS_GIVE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5303, 'points', 'themes/default/templates/POINTS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5304, 'points', 'themes/default/templates/POINTS_SEARCH_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5305, 'points', 'themes/default/templates/POINTS_SEARCH_RESULT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5306, 'points', 'themes/default/templates/POINTS_LEADER_BOARD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5307, 'points', 'themes/default/templates/POINTS_LEADER_BOARD_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5308, 'points', 'themes/default/templates/POINTS_LEADER_BOARD_ROW.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5309, 'points', 'themes/default/templates/POINTS_LEADER_BOARD_WEEK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5310, 'points', 'adminzone/pages/modules/admin_points.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5311, 'points', 'themes/default/css/points.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5312, 'points', 'lang/EN/points.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5313, 'points', 'site/pages/modules/points.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5314, 'points', 'sources/hooks/blocks/main_staff_checklist/points.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5315, 'points', 'sources/hooks/systems/page_groupings/points.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5316, 'points', 'sources/hooks/systems/cns_cpf_filter/points.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5317, 'points', 'sources/hooks/systems/rss/points.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5318, 'points', 'sources/points.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5319, 'points', 'sources/points2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5320, 'points', 'sources/hooks/systems/commandr_commands/give.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5321, 'points', 'site/pages/modules/leader_board.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5322, 'points', 'sources/blocks/main_leader_board.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5323, 'points', 'sources/hooks/systems/cron/leader_board.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5324, 'points', 'sources/leader_board.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5325, 'points', 'lang/EN/leader_board.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5326, 'points', 'sources/hooks/systems/config/points_per_currency_unit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5327, 'points', 'sources/hooks/systems/config/point_logs_per_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5328, 'points', 'sources/hooks/systems/config/points_if_liked.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5329, 'points', 'sources/hooks/systems/config/gift_reward_amount.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5330, 'points', 'sources/hooks/systems/config/gift_reward_chance.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5331, 'points', 'sources/hooks/systems/tasks/export_points_log.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5332, 'points', 'sources/hooks/systems/commandr_fs_extended_member/point_charges.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5333, 'points', 'sources/hooks/systems/commandr_fs_extended_member/point_gifts_given.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5334, 'pointstore', 'themes/default/images/icons/24x24/menu/social/pointstore.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5335, 'pointstore', 'themes/default/images/icons/48x48/menu/social/pointstore.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5336, 'pointstore', 'themes/default/images/icons/24x24/menu/adminzone/audit/pointstore_log.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5337, 'pointstore', 'themes/default/images/icons/48x48/menu/adminzone/audit/pointstore_log.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5338, 'pointstore', 'sources/hooks/systems/notifications/pointstore_request_custom.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5339, 'pointstore', 'sources/hooks/systems/notifications/pointstore_request_forwarding.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5340, 'pointstore', 'sources/hooks/systems/notifications/pointstore_request_pop3.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5341, 'pointstore', 'sources/hooks/systems/notifications/pointstore_request_quota.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5342, 'pointstore', 'sources/hooks/systems/config_categories/pointstore.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5343, 'pointstore', 'sources/hooks/systems/config/average_gamble_multiplier.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5344, 'pointstore', 'sources/hooks/systems/config/banner_hit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5345, 'pointstore', 'sources/hooks/systems/config/banner_imp.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5346, 'pointstore', 'sources/hooks/systems/config/banner_setup.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5347, 'pointstore', 'sources/hooks/systems/config/forw_url.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5348, 'pointstore', 'sources/hooks/systems/config/highlight_name.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5349, 'pointstore', 'sources/hooks/systems/config/initial_banner_hits.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5350, 'pointstore', 'sources/hooks/systems/config/initial_quota.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5351, 'pointstore', 'sources/hooks/systems/config/is_on_banner_buy.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5352, 'pointstore', 'sources/hooks/systems/config/is_on_forw_buy.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5353, 'pointstore', 'sources/hooks/systems/config/is_on_gambling_buy.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5354, 'pointstore', 'sources/hooks/systems/config/is_on_highlight_name_buy.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5355, 'pointstore', 'sources/hooks/systems/config/is_on_pop3_buy.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5356, 'pointstore', 'sources/hooks/systems/config/is_on_topic_pin_buy.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5357, 'pointstore', 'sources/hooks/systems/config/mail_server.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5358, 'pointstore', 'sources/hooks/systems/config/max_quota.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5359, 'pointstore', 'sources/hooks/systems/config/maximum_gamble_amount.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5360, 'pointstore', 'sources/hooks/systems/config/maximum_gamble_multiplier.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5361, 'pointstore', 'sources/hooks/systems/config/minimum_gamble_amount.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5362, 'pointstore', 'sources/hooks/systems/config/pop_url.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5363, 'pointstore', 'sources/hooks/systems/config/quota.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5364, 'pointstore', 'sources/hooks/systems/config/quota_url.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5365, 'pointstore', 'sources/hooks/systems/config/topic_pin.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5366, 'pointstore', 'sources/hooks/systems/addon_registry/pointstore.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5367, 'pointstore', 'sources/hooks/modules/admin_import_types/pointstore.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5368, 'pointstore', 'sources/hooks/systems/cron/topic_pin.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5369, 'pointstore', 'sources/hooks/systems/config/topic_pin_max_days.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5370, 'pointstore', 'themes/default/templates/POINTSTORE_PRICES_FORM_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5371, 'pointstore', 'themes/default/templates/POINTSTORE_CUSTOM.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5372, 'pointstore', 'themes/default/templates/POINTSTORE_CUSTOM_ITEM_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5373, 'pointstore', 'themes/default/templates/POINTSTORE_GAMBLING.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5374, 'pointstore', 'themes/default/templates/POINTSTORE_HIGHLIGHT_NAME.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5375, 'pointstore', 'themes/default/templates/POINTSTORE_HIGHLIGHT_NAME_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5376, 'pointstore', 'themes/default/templates/POINTSTORE_PERMISSION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5377, 'pointstore', 'themes/default/templates/POINTSTORE_TOPIC_PIN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5378, 'pointstore', 'themes/default/templates/POINTSTORE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5379, 'pointstore', 'themes/default/templates/POINTSTORE_CONFIRM_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5380, 'pointstore', 'themes/default/text/POINTSTORE_FORWARDER_MAIL.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5381, 'pointstore', 'themes/default/templates/POINTSTORE_LOG_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5382, 'pointstore', 'themes/default/templates/POINTSTORE_MAIL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5383, 'pointstore', 'themes/default/templates/POINTSTORE_MFORWARDING_LINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5384, 'pointstore', 'themes/default/templates/POINTSTORE_MPOP3_LINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5385, 'pointstore', 'themes/default/templates/POINTSTORE_POP3_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5386, 'pointstore', 'themes/default/templates/POINTSTORE_POP3_ACTIVATE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5387, 'pointstore', 'themes/default/text/POINTSTORE_POP3_MAIL.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5388, 'pointstore', 'themes/default/templates/POINTSTORE_POP3_QUOTA.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5389, 'pointstore', 'themes/default/templates/POINTSTORE_PRICE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5390, 'pointstore', 'themes/default/templates/POINTSTORE_QUOTA.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5391, 'pointstore', 'themes/default/text/POINTSTORE_QUOTA_MAIL.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5392, 'pointstore', 'adminzone/pages/modules/admin_pointstore.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5393, 'pointstore', 'lang/EN/pointstore.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5394, 'pointstore', 'site/pages/modules/pointstore.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5395, 'pointstore', 'sources/hooks/blocks/main_staff_checklist/pointstore.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5396, 'pointstore', 'sources/hooks/modules/pointstore/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5397, 'pointstore', 'sources_custom/hooks/modules/pointstore/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5398, 'pointstore', 'sources/hooks/modules/pointstore/custom.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5399, 'pointstore', 'sources/hooks/modules/pointstore/forwarding.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5400, 'pointstore', 'sources/hooks/modules/pointstore/gambling.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5401, 'pointstore', 'sources/hooks/modules/pointstore/highlight_name.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5402, 'pointstore', 'sources/hooks/modules/pointstore/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5403, 'pointstore', 'sources_custom/hooks/modules/pointstore/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5404, 'pointstore', 'sources/hooks/modules/pointstore/permission.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5405, 'pointstore', 'sources/hooks/modules/pointstore/pop3.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5406, 'pointstore', 'sources/hooks/modules/pointstore/topic_pin.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5407, 'pointstore', 'sources/hooks/systems/page_groupings/pointstore.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5408, 'pointstore', 'sources/pointstore.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5409, 'pointstore', 'sources/hooks/systems/commandr_fs_extended_config/pstore_customs.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5410, 'pointstore', 'sources/hooks/systems/commandr_fs_extended_config/pstore_permissions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5411, 'pointstore', 'sources/hooks/systems/commandr_fs_extended_config/pstore_prices.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5412, 'polls', 'themes/default/images/icons/24x24/menu/social/polls.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5413, 'polls', 'themes/default/images/icons/48x48/menu/social/polls.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5414, 'polls', 'sources/polls2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5415, 'polls', 'sources/hooks/systems/block_ui_renderers/polls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5416, 'polls', 'sources/hooks/systems/notifications/poll_chosen.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5417, 'polls', 'sources/hooks/systems/config/points_ADD_POLL.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5418, 'polls', 'sources/hooks/systems/config/points_CHOOSE_POLL.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5419, 'polls', 'sources/hooks/systems/config/poll_update_time.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5420, 'polls', 'sources/hooks/systems/realtime_rain/polls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5421, 'polls', 'themes/default/templates/BLOCK_MAIN_POLL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5422, 'polls', 'sources/hooks/systems/content_meta_aware/poll.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5423, 'polls', 'sources/hooks/systems/commandr_fs/polls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5424, 'polls', 'sources/hooks/systems/addon_registry/polls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5425, 'polls', 'sources/hooks/systems/preview/poll.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5426, 'polls', 'sources/hooks/modules/admin_setupwizard/polls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5427, 'polls', 'sources/hooks/modules/admin_import_types/polls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5428, 'polls', 'sources/hooks/systems/sitemap/poll.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5429, 'polls', 'themes/default/templates/POLL_BOX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5430, 'polls', 'themes/default/templates/POLL_ANSWER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5431, 'polls', 'themes/default/templates/POLL_ANSWER_RESULT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5432, 'polls', 'themes/default/templates/POLL_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5433, 'polls', 'themes/default/templates/POLL_LIST_ENTRY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5434, 'polls', 'themes/default/templates/POLL_RSS_SUMMARY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5435, 'polls', 'themes/default/css/polls.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5436, 'polls', 'cms/pages/modules/cms_polls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5437, 'polls', 'lang/EN/polls.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5438, 'polls', 'site/pages/modules/polls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5439, 'polls', 'sources/hooks/blocks/main_staff_checklist/polls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5440, 'polls', 'sources/hooks/modules/search/polls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5441, 'polls', 'sources/hooks/systems/page_groupings/polls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5442, 'polls', 'sources/hooks/systems/rss/polls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5443, 'polls', 'sources/hooks/systems/trackback/polls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5444, 'polls', 'sources/polls.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5445, 'polls', 'sources/blocks/main_poll.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5446, 'printer_friendly_block', 'themes/default/templates/BLOCK_SIDE_PRINTER_FRIENDLY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5447, 'printer_friendly_block', 'sources/blocks/side_printer_friendly.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5448, 'printer_friendly_block', 'sources/hooks/systems/addon_registry/printer_friendly_block.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5449, 'printer_friendly_block', 'sources/hooks/modules/admin_setupwizard/printer_friendly_block.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5450, 'quizzes', 'themes/default/images/icons/24x24/menu/cms/quiz/find_winners.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5451, 'quizzes', 'themes/default/images/icons/24x24/menu/cms/quiz/quiz_results.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5452, 'quizzes', 'themes/default/images/icons/48x48/menu/cms/quiz/find_winners.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5453, 'quizzes', 'themes/default/images/icons/48x48/menu/cms/quiz/quiz_results.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5454, 'quizzes', 'themes/default/images/icons/24x24/menu/rich_content/quiz.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5455, 'quizzes', 'themes/default/images/icons/48x48/menu/rich_content/quiz.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5456, 'quizzes', 'themes/default/images/icons/24x24/menu/cms/quiz/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5457, 'quizzes', 'themes/default/images/icons/48x48/menu/cms/quiz/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5458, 'quizzes', 'sources/hooks/systems/notifications/quiz_results.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5459, 'quizzes', 'sources/hooks/systems/config/points_ADD_QUIZ.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5460, 'quizzes', 'sources/hooks/systems/config/quiz_show_stats_count_total_open.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5461, 'quizzes', 'sources/hooks/systems/meta/quiz.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5462, 'quizzes', 'sources/hooks/blocks/side_stats/stats_quiz.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5463, 'quizzes', 'themes/default/templates/QUIZ_ARCHIVE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5464, 'quizzes', 'themes/default/text/QUIZ_SURVEY_ANSWERS_MAIL.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5465, 'quizzes', 'themes/default/text/QUIZ_TEST_ANSWERS_MAIL.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5466, 'quizzes', 'sources/hooks/systems/content_meta_aware/quiz.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5467, 'quizzes', 'sources/hooks/systems/commandr_fs/quizzes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5468, 'quizzes', 'sources/hooks/systems/addon_registry/quizzes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5469, 'quizzes', 'sources/hooks/modules/admin_import_types/quizzes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5470, 'quizzes', 'themes/default/templates/QUIZ_BOX.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5471, 'quizzes', 'themes/default/templates/QUIZ_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5472, 'quizzes', 'themes/default/templates/QUIZ_RESULTS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5473, 'quizzes', 'themes/default/templates/QUIZ_DONE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5474, 'quizzes', 'themes/default/templates/QUIZ_RESULT_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5475, 'quizzes', 'themes/default/templates/QUIZ_RESULTS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5476, 'quizzes', 'themes/default/templates/MEMBER_QUIZ_ENTRIES.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5477, 'quizzes', 'adminzone/pages/modules/admin_quiz.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5478, 'quizzes', 'cms/pages/modules/cms_quiz.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5479, 'quizzes', 'lang/EN/quiz.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5480, 'quizzes', 'site/pages/modules/quiz.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5481, 'quizzes', 'sources/hooks/systems/sitemap/quiz.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5482, 'quizzes', 'sources/hooks/modules/admin_newsletter/quiz.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5483, 'quizzes', 'sources/hooks/modules/admin_unvalidated/quiz.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5484, 'quizzes', 'sources/hooks/modules/search/quiz.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5485, 'quizzes', 'sources/hooks/modules/members/quiz.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5486, 'quizzes', 'sources/hooks/systems/page_groupings/quiz.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5487, 'quizzes', 'sources/quiz.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5488, 'quizzes', 'sources/quiz2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5489, 'quizzes', 'sources/hooks/systems/preview/quiz.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5490, 'quizzes', 'themes/default/css/quizzes.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5491, 'random_quotes', 'themes/default/images/icons/24x24/menu/adminzone/style/quotes.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5492, 'random_quotes', 'themes/default/images/icons/48x48/menu/adminzone/style/quotes.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5493, 'random_quotes', 'sources/hooks/blocks/main_notes/quotes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5494, 'random_quotes', 'sources/hooks/modules/admin_import_types/quotes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5495, 'random_quotes', 'sources/hooks/modules/admin_setupwizard/random_quotes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5496, 'random_quotes', 'sources/hooks/systems/addon_registry/random_quotes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5497, 'random_quotes', 'themes/default/templates/BLOCK_MAIN_QUOTES.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5498, 'random_quotes', 'adminzone/pages/comcode/EN/quotes.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5499, 'random_quotes', 'text/EN/quotes.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5500, 'random_quotes', 'lang/EN/quotes.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5501, 'random_quotes', 'sources/blocks/main_quotes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5502, 'random_quotes', 'sources/hooks/systems/page_groupings/quotes.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5503, 'random_quotes', 'themes/default/css/random_quotes.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5504, 'realtime_rain', 'themes/default/images/icons/24x24/menu/adminzone/audit/realtime_rain.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5505, 'realtime_rain', 'themes/default/images/icons/48x48/menu/adminzone/audit/realtime_rain.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5506, 'realtime_rain', 'themes/default/images/icons/24x24/tool_buttons/realtime_rain_off.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5507, 'realtime_rain', 'themes/default/images/icons/24x24/tool_buttons/realtime_rain_on.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5508, 'realtime_rain', 'themes/default/images/icons/48x48/tool_buttons/realtime_rain_off.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5509, 'realtime_rain', 'themes/default/images/icons/48x48/tool_buttons/realtime_rain_on.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5510, 'realtime_rain', 'adminzone/pages/modules/admin_realtime_rain.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5511, 'realtime_rain', 'sources/realtime_rain.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5512, 'realtime_rain', 'data/realtime_rain.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5513, 'realtime_rain', 'sources/hooks/systems/snippets/realtime_rain_load.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5514, 'realtime_rain', 'sources/hooks/systems/page_groupings/realtime_rain.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5515, 'realtime_rain', 'themes/default/css/realtime_rain.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5516, 'realtime_rain', 'themes/default/templates/REALTIME_RAIN_OVERLAY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5517, 'realtime_rain', 'themes/default/templates/REALTIME_RAIN_BUBBLE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5518, 'realtime_rain', 'themes/default/javascript/realtime_rain.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5519, 'realtime_rain', 'themes/default/javascript/button_realtime_rain.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5520, 'realtime_rain', 'lang/EN/realtime_rain.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5521, 'realtime_rain', 'themes/default/images/realtime_rain/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5522, 'realtime_rain', 'sources/hooks/systems/realtime_rain/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5523, 'realtime_rain', 'sources_custom/hooks/systems/realtime_rain/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5524, 'realtime_rain', 'sources/hooks/systems/realtime_rain/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5525, 'realtime_rain', 'sources_custom/hooks/systems/realtime_rain/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5526, 'realtime_rain', 'sources/hooks/systems/addon_registry/realtime_rain.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5527, 'realtime_rain', 'sources/hooks/systems/config/bottom_show_realtime_rain_button.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5528, 'realtime_rain', 'themes/default/images/realtime_rain/email-icon.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5529, 'realtime_rain', 'themes/default/images/realtime_rain/news-icon.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5530, 'realtime_rain', 'themes/default/images/realtime_rain/phone-icon.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5531, 'realtime_rain', 'themes/default/images/realtime_rain/searchengine-icon.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5532, 'realtime_rain', 'themes/default/images/realtime_rain/news-bg.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5533, 'realtime_rain', 'themes/default/images/realtime_rain/news-bot.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5534, 'realtime_rain', 'themes/default/images/realtime_rain/news-header.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5535, 'realtime_rain', 'themes/default/images/realtime_rain/news-out.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5536, 'realtime_rain', 'themes/default/images/realtime_rain/news-top.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5537, 'realtime_rain', 'themes/default/images/realtime_rain/next.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5538, 'realtime_rain', 'themes/default/images/realtime_rain/pause-but.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5539, 'realtime_rain', 'themes/default/images/realtime_rain/pre.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5540, 'realtime_rain', 'themes/default/images/realtime_rain/realtime-bubble.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5541, 'realtime_rain', 'themes/default/images/realtime_rain/time-line.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5542, 'realtime_rain', 'themes/default/images/realtime_rain/timer-bg.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5543, 'realtime_rain', 'themes/default/images/realtime_rain/actionlog-avatar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5544, 'realtime_rain', 'themes/default/images/realtime_rain/actionlog-bubble.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5545, 'realtime_rain', 'themes/default/images/realtime_rain/banners-avatar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5546, 'realtime_rain', 'themes/default/images/realtime_rain/banners-bubble.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5547, 'realtime_rain', 'themes/default/images/realtime_rain/calendar-avatar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5548, 'realtime_rain', 'themes/default/images/realtime_rain/calendar-bubble.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5549, 'realtime_rain', 'themes/default/images/realtime_rain/chat-avatar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5550, 'realtime_rain', 'themes/default/images/realtime_rain/chat-bubble.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5551, 'realtime_rain', 'themes/default/images/realtime_rain/ecommerce-avatar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5552, 'realtime_rain', 'themes/default/images/realtime_rain/ecommerce-bubble.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5553, 'realtime_rain', 'themes/default/images/realtime_rain/join-avatar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5554, 'realtime_rain', 'themes/default/images/realtime_rain/join-bubble.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5555, 'realtime_rain', 'themes/default/images/realtime_rain/news-avatar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5556, 'realtime_rain', 'themes/default/images/realtime_rain/news-bubble.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5557, 'realtime_rain', 'themes/default/images/realtime_rain/point_charges-avatar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5558, 'realtime_rain', 'themes/default/images/realtime_rain/point_charges-bubble.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5559, 'realtime_rain', 'themes/default/images/realtime_rain/point_gifts-avatar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5560, 'realtime_rain', 'themes/default/images/realtime_rain/point_gifts-bubble.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5561, 'realtime_rain', 'themes/default/images/realtime_rain/polls-avatar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5562, 'realtime_rain', 'themes/default/images/realtime_rain/polls-bubble.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5563, 'realtime_rain', 'themes/default/images/realtime_rain/post-avatar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5564, 'realtime_rain', 'themes/default/images/realtime_rain/post-bubble.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5565, 'realtime_rain', 'themes/default/images/realtime_rain/recommend-avatar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5566, 'realtime_rain', 'themes/default/images/realtime_rain/recommend-bubble.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5567, 'realtime_rain', 'themes/default/images/realtime_rain/search-avatar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5568, 'realtime_rain', 'themes/default/images/realtime_rain/search-bubble.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5569, 'realtime_rain', 'themes/default/images/realtime_rain/security-avatar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5570, 'realtime_rain', 'themes/default/images/realtime_rain/security-bubble.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5571, 'realtime_rain', 'themes/default/images/realtime_rain/stats-avatar.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5572, 'realtime_rain', 'themes/default/images/realtime_rain/stats-bubble.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5573, 'realtime_rain', 'themes/default/images/realtime_rain/sun-effect.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5574, 'realtime_rain', 'themes/default/images/realtime_rain/halo-effect.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5575, 'realtime_rain', 'themes/default/images/realtime_rain/horns-effect.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5576, 'realtime_rain', 'themes/default/images/realtime_rain/shadow-effect.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5577, 'realtime_rain', 'themes/default/images/flags/AD.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5578, 'realtime_rain', 'themes/default/images/flags/AE.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5579, 'realtime_rain', 'themes/default/images/flags/AF.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5580, 'realtime_rain', 'themes/default/images/flags/AG.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5581, 'realtime_rain', 'themes/default/images/flags/AL.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5582, 'realtime_rain', 'themes/default/images/flags/AM.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5583, 'realtime_rain', 'themes/default/images/flags/AO.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5584, 'realtime_rain', 'themes/default/images/flags/AR.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5585, 'realtime_rain', 'themes/default/images/flags/AT.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5586, 'realtime_rain', 'themes/default/images/flags/AU.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5587, 'realtime_rain', 'themes/default/images/flags/AZ.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5588, 'realtime_rain', 'themes/default/images/flags/BA.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5589, 'realtime_rain', 'themes/default/images/flags/BB.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5590, 'realtime_rain', 'themes/default/images/flags/BD.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5591, 'realtime_rain', 'themes/default/images/flags/BE.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5592, 'realtime_rain', 'themes/default/images/flags/BF.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5593, 'realtime_rain', 'themes/default/images/flags/BG.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5594, 'realtime_rain', 'themes/default/images/flags/BH.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5595, 'realtime_rain', 'themes/default/images/flags/BI.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5596, 'realtime_rain', 'themes/default/images/flags/BJ.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5597, 'realtime_rain', 'themes/default/images/flags/BN.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5598, 'realtime_rain', 'themes/default/images/flags/BO.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5599, 'realtime_rain', 'themes/default/images/flags/BR.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5600, 'realtime_rain', 'themes/default/images/flags/BS.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5601, 'realtime_rain', 'themes/default/images/flags/BT.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5602, 'realtime_rain', 'themes/default/images/flags/BW.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5603, 'realtime_rain', 'themes/default/images/flags/BY.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5604, 'realtime_rain', 'themes/default/images/flags/BZ.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5605, 'realtime_rain', 'themes/default/images/flags/CA.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5606, 'realtime_rain', 'themes/default/images/flags/CD.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5607, 'realtime_rain', 'themes/default/images/flags/CF.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5608, 'realtime_rain', 'themes/default/images/flags/CH.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5609, 'realtime_rain', 'themes/default/images/flags/CI.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5610, 'realtime_rain', 'themes/default/images/flags/CL.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5611, 'realtime_rain', 'themes/default/images/flags/CM.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5612, 'realtime_rain', 'themes/default/images/flags/CN.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5613, 'realtime_rain', 'themes/default/images/flags/CO.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5614, 'realtime_rain', 'themes/default/images/flags/CR.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5615, 'realtime_rain', 'themes/default/images/flags/CU.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5616, 'realtime_rain', 'themes/default/images/flags/CV.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5617, 'realtime_rain', 'themes/default/images/flags/CY.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5618, 'realtime_rain', 'themes/default/images/flags/CZ.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5619, 'realtime_rain', 'themes/default/images/flags/DE.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5620, 'realtime_rain', 'themes/default/images/flags/DJ.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5621, 'realtime_rain', 'themes/default/images/flags/DK.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5622, 'realtime_rain', 'themes/default/images/flags/DM.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5623, 'realtime_rain', 'themes/default/images/flags/DO.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5624, 'realtime_rain', 'themes/default/images/flags/DZ.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5625, 'realtime_rain', 'themes/default/images/flags/EC.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5626, 'realtime_rain', 'themes/default/images/flags/EE.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5627, 'realtime_rain', 'themes/default/images/flags/EG.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5628, 'realtime_rain', 'themes/default/images/flags/EH.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5629, 'realtime_rain', 'themes/default/images/flags/ER.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5630, 'realtime_rain', 'themes/default/images/flags/ES.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5631, 'realtime_rain', 'themes/default/images/flags/ET.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5632, 'realtime_rain', 'themes/default/images/flags/FI.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5633, 'realtime_rain', 'themes/default/images/flags/FJ.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5634, 'realtime_rain', 'themes/default/images/flags/FM.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5635, 'realtime_rain', 'themes/default/images/flags/FR.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5636, 'realtime_rain', 'themes/default/images/flags/GA.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5637, 'realtime_rain', 'themes/default/images/flags/GB.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5638, 'realtime_rain', 'themes/default/images/flags/GD.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5639, 'realtime_rain', 'themes/default/images/flags/GE.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5640, 'realtime_rain', 'themes/default/images/flags/GH.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5641, 'realtime_rain', 'themes/default/images/flags/GI.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5642, 'realtime_rain', 'themes/default/images/flags/GL.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5643, 'realtime_rain', 'themes/default/images/flags/GM.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5644, 'realtime_rain', 'themes/default/images/flags/GN.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5645, 'realtime_rain', 'themes/default/images/flags/GP.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5646, 'realtime_rain', 'themes/default/images/flags/GQ.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5647, 'realtime_rain', 'themes/default/images/flags/GR.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5648, 'realtime_rain', 'themes/default/images/flags/GT.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5649, 'realtime_rain', 'themes/default/images/flags/GW.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5650, 'realtime_rain', 'themes/default/images/flags/GY.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5651, 'realtime_rain', 'themes/default/images/flags/HN.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5652, 'realtime_rain', 'themes/default/images/flags/HR.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5653, 'realtime_rain', 'themes/default/images/flags/HT.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5654, 'realtime_rain', 'themes/default/images/flags/HU.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5655, 'realtime_rain', 'themes/default/images/flags/ID.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5656, 'realtime_rain', 'themes/default/images/flags/IE.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5657, 'realtime_rain', 'themes/default/images/flags/IL.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5658, 'realtime_rain', 'themes/default/images/flags/IN.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5659, 'realtime_rain', 'themes/default/images/flags/IQ.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5660, 'realtime_rain', 'themes/default/images/flags/IR.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5661, 'realtime_rain', 'themes/default/images/flags/IS.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5662, 'realtime_rain', 'themes/default/images/flags/IT.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5663, 'realtime_rain', 'themes/default/images/flags/JM.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5664, 'realtime_rain', 'themes/default/images/flags/JO.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5665, 'realtime_rain', 'themes/default/images/flags/JP.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5666, 'realtime_rain', 'themes/default/images/flags/KE.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5667, 'realtime_rain', 'themes/default/images/flags/KG.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5668, 'realtime_rain', 'themes/default/images/flags/KH.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5669, 'realtime_rain', 'themes/default/images/flags/KI.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5670, 'realtime_rain', 'themes/default/images/flags/KM.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5671, 'realtime_rain', 'themes/default/images/flags/KN.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5672, 'realtime_rain', 'themes/default/images/flags/KP.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5673, 'realtime_rain', 'themes/default/images/flags/KR.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5674, 'realtime_rain', 'themes/default/images/flags/KW.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5675, 'realtime_rain', 'themes/default/images/flags/KZ.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5676, 'realtime_rain', 'themes/default/images/flags/LA.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5677, 'realtime_rain', 'themes/default/images/flags/LB.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5678, 'realtime_rain', 'themes/default/images/flags/LC.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5679, 'realtime_rain', 'themes/default/images/flags/LI.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5680, 'realtime_rain', 'themes/default/images/flags/LK.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5681, 'realtime_rain', 'themes/default/images/flags/LR.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5682, 'realtime_rain', 'themes/default/images/flags/LS.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5683, 'realtime_rain', 'themes/default/images/flags/LT.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5684, 'realtime_rain', 'themes/default/images/flags/LU.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5685, 'realtime_rain', 'themes/default/images/flags/LV.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5686, 'realtime_rain', 'themes/default/images/flags/LY.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5687, 'realtime_rain', 'themes/default/images/flags/MA.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5688, 'realtime_rain', 'themes/default/images/flags/MC.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5689, 'realtime_rain', 'themes/default/images/flags/MD.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5690, 'realtime_rain', 'themes/default/images/flags/ME.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5691, 'realtime_rain', 'themes/default/images/flags/MG.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5692, 'realtime_rain', 'themes/default/images/flags/MH.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5693, 'realtime_rain', 'themes/default/images/flags/MK.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5694, 'realtime_rain', 'themes/default/images/flags/ML.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5695, 'realtime_rain', 'themes/default/images/flags/MM.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5696, 'realtime_rain', 'themes/default/images/flags/MN.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5697, 'realtime_rain', 'themes/default/images/flags/MQ.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5698, 'realtime_rain', 'themes/default/images/flags/MR.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5699, 'realtime_rain', 'themes/default/images/flags/MT.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5700, 'realtime_rain', 'themes/default/images/flags/MU.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5701, 'realtime_rain', 'themes/default/images/flags/MV.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5702, 'realtime_rain', 'themes/default/images/flags/MW.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5703, 'realtime_rain', 'themes/default/images/flags/MX.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5704, 'realtime_rain', 'themes/default/images/flags/MY.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5705, 'realtime_rain', 'themes/default/images/flags/MZ.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5706, 'realtime_rain', 'themes/default/images/flags/NA.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5707, 'realtime_rain', 'themes/default/images/flags/NE.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5708, 'realtime_rain', 'themes/default/images/flags/NG.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5709, 'realtime_rain', 'themes/default/images/flags/NI.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5710, 'realtime_rain', 'themes/default/images/flags/NL.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5711, 'realtime_rain', 'themes/default/images/flags/NO.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5712, 'realtime_rain', 'themes/default/images/flags/NP.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5713, 'realtime_rain', 'themes/default/images/flags/NR.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5714, 'realtime_rain', 'themes/default/images/flags/NZ.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5715, 'realtime_rain', 'themes/default/images/flags/OM.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5716, 'realtime_rain', 'themes/default/images/flags/PA.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5717, 'realtime_rain', 'themes/default/images/flags/PE.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5718, 'realtime_rain', 'themes/default/images/flags/PF.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5719, 'realtime_rain', 'themes/default/images/flags/PG.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5720, 'realtime_rain', 'themes/default/images/flags/PH.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5721, 'realtime_rain', 'themes/default/images/flags/PK.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5722, 'realtime_rain', 'themes/default/images/flags/PL.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5723, 'realtime_rain', 'themes/default/images/flags/PR.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5724, 'realtime_rain', 'themes/default/images/flags/PS.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5725, 'realtime_rain', 'themes/default/images/flags/PT.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5726, 'realtime_rain', 'themes/default/images/flags/PW.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5727, 'realtime_rain', 'themes/default/images/flags/PY.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5728, 'realtime_rain', 'themes/default/images/flags/QA.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5729, 'realtime_rain', 'themes/default/images/flags/RO.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5730, 'realtime_rain', 'themes/default/images/flags/RS.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5731, 'realtime_rain', 'themes/default/images/flags/RU.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5732, 'realtime_rain', 'themes/default/images/flags/RW.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5733, 'realtime_rain', 'themes/default/images/flags/SA.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5734, 'realtime_rain', 'themes/default/images/flags/SB.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5735, 'realtime_rain', 'themes/default/images/flags/SC.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5736, 'realtime_rain', 'themes/default/images/flags/SD.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5737, 'realtime_rain', 'themes/default/images/flags/SE.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5738, 'realtime_rain', 'themes/default/images/flags/SG.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5739, 'realtime_rain', 'themes/default/images/flags/SI.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5740, 'realtime_rain', 'themes/default/images/flags/SK.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5741, 'realtime_rain', 'themes/default/images/flags/SL.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5742, 'realtime_rain', 'themes/default/images/flags/SM.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5743, 'realtime_rain', 'themes/default/images/flags/SN.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5744, 'realtime_rain', 'themes/default/images/flags/SO.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5745, 'realtime_rain', 'themes/default/images/flags/SR.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5746, 'realtime_rain', 'themes/default/images/flags/ST.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5747, 'realtime_rain', 'themes/default/images/flags/SV.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5748, 'realtime_rain', 'themes/default/images/flags/SY.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5749, 'realtime_rain', 'themes/default/images/flags/SZ.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5750, 'realtime_rain', 'themes/default/images/flags/TD.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5751, 'realtime_rain', 'themes/default/images/flags/TG.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5752, 'realtime_rain', 'themes/default/images/flags/TH.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5753, 'realtime_rain', 'themes/default/images/flags/TJ.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5754, 'realtime_rain', 'themes/default/images/flags/TM.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5755, 'realtime_rain', 'themes/default/images/flags/TN.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5756, 'realtime_rain', 'themes/default/images/flags/TO.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5757, 'realtime_rain', 'themes/default/images/flags/TP.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5758, 'realtime_rain', 'themes/default/images/flags/TR.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5759, 'realtime_rain', 'themes/default/images/flags/TT.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5760, 'realtime_rain', 'themes/default/images/flags/TV.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5761, 'realtime_rain', 'themes/default/images/flags/TW.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5762, 'realtime_rain', 'themes/default/images/flags/TZ.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5763, 'realtime_rain', 'themes/default/images/flags/UA.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5764, 'realtime_rain', 'themes/default/images/flags/UG.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5765, 'realtime_rain', 'themes/default/images/flags/US.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5766, 'realtime_rain', 'themes/default/images/flags/UY.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5767, 'realtime_rain', 'themes/default/images/flags/UZ.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5768, 'realtime_rain', 'themes/default/images/flags/VA.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5769, 'realtime_rain', 'themes/default/images/flags/VC.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5770, 'realtime_rain', 'themes/default/images/flags/VE.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5771, 'realtime_rain', 'themes/default/images/flags/VG.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5772, 'realtime_rain', 'themes/default/images/flags/VI.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5773, 'realtime_rain', 'themes/default/images/flags/VN.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5774, 'realtime_rain', 'themes/default/images/flags/VU.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5775, 'realtime_rain', 'themes/default/images/flags/WS.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5776, 'realtime_rain', 'themes/default/images/flags/YE.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5777, 'realtime_rain', 'themes/default/images/flags/ZA.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5778, 'realtime_rain', 'themes/default/images/flags/ZM.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5779, 'realtime_rain', 'themes/default/images/flags/ZW.gif');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5780, 'realtime_rain', 'themes/default/images/flags/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5781, 'recommend', 'themes/default/images/icons/24x24/menu/site_meta/recommend.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5782, 'recommend', 'themes/default/images/icons/48x48/menu/site_meta/recommend.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5783, 'recommend', 'themes/default/images/icons/24x24/links/digg.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5784, 'recommend', 'themes/default/images/icons/24x24/links/favorites.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5785, 'recommend', 'themes/default/images/icons/48x48/links/digg.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5786, 'recommend', 'themes/default/images/icons/48x48/links/favorites.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5787, 'recommend', 'sources/hooks/systems/config/points_RECOMMEND_SITE.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5788, 'recommend', 'sources/hooks/systems/realtime_rain/recommend.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5789, 'recommend', 'sources/hooks/systems/addon_registry/recommend.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5790, 'recommend', 'lang/EN/recommend.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5791, 'recommend', 'pages/modules/recommend.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5792, 'recommend', 'pages/comcode/EN/recommend_help.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5793, 'recommend', 'sources/recommend.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5794, 'recommend', 'sources/blocks/main_screen_actions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5795, 'recommend', 'themes/default/css/screen_actions.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5796, 'recommend', 'themes/default/templates/BLOCK_MAIN_SCREEN_ACTIONS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5797, 'recommend', 'sources/hooks/systems/config/enable_csv_recommend.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5798, 'recommend', 'sources/hooks/systems/page_groupings/recommend.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5799, 'recommend', 'themes/default/css/recommend.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5800, 'redirects_editor', 'themes/default/images/icons/24x24/menu/adminzone/structure/redirects.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5801, 'redirects_editor', 'themes/default/images/icons/48x48/menu/adminzone/structure/redirects.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5802, 'redirects_editor', 'sources/hooks/systems/addon_registry/redirects_editor.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5803, 'redirects_editor', 'sources/hooks/systems/commandr_fs_extended_config/redirects.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5804, 'redirects_editor', 'themes/default/templates/REDIRECTE_TABLE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5805, 'redirects_editor', 'themes/default/templates/REDIRECTE_TABLE_REDIRECT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5806, 'redirects_editor', 'adminzone/pages/modules/admin_redirects.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5807, 'redirects_editor', 'lang/EN/redirects.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5808, 'redirects_editor', 'themes/default/css/redirects_editor.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5809, 'rootkit_detector', 'sources/hooks/systems/addon_registry/rootkit_detector.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5810, 'rootkit_detector', 'rootkit_detection.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5811, 'search', 'themes/default/images/icons/24x24/menu/adminzone/audit/statistics/search.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5812, 'search', 'themes/default/images/icons/48x48/menu/adminzone/audit/statistics/search.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5813, 'search', 'sources/hooks/systems/realtime_rain/search.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5814, 'search', 'themes/default/templates/TAGS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5815, 'search', 'sources/blocks/side_tag_cloud.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5816, 'search', 'sources/hooks/systems/sitemap/search.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5817, 'search', 'themes/default/templates/BLOCK_SIDE_TAG_CLOUD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5818, 'search', 'themes/default/templates/SEARCH_RESULT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5819, 'search', 'themes/default/templates/SEARCH_RESULT_TABLE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5820, 'search', 'sources/hooks/systems/addon_registry/search.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5821, 'search', 'sources/hooks/modules/admin_stats/search.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5822, 'search', 'sources/hooks/modules/admin_setupwizard/search.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5823, 'search', 'themes/default/templates/SEARCH_ADVANCED.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5824, 'search', 'themes/default/templates/BLOCK_MAIN_SEARCH.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5825, 'search', 'themes/default/templates/BLOCK_TOP_SEARCH.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5826, 'search', 'themes/default/templates/SEARCH_DOMAINS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5827, 'search', 'themes/default/templates/SEARCH_FORM_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5828, 'search', 'themes/default/templates/SEARCH_FOR_SEARCH_DOMAIN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5829, 'search', 'themes/default/templates/SEARCH_FOR_SEARCH_DOMAIN_OPTION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5830, 'search', 'themes/default/templates/SEARCH_FOR_SEARCH_DOMAIN_OPTION_LIST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5831, 'search', 'themes/default/templates/SEARCH_FOR_SEARCH_DOMAIN_OPTION_MULTI_LIST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5832, 'search', 'themes/default/templates/SEARCH_FOR_SEARCH_DOMAIN_OPTION_TEXT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5833, 'search', 'themes/default/templates/SEARCH_FOR_SEARCH_DOMAIN_OPTION_TICK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5834, 'search', 'themes/default/templates/SEARCH_FOR_SEARCH_DOMAIN_OPTION_FLOAT.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5835, 'search', 'themes/default/templates/SEARCH_FOR_SEARCH_DOMAIN_OPTION_INTEGER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5836, 'search', 'themes/default/templates/SEARCH_FOR_SEARCH_DOMAIN_OPTION_DATE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5837, 'search', 'themes/default/templates/SEARCH_FOR_SEARCH_DOMAIN_OPTION_JUST_DATE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5838, 'search', 'themes/default/templates/SEARCH_FOR_SEARCH_DOMAIN_OPTION_JUST_TIME.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5839, 'search', 'themes/default/css/search.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5840, 'search', 'lang/EN/search.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5841, 'search', 'site/pages/modules/search.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5842, 'search', 'sources/search.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5843, 'search', 'sources/blocks/main_search.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5844, 'search', 'sources/blocks/top_search.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5845, 'search', 'sources/hooks/modules/search/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5846, 'search', 'sources_custom/hooks/modules/search/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5847, 'search', 'sources/hooks/modules/search/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5848, 'search', 'sources_custom/hooks/modules/search/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5849, 'search', 'themes/default/xml/OPENSEARCH.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5850, 'search', 'data/opensearch.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5851, 'search', 'sources/hooks/systems/config/search_results_per_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5852, 'search', 'sources/hooks/systems/config/search_with_date_range.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5853, 'search', 'sources/hooks/systems/config/enable_boolean_search.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5854, 'search', 'sources/hooks/systems/page_groupings/search.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5855, 'search', 'sources/hooks/systems/config/minimum_autocomplete_length.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5856, 'search', 'sources/hooks/systems/config/maximum_autocomplete_suggestions.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5857, 'search', 'sources/hooks/systems/config/minimum_autocomplete_past_search.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5858, 'search', 'sources/hooks/systems/commandr_fs_extended_member/searches_saved.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5859, 'search', 'sources/hooks/systems/config/block_top_search.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5860, 'securitylogging', 'themes/default/images/icons/24x24/menu/adminzone/audit/security_log.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5861, 'securitylogging', 'themes/default/images/icons/48x48/menu/adminzone/audit/security_log.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5862, 'securitylogging', 'themes/default/images/icons/24x24/menu/adminzone/tools/users/investigate_user.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5863, 'securitylogging', 'themes/default/images/icons/48x48/menu/adminzone/tools/users/investigate_user.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5864, 'securitylogging', 'themes/default/images/icons/24x24/menu/adminzone/security/ip_ban.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5865, 'securitylogging', 'themes/default/images/icons/48x48/menu/adminzone/security/ip_ban.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5866, 'securitylogging', 'sources/hooks/systems/realtime_rain/security.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5867, 'securitylogging', 'sources/hooks/systems/addon_registry/securitylogging.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5868, 'securitylogging', 'themes/default/templates/SECURITY_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5869, 'securitylogging', 'themes/default/templates/SECURITY_ALERT_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5870, 'securitylogging', 'adminzone/pages/modules/admin_security.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5871, 'securitylogging', 'themes/default/text/HACK_ATTEMPT_MAIL.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5872, 'securitylogging', 'adminzone/pages/modules/admin_ip_ban.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5873, 'securitylogging', 'lang/EN/lookup.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5874, 'securitylogging', 'lang/EN/security.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5875, 'securitylogging', 'lang/EN/submitban.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5876, 'securitylogging', 'adminzone/pages/modules/admin_lookup.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5877, 'securitylogging', 'sources/lookup.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5878, 'securitylogging', 'sources/hooks/systems/commandr_fs_extended_member/banned_from_submitting.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5879, 'securitylogging', 'sources/hooks/systems/commandr_fs_extended_config/ip_banned.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5880, 'securitylogging', 'sources/hooks/systems/commandr_fs_extended_config/ip_unbannable.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5881, 'setupwizard', 'themes/default/images/icons/24x24/menu/adminzone/setup/setupwizard.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5882, 'setupwizard', 'themes/default/images/icons/48x48/menu/adminzone/setup/setupwizard.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5883, 'setupwizard', 'sources/hooks/modules/admin_setupwizard_installprofiles/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5884, 'setupwizard', 'sources_custom/hooks/modules/admin_setupwizard_installprofiles/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5885, 'setupwizard', 'sources/hooks/modules/admin_setupwizard_installprofiles/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5886, 'setupwizard', 'sources_custom/hooks/modules/admin_setupwizard_installprofiles/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5887, 'setupwizard', 'sources/hooks/modules/admin_setupwizard_installprofiles/community.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5888, 'setupwizard', 'sources/hooks/modules/admin_setupwizard_installprofiles/infosite.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5889, 'setupwizard', 'themes/default/templates/SETUPWIZARD_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5890, 'setupwizard', 'themes/default/templates/SETUPWIZARD_2.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5891, 'setupwizard', 'themes/default/templates/SETUPWIZARD_7.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5892, 'setupwizard', 'themes/default/templates/SETUPWIZARD_BLOCK_PREVIEW.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5893, 'setupwizard', 'sources/hooks/systems/addon_registry/setupwizard.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5894, 'setupwizard', 'sources/setupwizard.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5895, 'setupwizard', 'sources/hooks/systems/preview/setupwizard.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5896, 'setupwizard', 'sources/hooks/systems/preview/setupwizard_blocks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5897, 'setupwizard', 'adminzone/pages/modules/admin_setupwizard.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5898, 'setupwizard', 'sources/hooks/modules/admin_setupwizard/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5899, 'setupwizard', 'sources_custom/hooks/modules/admin_setupwizard/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5900, 'setupwizard', 'sources/hooks/modules/admin_setupwizard/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5901, 'setupwizard', 'sources_custom/hooks/modules/admin_setupwizard/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5902, 'setupwizard', 'sources/hooks/systems/page_groupings/setupwizard.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5903, 'setupwizard', 'sources/hooks/modules/admin_setupwizard/core.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5904, 'setupwizard', 'sources/hooks/modules/admin_setupwizard_installprofiles/minimalistic.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5905, 'setupwizard', 'themes/default/css/setupwizard.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5906, 'shopping', 'themes/default/images/icons/24x24/menu/adminzone/audit/ecommerce/orders.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5907, 'shopping', 'themes/default/images/icons/48x48/menu/adminzone/audit/ecommerce/orders.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5908, 'shopping', 'themes/default/images/icons/24x24/menu/rich_content/ecommerce/orders.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5909, 'shopping', 'themes/default/images/icons/48x48/menu/rich_content/ecommerce/orders.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5910, 'shopping', 'themes/default/images/icons/24x24/menu/adminzone/audit/ecommerce/undispatched_orders.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5911, 'shopping', 'themes/default/images/icons/48x48/menu/adminzone/audit/ecommerce/undispatched_orders.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5912, 'shopping', 'themes/default/images/icons/24x24/menu/rich_content/ecommerce/shopping_cart.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5913, 'shopping', 'themes/default/images/icons/48x48/menu/rich_content/ecommerce/shopping_cart.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5914, 'shopping', 'themes/default/images/icons/24x24/buttons/cart_add.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5915, 'shopping', 'themes/default/images/icons/24x24/buttons/cart_checkout.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5916, 'shopping', 'themes/default/images/icons/24x24/buttons/cart_empty.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5917, 'shopping', 'themes/default/images/icons/24x24/buttons/cart_update.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5918, 'shopping', 'themes/default/images/icons/24x24/buttons/cart_view.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5919, 'shopping', 'themes/default/images/icons/48x48/buttons/cart_add.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5920, 'shopping', 'themes/default/images/icons/48x48/buttons/cart_checkout.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5921, 'shopping', 'themes/default/images/icons/48x48/buttons/cart_empty.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5922, 'shopping', 'themes/default/images/icons/48x48/buttons/cart_update.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5923, 'shopping', 'themes/default/images/icons/48x48/buttons/cart_view.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5924, 'shopping', 'sources/hooks/systems/notifications/order_dispatched.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5925, 'shopping', 'sources/hooks/systems/notifications/new_order.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5926, 'shopping', 'sources/hooks/systems/notifications/low_stock.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5927, 'shopping', 'sources/hooks/modules/admin_setupwizard_installprofiles/shopping.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5928, 'shopping', 'sources/hooks/systems/config/allow_opting_out_of_tax.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5929, 'shopping', 'sources/hooks/systems/config/shipping_cost_factor.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5930, 'shopping', 'sources/hooks/systems/config/cart_hold_hours.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5931, 'shopping', 'sources/hooks/systems/ecommerce/catalogue_items.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5932, 'shopping', 'sources/hooks/systems/ecommerce/cart_orders.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5933, 'shopping', 'sources/hooks/blocks/main_staff_checklist/ecommerce_orders.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5934, 'shopping', 'sources/shopping.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5935, 'shopping', 'site/pages/modules/shopping.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5936, 'shopping', 'themes/default/templates/CATALOGUE_products_CATEGORY_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5937, 'shopping', 'themes/default/templates/CATALOGUE_products_CATEGORY_EMBED.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5938, 'shopping', 'themes/default/templates/CATALOGUE_products_ENTRY_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5939, 'shopping', 'themes/default/templates/CATALOGUE_products_GRID_ENTRY_FIELD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5940, 'shopping', 'themes/default/templates/CATALOGUE_products_FIELDMAP_ENTRY_FIELD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5941, 'shopping', 'themes/default/templates/CATALOGUE_products_GRID_ENTRY_WRAP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5942, 'shopping', 'themes/default/templates/RESULTS_products_TABLE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5943, 'shopping', 'themes/default/javascript/shopping.js');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5944, 'shopping', 'themes/default/templates/ECOM_SHOPPING_CART_BUTTONS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5945, 'shopping', 'adminzone/pages/modules/admin_orders.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5946, 'shopping', 'lang/EN/shopping.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5947, 'shopping', 'sources/hooks/systems/addon_registry/shopping.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5948, 'shopping', 'sources/hooks/systems/cns_cpf_filter/shopping_cart.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5949, 'shopping', 'themes/default/css/shopping.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5950, 'shopping', 'themes/default/templates/ECOM_ADMIN_ORDER_ACTIONS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5951, 'shopping', 'themes/default/templates/ECOM_CART_LINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5952, 'shopping', 'themes/default/templates/ECOM_ADMIN_ORDERS_DETAILS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5953, 'shopping', 'themes/default/templates/ECOM_ADMIN_ORDERS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5954, 'shopping', 'themes/default/templates/ECOM_ORDERS_DETAILS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5955, 'shopping', 'themes/default/templates/ECOM_ORDERS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5956, 'shopping', 'themes/default/templates/ECOM_SHIPPING_ADDRESS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5957, 'shopping', 'themes/default/templates/ECOM_CART_BUTTON_VIA_PAYPAL.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5958, 'shopping', 'themes/default/templates/ECOM_ITEM_DETAILS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5959, 'shopping', 'themes/default/templates/ECOM_SHOPPING_CART_PROCEED.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5960, 'shopping', 'themes/default/templates/ECOM_SHOPPING_CART_STAGE_PAY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5961, 'shopping', 'themes/default/templates/ECOM_SHOPPING_CART_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5962, 'shopping', 'themes/default/templates/ECOM_SHOPPING_ITEM_QUANTITY_FIELD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5963, 'shopping', 'themes/default/templates/ECOM_SHOPPING_ITEM_REMOVE_FIELD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5964, 'shopping', 'themes/default/templates/RESULTS_cart_TABLE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5965, 'shopping', 'themes/default/templates/RESULTS_TABLE_cart_FIELD.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5966, 'shopping', 'sources/hooks/systems/symbols/STOCK_CHECK.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5967, 'shopping', 'sources/hooks/systems/symbols/CART_LINK.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5968, 'sms', 'sources/hooks/systems/addon_registry/sms.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5969, 'sms', 'sources/sms.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5970, 'sms', 'lang/EN/sms.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5971, 'sms', 'sources/hooks/systems/config/sms_password.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5972, 'sms', 'sources/hooks/systems/config/sms_username.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5973, 'sms', 'sources/hooks/systems/config/sms_low_limit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5974, 'sms', 'sources/hooks/systems/config/sms_high_limit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5975, 'sms', 'sources/hooks/systems/config/sms_low_trigger_limit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5976, 'sms', 'sources/hooks/systems/config/sms_high_trigger_limit.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5977, 'sms', 'sources/hooks/systems/config/sms_api_id.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5978, 'sms', 'sources/hooks/systems/cns_cpf_filter/sms.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5979, 'sms', 'data/sms.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5980, 'ssl', 'themes/default/images/icons/24x24/menu/adminzone/security/ssl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5981, 'ssl', 'themes/default/images/icons/48x48/menu/adminzone/security/ssl.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5982, 'ssl', 'sources/hooks/systems/addon_registry/ssl.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5983, 'ssl', 'themes/default/templates/SSL_CONFIGURATION_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5984, 'ssl', 'adminzone/pages/modules/admin_ssl.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5985, 'ssl', 'sources/hooks/systems/page_groupings/ssl.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5986, 'ssl', 'lang/EN/ssl.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5987, 'ssl', 'sources/hooks/systems/commandr_fs_extended_config/https_settings.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5988, 'staff', 'themes/default/images/icons/24x24/menu/site_meta/staff.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5989, 'staff', 'themes/default/images/icons/48x48/menu/site_meta/staff.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5990, 'staff', 'sources/hooks/systems/config/is_on_staff_filter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5991, 'staff', 'sources/hooks/systems/config/is_on_sync_staff.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5992, 'staff', 'sources/hooks/systems/config/staff_text.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5993, 'staff', 'sources/hooks/systems/addon_registry/staff.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5994, 'staff', 'sources/hooks/systems/page_groupings/staff.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5995, 'staff', 'themes/default/templates/STAFF_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5996, 'staff', 'themes/default/templates/STAFF_EDIT_WRAPPER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5997, 'staff', 'themes/default/templates/STAFF_ADMIN_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5998, 'staff', 'adminzone/pages/modules/admin_staff.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (5999, 'staff', 'site/pages/modules/staff.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6000, 'staff', 'lang/EN/staff.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6001, 'staff', 'sources/hooks/systems/cns_cpf_filter/staff_filter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6002, 'staff_messaging', 'themes/default/images/icons/24x24/menu/adminzone/audit/messaging.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6003, 'staff_messaging', 'themes/default/images/icons/48x48/menu/adminzone/audit/messaging.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6004, 'staff_messaging', 'themes/default/images/icons/24x24/menu/site_meta/contact_us.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6005, 'staff_messaging', 'themes/default/images/icons/48x48/menu/site_meta/contact_us.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6006, 'staff_messaging', 'sources/hooks/blocks/main_staff_checklist/messaging.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6007, 'staff_messaging', 'sources/hooks/systems/config/messaging_forum_name.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6008, 'staff_messaging', 'sources/hooks/systems/addon_registry/staff_messaging.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6009, 'staff_messaging', 'sources/hooks/systems/page_groupings/messaging.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6010, 'staff_messaging', 'themes/default/templates/BLOCK_MAIN_CONTACT_SIMPLE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6011, 'staff_messaging', 'themes/default/templates/BLOCK_MAIN_CONTACT_US.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6012, 'staff_messaging', 'adminzone/pages/modules/admin_messaging.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6013, 'staff_messaging', 'themes/default/templates/MESSAGING_MESSAGE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6014, 'staff_messaging', 'themes/default/css/messaging.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6015, 'staff_messaging', 'sources/hooks/systems/notifications/messaging.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6016, 'staff_messaging', 'lang/EN/messaging.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6017, 'staff_messaging', 'data/form_to_email.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6018, 'staff_messaging', 'sources/blocks/main_contact_simple.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6019, 'staff_messaging', 'sources/blocks/main_contact_us.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6020, 'stats', 'themes/default/images/icons/24x24/menu/adminzone/audit/statistics/statistics.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6021, 'stats', 'themes/default/images/icons/48x48/menu/adminzone/audit/statistics/statistics.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6022, 'stats', 'themes/default/images/icons/24x24/menu/adminzone/audit/statistics/clear_stats.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6023, 'stats', 'themes/default/images/icons/24x24/menu/adminzone/audit/statistics/geolocate.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6024, 'stats', 'themes/default/images/icons/24x24/menu/adminzone/audit/statistics/load_times.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6025, 'stats', 'themes/default/images/icons/24x24/menu/adminzone/audit/statistics/page_views.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6026, 'stats', 'themes/default/images/icons/24x24/menu/adminzone/audit/statistics/submits.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6027, 'stats', 'themes/default/images/icons/24x24/menu/adminzone/audit/statistics/top_keywords.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6028, 'stats', 'themes/default/images/icons/24x24/menu/adminzone/audit/statistics/top_referrers.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6029, 'stats', 'themes/default/images/icons/24x24/menu/adminzone/audit/statistics/users_online.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6030, 'stats', 'themes/default/images/icons/48x48/menu/adminzone/audit/statistics/clear_stats.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6031, 'stats', 'themes/default/images/icons/48x48/menu/adminzone/audit/statistics/geolocate.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6032, 'stats', 'themes/default/images/icons/48x48/menu/adminzone/audit/statistics/load_times.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6033, 'stats', 'themes/default/images/icons/48x48/menu/adminzone/audit/statistics/page_views.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6034, 'stats', 'themes/default/images/icons/48x48/menu/adminzone/audit/statistics/submits.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6035, 'stats', 'themes/default/images/icons/48x48/menu/adminzone/audit/statistics/top_keywords.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6036, 'stats', 'themes/default/images/icons/48x48/menu/adminzone/audit/statistics/top_referrers.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6037, 'stats', 'themes/default/images/icons/48x48/menu/adminzone/audit/statistics/users_online.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6038, 'stats', 'themes/default/images/icons/24x24/menu/adminzone/audit/statistics/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6039, 'stats', 'themes/default/images/icons/48x48/menu/adminzone/audit/statistics/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6040, 'stats', 'sources/hooks/modules/admin_setupwizard/stats.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6041, 'stats', 'sources/hooks/systems/config/stats_store_time.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6042, 'stats', 'sources/hooks/systems/config/super_logging.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6043, 'stats', 'sources/hooks/systems/realtime_rain/stats.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6044, 'stats', 'data/modules/admin_cleanup/page_stats.php.pre');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6045, 'stats', 'sources/hooks/systems/cleanup/page_stats.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6046, 'stats', 'sources/hooks/systems/cron/stats_clean.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6047, 'stats', 'sources/hooks/systems/page_groupings/stats.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6048, 'stats', 'sources/hooks/systems/non_active_urls/stats.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6049, 'stats', 'sources/hooks/systems/addon_registry/stats.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6050, 'stats', 'sources/hooks/modules/admin_stats/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6051, 'stats', 'sources_custom/hooks/modules/admin_stats/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6052, 'stats', 'sources/hooks/modules/admin_stats/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6053, 'stats', 'sources_custom/hooks/modules/admin_stats/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6054, 'stats', 'themes/default/templates/STATS_GRAPH.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6055, 'stats', 'themes/default/templates/STATS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6056, 'stats', 'themes/default/templates/STATS_SCREEN_ISCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6057, 'stats', 'themes/default/templates/STATS_OVERVIEW_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6058, 'stats', 'adminzone/pages/modules/admin_stats.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6059, 'stats', 'themes/default/css/stats.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6060, 'stats', 'themes/default/css/svg.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6061, 'stats', 'data/modules/admin_stats/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6062, 'stats', 'data/modules/admin_stats/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6063, 'stats', 'data/modules/admin_stats/IP_Country.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6064, 'stats', 'data_custom/modules/admin_stats/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6065, 'stats', 'lang/EN/stats.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6066, 'stats', 'sources/hooks/systems/cleanup/stats.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6067, 'stats', 'sources/svg.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6068, 'stats', 'sources/hooks/systems/config/bot_stats.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6069, 'stats', 'sources/hooks/systems/tasks/install_geolocation_data.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6070, 'stats_block', 'sources/hooks/systems/config/activity_show_stats_count_page_views_this_month.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6071, 'stats_block', 'sources/hooks/systems/config/activity_show_stats_count_page_views_this_week.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6072, 'stats_block', 'sources/hooks/systems/config/activity_show_stats_count_page_views_today.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6073, 'stats_block', 'sources/hooks/systems/config/activity_show_stats_count_users_online.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6074, 'stats_block', 'sources/hooks/systems/config/activity_show_stats_count_users_online_forum.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6075, 'stats_block', 'sources/hooks/systems/config/activity_show_stats_count_users_online_record.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6076, 'stats_block', 'sources/hooks/systems/config/forum_show_stats_count_members.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6077, 'stats_block', 'sources/hooks/systems/config/forum_show_stats_count_members_active_this_month.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6078, 'stats_block', 'sources/hooks/systems/config/forum_show_stats_count_members_active_this_week.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6079, 'stats_block', 'sources/hooks/systems/config/forum_show_stats_count_members_active_today.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6080, 'stats_block', 'sources/hooks/systems/config/forum_show_stats_count_members_new_this_month.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6081, 'stats_block', 'sources/hooks/systems/config/forum_show_stats_count_members_new_this_week.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6082, 'stats_block', 'sources/hooks/systems/config/forum_show_stats_count_members_new_today.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6083, 'stats_block', 'sources/hooks/systems/config/forum_show_stats_count_posts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6084, 'stats_block', 'sources/hooks/systems/config/forum_show_stats_count_posts_today.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6085, 'stats_block', 'sources/hooks/systems/config/forum_show_stats_count_topics.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6086, 'stats_block', 'sources/hooks/systems/addon_registry/stats_block.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6087, 'stats_block', 'sources/hooks/modules/admin_setupwizard/stats_block.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6088, 'stats_block', 'themes/default/templates/BLOCK_SIDE_STATS_SECTION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6089, 'stats_block', 'themes/default/templates/BLOCK_SIDE_STATS_SUBLINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6090, 'stats_block', 'themes/default/templates/BLOCK_SIDE_STATS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6091, 'stats_block', 'sources/blocks/side_stats.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6092, 'stats_block', 'sources/hooks/blocks/side_stats/stats_forum.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6093, 'stats_block', 'sources/hooks/blocks/side_stats/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6094, 'stats_block', 'sources_custom/hooks/blocks/side_stats/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6095, 'stats_block', 'sources/hooks/blocks/side_stats/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6096, 'stats_block', 'sources_custom/hooks/blocks/side_stats/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6097, 'supermember_directory', 'themes/default/images/icons/24x24/menu/collaboration/supermembers.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6098, 'supermember_directory', 'themes/default/images/icons/48x48/menu/collaboration/supermembers.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6099, 'supermember_directory', 'sources/hooks/systems/config/supermembers_text.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6100, 'supermember_directory', 'sources/hooks/systems/addon_registry/supermember_directory.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6101, 'supermember_directory', 'lang/EN/supermembers.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6102, 'supermember_directory', 'themes/default/templates/SUPERMEMBERS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6103, 'supermember_directory', 'themes/default/templates/SUPERMEMBERS_SCREEN_GROUP.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6104, 'supermember_directory', 'collaboration/pages/modules/supermembers.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6105, 'supermember_directory', 'sources/hooks/systems/page_groupings/supermember_directory.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6106, 'syndication', 'sources/hooks/systems/addon_registry/syndication.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6107, 'syndication', 'themes/default/templates/RSS_HEADER.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6108, 'syndication', 'themes/default/xml/ATOM_ENTRY.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6109, 'syndication', 'themes/default/xml/ATOM_WRAPPER.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6110, 'syndication', 'themes/default/xml/RSS_CLOUD.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6111, 'syndication', 'themes/default/xml/RSS_ENTRY.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6112, 'syndication', 'themes/default/xml/RSS_ENTRY_COMMENTS.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6113, 'syndication', 'themes/default/xml/RSS_WRAPPER.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6114, 'syndication', 'themes/default/xml/ATOM_XSLT.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6115, 'syndication', 'themes/default/xml/RSS_ABBR.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6116, 'syndication', 'themes/default/xml/RSS_XSLT.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6117, 'syndication', 'themes/default/xml/OPML_WRAPPER.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6118, 'syndication', 'themes/default/xml/OPML_XSLT.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6119, 'syndication', 'backend.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6120, 'syndication', 'data/backend_cloud.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6121, 'syndication', 'sources/rss2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6122, 'syndication', 'sources/hooks/systems/rss/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6123, 'syndication', 'sources_custom/hooks/systems/rss/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6124, 'syndication', 'sources/hooks/systems/rss/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6125, 'syndication', 'sources_custom/hooks/systems/rss/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6126, 'syndication', 'sources/hooks/systems/non_active_urls/news_rss_cloud.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6127, 'syndication_blocks', 'sources/hooks/systems/notifications/error_occurred_rss.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6128, 'syndication_blocks', 'sources/hooks/systems/config/is_on_rss.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6129, 'syndication_blocks', 'sources/hooks/systems/config/is_rss_advertised.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6130, 'syndication_blocks', 'sources/hooks/systems/config/rss_update_time.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6131, 'syndication_blocks', 'themes/default/templates/BLOCK_MAIN_RSS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6132, 'syndication_blocks', 'themes/default/templates/BLOCK_MAIN_RSS_SUMMARY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6133, 'syndication_blocks', 'themes/default/templates/BLOCK_SIDE_RSS.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6134, 'syndication_blocks', 'themes/default/templates/BLOCK_SIDE_RSS_SUMMARY.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6135, 'syndication_blocks', 'themes/default/css/rss.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6136, 'syndication_blocks', 'sources/blocks/bottom_rss.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6137, 'syndication_blocks', 'sources/blocks/main_rss.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6138, 'syndication_blocks', 'sources/blocks/side_rss.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6139, 'syndication_blocks', 'sources/hooks/systems/commandr_commands/feed_display.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6140, 'syndication_blocks', 'sources/hooks/systems/addon_registry/syndication_blocks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6141, 'syndication_blocks', 'sources/hooks/modules/admin_setupwizard/syndication_blocks.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6142, 'textbased_persistent_caching', 'sources/hooks/systems/addon_registry/textbased_persistent_caching.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6143, 'textbased_persistent_caching', 'caches/persistent/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6144, 'textbased_persistent_caching', 'caches/persistent/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6145, 'themewizard', 'themes/default/images/icons/24x24/menu/adminzone/style/themes/logowizard.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6146, 'themewizard', 'themes/default/images/icons/24x24/menu/adminzone/style/themes/themewizard.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6147, 'themewizard', 'themes/default/images/icons/48x48/menu/adminzone/style/themes/logowizard.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6148, 'themewizard', 'themes/default/images/icons/48x48/menu/adminzone/style/themes/themewizard.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6149, 'themewizard', 'sources/hooks/systems/commandr_commands/themewizard_find_color.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6150, 'themewizard', 'sources/hooks/systems/commandr_commands/themewizard_compute_equation.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6151, 'themewizard', 'sources/hooks/modules/admin_themewizard/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6152, 'themewizard', 'sources_custom/hooks/modules/admin_themewizard/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6153, 'themewizard', 'sources/hooks/systems/snippets/themewizard_equation.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6154, 'themewizard', 'sources/hooks/modules/admin_themewizard/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6155, 'themewizard', 'sources_custom/hooks/modules/admin_themewizard/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6156, 'themewizard', 'sources/hooks/systems/addon_registry/themewizard.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6157, 'themewizard', 'sources/themewizard.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6158, 'themewizard', 'adminzone/pages/modules/admin_themewizard.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6159, 'themewizard', 'themes/default/templates/THEMEWIZARD_2_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6160, 'themewizard', 'themes/default/templates/THEMEWIZARD_2_PREVIEW.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6161, 'themewizard', 'adminzone/themewizard.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6162, 'themewizard', 'sources/hooks/systems/page_groupings/themewizard.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6163, 'themewizard', 'themes/default/templates/LOGOWIZARD_2.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6164, 'themewizard', 'adminzone/logowizard.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6165, 'themewizard', 'themes/default/images/logo/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6166, 'themewizard', 'themes/default/images/logo/default_logos/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6167, 'themewizard', 'themes/default/images/logo/default_logos/logo1.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6168, 'themewizard', 'themes/default/images/logo/default_logos/logo2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6169, 'themewizard', 'themes/default/images/logo/default_logos/logo3.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6170, 'themewizard', 'themes/default/images/logo/default_logos/logo4.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6171, 'themewizard', 'themes/default/images/logo/default_logos/logo5.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6172, 'themewizard', 'themes/default/images/logo/default_logos/logo6.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6173, 'themewizard', 'themes/default/images/logo/default_logos/logo7.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6174, 'themewizard', 'themes/default/images/logo/default_logos/logo8.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6175, 'themewizard', 'themes/default/images/logo/default_logos/logo9.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6176, 'themewizard', 'themes/default/images/logo/default_logos/logo10.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6177, 'themewizard', 'themes/default/images/logo/default_logos/logo11.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6178, 'themewizard', 'themes/default/images/logo/default_logos/logo12.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6179, 'themewizard', 'themes/default/images/logo/default_backgrounds/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6180, 'themewizard', 'themes/default/images/logo/default_backgrounds/banner1.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6181, 'themewizard', 'themes/default/images/logo/default_backgrounds/banner2.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6182, 'themewizard', 'themes/default/images/logo/default_backgrounds/banner3A.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6183, 'themewizard', 'themes/default/images/logo/default_backgrounds/banner3B.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6184, 'themewizard', 'themes/default/images/logo/default_backgrounds/banner3C.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6185, 'themewizard', 'themes/default/images/logo/default_backgrounds/banner4.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6186, 'themewizard', 'themes/default/images/logo/default_backgrounds/banner5.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6187, 'themewizard', 'themes/default/images/logo/default_backgrounds/banner6.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6188, 'themewizard', 'themes/default/images/logo/default_backgrounds/banner7A.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6189, 'themewizard', 'themes/default/images/logo/default_backgrounds/banner7B.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6190, 'themewizard', 'themes/default/images/logo/default_backgrounds/banner8A.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6191, 'themewizard', 'themes/default/images/logo/default_backgrounds/banner8B.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6192, 'themewizard', 'themes/default/images/logo/default_backgrounds/banner9.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6193, 'themewizard', 'themes/default/images/logo/default_backgrounds/banner10.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6194, 'tickets', 'themes/default/images/icons/24x24/menu/site_meta/tickets.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6195, 'tickets', 'themes/default/images/icons/48x48/menu/site_meta/tickets.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6196, 'tickets', 'themes/default/images/icons/24x24/buttons/add_ticket.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6197, 'tickets', 'themes/default/images/icons/48x48/buttons/add_ticket.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6198, 'tickets', 'themes/default/images/icons/24x24/buttons/new_reply_staff_only.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6199, 'tickets', 'themes/default/images/icons/48x48/buttons/new_reply_staff_only.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6200, 'tickets', 'sources/hooks/systems/resource_meta_aware/ticket_type.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6201, 'tickets', 'sources/hooks/systems/commandr_fs/ticket_types.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6202, 'tickets', 'sources/hooks/systems/addon_registry/tickets.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6203, 'tickets', 'sources/hooks/modules/admin_import_types/tickets.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6204, 'tickets', 'themes/default/templates/SUPPORT_TICKET_TYPE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6205, 'tickets', 'themes/default/templates/SUPPORT_TICKET_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6206, 'tickets', 'themes/default/templates/SUPPORT_TICKETS_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6207, 'tickets', 'themes/default/templates/SUPPORT_TICKET_LINK.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6208, 'tickets', 'themes/default/templates/SUPPORT_TICKETS_SEARCH_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6209, 'tickets', 'adminzone/pages/modules/admin_tickets.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6210, 'tickets', 'themes/default/css/tickets.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6211, 'tickets', 'lang/EN/tickets.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6212, 'tickets', 'site/pages/modules/tickets.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6213, 'tickets', 'sources/hooks/systems/change_detection/tickets.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6214, 'tickets', 'sources/hooks/systems/page_groupings/tickets.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6215, 'tickets', 'sources/hooks/systems/module_permissions/tickets.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6216, 'tickets', 'sources/hooks/systems/rss/tickets.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6217, 'tickets', 'sources/hooks/systems/cron/ticket_type_lead_times.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6218, 'tickets', 'sources/tickets.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6219, 'tickets', 'sources/tickets2.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6220, 'tickets', 'sources/hooks/systems/preview/ticket.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6221, 'tickets', 'sources/hooks/blocks/main_staff_checklist/tickets.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6222, 'tickets', 'sources/hooks/systems/notifications/ticket_reply.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6223, 'tickets', 'sources/hooks/systems/notifications/ticket_new_staff.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6224, 'tickets', 'sources/hooks/systems/notifications/ticket_reply_staff.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6225, 'tickets', 'sources/hooks/systems/notifications/ticket_assigned_staff.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6226, 'tickets', 'sources/tickets_email_integration.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6227, 'tickets', 'sources/hooks/systems/cron/tickets_email_integration.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6228, 'tickets', 'sources/hooks/systems/config/ticket_forum_name.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6229, 'tickets', 'sources/hooks/systems/config/ticket_member_forums.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6230, 'tickets', 'sources/hooks/systems/config/ticket_text.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6231, 'tickets', 'sources/hooks/systems/config/ticket_type_forums.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6232, 'tickets', 'sources/hooks/systems/config/ticket_mail_on.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6233, 'tickets', 'sources/hooks/systems/config/ticket_email_from.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6234, 'tickets', 'sources/hooks/systems/config/ticket_mail_server.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6235, 'tickets', 'sources/hooks/systems/config/ticket_mail_server_port.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6236, 'tickets', 'sources/hooks/systems/config/ticket_mail_server_type.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6237, 'tickets', 'sources/hooks/systems/config/ticket_mail_username.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6238, 'tickets', 'sources/hooks/systems/config/ticket_mail_password.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6239, 'tickets', 'sources/hooks/systems/config/support_operator.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6240, 'tickets', 'sources/hooks/systems/config/ticket_auto_assign.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6241, 'tickets', 'data/incoming_ticket_email.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6242, 'tickets', 'sources/hooks/systems/commandr_fs_extended_member/ticket_known_emailers.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6243, 'uninstaller', 'sources/hooks/systems/addon_registry/uninstaller.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6244, 'uninstaller', 'uninstall.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6245, 'uninstaller', 'themes/default/templates/UNINSTALL_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6246, 'unvalidated', 'themes/default/images/icons/24x24/menu/adminzone/audit/unvalidated.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6247, 'unvalidated', 'themes/default/images/icons/48x48/menu/adminzone/audit/unvalidated.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6248, 'unvalidated', 'sources/hooks/systems/notifications/content_validated.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6249, 'unvalidated', 'sources/hooks/systems/notifications/needs_validation.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6250, 'unvalidated', 'sources/hooks/systems/addon_registry/unvalidated.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6251, 'unvalidated', 'themes/default/templates/UNVALIDATED_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6252, 'unvalidated', 'themes/default/templates/UNVALIDATED_SECTION.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6253, 'unvalidated', 'themes/default/text/VALIDATION_REQUEST_MAIL.txt');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6254, 'unvalidated', 'adminzone/pages/modules/admin_unvalidated.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6255, 'unvalidated', 'lang/EN/unvalidated.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6256, 'unvalidated', 'sources/hooks/blocks/main_staff_checklist/unvalidated.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6257, 'unvalidated', 'sources/hooks/modules/admin_unvalidated/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6258, 'unvalidated', 'sources_custom/hooks/modules/admin_unvalidated/.htaccess');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6259, 'unvalidated', 'sources/hooks/modules/admin_unvalidated/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6260, 'unvalidated', 'sources_custom/hooks/modules/admin_unvalidated/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6261, 'unvalidated', 'sources/hooks/systems/page_groupings/unvalidated.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6262, 'users_online_block', 'sources/hooks/systems/config/usersonline_show_birthdays.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6263, 'users_online_block', 'sources/hooks/systems/config/usersonline_show_newest_member.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6264, 'users_online_block', 'sources/hooks/systems/addon_registry/users_online_block.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6265, 'users_online_block', 'themes/default/templates/BLOCK_SIDE_USERS_ONLINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6266, 'users_online_block', 'sources/blocks/side_users_online.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6267, 'welcome_emails', 'themes/default/images/icons/24x24/menu/adminzone/setup/welcome_emails.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6268, 'welcome_emails', 'themes/default/images/icons/48x48/menu/adminzone/setup/welcome_emails.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6269, 'welcome_emails', 'sources/hooks/systems/addon_registry/welcome_emails.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6270, 'welcome_emails', 'adminzone/pages/modules/admin_cns_welcome_emails.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6271, 'welcome_emails', 'lang/EN/cns_welcome_emails.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6272, 'welcome_emails', 'sources/hooks/systems/cron/cns_welcome_emails.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6273, 'welcome_emails', 'sources/hooks/systems/preview/cns_welcome_email.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6274, 'welcome_emails', 'sources/hooks/systems/commandr_fs/welcome_emails.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6275, 'welcome_emails', 'sources/hooks/systems/resource_meta_aware/welcome_email.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6276, 'wiki', 'themes/default/images/icons/24x24/menu/rich_content/wiki.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6277, 'wiki', 'themes/default/images/icons/24x24/menu/rich_content/wiki/random_page.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6278, 'wiki', 'themes/default/images/icons/48x48/menu/rich_content/wiki.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6279, 'wiki', 'themes/default/images/icons/48x48/menu/rich_content/wiki/random_page.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6280, 'wiki', 'themes/default/images/icons/24x24/buttons/edit_tree.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6281, 'wiki', 'themes/default/images/icons/48x48/buttons/edit_tree.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6282, 'wiki', 'themes/default/images/icons/24x24/menu/rich_content/wiki/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6283, 'wiki', 'themes/default/images/icons/48x48/menu/rich_content/wiki/index.html');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6284, 'wiki', 'sources/hooks/systems/sitemap/wiki_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6285, 'wiki', 'sources/hooks/systems/content_meta_aware/wiki_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6286, 'wiki', 'sources/hooks/systems/content_meta_aware/wiki_post.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6287, 'wiki', 'sources/hooks/systems/commandr_fs/wiki.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6288, 'wiki', 'sources/hooks/systems/config/wiki_show_stats_count_pages.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6289, 'wiki', 'sources/hooks/systems/config/wiki_show_stats_count_posts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6290, 'wiki', 'sources/hooks/systems/config/points_wiki.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6291, 'wiki', 'sources/hooks/systems/config/wiki_enable_children.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6292, 'wiki', 'sources/hooks/systems/config/wiki_enable_content_posts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6293, 'wiki', 'sources/hooks/systems/meta/wiki_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6294, 'wiki', 'sources/hooks/systems/disposable_values/num_wiki_files.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6295, 'wiki', 'sources/hooks/systems/disposable_values/num_wiki_pages.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6296, 'wiki', 'sources/hooks/systems/disposable_values/num_wiki_posts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6297, 'wiki', 'sources/hooks/systems/cns_cpf_filter/points_wiki.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6298, 'wiki', 'sources/hooks/systems/addon_registry/wiki.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6299, 'wiki', 'sources/hooks/modules/admin_themewizard/wiki.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6300, 'wiki', 'sources/hooks/modules/admin_import_types/wiki.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6301, 'wiki', 'themes/default/templates/WIKI_LIST_TREE_LINE.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6302, 'wiki', 'themes/default/templates/WIKI_MANAGE_TREE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6303, 'wiki', 'themes/default/templates/WIKI_PAGE_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6304, 'wiki', 'themes/default/templates/WIKI_POST.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6305, 'wiki', 'themes/default/templates/WIKI_POSTING_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6306, 'wiki', 'themes/default/templates/WIKI_RATING.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6307, 'wiki', 'themes/default/templates/WIKI_RATING_FORM.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6308, 'wiki', 'themes/default/css/wiki.css');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6309, 'wiki', 'sources/hooks/systems/ajax_tree/choose_wiki_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6310, 'wiki', 'cms/pages/modules/cms_wiki.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6311, 'wiki', 'lang/EN/wiki.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6312, 'wiki', 'site/pages/modules/wiki.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6313, 'wiki', 'sources/wiki.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6314, 'wiki', 'sources/wiki_stats.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6315, 'wiki', 'sources/hooks/blocks/main_staff_checklist/wiki.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6316, 'wiki', 'sources/hooks/blocks/side_stats/stats_wiki.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6317, 'wiki', 'sources/hooks/modules/admin_newsletter/wiki.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6318, 'wiki', 'sources/hooks/modules/admin_unvalidated/wiki.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6319, 'wiki', 'sources/hooks/modules/search/wiki_pages.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6320, 'wiki', 'sources/hooks/modules/search/wiki_posts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6321, 'wiki', 'sources/hooks/systems/attachments/wiki_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6322, 'wiki', 'sources/hooks/systems/attachments/wiki_post.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6323, 'wiki', 'sources/hooks/systems/page_groupings/wiki.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6324, 'wiki', 'sources/hooks/systems/preview/wiki_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6325, 'wiki', 'sources/hooks/systems/preview/wiki_post.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6326, 'wiki', 'sources/hooks/systems/rss/wiki.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6327, 'wiki', 'sources/hooks/systems/module_permissions/wiki_page.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6328, 'wiki', 'sources/hooks/systems/notifications/wiki.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6329, 'windows_helper_scripts', 'sources/hooks/systems/addon_registry/windows_helper_scripts.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6330, 'windows_helper_scripts', 'fixperms.bat');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6331, 'wordfilter', 'themes/default/images/icons/24x24/menu/adminzone/security/wordfilter.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6332, 'wordfilter', 'themes/default/images/icons/48x48/menu/adminzone/security/wordfilter.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6333, 'wordfilter', 'sources/hooks/systems/addon_registry/wordfilter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6334, 'wordfilter', 'sources/hooks/systems/page_groupings/wordfilter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6335, 'wordfilter', 'lang/EN/wordfilter.ini');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6336, 'wordfilter', 'themes/default/templates/WORDFILTER_SCREEN.tpl');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6337, 'wordfilter', 'adminzone/pages/modules/admin_wordfilter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6338, 'wordfilter', 'sources/hooks/modules/admin_setupwizard/wordfilter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6339, 'wordfilter', 'sources/hooks/modules/admin_import_types/wordfilter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6340, 'wordfilter', 'sources/hooks/systems/commandr_fs_extended_config/wordfilter.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6341, 'xml_fields', 'themes/default/images/icons/24x24/menu/adminzone/setup/xml_fields.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6342, 'xml_fields', 'themes/default/images/icons/48x48/menu/adminzone/setup/xml_fields.png');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6343, 'xml_fields', 'data/xml_config/fields.xml');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6344, 'xml_fields', 'sources/hooks/systems/addon_registry/xml_fields.php');
INSERT INTO cms_addons_files (id, addon_name, filename) VALUES (6345, 'zone_logos', 'sources/hooks/systems/addon_registry/zone_logos.php');

DROP TABLE IF EXISTS cms_aggregate_type_instances;

CREATE TABLE cms_aggregate_type_instances (
    aggregate_label varchar(255) NOT NULL,
    aggregate_type varchar(80) NOT NULL,
    other_parameters longtext NOT NULL,
    add_time integer unsigned NOT NULL,
    edit_time integer unsigned NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_aggregate_type_instances ADD INDEX aggregate_lookup (aggregate_label(250));

DROP TABLE IF EXISTS cms_alternative_ids;

CREATE TABLE cms_alternative_ids (
    resource_resource_fs_hook varchar(80) NOT NULL,
    resource_guid varchar(80) NOT NULL,
    resource_label varchar(255) NOT NULL,
    resource_moniker varchar(80) NOT NULL,
    resource_id varchar(80) NOT NULL,
    resource_type varchar(80) NOT NULL,
    PRIMARY KEY (resource_id, resource_type)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_alternative_ids ADD INDEX resource_guid (resource_guid);

ALTER TABLE cms_alternative_ids ADD INDEX resource_label (resource_label(250));

ALTER TABLE cms_alternative_ids ADD INDEX resource_moniker (resource_moniker,resource_type);

ALTER TABLE cms_alternative_ids ADD INDEX resource_moniker_uniq (resource_moniker,resource_resource_fs_hook);

DROP TABLE IF EXISTS cms_attachment_refs;

CREATE TABLE cms_attachment_refs (
    r_referer_type varchar(80) NOT NULL,
    r_referer_id varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    a_id integer NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_attachments;

CREATE TABLE cms_attachments (
    a_add_time integer NOT NULL,
    a_last_downloaded_time integer NULL,
    a_num_downloads integer NOT NULL,
    a_original_filename varchar(255) NOT NULL,
    a_thumb_url varchar(255) NOT NULL,
    a_description varchar(255) NOT NULL,
    a_url varchar(255) NOT NULL,
    a_file_size integer NULL,
    a_member_id integer NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_attachments ADD INDEX attachmentlimitcheck (a_add_time);

ALTER TABLE cms_attachments ADD INDEX ownedattachments (a_member_id);

DROP TABLE IF EXISTS cms_authors;

CREATE TABLE cms_authors (
    url varchar(255) BINARY NOT NULL,
    author varchar(80) NOT NULL,
    member_id integer NULL,
    description longtext NOT NULL,
    skills longtext NOT NULL,
    description__text_parsed longtext NOT NULL,
    description__source_user integer DEFAULT 1 NOT NULL,
    skills__text_parsed longtext NOT NULL,
    skills__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (author)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_authors ADD FULLTEXT description (description);

ALTER TABLE cms_authors ADD FULLTEXT skills (skills);

ALTER TABLE cms_authors ADD INDEX findmemberlink (member_id);

DROP TABLE IF EXISTS cms_autosave;

CREATE TABLE cms_autosave (
    a_time integer unsigned NOT NULL,
    a_value longtext NOT NULL,
    a_key longtext NOT NULL,
    a_member_id integer NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_autosave ADD INDEX myautosaves (a_member_id);

DROP TABLE IF EXISTS cms_award_archive;

CREATE TABLE cms_award_archive (
    a_type_id integer NOT NULL,
    date_and_time integer unsigned NOT NULL,
    content_id varchar(80) NOT NULL,
    member_id integer NOT NULL,
    PRIMARY KEY (a_type_id, date_and_time)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_award_archive ADD INDEX awardquicksearch (content_id);

DROP TABLE IF EXISTS cms_award_types;

CREATE TABLE cms_award_types (
    id integer unsigned auto_increment NOT NULL,
    a_title longtext NOT NULL,
    a_description longtext NOT NULL,
    a_points integer NOT NULL,
    a_content_type varchar(80) NOT NULL,
    a_hide_awardee tinyint(1) NOT NULL,
    a_update_time_hours integer NOT NULL,
    a_description__text_parsed longtext NOT NULL,
    a_description__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

INSERT INTO cms_award_types (id, a_title, a_description, a_points, a_content_type, a_hide_awardee, a_update_time_hours, a_description__text_parsed, a_description__source_user) VALUES (1, 'Download of the week', 'The best downloads in the download system, chosen every week.', 0, 'download', 1, 168, 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:39:\\\"string_attach_5cae3491a692f3.29383068_1\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:39:\\\"string_attach_5cae3491a692f3.29383068_1\\\";s:129:\\\"\\$tpl_funcs[\'string_attach_5cae3491a692f3.29383068_1\']=\\\"echo \\\\\\\"The best downloads in the download system, chosen every week.\\\\\\\";\\\";\\n\\\";}}\");
', 1);

ALTER TABLE cms_award_types ADD FULLTEXT a_description (a_description);

ALTER TABLE cms_award_types ADD FULLTEXT a_title (a_title);

DROP TABLE IF EXISTS cms_banned_ip;

CREATE TABLE cms_banned_ip (
    ip varchar(40) NOT NULL,
    i_descrip longtext NOT NULL,
    i_ban_until integer unsigned NULL,
    i_ban_positive tinyint(1) NOT NULL,
    PRIMARY KEY (ip)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_banner_clicks;

CREATE TABLE cms_banner_clicks (
    c_member_id integer NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    c_ip_address varchar(40) NOT NULL,
    c_source varchar(80) NOT NULL,
    c_banner_id varchar(80) NOT NULL,
    c_date_and_time integer unsigned NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_banner_clicks ADD INDEX c_banner_id (c_banner_id);

ALTER TABLE cms_banner_clicks ADD INDEX clicker_ip (c_ip_address);

DROP TABLE IF EXISTS cms_banner_types;

CREATE TABLE cms_banner_types (
    id varchar(80) NOT NULL,
    t_is_textual tinyint(1) NOT NULL,
    t_image_width integer NOT NULL,
    t_image_height integer NOT NULL,
    t_max_file_size integer NOT NULL,
    t_comcode_inline tinyint(1) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

INSERT INTO cms_banner_types (id, t_is_textual, t_image_width, t_image_height, t_max_file_size, t_comcode_inline) VALUES ('', 0, 728, 90, 150, 0);

ALTER TABLE cms_banner_types ADD INDEX hottext (t_comcode_inline);

DROP TABLE IF EXISTS cms_banners;

CREATE TABLE cms_banners (
    the_type tinyint NOT NULL,
    b_title_text varchar(255) NOT NULL,
    caption longtext NOT NULL,
    b_direct_code longtext NOT NULL,
    campaign_remaining integer NOT NULL,
    site_url varchar(255) BINARY NOT NULL,
    hits_from integer NOT NULL,
    views_from integer NOT NULL,
    hits_to integer NOT NULL,
    views_to integer NOT NULL,
    importance_modulus integer NOT NULL,
    img_url varchar(255) BINARY NOT NULL,
    validated tinyint(1) NOT NULL,
    add_date integer unsigned NOT NULL,
    edit_date integer unsigned NULL,
    b_type varchar(80) NOT NULL,
    submitter integer NOT NULL,
    expiry_date integer unsigned NULL,
    name varchar(80) NOT NULL,
    notes longtext NOT NULL,
    caption__text_parsed longtext NOT NULL,
    caption__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (name)
) CHARACTER SET=utf8mb4 engine=MyISAM;

INSERT INTO cms_banners (name, expiry_date, submitter, img_url, the_type, b_title_text, caption, b_direct_code, campaign_remaining, site_url, hits_from, views_from, hits_to, views_to, importance_modulus, notes, validated, add_date, edit_date, b_type, caption__text_parsed, caption__source_user) VALUES ('advertise_here', NULL, 1, 'data/images/advertise_here.png', 2, '', 'Advertise here!', '', 0, 'http://localhost/composr/index.php?page=advertise', 0, 0, 0, 0, 10, 'Provided as a default. This is a fallback banner (it shows when others are not available).', 1, 1554920603, NULL, '', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:39:\\\"string_attach_5cae349b0fc107.69225297_1\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:39:\\\"string_attach_5cae349b0fc107.69225297_1\\\";s:83:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_1\']=\\\"echo \\\\\\\"Advertise here!\\\\\\\";\\\";\\n\\\";}}\");
', 1);
INSERT INTO cms_banners (name, expiry_date, submitter, img_url, the_type, b_title_text, caption, b_direct_code, campaign_remaining, site_url, hits_from, views_from, hits_to, views_to, importance_modulus, notes, validated, add_date, edit_date, b_type, caption__text_parsed, caption__source_user) VALUES ('donate', NULL, 1, 'data/images/donate.png', 0, '', 'Please donate to keep this site alive', '', 0, 'http://localhost/composr/index.php?page=donate', 0, 0, 0, 0, 30, 'Provided as a default.', 1, 1554920603, NULL, '', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:39:\\\"string_attach_5cae349b0fc107.69225297_2\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:39:\\\"string_attach_5cae349b0fc107.69225297_2\\\";s:105:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_2\']=\\\"echo \\\\\\\"Please donate to keep this site alive\\\\\\\";\\\";\\n\\\";}}\");
', 1);

ALTER TABLE cms_banners ADD FULLTEXT caption (caption);

ALTER TABLE cms_banners ADD INDEX badd_date (add_date);

ALTER TABLE cms_banners ADD INDEX banner_child_find (b_type);

ALTER TABLE cms_banners ADD INDEX bvalidated (validated);

ALTER TABLE cms_banners ADD INDEX campaign_remaining (campaign_remaining);

ALTER TABLE cms_banners ADD INDEX expiry_date (expiry_date);

ALTER TABLE cms_banners ADD INDEX the_type (the_type);

ALTER TABLE cms_banners ADD INDEX topsites (hits_from,hits_to);

DROP TABLE IF EXISTS cms_banners_types;

CREATE TABLE cms_banners_types (
    b_type varchar(80) NOT NULL,
    name varchar(80) NOT NULL,
    PRIMARY KEY (b_type, name)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_blocks;

CREATE TABLE cms_blocks (
    block_version integer NOT NULL,
    block_hack_version integer NULL,
    block_hacked_by varchar(80) NOT NULL,
    block_organisation varchar(80) NOT NULL,
    block_author varchar(80) NOT NULL,
    block_name varchar(80) NOT NULL,
    PRIMARY KEY (block_name)
) CHARACTER SET=utf8mb4 engine=MyISAM;

INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('bottom_forum_news', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('bottom_news', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('bottom_rss', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_awards', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_banner_wave', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_bottom_bar', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_cc_embed', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_cns_involved_topics', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_comcode_page_children', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_comments', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_contact_catalogues', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_contact_simple', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_contact_us', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_content', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_content_filtering', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_count', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_countdown', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_custom_comcode_tags', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_custom_gfx', 'Chris Graham', 'ocProducts', '', NULL, 1);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_db_notes', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_emoticon_codes', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_forum_news', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_forum_topics', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_friends_list', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_gallery_embed', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_greeting', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_image_fader', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_image_fader_news', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_include_module', 'Chris Graham', 'ocProducts', '', NULL, 1);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_leader_board', 'Chris Graham', 'ocProducts', '', NULL, 3);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_member_bar', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_members', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_multi_content', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_news', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_newsletter_signup', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_notes', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_only_if_match', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_personal_galleries_list', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_poll', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_pt_notifications', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_quotes', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_rating', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_rss', 'Chris Graham', 'ocProducts', '', NULL, 3);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_screen_actions', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_search', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_staff_actions', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_staff_checklist', 'Chris Graham', 'ocProducts', '', NULL, 4);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_staff_links', 'Jack Franklin', 'ocProducts', '', NULL, 3);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_staff_new_version', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_staff_tips', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_staff_website_monitoring', 'Jack Franklin', 'ocProducts', '', NULL, 3);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_top_sites', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('main_trackback', 'Philip Withnall', 'ocProducts', '', NULL, 1);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('menu', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('side_calendar', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('side_cns_private_topics', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('side_forum_news', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('side_friends', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('side_galleries', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('side_language', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('side_network', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('side_news', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('side_news_archive', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('side_news_categories', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('side_personal_stats', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('side_printer_friendly', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('side_rss', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('side_shoutbox', 'Philip Withnall', 'ocProducts', '', NULL, 3);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('side_stats', 'Chris Graham', 'ocProducts', '', NULL, 3);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('side_tag_cloud', 'Chris Graham', 'ocProducts', '', NULL, 3);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('side_users_online', 'Chris Graham', 'ocProducts', '', NULL, 3);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('top_login', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('top_notifications', 'Chris Graham', 'ocProducts', '', NULL, 1);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('top_personal_stats', 'Chris Graham', 'ocProducts', '', NULL, 2);
INSERT INTO cms_blocks (block_name, block_author, block_organisation, block_hacked_by, block_hack_version, block_version) VALUES ('top_search', 'Chris Graham', 'ocProducts', '', NULL, 2);

DROP TABLE IF EXISTS cms_bookmarks;

CREATE TABLE cms_bookmarks (
    id integer unsigned auto_increment NOT NULL,
    b_owner integer NOT NULL,
    b_folder varchar(255) NOT NULL,
    b_title varchar(255) NOT NULL,
    b_page_link varchar(255) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_cache;

CREATE TABLE cms_cache (
    is_bot tinyint(1) NULL,
    groups varchar(255) NOT NULL,
    the_member integer NULL,
    staff_status tinyint(1) NULL,
    the_theme varchar(40) NOT NULL,
    identifier varchar(40) NOT NULL,
    cached_for varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    timezone varchar(40) NOT NULL,
    lang varchar(5) NOT NULL,
    the_value longtext NOT NULL,
    dependencies longtext NOT NULL,
    date_and_time integer unsigned NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_cache ADD INDEX cached_ford (date_and_time);

ALTER TABLE cms_cache ADD INDEX cached_fore (cached_for);

ALTER TABLE cms_cache ADD INDEX cached_forf (cached_for,identifier,the_theme,lang,staff_status,the_member,is_bot);

ALTER TABLE cms_cache ADD INDEX cached_forh (the_theme);

DROP TABLE IF EXISTS cms_cache_on;

CREATE TABLE cms_cache_on (
    cache_ttl integer NOT NULL,
    special_cache_flags integer NOT NULL,
    cache_on longtext NOT NULL,
    cached_for varchar(80) NOT NULL,
    PRIMARY KEY (cached_for)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_cached_comcode_pages;

CREATE TABLE cms_cached_comcode_pages (
    cc_page_title longtext NOT NULL,
    the_theme varchar(80) NOT NULL,
    string_index longtext NOT NULL,
    the_page varchar(80) NOT NULL,
    the_zone varchar(80) NOT NULL,
    string_index__text_parsed longtext NOT NULL,
    string_index__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (the_theme, the_page, the_zone)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_cached_comcode_pages ADD FULLTEXT cc_page_title (cc_page_title);

ALTER TABLE cms_cached_comcode_pages ADD FULLTEXT page_search__combined (cc_page_title,string_index);

ALTER TABLE cms_cached_comcode_pages ADD FULLTEXT string_index (string_index);

ALTER TABLE cms_cached_comcode_pages ADD INDEX ccp_join (the_page,the_zone);

ALTER TABLE cms_cached_comcode_pages ADD INDEX ftjoin_ccpt (cc_page_title(250));

ALTER TABLE cms_cached_comcode_pages ADD INDEX ftjoin_ccsi (string_index(250));

DROP TABLE IF EXISTS cms_calendar_events;

CREATE TABLE cms_calendar_events (
    allow_trackbacks tinyint(1) NOT NULL,
    notes longtext NOT NULL,
    e_type integer NOT NULL,
    validated tinyint(1) NOT NULL,
    e_views integer NOT NULL,
    e_member_calendar integer NULL,
    e_submitter integer NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    e_end_hour tinyint NULL,
    e_content longtext NOT NULL,
    e_add_date integer unsigned NOT NULL,
    e_edit_date integer unsigned NULL,
    allow_comments tinyint NOT NULL,
    allow_rating tinyint(1) NOT NULL,
    e_priority tinyint NOT NULL,
    e_do_timezone_conv tinyint(1) NOT NULL,
    e_timezone varchar(80) NOT NULL,
    e_end_minute tinyint NULL,
    e_recurrence varchar(80) NOT NULL,
    e_recurrences tinyint NULL,
    e_seg_recurrences tinyint(1) NOT NULL,
    e_start_year integer NOT NULL,
    e_start_month tinyint NOT NULL,
    e_start_day tinyint NOT NULL,
    e_title longtext NOT NULL,
    e_end_monthly_spec_type varchar(80) NOT NULL,
    e_end_day tinyint NULL,
    e_end_month tinyint NULL,
    e_end_year integer NULL,
    e_start_minute tinyint NULL,
    e_start_hour tinyint NULL,
    e_start_monthly_spec_type varchar(80) NOT NULL,
    e_content__text_parsed longtext NOT NULL,
    e_content__source_user integer DEFAULT 1 NOT NULL,
    e_title__text_parsed longtext NOT NULL,
    e_title__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_calendar_events ADD FULLTEXT e_content (e_content);

ALTER TABLE cms_calendar_events ADD FULLTEXT e_title (e_title);

ALTER TABLE cms_calendar_events ADD FULLTEXT event_search__combined (e_title,e_content);

ALTER TABLE cms_calendar_events ADD INDEX ces (e_submitter);

ALTER TABLE cms_calendar_events ADD INDEX e_add_date (e_add_date);

ALTER TABLE cms_calendar_events ADD INDEX e_type (e_type);

ALTER TABLE cms_calendar_events ADD INDEX e_views (e_views);

ALTER TABLE cms_calendar_events ADD INDEX eventat (e_start_year,e_start_month,e_start_day,e_start_hour,e_start_minute);

ALTER TABLE cms_calendar_events ADD INDEX ftjoin_econtent (e_content(250));

ALTER TABLE cms_calendar_events ADD INDEX ftjoin_etitle (e_title(250));

ALTER TABLE cms_calendar_events ADD INDEX validated (validated);

DROP TABLE IF EXISTS cms_calendar_interests;

CREATE TABLE cms_calendar_interests (
    t_type integer NOT NULL,
    i_member_id integer NOT NULL,
    PRIMARY KEY (t_type, i_member_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_calendar_jobs;

CREATE TABLE cms_calendar_jobs (
    j_member_id integer NULL,
    j_reminder_id integer NULL,
    j_time integer unsigned NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    j_event_id integer NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_calendar_jobs ADD INDEX applicablejobs (j_time);

DROP TABLE IF EXISTS cms_calendar_reminders;

CREATE TABLE cms_calendar_reminders (
    n_seconds_before integer NOT NULL,
    n_member_id integer NOT NULL,
    e_id integer NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_calendar_types;

CREATE TABLE cms_calendar_types (
    t_external_feed varchar(255) BINARY NOT NULL,
    t_logo varchar(255) NOT NULL,
    t_title longtext NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    t_title__text_parsed longtext NOT NULL,
    t_title__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

INSERT INTO cms_calendar_types (id, t_title, t_logo, t_external_feed, t_title__text_parsed, t_title__source_user) VALUES (1, '(System command)', 'calendar/system_command', '', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:39:\\\"string_attach_5cae349b0fc107.69225297_3\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:39:\\\"string_attach_5cae349b0fc107.69225297_3\\\";s:84:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_3\']=\\\"echo \\\\\\\"(System command)\\\\\\\";\\\";\\n\\\";}}\");
', 1);
INSERT INTO cms_calendar_types (id, t_title, t_logo, t_external_feed, t_title__text_parsed, t_title__source_user) VALUES (2, 'General', 'calendar/general', '', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:39:\\\"string_attach_5cae349b0fc107.69225297_4\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:39:\\\"string_attach_5cae349b0fc107.69225297_4\\\";s:75:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_4\']=\\\"echo \\\\\\\"General\\\\\\\";\\\";\\n\\\";}}\");
', 1);
INSERT INTO cms_calendar_types (id, t_title, t_logo, t_external_feed, t_title__text_parsed, t_title__source_user) VALUES (3, 'Birthday', 'calendar/birthday', '', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:39:\\\"string_attach_5cae349b0fc107.69225297_5\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:39:\\\"string_attach_5cae349b0fc107.69225297_5\\\";s:76:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_5\']=\\\"echo \\\\\\\"Birthday\\\\\\\";\\\";\\n\\\";}}\");
', 1);
INSERT INTO cms_calendar_types (id, t_title, t_logo, t_external_feed, t_title__text_parsed, t_title__source_user) VALUES (4, 'Public holiday', 'calendar/public_holiday', '', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:39:\\\"string_attach_5cae349b0fc107.69225297_6\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:39:\\\"string_attach_5cae349b0fc107.69225297_6\\\";s:82:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_6\']=\\\"echo \\\\\\\"Public holiday\\\\\\\";\\\";\\n\\\";}}\");
', 1);
INSERT INTO cms_calendar_types (id, t_title, t_logo, t_external_feed, t_title__text_parsed, t_title__source_user) VALUES (5, 'Vacation', 'calendar/vacation', '', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:39:\\\"string_attach_5cae349b0fc107.69225297_7\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:39:\\\"string_attach_5cae349b0fc107.69225297_7\\\";s:76:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_7\']=\\\"echo \\\\\\\"Vacation\\\\\\\";\\\";\\n\\\";}}\");
', 1);
INSERT INTO cms_calendar_types (id, t_title, t_logo, t_external_feed, t_title__text_parsed, t_title__source_user) VALUES (6, 'Appointment', 'calendar/appointment', '', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:39:\\\"string_attach_5cae349b0fc107.69225297_8\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:39:\\\"string_attach_5cae349b0fc107.69225297_8\\\";s:79:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_8\']=\\\"echo \\\\\\\"Appointment\\\\\\\";\\\";\\n\\\";}}\");
', 1);
INSERT INTO cms_calendar_types (id, t_title, t_logo, t_external_feed, t_title__text_parsed, t_title__source_user) VALUES (7, 'Task', 'calendar/commitment', '', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:39:\\\"string_attach_5cae349b0fc107.69225297_9\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:39:\\\"string_attach_5cae349b0fc107.69225297_9\\\";s:72:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_9\']=\\\"echo \\\\\\\"Task\\\\\\\";\\\";\\n\\\";}}\");
', 1);
INSERT INTO cms_calendar_types (id, t_title, t_logo, t_external_feed, t_title__text_parsed, t_title__source_user) VALUES (8, 'Anniversary', 'calendar/anniversary', '', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_5cae349b0fc107.69225297_10\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:40:\\\"string_attach_5cae349b0fc107.69225297_10\\\";s:80:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_10\']=\\\"echo \\\\\\\"Anniversary\\\\\\\";\\\";\\n\\\";}}\");
', 1);

ALTER TABLE cms_calendar_types ADD FULLTEXT t_title (t_title);

DROP TABLE IF EXISTS cms_captchas;

CREATE TABLE cms_captchas (
    si_session_id varchar(80) NOT NULL,
    si_code varchar(80) NOT NULL,
    si_time integer unsigned NOT NULL,
    PRIMARY KEY (si_session_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_captchas ADD INDEX si_time (si_time);

DROP TABLE IF EXISTS cms_catalogue_cat_treecache;

CREATE TABLE cms_catalogue_cat_treecache (
    cc_ancestor_id integer NOT NULL,
    cc_id integer NOT NULL,
    PRIMARY KEY (cc_ancestor_id, cc_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_catalogue_cat_treecache ADD INDEX cc_ancestor_id (cc_ancestor_id);

DROP TABLE IF EXISTS cms_catalogue_categories;

CREATE TABLE cms_catalogue_categories (
    cc_move_days_higher integer NOT NULL,
    cc_move_days_lower integer NOT NULL,
    cc_move_target integer NULL,
    cc_parent_id integer NULL,
    cc_add_date integer unsigned NOT NULL,
    cc_notes longtext NOT NULL,
    rep_image varchar(255) BINARY NOT NULL,
    cc_description longtext NOT NULL,
    cc_title longtext NOT NULL,
    c_name varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    cc_order integer NOT NULL,
    cc_description__text_parsed longtext NOT NULL,
    cc_description__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

INSERT INTO cms_catalogue_categories (id, c_name, cc_title, cc_description, rep_image, cc_notes, cc_add_date, cc_parent_id, cc_move_target, cc_move_days_lower, cc_move_days_higher, cc_order, cc_description__text_parsed, cc_description__source_user) VALUES (1, 'projects', 'Super-member projects', 'These are projects listed by super-members, designed to: advertise project existence, detail current progress, and solicit help.', '', '', 1554920603, NULL, NULL, 30, 60, 0, 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_5cae349b0fc107.69225297_12\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:40:\\\"string_attach_5cae349b0fc107.69225297_12\\\";s:197:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_12\']=\\\"echo \\\\\\\"These are projects listed by super-members, designed to: advertise project existence, detail current progress, and solicit help.\\\\\\\";\\\";\\n\\\";}}\");
', 1);
INSERT INTO cms_catalogue_categories (id, c_name, cc_title, cc_description, rep_image, cc_notes, cc_add_date, cc_parent_id, cc_move_target, cc_move_days_lower, cc_move_days_higher, cc_order, cc_description__text_parsed, cc_description__source_user) VALUES (2, 'links', 'Links home', '', '', '', 1554920603, NULL, NULL, 30, 60, 0, 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_5cae349b0fc107.69225297_14\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:40:\\\"string_attach_5cae349b0fc107.69225297_14\\\";s:69:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_14\']=\\\"echo \\\\\\\"\\\\\\\";\\\";\\n\\\";}}\");
', 1);
INSERT INTO cms_catalogue_categories (id, c_name, cc_title, cc_description, rep_image, cc_notes, cc_add_date, cc_parent_id, cc_move_target, cc_move_days_lower, cc_move_days_higher, cc_order, cc_description__text_parsed, cc_description__source_user) VALUES (3, 'faqs', 'Frequently Asked Questions', 'If you have questions that are not covered in our FAQ, please post them in an appropriate forum.', '', '', 1554920603, NULL, NULL, 30, 60, 0, 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_5cae349b0fc107.69225297_16\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:40:\\\"string_attach_5cae349b0fc107.69225297_16\\\";s:165:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_16\']=\\\"echo \\\\\\\"If you have questions that are not covered in our FAQ, please post them in an appropriate forum.\\\\\\\";\\\";\\n\\\";}}\");
', 1);
INSERT INTO cms_catalogue_categories (id, c_name, cc_title, cc_description, rep_image, cc_notes, cc_add_date, cc_parent_id, cc_move_target, cc_move_days_lower, cc_move_days_higher, cc_order, cc_description__text_parsed, cc_description__source_user) VALUES (4, 'contacts', 'Contacts', '', '', '', 1554920603, NULL, NULL, 30, 60, 0, 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_5cae349b0fc107.69225297_18\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:40:\\\"string_attach_5cae349b0fc107.69225297_18\\\";s:69:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_18\']=\\\"echo \\\\\\\"\\\\\\\";\\\";\\n\\\";}}\");
', 1);
INSERT INTO cms_catalogue_categories (id, c_name, cc_title, cc_description, rep_image, cc_notes, cc_add_date, cc_parent_id, cc_move_target, cc_move_days_lower, cc_move_days_higher, cc_order, cc_description__text_parsed, cc_description__source_user) VALUES (5, 'products', 'Products home', '', '', '', 1554920603, NULL, NULL, 30, 60, 0, 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_5cae349b0fc107.69225297_20\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:40:\\\"string_attach_5cae349b0fc107.69225297_20\\\";s:69:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_20\']=\\\"echo \\\\\\\"\\\\\\\";\\\";\\n\\\";}}\");
', 1);

ALTER TABLE cms_catalogue_categories ADD FULLTEXT cat_cat_search__combined (cc_title,cc_description);

ALTER TABLE cms_catalogue_categories ADD FULLTEXT cc_description (cc_description);

ALTER TABLE cms_catalogue_categories ADD FULLTEXT cc_title (cc_title);

ALTER TABLE cms_catalogue_categories ADD INDEX cataloguefind (c_name);

ALTER TABLE cms_catalogue_categories ADD INDEX catstoclean (cc_move_target);

ALTER TABLE cms_catalogue_categories ADD INDEX cc_order (cc_order);

ALTER TABLE cms_catalogue_categories ADD INDEX cc_parent_id (cc_parent_id);

ALTER TABLE cms_catalogue_categories ADD INDEX ftjoin_ccdescrip (cc_description(250));

ALTER TABLE cms_catalogue_categories ADD INDEX ftjoin_cctitle (cc_title(250));

DROP TABLE IF EXISTS cms_catalogue_childcountcache;

CREATE TABLE cms_catalogue_childcountcache (
    cc_id integer NOT NULL,
    c_num_rec_children integer NOT NULL,
    c_num_rec_entries integer NOT NULL,
    PRIMARY KEY (cc_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_catalogue_efv_float;

CREATE TABLE cms_catalogue_efv_float (
    id integer unsigned auto_increment NOT NULL,
    cf_id integer NOT NULL,
    ce_id integer NOT NULL,
    cv_value real NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_catalogue_efv_float ADD INDEX cefv_f_combo (ce_id,cf_id);

ALTER TABLE cms_catalogue_efv_float ADD INDEX fce_id (ce_id);

ALTER TABLE cms_catalogue_efv_float ADD INDEX fcf_id (cf_id);

ALTER TABLE cms_catalogue_efv_float ADD INDEX fcv_value (cv_value);

DROP TABLE IF EXISTS cms_catalogue_efv_integer;

CREATE TABLE cms_catalogue_efv_integer (
    ce_id integer NOT NULL,
    cv_value integer NULL,
    cf_id integer NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_catalogue_efv_integer ADD INDEX cefv_i_combo (ce_id,cf_id);

ALTER TABLE cms_catalogue_efv_integer ADD INDEX ice_id (ce_id);

ALTER TABLE cms_catalogue_efv_integer ADD INDEX icf_id (cf_id);

ALTER TABLE cms_catalogue_efv_integer ADD INDEX itv_value (cv_value);

DROP TABLE IF EXISTS cms_catalogue_efv_long;

CREATE TABLE cms_catalogue_efv_long (
    cv_value longtext NOT NULL,
    ce_id integer NOT NULL,
    cf_id integer NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_catalogue_efv_long ADD FULLTEXT lcv_value (cv_value);

ALTER TABLE cms_catalogue_efv_long ADD INDEX cefv_l_combo (ce_id,cf_id);

ALTER TABLE cms_catalogue_efv_long ADD INDEX lce_id (ce_id);

ALTER TABLE cms_catalogue_efv_long ADD INDEX lcf_id (cf_id);

DROP TABLE IF EXISTS cms_catalogue_efv_long_trans;

CREATE TABLE cms_catalogue_efv_long_trans (
    id integer unsigned auto_increment NOT NULL,
    cf_id integer NOT NULL,
    ce_id integer NOT NULL,
    cv_value longtext NOT NULL,
    cv_value__text_parsed longtext NOT NULL,
    cv_value__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_catalogue_efv_long_trans ADD FULLTEXT cv_value (cv_value);

ALTER TABLE cms_catalogue_efv_long_trans ADD INDEX cefv_lt_combo (ce_id,cf_id);

ALTER TABLE cms_catalogue_efv_long_trans ADD INDEX ltce_id (ce_id);

ALTER TABLE cms_catalogue_efv_long_trans ADD INDEX ltcf_id (cf_id);

ALTER TABLE cms_catalogue_efv_long_trans ADD INDEX ltcv_value (cv_value(250));

DROP TABLE IF EXISTS cms_catalogue_efv_short;

CREATE TABLE cms_catalogue_efv_short (
    cv_value varchar(255) NOT NULL,
    ce_id integer NOT NULL,
    cf_id integer NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_catalogue_efv_short ADD FULLTEXT scv_value (cv_value);

ALTER TABLE cms_catalogue_efv_short ADD INDEX cefv_s_combo (ce_id,cf_id);

ALTER TABLE cms_catalogue_efv_short ADD INDEX iscv_value (cv_value(250));

ALTER TABLE cms_catalogue_efv_short ADD INDEX sce_id (ce_id);

ALTER TABLE cms_catalogue_efv_short ADD INDEX scf_id (cf_id);

DROP TABLE IF EXISTS cms_catalogue_efv_short_trans;

CREATE TABLE cms_catalogue_efv_short_trans (
    cv_value longtext NOT NULL,
    ce_id integer NOT NULL,
    cf_id integer NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    cv_value__text_parsed longtext NOT NULL,
    cv_value__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_catalogue_efv_short_trans ADD FULLTEXT cv_value (cv_value);

ALTER TABLE cms_catalogue_efv_short_trans ADD INDEX cefv_st_combo (ce_id,cf_id);

ALTER TABLE cms_catalogue_efv_short_trans ADD INDEX stce_id (ce_id);

ALTER TABLE cms_catalogue_efv_short_trans ADD INDEX stcf_id (cf_id);

ALTER TABLE cms_catalogue_efv_short_trans ADD INDEX stcv_value (cv_value(250));

DROP TABLE IF EXISTS cms_catalogue_entries;

CREATE TABLE cms_catalogue_entries (
    allow_rating tinyint(1) NOT NULL,
    ce_last_moved integer NOT NULL,
    allow_trackbacks tinyint(1) NOT NULL,
    allow_comments tinyint NOT NULL,
    notes longtext NOT NULL,
    ce_validated tinyint(1) NOT NULL,
    ce_views_prior integer NOT NULL,
    ce_views integer NOT NULL,
    ce_edit_date integer unsigned NULL,
    ce_add_date integer unsigned NOT NULL,
    ce_submitter integer NOT NULL,
    cc_id integer NOT NULL,
    c_name varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_catalogue_entries ADD INDEX ce_add_date (ce_add_date);

ALTER TABLE cms_catalogue_entries ADD INDEX ce_c_name (c_name);

ALTER TABLE cms_catalogue_entries ADD INDEX ce_cc_id (cc_id);

ALTER TABLE cms_catalogue_entries ADD INDEX ce_validated (ce_validated);

ALTER TABLE cms_catalogue_entries ADD INDEX ce_views (ce_views);

ALTER TABLE cms_catalogue_entries ADD INDEX ces (ce_submitter);

DROP TABLE IF EXISTS cms_catalogue_entry_linkage;

CREATE TABLE cms_catalogue_entry_linkage (
    content_type varchar(80) NOT NULL,
    content_id varchar(80) NOT NULL,
    catalogue_entry_id integer NOT NULL,
    PRIMARY KEY (catalogue_entry_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_catalogue_entry_linkage ADD INDEX custom_fields (content_type,content_id);

DROP TABLE IF EXISTS cms_catalogue_fields;

CREATE TABLE cms_catalogue_fields (
    cf_options varchar(255) NOT NULL,
    cf_put_in_search tinyint(1) NOT NULL,
    cf_put_in_category tinyint(1) NOT NULL,
    cf_required tinyint(1) NOT NULL,
    cf_default longtext NOT NULL,
    cf_searchable tinyint(1) NOT NULL,
    cf_visible tinyint(1) NOT NULL,
    cf_defines_order tinyint NOT NULL,
    cf_order integer NOT NULL,
    cf_type varchar(80) NOT NULL,
    cf_description longtext NOT NULL,
    cf_name longtext NOT NULL,
    c_name varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (1, 'projects', 'Name', 'The name for this.', 'short_trans', 0, 1, 1, 1, '', 1, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (2, 'projects', 'Maintainer', 'The maintainer of this project.', 'member', 1, 0, 1, 1, '!', 1, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (3, 'projects', 'Description', 'A concise description for this.', 'long_trans', 2, 0, 1, 1, '', 1, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (4, 'projects', 'Project progress', 'The estimated percentage of completion of this project.', 'integer', 3, 0, 1, 1, '0', 1, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (5, 'links', 'Title', 'A concise title for this.', 'short_trans', 0, 1, 1, 1, '', 1, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (6, 'links', 'URL', 'The entered text will be interpreted as a URL, and used as a hyperlink.', 'url', 1, 0, 1, 1, '', 1, 0, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (7, 'links', 'Description', 'A concise description for this.', 'long_trans', 2, 0, 1, 1, '', 0, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (8, 'faqs', 'Question', 'The question asked.', 'short_trans', 0, 0, 1, 1, '', 1, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (9, 'faqs', 'Answer', 'The answer(s) to the question.', 'long_trans', 1, 0, 1, 1, '', 1, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (10, 'faqs', 'Order', 'The order priority this entry will be given in the entry category, relative to other entries.', 'integer', 2, 1, 0, 1, '', 0, 1, 1, 'AUTO_INCREMENT');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (11, 'contacts', 'Forename', '', 'short_text', 0, 0, 1, 1, '', 1, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (12, 'contacts', 'Surname', '', 'short_text', 1, 1, 1, 1, '', 1, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (13, 'contacts', 'E-mail address', '', 'short_text', 2, 0, 1, 1, '', 0, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (14, 'contacts', 'Company', '', 'short_text', 3, 0, 1, 1, '', 0, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (15, 'contacts', 'Home address', '', 'short_text', 4, 0, 1, 1, '', 0, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (16, 'contacts', 'City', '', 'short_text', 5, 0, 1, 1, '', 0, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (17, 'contacts', 'Home phone number', '', 'short_text', 6, 0, 1, 1, '', 0, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (18, 'contacts', 'Work phone number', '', 'short_text', 7, 0, 1, 1, '', 0, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (19, 'contacts', 'Homepage', '', 'short_text', 8, 0, 1, 1, '', 0, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (20, 'contacts', 'Instant messenger handle', '', 'short_text', 9, 0, 1, 1, '', 0, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (21, 'contacts', 'Notes', '', 'long_text', 10, 0, 1, 1, '', 0, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (22, 'contacts', 'Photo', '', 'picture', 11, 0, 1, 1, '', 0, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (23, 'products', 'Product title', 'A concise title for this.', 'short_trans', 0, 1, 1, 1, '', 1, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (24, 'products', 'Product code', 'The SKU (Stock Keeping Unit) for the product.', 'codename', 1, 0, 1, 1, 'RANDOM', 1, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (25, 'products', 'Gross Price', 'The price, before tax is added, in the primary currency of this website.', 'float', 2, 0, 1, 1, 'decimal_points_behaviour=price', 1, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (26, 'products', 'Stock level', 'The stock level of the product (leave blank if no stock counting is to be done).', 'integer', 3, 0, 1, 0, '', 0, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (27, 'products', 'Stock level warn-threshold', 'Send out a notification to the staff if the stock goes below this level (leave blank if no stock counting is to be done).', 'integer', 4, 0, 0, 0, '', 0, 0, 0, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (28, 'products', 'Stock maintained', 'Whether stock will be maintained. If the stock is not maintained then users will not be able to purchase it if the stock runs out.', 'list', 5, 0, 0, 0, 'No|Yes', 1, 0, 0, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (29, 'products', 'Product tax rate', 'The tax rates that products can be assigned.', 'list', 6, 0, 0, 0, '0%|5%|17.5%', 1, 0, 0, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (30, 'products', 'Product image', 'Upload an image of your product.', 'picture', 7, 0, 1, 1, '', 0, 1, 1, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (31, 'products', 'Product weight', 'The weight, in whatever units are assumed by the shipping costs programmed-in to this website.', 'float', 8, 0, 0, 0, '', 1, 0, 0, '');
INSERT INTO cms_catalogue_fields (id, c_name, cf_name, cf_description, cf_type, cf_order, cf_defines_order, cf_visible, cf_searchable, cf_default, cf_required, cf_put_in_category, cf_put_in_search, cf_options) VALUES (32, 'products', 'Product description', 'A concise description for this.', 'long_trans', 9, 0, 1, 1, '', 1, 1, 1, '');

ALTER TABLE cms_catalogue_fields ADD FULLTEXT cf_description (cf_description);

ALTER TABLE cms_catalogue_fields ADD FULLTEXT cf_name (cf_name);

DROP TABLE IF EXISTS cms_catalogues;

CREATE TABLE cms_catalogues (
    c_description longtext NOT NULL,
    c_send_view_reports varchar(80) NOT NULL,
    c_default_review_freq integer NULL,
    c_ecommerce tinyint(1) NOT NULL,
    c_submit_points integer NOT NULL,
    c_add_date integer unsigned NOT NULL,
    c_notes longtext NOT NULL,
    c_is_tree tinyint(1) NOT NULL,
    c_display_type tinyint NOT NULL,
    c_title longtext NOT NULL,
    c_name varchar(80) NOT NULL,
    c_description__text_parsed longtext NOT NULL,
    c_description__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (c_name)
) CHARACTER SET=utf8mb4 engine=MyISAM;

INSERT INTO cms_catalogues (c_name, c_title, c_description, c_display_type, c_is_tree, c_notes, c_add_date, c_submit_points, c_ecommerce, c_default_review_freq, c_send_view_reports, c_description__text_parsed, c_description__source_user) VALUES ('projects', 'Super-member projects', '', 0, 0, '', 1554920603, 30, 0, NULL, 'never', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_5cae349b0fc107.69225297_11\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:40:\\\"string_attach_5cae349b0fc107.69225297_11\\\";s:69:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_11\']=\\\"echo \\\\\\\"\\\\\\\";\\\";\\n\\\";}}\");
', 1);
INSERT INTO cms_catalogues (c_name, c_title, c_description, c_display_type, c_is_tree, c_notes, c_add_date, c_submit_points, c_ecommerce, c_default_review_freq, c_send_view_reports, c_description__text_parsed, c_description__source_user) VALUES ('links', 'Links', 'Warning: these sites are outside our control.', 2, 1, '', 1554920603, 0, 0, NULL, 'never', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_5cae349b0fc107.69225297_13\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:40:\\\"string_attach_5cae349b0fc107.69225297_13\\\";s:114:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_13\']=\\\"echo \\\\\\\"Warning: these sites are outside our control.\\\\\\\";\\\";\\n\\\";}}\");
', 1);
INSERT INTO cms_catalogues (c_name, c_title, c_description, c_display_type, c_is_tree, c_notes, c_add_date, c_submit_points, c_ecommerce, c_default_review_freq, c_send_view_reports, c_description__text_parsed, c_description__source_user) VALUES ('faqs', 'Frequently Asked Questions', '', 0, 0, '', 1554920603, 0, 0, NULL, 'never', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_5cae349b0fc107.69225297_15\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:40:\\\"string_attach_5cae349b0fc107.69225297_15\\\";s:69:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_15\']=\\\"echo \\\\\\\"\\\\\\\";\\\";\\n\\\";}}\");
', 1);
INSERT INTO cms_catalogues (c_name, c_title, c_description, c_display_type, c_is_tree, c_notes, c_add_date, c_submit_points, c_ecommerce, c_default_review_freq, c_send_view_reports, c_description__text_parsed, c_description__source_user) VALUES ('contacts', 'Contacts', 'A contacts/address-book.', 0, 0, '', 1554920603, 30, 0, NULL, 'never', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_5cae349b0fc107.69225297_17\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:40:\\\"string_attach_5cae349b0fc107.69225297_17\\\";s:93:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_17\']=\\\"echo \\\\\\\"A contacts/address-book.\\\\\\\";\\\";\\n\\\";}}\");
', 1);
INSERT INTO cms_catalogues (c_name, c_title, c_description, c_display_type, c_is_tree, c_notes, c_add_date, c_submit_points, c_ecommerce, c_default_review_freq, c_send_view_reports, c_description__text_parsed, c_description__source_user) VALUES ('products', 'Products', 'These are products for sale from this website.', 3, 1, '', 1554920603, 0, 1, NULL, 'never', 'return unserialize(\"a:5:{i:0;a:1:{i:0;a:1:{i:0;a:5:{i:0;s:40:\\\"string_attach_5cae349b0fc107.69225297_19\\\";i:1;a:0:{}i:2;i:1;i:3;s:0:\\\"\\\";i:4;s:0:\\\"\\\";}}}i:1;a:0:{}i:2;s:10:\\\":container\\\";i:3;N;i:4;a:1:{s:40:\\\"string_attach_5cae349b0fc107.69225297_19\\\";s:115:\\\"\\$tpl_funcs[\'string_attach_5cae349b0fc107.69225297_19\']=\\\"echo \\\\\\\"These are products for sale from this website.\\\\\\\";\\\";\\n\\\";}}\");
', 1);

ALTER TABLE cms_catalogues ADD FULLTEXT c_description (c_description);

ALTER TABLE cms_catalogues ADD FULLTEXT c_title (c_title);

DROP TABLE IF EXISTS cms_chargelog;

CREATE TABLE cms_chargelog (
    date_and_time integer unsigned NOT NULL,
    reason longtext NOT NULL,
    amount integer NOT NULL,
    member_id integer NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    reason__text_parsed longtext NOT NULL,
    reason__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_chargelog ADD FULLTEXT reason (reason);

DROP TABLE IF EXISTS cms_chat_active;

CREATE TABLE cms_chat_active (
    date_and_time integer unsigned NOT NULL,
    room_id integer NULL,
    member_id integer NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_chat_active ADD INDEX active_ordering (date_and_time);

ALTER TABLE cms_chat_active ADD INDEX member_select (member_id);

ALTER TABLE cms_chat_active ADD INDEX room_select (room_id);

DROP TABLE IF EXISTS cms_chat_blocking;

CREATE TABLE cms_chat_blocking (
    date_and_time integer unsigned NOT NULL,
    member_blocked integer NOT NULL,
    member_blocker integer NOT NULL,
    PRIMARY KEY (member_blocked, member_blocker)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_chat_events;

CREATE TABLE cms_chat_events (
    e_date_and_time integer unsigned NOT NULL,
    e_room_id integer NULL,
    e_member_id integer NOT NULL,
    e_type_code varchar(80) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_chat_events ADD INDEX event_ordering (e_date_and_time);

DROP TABLE IF EXISTS cms_chat_friends;

CREATE TABLE cms_chat_friends (
    date_and_time integer unsigned NOT NULL,
    member_liked integer NOT NULL,
    member_likes integer NOT NULL,
    PRIMARY KEY (member_liked, member_likes)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_chat_messages;

CREATE TABLE cms_chat_messages (
    text_colour varchar(255) NOT NULL,
    font_name varchar(255) NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    the_message longtext NOT NULL,
    date_and_time integer unsigned NOT NULL,
    member_id integer NOT NULL,
    room_id integer NOT NULL,
    ip_address varchar(40) NOT NULL,
    system_message tinyint(1) NOT NULL,
    the_message__text_parsed longtext NOT NULL,
    the_message__source_user integer DEFAULT 1 NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_chat_messages ADD FULLTEXT the_message (the_message);

ALTER TABLE cms_chat_messages ADD INDEX ordering (date_and_time);

ALTER TABLE cms_chat_messages ADD INDEX room_id (room_id);

DROP TABLE IF EXISTS cms_chat_rooms;

CREATE TABLE cms_chat_rooms (
    allow_list_groups longtext NOT NULL,
    is_im tinyint(1) NOT NULL,
    c_welcome longtext NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    room_name varchar(255) NOT NULL,
    room_owner integer NULL,
    allow_list longtext NOT NULL,
    room_language varchar(5) NOT NULL,
    disallow_list longtext NOT NULL,
    disallow_list_groups longtext NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

INSERT INTO cms_chat_rooms (id, room_name, room_owner, allow_list, allow_list_groups, disallow_list, disallow_list_groups, room_language, c_welcome, is_im) VALUES (1, 'General chat', NULL, '', '', '', '', 'EN', '', 0);

ALTER TABLE cms_chat_rooms ADD FULLTEXT c_welcome (c_welcome);

ALTER TABLE cms_chat_rooms ADD INDEX allow_list (allow_list(30));

ALTER TABLE cms_chat_rooms ADD INDEX first_public (is_im,id);

ALTER TABLE cms_chat_rooms ADD INDEX is_im (is_im);

ALTER TABLE cms_chat_rooms ADD INDEX room_name (room_name(250));

DROP TABLE IF EXISTS cms_chat_sound_effects;

CREATE TABLE cms_chat_sound_effects (
    s_url varchar(255) BINARY NOT NULL,
    s_member integer NOT NULL,
    s_effect_id varchar(80) NOT NULL,
    PRIMARY KEY (s_member, s_effect_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_comcode_pages;

CREATE TABLE cms_comcode_pages (
    p_order integer NOT NULL,
    the_zone varchar(80) NOT NULL,
    the_page varchar(80) NOT NULL,
    p_parent_page varchar(80) NOT NULL,
    p_validated tinyint(1) NOT NULL,
    p_edit_date integer unsigned NULL,
    p_add_date integer unsigned NOT NULL,
    p_submitter integer NOT NULL,
    p_show_as_edit tinyint(1) NOT NULL,
    PRIMARY KEY (the_zone, the_page)
) CHARACTER SET=utf8mb4 engine=MyISAM;

INSERT INTO cms_comcode_pages (the_zone, the_page, p_parent_page, p_validated, p_edit_date, p_add_date, p_submitter, p_show_as_edit, p_order) VALUES ('site', 'userguide_comcode', 'help', 1, NULL, 1554920593, 2, 0, 0);
INSERT INTO cms_comcode_pages (the_zone, the_page, p_parent_page, p_validated, p_edit_date, p_add_date, p_submitter, p_show_as_edit, p_order) VALUES ('', 'keymap', 'help', 1, NULL, 1554920593, 2, 0, 0);
INSERT INTO cms_comcode_pages (the_zone, the_page, p_parent_page, p_validated, p_edit_date, p_add_date, p_submitter, p_show_as_edit, p_order) VALUES ('site', 'userguide_chatcode', 'help', 1, NULL, 1554920604, 2, 0, 0);
INSERT INTO cms_comcode_pages (the_zone, the_page, p_parent_page, p_validated, p_edit_date, p_add_date, p_submitter, p_show_as_edit, p_order) VALUES ('site', 'popup_blockers', 'help', 1, NULL, 1554920604, 2, 0, 0);
INSERT INTO cms_comcode_pages (the_zone, the_page, p_parent_page, p_validated, p_edit_date, p_add_date, p_submitter, p_show_as_edit, p_order) VALUES ('', 'recommend_help', 'recommend', 1, NULL, 1554920618, 2, 0, 0);

ALTER TABLE cms_comcode_pages ADD INDEX p_add_date (p_add_date);

ALTER TABLE cms_comcode_pages ADD INDEX p_order (p_order);

ALTER TABLE cms_comcode_pages ADD INDEX p_submitter (p_submitter);

ALTER TABLE cms_comcode_pages ADD INDEX p_validated (p_validated);

DROP TABLE IF EXISTS cms_commandrchat;

CREATE TABLE cms_commandrchat (
    c_timestamp integer unsigned NOT NULL,
    c_incoming tinyint(1) NOT NULL,
    c_url varchar(255) BINARY NOT NULL,
    c_message longtext NOT NULL,
    id integer unsigned auto_increment NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_config;

CREATE TABLE cms_config (
    c_name varchar(80) NOT NULL,
    c_set tinyint(1) NOT NULL,
    c_value longtext NOT NULL,
    c_value_trans longtext NOT NULL,
    c_needs_dereference tinyint(1) NOT NULL,
    PRIMARY KEY (c_name)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_config ADD FULLTEXT c_value_trans (c_value_trans);

DROP TABLE IF EXISTS cms_content_privacy;

CREATE TABLE cms_content_privacy (
    friend_view tinyint(1) NOT NULL,
    member_view tinyint(1) NOT NULL,
    guest_view tinyint(1) NOT NULL,
    content_id varchar(80) NOT NULL,
    content_type varchar(80) NOT NULL,
    PRIMARY KEY (content_id, content_type)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_content_privacy ADD INDEX friend_view (friend_view);

ALTER TABLE cms_content_privacy ADD INDEX guest_view (guest_view);

ALTER TABLE cms_content_privacy ADD INDEX member_view (member_view);

DROP TABLE IF EXISTS cms_content_privacy__members;

CREATE TABLE cms_content_privacy__members (
    member_id integer NOT NULL,
    content_id varchar(80) NOT NULL,
    content_type varchar(80) NOT NULL,
    PRIMARY KEY (member_id, content_id, content_type)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_content_regions;

CREATE TABLE cms_content_regions (
    content_type varchar(80) NOT NULL,
    content_id varchar(80) NOT NULL,
    region varchar(80) NOT NULL,
    PRIMARY KEY (content_type, content_id, region)
) CHARACTER SET=utf8mb4 engine=MyISAM;

DROP TABLE IF EXISTS cms_content_reviews;

CREATE TABLE cms_content_reviews (
    content_type varchar(80) NOT NULL,
    content_id varchar(80) NOT NULL,
    review_freq integer NULL,
    next_review_time integer unsigned NOT NULL,
    auto_action varchar(80) NOT NULL,
    review_notification_happened tinyint(1) NOT NULL,
    display_review_status tinyint(1) NOT NULL,
    last_reviewed_time integer unsigned NOT NULL,
    PRIMARY KEY (content_type, content_id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_content_reviews ADD INDEX needs_review (next_review_time,content_type);

ALTER TABLE cms_content_reviews ADD INDEX next_review_time (next_review_time,review_notification_happened);

DROP TABLE IF EXISTS cms_cron_caching_requests;

CREATE TABLE cms_cron_caching_requests (
    id integer unsigned auto_increment NOT NULL,
    c_store_as_tempcode tinyint(1) NOT NULL,
    c_codename varchar(80) NOT NULL,
    c_member integer NULL,
    c_timezone varchar(40) NOT NULL,
    c_is_bot tinyint(1) NULL,
    c_groups varchar(255) NOT NULL,
    c_staff_status tinyint(1) NULL,
    c_theme varchar(80) NOT NULL,
    c_map longtext NOT NULL,
    c_lang varchar(5) NOT NULL,
    PRIMARY KEY (id)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_cron_caching_requests ADD INDEX c_compound (c_codename,c_theme,c_lang,c_timezone);

ALTER TABLE cms_cron_caching_requests ADD INDEX c_is_bot (c_is_bot);

ALTER TABLE cms_cron_caching_requests ADD INDEX c_store_as_tempcode (c_store_as_tempcode);

DROP TABLE IF EXISTS cms_custom_comcode;

CREATE TABLE cms_custom_comcode (
    tag_textual_tag tinyint(1) NOT NULL,
    tag_parameters longtext NOT NULL,
    tag_block_tag tinyint(1) NOT NULL,
    tag_tag varchar(80) NOT NULL,
    tag_dangerous_tag tinyint(1) NOT NULL,
    tag_enabled tinyint(1) NOT NULL,
    tag_example longtext NOT NULL,
    tag_replace longtext NOT NULL,
    tag_title longtext NOT NULL,
    tag_description longtext NOT NULL,
    PRIMARY KEY (tag_tag)
) CHARACTER SET=utf8mb4 engine=MyISAM;

ALTER TABLE cms_custom_comcode ADD FULLTEXT tag_description (tag_description);

ALTER TABLE cms_custom_comcode ADD FULLTEXT tag_title (tag_title);

