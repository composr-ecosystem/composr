<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    achievements
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

require_code('files');

require_lang('achievements');

require_javascript('core_form_interfaces');

$title = get_screen_title('EDIT_ACHIEVEMENTS');
$type = get_param_string('type', 'browse');

$full_path = get_custom_file_base() . '/data_custom/xml_config/achievements.xml';

$post_url = build_url(['page' => '_SELF', 'type' => 'save'], '_SELF');

// Default XML in case it does not exist or is empty
$default_xml = '
<!--
    Each achievement supports these attributes:
    - name (required; a unique alphanumeric codename for the achievement)
    - title (required; a title for this achievement which will be publicly visible... can be a language string codename or raw text)
    - image (optional but recommended; image to use as the achievement badge. Can be an absolute URL, relative path, or a theme image code. Defaults to no image.)
    - readOnly (Optional; set to 1 to disable the ability to earn this achievement if not already earned. Defaults to 0.)
    - hidden (Optional; set to 1 if you do not want to display this achievement among those that can be earned... it will only be visible when earned and only to the member who earned it. Defaults to 0.)
     - If an achievement has no qualifications defined, or all of the qualifications are disabled, the achievement will be both hidden and readOnly.
    - permanent (optional; set to 1 if this achievement cannot be revoked if a member earned it but later no-longer meets its qualifications. Defaults to 0.)
        - Note that if you set this to 1, you must manually remove achievement awards from the database (achievements_members) and their points if you want to revoke an achievement from a member.
    - points (optional; award this many points for the achievement [points will be reversed if the achievement is removed])
-->

<!--
    You should check sources_custom/hooks/systems/achievement_qualifications for parameters to specific qualifications.
    Qualification tags also have these additional global attributes:
    - name (Required; the name of the achievement_qualifications hook we are using [filename without the .php at the end]; if it does not exist or it returns null, it will be ignored.)
    - persist (Optional; set to 1 if qualification progress should only ever go up, not down [e.g. if a content is deleted, progress would not discount it anymore]. Defaults to a hook persist_progress_default value.)
-->

<!--
    A note about how achievement progress is tracked:
     - Tracking in the database of achievements earned is matched to the defined achievement "name". If you change a name, everyone loses the achievement.
     - Tracking in the database of qualification progress is matched to a hash of the qualification attributes, group number, and achievement (the hash also considers the name of the achievement).
      - If you change any qualification parameters or the achievement name, progress is reset and re-calculated.
      - If you move a qualification to another achievement, progress will also be reset and re-calculated.
-->

<achievements>
    <!--
    <achievement name="extrovert" title="achievements:ACHIEVEMENT_EXTROVERT" points="100">
        <qualifications>
            <qualification name="activity_feed" count="10" />
            <qualification name="chat_messages" count="50" />
        </qualifications>
        <qualifications>
            <qualification name="content" types="post" count="25" />
        </qualifications>
    </achievement>
    -->
</achievements>
';

// Load current contents of the XML file (or what we are about to save if applicable)
$prev_xml = file_exists($full_path) ? cms_file_get_contents_safe($full_path, FILE_READ_LOCK | FILE_READ_BOM) : '';
if (empty(trim($prev_xml))) {
    $prev_xml = $default_xml;

    // Must drop it immediately so the revisions system works
    cms_file_put_contents_safe($full_path, $default_xml, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE | FILE_WRITE_BOM);
}

if ($type == 'save') {
    $xml = post_param_string('xml');
} else {
    $xml = $prev_xml;
}

// Support revisions tracking for achievements
$revision_loaded = null;
if (addon_installed('actionlog')) {
    require_code('revisions_engine_files');
    $revision_engine = new RevisionEngineFiles();
    $directory = 'data_custom/xml_config';
    $revisions = $revision_engine->ui_revisions_controller($directory, 'achievements', 'xml', 'EDIT_ACHIEVEMENTS', $xml, $revision_loaded);
    if ((get_param_integer('diffing', 0) == 1) && (!$revisions->is_empty())) {
        return $revisions;
    }

    // Log a revision if we are about to save
    if ($type == 'save') {
        $revision_engine->add_revision(
            dirname($full_path),
            'achievements',
            'xml',
            $prev_xml,
            filemtime($full_path)
        );
    }
} else {
    $revisions = new Tempcode();
}

// Save actualisation
if ($type == 'save') {
    require_code('input_filter_2');
    if (get_value('disable_modsecurity_workaround') !== '1') {
        modsecurity_workaround_enable();
    }

    // Drop the new XML into the file
    cms_file_put_contents_safe($full_path, $xml, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE | FILE_WRITE_BOM);

    log_it('EDIT_ACHIEVEMENTS');

    // This will display validation errors and run cleanup operations
    require_code('achievements');
    $ob = load_achievements(true);
    $ob->cleanup();

    // Clear cache for everyone
    require_code('caches');
    delete_cache_entry('achievements');
}

$description = do_lang_tempcode('DESCRIPTION_EDIT_ACHIEVEMENTS');

set_helper_panel_tutorial('sup_achievements');

// Render the XML editing screen (and revisions)
return do_template('XML_CONFIG_SCREEN', [
    '_GUID' => '235512de6acf592baa330118521602a3',
    'TITLE' => $title,
    'POST_URL' => $post_url,
    'XML' => $xml,
    'DESCRIPTION' => $description,
    'REVISIONS' => $revisions,
]);
