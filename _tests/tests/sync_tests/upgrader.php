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
 * Composr test case class (unit testing).
 */
class upgrader_test_set extends cms_test_case
{
    protected $from_version = '13.1';
    protected $to_version = '14';
    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLUGGISH);

        // Clean up old test upgrades
        require_code('files');
        foreach (['full', 'tar_build'] as $subdir) {
            deldir_contents(get_file_base() . 'uploads/website_specific/cms_homesite/upgrades/' . $subdir . '/' . $this->from_version, false, true);
            deldir_contents(get_file_base() . 'uploads/website_specific/cms_homesite/upgrades/' . $subdir . '/' . $this->to_version, false, true);
        }

        // Also delete generated upgrades so we can re-generate them
        deldir_contents(get_file_base() . 'uploads/website_specific/cms_homesite/upgrades/tars', true, false);
    }

    public function testFileManifest()
    {
        require_code('failure');
        require_code('global3');

        set_throw_errors(true);

        try {
            $files_previous_path = get_file_base() . '/data/files_previous.bin';
            $file_data = cms_file_get_contents_safe($files_previous_path, FILE_READ_LOCK);
            if ($file_data !== null) {
                $files_previous = unserialize($file_data);
                if (cms_empty_safe($files_previous)) {
                    throw new CMSException('Invalid files_previous');
                }
            } else {
                throw new CMSException('Invalid files_previous');
            }
        } catch (Exception $e) {
            $this->assertTrue(false, 'data/files_previous.bin is missing or corrupt. Grab the files_previous file from the previous major/minor release.');
        }

        try {
            $files_path = get_file_base() . '/data/files.bin';
            $file_data = cms_file_get_contents_safe($files_path, FILE_READ_LOCK);
            if ($file_data !== null) {
                $files = unserialize($file_data);
                if (cms_empty_safe($files)) {
                    throw new CMSException('Invalid files');
                }
            } else {
                throw new CMSException('Invalid files');
            }
        } catch (Exception $e) {
            $this->assertTrue(false, 'data/files.bin is missing or corrupt. You may need to generate / make this release again.');
        }

        set_throw_errors(false);
    }

    public function testMakePersonalUpgraderNoAddonsDefined()
    {
        if (!addon_installed('cms_homesite')) {
            $this->assertTrue(false, 'This test requires the cms_homesite addon.');
            return;
        }
        if (!addon_installed('downloads')) {
            $this->assertTrue(false, 'This test requires the downloads addon.');
            return;
        }
        if (!addon_installed('news')) {
            $this->assertTrue(false, 'This test requires the news addon.');
            return;
        }

        require_code('version2');
        require_code('cms_homesite');
        require_code('cms_homesite_make_upgrader');

        $_GET['test_mode'] = '1';

        list($tar_path, $err) = make_upgrade_get_path($this->from_version, $this->to_version);
        $this->assertTrue(($tar_path !== null), 'Failed to make a personal upgrader: ' . $err);
    }

    public function testMakePersonalUpgraderAddonsDefined()
    {
        if (!addon_installed('cms_homesite')) {
            $this->assertTrue(false, 'This test requires the cms_homesite addon.');
            return;
        }
        if (!addon_installed('downloads')) {
            $this->assertTrue(false, 'This test requires the downloads addon.');
            return;
        }
        if (!addon_installed('news')) {
            $this->assertTrue(false, 'This test requires the news addon.');
            return;
        }

        require_code('version2');
        require_code('cms_homesite');
        require_code('cms_homesite_make_upgrader');

        $addons = [ // A good test base; we have two core addons and one non-bundled addon. Note these addon_registry hooks are in the sample data.
            'cms_homesite' => true,
            'downloads' => true,
            'news' => true,
        ];

        $_GET['test_mode'] = '1';

        list($tar_path, $err) = make_upgrade_get_path($this->from_version, $this->to_version, $addons);
        $this->assertTrue(($tar_path !== null), 'Failed to make a personal upgrader: ' . $err);
    }
}
