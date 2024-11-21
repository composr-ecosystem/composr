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

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

class Miniblock_cms_homesite_make_upgrader
{
    /**
     * Run the miniblock.
     *
     * @param  array $map Array of block parameters
     * @return Tempcode
     */
    public function run(array $map)
    {
        if (!addon_installed('cms_homesite')) {
            return do_template('RED_ALERT', ['_GUID' => '258acd7a2cab51d09d90651ade970d2d', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('cms_homesite'))]);
        }
        if (!addon_installed('downloads')) {
            return do_template('RED_ALERT', ['_GUID' => '03c2605b881b5bbc9604d3c25a9486a0', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('downloads'))]);
        }
        if (!addon_installed('news')) {
            return do_template('RED_ALERT', ['_GUID' => 'f70209f52b2654b0874fad8a88f8f3c8', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('news'))]);
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
                $this->mu_ui();
                echo <<<END
                </div>
            </div>
        END;
                return new Tempcode();
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

        require_code('cms_homesite');
        require_code('cms_homesite_make_upgrader');
        $ret = make_upgrade_get_path($from_version_dotted, $to_version_dotted);

        if ($ret[1] !== null) {
            echo '<p>' . $ret[1] . '</p>';
        }

        if ($ret[0] !== null) {
            $this->mu_result($ret[0]);
        }

        echo <<<END
                </div>
            </div>
        END;

        return new Tempcode();
    }

    /**
     * Build and echo a form for specifying the version.
     */
    protected function mu_ui()
    {
        require_code('form_templates');

        $form = new Tempcode();
        $form->attach(form_input_integer('Major version', 'The major version you are currently running. In the example 11.0.19.alpha1, it is 11.', 'from_version_a', null, true));
        $form->attach(form_input_integer('Minor version', 'The minor version you are currently running. Examples: It is 0 in 11.0.19.alpha1, it is 3 in 11.3.8, it is 2 in 11.2, it is 0 in 11.beta2 (not defined), and it is 0 in 11 (not defined).', 'from_version_b', null, true));
        $form->attach(form_input_integer('Patch version', 'The patch version you are currently running. Examples: It is 19 in 11.0.19.alpha1, it is 8 in 11.3.8, it is 0 in 11.2 (not defined), it is 0 in 11.beta2 (not defined), and it is 0 in 11 (not defined).', 'from_version_c', null, true));
        $form->attach(form_input_line('Bleeding-edge version', 'The bleeding-edge version you are currently running, if applicable. Examples: It is alpha1 in 11.0.19.alpha1, it is blank in 11.3.8 (not defined), it is blank in 11.2 (not defined), it is beta2 in 11.beta2, and it is blank in 11 (not defined).', 'from_version_d', '', false));

        $hidden = new Tempcode();

        $post_url = get_self_url();

        $ret = do_template('FORM_SCREEN', [
            '_GUID' => '6ba4f8844a2a3954696071b65af0b12e',
            'GET' => false,
            'SKIP_WEBSTANDARDS' => true,
            'HIDDEN' => $hidden,
            'TITLE' => 'Make a Composr upgrader',
            'TEXT' => paragraph('You can generate an upgrader from any version of Composr to any other version. If you access this upgrade post via the version information box on your Admin Zone dashboard then we\'ll automatically know what version you\'re running. If you\'d prefer though you can enter in your version number right here:'),
            'SUBMIT_ICON' => 'buttons/proceed',
            'SUBMIT_NAME' => do_lang_tempcode('PROCEED'),
            'FIELDS' => $form,
            'URL' => $post_url,
        ]);

        $ret->evaluate_echo();
    }

    /**
     * Echo the results of the upgrader generation.
     * @param  PATH $path The file path to the upgrade
     */
    protected function mu_result(string $path)
    {
        // Shorten path to be more readable
        // Actually let's not do that; some servers do not support symlinks and will throw an error
        /*
            $normal_bore = get_file_base() . '/uploads/website_specific/cms_homesite/upgrades/tars/';
            $shortened = get_file_base() . '/upgrades/';
            if (!file_exists($shortened)) {
                symlink($normal_bore, 'upgrades');
            }
            if (substr($path, 0, strlen($normal_bore)) == $normal_bore) {
                $path = $shortened . substr($path, strlen($normal_bore));
            }
        */

        $base_url = get_base_url();
        $url = $base_url . '/' . rawurldecode(substr($path, strlen(get_file_base()) + 1));

        require_code('files');

        echo '<label for="upgrade-file">Upgrade file:</label> <input id="upgrade-file" class="notranslate" size="45" readonly="readonly" type="text" value="' . escape_html($url) . '" />, or <a href="' . escape_html($url) . '">download upgrade directly</a> (' . escape_html(clean_file_size(filesize($path))) . ').';
    }
}

$miniblock = new Miniblock_cms_homesite_make_upgrader();
return $miniblock->run($map);
