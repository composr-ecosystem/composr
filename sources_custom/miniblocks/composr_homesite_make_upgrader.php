<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!function_exists('mu_ui')) {
    function mu_ui()
    {
        $spammer_blackhole = static_evaluate_tempcode(symbol_tempcode('INSERT_SPAMMER_BLACKHOLE'));

        echo <<<END
<p>
    You can generate an upgrader from any version of Composr to any other version. If you access this upgrade post via the version information box on your Admin Zone front page then we'll automatically know what version you're running.
    <br />
    If you'd prefer though you can enter in your version number right here:
</p>
<form onsubmit="this.elements['make_upgrader_button'].disabled=true;" action="#" method="post">
    {$spammer_blackhole}

    <p style="margin: 4px 0">
        <label style="width: 170px; float: left" for="from_version_a">Major version (e.g. <kbd>10</kbd>)</label>
        <input size="2" maxlength="2" type="text" name="from_version_a" id="from_version_a" value="" />
    </p>
    <p style="margin: 4px 0">
        <label style="width: 170px; float: left" for="from_version_b">Minor version (e.g. <kbd>0</kbd>)</label>
        <input size="1" maxlength="1" type="text" name="from_version_b" id="from_version_b" value="" />
    </p>
    <p style="margin: 4px 0">
        <label style="width: 170px; float: left" for="from_version_c">Patch version (e.g. <kbd>0</kbd>)</label>
        <input size="2" maxlength="2" type="text" name="from_version_c" id="from_version_c" value="" />
    </p>
    <p style="margin: 4px 0; font-size: 0.8em">
        <label style="width: 170px; float: left" for="from_version_d">Pre-release version (e.g. beta1)</label>
        <input size="6" type="text" name="from_version_d" id="from_version_d" value="" /> (usually blank)
    </p>
    <p>(example above is for upgrading from 10.0.0 beta1)</p>
    <p>
        <input class="buttons__proceed button_screen_item" id="make_upgrader_button" type="submit" value="Generate" />
    </p>
</form>
END;
    }
}

if (!function_exists('mu_result')) {
    function mu_result($path)
    {
        // Shorten path to be more readable
        $normal_bore = get_file_base() . '/uploads/website_specific/compo.sr/upgrades/tars/';
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

        echo '<label for="upgrade_file">Upgrade file:</label> <input id="upgrade_file" class="notranslate" size="45" readonly="readonly" type="text" value="' . escape_html($url) . '" />, or <a href="' . escape_html($url) . '">download upgrade directly</a> (' . escape_html(clean_file_size(filesize($path))) . ').';
    }
}

$to_version_dotted = $map['param'];

require_code('version2');
$to_version_pretty = get_version_pretty__from_dotted($to_version_dotted);

echo <<<END
    <div class="box">
        <div class="box_inner">
            <h4>Your upgrade to version {$to_version_pretty}</h4>
END;

$from_long_dotted_number_with_qualifier = get_param_string('from_version', null); // Dotted format
if (is_null($from_long_dotted_number_with_qualifier)) {
    $a = post_param_string('from_version_a', null);
    $b = post_param_string('from_version_b', null);
    $c = post_param_string('from_version_c', null);
    $d = post_param_string('from_version_d', null);
    if ((is_null($a)) || (is_null($b)) || (is_null($c))) {
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
require_code('uploads/website_specific/compo.sr/upgrades/make_upgrader.php');
$ret = make_upgrade_get_path($from_version_dotted, $to_version_dotted);

if (!is_null($ret[1])) {

    echo '<p>' . $ret[1] . '</p>';
}

if (!is_null($ret[0])) {
    mu_result($ret[0]);
}

echo <<<END
        </div>
    </div>
END;
