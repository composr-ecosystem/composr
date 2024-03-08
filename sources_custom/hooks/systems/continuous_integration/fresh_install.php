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

        copy(get_file_base() . '/_config.php', get_file_base() . '/_config_ci.php.bak');
        fix_permissions(get_file_base() . '/_config_ci.php.bak');

        $database = get_db_site();
        $username = get_db_site_user();
        $password = get_db_site_password();
        $table_prefix = 'ci_' . $GLOBALS['SITE_DB']->get_table_prefix();

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

        // Cannot continue CI on this process since we have a new database
        $context['fresh_install'] = 0;
        return false;
    }

    public function after($output, $commit_id, $verbose, $dry_run, $limit_to, $context)
    {
        if (!addon_installed('testing_platform')) {
            return true;
        }

        if ($context['fresh_install'] !== '0') {
            return true;
        }

        if ($output) {
            echo "\n" . 'Cleaning up fresh installation... ';
        }

        $table_prefix = $GLOBALS['SITE_DB']->get_table_prefix();

        if (strpos($table_prefix, 'ci') !== 0) {
            @unlink(get_file_base() . '/_config.php');
            @rename(get_file_base() . '/_config_ci.php.bak', get_file_base() . '/_config.php');

            if ($output) {
                echo 'NOT NECESSARY';
            }

            // Cannot continue CI on this process since we have a new database
            unset($context['fresh_install']);
            return false;
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

        @unlink(get_file_base() . '/_config.php');
        @rename(get_file_base() . '/_config_ci.php.bak', get_file_base() . '/_config.php');

        if ($output) {
            echo 'DONE';
        }

        // Cannot continue CI on this process since we have a new database
        unset($context['fresh_install']);
        return false;
    }
}
