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
class Hook_endpoint_cms_homesite_version
{
    /**
     * Return information about this endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return array Info about the hook
     */
    public function info(?string $type, ?string $id) : array
    {
        return [
            'authorization' => false,
        ];
    }

    /**
     * Run an API endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return array Data structure that will be converted to correct response type
     */
    public function run(?string $type, ?string $id) : array
    {
        if (!addon_installed('cms_homesite')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('cms_homesite')));
        }
        if (!addon_installed('downloads')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('downloads')));
        }
        if (!addon_installed('news')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('news')));
        }

        require_lang('cms_homesite');
        require_code('cms_homesite');
        require_code('version2');

        $output = '';
        if (get_param_integer('html', 0) == 1) {
            $output .= '<script ' . csp_nonce_html() . ' src="' . get_base_url() . '/themes/default/templates_cached/EN/javascript.js"></script>';
        }

        $version_dotted = get_param_string('version', $id);
        $version_pretty = get_version_pretty__from_dotted($version_dotted);
        list(, $qualifier, $qualifier_number, $long_dotted_number, , $long_dotted_number_with_qualifier) = get_version_components__from_dotted($version_dotted);

        // Work out upgrade paths
        $release_tree = get_release_tree('quick');
        $higher_versions = [null, null, null, null];
        $description = '';
        foreach ($release_tree as $other_version_dotted => $download_row) { // As $release_tree is sorted we will keep updating recommendations with newer, so we end with the newest on each level
            list(, $other_qualifier, $other_qualifier_number, $other_long_dotted_number, , $other_long_dotted_number_with_qualifier) = get_version_components__from_dotted($other_version_dotted);

            if (version_compare($long_dotted_number_with_qualifier, $other_long_dotted_number_with_qualifier, '>=')) {
                continue; // Disconsider because our branch is the same or older
            }

            // Ok it's newer...

            $differs_at = $this->find_biggest_branch_differ_position($long_dotted_number_with_qualifier, $other_long_dotted_number_with_qualifier);
            if ($differs_at === null) {
                fatal_exit(do_lang_tempcode('INTERNAL_ERROR'));
            }

            if (($other_qualifier !== null) && ($qualifier === null)) {
                continue; // It's an alpha or beta or RC and we are not
            }

            if (($other_qualifier !== null) && ($qualifier !== null) && ($differs_at != 3)) {
                continue; // It's an alpha or beta or RC and we are, but it's a different branch (assumption is user wants to upgrade to final version of current branch)
            }

            $other_version_pretty = get_version_pretty__from_dotted($other_version_dotted);

            $news_row = find_version_news($other_version_pretty);

            if ($news_row === null) {
                continue; // Somehow the news post is missing, we'll consider the release pulled
            }

            // We don't want changelogs
            $nice_description_parts = explode('---', $download_row['nice_description']);
            $nice_description = $nice_description_parts[0];

            // We chain all the download descriptions together; each says why the version involved is out of date, so together it is like a "why upgrade" history. The news posts on the other hand says what a new version itself offers.
            if (strlen($nice_description) < 3000) { // If not too long
                if ($description != '') {
                    $description = "\n---\n" . $description;
                }
                $description .= $this->strip_download_description($nice_description);
            }

            $higher_versions[$differs_at] = [
                'version_pretty' => $other_version_pretty,
                'version_dotted' => $other_version_dotted,
                'news_id' => $news_row['id'],
                'download_description' => $description,
                'add_date' => $download_row['add_date'],
            ];
        }
        $has_jump = ($higher_versions[0] !== null) || ($higher_versions[1] !== null) || ($higher_versions[2] !== null) || ($higher_versions[3] !== null);

        // Current version
        $our_version = null;
        $download_row = find_version_download($version_pretty, 'quick');

        // We don't want changelogs
        if ($download_row !== null) {
            $nice_description_parts = explode('---', $download_row['nice_description']);
            $nice_description = $nice_description_parts[0];

            $our_version = [
                'version_pretty' => $version_pretty,
                'version_dotted' => $version_dotted,
                'download_description' => $this->strip_download_description($nice_description),
                'add_date' => $download_row['add_date'],
            ];
        }
        $output .= '<h3 class="notes-about">Notes about your current version (' . escape_html($version_pretty) . ')</h3>';
        if ($our_version !== null) {
            if (!$has_jump) {
                $descrip = $our_version['download_description'] . ' You are running the latest version.';

                $tracker_url = get_base_url() . '/tracker/search.php?version=' . urlencode($version_dotted) . '&sort=last_updated%2Cid&dir=DESC%2CDESC';
                $descrip .= '<br /><br />See bug reports for <a target="_blank" title="Bug reports (this link will open in a new window)" href="' . escape_html($tracker_url) . '">' . escape_html($version_pretty) . '</a>.';
            } else {
                $descrip = 'You are <strong>not</strong> running the latest version. Browse the <a title="Composr news archive (this link will open in a new window)" target="_blank" href="' . escape_html(static_evaluate_tempcode(build_url(['page' => 'news'], get_module_zone('news')))) . '">Composr news archive</a> for a full list of the updates or see below for recommended paths.';
            }
            $output .= '<p>' . $descrip . '</p>';
        } else {
            $output .= '<p>This version does not exist in our database. This means it is either very new, or unsupported (or we have made a mistake - in which case, please contact us).</p>';
        }

        // Latest versions
        if ($has_jump) {
            $output .= '<h3>Latest recommended upgrade paths</h3>';

            $upgrade_type = ['major upgrade, may break compatibility of customisations', 'feature upgrade', 'easy patch upgrade'];
            for ($i = 0; $i <= 3; $i++) {
                if ($higher_versions[$i] !== null) {
                    $this->display_version_upgrade_path($output, $higher_versions[$i]);
                }
            }
        }

        if ($id === '_LEGACY_') { // LEGACY
            echo $output;
            exit;
        }

        return [
            'HTML' => $output,
        ];
    }

    protected function find_biggest_branch_differ_position($long_dotted_number_with_qualifier, $other_long_dotted_number_with_qualifier)
    {
        $parts = explode('.', $long_dotted_number_with_qualifier);
        $other_parts = explode('.', $other_long_dotted_number_with_qualifier);

        // Add in last component if one has it but the other does not (this is the qualifier component, i.e. alpha/beta/RC)
        if (count($parts) > count($other_parts)) {
            $other_parts[] = '0';
        } elseif (count($other_parts) > count($parts)) {
            $parts[] = '0';
        }

        foreach ($parts as $i => $part) {
            if ($other_parts[$i] != $part) {
                return $i; // 0|1|2|3
            }
        }

        return null;
    }

    protected function strip_download_description($d)
    {
        return static_evaluate_tempcode(comcode_to_tempcode(preg_replace('#A new version, [\.\d\w]+ is available\. #', '', preg_replace('# There may have been other upgrades since .* - see .+\.#', '', $d))));
    }

    protected function display_version_upgrade_path(&$output, $higher_version)
    {
        $version_dotted = get_param_string('version');

        static $i = 0;
        $i++;

        $note = '&ndash; ' . $higher_version['download_description'] . '<br />';

        if (is_release_discontinued($higher_version['version_dotted'])) {
            list(, , , , $general_number) = get_version_components__from_dotted($higher_version['version_dotted']);
            $note .= ' &ndash; <em>Note that the ' . get_version_branch($general_number) . ' version line is no longer supported</em>';
        }

        $upgrade_url = static_evaluate_tempcode(build_url(['page' => 'news', 'type' => 'view', 'id' => $higher_version['news_id'], 'from_version' => $version_dotted, 'wide_high' => 1], get_module_zone('news')));

        $upgrade_script = 'upgrader.php';
        if (isset($higher_version['news_id'])) {
            $upgrade_script .= '?news_id=' . strval($higher_version['news_id']) . '&from_version=' . urlencode($version_dotted);
        }

        $version_number = escape_html($higher_version['version_pretty']);
        $version_released = display_time_period(time() - $higher_version['add_date']);
        $_upgrade_script = escape_html($upgrade_script);
        $_upgrade_url = escape_html($upgrade_url);
        $link_pos_id = strval($i);
        $upgrade_icon = do_template('ICON', ['_GUID' => '083acd2905f7296c7a41e0db83e19cef', 'NAME' => 'menu/adminzone/tools/upgrade'])->evaluate();
        $output .= <<<HTML
            <div class="version vertical-alignment">
                <!-- Version number and notes -->
                <span class="version-number">$version_number</span> (released $version_released ago)
                <span class="version-note">$note</span>
                <!-- Output upgrader link -->
                <form style="display: inline" action="../$_upgrade_script" target="_blank" method="post">
                <span class="version-button" id="link-pos-$link_pos_id">
                    <button class="btn btn-primary btn-scri menu--adminzone--tools--upgrade" type="submit" title="Upgrade to $version_number">$upgrade_icon Launch upgrader</button>
                </span>
                </form>
                <!-- Version News link -->
                <span class="version-news-link">[ <a onclick="window.open(this.href,null,'status=yes,toolbar=no,location=no,menubar=no,resizable=yes,scrollbars=yes,width=976,height=600'); return false;" target="_blank" title="$version_number news post (this link will open in a new window)" href="$_upgrade_url">view news post</a> ]</span>
            </div>
        HTML;
    }
}
