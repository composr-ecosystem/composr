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
class Hook_endpoint_cms_homesite_release_details
{
    /**
     * Return information about this endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return ?array Info about the hook (null: endpoint is disabled)
     */
    public function info(?string $type, ?string $id) : ?array
    {
        if (!addon_installed('cms_homesite')) {
            return null;
        }
        if (!addon_installed('news')) {
            return null;
        }

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
        // LEGACY
        $legacy = false;
        if ($id === '_LEGACY_') {
            $legacy = true;
        }

        $id = get_param_string('news_id', $id);

        if (!is_numeric($id)) {
            if ($legacy) { // LEGACY
                echo json_encode(['', '', '']);
                exit();
            }

            return [
                'success' => false,
                'error_details' => 'You must provide the ID of the release news article.'
            ];
        }

        $news_rows = $GLOBALS['SITE_DB']->query_select('news', ['*'], ['validated' => 1, 'id' => $id], '', 1);
        if ((array_key_exists(0, $news_rows)) && (has_category_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'news', $news_rows[0]['news_category']))) {
            $_news_html = get_translated_tempcode('news', $news_rows[0], 'news_article'); // To force it to evaluate, so we can know the TAR URL
            $news_html = $_news_html->evaluate();
            $news = static_evaluate_tempcode(comcode_to_tempcode(get_translated_text($news_rows[0]['news_article']), null, true));

            $matches = [];
            preg_match('#"(https?://composr.app/uploads/website_specific/cms_homesite/upgrades/tars/[^"]*\.cms)"#', $news_html, $matches);
            $tar_url = array_key_exists(1, $matches) ? $matches[1] : '';
            $changes = '';
            if (preg_match('#<br />([^>]*the following.*:<br /><ul>)#U', $news_html, $matches) != 0) {
                $offset = strpos($news_html, $matches[1]);
                $changes = substr($news_html, $offset, strrpos($news_html, '</ul>') - $offset + 5);
                $news_html = substr($news_html, 0, $offset);
            }
            $news_html = preg_replace('#To upgrade follow.*during step 3.#s', '', $news_html);
            $news_html = preg_replace('#(<div[^>]*>[\s\n]*)+<h4[^>]*>Your upgrade to version.*</form>([\s\n]*</div>)+#s', '', $news_html);
            $news_html = preg_replace('#(<div[^>]*>[\s\n]*)+<h4[^>]*>Your upgrade to version.*download upgrade directly</a>\s+\([^\)]*\)\.([\s\n]*</div>)+#s', '', $news_html);
            $news_html = preg_replace('#<a class="hide_button" href="\#!" onclick="hideTag\(this\.parentNode\.parentNode\); return false;"><img alt="Expand" title="Expand" src="' . escape_html(find_theme_image('icons/trays/expand')) . '" /></a>#', '', $news_html);
            $news_html = preg_replace('#(\s*<br />)+#', '<br />', $news_html);
            $news_html = str_replace('display: none', 'display: block', $news_html);
            $notes = $news_html;

            if ($legacy) { // LEGACY
                echo json_encode([$notes, $tar_url, $changes]);
                exit();
            }

            return [
                'notes' => $notes,
                'tar_url' => $tar_url,
                'changes' => $changes
            ];
        }

        if ($legacy) { // LEGACY
            echo json_encode(['', '', '']);
            exit();
        }

        return [
            'success' => false,
            'error_details' => 'The provided release news ID was not found.'
        ];
    }
}
