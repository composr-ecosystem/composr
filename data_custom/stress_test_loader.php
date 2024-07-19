<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    stress_test
 */

/*EXTRA FUNCTIONS: gc_enable*/

// Fixup SCRIPT_FILENAME potentially being missing
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

// Find Composr base directory, and chdir into it
global $FILE_BASE, $RELATIVE_PATH;
$FILE_BASE = (strpos(__FILE__, './') === false) ? __FILE__ : realpath(__FILE__);
$FILE_BASE = dirname($FILE_BASE);
if (!is_file($FILE_BASE . '/sources/global.php')) {
    $RELATIVE_PATH = basename($FILE_BASE);
    $FILE_BASE = dirname($FILE_BASE);
} else {
    $RELATIVE_PATH = '';
}

@chdir($FILE_BASE);

global $FORCE_INVISIBLE_GUEST;
$FORCE_INVISIBLE_GUEST = false;
global $EXTERNAL_CALL;
$EXTERNAL_CALL = false;
if (!is_file($FILE_BASE . '/sources/global.php')) {
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>The core developers maintain full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="https://composr.app">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by Christopher Graham.</p></body></html>');
}

require($FILE_BASE . '/sources/global.php');

if (!addon_installed('stress_test')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('stress_test')));
}

header('X-Robots-Tag: noindex');

cms_ini_set('ocproducts.xss_detect', '0');
@header('Content-Type: text/plain; charset=' . get_charset());
disable_php_memory_limit();
if (function_exists('gc_enable')) {
    gc_enable();
}

push_query_limiting(false);

do_work();

