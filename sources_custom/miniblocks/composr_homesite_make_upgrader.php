<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_homesite
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('composr_homesite')) {
    return do_template('RED_ALERT', ['_GUID' => '0q6vvpwtbrnqw5y6wmpzn7kqechhxkv9', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('composr_homesite'))]);
}

if (!addon_installed('downloads')) {
    return do_template('RED_ALERT', ['_GUID' => '033cdc5wtl2new7j2degs0lo2n2gbhwy', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('downloads'))]);
}
if (!addon_installed('news')) {
    return do_template('RED_ALERT', ['_GUID' => 'c39eg9wtj29jb07b15zkmno460d6712y', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('news'))]);
}

if (!function_exists('mu_ui')) {
    function mu_ui()
    {
        $spammer_blackhole = static_evaluate_tempcode(symbol_tempcode('INSERT_FORM_POST_SECURITY'));
        $proceed_icon = do_template('ICON', ['_GUID' => '79e1ec738649822eaf1a8c25e7ffbfc8', 'NAME' => 'buttons/proceed']);
        echo <<<END
<p>
    You can generate an upgrader from any version of Composr to any other version. If you access this upgrade post via the version information box on your Admin Zone dashboard then we'll automatically know what version you're running.
    <br />
    If you'd prefer though you can enter in your version number right here:
</p>
<form onsubmit="document.getElementById('make-upgrader-button').disabled = true;" action="#!" method="post">
    {$spammer_blackhole}

    <p style="margin: 4px 0">
        <label style="width: 170px; float: left" for="from_version_a">Major version (e.g. <kbd>11</kbd>)</label>
        <input size="2" maxlength="2" type="text" name="from_version_a" id="from_version_a" value="" />
    </p>
    <p style="margin: 4px 0">
        <label style="width: 170px; float: left" for="from_version_b">Minor version (e.g. <kbd>0</kbd>)</label>
        <input size="1" maxlength="2" type="text" name="from_version_b" id="from_version_b" value="" />
    </p>
    <p style="margin: 4px 0">
        <label style="width: 170px; float: left" for="from_version_c">Patch version (e.g. <kbd>0</kbd>)</label>
        <input size="2" maxlength="3" type="text" name="from_version_c" id="from_version_c" value="" />
    </p>
    <p style="margin: 4px 0; font-size: 0.8em">
        <label style="width: 170px; float: left" for="from_version_d">Bleeding-edge version (e.g. alpha1)</label>
        <input size="6" type="text" name="from_version_d" id="from_version_d" value="" /> (usually blank)
    </p>
    <p>(example above is for upgrading from 11.0.0 alpha1)</p>
    <p>
        <button class="btn btn-primary btn-scri buttons--proceed" id="make-upgrader-button" type="submit">{$proceed_icon} Generate</button>
    </p>
</form>
END;
    }
}

if (!function_exists('mu_result')) {
    function mu_result($path)
    {
        // Shorten path to be more readable
        $normal_bore = get_file_base() . '/uploads/website_specific/composr.app/upgrades/tars/';
        $shortened = get_file_base() . '/upgrades/';
        if (!file_exists($shortened)) {
            symlink($normal_bore, 'upgrades');
        }
        if (substr($path, 0, strlen($normal_bore)) == $normal_bore) {
            $path = $shortened . substr($path, strlen($normal_bore));
        }

        $base_url = get_base_url();
        $base_url = preg_replace('#^https://#', 'http://', $base_url); // Some PHP installs have problem with HTTPS
        $url = $base_url . '/' . rawurldecode(substr($path, strlen(get_file_base()) + 1));

        require_code('files');

        echo '<label for="upgrade-file">Upgrade file:</label> <input id="upgrade-file" class="notranslate" size="45" readonly="readonly" type="text" value="' . escape_html($url) . '" />, or <a href="' . escape_html($url) . '">download upgrade directly</a> (' . escape_html(clean_file_size(filesize($path))) . ').';
    }
}

if (@cms_empty_safe($map['param'])) {
    return do_template('RED_ALERT', ['_GUID' => '8e2afe63b797f602d9469ecba4578028', 'TEXT' => do_lang_tempcode('NO_PARAMETER_SENT', 'param')]);
}
$to_version_dotted = $map['param'];

require_code('version2');
$to_version_pretty = get_version_pretty__from_dotted($to_version_dotted);

echo <<<END
    <div class="box">
        <div class="box-inner">
            <h4>Your upgrade to version {$to_version_pretty}</h4>
END;

$from_long_dotted_number_with_qualifier = get_param_string('from_version', null); // Dotted format
if ($from_long_dotted_number_with_qualifier === null) {
    $a = post_param_string('from_version_a', null);
    $b = post_param_string('from_version_b', null);
    $c = post_param_string('from_version_c', null);
    $d = post_param_string('from_version_d', null);
    if (($a === null) || ($b === null) || ($c === null)) {
        mu_ui();
        echo <<<END
        </div>
    </div>
END;
        return;
    }

    // Trim spaces and leading zeros
    $a = rtrim(preg_replace('#^(0\s)#', '', $a));
    $b = rtrim(preg_replace('#^(0\s)#', '', $b));
    $c = rtrim(preg_replace('#^(0\s)#', '', $c));
    $d = rtrim(preg_replace('#^(0\s)#', '', $d));

    $from_long_dotted_number_with_qualifier = $a;
    if ($b != '') {
        $from_long_dotted_number_with_qualifier .= '.' . $b;
    }
    if ($c != '') {
        $from_long_dotted_number_with_qualifier .= '.' . $c;
    }
    if ($d != '') {
        $from_long_dotted_number_with_qualifier .= '.' . $d;
    }
}
require_code('version2');
$from_version_dotted = get_version_dotted__from_anything($from_long_dotted_number_with_qualifier); // Canonicalise

require_code('composr_homesite');
require_code('uploads/website_specific/composr.app/upgrades/make_upgrader.php');
$ret = make_upgrade_get_path($from_version_dotted, $to_version_dotted);

if ($ret[1] !== null) {
    echo '<p>' . $ret[1] . '</p>';
}

if ($ret[0] !== null) {
    mu_result($ret[0]);
}

echo <<<END
        </div>
    </div>
END;
