<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    hybridauth
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

require_lang('hybridauth');

require_javascript('core_form_interfaces');

$title = get_screen_title('HYBRIDAUTH_CONFIG');

$type = get_param_string('type', 'browse');

$full_path = get_custom_file_base() . '/data_custom/xml_config/hybridauth.xml';

$post_url = build_url(['page' => '_SELF', 'type' => 'save'], '_SELF');

set_helper_panel_text(comcode_lang_string('DOC_HYBRIDAUTH'));

if ($type == 'save') {
    require_code('input_filter_2');
    if (get_value('disable_modsecurity_workaround') !== '1') {
        modsecurity_workaround_enable();
    }

    require_code('files');
    $xml = post_param_string('xml');
    cms_file_put_contents_safe($full_path, $xml, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE | FILE_WRITE_BOM);

    log_it('HYBRIDAUTH_CONFIG');
} else {
    $default_xml = '
<hybridauth>
    <!--
    <SomeProvider>
        <composr-config allow_signups="true" />
        <keys-config id="ExampleOAuthId" secret="ExampleOAuthSecret" />
    </SomeProvider>
    -->
</hybridauth>
    ';

    $xml = file_exists($full_path) ? cms_file_get_contents_safe($full_path) : '';
    if (empty(trim($xml))) {
        $xml = $default_xml;
    }
}

require_code('form_templates');
list($warning_details, $ping_url) = handle_conflict_resolution('', false);

return do_template('XML_CONFIG_SCREEN', [
    '_GUID' => 'feb50a2637586dbb75ed5ddb41e4f01b',
    'TITLE' => $title,
    'POST_URL' => $post_url,
    'XML' => $xml,
    'WARNING_DETAILS' => $warning_details,
    'PING_URL' => $ping_url,
]);
