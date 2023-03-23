<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    better_mail
 */

/**
 * Hook class.
 */
class Hook_addon_registry_better_mail
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array(bool $runtime = false) : array
    {
        return [];
    }

    /**
     * Get the version of Composr this addon is for.
     *
     * @return float Version number
     */
    public function get_version() : float
    {
        return cms_version_number();
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Architecture';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Replaces Composr\'s built-in mailer with one based around Swift Mailer. This may help workaround problems with buggy/complex SMTP servers, or ones that require SSL (e.g. gmail). If you\'re not have mail problems there\'s no point using this.

There is a new hidden option, [tt]mail_encryption[/tt] to set SMTP encryption. Set this to [tt]tcp[/tt] (no SMTP encryption), [tt]ssl[/tt] (implicit SSL/TLS SMTP encryption), or [tt]tls[/tt] (explicit START-TLS SMTP encryption).
e.g. Type the following into Commandr:
[code]
:set_value(\'mail_encryption\', \'tls\');
[/code]
';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return [];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array A structure specifying dependency information
     */
    public function get_dependencies() : array
    {
        return [
            'requires' => [],
            'recommends' => [],
            'conflicts_with' => [],
        ];
    }

    /**
     * Explicitly say which icon should be used.
     *
     * @return URLPATH Icon
     */
    public function get_default_icon() : string
    {
        return 'themes/default/images/icons/admin/component.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'sources_custom/hooks/systems/addon_registry/better_mail.php',
            'sources_custom/mail.php',
            'sources_custom/hooks/systems/health_checks/email.php',

            'sources_custom/swift_mailer/.htaccess',
            'sources_custom/swift_mailer/index.html',
            'sources_custom/swift_mailer/vendor/.htaccess',
            'sources_custom/swift_mailer/vendor/composer/.htaccess',
            'sources_custom/swift_mailer/vendor/composer/index.html',
            'sources_custom/swift_mailer/vendor/doctrine/deprecations/.htaccess',
            'sources_custom/swift_mailer/vendor/doctrine/deprecations/index.html',
            'sources_custom/swift_mailer/vendor/doctrine/deprecations/lib/Doctrine/Deprecations/.htaccess',
            'sources_custom/swift_mailer/vendor/doctrine/deprecations/lib/Doctrine/Deprecations/PHPUnit/.htaccess',
            'sources_custom/swift_mailer/vendor/doctrine/deprecations/lib/Doctrine/Deprecations/PHPUnit/index.html',
            'sources_custom/swift_mailer/vendor/doctrine/deprecations/lib/Doctrine/Deprecations/index.html',
            'sources_custom/swift_mailer/vendor/doctrine/lexer/.htaccess',
            'sources_custom/swift_mailer/vendor/doctrine/lexer/index.html',
            'sources_custom/swift_mailer/vendor/doctrine/lexer/src/.htaccess',
            'sources_custom/swift_mailer/vendor/doctrine/lexer/src/index.html',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/.htaccess',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/index.html',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/.htaccess',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Parser/.htaccess',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Parser/CommentStrategy/.htaccess',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Parser/CommentStrategy/index.html',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Parser/index.html',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/.htaccess',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/.htaccess',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/index.html',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/index.html',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Validation/.htaccess',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Validation/Exception/.htaccess',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Validation/Exception/index.html',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Validation/Extra/.htaccess',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Validation/Extra/index.html',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Validation/index.html',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/.htaccess',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/index.html',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/index.html',
            'sources_custom/swift_mailer/vendor/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/.github/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/.github/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/.github/workflows/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/.github/workflows/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/doc/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/doc/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/AddressEncoder/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/AddressEncoder/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/ByteStream/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/ByteStream/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/CharacterReader/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/CharacterReader/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/CharacterReaderFactory/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/CharacterReaderFactory/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/CharacterStream/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/CharacterStream/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Encoder/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Encoder/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Events/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Events/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/KeyCache/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/KeyCache/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mailer/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mailer/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/ContentEncoder/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/ContentEncoder/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/HeaderEncoder/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/HeaderEncoder/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/Headers/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/Headers/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/Decorator/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/Decorator/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/Loggers/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/Loggers/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/Pop/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/Pop/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/Reporters/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/Reporters/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Signers/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Signers/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/StreamFilters/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/StreamFilters/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/Esmtp/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/Esmtp/Auth/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/Esmtp/Auth/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/Esmtp/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/dependency_maps/.htaccess',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/dependency_maps/index.html',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/index.html',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/.htaccess',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/.htaccess',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/index.html',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/index.html',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/.htaccess',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/Resources/unidata/.htaccess',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/Resources/unidata/index.html',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/index.html',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-normalizer/.htaccess',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-normalizer/Resources/stubs/.htaccess',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-normalizer/Resources/stubs/index.html',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-normalizer/Resources/unidata/.htaccess',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-normalizer/Resources/unidata/index.html',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-normalizer/index.html',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-mbstring/.htaccess',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-mbstring/Resources/unidata/.htaccess',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-mbstring/Resources/unidata/index.html',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-mbstring/index.html',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-php72/.htaccess',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-php72/index.html',
            'sources_custom/swift_mailer/composer.json',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-mbstring/LICENSE',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-mbstring/Resources/unidata/lowerCase.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-mbstring/Resources/unidata/upperCase.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-mbstring/Resources/unidata/titleCaseRegexp.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-mbstring/bootstrap.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-mbstring/README.md',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-mbstring/bootstrap80.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-mbstring/composer.json',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-mbstring/Mbstring.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-php72/Php72.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-php72/LICENSE',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-php72/bootstrap.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-php72/README.md',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-php72/composer.json',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-normalizer/LICENSE',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-normalizer/Resources/stubs/Normalizer.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-normalizer/Resources/unidata/canonicalDecomposition.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-normalizer/Resources/unidata/compatibilityDecomposition.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-normalizer/Resources/unidata/canonicalComposition.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-normalizer/Resources/unidata/combiningClass.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-normalizer/bootstrap.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-normalizer/Normalizer.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-normalizer/README.md',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-normalizer/bootstrap80.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-normalizer/composer.json',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/LICENSE',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.windows-1251.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-14.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.windows-1254.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp866.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-10.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-9.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-6.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp1026.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.windows-1252.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp869.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.windows-1250.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-16.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp862.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp864.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp863.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.windows-1258.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp932.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp037.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp949.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-4.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/translit.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp737.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp850.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.windows-1256.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.koi8-u.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.us-ascii.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp865.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp856.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp775.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-15.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-2.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp500.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp875.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-1.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp424.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp437.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.big5.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.windows-1255.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp874.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp950.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp857.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-7.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.koi8-r.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-3.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp936.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-13.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp855.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp1006.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp852.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-8.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-11.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.windows-1253.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp860.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-5.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.cp861.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Resources/charset/from.windows-1257.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/bootstrap.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/README.md',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/bootstrap80.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/composer.json',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-iconv/Iconv.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/LICENSE',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/Idn.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/Resources/unidata/disallowed_STD3_mapped.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/Resources/unidata/Regex.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/Resources/unidata/virama.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/Resources/unidata/ignored.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/Resources/unidata/disallowed_STD3_valid.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/Resources/unidata/mapped.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/Resources/unidata/DisallowedRanges.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/Resources/unidata/disallowed.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/Resources/unidata/deviation.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/bootstrap.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/README.md',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/bootstrap80.php',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/composer.json',
            'sources_custom/swift_mailer/vendor/symfony/polyfill-intl-idn/Info.php',
            'sources_custom/swift_mailer/vendor/doctrine/deprecations/lib/Doctrine/Deprecations/PHPUnit/VerifyDeprecations.php',
            'sources_custom/swift_mailer/vendor/doctrine/deprecations/lib/Doctrine/Deprecations/Deprecation.php',
            'sources_custom/swift_mailer/vendor/doctrine/deprecations/LICENSE',
            'sources_custom/swift_mailer/vendor/doctrine/deprecations/README.md',
            'sources_custom/swift_mailer/vendor/doctrine/deprecations/composer.json',
            'sources_custom/swift_mailer/vendor/doctrine/deprecations/phpcs.xml',
            'sources_custom/swift_mailer/vendor/doctrine/lexer/UPGRADE.md',
            'sources_custom/swift_mailer/vendor/doctrine/lexer/src/Token.php',
            'sources_custom/swift_mailer/vendor/doctrine/lexer/src/AbstractLexer.php',
            'sources_custom/swift_mailer/vendor/doctrine/lexer/LICENSE',
            'sources_custom/swift_mailer/vendor/doctrine/lexer/README.md',
            'sources_custom/swift_mailer/vendor/doctrine/lexer/composer.json',
            'sources_custom/swift_mailer/vendor/autoload.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/CONTRIBUTING.md',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Validation/MessageIDValidation.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Validation/MultipleValidationWithAnd.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Validation/DNSCheckValidation.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Validation/DNSGetRecordWrapper.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Validation/Extra/SpoofCheckValidation.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Validation/NoRFCWarningsValidation.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Validation/Exception/EmptyValidationList.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Validation/EmailValidation.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Validation/DNSRecords.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Validation/RFCValidation.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/MessageIDParser.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Parser.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/CFWSWithFWS.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/IPV6MaxGroups.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/TLD.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/CFWSNearAt.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/AddressLiteral.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/EmailTooLong.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/IPV6BadChar.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/DomainLiteral.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/NoDNSMXRecord.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/IPV6DoubleColon.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/IPV6GroupCount.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/DeprecatedComment.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/Warning.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/IPV6ColonStart.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/ObsoleteDTEXT.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/IPV6ColonEnd.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/QuotedString.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/IPV6Deprecated.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/Comment.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/QuotedPart.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Warning/LocalTooLong.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/ValidEmail.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/NoDNSRecord.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/NoDomainPart.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/CharNotAllowed.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/DomainAcceptsNoMail.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/LocalOrReservedDomain.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/UnusualElements.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/Reason.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/DomainTooLong.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/ExpectingDTEXT.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/EmptyReason.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/LabelTooLong.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/DetailedReason.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/ConsecutiveAt.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/UnclosedQuotedString.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/CRNoLF.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/CommaInDomain.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/ExpectingDomainLiteralClose.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/AtextAfterCFWS.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/ConsecutiveDot.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/CommentsInIDRight.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/UnableToGetDNSRecord.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/NoLocalPart.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/ExpectingATEXT.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/CRLFAtTheEnd.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/CRLFX2.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/ExpectingCTEXT.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/SpoofEmail.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/DomainHyphened.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/DotAtStart.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/ExceptionFound.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/RFCWarnings.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/UnclosedComment.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/DotAtEnd.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Reason/UnOpenedComment.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/InvalidEmail.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/Result.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/MultipleErrors.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Result/SpoofEmail.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/EmailLexer.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Parser/CommentStrategy/DomainComment.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Parser/CommentStrategy/LocalComment.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Parser/CommentStrategy/CommentStrategy.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Parser/IDRightPart.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Parser/DoubleQuote.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Parser/IDLeftPart.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Parser/DomainLiteral.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Parser/PartParser.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Parser/DomainPart.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Parser/Comment.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Parser/FoldingWhiteSpace.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/Parser/LocalPart.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/EmailParser.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/src/EmailValidator.php',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/LICENSE',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/CHANGELOG.md',
            'sources_custom/swift_mailer/vendor/egulias/email-validator/composer.json',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/.php_cs.dist',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/dependency_maps/transport_deps.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/dependency_maps/mime_deps.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/dependency_maps/message_deps.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/dependency_maps/cache_deps.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/EncodingObserver.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/SimpleHeaderSet.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/SimpleHeaderFactory.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/HeaderEncoder.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/SimpleMessage.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/EmbeddedFile.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/ContentEncoder.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/CharsetObserver.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/Headers/MailboxHeader.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/Headers/ParameterizedHeader.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/Headers/AbstractHeader.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/Headers/IdentificationHeader.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/Headers/PathHeader.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/Headers/UnstructuredHeader.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/Headers/DateHeader.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/Headers/OpenDKIMHeader.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/SimpleMimeEntity.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/Attachment.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/MimePart.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/IdGenerator.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/HeaderEncoder/QpHeaderEncoder.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/HeaderEncoder/Base64HeaderEncoder.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/ContentEncoder/RawContentEncoder.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/ContentEncoder/NullContentEncoder.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/ContentEncoder/PlainContentEncoder.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/ContentEncoder/QpContentEncoder.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/ContentEncoder/QpContentEncoderProxy.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/ContentEncoder/NativeQpContentEncoder.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/ContentEncoder/Base64ContentEncoder.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mime/Header.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/Pop/Pop3Connection.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/Pop/Pop3Exception.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/Reporters/HitReporter.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/Reporters/HtmlReporter.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/Decorator/Replacements.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/Loggers/EchoLogger.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/Loggers/ArrayLogger.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/ReporterPlugin.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/ThrottlerPlugin.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/Timer.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/PopBeforeSmtpPlugin.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/AntiFloodPlugin.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/BandwidthMonitorPlugin.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/RedirectingPlugin.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/Reporter.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/MessageLogger.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/DecoratorPlugin.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/LoggerPlugin.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/Sleeper.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/Logger.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Plugins/ImpersonatePlugin.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/CharacterReaderFactory/SimpleCharacterReaderFactory.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Signer.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/DependencyException.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/CharacterStream.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mailer.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/StreamFilters/ByteArrayReplacementFilter.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/StreamFilters/StringReplacementFilter.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/StreamFilters/StringReplacementFilterFactory.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/CharacterReader.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/AddressEncoder/IdnAddressEncoder.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/AddressEncoder/Utf8AddressEncoder.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/SendmailTransport.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Message.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Preferences.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/SendmailTransport.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/NullTransport.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/EsmtpTransport.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/IoBuffer.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/StreamBuffer.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/SmtpAgent.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/LoadBalancedTransport.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/EsmtpHandler.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/SpoolTransport.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/FailoverTransport.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/AbstractSmtpTransport.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/Esmtp/Auth/CramMd5Authenticator.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/Esmtp/Auth/LoginAuthenticator.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/Esmtp/Auth/XOAuth2Authenticator.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/Esmtp/Auth/PlainAuthenticator.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/Esmtp/Auth/NTLMAuthenticator.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/Esmtp/EightBitMimeHandler.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/Esmtp/SmtpUtf8Handler.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/Esmtp/AuthHandler.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport/Esmtp/Authenticator.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/NullTransport.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Signers/DomainKeySigner.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Signers/OpenDKIMSigner.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Signers/DKIMSigner.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Signers/SMimeSigner.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Signers/HeaderSigner.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Signers/BodySigner.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Spool.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/EmbeddedFile.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Image.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/StreamFilter.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Encoder/Base64Encoder.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Encoder/QpEncoder.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Encoder/Rfc2231Encoder.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/IoException.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/TransportException.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Filterable.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/AddressEncoderException.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/KeyCache/NullKeyCache.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/KeyCache/KeyCacheInputStream.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/KeyCache/ArrayKeyCache.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/KeyCache/SimpleKeyCacheInputStream.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/KeyCache/DiskKeyCache.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/ByteStream/FileByteStream.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/ByteStream/AbstractFilterableInputStream.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/ByteStream/ArrayByteStream.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/ByteStream/TemporaryFileByteStream.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/KeyCache.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/CharacterStream/ArrayCharacterStream.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/CharacterStream/NgCharacterStream.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/MemorySpool.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Transport.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/RfcComplianceException.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/AddressEncoder.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/CharacterReaderFactory.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/LoadBalancedTransport.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/InputByteStream.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/ReplacementFilterFactory.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Attachment.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/FileStream.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/FileSpool.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/SwiftException.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/MimePart.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/ConfigurableSpool.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/DependencyContainer.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Encoder.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/IdGenerator.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/SmtpTransport.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mailer/RecipientIterator.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Mailer/ArrayRecipientIterator.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/SpoolTransport.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/FailoverTransport.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Events/Event.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Events/CommandEvent.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Events/ResponseListener.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Events/CommandListener.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Events/SendListener.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Events/TransportExceptionEvent.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Events/EventDispatcher.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Events/ResponseEvent.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Events/SimpleEventDispatcher.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Events/TransportChangeListener.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Events/TransportChangeEvent.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Events/EventListener.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Events/SendEvent.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Events/TransportExceptionListener.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/Events/EventObject.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/OutputByteStream.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/CharacterReader/GenericFixedWidthReader.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/CharacterReader/UsAsciiReader.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/classes/Swift/CharacterReader/Utf8Reader.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/swift_required.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/mime_types.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/swiftmailer_generate_mimes_config.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/lib/preferences.php',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/.gitattributes',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/LICENSE',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/README.md',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/CHANGES',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/composer.json',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/.github/ISSUE_TEMPLATE.md',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/.github/workflows/tests.yml',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/.github/PULL_REQUEST_TEMPLATE.md',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/.gitignore',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/doc/introduction.rst',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/doc/messages.rst',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/doc/sending.rst',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/doc/headers.rst',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/doc/plugins.rst',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/doc/index.rst',
            'sources_custom/swift_mailer/vendor/swiftmailer/swiftmailer/doc/japanese.rst',
            'sources_custom/swift_mailer/vendor/composer/autoload_classmap.php',
            'sources_custom/swift_mailer/vendor/composer/InstalledVersions.php',
            'sources_custom/swift_mailer/vendor/composer/LICENSE',
            'sources_custom/swift_mailer/vendor/composer/installed.json',
            'sources_custom/swift_mailer/vendor/composer/autoload_psr4.php',
            'sources_custom/swift_mailer/vendor/composer/autoload_namespaces.php',
            'sources_custom/swift_mailer/vendor/composer/ClassLoader.php',
            'sources_custom/swift_mailer/vendor/composer/autoload_files.php',
            'sources_custom/swift_mailer/vendor/composer/autoload_static.php',
            'sources_custom/swift_mailer/vendor/composer/platform_check.php',
            'sources_custom/swift_mailer/vendor/composer/installed.php',
            'sources_custom/swift_mailer/vendor/composer/autoload_real.php',
            'sources_custom/swift_mailer/composer.lock',
        ];
    }
}
