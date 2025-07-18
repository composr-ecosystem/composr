<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core_forum_drivers
 */

/**
 * Forum driver class.
 *
 * @package    core_forum_drivers
 */
class Forum_driver_none extends Forum_driver_base
{
    /**
     * Get the administration username for the site.
     *
     * @return string The admin username
     */
    public function get_admin_username()
    {
        global $SITE_INFO;
        $ret = (!empty($SITE_INFO['admin_username'])) ? $SITE_INFO['admin_username'] : 'admin';
        if ($ret == '') {
            $ret = 'admin';
        }
        return $ret;
    }

    /**
     * Get the rows for the top given number of posters on the forum.
     *
     * @param  integer $limit The limit to the number of top posters to fetch
     * @return array The rows for the given number of top posters in the forum
     */
    public function get_top_posters($limit)
    {
        return array(array(1));
    }

    /**
     * Attempt to to find the member's language from their forum profile. It converts between language-identifiers using a map (lang/map.ini).
     *
     * @param  MEMBER $member The member who's language needs to be fetched
     * @return ?LANGUAGE_NAME The member's language (null: unknown)
     */
    public function forum_get_lang($member)
    {
        return null;
    }

    /**
     * Find if login cookie is md5-hashed.
     *
     * @return boolean Whether the login cookie is md5-hashed
     */
    public function is_hashed()
    {
        return true;
    }

    /**
     * Find if the login cookie contains the login name instead of the member ID.
     *
     * @return boolean Whether the login cookie contains a login name or a member ID
     */
    public function is_cookie_login_name()
    {
        return true;
    }

    /**
     * Find the member ID of the forum guest member.
     *
     * @return MEMBER The member ID of the forum guest member
     */
    public function get_guest_id()
    {
        return 0;
    }

    /**
     * Add the specified custom field to the forum (some forums implemented this using proper custom profile fields, others through adding a new field).
     *
     * @param  string $name The name of the new custom field
     * @param  integer $length The length of the new custom field
     * @return boolean Whether the custom field was created successfully
     */
    public function install_create_custom_field($name, $length)
    {
        return false;
    }

    /**
     * Edit a custom profile field.
     *
     * @param  string $old_name The name of the current custom field
     * @param  string $new_name The new name of the custom profile field (blank: do not rename)
     * @param  integer $length The new length of the custom field
     * @return boolean Whether the custom field was edited successfully
     */
    public function install_edit_custom_field($old_name, $new_name, $length)
    {
        return false;
    }

    /**
     * Get an array of attributes to take in from the installer. Almost all forums require a table prefix, which the requirement there-of is defined through this function.
     * The attributes have 4 values in an array
     * - name, the name of the attribute for _config.php
     * - default, the default value (perhaps obtained through autodetection from forum config)
     * - description, a textual description of the attributes
     * - title, a textual title of the attribute
     *
     * @return array The attributes for the forum
     */
    public function install_specifics()
    {
        $c = array();
        $c['name'] = 'admin_username';
        $c['default'] = 'admin';
        $c['description'] = do_lang('DESCRIPTION_ADMIN_USERNAME');
        $c['title'] = do_lang('ADMIN_USERNAME');
        return array($c);
    }

    /**
     * Searches for forum auto-config at this path.
     *
     * @param  PATH $path The path in which to search
     * @return boolean Whether the forum auto-config could be found
     */
    public function install_test_load_from($path)
    {
        global $PROBED_FORUM_CONFIG;
        $PROBED_FORUM_CONFIG = array();
        $PROBED_FORUM_CONFIG['sql_database'] = 'cms';
        $PROBED_FORUM_CONFIG['sql_user'] = $GLOBALS['DB_STATIC_OBJECT']->db_default_user();
        $PROBED_FORUM_CONFIG['sql_pass'] = $GLOBALS['DB_STATIC_OBJECT']->db_default_password();
        return true;
    }

    /**
     * Get an array of paths to search for config at.
     *
     * @return array The paths in which to search for the forum config
     */
    public function install_get_path_search_list()
    {
        return array();
    }

