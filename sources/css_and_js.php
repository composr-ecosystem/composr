<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core
 */

/**
 * Standard code module initialisation function.
 *
 * @ignore
 */
function init__css_and_js()
{
    global $CSS_COMPILE_ACTIVE_THEME;
    $CSS_COMPILE_ACTIVE_THEME = 'default';
}

/**
 * Inherit from a CSS file to create a string for a (possibly theme-gen) modified version of that file.
 *
 * @param  ID_TEXT $css_file Source CSS file
 * @param  ID_TEXT $theme Source theme
 * @param  ID_TEXT $destination_theme Destination theme
 * @param  ?ID_TEXT $seed Seed (null: do not re-seed)
 * @param  boolean $dark Whether it is a dark theme
 * @param  ID_TEXT $algorithm The algorithm to use
 * @set equations hsv
 * @return string The sheet
 */
function css_inherit($css_file, $theme, $destination_theme, $seed, $dark, $algorithm)
{
    // Find source
    $full_path = get_custom_file_base() . '/themes/' . $theme . '/css_custom/' . $css_file . '.css';
    if (!is_file($full_path)) {
        $full_path = get_custom_file_base() . '/themes/' . $theme . '/css/' . $css_file . '.css';
        if (!is_null($GLOBALS['CURRENT_SHARE_USER'])) {
            $full_path = get_file_base() . '/themes/' . $theme . '/css_custom/' . $css_file . '.css';
            if (!is_file($full_path)) {
                $full_path = get_file_base() . '/themes/' . $theme . '/css/' . $css_file . '.css';
            }
        }
        if (!is_file($full_path)) {
            $theme = 'default';
            $full_path = get_file_base() . '/themes/' . $theme . '/css_custom/' . $css_file . '.css';
            if (!is_file($full_path)) {
                $full_path = get_file_base() . '/themes/' . $theme . '/css/' . $css_file . '.css';
            }
        }
    }

    // Read a raw
    $sheet = cms_file_get_contents_safe($full_path);

    // Re-seed
    if (addon_installed('themewizard')) {
        if (!is_null($seed)) {
            // Not actually needed
            $sheet = preg_replace('#\{\$THEME_WIZARD_COLOR,\#[A-Fa-f0-9]{6},seed,100% [A-Fa-f0-9]{6}\}#', '{$THEME_WIZARD_COLOR,#' . $seed . ',seed,100% ' . $seed . '}', $sheet);
            $sheet = preg_replace('#\{\$THEME_WIZARD_COLOR,\#[A-Fa-f0-9]{6},WB,100% [A-Fa-f0-9]{6}\}#', '{$THEME_WIZARD_COLOR,#' . $seed . ',WB,100% ' . ($dark ? '000000' : 'FFFFFF') . '}', $sheet);
            $sheet = preg_replace('#\{\$THEME_WIZARD_COLOR,\#[A-Fa-f0-9]{6},BW,100% [A-Fa-f0-9]{6}\}#', '{$THEME_WIZARD_COLOR,#' . $seed . ',BW,100% ' . ($dark ? 'FFFFFF' : '000000') . '}', $sheet);

            require_code('themewizard');
            list($colours, $landscape) = calculate_theme($seed, $theme, $algorithm, 'colours', $dark);

            // The main thing (THEME_WIZARD_COLOR is not executed in full by Tempcode, so we need to sub it according to our theme wizard landscape)
            foreach ($landscape as $peak) {
                $from = $peak[2];
                $to = preg_replace('#\{\$THEME_WIZARD_COLOR,\#[\da-fA-F]{6},#', '{$THEME_WIZARD_COLOR,#' . $peak[3] . ',', $peak[2]);
                $sheet = str_replace($from, $to, $sheet);
            }
        }
    }

    // Copy to tmp file
    $tmp_filename = $css_file . '__tmp_copy_' . uniqid('', true);
    $temp_file = get_custom_file_base() . '/themes/' . $destination_theme . '/css_custom/' . $tmp_filename . '.css';
    $myfile = @fopen($temp_file, GOOGLE_APPENGINE ? 'wb' : 'at') or intelligent_write_error($temp_file);
    flock($myfile, LOCK_EX);
    if (!GOOGLE_APPENGINE) {
        ftruncate($myfile, 0);
    }
    fwrite($myfile, $sheet);

    // Load up as Tempcode
    $_sheet = _css_compile($destination_theme, $destination_theme, $tmp_filename, $temp_file, false);
    flock($myfile, LOCK_UN);
    fclose($myfile);
    fix_permissions($temp_file);
    @unlink($temp_file);
    $sheet = $_sheet[1];

    return $sheet;
}

