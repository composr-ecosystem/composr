<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/*EXTRA FUNCTIONS: shell_exec*/

/**
 * Composr test case class (unit testing).
 */
class file_type_whitelisting_test_set extends cms_test_case
{
    protected $file_types = array();

    public function setUp()
    {
        parent::setUp();

        $path = get_file_base() . '/sources/mime_types.php';
        $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

        $this->file_types = array();
        $matches = array();
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
        $c = http_get_contents($url, array('convert_to_internal_encoding' => true));

        $found_bin = false;

        $matches = array();
        $num_matches = preg_match_all('#<mimeMap fileExtension="\.([^"]*)" mimeType="([^"]*)" />#', $c, $matches);
        $exts = array();
        for ($i = 0; $i < $num_matches; $i++) {
            $ext = $matches[1][$i];
            $mime_type = $matches[2][$i];

            if ($ext == 'bin') { // Needed for security
                $this->assertTrue($mime_type == 'application/octet-stream');
                $found_bin = true;
            }

            // Things 'incorrect' in IIS
            if (in_array(serialize(array($ext, $mime_type)), array(
                serialize(array('aifc', 'audio/aiff')),
                serialize(array('aiff', 'audio/aiff')),
                serialize(array('gz', 'application/x-gzip')),
                serialize(array('mid', 'audio/mid')),
                serialize(array('odc', 'text/x-ms-odc')),
                serialize(array('ods', 'application/oleobject')),
                serialize(array('ogg', 'video/ogg')),
                serialize(array('tgz', 'application/x-compressed')),
                serialize(array('woff', 'font/x-woff')),
                serialize(array('woff2', 'application/font-woff2')),
                serialize(array('zip', 'application/x-zip-compressed')),
                serialize(array('csv', 'application/octet-stream')),
                serialize(array('cur', 'application/octet-stream')),
                serialize(array('psd', 'application/octet-stream')),
                serialize(array('rar', 'application/octet-stream')),
                serialize(array('exe', 'application/octet-stream')),
                serialize(array('ttf', 'application/octet-stream')),
            ))) {
                continue;
            }

            $cms_mime_type = array_key_exists($ext, $cms_mime_types) ? $cms_mime_types[$ext] : null;
            $this->assertTrue(($cms_mime_type == $mime_type) || ($cms_mime_type === null), 'Inconsistency between IIS mime types and Composr: ' . $ext . ': ' . $cms_mime_type . ' vs ' . $mime_type);
        }

        $this->assertTrue($found_bin);
    }

