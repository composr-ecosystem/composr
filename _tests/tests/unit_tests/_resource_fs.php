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

/*
These tests test all var hooks. Some general Resource-FS tests are in the commandr_fs test set.
*/

/**
 * Composr test case class (unit testing).
 */
class _resource_fs_test_set extends cms_test_case
{
    public $resource_fs_obs;
    public $paths = null;

    public $test_filename = null;

    public function setUp()
    {
        parent::setUp();

        if ($this->test_filename === null) {
            $this->test_filename = 'test_content' . strval(time());
        }

        $GLOBALS['NO_QUERY_LIMIT'] = true;

        require_code('content');
        require_code('resource_fs');

        if ($this->paths === null) {
            $this->paths = array();
        }

        //$GLOBALS['SITE_DB']->query_delete('alternative_ids'); Messes up future runs
        $GLOBALS['SITE_DB']->query_delete('url_id_monikers');

        $limit_to = get_param_string('limit_to', null); // Useful for breaking down testing into more manageable isolated pieces

        $this->resource_fs_obs = array();
        $commandr_fs_hooks = find_all_hooks('systems', 'commandr_fs');
        foreach ($commandr_fs_hooks as $commandr_fs_hook => $dir) {
            if ((!is_null($limit_to)) && ($commandr_fs_hook != $limit_to)) {
                continue;
            }

            $path = get_file_base() . '/' . $dir . '/hooks/systems/commandr_fs/' . $commandr_fs_hook . '.php';
            $contents = file_get_contents($path);
            if (strpos($contents, ' extends Resource_fs_base') !== false) {
                require_code('hooks/systems/commandr_fs/' . $commandr_fs_hook);
                $ob = object_factory('Hook_commandr_fs_' . $commandr_fs_hook);
                $this->resource_fs_obs[$commandr_fs_hook] = $ob;
            }
        }
    }

    public function testAdd()
    {
        foreach ($this->resource_fs_obs as $commandr_fs_hook => $ob) {
            $path = '';
            if (!is_null($ob->folder_resource_type)) {
                $result = $ob->folder_add('test-a', $path, array());
                $this->assertTrue($result !== false, 'Failed to folder_add ' . $commandr_fs_hook);
                $folder_resource_type_1 = is_array($ob->folder_resource_type) ? $ob->folder_resource_type[0] : $ob->folder_resource_type;
                $path = $ob->folder_convert_id_to_filename($folder_resource_type_1, $result);
                $result = $ob->folder_add('test-b', $path, array());
                if ($result !== false) {
                    $folder_resource_type_2 = is_array($ob->folder_resource_type) ? $ob->folder_resource_type[1] : $ob->folder_resource_type;
                    $path .= '/' . $ob->folder_convert_id_to_filename($folder_resource_type_2, $result);
                }
            }
            $result = $ob->file_add($this->test_filename . '.' . RESOURCE_FS_DEFAULT_EXTENSION, $path, array());
            destrictify();
            $this->assertTrue($result !== false, 'Failed to file_add ' . $commandr_fs_hook . ' (' . $path . ')');
            $this->paths[$commandr_fs_hook] = $path;
        }
    }

    public function testCount()
    {
        $commandr_fs = new Commandr_fs();

        foreach ($this->resource_fs_obs as $commandr_fs_hook => $ob) {
            $count_folders = 0;
            if (!is_null($ob->folder_resource_type)) {
                foreach (is_array($ob->folder_resource_type) ? $ob->folder_resource_type : array($ob->folder_resource_type) as $resource_type) {
                    $count_folders += $ob->get_resources_count($resource_type);
                    $this->assertTrue($ob->find_resource_by_label($resource_type, str_replace('.', '_', uniqid('', true))) == array(), 'Random unique ID found something (when it should have found nothing) for folder resource ' . $commandr_fs_hook . '->' . $resource_type); // Search for a unique random ID should find nothing
                }
            }
            $count_files = 0;
            foreach (is_array($ob->file_resource_type) ? $ob->file_resource_type : array($ob->file_resource_type) as $resource_type) {
                $count_files += $ob->get_resources_count($resource_type);
                $this->assertTrue($ob->find_resource_by_label($resource_type, str_replace('.', '_', uniqid('', true))) == array(), 'Random unique ID found something (when it should have found nothing) for file resource ' . $commandr_fs_hook . '->' . $resource_type); // Search for a unique random ID should find nothing
            }

            $listing = $this->_recursive_listing($ob, array(), array('var', $commandr_fs_hook), $commandr_fs);
            $count = $count_folders + $count_files;
            $this->assertTrue(
                $count == count($listing),
                'File/folder count mismatch for ' . $commandr_fs_hook . ' (' . integer_format($count_folders) . ' folders + ' . integer_format($count_files) . ' files -vs- ' . integer_format(count($listing)) . ' in Commandr-fs listing)'
            );
            //if ($count != count($listing)) { @var_dump($listing); @exit('!' . $count . '!' . $commandr_fs_hook); } //Useful for debugging
        }
    }

