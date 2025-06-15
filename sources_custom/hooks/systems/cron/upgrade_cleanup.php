<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

/**
 * Hook class.
 */
class Hook_cron_upgrade_cleanup
{
    /**
     * Get info from this hook.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     * @param  ?boolean $calculate_num_queued Calculate the number of items queued, if possible (null: the hook may decide / low priority)
     * @return ?array Return a map of info about the hook (null: disabled)
     */
    public function info(?int $last_run, ?bool $calculate_num_queued) : ?array
    {
        if (!addon_installed('cms_homesite')) {
            return null;
        }

        $num_queued_val = null;

        if ($calculate_num_queued !== false) {
            $num_queued_val = $this->clean(true);
        }

        return [
            'label' => 'Clean up old personal upgraders',
            'num_queued' => $num_queued_val,
            'minutes_between_runs' => 60 * 24,
            'enabled_by_default' => true,
        ];
    }

    /**
     * Run function for system scheduler hooks. Searches for things to do. ->info(..., true) must be called before this method.
     *
     * @param  ?TIME $last_run Last time run (null: never)
     */
    public function run(?int $last_run)
    {
        $this->clean();
    }

    /**
     * Clean old upgraders.
     *
     * @param  boolean $return_only Whether to not actually clean
     * @return integer The number of files cleaned or to be cleaned
     */
    protected function clean(bool $return_only = false) : int
    {
        $build_path = get_custom_file_base() . '/uploads/website_specific/cms_homesite/upgrades/tars';

        require_code('failure');

        $files_to_clean_up = 0;
        if (is_dir($build_path)) {
            //set_throw_errors(true);
            try {
                $cutoff_time = time() - (30 * 24 * 60 * 60); // 30 days ago

                $directory_iterator = new RecursiveDirectoryIterator($build_path, RecursiveDirectoryIterator::SKIP_DOTS);
                $iterator = new RecursiveIteratorIterator($directory_iterator, RecursiveIteratorIterator::LEAVES_ONLY);

                foreach ($iterator as $file_info) {
                    if (!$file_info->isFile()) {
                        continue;
                    }

                    // Do not delete access controllers
                    if (($file_info->getFilename() == 'index.html') || ($file_info->getFilename() == '.htaccess')) {
                        continue;
                    }

                    if ($file_info->getMTime() >= $cutoff_time) {
                        continue;
                    }

                    $files_to_clean_up++;

                    if ($return_only === false) {
                        @unlink($file_info->getRealPath());
                    }
                }
            } catch (Exception $e) {
                // $files_to_clean_up will remain its current count.
            }
            //set_throw_errors(false);
        }

        return $files_to_clean_up;
    }
}