    /**
     * Get an emoticon chooser template.
     *
     * @param  string $field_name The ID of the form field the emoticon chooser adds to
     * @return Tempcode The emoticon chooser template
     */
    public function get_emoticon_chooser($field_name = 'post')
    {
        require_code('comcode_compiler');
        $GLOBALS['NO_DB_SCOPE_CHECK'] = true;
        $emoticons = $GLOBALS['SITE_DB']->query_select('f_emoticons', array('*'), array('e_relevance_level' => 0), 'ORDER BY e_code');
        $GLOBALS['NO_DB_SCOPE_CHECK'] = false;
        $em = new Tempcode();
        foreach ($emoticons as $emo) {
            $code = $emo['e_code'];

            $em->attach(do_template('EMOTICON_CLICK_CODE', array('_GUID' => '0b51492b6e170db4466be74fdf312260', 'FIELD_NAME' => $field_name, 'CODE' => $code, 'IMAGE' => apply_emoticons($code))));
        }

        return $em;
    }

    /**
     * Find the base URL to the emoticons.
     *
     * @return URLPATH The base URL
     */
    public function get_emo_dir()
    {
        return '';
    }

    /**
     * Get a map between emoticon codes and templates representing the HTML-image-code for this emoticon. The emoticons presented of course depend on the forum involved.
     *
     * @return array The map
     */
    public function find_emoticons()
    {
        global $IN_MINIKERNEL_VERSION;
        if ($IN_MINIKERNEL_VERSION) {
            return array();
        }

        global $EMOTICON_LEVELS;
        if (!is_null($this->EMOTICON_CACHE)) {
            return $this->EMOTICON_CACHE;
        }
        $this->EMOTICON_CACHE = array();
        $EMOTICON_LEVELS = array();
        $GLOBALS['NO_DB_SCOPE_CHECK'] = true;
        $rows = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'f_emoticons WHERE e_relevance_level<4');
        $GLOBALS['NO_DB_SCOPE_CHECK'] = false;
        foreach ($rows as $myrow) {
            $this->EMOTICON_CACHE[$myrow['e_code']] = array('EMOTICON_IMG_CODE_THEMED', $myrow['e_theme_img_code'], $myrow['e_code']);
            $EMOTICON_LEVELS[$myrow['e_code']] = $myrow['e_relevance_level'];
        }
        uksort($this->EMOTICON_CACHE, '_strlen_sort');
        $this->EMOTICON_CACHE = array_reverse($this->EMOTICON_CACHE);
        return $this->EMOTICON_CACHE;
    }

    /**
     * Set a custom profile field's value, if the custom field exists. Only works on specially-named (titled) fields.
     *
     * @param  MEMBER $member The member ID
     * @param  string $field The field name (e.g. "firstname" for the CPF with a title of "cms_firstname")
     * @param  string $value The value
     */
    public function set_custom_field($member, $field, $value)
    {
    }

    /**
     * Get custom profile fields values for all 'cms_' prefixed keys.
     *
     * @param  MEMBER $member The member ID
     * @return ?array A map of the custom profile fields, key_suffix=>value (null: no fields)
     */
    public function get_custom_fields($member)
    {
        return array();
    }

    /**
     * Get a member row for the member of the given name.
     *
     * @param  SHORT_TEXT $name The member name
     * @return ?array The profile-row (null: no row)
     */
    public function get_mrow($name)
    {
        if ($name == $this->get_admin_username()) {
            return array(1);
        }
        return null;
    }

    /**
     * Get a member row.
     *
     * @param  AUTO_LINK $id The member ID
     * @return array The profile-row
     */
    public function get_member_row($id)
    {
        return array(0);
    }

    /**
     * Get a member row.
     *
     * @param  AUTO_LINK $id The member ID
     * @param  ID_TEXT $field The field
     * @return ?array The result (null: unknown)
     */
    public function get_member_row_field($id, $field)
    {
        return null;
    }

    /**
     * From a member row, get the member's primary usergroup.
     *
     * @param  array $r The profile-row
     * @return GROUP The member's primary usergroup
     */
    public function mrow_group($r)
    {
        if ($r[0] == 1) {
            return 1;
        }
        return 0;
    }

    /**
     * From a member row, get the member's member ID.
     *
     * @param  array $r The profile-row
     * @return MEMBER The member ID
     */
    public function mrow_id($r)
    {
        return $r[0];
    }

    /**
     * From a member row, get the member's last visit date.
     *
     * @param  array $r The profile-row
     * @return TIME The last visit date
     */
    public function mrow_lastvisit($r)
    {
        return time();
    }

    /**
     * From a member row, get the member's name.
     *
     * @param  array $r The profile-row
     * @return string The member name
     */
    public function mrow_username($r)
    {
        return $this->get_username($r[0]);
    }

    /**
     * From a member row, get the member's e-mail address.
     *
     * @param  array $r The profile-row
     * @return SHORT_TEXT The member e-mail address
     */
    public function mrow_email($r)
    {
        return $this->get_member_email_address($r[0]);
    }

    /**
     * Get a URL to the specified member's home (control panel).
     *
     * @param  MEMBER $id The member ID
     * @return URLPATH The URL to the members home
     */
    public function member_home_url($id)
    {
        return get_base_url();
    }

    /**
     * Get the photo thumbnail URL for the specified member ID.
     *
     * @param  MEMBER $member The member ID
     * @return URLPATH The URL (blank: none)
     */
    public function get_member_photo_url($member)
    {
        return '';
    }

    /**
     * Get the avatar URL for the specified member ID.
     *
     * @param  MEMBER $member The member ID
     * @return URLPATH The URL (blank: none)
     */
    public function get_member_avatar_url($member)
    {
        return '';
    }

    /**
     * Get a URL to the specified member's profile.
     *
     * @param  MEMBER $id The member ID
     * @return URLPATH The URL to the member profile
     */
    protected function _member_profile_url($id)
    {
        if (!addon_installed('authors')) {
            return get_base_url();
        }

        if ($id == 1) {
            $url = build_url(array('page' => 'authors', 'type' => 'browse', 'id' => $this->get_admin_username()), get_module_zone('authors'), null, false, false, true);
            return $url->evaluate();
        }
        $url = build_url(array('page' => 'authors', 'type' => 'browse', 'id' => do_lang('GUEST')), get_module_zone('authors'), null, false, false, true);
        return $url->evaluate();
    }

    /**
     * Get a URL to the registration page (for people to create member accounts).
     *
     * @return URLPATH The URL to the registration page
     */
    protected function _join_url()
    {
        return '';
    }

    /**
     * Get a URL to the members-online page.
     *
     * @return URLPATH The URL to the members-online page
     */
    protected function _users_online_url()
    {
        return '';
    }

    /**
     * Get a URL to send a private/personal message to the given member.
     *
     * @param  MEMBER $id The member ID
     * @return URLPATH The URL to the private/personal message page
     */
    protected function _member_pm_url($id)
    {
        return 'mailto:' . get_option('staff_address');
    }

    /**
     * Get a URL to the specified forum.
     *
     * @param  integer $id The forum ID
     * @return URLPATH The URL to the specified forum
     */
    protected function _forum_url($id)
    {
        return '';
    }

    /**
     * Get the forum ID from a forum name.
     *
     * @param  SHORT_TEXT $forum_name The forum name
     * @return ?integer The forum ID (null: not found)
     */
    public function forum_id_from_name($forum_name)
    {
        return null;
    }

    /**
     * Get the topic ID from a topic identifier in the specified forum. It is used by comment topics, which means that the unique-topic-name assumption holds valid.
     *
     * @param  string $forum The forum name / ID
     * @param  SHORT_TEXT $topic_identifier The topic identifier
     * @return ?integer The topic ID (null: not found)
     */
    public function find_topic_id_for_topic_identifier($forum, $topic_identifier)
    {
        return null;
    }

    /**
     * Makes a post in the specified forum, in the specified topic according to the given specifications. If the topic doesn't exist, it is created along with a spacer-post.
     * Spacer posts exist in order to allow staff to delete the first true post in a topic. Without spacers, this would not be possible with most forum systems. They also serve to provide meta information on the topic that cannot be encoded in the title (such as a link to the content being commented upon).
     *
     * @param  SHORT_TEXT $forum_name The forum name
     * @param  SHORT_TEXT $topic_identifier The topic identifier (usually <content-type>_<content-id>)
     * @param  MEMBER $member_id The member ID
     * @param  LONG_TEXT $post_title The post title
     * @param  LONG_TEXT $_post The post content in Comcode format
     * @param  string $content_title The topic title; must be same as content title if this is for a comment topic
     * @param  string $topic_identifier_encapsulation_prefix This is put together with the topic identifier to make a more-human-readable topic title or topic description (hopefully the latter and a $content_title title, but only if the forum supports descriptions)
     * @param  ?URLPATH $content_url URL to the content (null: do not make spacer post)
     * @param  ?TIME $time The post time (null: use current time)
     * @param  ?IP $ip The post IP address (null: use current members IP address)
     * @param  ?BINARY $validated Whether the post is validated (null: unknown, find whether it needs to be marked unvalidated initially). This only works with the Conversr driver.
     * @param  ?BINARY $topic_validated Whether the topic is validated (null: unknown, find whether it needs to be marked unvalidated initially). This only works with the Conversr driver.
     * @param  boolean $skip_post_checks Whether to skip post checks
     * @param  SHORT_TEXT $poster_name_if_guest The name of the poster
     * @param  ?AUTO_LINK $parent_id ID of post being replied to (null: N/A)
     * @param  boolean $staff_only Whether the reply is only visible to staff
     * @return array Topic ID (may be null), and whether a hidden post has been made
     */
    public function make_post_forum_topic($forum_name, $topic_identifier, $member_id, $post_title, $_post, $content_title, $topic_identifier_encapsulation_prefix, $content_url = null, $time = null, $ip = null, $validated = null, $topic_validated = 1, $skip_post_checks = false, $poster_name_if_guest = '', $parent_id = null, $staff_only = false)
    {
        return array(null, false);
    }

    /**
     * Get an array of maps for the topic in the given forum.
     *
     * @param  integer $topic_id The topic ID
     * @return mixed The array of maps (Each map is: title, message, member, date) (-1 for no such forum, -2 for no such topic)
     */
    public function get_forum_topic_posts($topic_id)
    {
        return (-1);
    }

    /**
     * Get a URL to the specified topic ID. Most forums don't require the second parameter, but some do, so it is required in the interface.
     *
     * @param  integer $id The topic ID
     * @param  string $forum The forum ID
     * @return URLPATH The URL to the topic
     */
    public function topic_url($id, $forum)
    {
        $url = build_url(array('page' => 'news', 'id' => $id), get_module_zone('news'), null, false, false, true);
        return $url->evaluate();
    }

    /**
     * Get a URL to the specified post ID.
     *
     * @param  integer $id The post ID
     * @param  string $forum The forum ID
     * @return URLPATH The URL to the post
     */
    public function post_url($id, $forum)
    {
        $url = build_url(array('page' => 'news', 'id' => $id), get_module_zone('news'), null, false, false, true);
        return $url->evaluate();
    }

    /**
     * Get an array of topics in the given forum. Each topic is an array with the following attributes:
     * - id, the topic ID
     * - title, the topic title
     * - lastusername, the username of the last poster
     * - lasttime, the timestamp of the last reply
     * - closed, a Boolean for whether the topic is currently closed or not
     * - firsttitle, the title of the first post
     * - firstpost, the first post (only set if $show_first_posts was true)
     *
     * @param  SHORT_TEXT $name The forum name
     * @param  integer $limit The limit
     * @param  integer $start The start position
     * @param  integer $max_rows The total rows (not a parameter: returns by reference)
     * @param  SHORT_TEXT $filter_topic_title The topic title filter
     * @param  boolean $show_first_posts Whether to show the first posts
     * @param  string $date_key The date key to sort by
     * @set    lasttime firsttime
     * @param  boolean $hot Whether to limit to hot topics
     * @param  SHORT_TEXT $filter_topic_description The topic description filter
     * @return ?array The array of topics (null: error)
     */
    public function show_forum_topics($name, $limit, $start, &$max_rows, $filter_topic_title = '', $show_first_posts = false, $date_key = 'lasttime', $hot = false, $filter_topic_description = '')
    {
        return null;
    }

    /**
     * Get an array of members who are in at least one of the given array of usergroups.
     *
     * @param  array $groups The array of usergroups
     * @param  ?integer $max Return up to this many entries for primary members and this many entries for secondary members (null: no limit, only use no limit if querying very restricted usergroups!)
     * @param  integer $start Return primary members after this offset and secondary members after this offset
     * @return ?array The array of members (null: no members)
     */
    public function member_group_query($groups, $max = null, $start = 0)
    {
        if (in_array(1, $groups)) {
            return array(array(1));
        }

        return array();
    }

    /**
     * This is the opposite of the get_next_member function.
     *
     * @param  MEMBER $member The member ID to decrement
     * @return ?MEMBER The previous member ID (null: no previous member)
     */
    public function get_previous_member($member)
    {
        return null; // Guest doesn't count
    }

    /**
     * Get the member ID of the next member after the given one, or null.
     * It cannot be assumed there are no gaps in member IDs, as members may be deleted.
     *
     * @param  MEMBER $member The member ID to increment
     * @return ?MEMBER The next member ID (null: no next member)
     */
    public function get_next_member($member)
    {
        if ($member < 1) {
            return 1;
        } else {
            return null;
        }
    }

    /**
     * Try to find a member with the given IP address
     *
     * @param  IP $ip The IP address
     * @return array The distinct rows found
     */
    public function probe_ip($ip)
    {
        return array();
    }

    /**
     * Get the name relating to the specified member ID.
     * If this returns null, then the member has been deleted. Always take potential null output into account.
     *
     * @param  MEMBER $member The member ID
     * @return ?SHORT_TEXT The member name (null: member deleted)
     */
    protected function _get_username($member)
    {
        if ($member == $this->get_guest_id()) {
            return do_lang('GUEST');
        }
        if ($member == 1) {
            return $this->get_admin_username();
        }
        return do_lang('GUEST'); // For now
    }

    /**
     * Get the e-mail address for the specified member ID.
     *
     * @param  MEMBER $member The member ID
     * @return SHORT_TEXT The e-mail address
     */
    protected function _get_member_email_address($member)
    {
        if ($member == 1) {
            return get_option('staff_address');
        }
        return '';
    }

    /**
     * Find if this member may have e-mails sent to them
     *
     * @param  MEMBER $member The member ID
     * @return boolean Whether the member may have e-mails sent to them
     */
    public function get_member_email_allowed($member)
    {
        return true;
    }

    /**
     * Get the timestamp of a member's join date.
     *
     * @param  MEMBER $member The member ID
     * @return TIME The timestamp
     */
    public function get_member_join_timestamp($member)
    {
        return filectime(get_file_base() . '/_config.php');
    }

    /**
     * Find all members with a name matching the given SQL LIKE string.
     *
     * @param  string $pattern The pattern
     * @param  ?integer $limit Maximum number to return (limits to the most recent active) (null: no limit)
     * @return ?array The array of matched members (null: none found)
     */
    public function get_matching_members($pattern, $limit = null)
    {
        return array();
    }

    /**
     * Get the given member's post count.
     *
     * @param  MEMBER $member The member ID
     * @return integer The post count
     */
    public function get_post_count($member)
    {
        return 0;
    }

    /**
     * Get the given member's topic count.
     *
     * @param  MEMBER $member The member ID
     * @return integer The topic count
     */
    public function get_topic_count($member)
    {
        return 0;
    }

    /**
     * Find out if the given member ID is banned.
     *
     * @param  MEMBER $member The member ID
     * @return boolean Whether the member is banned
     */
    public function is_banned($member)
    {
        return false;
    }

    /**
     * Try to find the theme that the logged-in/guest member is using, and map it to a Composr theme.
     * The themes/map.ini file functions to provide this mapping between forum themes, and Composr themes, and has a slightly different meaning for different forum drivers. For example, some drivers map the forum themes theme directory to the Composr theme name, while others made the humanly readeable name.
     *
     * @return ID_TEXT The theme
     */
    public function _get_theme()
    {
        return 'default';
    }

    /**
     * Find if the specified member ID is marked as staff or not.
     *
     * @param  MEMBER $member The member ID
     * @return boolean Whether the member is staff
     */
    protected function _is_staff($member)
    {
        return ($member == 1);
    }

    /**
     * Find if the specified member ID is marked as a super admin or not.
     *
     * @param  MEMBER $member The member ID
     * @return boolean Whether the member is a super admin
     */
    protected function _is_super_admin($member)
    {
        return ($member == 1);
    }

    /**
     * Get the number of members currently online on the forums.
     *
     * @return ?integer The number of members (null: NA)
     */
    public function get_num_users_forums()
    {
        return null;
    }

    /**
     * Get the number of members registered on the forum.
     *
     * @return integer The number of members
     */
    public function get_members()
    {
        return 1;
    }

    /**
     * Get the total topics ever made on the forum.
     *
     * @return integer The number of topics
     */
    public function get_topics()
    {
        return 0;
    }

    /**
     * Get the total posts ever made on the forum.
     *
     * @return integer The number of posts
     */
    public function get_num_forum_posts()
    {
        return 0;
    }

    /**
     * Get the number of new forum posts.
     *
     * @return integer The number of posts
     */
    protected function _get_num_new_forum_posts()
    {
        return 0;
    }

    /**
     * Get a member ID from the given member's username.
     *
     * @param  SHORT_TEXT $name The member name
     * @return MEMBER The member ID
     */
    public function get_member_from_username($name)
    {
        if ($name == $this->get_admin_username()) {
            return 1;
        }
        if ($name == do_lang('GUEST')) {
            return 0;
        }
        return 0;
    }

    /**
     * Get the IDs of the admin usergroups.
     *
     * @return array The admin usergroup IDs
     */
    protected function _get_super_admin_groups()
    {
        return array(1);
    }

    /**
     * Get the IDs of the moderator usergroups.
     * It should not be assumed that a member only has one usergroup - this depends upon the forum the driver works for. It also does not take the staff site filter into account.
     *
     * @return array The moderator usergroup IDs
     */
    protected function _get_moderator_groups()
    {
        return array();
    }

    /**
     * Get the forum usergroup list.
     *
     * @return array The usergroup list
     */
    protected function _get_usergroup_list()
    {
        return array(0 => do_lang('GUESTS'), 1 => do_lang('ADMINISTRATORS'));
    }

    /**
     * Get the forum usergroup relating to the specified member ID.
     *
     * @param  MEMBER $member The member ID
     * @return array The array of forum usergroups
     */
    protected function _get_members_groups($member)
    {
        if ($member == 1) {
            return array(db_get_first_id() + 1);
        }
        return array(0);
    }

    /**
     * Find if the given member ID and password is valid. If username is null, then the member ID is used instead.
     * All authorisation, cookies, and form-logins, are passed through this function.
     * Some forums do cookie logins differently, so a Boolean is passed in to indicate whether it is a cookie login.
     *
     * @param  ?SHORT_TEXT $username The member username (null: don't use this in the authentication - but look it up using the ID if needed)
     * @param  MEMBER $userid The member ID
     * @param  SHORT_TEXT $password_hashed The md5-hashed password
     * @param  string $password_raw The raw password
     * @param  boolean $cookie_login Whether this is a cookie login
     * @return array A map of 'id' and 'error'. If 'id' is null, an error occurred and 'error' is set
     */
    public function forum_authorise_login($username, $userid, $password_hashed, $password_raw, $cookie_login = false)
    {
        $out = array();
        $out['id'] = null;

        if (($username != $this->get_admin_username()) && ($userid != 1)) { // All hands to lifeboats
            $out['error'] = do_lang_tempcode((get_option('login_error_secrecy') == '1') ? 'MEMBER_INVALID_LOGIN' : '_MEMBER_NO_EXIST', $username);
            return $out;
        }

        require_code('crypt_master');
        if (!check_master_password($password_raw)) {
            $out['error'] = do_lang_tempcode((get_option('login_error_secrecy') == '1') ? 'MEMBER_INVALID_LOGIN' : 'MEMBER_BAD_PASSWORD');
            return $out;
        }

        $out['id'] = 1;
        return $out;
    }

    /**
     * Get a first known IP address of the given member.
     *
     * @param  MEMBER $id The member ID
     * @return IP The IP address
     */
    public function get_member_ip($id)
    {
        return '';
    }
}