/**
 * Compile a JavaScript file.
 *
 * @param  ID_TEXT $j Name of the JS file
 * @param  PATH $js_cache_path Full path to the JS file
 * @param  boolean $minify Whether to also do minification
 */
function js_compile($j, $js_cache_path, $minify = true)
{
    if (php_function_allowed('set_time_limit')) {
        if (ini_get('max_execution_time') != '600') { // TODO: Improve in #3634
            @set_time_limit(30);
        }
    }

    cms_profile_start_for('js_compile file ' . $j);

    global $KEEP_MARKERS, $SHOW_EDIT_LINKS;
    $temp_keep_markers = $KEEP_MARKERS;
    $temp_show_edit_links = $SHOW_EDIT_LINKS;
    $KEEP_MARKERS = false;
    $SHOW_EDIT_LINKS = false;
    $tpl_params = array();
    if ($j == 'staff') {
        $url_patterns = array();
        $cma_hooks = find_all_hooks('systems', 'content_meta_aware');
        foreach (array_keys($cma_hooks) as $content_type) {
            require_code('content');
            $content_type_ob = get_content_object($content_type);
            if ($content_type_ob !== null) {
                $info = $content_type_ob->info();
                if (isset($info['view_page_link_pattern'])) {
                    list($zone, $attributes,) = page_link_decode($info['view_page_link_pattern']);
                    $url = build_url($attributes, $zone, null, false, false, true);
                    $url_patterns[$url->evaluate()] = array(
                        'PATTERN' => $url->evaluate(),
                        'HOOK' => $content_type,
                    );
                }
                if (isset($info['edit_page_link_pattern'])) {
                    list($zone, $attributes,) = page_link_decode($info['edit_page_link_pattern']);
                    $url = build_url($attributes, $zone, null, false, false, true);
                    $url_patterns[$url->evaluate()] = array(
                        'PATTERN' => $url->evaluate(),
                        'HOOK' => $content_type,
                    );
                }
            }
        }
        $tpl_params['URL_PATTERNS'] = array_values($url_patterns);
    }
    require_code('tempcode');
    cms_profile_start_for('js_compile: do_template');
    $js = do_template($j, $tpl_params, null, false, null, '.js', 'javascript');
    cms_profile_end_for('js_compile: do_template');

    $KEEP_MARKERS = $temp_keep_markers;
    $SHOW_EDIT_LINKS = $temp_show_edit_links;
    global $ATTACHED_MESSAGES_RAW;
    $num_msgs_before = count($ATTACHED_MESSAGES_RAW);

    cms_profile_start_for('js_compile: do_template->evaluate');
    $out = $js->evaluate();
    cms_profile_end_for('js_compile: do_template->evaluate');

    $num_msgs_after = count($ATTACHED_MESSAGES_RAW);
    $success_status = ($num_msgs_before == $num_msgs_after);
    if ($minify) {
        cms_profile_start_for('js_compile: js_minify');
        $out = js_minify($out);
        cms_profile_end_for('js_compile: js_minify');
    }

    if (($out == '') || ($minify)) {
        $contents = $out;
    } else {
        $contents = '/* DO NOT EDIT. THIS IS A CACHE FILE AND WILL GET OVERWRITTEN RANDOMLY.' . "\n" . 'INSTEAD EDIT THE TEMPLATE FROM WITHIN THE ADMIN ZONE, OR BY MANUALLY EDITING A JAVASCRIPT_CUSTOM OVERRIDE. */' . "\n\n" . $out;
    }
    require_code('files');
    $success_status = cms_file_put_contents_safe($js_cache_path, $contents, FILE_WRITE_FAILURE_SILENT | FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
    if (!$success_status) {
        @touch($js_cache_path, time() - 60 * 60 * 24); // Fudge it so it's going to auto expire. We do have to write the file as it's referenced, but we want it to expire instantly so that any errors will reshow.
    } else {
        cms_profile_start_for('js_compile: compress_cms_stub_file');
        compress_cms_stub_file($js_cache_path);
        cms_profile_end_for('js_compile: compress_cms_stub_file');
    }

    cms_profile_end_for('js_compile file ' . $j);
}

/**
 * Compress a file, and save with a stem of .gz.
 *
 * @param  PATH $stub_file Full path to the file to compress
 */
function compress_cms_stub_file($stub_file)
{
    if (function_exists('gzencode')) {
        $data = @cms_file_get_contents_safe($stub_file);

        if ($data === false) {
            return;
        }

        require_code('files');
        cms_file_put_contents_safe($stub_file . '.gz', gzencode($data, 9), FILE_WRITE_FAILURE_SILENT | FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
    }
}

/**
 * Compile a CSS file.
 *
 * @param  ID_TEXT $active_theme The theme the file is being loaded for
 * @param  ID_TEXT $theme The theme the file is in
 * @param  ID_TEXT $c Name of the CSS file
 * @param  PATH $full_path Full path to the CSS file
 * @param  PATH $css_cache_path Full path to where the cached CSS file will go
 * @param  boolean $minify Whether to also do minification
 */
function css_compile($active_theme, $theme, $c, $full_path, $css_cache_path, $minify = true)
{
    cms_profile_start_for('css_compile file ' . $c);

    if ($c != 'global') { // We need to make sure the global.css file is parsed, as it contains some shared THEME_WIZARD_COLOR variables that Tempcode will pick up on
        $found = find_template_place('global', '', $active_theme, '.css', 'css');
        $d_theme = $found[0];
        $global_full_path = get_custom_file_base() . '/themes/' . $d_theme . $found[1] . 'global' . $found[2];
        if (!is_file($global_full_path)) {
            $global_full_path = get_file_base() . '/themes/' . $d_theme . $found[1] . 'global' . $found[2];
        }

        if (strpos(cms_file_get_contents_safe($global_full_path), '{$THEME_WIZARD_COLOR,') !== false) {
            require_code('tempcode_compiler');
            cms_profile_start_for('css_compile: template_to_tempcode');
            $temp = template_to_tempcode(cms_file_get_contents_safe($global_full_path), 0, false, $c, $active_theme, user_lang());
            $temp->evaluate(); // We just need it to evaluate, not do anything with it
            cms_profile_end_for('css_compile: template_to_tempcode');
        }
    }

    cms_profile_start_for('css_compile: _css_compile');
    list($success_status, $out) = _css_compile($active_theme, $theme, $c, $full_path, $minify);
    cms_profile_end_for('css_compile: _css_compile');

    require_code('files');
    $success_status = cms_file_put_contents_safe($css_cache_path, $out, FILE_WRITE_FAILURE_SILENT | FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
    if (!$success_status) {
        @touch($css_cache_path, time() - 60 * 60 * 24); // Fudge it so it's going to auto expire. We do have to write the file as it's referenced, but we want it to expire instantly so that any errors will reshow.
    } else {
        cms_profile_start_for('css_compile: compress_cms_stub_file');
        compress_cms_stub_file($css_cache_path);
        cms_profile_end_for('css_compile: compress_cms_stub_file');
    }

    cms_profile_end_for('css_compile file ' . $c);
}

/**
 * Return a specific compiled CSS file.
 *
 * @param  ID_TEXT $active_theme The theme the file is being loaded for
 * @param  string $theme Theme name
 * @param  string $c The CSS file required
 * @param  PATH $full_path Full path to CSS file (file is in uncompiled Tempcode format)
 * @param  boolean $minify Whether to also do minification
 * @return array A pair: success status, The text of the compiled file
 *
 * @ignore
 */
function _css_compile($active_theme, $theme, $c, $full_path, $minify = true)
{
    cms_profile_start_for('_css_compile file ' . $c);

    // Book-keeping, then loading up the CSS text
    global $KEEP_MARKERS, $SHOW_EDIT_LINKS;
    $keep_markers = $KEEP_MARKERS;
    $show_edit_links = $SHOW_EDIT_LINKS;
    $KEEP_MARKERS = false;
    $SHOW_EDIT_LINKS = false;
    if (($theme != 'default') && (!is_file($full_path))) {
        $theme = 'default';
    }
    if ($GLOBALS['RECORD_TEMPLATES_USED']) {
        global $RECORDED_TEMPLATES_USED;
        $RECORDED_TEMPLATES_USED[] = 'css/' . $c . '.css';
    }
    require_code('tempcode_compiler');
    global $ATTACHED_MESSAGES_RAW;
    $num_msgs_before = count($ATTACHED_MESSAGES_RAW);
    $suffix = '.' . get_file_extension($full_path);

    cms_profile_start_for('_css_compile: _do_template');
    $css = _do_template($theme, (strpos($full_path, '/css_custom/') !== false) ? '/css_custom/' : '/css/', $c, $c, user_lang(), $suffix, $active_theme);
    cms_profile_end_for('_css_compile: _do_template');

    cms_profile_start_for('_css_compile: _do_template->evaluate');
    $out = $css->evaluate();
    cms_profile_end_for('_css_compile: _do_template->evaluate');

    $num_msgs_after = count($ATTACHED_MESSAGES_RAW);
    global $CSS_COMPILE_ACTIVE_THEME;
    $CSS_COMPILE_ACTIVE_THEME = $active_theme;

    // Strip empty comments (would have encapsulated Tempcode comments)
    $out = cms_preg_replace_safe('#/\*\s*\*/#', '', $out);

    // The language may need to filter it
    global $LANG_FILTER_OB;
    if (isset($LANG_FILTER_OB)) {
        $out = $LANG_FILTER_OB->filter_css($c, $out);
    }

    // Minification
    if ($minify) {
        cms_profile_start_for('_css_compile: css_minify');
        $out = css_minify($out);
        cms_profile_end_for('_css_compile: css_minify');
    }

    // "Do not edit the cache file" warning
    if ($c != 'no_cache') {
        if (($out != '') && (!$minify)) {
            $out = '/* DO NOT EDIT. THIS IS A CACHE FILE AND WILL GET OVERWRITTEN RANDOMLY.' . "\n" . 'INSTEAD EDIT THE CSS FROM WITHIN THE ADMIN ZONE, OR BY MANUALLY EDITING A CSS_CUSTOM OVERRIDE. */' . "\n\n" . $out;
        }
    }

    // Book-keeping then finish
    $KEEP_MARKERS = $keep_markers;
    $SHOW_EDIT_LINKS = $show_edit_links;

    cms_profile_end_for('_css_compile file ' . $c);

    if ($num_msgs_after > $num_msgs_before) { // Was an error (e.g. missing theme image), so don't cache so that the error will be visible on refresh and hence debugged
        return array(false, $out);
    }
    return array(true, $out);
}

/**
 * Minimise the given JavaScript
 *
 * @param   string $js JavaScript to minimise
 * @return  string      Minimised JavaScript
 */
function js_minify($js)
{
    if (strpos(substr($js, 0, 1000), 'no minify') !== false) {
        return str_replace('/*no minify*/', '', $js);
    }

    require_code('jsmin');

    if (class_exists('JSMin')) {
        $jsmin = new JSMin($js);
        $js = $jsmin->min();
    }

    return $js;
}

/**
 * cssmin.php - A simple CSS minifier.
 * --
 *
 * <code>
 * include("cssmin.php");
 * file_put_contents("path/to/target.css", cssmin::minify(file_get_contents("path/to/source.css")));
 * </code>
 * --
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING
 * BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * --
 *
 * @author     Joe Scylla <joe.scylla@gmail.com>
 * @copyright  2008 Joe Scylla <joe.scylla@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php MIT License
 * @version    1.0 (2008-01-31)
 * @package    core
 */

/**
 * Minifies stylesheet definitions
 *
 * @param   string $v Stylesheet definitions as string
 * @return  string      Minified stylesheet definitions
 */
function css_minify($v)
{
    $search = array('/\/\*[\d\D]*?\*\/|\t+/', '/\s+/');
    $replace = array('', ' ');
    $v = preg_replace($search, $replace, $v);
    $search = array('/\\;\s/', '/\s+\{\\s+/', '/\\:\s+\\#/', '/,\s+/i', '/\\:\s+\\\'/i', '/\\:\s+([0-9]+|[A-F]+)/i');
    $replace = array(';', '{', ':#', ',', ':\'', ':$1');
    $v = preg_replace($search, $replace, $v);
    $v = str_replace("\n", '', $v);
    return trim($v);
}
