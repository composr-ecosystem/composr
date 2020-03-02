<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class phpdoc_test_set extends cms_test_case
{
    public function testForCopyAndPastedDescriptions()
    {
        $phpdoc_to_functions = [];

        $exceptions = [
            'Convert a field value to something renderable.',
            'Get details of action log entry types handled by this hook.',
            'Find the e-mail address for system e-mails (Reply-To header).',
            'Process an e-mail found.',
            'Strip system code from an e-mail component.',
            'Send out an e-mail about us not recognising an e-mail address for an incoming e-mail.',
            'Output a login page.',
            'Find an entry thumbnail.',
            'Standard code module initialisation function.',
            'Provides a hook for file synchronisation between mirrored servers.',
            'Check the given master password is valid.',
            'Evaluate a particular Tempcode directive.',
            'Evaluate a particular Tempcode symbol.',
            'Check the given master password is valid.',
            'Return parse info for parse type.',
            'Create file with unique file name, but works around compatibility issues between servers.',
            'Standard Tapatalk endpoint implementation.',
            'Standard Tapatalk endpoint test.',
            'Make sure we are doing necessary join to be able to access the given field.',
            'This is a less-revealing alternative to fatal_exit, that is used for user-errors/common-corruption-scenarios.',
            'Get suitable placeholder text.',
            'Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.',
            'Run a section of health checks.',
            'Standard Commandr-fs',
            'Implementation-specific e-mail dispatcher, passed with pre-prepared/tidied e-mail component details for us to use.',
            'Standard import function.',
            'Further filter results from _all_members_who_have_enabled.',
            '@license',
            '@package',
            'Standard PHP XML parser function.',
            'Find the cache signature for the block.',
            'Actualiser to undo a certain type of punitive action.',
            'Substitution callback for \'fix_links\'.',
            'Get the filename for a resource ID. Note that filenames are unique across all folders in a filesystem.',
            'Ensure that the specified file/folder is writeable for the FTP user',
            'Helper function for usort to sort a list by string length.',
            'Read a virtual property for a member file.',
            'Do the inner call using a particular downloader method.',
            'XHTML-aware helper function',
            'Get a well formed URL equivalent to the current URL.',
            'Syndicate human-intended descriptions of activities performed to the internal wall, and external listeners.',
            'Execute the module.',
            'Execute the block.',
            'Find caching details for the block.',
            'Database driver class.',
            'Forum driver class.',
            'Find details of a position in the Sitemap.',
            'Get the permission page that nodes matching $page_link in this hook are tied to.',
            'Find if a page-link will be covered by this node.',
            'Spreadsheet reader.',
            'Read spreadsheet row.',
            'Write spreadsheet row.',
            'Get Tempcode for an adding form.',
            'Standard crud_module edit form filler.',
            'Standard crud_module add actualiser.',
            'Standard crud_module edit actualiser.',
            'Standard crud_module category getter.',
            'This function is a very basic query executor.',
            'Escape a string so it may be inserted into a query.',
            'Get a map of Composr field types, to actual database types.',
            'Find all the graphs in a stats category.',
            'Find metadata about stats graphs that are provided by this stats hook.',
            'Generate final data from preprocessed data.',
            'Generate a stats graph filter form.',
            'Find an entry content-type language string label.',
            'Find an entry content-type universal label (doesn\'t depend on language pack).',
            'Find an entry title.',
            'Find an entry description.',
        ];
        $exceptions_regexp = '#' . implode('|', array_map('preg_quote', $exceptions)) . '#';

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_FLOATING | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE, true, true, ['php']);

        foreach ($files as $file) {
            if (preg_match('#^(tracker|_tests|sources_custom/photobucket|sources_custom/ILess|sources_custom/swift_mailer|sources_custom/spout|sources_custom/sabredav|sources_custom/aws|mobiquo/lib)/#', $file) != 0) {
                continue;
            }
            if (preg_match('#^(sources_custom/facebook/facebook.php|sources/mail_dkim\.php|sources/firephp\.php|sources_custom/twitter\.php|sources_custom/browser_detect\.php)$#', $file) != 0) {
                continue;
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $file);

            $c = preg_replace('#\n\/\*\*.*\n \*\/\n\n#Us', '', $c);

            $matches = [];
            $num_matches = preg_match_all('#\/\*\*\n\s+\* (.*)\n\s+\*\/\n(\s*)((public|protected|private) )?function &?(\w+)\(#Us', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $phpdoc = $matches[1][$i];
                $function_name = $matches[5][$i];

                $phpdoc = preg_replace('#\* @.*$#s', '', $phpdoc);

                if (preg_match($exceptions_regexp, $phpdoc) != 0) {
                    continue;
                }

                if (!isset($phpdoc_to_functions[$phpdoc])) {
                    $phpdoc_to_functions[$phpdoc] = [];
                }
                $phpdoc_to_functions[$phpdoc][] = $function_name;
            }
        }

        foreach ($phpdoc_to_functions as $phpdoc => $functions) {
            $rationalised_functions = [];
            foreach ($functions as $function_name) {
                $rationalised_function_name = ltrim($function_name, '_');
                $rationalised_functions[$rationalised_function_name] = true;
            }

            $this->assertTrue(count($rationalised_functions) == 1, 'Multiple use of phpdoc comment... ' . $phpdoc . ' (' . serialize($rationalised_functions) . ')');
        }
    }
}
