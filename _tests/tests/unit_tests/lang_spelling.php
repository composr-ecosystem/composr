<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class lang_spelling_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('lang_compile');
        require_code('files2');

        cms_extend_time_limit(TIME_LIMIT_EXTEND__MODEST);
    }

    public function testLangMistakes()
    {
        $verbose = (get_param_integer('verbose', 0) == 1);

        // Language files...

        $files = get_directory_contents(get_file_base() . '/lang/EN', get_file_base() . '/lang/EN', null, false, true, ['ini']);
        foreach ($files as $path) {
            $input = [];
            _get_lang_file_map($path, $input, 'strings', false, false, 'EN');

            foreach ($input as $key => $string) {
                $this->check($this, $path, $key, $string, $verbose);
            }
        }

        // Text files...

        $dirs = [
            'text',
            'text/EN',
            'pages/comcode/EN',
            'site/pages/comcode/EN',
            'docs/pages/comcode_custom/EN',
        ];
        if ((function_exists('git_repos')) && (git_repos() == 'composr_homesite')) {
            $dirs = array_merge($dirs, [
                'text_custom',
                'text_custom/EN',
                'pages/comcode_custom/EN',
                'site/pages/comcode_custom/EN',
            ]);
        }
        foreach ($dirs as $dir) {
            $files = get_directory_contents(get_file_base() . '/' . $dir, get_file_base() . '/' . $dir, null, false, true, ['txt']);
            foreach ($files as $path) {
                // Exceptions
                if (in_array(basename($path), [
                    'synonyms.txt',
                    'unbannable_ips.txt',
                    'sitemap.txt',
                ])) {
                    continue;
                }

                $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

                $this->check($this, $path, null, $c, $verbose);
            }
        }

        // Particular files...

        $path = get_file_base();
        $files = [
            'uploads/website_specific/compo.sr/errorservice.csv',
            'data/maintenance_status.csv',
        ];
        foreach ($files as $_path) {
            $c = cms_file_get_contents_safe($path . '/' . $_path);

            if ($_path == 'data/maintenance_status.csv') {
                $c = preg_replace('#^.*,#mU', '', $c);
            }

            $this->check($this, $path . '/' . $_path, null, $c, $verbose);
        }
    }

    // NB: This is also called from the other spell check test, which scans a much larger field of text
    public function check($ob, $path, $key, $string, $verbose)
    {
        $file = basename($path);

        $string = preg_replace('#https?://[^\s\'"]*#', '', $string);
        $string = preg_replace('#data_custom/[\w\./]*#', '', $string);
        $string = preg_replace('#\{\$[^\{\}]*\}#', '', $string);
        $string = preg_replace('#\[code.*\[/code\]#Us', '', $string);
        $string = preg_replace('#\[codebox.*\[/codebox\]#Us', '', $string);
        $string = preg_replace('#\[tt.*\[/tt\]#Us', '', $string);
        $string = preg_replace('#<kbd.*</kbd>#Us', '', $string);

        $matches = [];

        // Support debranding
        if ($key !== null) {
            if (
                (strpos($string, 'Composr') !== false) &&

                (strpos($key, 'SETUPWIZARD') === false) &&
                (strpos($key, 'composr_fast_custom_index') === false) &&
                (strpos($key, 'COMPOSR_FAST_CUSTOM_INDEX') === false) &&
                (!in_array($key, [
                    'NO_PHP_IN_TEMPLATES',
                    'FORUM_BASE_URL_INVALID',
                    'WHAT_TO_EXPECT',
                    'DESCRIPTION_INCLUDE_CMS_ADVERT',
                    'INCLUDE_CMS_ADVERT',
                    'UNINSTALL_WARNING',
                    'SOFTWARE_CHAT_EXTRA',
                    'CONFIG_CATEGORY_COMPOSR_APIS',
                    'CONFIG_CATEGORY_DESCRIPTION__COMPOSR_APIS',
                    'CANNOT_CONNECT_HOME',
                    'NO_GD_ON_SERVER',
                    'MYSQL_TOO_OLD',
                    'MARIADB_TOO_OLD',
                    'APACHE_TOO_OLD',
                    'LOW_MEMORY_LIMIT',
                    'NO_ZIP_ON_SERVER',
                    'WARNING_MBSTRING_FUNC_OVERLOAD',
                    'DISABLED_FUNCTION',
                    'DASHBOARD_COMPOSR_NEWS',
                    'CONFIG_OPTION_dashboard_composr_news',
                    'CONFIG_OPTION_dashboard_tips',
                    'CONFIG_OPTION_keywords',
                    'DESCRIPTION_META_KEYWORDS',
                    'DEFAULT_SLIDE3_TEXT',
                    'THEME_COMPOSR_VERSION',
                    'THEME_DESCRIPTION_COMPOSR_VERSION',
                    'CONFIG_OPTION_csrf_token_expire_new',
                    'CONFIG_OPTION_csrf_token_expire_fresh',
                ])) &&
                (!in_array($file, [
                    'lang.ini',
                    'version.ini',
                    'debrand.ini',
                    'import.ini',
                    'installer.ini',
                    'upgrade.ini',
                    'commandr.ini',
                    'addons.ini',
                ]))
            ) {
                $ob->assertTrue(false, 'The word \'Composr\' was used in ' . $path . ' (' . $key . '). This should probably be changed to \'the software\'.');
            }
        }

        // Bad use of it's. Imperfect test, but would rather have it anyway due to the prevalence of these mistakes. People can expand contractions to workaround.
        if ($path != 'sources/notifications.php') {
            $matches = [];
            if (preg_match('#it\'s (own|permission|id |definition|filename|contents|run|database|parent|child|cach|interface|use |various|result|properties)#', $string, $matches) != 0) {
                $ob->assertTrue(false, 'The phrase "' . $matches[0] . '" was used in ' . $path . '. This should be changed to "its own". There could be more infractions that we could not auto-detect.');
            }
        }

        // Hyphen wanted (we want our canonical way)
        if (
            (preg_match('#([^\[\]\|"\'/_])email#', $string, $matches) != 0) &&
            (!in_array($file, ['sup_facebook.txt', 'tut_fields.txt'])) &&
            (($key === null) || (stripos($string, '/') === false) && (stripos($string, 'codename') === false)) &&
            (stripos($string, 'Automatic code inserts after this') === false)
        ) {
            $prefix = $matches[1];
            $ob->assertTrue(false, 'The term \'email\' was used in ' . $path . '. (prefix is ' . $prefix . ') This should be changed to \'e-mail\'.');
        }
        if (stripos($string, 'comma separated') !== false) {
            $ob->assertTrue(false, 'The phrase \'comma separated\' was used in ' . $path . '. This should be changed to \'comma-separated\'.');
        }
        if (stripos($string, 'front end') !== false) {
            $ob->assertTrue(false, 'The phrase \'front end\' was used in ' . $path . '. This should be changed to \'front-end\'.');
        }
        if (stripos($string, 'back end') !== false) {
            $ob->assertTrue(false, 'The phrase \'back end\' was used in ' . $path . '. This should be changed to \'back-end\'.');
        }
        if (stripos($string, 'meta tree') !== false) {
            $ob->assertTrue(false, 'The phrase \'meta tree\' was used in ' . $path . '. This should be changed to \'meta-tree\'.');
        }
        if ((stripos($string, 'screen reader') !== false) || (stripos($string, 'screenreader') !== false)) {
            $ob->assertTrue(false, 'The phrase \'screen reader\' was used in ' . $path . '. This should be changed to \'screen-reader\'.');
        }
        if (preg_match('#([^\[\]<>\|"\'/_])popup#', $string, $matches) != 0) {
            $ob->assertTrue(false, 'The phrase \'popup\' was used in ' . $path . '. This should be changed to \'pop-up\'.');
        }
        if ((preg_match('#[^e]built in[^t]#', $string) != 0) && (strpos($string, 'in a ') === false)) {
            $ob->assertTrue(false, 'The phrase \'built in\' was used in ' . $path . '. This should be changed to \'built-in\'.');
        }
        if (stripos($string, 'PHP info') !== false) {
            $ob->assertTrue(false, 'The word \'PHP info\' was used in ' . $path . '. This should be changed to \'PHP-Info\'.');
        }

        // Space wanted (we want our canonical way)
        if (stripos($string, 'de-facto') !== false) {
            $ob->assertTrue(false, 'The word \'de-facto\' was used in ' . $path . '. This should be changed to \'de facto\'.');
        }
        if (stripos($string, 'defacto') !== false) {
            $ob->assertTrue(false, 'The word \'defacto\' was used in ' . $path . '. This should be changed to \'de facto\'.');
        }
        if (stripos($string, 'safe-mode') !== false) {
            $ob->assertTrue(false, 'The word \'safe-mode\' was used in ' . $path . '. This should be changed to \'safe mode\'.');
        }
        if (stripos($string, 'base-URL') !== false) {
            $ob->assertTrue(false, 'The word \'base-URL\' was used in ' . $path . '. This should be changed to \'base URL\'.');
        }
        if (($key === null) || (!in_array($key, ['WARNING_DB_OVERWRITE']))) {
            if (stripos($string, 'upper-case') !== false) {
                $ob->assertTrue(false, 'The word \'upper-case\' was used in ' . $path . '. This should be changed to \'upper case\'.');
            }
            if (preg_match('#uppercase[^\(]#i', $string) != 0) {
                $ob->assertTrue(false, 'The word \'uppercase\' was used in ' . $path . '. This should be changed to \'upper case\'.');
            }
            if (stripos($string, 'lower-case') !== false) {
                $ob->assertTrue(false, 'The word \'lower-case\' was used in ' . $path . '. This should be changed to \'lower case\'.');
            }
            if (preg_match('#lowercase[^\(]#i', $string) != 0) {
                $ob->assertTrue(false, 'The word \'lowercase\' was used in ' . $path . '. This should be changed to \'lower case\'.');
            }
        }

        // Our preferred progressive language
        if (stripos($string, 'he/she') !== false) {
            $ob->assertTrue(false, 'The phrase \'he/she\' was used in ' . $path . '. This should be changed to \'they\' (and the surrounding grammar may need tweaking).');
        }
        if (stripos($string, 'his/her') !== false) {
            $ob->assertTrue(false, 'The phrase \'his/her\' was used in ' . $path . '. This should be changed to \'their\' (and the surrounding grammar may need tweaking).');
        }
        if (stripos($string, 'him/her') !== false) {
            $ob->assertTrue(false, 'The phrase \'him/her\' was used in ' . $path . '. This should be changed to \'them\' (and the surrounding grammar may need tweaking).');
        }

        // No space or hyphen wanted (we want our canonical way)
        if (
            (stripos($string, 'set-up') !== false) &&
            (($key === null) || (!in_array($key, ['CONFIG_OPTION_taxcloud_api_key', 'CONFIG_OPTION_taxcloud_api_id'])))
        ) {
            $ob->assertTrue(false, 'The phrase \'set-up\' was used in ' . $path . '. This might need to be changed to \'setup\', depending on the usage.');
        }
        if (stripos($string, 'add-on') !== false) {
            $ob->assertTrue(false, 'The word \'add-on\' was used in ' . $path . '. This should be changed to \'addon\'.');
        }
        if (stripos($string, ' on-line') !== false) {
            $ob->assertTrue(false, 'The word \'on-line\' was used in ' . $path . '. This should be changed to \'online\'.');
        }
        if (stripos($string, 'e-commerce') !== false) {
            $ob->assertTrue(false, 'The phrase \'e-commerce\' was used in ' . $path . '. This should be changed to \'eCommerce\'.');
        }
        if (preg_match('#user[ -]group#', $string) != 0) {
            $ob->assertTrue(false, 'The term \'user-group\' was used in ' . $path . '. This should be changed to \'usergroup\'.');
        }
        if (preg_match('#chat[ -]room#i', $string) != 0) {
            $ob->assertTrue(false, 'The phrase \'chat room\' or \'chat-room\' was used in ' . $path . '. This should be changed to \'chatroom\'.');
        }
        if (preg_match('#user[ -]name[^d]#i', $string) != 0) {
            $ob->assertTrue(false, 'The term \'user name\' was used in ' . $path . '. This should be changed to \'username\'.');
        }
        if (preg_match('#web[ -]site#i', $string) != 0) {
            $ob->assertTrue(false, 'The phrase \'web site\' or \'web-site\' was used in ' . $path . '. This should be changed to \'website\'.');
        }
        if (preg_match('#web[ -]host#i', $string) != 0) {
            $ob->assertTrue(false, 'The phrase \'web host\' or \'web-host\' was used in ' . $path . '. This should be changed to \'webhost\'.');
        }
        if (preg_match('#meta[ -]data([^A-Za-z"]+)#i', $string) != 0) {
            $ob->assertTrue(false, 'The phrase \'meta data\' or \'meta-data\' was used in ' . $path . '. This should be changed to \'metadata\'.');
        }

        // Wrong way of writing proper noun (we want our canonical way)
        if (
            (stripos($string, 'unvalidated') !== false) &&
            (!in_array($file, ['tut_addon_index.txt', 'sup_set_up_a_workflow_in_composr.txt']))
        ) {
            $ob->assertTrue(false, 'The word \'unvalidated\' was used in ' . $path . '. This should be changed to \'non-validated\'.');
        }
        if (
            (preg_match('#([^\]/A-Za-z"_<\']+)comcode([^A-Za-z"\']+)#', $string) != 0) &&
            (!in_array($file, ['stress_test_loader.php', 'global.css', 'zones.ini', 'blocks.ini', 'main_multi_content.php']))
        ) {
            $ob->assertTrue(false, 'The term \'comcode\' was used in ' . $path . '. This should be changed to \'Comcode\'.');
        }
        if (strpos($string, 'wiki+') !== false) {
            $ob->assertTrue(false, 'The term \'wiki+\' was used in ' . $path . '. This should be changed to \'Wiki+\'.');
        }
        if (preg_match('#([^A-Za-z]+)(PHP-Doc|phpdoc|phpDoc)([^A-Za-z]+)#', $string) != 0) {
            $ob->assertTrue(false, 'A misspelling of \'PHPDoc\' occurred in ' . $path . '.');
        }
        if (preg_match('#([^A-Za-z]+)(PHP-Storm|phpStorm)([^A-Za-z]+)#', $string) != 0) {
            $ob->assertTrue(false, 'A misspelling of \'PhpStorm\' occurred in ' . $path . '.');
        }
        if (preg_match('#([^A-Za-z]+)CloudFlare([^A-Za-z]+)#', $string) != 0) {
            $ob->assertTrue(false, 'The word \'CloudFlare\' was used in ' . $path . '. This should be changed to \'Cloudflare\'.');
        }
        if (preg_match('#([^A-Za-z]+)Javascript([^A-Za-z]+)#', $string) != 0) {
            $ob->assertTrue(false, 'The word \'Javascript\' was used in ' . $path . '. This should be changed to \'JavaScript\'.');
        }
        if (preg_match('#([^A-Za-z]+)mySQL([^A-Za-z]+)#', $string) != 0) {
            $ob->assertTrue(false, 'The word \'mySQL\' was used in ' . $path . '. This should be changed to \'MySQL\'.');
        }
        if (preg_match('#([^/\.A-Za-z>]+)tar([^A-Za-z]+)#', $string) != 0) {
            $ob->assertTrue(false, 'The filetype \'tar\' was used in ' . $path . '. This should be changed to \'TAR\'.');
        }
        if (
            (preg_match('#([^/_\-\.A-Za-z]+)zip([^A-Za-z]+)#', $string) != 0) &&
            (($key === null) || (!in_array($key, ['ZIP_NEEDED_FOR_USA', 'INVALID_ZIP_FOR_USA', 'CONFIG_OPTION_business_post_code'])))
        ) {
            $ob->assertTrue(false, 'The filetype \'zip\' was used in ' . $path . '. This should be changed to \'ZIP\'.');
        }
        if (preg_match('#([^\]/A-Za-z"_<]+)internet#', $string) != 0) {
            $ob->assertTrue(false, 'The term \'internet\' was used in ' . $path . '. This should be changed to \'Internet\'.');
        }
        if (stripos($string, 'CommandrFS') !== false) {
            $ob->assertTrue(false, 'The phrase \'CommandrFS\' was used in ' . $path . '. This should be changed to \'Commandr-fs\'.');
        }
        if (strpos($string, 'Commandr-FS') !== false) {
            $ob->assertTrue(false, 'The phrase \'Commandr-FS\' was used in ' . $path . '. This should be changed to \'Commandr-fs\'.');
        }
        if (stripos($string, 'ResourceFS') !== false) {
            $ob->assertTrue(false, 'The phrase \'ResourceFS\' was used in ' . $path . '. This should be changed to \'Resource-fs\'.');
        }
        if (strpos($string, 'Resource-FS') !== false) {
            $ob->assertTrue(false, 'The phrase \'Resource-FS\' was used in ' . $path . '. This should be changed to \'Resource-fs\'.');
        }
        if (strpos($string, 'OpenGraph') !== false) {
            $ob->assertTrue(false, 'The phrase \'OpenGraph\' was used in ' . $path . '. This should be changed to \'Open Graph\'.');
        }
        if (strpos($string, 'ReCAPTCHA') !== false) {
            $ob->assertTrue(false, 'The phrase \'ReCAPTCHA\' was used in ' . $path . '. This should be changed to \'reCAPTCHA\'.');
        }
        if (strpos($string, 'RECAPTCHA') !== false) {
            $ob->assertTrue(false, 'The phrase \'RECAPTCHA\' was used in ' . $path . '. This should be changed to \'reCAPTCHA\'.');
        }
        if (stripos($string, 'MacOS') !== false) {
            $ob->assertTrue(false, 'The word \'MacOS\' was used in ' . $path . '. This should be changed to \'Mac OS\'.');
        }
        if (strpos($string, 'Youtube') !== false) {
            $ob->assertTrue(false, 'The word \'Youtube\' was used in ' . $path . '. This should be changed to \'YouTube\'.');
        }
        if (preg_match('#(^|[^\w])(ccbill|ccBill|CCbill)([^\w]|$)#', $string) != 0) {
            $ob->assertTrue(false, 'The word \'CCBill\' was misspelled in ' . $path . '.');
        }
        if (strpos($string, 'FlexForm') !== false) {
            $ob->assertTrue(false, 'The word \'FlexForm\' was used in ' . $path . '. This should be changed to \'Flexform\'.');
        }

        if (preg_match('#(^|[^\w])(cron|CRON)([^\w]|$)#', $string) != 0) {
            $ob->assertTrue(false, 'The word \'Cron\' was misspelled in ' . $path . '.');
        }
        // page-link, but we can't test for that because "page link" is a valid phrase too

        // Our canonical way of writing "Open Source"
        if (strpos($string, 'open source') !== false) {
            $ob->assertTrue(false, 'The phrase \'open source\' was used in ' . $path . '. This should be changed to \'Open Source\'.');
        }
        if (stripos($string, 'open-source') !== false) {
            $ob->assertTrue(false, 'The phrase \'open-source\' was used in ' . $path . '. This should be changed to \'Open Source\'.');
        }
        if (strpos($string, 'Open source') !== false) {
            $ob->assertTrue(false, 'The phrase \'Open source\' was used in ' . $path . '. This should be changed to \'Open Source\'.');
        }

        // Super-[moderator|admin]
        if ((stripos($string, 'super moderator') !== false) && (!in_array($file, ['vb3.php', 'tut_nuances.txt'/*MyBB-specific option*/]))) {
            $ob->assertTrue(false, 'The phrase \'super moderator\' was used in ' . $path . '. This should be changed to \'super-moderator\'.');
        }
        if (stripos($string, 'super admin') !== false) {
            $ob->assertTrue(false, 'The phrase \'super admin\' was used in ' . $path . '. This should be changed to \'super-admin\'.');
        }

        // Bad use of acronyms
        if (stripos($string, 'CMS system') !== false) {
            $ob->assertTrue(false, 'The phrase \'CMS system\' was used in ' . $path . '. That would expand to Content Management System System. The plural of CMS is CMSs.');
        }
        if (strpos($string, 'eg.') !== false) {
            $ob->assertTrue(false, 'The phrase \'eg.\' was used in ' . $path . '. It should be \'e.g.\'.');
        }

        // Bad way of writing PHP versions etc
        if (stripos($string, 'PHP5.') !== false || stripos($string, 'PHP7.') !== false) {
            $ob->assertTrue(false, 'PHP version written missing a space in ' . $path . '.');
        }
        if (stripos($string, 'end of life') !== false) {
            $ob->assertTrue(false, 'Use dashes for end-of-life, ' . $path . '.');
        }

        // Weird British English
        if (stripos($string, 'amongst') !== false) {
            $ob->assertTrue(false, 'The word \'amongst\' was used in ' . $path . '. This should be changed to \'among\'.');
        }
        if (stripos($string, 'whilst') !== false) {
            $ob->assertTrue(false, 'The word \'whilst\' was used in ' . $path . '. This should be changed to \'while\'.');
        }
        if (
            (preg_match('#[^s]tick [^\(]#', $string) != 0) &&
            (!in_array($file, ['cns_install.php']))
        ) {
            $ob->assertTrue(false, 'The word tick was used in ' . $path . ' without being followed by check in parentheses in the conventional way.');
        }

        $common_spelling_mistakes = [
            // Common spelling errors
            'cacheing' => 'caching',
            'publically' => 'publicly',
            'seperate' => 'separate',
            'adhoc' => 'ad hoc',
            'yness' => 'iness',
            'overidden' => 'overridden',
            'overide' => 'override',
            'nieve' => 'naive',
            'in-situe' => 'in-situ',
            'infact' => 'in fact',
            'in-fact' => 'in fact',
            'conveniant' => 'convenient',
            'conveniance' => 'convenience',
            'routeable' => 'routable',
            'supercede' => 'supersede',
            'targetted' => 'targeted',
            'targetting' => 'targeting',
            'fulfills' => 'fulfils',
            'progmatically' => 'programatically',
            'persistant' => 'persistent',
            'recieve' => 'receive',
            'eratic' => 'erratic',
            'psuedo' => 'pseudo',
            'licencing' => 'licensing',
            'overidable' => 'overridable',

            // Common proper noun miswriting
            'Permission Tree Editor' => 'Permissions Tree Editor',

            // Common inconsistencies
            'stack dump' => 'stack trace',
        ];
        if (strpos($file, 'calendar') !== false) {
            $common_spelling_mistakes['occurrence'] = 'recurrence';
        }
        foreach ($common_spelling_mistakes as $from => $to) {
            if (stripos($string, $from) !== false) {
                $ob->assertTrue(false, $from . ' should be ' . $to . ' in ' . $path . '.');
            }
        }

        // Common grammar errors
        if (
            (stripos($string, 'URL\'s') !== false) &&
            (!in_array($file, ['tut_importer.txt', 'tut_tempcode.txt']))
        ) {
            $ob->assertTrue(false, 'The acronym \'URL\'s\' was used in ' . $path . '. This should be changed to \'URLs\'.');
        }
        if (stripos($string, 'ID\'s') !== false) {
            $ob->assertTrue(false, 'The term \'ID\'s\' was used in ' . $path . '. This should be changed to \'IDs\'.');
        }
        if (preg_match('#([^A-Za-z]>+)id([^A-Za-z=<]+)#', $string) != 0) {
            $ob->assertTrue(false, 'The acronym \'id\' was used in ' . $path . '. This should be changed to \'ID\'.');
        }
        if (
            (preg_match('#([^\$:_A-Za-z\[\]></\']+)url([^\}\(A-Za-z=\']+)#', $string, $matches) != 0) &&
            (!in_array($file, ['attachments3.php', 'cns_install.php', 'tut_fields.txt', 'maintenance_status.csv', 'toastify.js']))
        ) {
            $prefix = $matches[1];
            if ($prefix != '="') {
                $ob->assertTrue(false, 'The acronym \'url\' was used in ' . $path . '. (prefix is ' . $prefix . ') This should be changed to \'URL\'.');
            }
        }
        if (preg_match('#([^\]/A-Za-z"_<]+)thankyou#i', $string) != 0) {
            $ob->assertTrue(false, 'The word \'thankyou\' was used in ' . $path . '. This should be changed to \'thank you\'.');
        }


        // Extra checks that give lots of false-positives
        if ($verbose) {
            foreach (['user' => 'member', 'color' => 'colour', 'license' => 'licence', 'center' => 'centre'] as $from => $to) {
                if (stripos($string, $from) !== false) {
                    $ob->assertTrue(false, 'The term \'' . $from . '\' was used in ' . $path . '. This might need to be changed to \'' . $to . '\', depending on the circumstances.');
                }
            }

            if (preg_match('#([A-Za-z]+)s( |-)#', $string) != 0) {
                $ob->assertTrue(false, 'A word in ' . $path . ' ended with an \'s\', but did not contain an apostrophe. This might be a case of mistaken plurality.');
            }

            if (preg_match('#([A-Za-z]+)\'s#', $string) != 0) {
                $ob->assertTrue(false, 'A word in ' . $path . ' has an apostrophe, confirm it is intended..');
            }

            if (preg_match('#([a-z]+)( |-)([A-Z]{1})([a-z]+)#', $string) != 0) {
                $ob->assertTrue(false, 'A word in ' . $path . ' that was in the middle of the string started with a capital letter. This might be a badly capitalised string.');
            }
        }
    }
}
