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

/**
 * Composr test case class (unit testing).
 */
class _commandr_fs_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        cms_disable_time_limit();

        push_query_limiting(false);

        require_code('commandr_fs');
        require_code('resource_fs');

        disable_php_memory_limit();
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
            $c = cms_file_get_contents_safe($_path);
            if (strpos($c, ' extends Resource_fs_base') !== false) {
                if (get_forum_type() != 'cns') {
                    if (in_array($commandr_fs_hook, array('forums', 'groups'))) {
                        continue;
                    }
                }

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
        $resource_id_in = strval(db_get_first_id());
        $port_out = remap_resource_id_as_portable('ticket_type', $resource_id_in);
        $this->assertTrue($port_out['label'] == 'Other', 'Failed reading Other ticket type label');
        $this->assertTrue($port_out['subpath'] == '', 'Failed reading Other ticket type subpath');
        $this->assertTrue($port_out['id'] == db_get_first_id(), 'Failed reading Other ticket type ID');

        // Test importing to something - binding to something that exists
        $resource_id_out = remap_portable_as_resource_id('ticket_type', $port_out);
        $this->assertTrue($resource_id_out == $resource_id_in, 'Portable ID remapping cycle broken');

        // Test importing to something - something that does not exist
        $ob = get_resource_commandr_fs_object('download');
        $port_in = array(
            'guid' => 'a-b-c-d-e-f',
            'label' => 'My Test Download',
            'subpath' => 'downloads-home/some-deep/path',
        );
        $resource_id_out = remap_portable_as_resource_id('download', $port_in); // Will create a new empty resource as it is missing
        $guid = find_guid_via_id('download', $resource_id_out);
        $filename = $ob->convert_id_to_filename('download', $resource_id_out);
        $this->assertTrue($guid == $port_in['guid'], 'Download GUID not holding'); // Tests it imported with the same GUID
        $subpath = $ob->search('download', $resource_id_out, true);
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
            if ($info !== null) {
                $fs_hook = $info['commandr_filesystem_hook'];
                if ($fs_hook !== null) {
                    $this->assertTrue(array_key_exists($fs_hook, $commandr_fs_hooks), 'Commandr-fs hook with broken Resource-fs reference: ' . $fs_hook);
                    $referenced_in_cma[$fs_hook] = true;
                }
            }
        }

        foreach ($commandr_fs_hooks as $commandr_fs_hook => $dir) {
            if (get_forum_type() != 'cns') {
                if (in_array($commandr_fs_hook, array('forums', 'groups'))) {
                    continue;
                }
            }

            $path = get_file_base() . '/' . $dir . '/hooks/systems/commandr_fs/' . $commandr_fs_hook . '.php';
            $c = cms_file_get_contents_safe($path);
            if (strpos($c, ' extends Resource_fs_base') !== false) {
                $this->assertTrue(array_key_exists($commandr_fs_hook, $referenced_in_cma), 'Resource-fs hook not referenced: ' . $commandr_fs_hook);
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

                $ok = ($data1 == $data2);

                if ($this->debug) {
                    if (!$ok) {
                        @var_dump($data1);
                        @var_dump($data2);
                    }
                }

                $this->assertTrue($ok, 'Failed writing to /etc/' . $file[0]);
            }
        }
    }
}