function do_work()
{
    $num_wanted = isset($_SERVER['argv'][1]) ? intval($_SERVER['argv'][1]) : 200;
    $want_zones = isset($_SERVER['argv'][2]) ? (in_array('zones', explode(',', $_SERVER['argv'][2]))) : false;

    cms_set_time_limit($num_wanted * 30);

    if (!is_cli()) {
        header('Content-Type: text/plain; charset=' . get_charset());
        exit('Must run this script on command line, for security reasons');
    }

    $deps = [
        'authors',
        'banners',
        'calendar',
        'catalogues',
        'chat',
        'cns_clubs',
        'downloads',
        'galleries',
        'news',
        'newsletter',
        'points',
        'polls',
        'quizzes',
        'shopping',
        'tickets',
        'wiki',
    ];
    foreach ($deps as $dep) {
        if (!addon_installed($dep)) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html($dep)));
        }
    }

    if (get_forum_type() != 'cns') {
        warn_exit(do_lang_tempcode('NO_CNS'));
    }

    require_code('config2');
    set_option('post_read_history_days', '0'); // Needed for a little sanity in recent post retrieval

    require_code('crypt');

    set_mass_import_mode();

    // members (remember to test the username autocompleter, and birthdays)
    // authors (remember to check author autocompleter and pop-up author list)
    // lots of people getting notifications on a forum
    // lots of people getting notifications on a topic
    require_code('authors');
    require_code('cns_members_action');
    require_code('notifications');
    require_code('crypt');
    echo 'STARTING: Create members' . "\n";
    for ($i = $GLOBALS['FORUM_DB']->query_select_value('f_members', 'COUNT(*)'); $i < $num_wanted; $i++) {
        $member_id = cns_make_member(
            uniqid('', false), // username
            get_secure_random_password(), // password, necessary to use crypt so we do not error on a password not being strong enough
            uniqid('', true) . '@example.com', // email_address
            null, // primary_group
            null, // secondary_groups
            intval(date('d')), // dob_day
            intval(date('m')), // dob_month
            intval(date('Y')), // dob_year
            [], // custom_fields
            null, // timezone
            '', // language
            '', // theme
            '', // title
            '', // photo_url
            null, // avatar_url
            '', // signature
            null, // preview_posts
            1, // reveal_age
            1, // views_signatures
            null, // auto_monitor_contrib_content
            null, // smart_topic_notification
            null, // mailing_list_style
            1, // auto_mark_read
            null, // sound_enabled
            1, // allow_emails
            1, // allow_emails_from_staff
            0, // highlighted_name
            '*', // pt_allow
            '', // pt_rules_text
            1, // validated
            '', // validated_email_confirm_code
            null, // probation_expiration_time
            '0', // is_perm_banned
            false // check_correctness
        );

        add_author(random_line(), '', $member_id, random_text(), random_text());

        set_notifications('cns_topic', 'forum:' . strval(db_get_first_id()), $member_id);

        set_notifications('cns_topic', strval(db_get_first_id()), $member_id);

        // number of friends to a single member
        $GLOBALS['SITE_DB']->query_insert('chat_friends', [
            'member_likes' => $member_id,
            'member_liked' => db_get_first_id() + 1,
            'date_and_time' => time(),
        ], false, true);
    }
    echo 'FINISHED: Create members' . "\n";
    $member_id = db_get_first_id() + 2;

    // point earn list to a single member
    require_code('points2');
    echo 'STARTING: Points' . "\n";
    for ($j = $GLOBALS['SITE_DB']->query_select_value('points_ledger', 'COUNT(*)'); $j < ($num_wanted * 6); $j += 6) {
        // Credit transaction with a random aggregate type
        points_credit_member(mt_rand(db_get_first_id(), $num_wanted - 1), random_line(), random_points(), 0, null, 0, random_t_type(), 'add', '');

        // Transactions between two members of random point values with a 1% chance of it using gift points too.
        points_transact(mt_rand(db_get_first_id(), $num_wanted - 1), $member_id, random_line(), random_points(), ((mt_rand(1, 100) == 1) ? null : 0), 0, null);
        points_transact($member_id, mt_rand(db_get_first_id(), $num_wanted - 1), random_line(), random_points(), ((mt_rand(1, 100) == 1) ? null : 0), 0, null);

        // Debit transaction
        points_debit_member(mt_rand(db_get_first_id(), $num_wanted - 1), random_line(), random_points(), 0, 0, null);

        // Credit transaction that gets reversed
        $reverse = points_credit_member($member_id, random_line(), random_points(), 0, null);
        points_transaction_reverse($reverse);

        // Flush runtime cache every 180 transactions
        if (($j % 180) == 0) {
            points_flush_runtime_cache();
        }
    }
    echo 'FINISHED: Points' . "\n";
    // number of friends of a single member
    echo 'STARTING: Friends' . "\n";
    for ($j = intval(floatval($GLOBALS['SITE_DB']->query_select_value('chat_friends', 'COUNT(*)')) / 2.0); $j < $num_wanted; $j++) {
        $GLOBALS['SITE_DB']->query_insert('chat_friends', [
            'member_likes' => $member_id,
            'member_liked' => $j + db_get_first_id(),
            'date_and_time' => time(),
        ], false, true);
    }
    echo 'FINISHED: Friends' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // banners
    require_code('banners');
    require_code('banners2');
    echo 'STARTING: Banners' . "\n";
    for ($i = $GLOBALS['SITE_DB']->query_select_value('banners', 'COUNT(*)'); $i < $num_wanted; $i++) {
        add_banner(uniqid('', false), get_logo_url(), random_line(), random_text(), '', 100, get_base_url(), 3, '', BANNER_PERMANENT, null, db_get_first_id() + 1, 1);
    }
    echo 'FINISHED: Banners' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // comcode pages
    require_code('files');
    require_code('files2');
    echo 'STARTING: Comcode Pages' . "\n";
    for ($i = $GLOBALS['SITE_DB']->query_select_value('comcode_pages', 'COUNT(*)'); $i < $num_wanted; $i++) {
        $file = uniqid('', false);
        /*$path = get_custom_file_base() . '/site/pages/comcode_custom/' . fallback_lang() . '/' . $file . '.txt';
        cms_file_put_contents_safe($path, random_text(), FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);*/
        $GLOBALS['SITE_DB']->query_insert('comcode_pages', [
            'the_zone' => 'site',
            'the_page' => $file,
            'p_parent_page' => '',
            'p_validated' => 1,
            'p_edit_date' => null,
            'p_add_date' => time(),
            'p_submitter' => db_get_first_id(),
            'p_show_as_edit' => 0,
            'p_include_on_sitemap' => 1,
            'p_order' => 0,
        ]);
    }
    echo 'FINISHED: Comcode Pages' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // zones
    if ($want_zones) {
        require_code('zones2');
        require_code('abstract_file_manager');
        echo 'STARTING: Zones' . "\n";
        for ($i = $GLOBALS['SITE_DB']->query_select_value('zones', 'COUNT(*)'); $i < min($num_wanted, 1000/* lets be somewhat reasonable! */); $i++) {
            actual_add_zone(uniqid('', false), random_line(), DEFAULT_ZONE_PAGE_NAME, random_line(), 'default', 0);
        }
        echo 'FINISHED: Zones' . "\n";

        if (function_exists('gc_collect_cycles')) {
            gc_enable();
        }
    }

    // calendar events
    require_code('calendar2');
    echo 'STARTING: Calendar events' . "\n";
    for ($i = $GLOBALS['SITE_DB']->query_select_value('calendar_events', 'COUNT(*)'); $i < $num_wanted; $i++) {
        add_calendar_event(db_get_first_id(), 'none', null, 0, random_line(), random_text(), 1, intval(date('Y')), intval(date('m')), intval(date('d')), 'day_of_month', 0, 0);
    }
    echo 'FINISHED: Calendar events' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // chatrooms
    require_code('chat2');
    require_code('chat');
    echo 'STARTING: Chat rooms' . "\n";
    for ($i = $GLOBALS['SITE_DB']->query_select_value('chat_rooms', 'COUNT(*)'); $i < $num_wanted; $i++) {
        $room_id = add_chatroom(random_text(), random_line(), mt_rand(db_get_first_id() + 1, $num_wanted - 1), strval(db_get_first_id() + 1), '', '', '', fallback_lang());
    }
    echo 'FINISHED: Chat rooms' . "\n";
    $room_id = db_get_first_id() + 1;

    // messages in chatroom
    echo 'STARTING: Chat messages' . "\n";
    for ($j = $GLOBALS['SITE_DB']->query_select_value('chat_messages', 'COUNT(*)'); $j < $num_wanted; $j++) {
        $map = [
            'system_message' => 0,
            'ip_address' => '',
            'room_id' => $room_id,
            'member_id' => db_get_first_id(),
            'date_and_time' => time(),
            'text_colour' => get_option('chat_default_post_colour'),
            'font_name' => get_option('chat_default_post_font'),
        ];
        $map += insert_lang_comcode('the_message', random_text(), 4);
        $GLOBALS['SITE_DB']->query_insert('chat_messages', $map);
    }
    echo 'FINISHED: Chat messages' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // download categories under a subcategory
    require_code('downloads2');
    $subcat_id = add_download_category(random_line(), db_get_first_id(), random_text(), '');
    echo 'STARTING: Download categories' . "\n";
    for ($i = $GLOBALS['SITE_DB']->query_select_value('download_categories', 'COUNT(*)'); $i < $num_wanted; $i++) {
        add_download_category(random_line(), $subcat_id, random_text(), '');
    }
    echo 'FINISHED: Download categories' . "\n";

    // downloads (remember to test content by the single author)
    require_code('downloads2');
    require_code('awards');
    $time = time();
    echo 'STARTING: Downloads / Awards' . "\n";
    for ($i = $GLOBALS['SITE_DB']->query_select_value('download_downloads', 'COUNT(*)'); $i < $num_wanted; $i++) {
        $content_id = add_download(db_get_first_id(), random_line(), get_logo_url(), random_text(), 'admin', random_text(), null, 1, 1, 1, 1, '', uniqid('', true) . '.jpg', 100, 110, 1);
        give_award(db_get_first_id(), strval($content_id), $time - $i);
    }
    echo 'FINISHED: Downloads / Awards' . "\n";

    $content_id = db_get_first_id();
    $content_url = build_url(['page' => 'downloads', 'type' => 'entry', 'id' => $content_id], get_module_zone('downloads'));
    echo 'STARTING: Trackbacks / Ratings / Comment topics' . "\n";
    for ($j = $GLOBALS['SITE_DB']->query_select_value('trackbacks', 'COUNT(*)'); $j < $num_wanted; $j++) {
        // trackbacks
        $GLOBALS['SITE_DB']->query_insert('trackbacks', ['trackback_for_type' => 'download', 'trackback_for_id' => strval($content_id), 'trackback_ip_address' => '', 'trackback_time' => time(), 'trackback_url' => '', 'trackback_title' => random_line(), 'trackback_excerpt' => random_text(), 'trackback_name' => random_line()]);

        // ratings
        $GLOBALS['SITE_DB']->query_insert('rating', ['rating_for_type' => 'download', 'rating_for_id' => strval($content_id), 'rating_member' => $j + 1, 'rating_ip_address' => '', 'rating_time' => time(), 'rating' => 3]);

        // posts in a comment topic
        $GLOBALS['FORUM_DRIVER']->make_post_forum_topic(
            get_option('comments_forum_name'),
            'downloads_' . strval($content_id),
            get_member(),
            random_line(),
            random_text(),
            random_line(),
            do_lang('COMMENT'),
            $content_url->evaluate(),
            null,
            null,
            1,
            1
        );
    }
    echo 'FINISHED: Trackbacks / Ratings / Comment topics' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // forums under a forum (don't test it can display, just make sure the main index still works)
    require_code('cns_forums_action');
    echo 'STARTING: Sub-forums' . "\n";
    for ($i = $GLOBALS['FORUM_DB']->query_select_value('f_forums', 'COUNT(*)'); $i < $num_wanted; $i++) {
        cns_make_forum(random_line(), random_text(), db_get_first_id(), [], db_get_first_id() + 3);
    }
    echo 'FINISHED: Sub-forums' . "\n";

    // forum topics
    require_code('cns_topics_action');
    require_code('cns_posts_action');
    require_code('cns_forums');
    require_code('cns_topics');
    echo 'STARTING: Topics' . "\n";
    for ($i = intval(floatval($GLOBALS['FORUM_DB']->query_select_value('f_topics', 'COUNT(*)')) / 2.0); $i < $num_wanted; $i++) {
        $topic_id = cns_make_topic(db_get_first_id(), '', '', null, 1, 0, 0, null, null, false);
        cns_make_post($topic_id, random_line(), random_text(), 0, true, 0, 0, null, null, null, null, null, null, null, false, false);
    }
    echo 'FINISHED: Topics' . "\n";

    // forum posts in a topic
    require_code('cns_topics_action');
    require_code('cns_posts_action');
    $topic_id = cns_make_topic(db_get_first_id() + 1, '', '', null, 1, 0, 0, null, null, false);
    echo 'STARTING: Topic Posts' . "\n";
    for ($i = intval(floatval($GLOBALS['FORUM_DB']->query_select_value('f_posts', 'COUNT(*)')) / 3.0); $i < $num_wanted; $i++) {
        cns_make_post($topic_id, random_line(), random_text(), 0, true, 0, 0, null, null, null, mt_rand(db_get_first_id(), $num_wanted - 1), null, null, null, false, false);
    }
    echo 'FINISHED: Topic Posts' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // clubs
    require_code('cns_groups_action');
    require_code('cns_groups');
    echo 'STARTING: Groups' . "\n";
    for ($i = $GLOBALS['FORUM_DB']->query_select_value('f_groups', 'COUNT(*)'); $i < $num_wanted; $i++) {
        cns_make_group(random_line(), 0, 0, 0, random_line(), '', null, null, 0, null, 5, 0, 70, 50, 100, 100, 30000, 700, 25, 1, 0, 0, 0, $i, 1, 0, 1);
    }
    echo 'FINISHED: Groups' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // galleries under a subcategory
    require_code('galleries2');
    $xsubcat_id = uniqid('', false);
    add_gallery($xsubcat_id, random_line(), random_text(), '', 'root');
    echo 'STARTING: Galleries in a Subcategory' . "\n";
    for ($i = $GLOBALS['SITE_DB']->query_select_value('galleries', 'COUNT(*)'); $i < $num_wanted; $i++) {
        add_gallery(uniqid('', false), random_line(), random_text(), '', $xsubcat_id);
    }
    echo 'FINISHED: Galleries in a Subcategory' . "\n";

    // images
    require_code('galleries2');
    echo 'STARTING: Images' . "\n";
    for ($i = $GLOBALS['SITE_DB']->query_select_value('images', 'COUNT(*)'); $i < $num_wanted; $i++) {
        add_image('', 'root', random_text(), get_logo_url(), 1, 1, 1, 1, '');
    }
    echo 'FINISHED: Images' . "\n";

    // videos / validation queue
    require_code('galleries2');
    echo 'STARTING: Videos which are not validated' . "\n";
    for ($i = $GLOBALS['SITE_DB']->query_select_value('videos', 'COUNT(*)'); $i < $num_wanted; $i++) {
        add_video('', 'root', random_text(), get_logo_url(), get_logo_url(), 0, 1, 1, 1, '', 0, 0, 0);
    }
    echo 'FINISHED: Videos which are not validated' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // newsletter subscribers
    require_code('newsletter');
    require_code('newsletter2');
    echo 'STARTING: Newsletter Subscriptions' . "\n";
    for ($i = $GLOBALS['SITE_DB']->query_select_value('newsletter_subscribers', 'COUNT(*)'); $i < $num_wanted; $i++) {
        basic_newsletter_join(uniqid('', true) . '@example.com');
    }
    echo 'FINISHED: Newsletter Subscriptions' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // polls (remember to test poll archive)
    require_code('polls2');
    echo 'STARTING: Polls' . "\n";
    for ($i = $GLOBALS['SITE_DB']->query_select_value('poll', 'COUNT(*)'); $i < $num_wanted; $i++) {
        $poll_id = add_poll(random_line(), random_line(), random_line(), random_line(), random_line(), random_line(), random_line(), random_line(), random_line(), random_line(), random_line(), 10, 0, 0, 0, 0, '');
    }
    echo 'FINISHED: Polls' . "\n";

    // votes on a poll
    $poll_id = db_get_first_id();
    echo 'STARTING: Poll Votes' . "\n";
    for ($j = $GLOBALS['SITE_DB']->query_select_value('poll_votes', 'COUNT(*)'); $j < $num_wanted; $j++) {
        $cast = mt_rand(1, 6);
        $ip = uniqid('', true);

        $GLOBALS['SITE_DB']->query_insert('poll_votes', [
            'v_poll_id' => $poll_id,
            'v_voting_member' => 2,
            'v_voting_ip_address' => $ip,
            'v_vote_for' => $cast,
            'v_vote_time' => time(),
        ]);
    }
    echo 'FINISHED: Poll Votes' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // quizzes
    require_code('quiz2');
    echo 'STARTING: Quizzes' . "\n";
    for ($i = $GLOBALS['SITE_DB']->query_select_value('quizzes', 'COUNT(*)'); $i < $num_wanted; $i++) {
        add_quiz(random_line(), 0, random_text(), random_text(), random_text(), '', 0, time(), null, 3, 300, 'SURVEY', 1, '1) Some question');
    }
    echo 'FINISHED: Quizzes' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // successful searches (to test the search recommender)
    // ACTUALLY: I have manually verified the code, it is an isolated portion

    // Wiki+ pages (do a long descendant tree for some, and orphans for others)
    // Wiki+ posts (remember to test Wiki+ changes screen)
    require_code('wiki');
    echo 'STARTING: Wiki+' . "\n";
    for ($i = $GLOBALS['SITE_DB']->query_select_value('wiki_pages', 'COUNT(*)'); $i < $num_wanted; $i++) {
        $page_id = wiki_add_page(random_line(), random_text(), '', 1);
        wiki_add_post($page_id, random_text(), 1, null, false);
    }
    echo 'FINISHED: Wiki+' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // logged hack attempts
    echo 'STARTING: Hack Attack Logs' . "\n";
    for ($i = $GLOBALS['SITE_DB']->query_select_value('hackattack', 'COUNT(*)'); $i < $num_wanted; $i++) {
        $GLOBALS['SITE_DB']->query_insert('hackattack', [
            'url' => get_base_url(),
            'data_post' => '',
            'user_agent' => '',
            'referer' => '',
            'user_os' => '',
            'member_id' => db_get_first_id(),
            'date_and_time' => time(),
            'ip' => uniqid('', true),
            'reason' => 'ASCII_ENTITY_URL_HACK',
            'reason_param_a' => '',
            'reason_param_b' => '',
            'percentage_score' => 100,
            'silent_to_staff_log' => 0,
        ]);
    }
    echo 'FINISHED: Hack Attack Logs' . "\n";

    // logged hits in one day
    require_code('site');
    echo 'STARTING: Page Hits' . "\n";
    for ($i = $GLOBALS['SITE_DB']->query_select_value('stats', 'COUNT(*)'); $i < $num_wanted; $i++) {
        log_stats(':' . uniqid('', true), mt_rand(100, 2000));
    }
    echo 'FINISHED: Page Hits' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // blogs and news entries (remember to test both blogs [categories] list, and a list of all news entries)
    require_code('news2');
    echo 'STARTING: Blogs and News' . "\n";
    for ($i = $GLOBALS['SITE_DB']->query_select_value('news', 'COUNT(*)'); $i < $num_wanted; $i++) {
        add_news(random_line(), random_text(), 'admin', 1, 1, 1, 1, '', random_text(), null, [], null, db_get_first_id() + $i);
    }
    echo 'FINISHED: Blogs and News' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // support tickets
    require_lang('tickets');
    require_code('tickets');
    require_code('tickets2');
    echo 'STARTING: Support Tickets' . "\n";
    for ($i = intval(floatval($GLOBALS['FORUM_DB']->query_select_value('f_topics', 'COUNT(*)')) / 2.0); $i < $num_wanted; $i++) {
        $ticket_member_id = mt_rand(db_get_first_id(), $num_wanted - 1);
        ticket_add_post(ticket_generate_new_id($ticket_member_id), db_get_first_id(), random_line(), random_text(), false, $ticket_member_id);
    }
    echo 'FINISHED: Support Tickets' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // catalogues
    require_code('catalogues2');
    $root_id = db_get_first_id();
    echo 'STARTING: Catalogues' . "\n";
    for ($i = $GLOBALS['SITE_DB']->query_select_value('catalogues', 'COUNT(*)'); $i < $num_wanted; $i++) {
        $catalogue_name = uniqid('', false);
        actual_add_catalogue($catalogue_name, random_line(), random_text(), mt_rand(0, 3), 1, '', 30);
        actual_add_catalogue_field($catalogue_name, uniqid('', false), random_text(), 'short_text', null, 0, 1, 0);
    }
    echo 'FINISHED: Catalogues' . "\n";
    $catalogue_name = 'products';
    $root_id = $GLOBALS['SITE_DB']->query_select_value_if_there('catalogue_categories', 'id', ['c_name' => $catalogue_name]);
    // catalogue categories under a subcategory (remember to test all catalogue views: atoz, index, and root cat)
    $subcat_id = actual_add_catalogue_category($catalogue_name, random_line(), random_text(), '', $root_id);
    echo 'STARTING: Catalogue Sub-categories' . "\n";
    for ($j = $GLOBALS['SITE_DB']->query_select_value('catalogue_categories', 'COUNT(*)'); $j < $num_wanted; $j++) {
        actual_add_catalogue_category($catalogue_name, random_line(), random_text(), '', $subcat_id);
    }
    echo 'FINISHED: Catalogue Sub-categories' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // items in shopping catalogue
    require_code('catalogues2');
    $cat_id = $GLOBALS['SITE_DB']->query_select_value('catalogue_categories', 'MIN(id)', ['c_name' => 'products']);
    $fields = collapse_1d_complexity('id', $GLOBALS['SITE_DB']->query_select('catalogue_fields', ['id'], ['c_name' => 'products']));
    echo 'STARTING: Shopping Items (Catalogue)' . "\n";
    for ($i = $GLOBALS['SITE_DB']->query_select_value('catalogue_entries', 'COUNT(*)'); $i < $num_wanted; $i++) {
        $map = [
            $fields[0] => random_line(),
            $fields[1] => uniqid('', true),
            $fields[2] => '1.0',
            $fields[3] => '1',
            $fields[4] => '0',
            $fields[5] => '1',
            $fields[6] => '0%',
            $fields[7] => get_logo_url(),
            $fields[8] => '2.0',
            $fields[9] => random_text(),
        ];
        $pid = actual_add_catalogue_entry($cat_id, 1, '', 1, 1, 1, $map);
        unset($map);
    }
    echo 'FINISHED: Shopping Items (Catalogue)' . "\n";
    // outstanding shopping orders
    $pid = $GLOBALS['SITE_DB']->query_select_value('catalogue_entries', 'MIN(id)', ['c_name' => 'products']);
    if ($pid === null) {
        $pid = db_get_first_id();
    }
    require_code('shopping');
    echo 'STARTING: Shopping Cart' . "\n";
    for ($j = $GLOBALS['SITE_DB']->query_select_value('shopping_cart', 'COUNT(*)'); $j < $num_wanted; $j++) {
        $GLOBALS['SITE_DB']->query_insert('shopping_cart', [
            'session_id' => get_secure_random_string(),
            'ordering_member' => mt_rand(db_get_first_id() + 1, $num_wanted - 1),
            'type_code' => strval(db_get_first_id()),
            'purchase_id' => strval(get_member()),
            'quantity' => 1,
            'add_time' => time()
        ]);
    }
    echo 'FINISHED: Shopping Cart' . "\n";
    echo 'STARTING: Shopping Orders' . "\n";
    for ($j = $GLOBALS['SITE_DB']->query_select_value('shopping_orders', 'COUNT(*)'); $j < $num_wanted; $j++) {
        $order_id = $GLOBALS['SITE_DB']->query_insert('shopping_orders', [
            'member_id' => mt_rand(db_get_first_id() + 1, $num_wanted - 1),
            'session_id' => get_secure_random_string(),
            'add_date' => time(),
            'total_price' => 10.00,
            'total_tax_derivation' => '',
            'total_tax' => 1.00,
            'total_tax_tracking' => '',
            'total_shipping_cost' => 2.00,
            'total_shipping_tax' => 0.00,
            'total_product_weight' => 0.00,
            'total_product_length' => 0.00,
            'total_product_width' => 0.00,
            'total_product_height' => 0.00,
            'order_currency' => get_option('currency'),
            'order_status' => 'ORDER_STATUS_awaiting_payment',
            'notes' => '',
            'purchase_through' => 'purchase_module',
            'txn_id' => '',
        ], true);

        $GLOBALS['SITE_DB']->query_insert('shopping_order_details', [
            'p_type_code' => '123',
            'p_purchase_id' => '',
            'p_name' => random_line(),
            'p_sku' => '123',
            'p_quantity' => 1,
            'p_price' => 10.00,
            'p_tax_code' => '10.0',
            'p_tax' => 1.00,
            'p_order_id' => $order_id,
            'p_dispatch_status' => 'ORDER_STATUS_awaiting_payment',
        ]);
    }
    echo 'FINISHED: Shopping Orders' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    echo '{{DONE}}' . "\n";
}

