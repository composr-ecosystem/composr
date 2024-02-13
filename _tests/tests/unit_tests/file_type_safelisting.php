<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/*EXTRA FUNCTIONS: shell_exec*/

/**
 * Composr test case class (unit testing).
 */
class file_type_safelisting_test_set extends cms_test_case
{
    protected $file_types = [];

    public function setUp()
    {
        parent::setUp();

        $path = get_file_base() . '/sources/mime_types.php';
        $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

        disable_php_memory_limit();

        $this->file_types = [];
        $matches = [];
        $num_matches = preg_match_all('#\'(\w{1,10})\'#', $c, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $this->file_types[] = $matches[1][$i];
        }
    }

    public function testIISMimeTypeConsistency()
    {
        require_code('mime_types');

        $cms_mime_types = get_mime_types(true);

        $url = 'https://raw.githubusercontent.com/microsoft/computerscience/f44092740662393051af0ed1c2fa3b2443660b79/Labs/Azure%20Services/Azure%20Storage/Solutions/Intellipix/.vs/config/applicationhost.config';
        $c = http_get_contents($url, ['convert_to_internal_encoding' => true, 'timeout' => 20.0]);

        $found_bin = false;

        $matches = [];
        $num_matches = preg_match_all('#<mimeMap fileExtension="\.([^"]*)" mimeType="([^"]*)" />#', $c, $matches);
        $exts = [];
        for ($i = 0; $i < $num_matches; $i++) {
            $ext = $matches[1][$i];
            $mime_type = $matches[2][$i];

            if ($ext == 'bin') { // Needed for security
                $this->assertTrue($mime_type == 'application/octet-stream');
                $found_bin = true;
            }

            // Things 'incorrect' in IIS
            if (in_array(serialize([$ext, $mime_type]), [
                serialize(['aifc', 'audio/aiff']),
                serialize(['aiff', 'audio/aiff']),
                serialize(['gz', 'application/x-gzip']),
                serialize(['mid', 'audio/mid']),
                serialize(['odc', 'text/x-ms-odc']),
                serialize(['ods', 'application/oleobject']),
                serialize(['ogg', 'video/ogg']),
                serialize(['tgz', 'application/x-compressed']),
                serialize(['woff', 'font/x-woff']),
                serialize(['woff2', 'application/font-woff2']),
                serialize(['zip', 'application/x-zip-compressed']),
                serialize(['csv', 'application/octet-stream']),
                serialize(['cur', 'application/octet-stream']),
                serialize(['psd', 'application/octet-stream']),
                serialize(['rar', 'application/octet-stream']),
                serialize(['exe', 'application/octet-stream']),
                serialize(['ttf', 'application/octet-stream']),
                serialize(['js', 'application/javascript']),
            ])) {
                continue;
            }

            $cms_mime_type = array_key_exists($ext, $cms_mime_types) ? $cms_mime_types[$ext] : null;
            $this->assertTrue(($cms_mime_type === $mime_type) || ($cms_mime_type === null), 'Inconsistency between IIS mime types and Composr: ' . $ext . ': ' . @strval($cms_mime_type) . ' vs ' . $mime_type);
        }

        $this->assertTrue($found_bin);
    }

    public function testApacheMimeTypeConsistency()
    {
        require_code('mime_types');

        $cms_mime_types = get_mime_types(true);

        $url = 'https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types';
        $c = http_get_contents($url, ['timeout' => 20.0]);

        $found_bin = false;

        $matches = [];
        $num_matches = preg_match_all('#^\#?\s*([^\s]*' . '/[^\s]*)\t+([^\t]+)$#m', $c, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $exts = $matches[2][$i];
            $mime_type = $matches[1][$i];

            foreach (explode(' ', $exts) as $ext) {
                if ($ext == 'bin') { // Needed for security
                    $this->assertTrue($mime_type == 'application/octet-stream');
                    $found_bin = true;
                }

                // Things 'incorrect' in Apache
                if (in_array(serialize([$ext, $mime_type]), [
                    serialize(['aac', 'audio/x-aac']),
                    serialize(['xml', 'application/xml']),
                    serialize(['xsl', 'application/xml']),
                    serialize(['mp2', 'audio/mpeg']),
                    serialize(['wav', 'audio/x-wav']),
                    serialize(['tpl', 'application/vnd.groove-tool-template']),
                    serialize(['f4v', 'video/x-f4v']),
                    serialize(['m4v', 'video/x-m4v']),
                    serialize(['avi', 'video/x-msvideo']),
                ])) {
                    continue;
                }

                $cms_mime_type = array_key_exists($ext, $cms_mime_types) ? $cms_mime_types[$ext] : null;
                $this->assertTrue(($cms_mime_type === $mime_type) || ($cms_mime_type === null), 'Inconsistency between Apache mime types and Composr: ' . $ext . ': ' . @strval($cms_mime_type) . ' vs ' . $mime_type);
            }
        }

        $this->assertTrue($found_bin);
    }