    public function testApacheMimeTypeConsistency()
    {
        require_code('mime_types');

        $cms_mime_types = get_mime_types(true);

        $url = 'https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types';
        $c = http_get_contents($url);

        $found_bin = false;

        $matches = array();
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
                if (in_array(serialize(array($ext, $mime_type)), array(
                    serialize(array('aac', 'audio/x-aac')),
                    serialize(array('xml', 'application/xml')),
                    serialize(array('xsl', 'application/xml')),
                    serialize(array('mp2', 'audio/mpeg')),
                    serialize(array('wav', 'audio/x-wav')),
                    serialize(array('tpl', 'application/vnd.groove-tool-template')),
                    serialize(array('f4v', 'video/x-f4v')),
                    serialize(array('m4v', 'video/x-m4v')),
                    serialize(array('avi', 'video/x-msvideo')),
                ))) {
                    continue;
                }

                $cms_mime_type = array_key_exists($ext, $cms_mime_types) ? $cms_mime_types[$ext] : null;
                $this->assertTrue(($cms_mime_type == $mime_type) || ($cms_mime_type === null), 'Inconsistency between Apache mime types and Composr: ' . $ext . ': ' . $cms_mime_type . ' vs ' . $mime_type);
            }
        }

        $this->assertTrue($found_bin);
    }

    public function testCodeTypes()
    {
        require_code('files2');
        $php_files = get_directory_contents(get_file_base(), '', IGNORE_NONBUNDLED | IGNORE_UNSHIPPED_VOLATILE | IGNORE_SHIPPED_VOLATILE | IGNORE_REBUILDABLE_OR_TEMP_FILES_FOR_BACKUP, true, true, array('php'));
        $exts = array();
        foreach ($php_files as $path) {
            $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);
            $matches = array();
            $num_matches = preg_match_all('#\.(\w{3})\'#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $ext = $matches[1][$i];

                if (preg_match('#^(\d+em|\d+)$#', $ext) != 0) {
                    continue;
                }

                if ($ext == 'dat') { // LEGACY
                    continue;
                }

                $exts[$ext] = true;
            }
        }
        $file_types = array_keys($exts);
        sort($file_types);
        $file_types = array_diff($file_types, array('MAD', 'MAI', 'MYD', 'MYI', 'alt', 'api', 'bat', 'cat', 'cgi', 'cms', 'com', 'crt', 'dir', 'dll', 'for', 'gcd', 'gid', 'git', 'inc', 'inf', 'jit', 'key', 'lcd', 'low', 'max', 'min', 'msg', 'net', 'old', 'org', 'pem', 'pid', 'pre', 'pwl', 'pws', 'rel', 'rev', 'src', 'swf', 'tcp', 'tld', 'tmp', 'uid', 'xap', 'xxx')); // Lots of stuff that is not needed to have any explicit handling

        $diff = array_diff($file_types, $this->file_types);
        $this->assertTrue(count($diff) == 0, 'File types used in code unknown to mime_types.php: ' . serialize($diff));
    }

    public function testTrackerValidTypes()
    {
        $path = get_file_base() . '/tracker/config/config_inc.php';
        $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

        $file_types = array();
        $matches = array();
        preg_match('#\$g_allowed_files = \'(.*)\';#', $c, $matches);
        $file_types = explode(',', $matches[1]);
        sort($file_types);

        $file_types_expected = $this->file_types;
        $file_types_expected = array_diff($file_types_expected, array('bin', 'exe', 'dmg', 'htm', 'html', 'svg', 'css', 'js', 'json', 'woff', 'woff2', 'xml', 'xsd', 'xsl', 'rss', 'atom')); // No executable or web formats should be uploaded by non-admins
        sort($file_types_expected);

        $this->assertTrue($file_types == $file_types_expected, 'Difference of: ' . serialize(array_diff($file_types_expected, $file_types)) . '/' . serialize(array_diff($file_types, $file_types_expected)));
    }

    public function testConfigValidTypes()
    {
        $path = get_file_base() . '/sources/hooks/systems/config/valid_types.php';
        $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

        $file_types = array();
        $matches = array();
        preg_match('#return \'([^\']+)\';#', $c, $matches);
        $file_types = explode(',', $matches[1]);
        sort($file_types);

        $file_types_expected = $this->file_types;
        $file_types_expected = array_diff($file_types_expected, array('bin', 'exe', 'dmg')); // No executables as users may try and get people to run on own machine (separately internally we filter web formats)
        sort($file_types_expected);

        $this->assertTrue($file_types == $file_types_expected, 'Difference of: ' . serialize(array_diff($file_types_expected, $file_types)) . '/' . serialize(array_diff($file_types, $file_types_expected)));
    }

    public function testAppYaml()
    {
        $path = get_file_base() . '/app.yaml';
        $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

        // --

        $file_types = array();
        $matches = array();
        preg_match('#- url: \/\(\.\*\\\.\((.*)\)\)#m', $c, $matches);
        $file_types = explode('|', $matches[1]);
        $file_types = array_diff($file_types, array('swf')); // We don't do mime-typing but do allow download
        sort($file_types);

        $file_types_expected = $this->file_types;
        $file_types_expected = array_diff($file_types_expected, array('php', 'htm')); // No files which may be web-processed/web-generated
        sort($file_types_expected);

        $this->assertTrue($file_types == $file_types_expected, 'Difference of: ' . serialize(array_diff($file_types_expected, $file_types)) . '/' . serialize(array_diff($file_types, $file_types_expected)));

        // --

        $file_types = array();
        $matches = array();
        preg_match('#  upload: \.\*\\\.\((.*)\)#m', $c, $matches);
        $file_types = explode('|', $matches[1]);
        $file_types = array_diff($file_types, array('swf')); // We don't do mime-typing but do allow download
        sort($file_types);

        $file_types_expected = $this->file_types;
        $file_types_expected = array_diff($file_types_expected, array('bin', 'php', 'htm')); // No files which may be web-processed/web-generated
        sort($file_types_expected);

        $this->assertTrue($file_types == $file_types_expected, 'Difference of: ' . serialize(array_diff($file_types_expected, $file_types)) . '/' . serialize(array_diff($file_types, $file_types_expected)));
    }

    public function testCodebookRef()
    {
        $path = get_file_base() . '/docs/pages/comcode_custom/EN/codebook_3.txt';
        $c = cms_file_get_contents_safe($path, FILE_READ_LOCK); // TODO #3467

        $file_types = array();
        $matches = array();
        preg_match('#\.\*\\\\\.\((.*)\)\\\\\?\?\'\);\[\/tt\]#', $c, $matches);
        $file_types = explode('|', $matches[1]);
        $file_types = array_diff($file_types, array('swf')); // We don't do mime-typing but do allow download
        sort($file_types);

        $file_types_expected = $this->file_types;
        $file_types_expected = array_diff($file_types_expected, array('php', 'htm')); // No files which may be web-processed/web-generated
        sort($file_types_expected);

        $this->assertTrue($file_types == $file_types_expected, 'Difference of: ' . serialize(array_diff($file_types_expected, $file_types)) . '/' . serialize(array_diff($file_types, $file_types_expected)));
    }

    public function testHtaccess()
    {
        $path = get_file_base() . '/recommended.htaccess';
        $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

        $file_types = array();
        $matches = array();
        preg_match('#RewriteCond \$1 \\\\\.\((.*)\) \[OR\]#', $c, $matches);
        $file_types = explode('|', $matches[1]);
        $file_types = array_diff($file_types, array('swf')); // We don't do mime-typing but do allow download
        sort($file_types);

        $file_types_expected = $this->file_types;
        $file_types_expected = array_diff($file_types_expected, array('htm')); // No .htm files which may be web-generated
        sort($file_types_expected);

        $this->assertTrue($file_types == $file_types_expected, 'Difference of: ' . serialize(array_diff($file_types_expected, $file_types)) . '/' . serialize(array_diff($file_types, $file_types_expected)));
    }

    public function testOtherValidTypes()
    {
        require_code('images');

        foreach (array('valid_images', 'valid_videos', 'valid_audios') as $f) {
            $path = get_file_base() . '/sources/hooks/systems/config/' . $f . '.php';
            $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

            $file_types = array();
            $matches = array();
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

    public function testGitAttributes()
    {
        $path = get_file_base() . '/.gitattributes';
        $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);

        foreach ($this->file_types as $file_type) {
            $this->assertTrue(strpos($c, '*.' . $file_type . ' ') !== false, 'File type missing from .gitattributes, ' . $file_type);
        }

        $matches = array();
        $num_matches = preg_match_all('#^\*\.(\w+) (text|binary)#m', $c, $matches);
        $found = array();
        for ($i = 0; $i < $num_matches; $i++) {
            $ext = $matches[1][$i];
            $this->assertTrue(!array_key_exists($ext, $found), 'Double referenced ' . $ext);
            $found[$ext] = true;
        }

        // Test git is conclusive
        $lines = explode("\n", shell_exec('git ls-files'));
        $exts = array();
        foreach ($lines as $line) {
            $filename = basename($line);
            $ext = get_file_extension($filename);
            if (($ext != '') && ('.' . $ext != $filename)) {
                $exts[$ext] = true;
            }
        }
        ksort($exts);
        foreach (array_keys($exts) as $ext) {
            $this->assertTrue(array_key_exists($ext, $found), 'Unknown file type in git that should be in .gitattributes to clarify how to manage it: ' . $ext);
        }
    }

    public function testCSS()
    {
        $path = get_file_base() . '/themes/default/css/global.css';
        $c = cms_file_get_contents_safe($path, FILE_READ_LOCK); // TODO #3467

        /*
        Not all will be here
        foreach ($this->file_types as $file_type) {
            $this->assertTrue(strpos($c, '[href$=".' . $file_type . '"]') !== false, 'File type missing from global.css, ' . $file_type);
        }
        */

        $matches = array();
        $num_matches = preg_match_all('#^\[href\$="\.(\w+)"\]#m', $c, $matches);
        $found = array();
        for ($i = 0; $i < $num_matches; $i++) {
            $ext = $matches[1][$i];
            $this->assertTrue(in_array($ext, $this->file_types), 'Rogue file type ' . $ext);
            $found[$ext] = true;
        }
    }
}