    protected function _recursive_listing($ob, $meta_dir, $meta_root_node, $commandr_fs)
    {
        $listing = $ob->listing($meta_dir, $meta_root_node, $commandr_fs);
        foreach ($listing as $f) {
            if ($f[1] == COMMANDR_FS_DIR) {
                $sub_listing = $this->_recursive_listing($ob, array_merge($meta_dir, array($f[0])), $meta_root_node, $commandr_fs);
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
            if (!is_null($ob->folder_resource_type)) {
                $folder_resource_type = is_array($ob->folder_resource_type) ? $ob->folder_resource_type[0] : $ob->folder_resource_type;
                list(, $folder_resource_id) = $ob->folder_convert_filename_to_id('test-a', $folder_resource_type);
                $this->assertTrue($folder_resource_id !== null, 'Could not folder_convert_filename_to_id for ' . $folder_resource_type);
                if ($folder_resource_id !== null) {
                    $test = $ob->search($folder_resource_type, $folder_resource_id, true);
                    $this->assertTrue($test !== null, 'Could not search for ' . $folder_resource_type . ' test-a');
                }
            }

            $file_resource_type = is_array($ob->file_resource_type) ? $ob->file_resource_type[0] : $ob->file_resource_type;
            list(, $file_resource_id) = $ob->file_convert_filename_to_id($this->test_filename, $file_resource_type);
            $this->assertTrue($file_resource_id !== null, 'Could not file_convert_filename_to_id');
            if ($file_resource_id !== null) {
                $test = $ob->search($file_resource_type, $file_resource_id, true);
                $this->assertTrue($test !== null, 'Could not search for ' . $file_resource_type . ' ' . $this->test_filename);
                if (!is_null($test)) {
                    if (is_null($ob->folder_resource_type)) {
                        $this->assertTrue($test == '', 'Should have found in root, ' . $file_resource_type);
                    } else {
                        $this->assertTrue($test != '', 'Should not have found in root, ' . $file_resource_type);
                    }
                }
            }
        }
    }

    public function testFindByLabel()
    {
        foreach ($this->resource_fs_obs as $commandr_fs_hook => $ob) {
            if (!is_null($ob->folder_resource_type)) {
                $results = array();
                foreach (is_array($ob->folder_resource_type) ? $ob->folder_resource_type : array($ob->folder_resource_type) as $resource_type) {
                    $results = array_merge($results, $ob->find_resource_by_label($resource_type, 'test-a'));
                    $results = array_merge($results, $ob->find_resource_by_label($resource_type, 'test-b'));
                }
                $this->assertTrue(count($results) > 0, 'Failed to find_resource_by_label (folder) ' . $commandr_fs_hook);
            }
            $results = array();
            foreach (is_array($ob->file_resource_type) ? $ob->file_resource_type : array($ob->file_resource_type) as $resource_type) {
                $results = array_merge($results, $ob->find_resource_by_label($resource_type, $this->test_filename));
            }
            $this->assertTrue(count($results) > 0, 'Failed to find_resource_by_label (file) ' . $commandr_fs_hook);
        }
    }

    public function testLoad()
    {
        foreach ($this->resource_fs_obs as $commandr_fs_hook => $ob) {
            $path = $this->paths[$commandr_fs_hook];

            if ($path != '') {
                $result = $ob->folder_load(basename($path), dirname($path));
                $this->assertTrue($result !== false, 'Failed to folder_load ' . $commandr_fs_hook);
            }

            $result = $ob->file_load($this->test_filename . '.' . RESOURCE_FS_DEFAULT_EXTENSION, $path);
            $this->assertTrue($result !== false, 'Failed to file_load ' . $commandr_fs_hook . ' (' . $path . ')');
        }
    }

    public function testEdit()
    {
        foreach ($this->resource_fs_obs as $commandr_fs_hook => $ob) {
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
                    $this->assertTrue($result !== false, 'Failed to folder_load before folder_edit ' . $commandr_fs_hook);
                    if ($result !== false) {
                        $result = $ob->folder_edit(basename($_path), (strpos($_path, '/') === false) ? '' : dirname($_path), $result);
                        $this->assertTrue($result !== false, 'Failed to folder_edit ' . $commandr_fs_hook . ' (' . $_path . ')');
                    }
                }
            }

            $result = $ob->file_edit($this->test_filename . '.' . RESOURCE_FS_DEFAULT_EXTENSION, $path, array('label' => $this->test_filename));
            $this->assertTrue($result !== false, 'Failed to file_edit ' . $commandr_fs_hook . ' (' . $path . ')');
        }
    }

    public function testDelete()
    {
        foreach ($this->resource_fs_obs as $commandr_fs_hook => $ob) {
            $path = $this->paths[$commandr_fs_hook];

            $result = $ob->file_delete($this->test_filename . '.' . RESOURCE_FS_DEFAULT_EXTENSION, $path);
            $this->assertTrue($result !== false, 'Failed to file_delete ' . $commandr_fs_hook . ' (' . $path . ')');

            if ($path != '') {
                $result = $ob->folder_delete(basename($path), (strpos($path, '/') === false) ? '' : dirname($path));
                $this->assertTrue($result !== false, 'Failed to folder_delete ' . $commandr_fs_hook . ' (' . $path . ')');

                if (strpos($path, '/') !== false) {
                    $_path = dirname($path);
                    $result = $ob->folder_delete(basename($_path), (strpos($_path, '/') === false) ? '' : dirname($_path));
                    $this->assertTrue($result !== false, 'Failed to folder_delete ' . $commandr_fs_hook . ' (' . $_path . ')');
                }
            }
        }
    }
}
