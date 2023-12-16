<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

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
        require_code('lang_compile');

        disable_php_memory_limit();

        parent::setUp();
    }

    public function testLangMistakes()
    {
        $verbose = false;

        $dh = opendir(get_file_base() . '/lang/EN/');
        while (($file = readdir($dh)) !== false) {
            if (substr($file, -4) != '.ini') {
                continue;
            }
            if ($file[0] == '.') {
                continue;
            }

            $input = array();
            _get_lang_file_map(get_file_base() . '/lang/EN/' . $file, $input, 'strings', false, false, 'EN');

            foreach ($input as $key => $string) {
                $this->check($file, $key, $string, $verbose);
            }
        }
        closedir($dh);

        $dirs = array(
            'text',
            'text/EN',
            'pages/comcode/EN',
            'site/pages/comcode/EN',
            'docs/pages/comcode_custom/EN',
        );
        if ((function_exists('git_repos')) && (git_repos() == 'composr_homesite')) {
            $dirs = array_merge($dirs, array(
                'text_custom',
                'text_custom/EN',
                'pages/comcode_custom/EN',
                'site/pages/comcode_custom/EN',
            ));
        }
        foreach ($dirs as $dir) {
            $path = get_file_base() . '/' . $dir;
            $dh = opendir($path);
            while (($file = readdir($dh)) !== false) {
                if (substr($file, -4) != '.txt') {
                    continue;
                }
                if ($file[0] == '.') {
                    continue;
                }
                if ($file == 'synonyms.txt') {
                    continue;
                }
                if ($file == 'sitemap.txt') {
                    continue;
                }

                $this->check($file, null, file_get_contents($path . '/' . $file), $verbose);
            }
            closedir($dh);
        }

        $files = array(
            'uploads/website_specific/compo.sr/errorservice.csv',
        );
        foreach ($files as $file) {
            $path = get_file_base() . '/';
            $this->check($file, null, file_get_contents($path . '/' . $file), $verbose);
        }
    }

    private function check($file, $key, $string, $verbose)
    {
        $string = preg_replace('#https?://[^ ]*#', '', $string);
        $string = preg_replace('#\{\$[^\{\}]*\}#', '', $string);
        $string = preg_replace('#\[code.*\[/code\]#s', '', $string);
        $string = preg_replace('#\[codebox.*\[/codebox\]#s', '', $string);
        $string = preg_replace('#\[tt.*\[/tt\]#s', '', $string);

        $matches = array();

        // Support debranding
        if (!is_null($key)) {
            if (strpos($string, 'Composr') !== false && $key != 'NO_PHP_IN_TEMPLATES' && $key != 'WHAT_TO_EXPECT' && $key != 'DESCRIPTION_INCLUDE_CMS_ADVERT' && $key != 'INCLUDE_CMS_ADVERT' && $key != 'UNINSTALL_WARNING' && $key != 'SOFTWARE_CHAT_EXTRA' && $key != 'CONFIG_CATEGORY_COMPOSR_APIS' && $key != 'CONFIG_CATEGORY_DESCRIPTION__COMPOSR_APIS' && (strpos($key, 'SETUPWIZARD') === false) && $key != 'CANNOT_CONNECT_HOME' && $key != 'NO_GD_ON_SERVER' && $key != 'MYSQL_TOO_OLD' && $key != 'LOW_MEMORY_LIMIT' && $key != 'NO_ZIP_ON_SERVER' && $key != 'TRANSCODING_SERVER' && $key != 'CONFIG_OPTION_transcoding_server' && $key != 'CONFIG_OPTION_network_links' && $key != 'WARNING_MBSTRING_FUNC_OVERLOAD' && $key != 'DISABLED_FUNCTION' && $file != 'lang.ini' && $file != 'version.ini' && $file != 'debrand.ini' && $file != 'import.ini' && $file != 'installer.ini' && $file != 'upgrade.ini' && $file != 'commandr.ini' && $file != 'addons.ini') {
                $this->assertTrue(false, 'The word \'Composr\' was used in ' . $file . ' (' . $key . '). This should probably be changed to \'the software\'.');
            }
        }

        // Bad use of it's. Imperfect test, but would rather have it anyway due to the prevalence of these mistakes. People can expand contractions to workaround.
        if (preg_match('#it\'s (own|permission|id |definition|filename|contents|run|database|parent|child|cach|interface|use |various|result|properties)#', $string) != 0) {
            $this->assertTrue(false, 'The phrase "it\'s own" was used in ' . $file . '. This should be changed to "its own". There could be more infractions that we could not auto-detect.');
        }

        // Hyphen wanted (we want our canonical way)
        if ((preg_match('#([^\[\]\|"\'/\_])email#', $string, $matches) != 0) && ((is_null($key)) || (stripos($string, '/') === false) && (stripos($string, 'codename') === false)) && (stripos($string, 'Automatic code inserts after this') === false) && (basename($file) != 'tut_facebook.txt')) {
            $prefix = $matches[1];
            $this->assertTrue(false, 'The term \'email\' was used in ' . $file . '. (prefix is ' . $prefix . ') This should be changed to \'e-mail\'.');
        }
        if (stripos($string, 'comma separated') !== false) {
            $this->assertTrue(false, 'The phrase \'comma separated\' was used in ' . $file . '. This should be changed to \'comma-separated\'.');
        }
        if (preg_match('#([^\[\]<>\|"\'/\_])popup#', $string, $matches) != 0) {
            $this->assertTrue(false, 'The phrase \'popup\' was used in ' . $file . '. This should be changed to \'pop-up\'.');
        }
        if ((preg_match('#[^e]built in[^t]#', $string) != 0) && (strpos($string, 'in a ') === false)) {
            $this->assertTrue(false, 'The phrase \'built in\' was used in ' . $file . '. This should be changed to \'built-in\'.');
        }

        // No hyphen wanted (we want our canonical way)
        if (stripos($string, 'set-up') !== false) {
            $this->assertTrue(false, 'The phrase \'set-up\' was used in ' . $file . '. This might need to be changed to \'setup\', depending on the usage.');
        }
        if (stripos($string, 'point-store') !== false) {
            $this->assertTrue(false, 'The word \'point-store\' was used in ' . $file . '. This should be changed to \'Point Store\'.');
        }
        if (stripos($string, 'add-on') !== false) {
            $this->assertTrue(false, 'The word \'add-on\' was used in ' . $file . '. This should be changed to \'addon\'.');
        }
        if (stripos($string, ' on-line') !== false) {
            $this->assertTrue(false, 'The word \'on-line\' was used in ' . $file . '. This should be changed to \'online\'.');
        }
        if (stripos($string, 'off-line') !== false) {
            $this->assertTrue(false, 'The word \'off-line\' was used in ' . $file . '. This should be changed to \'offline\'.');
        }

        // No space or hyphen wanted (we want our canonical way)
        if (stripos($string, 'user[ -]group') !== false) {
            $this->assertTrue(false, 'The term \'user-group\' was used in ' . $file . '. This should be changed to \'usergroup\'.');
        }
        if (preg_match('#chat[ -]room#i', $string) != 0) {
            $this->assertTrue(false, 'The phrase \'chat room\' or \'chat-room\' was used in ' . $file . '. This should be changed to \'chatroom\'.');
        }
        if (preg_match('#user[ -]name[^d]#i', $string) != 0) {
            $this->assertTrue(false, 'The term \'user name\' was used in ' . $file . '. This should be changed to \'username\'.');
        }
        if (preg_match('#web[ -]site#i', $string) != 0) {
            $this->assertTrue(false, 'The phrase \'web site\' or \'web-site\' was used in ' . $file . '. This should be changed to \'website\'.');
        }
        if (preg_match('#web[ -]host#i', $string) != 0) {
            $this->assertTrue(false, 'The phrase \'web host\' or \'web-host\' was used in ' . $file . '. This should be changed to \'webhost\'.');
        }
        if (preg_match('#meta[ -]data([^A-Za-z"]+)#i', $string) != 0) {
            $this->assertTrue(false, 'The phrase \'meta data\' or \'meta-data\' was used in ' . $file . '. This should be changed to \'metadata\'.');
        }
        if (stripos($string, 'PHP info') !== false) {
            $this->assertTrue(false, 'The word \'PHP info\' was used in ' . $file . '. This should be changed to \'PHP-info\'.');
        }

        // Wrong way of writing proper noun (we want our canonical way)
        if ((stripos($string, 'unvalidated') !== false) && ($file !== 'tut_addon_index.txt') && ($file !== 'sup_set_up_a_workflow_in_composr.txt')) {
            $this->assertTrue(false, 'The word \'unvalidated\' was used in ' . $file . '. This should be changed to \'non-validated\'.');
        }
        if (preg_match('#([^\]/A-Za-z"\_<]+)comcode([^A-Za-z"]+)#', $string) != 0) {
            $this->assertTrue(false, 'The term \'comcode\' was used in ' . $file . '. This should be changed to \'Comcode\'.');
        }
        if (strpos($string, 'wiki+') !== false) {
            $this->assertTrue(false, 'The term \'wiki+\' was used in ' . $file . '. This should be changed to \'Wiki+\'.');
        }
        if (!is_null($key)) {
            if (stripos($string, 'supermember') !== false) {
                $this->assertTrue(false, 'The term \'supermember\' was used in ' . $file . '. This should be changed to \'super-member\'.');
            }
        }
        if (preg_match('#([^A-Za-z]+)Javascript([^A-Za-z]+)#', $string) != 0) {
            $this->assertTrue(false, 'The word \'Javascript\' was used in ' . $file . '. This should be changed to \'JavaScript\'.');
        }
        if (preg_match('#([^/\.A-Za-z>]+)tar([^A-Za-z]+)#', $string) != 0) {
            $this->assertTrue(false, 'The filetype \'tar\' was used in ' . $file . '. This should be changed to \'TAR\'.');
        }
        if (preg_match('#([^/\_\-\.A-Za-z]+)zip([^A-Za-z]+)#', $string) != 0) {
            $this->assertTrue(false, 'The filetype \'zip\' was used in ' . $file . '. This should be changed to \'ZIP\'.');
        }
        if (preg_match('#([^\]/A-Za-z"\_<]+)internet#', $string) != 0) {
            $this->assertTrue(false, 'The term \'internet\' was used in ' . $file . '. This should be changed to \'Internet\'.');
        }
        if (stripos($string, 'e-commerce') !== false) {
            $this->assertTrue(false, 'The phrase \'e-commerce\' was used in ' . $file . '. This should be changed to \'eCommerce\'.');
        }
        if (stripos($string, 'CommandrFS') !== false) {
            $this->assertTrue(false, 'The phrase \'CommandrFS\' was used in ' . $file . '. This should be changed to \'Commandr-fs\'.');
        }
        if (strpos($string, 'Commandr-FS') !== false) {
            $this->assertTrue(false, 'The phrase \'Commandr-FS\' was used in ' . $file . '. This should be changed to \'Commandr-fs\'.');
        }
        if (stripos($string, 'ResourceFS') !== false) {
            $this->assertTrue(false, 'The phrase \'ResourceFS\' was used in ' . $file . '. This should be changed to \'Resource-fs\'.');
        }
        if (strpos($string, 'Resource-FS') !== false) {
            $this->assertTrue(false, 'The phrase \'Resource-FS\' was used in ' . $file . '. This should be changed to \'Resource-fs\'.');
        }
        if (strpos($string, 'OpenGraph') !== false) {
            $this->assertTrue(false, 'The phrase \'OpenGraph\' was used in ' . $file . '. This should be changed to \'Open Graph\'.');
        }

        // Our canonical way of writing "Open Source"
        if (strpos($string, 'open source') !== false) {
            $this->assertTrue(false, 'The phrase \'open source\' was used in ' . $file . '. This should be changed to \'Open Source\'.');
        }
        if (stripos($string, 'open-source') !== false) {
            $this->assertTrue(false, 'The phrase \'open-source\' was used in ' . $file . '. This should be changed to \'Open Source\'.');
        }
        if (strpos($string, 'Open source') !== false) {
            $this->assertTrue(false, 'The phrase \'Open source\' was used in ' . $file . '. This should be changed to \'Open Source\'.');
        }

        // Bad use of acronyms
        if (stripos($string, 'CMS system') !== false) {
            $this->assertTrue(false, 'The phrase \'CMS system\' was used in ' . $file . '. That would expand to Content Management System System. The plural of CMS is CMSs.');
        }

        // Bad way of writing PHP versions etc
        if (stripos($string, 'PHP5.') !== false || stripos($string, 'PHP7.') !== false) {
            $this->assertTrue(false, 'PHP version written missing a space in ' . $file . '.');
        }
        if (stripos($string, 'end of life') !== false) {
            $this->assertTrue(false, 'Use dashes for end-of-life, ' . $file . '.');
        }

        // Old-fashioned words
        if (stripos($string, 'amongst') !== false) {
            $this->assertTrue(false, 'The word \'amongst\' was used in ' . $file . '. This should be changed to \'among\'.');
        }
        if (stripos($string, 'whilst') !== false) {
            $this->assertTrue(false, 'The word \'whilst\' was used in ' . $file . '. This should be changed to \'while\'.');
        }

        // Common spelling errors
        $common_spelling_mistakes = array(
            'correspondance' => 'correspondence',
            'existant' => 'existent',
            'interpretor' => 'interpreter',
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
            'desparate' => 'desperate',
            'delimitor' => 'delimiter',
            'conveniant' => 'convenient',
            'conveniance' => 'convenience',
            'routeable' => 'routable',
            'supercede' => 'supersede',
            'targetted' => 'targeted',
            'targetting' => 'targeting',
            'fulfills' => 'fulfils',
            'progmatically' => 'programatically',
            'persistant' => 'persistent',
            'recieve'=> 'receive',
            'eratic' => 'erratic',
            'psuedo' => 'pseudo',
            'licencing' => 'licensing',
            'perf' => 'performance', // "perf" is strictly a noun, but we use it as an adjective in error a lot
        );
        if (strpos($file, 'calendar') !== false) {
            $common_spelling_mistakes['occurrence'] = 'recurrence';
        }
        foreach ($common_spelling_mistakes as $from => $to) {
            if (stripos($string, $from) !== false) {
                $this->assertTrue(false, $from . ' should be ' . $to . ' in ' . $file . '.');
            }
        }

        // Common grammar errors
        if ((stripos($string, 'URL\'s') !== false) && ($file != 'tut_importer.txt')) {
            $this->assertTrue(false, 'The acronym \'URL\'s\' was used in ' . $file . '. This should be changed to \'URLs\'.');
        }
        if (stripos($string, 'ID\'s') !== false) {
            $this->assertTrue(false, 'The term \'ID\'s\' was used in ' . $file . '. This should be changed to \'IDs\'.');
        }
        if (preg_match('#([^A-Za-z]>+)id([^A-Za-z=<]+)#', $string) != 0) {
            $this->assertTrue(false, 'The acronym \'id\' was used in ' . $file . '. This should be changed to \'ID\'.');
        }
        if (preg_match('#([^\$:\_A-Za-z\[\]></\']+)url([^\}\(A-Za-z=\']+)#', $string, $matches) != 0) {
            $prefix = $matches[1];
            if ($prefix != '="') {
                $this->assertTrue(false, 'The acronym \'url\' was used in ' . $file . '. (prefix is ' . $prefix . ') This should be changed to \'URL\'.');
            }
        }
        if (preg_match('#([^\]/A-Za-z"\_<]+)thankyou#i', $string) != 0) {
            $this->assertTrue(false, 'The word \'thankyou\' was used in ' . $file . '. This should be changed to \'thank you\'.');
        }


        // Extra checks that give lots of false-positives
        if ($verbose) {
            if (stripos($string, 'user') !== false) {
                $this->assertTrue(false, 'The term \'user\' was used in ' . $file . '. This might need to be changed to \'member\', depending on the circumstances.');
            }

            if (preg_match('#([A-Za-z]+)s( |-)#', $string) != 0) {
                $this->assertTrue(false, 'A word in ' . $file . ' ended with an \'s\', but did not contain an apostrophe. This might be a case of mistaken plurality.');
            }

            if (preg_match('#([A-Za-z]+)\'s#', $string) != 0) {
                $this->assertTrue(false, 'A word in ' . $file . ' has an apostrophe, confirm it is intended..');
            }

            if (preg_match('#([a-z]+)( |-)([A-Z]{1})([a-z]+)#', $string) != 0) {
                $this->assertTrue(false, 'A word in ' . $file . ' that was in the middle of the string started with a capital letter. This might be a badly capitalised string.');
            }

            //if (stripos($string, 'center') !== false) $this->assertTrue(false, 'The word \'center\' was used in ' . $file . '. This should be changed to \'centre\'.');
        }
    }
}