    public function testCodeTypes()
    {
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $known_file_types = array_flip($this->file_types);

        require_code('files2');
        $php_files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_NONBUNDLED | IGNORE_UNSHIPPED_VOLATILE | IGNORE_SHIPPED_VOLATILE | IGNORE_REBUILDABLE_OR_TEMP_FILES_FOR_BACKUP, true, true, ['php']);
        foreach ($php_files as $path) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK);
            $matches = [];
            $num_matches = preg_match_all('#\w\.(\w{3})\'#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $ext = $matches[1][$i];

                // Exceptions
                if (in_array($ext, [
                    'swf',
                    'tmp',
                    'crt',

                    // Composr types
                    'inf',
                    'gcd',
                    'lcd',
                    'tcp',
                    'cms',

                    // Common false positives from URLs
                    'com',
                    'net',
                    'org',

                    // LEGACY
                    'dat',
                ])) {
                    continue;
                }
                if (in_array($path, [
                    'sources/webstandards.php',
                ])) {
                    continue;
                }
                if (in_array($path . '/' . $ext, [
                    'sources/hooks/modules/admin_import/phpbb3.php/url',
                    'sources/aggregate_types.php/key',
                    'sources/backup.php/pre',
                    'sources/database_relations.php/cat',
                    'sources/files.php/old',
                    'sources/files.php/pem',
                    'sources/files.php/pid',
                    'sources/files.php/pwl',
                    'sources/files.php/xxx',
                    'sources/global.php/jit',
                    'sources/hooks/modules/admin_import/mybb.php/gid',
                    'sources/hooks/modules/admin_import/mybb.php/pid',
                    'sources/hooks/modules/admin_import/mybb.php/uid',
                    'sources/hooks/systems/addon_registry/backup.php/pre',
                    'sources/hooks/systems/addon_registry/core.php/xap',
                    'sources/hooks/systems/payment_gateway/authorize.php/api',
                    'sources/hooks/systems/payment_gateway/authorize.php/dll',
                    'sources/mail_dkim.php/tld',
                    'sources/themes3.php/dtd',
                    'sources/version2.php/inc',
                ])) {
                    continue;
                }
                if (preg_match('#^(\d+em|\d+)$#', $ext) != 0) {
                    continue;
                }
                if ($ext == 'dat') { // LEGACY
                    continue;
                }

                $this->assertTrue(isset($known_file_types[$ext]), 'Unrecognised file type, ' . $path . '/' . $ext);
            }
        }
    }

    public function testTrackerValidTypes()
    {
        $path = get_file_base() . '/tracker/config/config_inc.php';
        $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

        $file_types = [];
        $matches = [];
        preg_match('#\$g_allowed_files = \'(.*)\';#', $c, $matches);
        $file_types = explode(',', $matches[1]);
        sort($file_types);

        $file_types_expected = $this->file_types;
        $file_types_expected = array_diff($file_types_expected, ['bin', 'exe', 'dmg', 'htm', 'html', 'svg', 'css', 'js', 'json', 'woff', 'woff2', 'xml', 'xsd', 'xsl', 'rss', 'atom', 'yaml', 'yml']); // No executable or web formats should be uploaded by non-admins
        sort($file_types_expected);

        $this->assertTrue($file_types == $file_types_expected, 'Difference of: ' . serialize(array_diff($file_types_expected, $file_types)) . '/' . serialize(array_diff($file_types, $file_types_expected)));
    }

    public function testConfigValidTypes()
    {
        $path = get_file_base() . '/sources/hooks/systems/config/valid_types.php';
        $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

        $file_types = [];
        $matches = [];
        preg_match('#return \'([^\']+)\';#', $c, $matches);
        $file_types = explode(',', $matches[1]);
        sort($file_types);

        $file_types_expected = $this->file_types;
        $file_types_expected = array_diff($file_types_expected, ['bin', 'exe', 'dmg']); // No executables as users may try and get people to run on own machine (separately internally we filter web formats)
        sort($file_types_expected);

        $this->assertTrue($file_types == $file_types_expected, 'Difference of: ' . serialize(array_diff($file_types_expected, $file_types)) . '/' . serialize(array_diff($file_types, $file_types_expected)));
    }

    public function testAppYaml()
    {
        $path = get_file_base() . '/app.yaml';
        $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

        // --

        $file_types = [];
        $matches = [];
        preg_match('#- url: \/\(\.\*\\\.\((.*)\)\)#m', $c, $matches);
        $file_types = explode('|', $matches[1]);
        $file_types = array_diff($file_types, ['swf']); // We don't do mime-typing but do allow download
        sort($file_types);

        $file_types_expected = $this->file_types;
        $file_types_expected = array_diff($file_types_expected, ['php', 'htm']); // No files which may be web-processed/web-generated
        sort($file_types_expected);

        $this->assertTrue($file_types == $file_types_expected, 'Difference of: ' . serialize(array_diff($file_types_expected, $file_types)) . '/' . serialize(array_diff($file_types, $file_types_expected)));

        // --

        $file_types = [];
        $matches = [];
        preg_match('#  upload: \.\*\\\.\((.*)\)#m', $c, $matches);
        $file_types = explode('|', $matches[1]);
        $file_types = array_diff($file_types, ['swf']); // We don't do mime-typing but do allow download
        sort($file_types);

        $file_types_expected = $this->file_types;
        $file_types_expected = array_diff($file_types_expected, ['bin', 'php', 'htm']); // No files which may be web-processed/web-generated
        sort($file_types_expected);

        $this->assertTrue($file_types == $file_types_expected, 'Difference of: ' . serialize(array_diff($file_types_expected, $file_types)) . '/' . serialize(array_diff($file_types, $file_types_expected)));
    }

    public function testCodebookRef()
    {
        $path = get_file_base() . '/docs/pages/comcode_custom/EN/codebook_3.txt';
        $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

        $file_types = [];
        $matches = [];
        preg_match('#\.\*\\\\\.\((.*)\)\\\\\?\?\'\);\[\/tt\]#', $c, $matches);
        $file_types = explode('|', $matches[1]);
        $file_types = array_diff($file_types, ['swf']); // We don't do mime-typing but do allow download
        sort($file_types);

        $file_types_expected = $this->file_types;
        $file_types_expected = array_diff($file_types_expected, ['php', 'htm']); // No files which may be web-processed/web-generated
        sort($file_types_expected);

        $this->assertTrue($file_types == $file_types_expected, 'Difference of: ' . serialize(array_diff($file_types_expected, $file_types)) . '/' . serialize(array_diff($file_types, $file_types_expected)));
    }

    public function testHtaccessRealFileList()
    {
        $path = get_file_base() . '/recommended.htaccess';
        $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

        $file_types = [];
        $matches = [];
        preg_match('#RewriteCond \$1 \\\\\.\((.*)\)\(\$\|\\\\\?\) \[OR\]#', $c, $matches);
        $file_types = explode('|', $matches[1]);
        $file_types = array_diff($file_types, ['swf']); // We don't do mime-typing but do allow download
        sort($file_types);

        $file_types_expected = $this->file_types;
        $file_types_expected = array_diff($file_types_expected, ['htm']); // No .htm files which may be web-generated
        sort($file_types_expected);

        $this->assertTrue($file_types == $file_types_expected, 'recommended.htaccess difference of: ' . serialize(array_diff($file_types_expected, $file_types)) . '/' . serialize(array_diff($file_types, $file_types_expected)));
    }

    public function testDirectoryProtectRealFileList()
    {
        $hook = get_hook_ob('systems', 'addon_registry', 'directory_protect', 'Hook_addon_registry_', true);

        if (($hook === null) || !addon_installed('directory_protect')) {
            $this->assertTrue(false, 'Directory protect addon required for this test.');
            return;
        }

        $description = $hook->get_description();

        $file_types_expected = $this->file_types;
        $file_types_expected = array_diff($file_types_expected, ['htm']); // No .htm files which may be web-generated
        sort($file_types_expected);

        $file_types = [];
        $matches = [];
        preg_match('#RewriteCond \$1 \\\\\.\((.*)\)\(\$\|\\\\\?\) \[OR\]#', $description, $matches);
        if (!array_key_exists(1, $matches)) {
            $this->assertTrue(false, 'Directory protect addon description has an invalid code example.');
        } else {
            $file_types = explode('|', $matches[1]);
            $file_types = array_diff($file_types, ['swf']); // We don't do mime-typing but do allow download
            sort($file_types);

            $this->assertTrue($file_types == $file_types_expected, 'Directory protect addon description difference of: ' . serialize(array_diff($file_types_expected, $file_types)) . '/' . serialize(array_diff($file_types, $file_types_expected)));
        }
    }

    public function testOtherValidTypes()
    {
        require_code('images');

        foreach (['valid_images', 'valid_videos', 'valid_audios'] as $f) {
            $path = get_file_base() . '/sources/hooks/systems/config/' . $f . '.php';
            $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

            $file_types = [];
            $matches = [];
            if (preg_match('#return \'([^\']+)\';#', $c, $matches) == 0) {
                preg_match('#\$ret = \'([^\']+)\';#', $c, $matches);
            }
            $file_types = explode(',', $matches[1]);

            foreach ($file_types as $file_type) {
                $this->assertTrue(in_array($file_type, $this->file_types));

                if ($f == 'valid_images') {
                    $this->assertTrue(is_image('example.' . $file_type, IMAGE_CRITERIA_WEBSAFE, true), $file_type . ' not websafe?');
                }
            }

            $exts = explode(',', get_option($f));
            $this->assertTrue(count($exts) == count(array_unique($exts)));
        }
    }

    public function testNoInvalidImageTypesPopulating()
    {
        require_code('images');
        $images = $GLOBALS['SITE_DB']->query_select('images', ['url']);
        foreach ($images as $image) {
            $this->assertTrue(is_image($image['url'], IMAGE_CRITERIA_WEBSAFE));
        }
    }

    public function test404Excludes()
    {
        $recommended_htaccess = file_get_contents(get_file_base() . '/recommended.htaccess');
        $htaccess_404_file_exts = [];
        $matches = [];
        if (preg_match('#<FilesMatch "\(\?<!(.*jpg.*)\)\$">#', $recommended_htaccess, $matches) == 0) {
            $this->assertTrue(false, 'Could not find 404 page exception list in recommended.htaccess');
        } else {
            foreach (explode('|', $matches[1]) as $ext) {
                $htaccess_404_file_exts[] = trim($ext, '\.');
            }
        }

        $valid_images = array_map('trim', explode(',', get_option('valid_images')));
        // Add some more just so we get expected parity
        $valid_images[] = 'js';
        $valid_images[] = 'css';
        $valid_images[] = 'html';

        foreach ($valid_images as $file_type) {
            $this->assertTrue(in_array($file_type, $htaccess_404_file_exts), 'File type ' . $file_type . ' is not included in 404 rule in recommended.htaccess');
        }

        foreach ($htaccess_404_file_exts as $file_type) {
            $this->assertTrue(in_array($file_type, $valid_images), 'File type ' . $file_type . ' is not excluded from 404 rule in recommended.htaccess');
        }
    }

    public function testGitAttributes()
    {
        $path = get_file_base() . '/.gitattributes';
        $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

        foreach ($this->file_types as $file_type) {
            $this->assertTrue(strpos($c, '*.' . $file_type . ' ') !== false, 'File type missing from .gitattributes, ' . $file_type);
        }

        $matches = [];
        $num_matches = preg_match_all('#^\*\.(\w+) (text|binary)#m', $c, $matches);
        $found = [];
        for ($i = 0; $i < $num_matches; $i++) {
            $ext = $matches[1][$i];
            $this->assertTrue(!array_key_exists($ext, $found), 'Double referenced ' . $ext);
            $found[$ext] = true;
        }

        // Test Git is conclusive
        $lines = explode("\n", shell_exec('git ls-files'));
        $exts = [];
        foreach ($lines as $line) {
            $filename = basename($line);
            $ext = get_file_extension($filename);
            if (($ext != '') && ('.' . $ext != $filename)) {
                $exts[$ext] = true;
            }
        }
        ksort($exts);
        foreach (array_keys($exts) as $ext) {
            $this->assertTrue(array_key_exists($ext, $found), 'Unknown file type in Git that should be in .gitattributes to clarify how to manage it: ' . $ext);
        }
    }

    public function testCSS()
    {
        $path = get_file_base() . '/themes/default/css/global.css';
        $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

        /*
        Not all will be here
        foreach ($this->file_types as $file_type) {
            $this->assertTrue(strpos($c, '[href$=".' . $file_type . '"]') !== false, 'File type missing from global.css, ' . $file_type);
        }
        */

        $matches = [];
        $num_matches = preg_match_all('#^\[href\$="\.(\w+)"\]#', $c, $matches);
        $found = [];
        for ($i = 0; $i < $num_matches; $i++) {
            $ext = $matches[1][$i];
            $this->assertTrue(in_array($ext, $this->file_types), 'Rogue file type ' . $ext);
            $found[$ext] = true;
        }
    }
}
