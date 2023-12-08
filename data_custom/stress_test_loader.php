<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>ocProducts maintains full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="http://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by ocProducts.</p></body></html>');
}

require($FILE_BASE . '/sources/global.php');

if (php_function_allowed('set_time_limit')) {
    @set_time_limit(0);
}
safe_ini_set('ocproducts.xss_detect', '0');
@header('Content-type: text/plain; charset=' . get_charset());
disable_php_memory_limit();
if (function_exists('gc_enable')) {
    gc_enable();
}

$GLOBALS['NO_QUERY_LIMIT'] = true;

do_work();

function do_work()
{
    $cli = ((php_sapi_name() == 'cli') && (empty($_SERVER['REMOTE_ADDR'])) && (empty($_ENV['REMOTE_ADDR'])));
    if (!$cli) {
        header('Content-type: text/plain; charset=' . get_charset());
        exit('Must run this script on command line, for security reasons');
    }

    $num_wanted = isset($_SERVER['argv'][1]) ? intval($_SERVER['argv'][1]) : 200;
    $want_zones = isset($_SERVER['argv'][2]) ? (in_array('zones', explode(',', $_SERVER['argv'][2]))) : false;

    require_code('config2');
    set_option('post_read_history_days', '0'); // Needed for a little sanity in recent post retrieval
    set_option('enable_sunk', '0');

    require_code('crypt');

    set_mass_import_mode();

    // members (remember to test the username autocompleter, and birthdays)
    // authors (remember to check author autocompleter and popup author list)
    // lots of people getting notifications on a forum
    // lots of people getting notifications on a topic
    require_code('authors');
    require_code('cns_members_action');
    require_code('notifications');
    for ($i = $GLOBALS['FORUM_DB']->query_select_value('f_members', 'COUNT(*)'); $i < $num_wanted; $i++) {
        $member_id = cns_make_member(uniqid('', false), uniqid('', true), uniqid('', true) . '@example.com', array(), intval(date('d')), intval(date('m')), intval(date('Y')), array(), null, null, 1, null, null, '', null, '', 0, 0, 1, '', '', '', 1, 1, null, 1, 1, null, '', false);
        add_author(random_line(), '', $member_id, random_text(), random_text());

        enable_notifications('cns_topic', 'forum:' . strval(db_get_first_id()), $member_id);

        enable_notifications('cns_topic', strval(db_get_first_id()), $member_id);

        // number of friends to a single member
        $GLOBALS['SITE_DB']->query_insert('chat_friends', array(
            'member_likes' => $member_id,
            'member_liked' => db_get_first_id() + 1,
            'date_and_time' => time()
        ), false, true);
    }
    $member_id = db_get_first_id() + 2;
    // point earn list / gift points to a single member
    require_code('points2');
    for ($j = $GLOBALS['SITE_DB']->query_select_value('gifts', 'COUNT(*)'); $j < $num_wanted; $j++) {
        give_points(10, $member_id, mt_rand(db_get_first_id(),/*don't want wide distribution as points caching then eats RAM*/
            min(100, $num_wanted - 1)), random_line(), false, false);
    }
    // number of friends of a single member
    for ($j = intval(floatval($GLOBALS['SITE_DB']->query_select_value('chat_friends', 'COUNT(*)')) / 2.0); $j < $num_wanted; $j++) {
        $GLOBALS['SITE_DB']->query_insert('chat_friends', array(
            'member_likes' => $member_id,
            'member_liked' => $j + db_get_first_id(),
            'date_and_time' => time()
        ), false, true);
    }
    echo 'done member/authors/points/notifications/friends stuff' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // banners
    require_code('banners');
    require_code('banners2');
    for ($i = $GLOBALS['SITE_DB']->query_select_value('banners', 'COUNT(*)'); $i < $num_wanted; $i++) {
        add_banner(uniqid('', false), get_logo_url(), random_line(), random_text(), '', 100, get_base_url(), 3, '', BANNER_PERMANENT, null, db_get_first_id() + 1, 1);
    }
    echo 'done banner stuff' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // comcode pages
    require_code('files');
    require_code('files2');
    for ($i = $GLOBALS['SITE_DB']->query_select_value('comcode_pages', 'COUNT(*)'); $i < $num_wanted; $i++) {
        $file = uniqid('', false);
        /*$path = get_custom_file_base() . '/site/pages/comcode_custom/' . fallback_lang() . '/' . $file . '.txt';
        cms_file_put_contents_safe($path, random_text(), FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);*/
        $GLOBALS['SITE_DB']->query_insert('comcode_pages', array(
            'the_zone' => 'site',
            'the_page' => $file,
            'p_parent_page' => '',
            'p_validated' => 1,
            'p_edit_date' => null,
            'p_add_date' => time(),
            'p_submitter' => db_get_first_id(),
            'p_show_as_edit' => 0,
            'p_order' => 0,
        ));
    }
    echo 'done comcode stuff' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // zones
    if ($want_zones) {
        require_code('zones2');
        require_code('abstract_file_manager');
        for ($i = $GLOBALS['SITE_DB']->query_select_value('zones', 'COUNT(*)'); $i < min($num_wanted, 1000/* lets be somewhat reasonable! */); $i++) {
            actual_add_zone(uniqid('', false), random_line(), 'start', random_line(), 'default', 0);
        }
        echo 'done zone stuff' . "\n";

        if (function_exists('gc_collect_cycles')) {
            gc_enable();
        }
    }

    // calendar events
    require_code('calendar2');
    for ($i = $GLOBALS['SITE_DB']->query_select_value('calendar_events', 'COUNT(*)'); $i < $num_wanted; $i++) {
        add_calendar_event(db_get_first_id(), '', null, 0, random_line(), random_text(), 1, intval(date('Y')), intval(date('m')), intval(date('d')), 'day_of_month', 0, 0);
    }
    echo 'done event stuff' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // chatrooms
    require_code('chat2');
    require_code('chat');
    for ($i = $GLOBALS['SITE_DB']->query_select_value('chat_rooms', 'COUNT(*)'); $i < $num_wanted; $i++) {
        $room_id = add_chatroom(random_text(), random_line(), mt_rand(db_get_first_id() + 1, $num_wanted - 1), strval(db_get_first_id() + 1), '', '', '', fallback_lang());
    }
    $room_id = db_get_first_id() + 1;
    // messages in chatroom
    for ($j = $GLOBALS['SITE_DB']->query_select_value('chat_messages', 'COUNT(*)'); $j < $num_wanted; $j++) {
        $map = array(
            'system_message' => 0,
            'ip_address' => '',
            'room_id' => $room_id,
            'member_id' => db_get_first_id(),
            'date_and_time' => time(),
            'text_colour' => get_option('chat_default_post_colour'),
            'font_name' => get_option('chat_default_post_font'),
        );
        $map += insert_lang_comcode('the_message', random_text(), 4);
        $GLOBALS['SITE_DB']->query_insert('chat_messages', $map);
    }
    echo 'done chat stuff' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // download categories under a subcategory
    require_code('downloads2');
    $subcat_id = add_download_category(random_line(), db_get_first_id(), random_text(), '');
    for ($i = $GLOBALS['SITE_DB']->query_select_value('download_categories', 'COUNT(*)'); $i < $num_wanted; $i++) {
        add_download_category(random_line(), $subcat_id, random_text(), '');
    }
    // downloads (remember to test content by the single author)
    require_code('downloads2');
    require_code('awards');
    $time = time();
    for ($i = $GLOBALS['SITE_DB']->query_select_value('download_downloads', 'COUNT(*)'); $i < $num_wanted; $i++) {
        $content_id = add_download(db_get_first_id(), random_line(), get_logo_url(), random_text(), 'admin', random_text(), null, 1, 1, 1, 1, '', uniqid('', true) . '.jpg', 100, 110, 1);
        give_award(db_get_first_id(), strval($content_id), $time - $i);
    }
    $content_id = db_get_first_id();
    $content_url = build_url(array('page' => 'downloads', 'type' => 'entry', 'id' => $content_id), 'site');
    for ($j = $GLOBALS['SITE_DB']->query_select_value('trackbacks', 'COUNT(*)'); $j < $num_wanted; $j++) {
        // trackbacks
        $GLOBALS['SITE_DB']->query_insert('trackbacks', array('trackback_for_type' => 'downloads', 'trackback_for_id' => strval($content_id), 'trackback_ip' => '', 'trackback_time' => time(), 'trackback_url' => '', 'trackback_title' => random_line(), 'trackback_excerpt' => random_text(), 'trackback_name' => random_line()));

        // ratings
        $GLOBALS['SITE_DB']->query_insert('rating', array('rating_for_type' => 'downloads', 'rating_for_id' => strval($content_id), 'rating_member' => $j + 1, 'rating_ip' => '', 'rating_time' => time(), 'rating' => 3));

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
    echo 'done download stuff' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // forums under a forum (don't test it can display, just make sure the main index still works)
    require_code('cns_forums_action');
    for ($i = $GLOBALS['FORUM_DB']->query_select_value('f_forums', 'COUNT(*)'); $i < $num_wanted; $i++) {
        cns_make_forum(random_line(), random_text(), db_get_first_id(), array(), db_get_first_id() + 3);
    }
    // forum topics
    require_code('cns_topics_action');
    require_code('cns_posts_action');
    require_code('cns_forums');
    require_code('cns_topics');
    for ($i = intval(floatval($GLOBALS['FORUM_DB']->query_select_value('f_topics', 'COUNT(*)')) / 2.0); $i < $num_wanted; $i++) {
        $topic_id = cns_make_topic(db_get_first_id(), '', '', null, 1, 0, 0, 0, null, null, false);
        cns_make_post($topic_id, random_line(), random_text(), 0, true, 0, 0, null, null, null, null, null, null, null, false, false);
    }
    // forum posts in a topic
    require_code('cns_topics_action');
    require_code('cns_posts_action');
    $topic_id = cns_make_topic(db_get_first_id() + 1, '', '', null, 1, 0, 0, 0, null, null, false);
    for ($i = intval(floatval($GLOBALS['FORUM_DB']->query_select_value('f_posts', 'COUNT(*)')) / 3.0); $i < $num_wanted; $i++) {
        cns_make_post($topic_id, random_line(), random_text(), 0, true, 0, 0, null, null, null, mt_rand(db_get_first_id(), $num_wanted - 1), null, null, null, false, false);
    }
    echo 'done forum stuff' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // clubs
    require_code('cns_groups_action');
    require_code('cns_groups');
    for ($i = $GLOBALS['FORUM_DB']->query_select_value('f_groups', 'COUNT(*)'); $i < $num_wanted; $i++) {
        cns_make_group(random_line(), 0, 0, 0, random_line(), '', null, null, null, 5, 0, 70, 50, 100, 100, 30000, 700, 25, 1, 0, 0, 0, $i, 1, 0, 1);
    }
    echo 'done club stuff' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // galleries under a subcategory
    require_code('galleries2');
    $xsubcat_id = uniqid('', false);
    add_gallery($xsubcat_id, random_line(), random_text(), '', 'root');
    for ($i = $GLOBALS['SITE_DB']->query_select_value('galleries', 'COUNT(*)'); $i < $num_wanted; $i++) {
        add_gallery(uniqid('', false), random_line(), random_text(), '', $xsubcat_id);
    }
    // images
    require_code('galleries2');
    for ($i = $GLOBALS['SITE_DB']->query_select_value('images', 'COUNT(*)'); $i < $num_wanted; $i++) {
        add_image('', 'root', random_text(), get_logo_url(), get_logo_url(), 1, 1, 1, 1, '');
    }
    // videos / validation queue
    require_code('galleries2');
    for ($i = $GLOBALS['SITE_DB']->query_select_value('videos', 'COUNT(*)'); $i < $num_wanted; $i++) {
        add_video('', 'root', random_text(), get_logo_url(), get_logo_url(), 0, 1, 1, 1, '', 0, 0, 0);
    }
    echo 'done galleries stuff' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // newsletter subscribers
    require_code('newsletter');
    for ($i = $GLOBALS['SITE_DB']->query_select_value('newsletter_subscribers', 'COUNT(*)'); $i < $num_wanted; $i++) {
        basic_newsletter_join(uniqid('', true) . '@example.com');
    }
    echo 'done newsletter stuff' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // polls (remember to test poll archive)
    require_code('polls2');
    for ($i = $GLOBALS['SITE_DB']->query_select_value('poll', 'COUNT(*)'); $i < $num_wanted; $i++) {
        $poll_id = add_poll(random_line(), random_line(), random_line(), random_line(), random_line(), random_line(), random_line(), random_line(), random_line(), random_line(), random_line(), 10, 0, 0, 0, 0, '');
    }
    // votes on a poll
    $poll_id = db_get_first_id();
    for ($j = $GLOBALS['SITE_DB']->query_select_value('poll_votes', 'COUNT(*)'); $j < $num_wanted; $j++) {
        $cast = mt_rand(1, 6);
        $ip = uniqid('', true);

        $GLOBALS['SITE_DB']->query_insert('poll_votes', array(
            'v_poll_id' => $poll_id,
            'v_voter_id' => 2,
            'v_voter_ip' => $ip,
            'v_vote_for' => $cast,
        ));
    }
    echo 'done polls stuff' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // quizzes
    require_code('quiz2');
    for ($i = $GLOBALS['SITE_DB']->query_select_value('quizzes', 'COUNT(*)'); $i < $num_wanted; $i++) {
        add_quiz(random_line(), 0, random_text(), random_text(), random_text(), '', 0, time(), null, 3, 300, 'SURVEY', 1, '1) Some question');
    }
    echo 'done quizzes stuff' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // successful searches (to test the search recommender)
    // ACTUALLY: I have manually verified the code, it is an isolated portion

    // Wiki+ pages (do a long descendant tree for some, and orphans for others)
    // Wiki+ posts (remember to test Wiki+ changes screen)
    require_code('wiki');
    for ($i = $GLOBALS['SITE_DB']->query_select_value('wiki_pages', 'COUNT(*)'); $i < $num_wanted; $i++) {
        $page_id = wiki_add_page(random_line(), random_text(), '', 1);
        wiki_add_post($page_id, random_text(), 1, null, false);
    }
    echo 'done Wiki+ stuff' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // logged hack attempts
    for ($i = $GLOBALS['SITE_DB']->query_select_value('hackattack', 'COUNT(*)'); $i < $num_wanted; $i++) {
        $GLOBALS['SITE_DB']->query_insert('hackattack', array(
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
            'reason_param_b' => ''
        ));
    }
    // logged hits in one day
    require_code('site');
    for ($i = $GLOBALS['SITE_DB']->query_select_value('stats', 'COUNT(*)'); $i < $num_wanted; $i++) {
        log_stats('/testing' . uniqid('', true), mt_rand(100, 2000));
    }
    echo 'done logs stuff' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // blogs and news entries (remember to test both blogs [categories] list, and a list of all news entries)
    require_code('news2');
    for ($i = $GLOBALS['SITE_DB']->query_select_value('news', 'COUNT(*)'); $i < $num_wanted; $i++) {
        add_news(random_line(), random_text(), 'admin', 1, 1, 1, 1, '', random_text(), null, null, null, db_get_first_id() + $i);
    }
    echo 'done news stuff' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // support tickets
    require_code('tickets');
    require_code('tickets2');
    for ($i = intval(floatval($GLOBALS['FORUM_DB']->query_select_value('f_topics', 'COUNT(*)')) / 2.0); $i < $num_wanted; $i++) {
        ticket_add_post(mt_rand(db_get_first_id(), $num_wanted - 1), strval(get_member()) . '_' . uniqid('', true), db_get_first_id(), random_line(), random_text(), '', false);
    }
    echo 'done tickets stuff' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // catalogues
    require_code('catalogues2');
    $root_id = db_get_first_id();
    for ($i = $GLOBALS['SITE_DB']->query_select_value('catalogues', 'COUNT(*)'); $i < $num_wanted; $i++) {
        $catalogue_name = uniqid('', false);
        actual_add_catalogue($catalogue_name, random_line(), random_text(), mt_rand(0, 3), 1, '', 30);
    }
    $catalogue_name = 'products';
    $root_id = $GLOBALS['SITE_DB']->query_select_value_if_there('catalogue_categories', 'id', array('c_name' => $catalogue_name));
    // catalogue categories under a subcategory (remember to test all catalogue views: atoz, index, and root cat)
    $subcat_id = actual_add_catalogue_category($catalogue_name, random_line(), random_text(), '', $root_id);
    for ($j = $GLOBALS['SITE_DB']->query_select_value('catalogue_categories', 'COUNT(*)'); $j < $num_wanted; $j++) {
        actual_add_catalogue_category($catalogue_name, random_line(), random_text(), '', $subcat_id);
    }
    echo 'done catalogue stuff' . "\n";

    if (function_exists('gc_collect_cycles')) {
        gc_enable();
    }

    // items in ecommerce store
    require_code('catalogues2');
    $cat_id = $GLOBALS['SITE_DB']->query_select_value('catalogue_categories', 'MIN(id)', array('c_name' => 'products'));
    $fields = collapse_1d_complexity('id', $GLOBALS['SITE_DB']->query_select('catalogue_fields', array('id'), array('c_name' => 'products')));
    for ($i = $GLOBALS['SITE_DB']->query_select_value('catalogue_entries', 'COUNT(*)'); $i < $num_wanted; $i++) {
        $map = array(
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
        );
        $pid = actual_add_catalogue_entry($cat_id, 1, '', 1, 1, 1, $map);
        unset($map);
    }
    // outstanding ecommerce orders
    $pid = $GLOBALS['SITE_DB']->query_select_value('catalogue_entries', 'MIN(id)', array('c_name' => 'products'));
    if ($pid === null) {
        $pid = db_get_first_id();
    }
    require_code('shopping');
    for ($j = $GLOBALS['SITE_DB']->query_select_value('shopping_cart', 'COUNT(*)'); $j < $num_wanted; $j++) {
        $product_det = array(
            'product_id' => $pid,
            'product_name' => $fields[0],
            'product_code' => $fields[1],
            'price' => $fields[2],
            'tax' => preg_replace('#[^\d\.]#', '', $fields[6]),
            'description' => $fields[9],
            'quantity' => mt_rand(1, 20),
            'product_type' => 'catalogue_items',
            'product_weight' => $fields[8]
        );
        $GLOBALS['SITE_DB']->query_insert('shopping_cart',
            array(
                'session_id' => get_rand_password(),
                'ordered_by' => mt_rand(db_get_first_id() + 1, $num_wanted - 1),
                'product_id' => $product_det['product_id'],
                'product_name' => $product_det['product_name'],
                'product_code' => $product_det['product_code'],
                'quantity' => $product_det['quantity'],
                'price' => round(floatval($product_det['price']), 2),
                'price_pre_tax' => $product_det['tax'],
                'product_description' => $product_det['description'],
                'product_type' => $product_det['product_type'],
                'product_weight' => $product_det['product_weight'],
                'is_deleted' => 0,
            )
        );
    }
    for ($j = $GLOBALS['SITE_DB']->query_select_value('shopping_order', 'COUNT(*)'); $j < $num_wanted; $j++) {
        $order_id = $GLOBALS['SITE_DB']->query_insert('shopping_order', array(
            'c_member' => mt_rand(db_get_first_id() + 1, $num_wanted - 1),
            'session_id' => get_rand_password(),
            'add_date' => time(),
            'tot_price' => '123.00',
            'order_status' => 'ORDER_STATUS_awaiting_payment',
            'notes' => '',
            'purchase_through' => 'purchase_module',
            'transaction_id' => '',
            'tax_opted_out' => 0,
        ), true);

        $GLOBALS['SITE_DB']->query_insert('shopping_order_details', array(
            'p_id' => 123,
            'p_name' => random_line(),
            'p_code' => 123,
            'p_type' => 'catalogue_items',
            'p_quantity' => 1,
            'p_price' => '12.00',
            'order_id' => $order_id,
            'dispatch_status' => 'ORDER_STATUS_awaiting_payment',
            'included_tax' => '1.00',
        ));
    }
    echo 'done store stuff' . "\n";

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
    static $words = array('fish', 'cheese', 'soup', 'tomato', 'alphabet', 'whatever', 'cannot', 'be', 'bothered', 'to', 'type', 'many', 'more', 'will', 'be', 'here', 'all', 'day');
    static $word_count = null;
    if (is_null($word_count)) {
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
    static $words = array('fish', 'cheese', 'soup', 'tomato', 'alphabet', 'whatever', 'cannot', 'be', 'bothered', 'to', 'type', 'many', 'more', 'will', 'be', 'here', 'all', 'day');
    static $word_count = null;
    if (is_null($word_count)) {
        $word_count = count($words);
    }

    $word = $words[mt_rand(0, $word_count - 1)];
    return md5(uniqid('', true)) . ' ' . $word . ' ' . md5(uniqid('', true));
}
