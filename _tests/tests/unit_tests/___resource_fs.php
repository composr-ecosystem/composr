<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

// php _tests/index.php ___resource_fs

/*
These tests test all var hooks. Some general Resource-fs tests are in the commandr_fs test set.
*/

// If a test is failing, try emptying out the alternative_ids table, and test content from content tables.

/**
 * Composr test case class (unit testing).
 */
class ___resource_fs_test_set extends cms_test_case
{
    protected $resource_fs_obs;
    protected $paths = null;

    public function setUp()
    {
        parent::setUp();

        push_query_limiting(false);

        require_code('content');
        require_code('resource_fs');
        require_code('failure');

        if ($this->paths === null) {
            $this->paths = [];
        }

        static $done_once = false;
        if (!$done_once) {
            //$GLOBALS['SITE_DB']->query_delete('alternative_ids'); Messes up future runs
            $GLOBALS['SITE_DB']->query_delete('url_id_monikers');
            $done_once = true;
        }

        $this->resource_fs_obs = [];
        $commandr_fs_hooks = find_all_hooks('systems', 'commandr_fs');
        foreach ($commandr_fs_hooks as $commandr_fs_hook => $dir) {
            if (($this->only !== null) && ($commandr_fs_hook != $this->only)) {
                continue;
            }

            if (get_forum_type() != 'cns') {
                if ($commandr_fs_hook == 'aggregate_type_instances') { // Contains usergroup creation and referencing by default
                    continue;
                }
            }

            $path = get_file_base() . '/' . $dir . '/hooks/systems/commandr_fs/' . $commandr_fs_hook . '.php';
            $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);
            if (strpos($c, ' extends Resource_fs_base') !== false) {
                require_code('hooks/systems/commandr_fs/' . filter_naughty_harsh($commandr_fs_hook));
                $ob = object_factory('Hook_commandr_fs_' . filter_naughty_harsh($commandr_fs_hook));

                if ($ob->is_active()) {
                    $this->resource_fs_obs[$commandr_fs_hook] = $ob;
                }
            }
        }
    }

    public function testAdd()
    {
        foreach ($this->resource_fs_obs as $commandr_fs_hook => $ob) {
            $old_limit = cms_set_time_limit(10);

            $path = '';
            if ($ob->folder_resource_type !== null) {
                $folder_resource_type_1 = is_array($ob->folder_resource_type) ? $ob->folder_resource_type[0] : $ob->folder_resource_type;

                // Cleanup if run this before. Probably will product errors, but string-based IDs stick around on crashing and a new add would use a variant ID, so we need to try this.
                set_throw_errors(true);
                try {
                    $_path = $ob->folder_convert_id_to_filename($folder_resource_type_1, 'test-a');
                    $ob->folder_delete('test-a', $_path);
                } catch (Exception $e) {
                } catch (Error $e) {
                }
                set_throw_errors(false);

                $result = $ob->folder_add('test-a', $path, []);
                $this->assertTrue($result !== false, 'Failed to folder_add ' . $commandr_fs_hook);
                $path = $ob->folder_convert_id_to_filename($folder_resource_type_1, $result);
                $result = $ob->folder_add('test-b', $path, []);
                if ($result !== false) {
                    $folder_resource_type_2 = is_array($ob->folder_resource_type) ? $ob->folder_resource_type[1] : $ob->folder_resource_type;
                    $path .= '/' . $ob->folder_convert_id_to_filename($folder_resource_type_2, $result);
                }
            }

            // Cleanup if run this before. Probably will product errors, but string-based IDs stick around on crashing and a new add would use a variant ID, so we need to try this.
            set_throw_errors(true);
            try {
                $ob->file_delete('test_content.' . RESOURCE_FS_DEFAULT_EXTENSION, $path);
            } catch (Exception $e) {
            } catch (Error $e) {
            }
            set_throw_errors(false);

            $result = $ob->file_add('test_content.' . RESOURCE_FS_DEFAULT_EXTENSION, $path, []);
            destrictify();
            $this->assertTrue($result !== false, 'Failed to file_add ' . $commandr_fs_hook . ' (' . $path . ')');
            $this->paths[$commandr_fs_hook] = $path;

            cms_set_time_limit($old_limit);
        }
    }

    public function testCount()
    {
        $commandr_fs = new Commandr_fs();

        foreach ($this->resource_fs_obs as $commandr_fs_hook => $ob) {
            $old_limit = cms_set_time_limit(10);

            $count_folders = 0;
            if ($ob->folder_resource_type !== null) {
                foreach (is_array($ob->folder_resource_type) ? $ob->folder_resource_type : [$ob->folder_resource_type] as $resource_type) {
                    $count_folders += $ob->get_resources_count($resource_type);
                    $this->assertTrue(empty($ob->find_resource_by_label($resource_type, str_replace('.', '_', uniqid('', true))))); // Search for a unique random ID should find nothing
                }
            }
            $count_files = 0;
            foreach (is_array($ob->file_resource_type) ? $ob->file_resource_type : [$ob->file_resource_type] as $resource_type) {
                $count_files += $ob->get_resources_count($resource_type);
                $this->assertTrue(empty($ob->find_resource_by_label($resource_type, str_replace('.', '_', uniqid('', true))))); // Search for a unique random ID should find nothing
            }

            $listing = $this->_recursive_listing($ob, [], 'var', $commandr_fs);

            $count = $count_folders + $count_files;

            $ok = ($count == count($listing));
            if ($this->debug) {
                @var_dump($listing);
            }

            $this->assertTrue(
                $ok,
                'File/folder count mismatch for ' . $commandr_fs_hook . ' (' . integer_format($count_folders) . ' folders + ' . integer_format($count_files) . ' files -vs- ' . integer_format(count($listing)) . ' in Commandr-fs listing)'
            );

            cms_set_time_limit($old_limit);
        }
    }

    protected function _recursive_listing($ob, $meta_dir, $meta_root_node, $commandr_fs)
    {
        $listing = $ob->listing($meta_dir, $meta_root_node, $commandr_fs);
        foreach ($listing as $f) {
            if ($f[1] == COMMANDR_FS_DIR) {
                $sub_listing = $this->_recursive_listing($ob, array_merge($meta_dir, [$f[0]]), $meta_root_node, $commandr_fs);
                foreach ($sub_listing as $s_f) {
                    $suffix = '.' . RESOURCE_FS_DEFAULT_EXTENSION;
                    if (($s_f[0] != '_folder' . $suffix) && (($s_f[1] == COMMANDR_FS_DIR) || (substr($s_f[0], -strlen($suffix)) == $suffix))) {
                        $s_f[0] = $f[0] . '/' . $s_f[0];
                        $listing[] = $s_f;
                    }
                }
            }
        }
        return $listing;
    }

    public function testSearch()
    {
        foreach ($this->resource_fs_obs as $commandr_fs_hook => $ob) {
            $old_limit = cms_set_time_limit(10);

            if ($ob->folder_resource_type !== null) {
                $folder_resource_type = is_array($ob->folder_resource_type) ? $ob->folder_resource_type[0] : $ob->folder_resource_type;
                list(, $folder_resource_id) = $ob->folder_convert_filename_to_id('test-a', $folder_resource_type);
                $this->assertTrue($folder_resource_id !== null, 'Could not folder_convert_filename_to_id');
                if ($folder_resource_id !== null) {
                    $test = $ob->search($folder_resource_type, $folder_resource_id, true);
                    $this->assertTrue($test !== null, 'Could not search for ' . $folder_resource_type . ' test-a');
                }
            }

            $file_resource_type = is_array($ob->file_resource_type) ? $ob->file_resource_type[0] : $ob->file_resource_type;
            list(, $file_resource_id) = $ob->file_convert_filename_to_id('test_content', $file_resource_type);
            $this->assertTrue($file_resource_id !== null, 'Could not file_convert_filename_to_id');
            if ($file_resource_id !== null) {
                $test = $ob->search($file_resource_type, $file_resource_id, true);
                $this->assertTrue($test !== null, 'Could not search for ' . $file_resource_type . ' ' . $file_resource_id);
                if ($test !== null) {
                    if ($ob->folder_resource_type === null) {
                        $this->assertTrue($test == '', 'Should have found in root, ' . $file_resource_type);
                    } else {
                        $this->assertTrue($test != '', 'Should not have found in root, ' . $file_resource_type);
                    }
                }
            }

            cms_set_time_limit($old_limit);
        }
    }

    public function testFindByLabel()
    {
        foreach ($this->resource_fs_obs as $commandr_fs_hook => $ob) {
            $old_limit = cms_set_time_limit(10);

            if ($ob->folder_resource_type !== null) {
                $results = [];
                foreach (is_array($ob->folder_resource_type) ? $ob->folder_resource_type : [$ob->folder_resource_type] as $resource_type) {
                    $results = array_merge($results, $ob->find_resource_by_label($resource_type, 'test-a'));
                    $results = array_merge($results, $ob->find_resource_by_label($resource_type, 'test-b'));
                }
                $this->assertTrue(!empty($results), 'Failed to find_resource_by_label (folder) ' . $commandr_fs_hook);
            }
            $results = [];
            foreach (is_array($ob->file_resource_type) ? $ob->file_resource_type : [$ob->file_resource_type] as $resource_type) {
                $results = array_merge($results, $ob->find_resource_by_label($resource_type, 'test_content'));
            }
            $this->assertTrue(!empty($results), 'Failed to find_resource_by_label (file) ' . $commandr_fs_hook);

            cms_set_time_limit($old_limit);
        }
    }

    public function testLoad()
    {
        foreach ($this->resource_fs_obs as $commandr_fs_hook => $ob) {
            $old_limit = cms_set_time_limit(10);

            $path = $this->paths[$commandr_fs_hook];

            if ($path != '') {
                $result = $ob->folder_load(basename($path), dirname($path));
                $this->assertTrue($result !== false, 'Failed to folder_load ' . $commandr_fs_hook);
            }

            $result = $ob->file_load('test_content.' . RESOURCE_FS_DEFAULT_EXTENSION, $path);
            $this->assertTrue($result !== false, 'Failed to file_load ' . $commandr_fs_hook . ' (' . $path . ')');

            cms_set_time_limit($old_limit);
        }
    }

    public function testEdit()
    {
        foreach ($this->resource_fs_obs as $commandr_fs_hook => $ob) {
            $old_limit = cms_set_time_limit(10);

            $path = $this->paths[$commandr_fs_hook];

            if ($path != '') {
                $result = $ob->folder_load(basename($path), (strpos($path, '/') === false) ? '' : dirname($path));
                $this->assertTrue($result !== false, 'Failed to folder_load before folder_edit ' . $commandr_fs_hook);
                if ($result !== false) {
                    $result = $ob->folder_edit(basename($path), (strpos($path, '/') === false) ? '' : dirname($path), $result);
                    $this->assertTrue($result !== false, 'Failed to folder_edit ' . $commandr_fs_hook . ' (' . $path . ')');
                }

                if (strpos($path, '/') !== false) {
                    $_path = dirname($path);
                    $result = $ob->folder_load(basename($_path), (strpos($_path, '/') === false) ? '' : dirname($_path));

                    $result = $ob->folder_edit(basename($_path), (strpos($_path, '/') === false) ? '' : dirname($_path), $result);
                    $this->assertTrue($result !== false, 'Failed to folder_edit ' . $commandr_fs_hook . ' (' . $_path . ')');
                }
            }

            $result = $ob->file_edit('test_content.' . RESOURCE_FS_DEFAULT_EXTENSION, $path, ['label' => 'test_content']);
            $this->assertTrue($result !== false, 'Failed to file_edit ' . $commandr_fs_hook . ' (' . $path . ')');

            cms_set_time_limit($old_limit);
        }
    }

    public function testDelete()
    {
        foreach ($this->resource_fs_obs as $commandr_fs_hook => $ob) {
            $old_limit = cms_set_time_limit(10);

            $path = $this->paths[$commandr_fs_hook];

            $result = $ob->file_delete('test_content.' . RESOURCE_FS_DEFAULT_EXTENSION, $path);
            $this->assertTrue($result !== false, 'Failed to file_delete ' . $commandr_fs_hook . ' (' . $path . ')');

            if ($path != '') {
                $result = $ob->folder_delete(basename($path), (strpos($path, '/') === false) ? '' : dirname($path));
                $this->assertTrue($result !== false, 'Failed to folder_delete ' . $commandr_fs_hook . ' (' . $path . ')');

                set_throw_errors(true);
                try {
                    if (strpos($path, '/') !== false) {
                        $_path = dirname($path);
                        $result = $ob->folder_delete(basename($_path), (strpos($_path, '/') === false) ? '' : dirname($_path));
                        $this->assertTrue($result !== false, 'Failed to folder_delete ' . $commandr_fs_hook . ' (' . $_path . ')');
                    }
                } catch (Exception $e) {
                } catch (Error $e) {
                }
                set_throw_errors(false);
            }

            cms_set_time_limit($old_limit);
        }
    }
}