/* General things to test after we have this data:
 *  Searching
 *  Browsing
 *  Choosing-to-edit
 *  RSS
 *  Using blocks
 *  Cleanup tools
 *  Content translate queue
 *  load_all_category_permissions
 *  Anywhere else the data is queried (grep the code)
 *
 *  Generally click around and try and use the site
 */

function random_text()
{
    static $words = ['fish', 'cheese', 'soup', 'tomato', 'alphabet', 'whatever', 'cannot', 'be', 'bothered', 'to', 'type', 'many', 'more', 'will', 'be', 'here', 'all', 'day'];
    static $word_count = null;
    if ($word_count === null) {
        $word_count = count($words);
    }

    $out = '';
    for ($i = 0; $i < 30; $i++) {
        if ($i != 0) {
            $out .= ' ';
        }
        $out .= $words[mt_rand(0, $word_count - 1)];
    }
    return $out;
}

function random_line()
{
    static $words = ['fish', 'cheese', 'soup', 'tomato', 'alphabet', 'whatever', 'cannot', 'be', 'bothered', 'to', 'type', 'many', 'more', 'will', 'be', 'here', 'all', 'day'];
    static $word_count = null;
    if ($word_count === null) {
        $word_count = count($words);
    }

    $word = $words[mt_rand(0, $word_count - 1)];
    return md5(uniqid('', true)) . ' ' . $word . ' ' . md5(uniqid('', true));
}

/**
 * Return a random integer between 1 and 10,000 with a 50% chance of it being <100 and 50% chance of it being >= 100.
 *
 * @return integer A random number
 */
function random_points() : int
{
    $num1 = mt_rand(1, 10000);
    $num2 = mt_rand(1, 100);
    return intval(floor($num1 / $num2) + 1.0);
}

/**
 * Return a random content type (t_type) that could be used in a points transaction.
 *
 * @return string A content type
 */
function random_t_type() : string
{
    $content_types = [
        'escrow',
        'banner',
        'comcode_page',
        'zone',
        'event',
        'chat',
        'chat_message',
        'download',
        'download_download',
        'forum',
        'topic',
        'post',
        'image',
        'video',
        'poll',
        'quiz',
        'wiki_page',
        'wiki_post',
        'news',
        'catalogue_entry'
    ];

    return $content_types[mt_rand(0, (count($content_types) - 1))];
}
