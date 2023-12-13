<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    remote_content_grabber
 */

// Sample function for Photobucket, supporting CLI parameters to define ranges
function grab_photobucket_content()
{
    $opts = array(
        'local_save_path' => 'uploads/pbextract',
        'url_patterns' => array(
            '^' . preg_quote('http://', '#') . '.*' . preg_quote('photobucket.com/', '#') . '.*$',
            '^' . preg_quote(find_script('external_url_proxy') . '?url=', '#') . preg_quote(urlencode('http://'), '#') . '.*' . preg_quote(urlencode('photobucket.com/'), '#') . '.*$',
        ),
        'url_modifier' => function($url) { return $url . '~original'; }, // TODO: Against coding standards to have a function directly on an array item
        'filename_extractor' => function($url) { // TODO: Against coding standards to have a function directly on an array item
            if (strpos($url, find_script('external_url_proxy')) !== false) {
                return preg_replace('#\?.*$#', '', basename(urldecode(preg_replace('#^.*\?url=$#', '', $url))));
            } else {
                return preg_replace('#\?.*$#', '', basename($url));
            }
        },
        'testing_mode' => false,
        'start_from_id' => db_get_first_id(),
        'end_before_id' => null,
    );

    if (isset($_SERVER['argv'][1])) {
        $opts['start_from_id'] = intval($_SERVER['argv'][1]);
    }
    if (isset($_SERVER['argv'][2])) {
        $opts['end_before_id'] = intval($_SERVER['argv'][2]);
    }

    $ob = new RemoteContentGrabber($opts);
    $ob->run();
}

class RemoteContentGrabber
{
    protected $url_context_regexps = array();

    protected $opts = array();

    public function __construct($opts)
    {
        if (php_function_allowed('set_time_limit')) {
            @set_time_limit(0);
        }

        $this->opts = array(
            'local_save_path' => 'uploads/website_specific',
            'has_subdirs' => true,

            'url_patterns' => array(
                '^' . preg_quote('http://example.com/', '#') . '.*$',
            ),
            'url_modifier' => function($url) { return $url; }, // TODO: Against coding standards to have a function directly on an array item
            'filename_extractor' => function($url) { return preg_replace('#\?.*$#', '', basename($url)); }, // TODO: Against coding standards to have a function directly on an array item

            'db' => $GLOBALS['FORUM_DB'],
            'table' => 'f_posts',
            'id_field' => 'id',
            'content_field' => 'p_post',
            'where' => array(),
            //'start' => 0,
            'start_from_id' => db_get_first_id(),
            'end_before_id' => null,

            'testing_mode' => true,
        );

        $this->url_context_regexps = array(
            '\[img[^\[\]]*\]([^\[\]]*)\[\/img\]',
            '<img[^<>]*\ssrc="([^"]*)"',
        );

        $this->opts = $opts + $this->opts;
    }

    public function run()
    {
        $opts = &$this->opts;

        $max = 100;

        //$start = $opts['start'];
        $start_from_id = $opts['start_from_id'];

        do {
            $select = array($opts['id_field'], $opts['content_field']);

            $where = $opts['where'];
            if ($where === null) {
                $where = array();
            }

            if (count($where) == 0) {
                $extra = 'WHERE 1=1';
            } else {
                $extra = '';
            }

            $extra .= ' AND ' . $opts['id_field'] . '>=' . $start_from_id;
            if ($opts['end_before_id'] !== null) {
                $extra .= ' AND ' . $opts['id_field'] . '<' . $opts['end_before_id'];
            }

            $extra .= ' ORDER BY ' . $opts['id_field'];

            $rows = $opts['db']->query_select($opts['table'], $select, $where, $extra, $max);

            foreach ($rows as $row) {
                $this->process_row($row);

                $start_from_id = $row[$opts['id_field']] + 1;
            }

            //$start += $max;
        } while (count($rows) > 0);
    }

    protected function process_row($row)
    {
        $opts = &$this->opts;

        $id = $row[$opts['id_field']];
        $_id = is_integer($id) ? strval($id) : $id;
        $content = $row[$opts['content_field']];

        // Work out save position
        if ($opts['has_subdirs']) {
            $_path_base = $opts['local_save_path'] . '/' . $_id;
        } else {
            $_path_base = $opts['local_save_path'];
        }
        $path_base = get_custom_file_base() . '/' . $_path_base;
        $url_base = get_custom_base_url() . '/' . $_path_base;

        // Find referenced URLs in content
        $referenced_urls = array();
        foreach ($this->url_context_regexps as $content_regexp) {
            $matches_context = array();
            $num_matches_context = preg_match_all('#' . $content_regexp . '#i', $content, $matches_context);
            for ($i = 0; $i < $num_matches_context; $i++) {
                foreach ($this->opts['url_patterns'] as $url_regexp) {
                    $matches_urls = array();
                    if (preg_match('#' . $url_regexp . '#', $matches_context[1][$i], $matches_urls) != 0) {
                        $referenced_url = $matches_urls[0];
                        $_referenced_url = call_user_func($opts['url_modifier'], $referenced_url);
                        $referenced_urls[$referenced_url] = $_referenced_url;
                    }
                }
            }
        }

        // Download referenced URLs
        $downloaded_urls = array();
        foreach ($referenced_urls as $referenced_url => $download_url) {
            // Find target filename
            if ($opts['filename_extractor'] === null) {
                // No extractor function, so we need to do a HEAD request...

                $test = http_download_file($download_url, 0, false);
                if ($test === null) {
                    $this->log('Could not touch URL ' . $referenced_url . ' (while processing ' . $_id . ')');
                    continue;
                }

                global $HTTP_FILENAME;
                $filename = $HTTP_FILENAME;

                if ($filename === null) {
                    $this->log('Could not find filename for ' . $referenced_url . ' (while processing ' . $_id . ')');
                    continue;
                }
            } else {
                $filename = call_user_func($opts['filename_extractor'], $referenced_url);
            }

            if (empty($filename)) {
                $this->log('Empty filename for ' . $referenced_url . ' (while processing ' . $_id . ')');
                continue;
            }

            // Where it will be saved to
            $file_path = $path_base . '/' . $filename;
            $file_url = $url_base . '/' . $filename;

            // Download if not currently there
            if (is_file($file_path)) {
                $this->log('Already downloaded URL ' . $referenced_url . ' as ' . $filename . ' (while processing ' . $_id . ')');
            } else {
                $data = http_download_file($download_url, null, false);
                if (empty($data)) {
                    $this->log('Could not download URL ' . $referenced_url . ' (while processing ' . $_id . ')');
                    continue;
                } else {
                    $this->log('Downloaded URL ' . $referenced_url . ' as ' . $filename . ' (while processing ' . $_id . ')');
                }

                // Make directory if missing
                @mkdir($path_base, 0777, true);

                // Save
                file_put_contents($file_path, $data);
                fix_permissions($file_path);
                sync_file($file_path);
            }

            // Note mappings
            $downloaded_urls[$referenced_url] = $file_url;
        }

        // No changes?
        if (count($downloaded_urls) == 0) {
            return;
        }

        // Alter content
        foreach ($downloaded_urls as $referenced_url => $file_url) {
            $content = str_replace($referenced_url, $file_url, $content);
        }

        // Save content
        if (!$opts['testing_mode']) {
            $opts['db']->query_update($opts['table'], array($opts['content_field'] => $content), array($opts['id_field'] => $id), '', 1);
        }

        // Log
        $this->log('Done ' . $_id);
    }

    protected function log($message)
    {
        echo $message . "\n";
    }
}
