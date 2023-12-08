<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class _commandr_fs_test_set extends cms_test_case
{
    public function setUp()
    {
        $GLOBALS['NO_QUERY_LIMIT'] = true;

        require_code('commandr_fs');
        require_code('resource_fs');

        disable_php_memory_limit();

        parent::setUp();
    }

    public function testVar()
    {
        $ob = new Commandr_fs();

        // Check top-level 'var' works
        $var_files = $ob->listing(array('var'));
        $cnt = 0;
        $commandr_fs_hooks = find_all_hooks('systems', 'commandr_fs');
        foreach ($commandr_fs_hooks as $commandr_fs_hook => $dir) {
            $_path = get_file_base() . '/' . $dir . '/hooks/systems/commandr_fs/' . $commandr_fs_hook . '.php';
            $contents = file_get_contents($_path);
            if (strpos($contents, ' extends Resource_fs_base') !== false) {
                $cnt++;
            }
        }
        $this->assertTrue(count($var_files[0]) == $cnt, 'Not all var filesystems showing up');

        // Check one of the repository-FS filesystems works
        $files = $ob->listing(array('var', 'banners'));
        $this->assertTrue(count($files[0]) != 0, 'Missing banner types in file system');
        $files = $ob->listing(array('var', 'banners', 'untitled'));
        $this->assertTrue(count($files[0]) == 0, 'Unexpected subdirectory under banner type');
        $this->assertTrue(count($files[1]) != 0, 'Missing default banners under banner type');
        $path = array('var', 'banners', 'untitled', 'advertise_here.' . RESOURCE_FS_DEFAULT_EXTENSION);
        $GLOBALS['SITE_DB']->query_update('banners', array('edit_date' => null));
        $data1 = $ob->read_file($path);
        $ob->write_file($path, $data1);
        $GLOBALS['SITE_DB']->query_update('banners', array('edit_date' => null));
        $data2 = $ob->read_file($path);
        $this->assertTrue($data1 == $data2, 'Inconsistent banner read/write');

        // Check folder property editing works
        $path = array('var', 'banners', 'untitled', '_folder.' . RESOURCE_FS_DEFAULT_EXTENSION);
        $data1 = $ob->read_file($path);
        $ob->write_file($path, $data1);
        $data2 = $ob->read_file($path);
        $this->assertTrue($data1 == $data2, 'Inconsistent banner type read/write');
    }

    public function testVarPorting()
    {
        // Test exporting something
        $out = remap_resource_id_as_portable('group', '1');
        $this->assertTrue($out['label'] == 'Guests', 'Failed reading guest usergroup label');
        $this->assertTrue($out['subpath'] == '', 'Failed reading guest usergroup subpath');
        $this->assertTrue($out['id'] == db_get_first_id(), 'Failed reading guest usergroup ID');

        // Test importing to something - binding to something that exists
        $in = remap_portable_as_resource_id('group', $out);
        $this->assertTrue(intval($in) == db_get_first_id(), 'Portable ID remapping cycle broken');

        // Test importing to something - something that does not exist
        $ob = get_resource_commandr_fs_object('download');
        $port = array(
            'guid' => 'a-b-c-d-e-f',
            'label' => 'My Test Download',
            'subpath' => 'Downloads home/Some Deep/Path',
        );
        $in = remap_portable_as_resource_id('download', $port);
        $guid = find_guid_via_id('download', $in);
        $filename = $ob->convert_id_to_filename('download', $in);
        $this->assertTrue($guid == $port['guid'], 'Download GUID not holding'); // Tests it imported with the same GUID
        $subpath = $ob->search('download', $in, true);
        $this->assertTrue(strpos($subpath, '/') !== false, 'Download subpath lost'); // Test it imported with a deep path

        // Tidy up, delete it
        $ob->file_delete($filename, $subpath);
    }

    public function testFullVarCoverage()
    {
        $cma_hooks = find_all_hooks('systems', 'content_meta_aware') + find_all_hooks('systems', 'resource_meta_aware');
        $commandr_fs_hooks = find_all_hooks('systems', 'commandr_fs');

        $referenced_in_cma = array();

        foreach (array_keys($cma_hooks) as $cma_hook) {
            $ob = get_content_object($cma_hook);
            $info = $ob->info();
            if (!is_null($info)) {
                $fs_hook = $info['commandr_filesystem_hook'];
                if (!is_null($fs_hook)) {
                    $this->assertTrue(array_key_exists($fs_hook, $commandr_fs_hooks), 'Commandr-FS hook with broken Resource-FS reference: ' . $fs_hook);
                    $referenced_in_cma[$fs_hook] = true;
                }
            }
        }

        foreach ($commandr_fs_hooks as $commandr_fs_hook => $dir) {
            $path = get_file_base() . '/' . $dir . '/hooks/systems/commandr_fs/' . $commandr_fs_hook . '.php';
            $contents = file_get_contents($path);
            if (strpos($contents, ' extends Resource_fs_base') !== false) {
                $this->assertTrue(array_key_exists($commandr_fs_hook, $referenced_in_cma), 'Resource-FS hook not referenced: ' . $commandr_fs_hook);
            }
        }
    }

    // This test will test the commandr_fs_extended_config hooks are working properly, as well as the config option read/write in general.
    public function testEtcDir()
    {
        $ob = new Commandr_fs();
        $files = $ob->listing(array('etc'));
        foreach ($files[1] as $file) {
            if (strpos($file[0], '.' . RESOURCE_FS_DEFAULT_EXTENSION) !== false) {
                $path = array('etc', $file[0]);
                $data1 = $ob->read_file($path);
                $ob->write_file($path, $data1);
                $data2 = $ob->read_file($path);
                $this->assertTrue($data1 == $data2, 'Failed writing to /etc/' . $file[0]);
            }
        }
    }
}
