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

/**
 * Hook class.
 */
class Hook_ci_fresh_install
{
    public function before($output, $commit_id, $verbose, $dry_run, $limit_to, &$context)
    {
        if (!addon_installed('testing_platform')) {
            return true;
        }

        if ($context['fresh_install'] !== '1') {
            return true;
        }

        if ($output) {
            echo "\n" . 'Making a fresh installation of Composr CMS... ';
        }

        // Set up a fresh install of Composr CMS before running tests

        $database = get_db_site();
        $username = get_db_site_user();
        $password = get_db_site_password();
        $table_prefix = 'ci_' . $GLOBALS['SITE_DB']->get_table_prefix();

        require_code('install_headless');
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $success = do_install_to(
            $database,
            $username,
            $password,
            $table_prefix,
            false,
            'cns',
            null,
            null,
            null,
            null,
            null,
            [],
            true,
            get_db_type(),
            true
        );

        if (!$success) {
            if ($output) {
                echo 'FAILED!';
            }
            throw new Exception('Failed to install a fresh Composr CMS installation for continuous integration.');
        }

        // The installer test will overwrite this if we do not use a different name
        @rename(get_file_base() . '/_config.php.bak', get_file_base() . '/_config.php.ci.bak');

        // Cannot continue CI on this process since we have a new database
        $context['fresh_install'] = '0';
        return false;
    }

    public function after($output, $commit_id, $verbose, $dry_run, $limit_to, &$context)
    {
        if (!addon_installed('testing_platform')) {
            return true;
        }

        if ($context['fresh_install'] !== '0') {
            return true;
        }

        if ($output) {
            echo "\n" . 'Cleaning up fresh install database... ';
        }

        $table_prefix = $GLOBALS['SITE_DB']->get_table_prefix();

        if (strpos($table_prefix, 'ci_') !== 0) {
            if ($output) {
                echo 'NOT NECESSARY';
            }

            return true;
        }

        // Delete database tables
        $tables = $GLOBALS['SITE_DB']->query_select('db_meta', ['DISTINCT m_table'], [], '', null, 0, true);
        if ($tables !== null) {
            foreach ($tables as $i => $table) {
                // These tables must be dropped last
                if (($table['m_table'] == 'db_meta') || ($table['m_table'] == 'db_meta_indices')) {
                    continue;
                }

                if (strpos($table['m_table'], 'f_') === 0) {
                    $GLOBALS['FORUM_DB']->drop_table_if_exists($table['m_table']);
                } else {
                    $GLOBALS['SITE_DB']->drop_table_if_exists($table['m_table']);
                }
            }
            $GLOBALS['SITE_DB']->drop_table_if_exists('db_meta');
            $GLOBALS['SITE_DB']->drop_table_if_exists('db_meta_indices');
        }


        if ($output) {
            echo 'DONE';
        }

        return true;
    }

    public function after_checkout($output, $commit_id, $verbose, $dry_run, $limit_to, &$context)
    {
        if (!addon_installed('testing_platform')) {
            return true;
        }

        if ($context['fresh_install'] !== '0') {
            return true;
        }

        if ($output) {
            echo "\n" . 'Cleaning up git repository... ';
        }

        // Load in our backup config file before reset
        $config_file_path = get_file_base() . '/_config.php.ci.bak';
        $config_file = cms_file_get_contents_safe($config_file_path, FILE_READ_LOCK);

        // We also need to load in the ci_queue.bin file
        require_code('continuous_integration');
        $commit_queue = cms_file_get_contents_safe(CI_COMMIT_QUEUE_PATH, FILE_READ_LOCK);

        // Reset
        shell_exec('git clean -fdx');

        // Put our config and ci queue files back where they belong
        require_code('files');
        cms_file_put_contents_safe(get_file_base() . '/_config.php', $config_file); // bak file now becomes the main file
        cms_file_put_contents_safe(CI_COMMIT_QUEUE_PATH, $commit_queue);

        // Cannot continue CI on this process since we changed config files
        unset($context['fresh_install']);
        return false;
    }
}
