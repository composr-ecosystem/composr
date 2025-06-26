<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_tutorials
 */

/**
 * Module page class.
 */
class Module_tutorials
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'Composr';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'composr_tutorials';
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $tables = [
            'tutorials_external',
            'tutorials_external_tags',
            'tutorials_tags',
            'tutorials_internal',
        ];
        $GLOBALS['SITE_DB']->drop_table_if_exists($tables);
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install(?int $upgrade_from = null, ?int $upgrade_from_hack = null)
    {
        $GLOBALS['SITE_DB']->create_table('tutorials_external', [
            'id' => '*AUTO',
            't_url' => 'URLPATH',
            't_title' => 'SHORT_TEXT',
            't_summary' => 'LONG_TEXT',
            't_icon' => 'ID_TEXT',
            't_media_type' => 'ID_TEXT', // document|video|audio|slideshow|book
            't_difficulty_level' => 'ID_TEXT', // novice|regular|expert
            't_pinned' => 'BINARY',
            't_author' => 'ID_TEXT',
            't_submitter' => 'MEMBER',
            't_views' => 'INTEGER',
            't_add_date' => 'TIME',
            't_edit_date' => 'TIME',
        ]);

        $GLOBALS['SITE_DB']->create_index('tutorials_external', '#t_title', ['t_title']);
        $GLOBALS['SITE_DB']->create_index('tutorials_external', '#t_summary', ['t_summary']);

        $GLOBALS['SITE_DB']->create_table('tutorials_external_tags', [
            't_id' => '*AUTO_LINK',
            't_tag' => '*ID_TEXT',
        ]);

        $GLOBALS['SITE_DB']->create_table('tutorials_internal', [
            't_page_name' => '*ID_TEXT',
            't_views' => 'INTEGER',
        ]);

        // TODO: Insert default external tutorials
        $external_tutorials = [];

        foreach ($external_tutorials as $external_tutorial) {
            $id = $GLOBALS['SITE_DB']->query_insert('tutorials_external', [
                't_url' => $external_tutorial['url'],
                't_title' => $external_tutorial['title'],
                't_summary' => $external_tutorial['summary'],
                't_icon' => $external_tutorial['icon'],
                't_media_type' => $external_tutorial['media_type'],
                't_difficulty_level' => $external_tutorial['difficulty_level'],
                't_pinned' => 0,
                't_author' => $external_tutorial['author'],
                't_submitter' => $GLOBALS['FORUM_DRIVER']->get_guest_id(),
                't_views' => 0,
                't_add_date' => time() - 60 * 60 * 24 * 365,
                't_edit_date' => time() - 60 * 60 * 24 * 365,
            ], true);

            foreach ($external_tutorial['tags'] as $tag) {
                $GLOBALS['SITE_DB']->query_insert('tutorials_external_tags', [
                    't_id' => $id,
                    't_tag' => $tag,
                ]);
            }
        }
    }

    /**
     * Find entry-points available within this module.
     *
     * @param  boolean $check_perms Whether to check permissions
     * @param  ?MEMBER $member_id The member to check permissions as (null: current user)
     * @param  boolean $support_crosslinks Whether to allow cross links to other modules (identifiable via a full-page-link rather than a screen-name)
     * @param  boolean $be_deferential Whether to avoid any entry-point (or even return null to disable the page in the Sitemap) if we know another module, or page_group, is going to link to that entry-point. Note that "!" and "browse" entry points are automatically merged with container page nodes (likely called by page-groupings) as appropriate.
     * @return ?array A map of entry points (screen-name=>language-code/string or screen-name=>[language-code/string, icon-theme-image]) (null: disabled)
     */
    public function get_entry_points(bool $check_perms = true, ?int $member_id = null, bool $support_crosslinks = true, bool $be_deferential = false) : ?array
    {
        if (!addon_installed('composr_tutorials')) {
            return null;
        }

        return [
            'browse' => ['tutorials:TUTORIALS', 'help'],
        ];
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution
     */
    public function run() : object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('composr_tutorials', $error_msg)) {
            return $error_msg;
        }

        if (!addon_installed('cms_homesite')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('cms_homesite')));
        }
        if (!addon_installed('cms_release_build')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('cms_release_build')));
        }

        require_code('tutorials');
        require_css('tutorials');

        $type = get_param_string('type', 'browse');
        if ($type == 'view') {
            return $this->view();
        }

        return $this->browse();
    }

    /**
     * Browse tutorials.
     *
     * @return Tempcode The result of execution
     */
    public function browse() : object
    {
        $title = get_screen_title(protect_from_escaping('Tutorials &ndash; Learning Composr'), false);

        $tag = get_param_string('type', 'Installation', INPUT_FILTER_GET_COMPLEX); // $type, essentially

        $tags = list_tutorial_tags(true);

        $tutorials = list_tutorials_by('likes', ($tag == '') ? null : $tag);
        $_tutorials = templatify_tutorial_list($tutorials);

        return do_template('TUTORIAL_INDEX_SCREEN', [
            '_GUID' => '4569ab28e8959d9556dbb6d73c0e834a',
            'TITLE' => $title,
            'TAGS' => $tags,
            'SELECTED_TAG' => $tag,
            'TUTORIALS' => $_tutorials,
        ]);
    }

    /**
     * Redirect to external tutorial.
     *
     * @return Tempcode The result of execution
     */
    public function view() : object
    {
        $id = get_param_integer('id');

        $tutorial_rows = $GLOBALS['SITE_DB']->query_select('tutorials_external', ['*'], ['id' => $id], '', 1);
        if (!array_key_exists(0, $tutorial_rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'tutorial'));
        }

        $tutorial_row = $tutorial_rows[0];

        $title = get_screen_title(protect_from_escaping($tutorial_row['t_title']), false);

        $url = $tutorial_row['t_url'];
        return redirect_screen($title, $url, do_lang_tempcode('SUCCESS'));
    }
}
