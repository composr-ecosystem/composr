<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    imap
 */

/**
 * Hook class.
 */
class Hook_addon_registry_imap
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
     * Get the version of Composr this addon is for (used in generating the TAR filename).
     *
     * @return float Version number
     */
    public function get_version() : float
    {
        return cms_version_number();
    }

    /**
     * Get the minimum required version of the website software needed to use this addon.
     *
     * @return float Minimum required website software version
     */
    public function get_min_cms_version() : float
    {
        return 11.0;
    }

    /**
     * Get the maximum compatible version of the website software to use this addon.
     *
     * @return ?float Maximum compatible website software version (null: no maximum version currently)
     */
    public function get_max_cms_version() : ?float
    {
        return null;
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Development';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Patrick Schmalstig';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [
            'Javanile',
            'Francesco Bianco',
            'Other javanile/php-imap2 contributors',
            'RoundCube contributors including Aleksander Machniak, Ryo Chijiiwa',
        ];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'GNU General Public License v3.0';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'This addon adds an actively-maintained drop-in replacement for the questionably-maintained PHP IMAP extension by using javanile php-imap2. This addon is also useful for those without the PHP IMAP extension available to them. In the future, we have to be able to add oAuth support, which is supported by this library.';
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
            'recommends' => [ // These addons use IMAP to some optional capacity
                'cns_forum',
                'tickets',
                'ecommerce',
                'newsletter'
            ],
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
            'sources_custom/hooks/systems/addon_registry/imap.php',
            'sources_custom/imap.php',

            'sources_custom/imap/composer.json',
            'sources_custom/imap/composer.lock',
            'sources_custom/imap/vendor/autoload.php',
            'sources_custom/imap/vendor/composer/ClassLoader.php',
            'sources_custom/imap/vendor/composer/LICENSE',
            'sources_custom/imap/vendor/composer/autoload_classmap.php',
            'sources_custom/imap/vendor/composer/autoload_files.php',
            'sources_custom/imap/vendor/composer/autoload_namespaces.php',
            'sources_custom/imap/vendor/composer/autoload_psr4.php',
            'sources_custom/imap/vendor/composer/autoload_real.php',
            'sources_custom/imap/vendor/composer/autoload_static.php',
            'sources_custom/imap/vendor/composer/installed.json',
            'sources_custom/imap/vendor/guzzlehttp/psr7/CHANGELOG.md',
            'sources_custom/imap/vendor/guzzlehttp/psr7/LICENSE',
            'sources_custom/imap/vendor/guzzlehttp/psr7/README.md',
            'sources_custom/imap/vendor/guzzlehttp/psr7/composer.json',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/AppendStream.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/BufferStream.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/CachingStream.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/DroppingStream.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/Exception/MalformedUriException.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/FnStream.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/Header.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/HttpFactory.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/InflateStream.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/LazyOpenStream.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/LimitStream.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/Message.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/MessageTrait.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/MimeType.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/MultipartStream.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/NoSeekStream.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/PumpStream.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/Query.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/Request.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/Response.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/Rfc7230.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/ServerRequest.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/Stream.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/StreamDecoratorTrait.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/StreamWrapper.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/UploadedFile.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/Uri.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/UriComparator.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/UriNormalizer.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/UriResolver.php',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/Utils.php',
            'sources_custom/imap/vendor/javanile/php-imap2/CHANGELOG.md',
            'sources_custom/imap/vendor/javanile/php-imap2/LICENSE',
            'sources_custom/imap/vendor/javanile/php-imap2/README.md',
            'sources_custom/imap/vendor/javanile/php-imap2/bootstrap.php',
            'sources_custom/imap/vendor/javanile/php-imap2/composer.json',
            'sources_custom/imap/vendor/javanile/php-imap2/contrib/google-access-token.php',
            'sources_custom/imap/vendor/javanile/php-imap2/contrib/google-access-token.sh',
            'sources_custom/imap/vendor/javanile/php-imap2/contrib/refresh-access-token.sh',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Acl.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/BodyStructure.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Connection.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Errors.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Functions.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/HeaderInfo.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/ImapHelpers.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Mail.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Mailbox.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Message.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Polyfill.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Roundcube/Charset.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Roundcube/ImapClient.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Roundcube/MessageHeader.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Roundcube/MessageHeaderSorter.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Roundcube/Mime.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Roundcube/ResultIndex.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Roundcube/ResultThread.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Roundcube/Utils.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Thread.php',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Timeout.php',
            'sources_custom/imap/vendor/pimple/pimple/.github/workflows/tests.yml',
            'sources_custom/imap/vendor/pimple/pimple/.gitignore',
            'sources_custom/imap/vendor/pimple/pimple/.php_cs.dist',
            'sources_custom/imap/vendor/pimple/pimple/CHANGELOG',
            'sources_custom/imap/vendor/pimple/pimple/LICENSE',
            'sources_custom/imap/vendor/pimple/pimple/README.rst',
            'sources_custom/imap/vendor/pimple/pimple/composer.json',
            'sources_custom/imap/vendor/pimple/pimple/phpunit.xml.dist',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Container.php',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Exception/ExpectedInvokableException.php',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Exception/FrozenServiceException.php',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Exception/InvalidServiceIdentifierException.php',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Exception/UnknownIdentifierException.php',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Psr11/Container.php',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Psr11/ServiceLocator.php',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/ServiceIterator.php',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/ServiceProviderInterface.php',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Tests/Fixtures/Invokable.php',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Tests/Fixtures/NonInvokable.php',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Tests/Fixtures/PimpleServiceProvider.php',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Tests/Fixtures/Service.php',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Tests/PimpleServiceProviderInterfaceTest.php',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Tests/PimpleTest.php',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Tests/Psr11/ContainerTest.php',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Tests/Psr11/ServiceLocatorTest.php',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Tests/ServiceIteratorTest.php',
            'sources_custom/imap/vendor/psr/container/.gitignore',
            'sources_custom/imap/vendor/psr/container/LICENSE',
            'sources_custom/imap/vendor/psr/container/README.md',
            'sources_custom/imap/vendor/psr/container/composer.json',
            'sources_custom/imap/vendor/psr/container/src/ContainerExceptionInterface.php',
            'sources_custom/imap/vendor/psr/container/src/ContainerInterface.php',
            'sources_custom/imap/vendor/psr/container/src/NotFoundExceptionInterface.php',
            'sources_custom/imap/vendor/psr/http-factory/.gitignore',
            'sources_custom/imap/vendor/psr/http-factory/.pullapprove.yml',
            'sources_custom/imap/vendor/psr/http-factory/LICENSE',
            'sources_custom/imap/vendor/psr/http-factory/README.md',
            'sources_custom/imap/vendor/psr/http-factory/composer.json',
            'sources_custom/imap/vendor/psr/http-factory/src/RequestFactoryInterface.php',
            'sources_custom/imap/vendor/psr/http-factory/src/ResponseFactoryInterface.php',
            'sources_custom/imap/vendor/psr/http-factory/src/ServerRequestFactoryInterface.php',
            'sources_custom/imap/vendor/psr/http-factory/src/StreamFactoryInterface.php',
            'sources_custom/imap/vendor/psr/http-factory/src/UploadedFileFactoryInterface.php',
            'sources_custom/imap/vendor/psr/http-factory/src/UriFactoryInterface.php',
            'sources_custom/imap/vendor/psr/http-message/CHANGELOG.md',
            'sources_custom/imap/vendor/psr/http-message/LICENSE',
            'sources_custom/imap/vendor/psr/http-message/README.md',
            'sources_custom/imap/vendor/psr/http-message/composer.json',
            'sources_custom/imap/vendor/psr/http-message/src/MessageInterface.php',
            'sources_custom/imap/vendor/psr/http-message/src/RequestInterface.php',
            'sources_custom/imap/vendor/psr/http-message/src/ResponseInterface.php',
            'sources_custom/imap/vendor/psr/http-message/src/ServerRequestInterface.php',
            'sources_custom/imap/vendor/psr/http-message/src/StreamInterface.php',
            'sources_custom/imap/vendor/psr/http-message/src/UploadedFileInterface.php',
            'sources_custom/imap/vendor/psr/http-message/src/UriInterface.php',
            'sources_custom/imap/vendor/ralouphie/getallheaders/LICENSE',
            'sources_custom/imap/vendor/ralouphie/getallheaders/README.md',
            'sources_custom/imap/vendor/ralouphie/getallheaders/composer.json',
            'sources_custom/imap/vendor/ralouphie/getallheaders/src/getallheaders.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Iconv.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/LICENSE',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/README.md',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.big5.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp037.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp1006.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp1026.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp424.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp437.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp500.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp737.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp775.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp850.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp852.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp855.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp856.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp857.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp860.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp861.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp862.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp863.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp864.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp865.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp866.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp869.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp874.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp875.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp932.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp936.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp949.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.cp950.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-1.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-10.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-11.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-13.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-14.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-15.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-16.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-2.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-3.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-4.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-5.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-6.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-7.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-8.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.iso-8859-9.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.koi8-r.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.koi8-u.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.us-ascii.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.windows-1250.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.windows-1251.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.windows-1252.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.windows-1253.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.windows-1254.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.windows-1255.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.windows-1256.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.windows-1257.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/from.windows-1258.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/translit.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/bootstrap.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/bootstrap80.php',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/composer.json',
            'sources_custom/imap/vendor/symfony/polyfill-mbstring/LICENSE',
            'sources_custom/imap/vendor/symfony/polyfill-mbstring/Mbstring.php',
            'sources_custom/imap/vendor/symfony/polyfill-mbstring/README.md',
            'sources_custom/imap/vendor/symfony/polyfill-mbstring/Resources/unidata/lowerCase.php',
            'sources_custom/imap/vendor/symfony/polyfill-mbstring/Resources/unidata/titleCaseRegexp.php',
            'sources_custom/imap/vendor/symfony/polyfill-mbstring/Resources/unidata/upperCase.php',
            'sources_custom/imap/vendor/symfony/polyfill-mbstring/bootstrap.php',
            'sources_custom/imap/vendor/symfony/polyfill-mbstring/bootstrap80.php',
            'sources_custom/imap/vendor/symfony/polyfill-mbstring/composer.json',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/.github/FUNDING.yml',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/.github/workflows/tests.yml',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/LICENSE',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/README.md',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/composer.json',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/composer.lock',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Container.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/AbstractHeader.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/AddressHeader.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/AbstractConsumer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/AddressBaseConsumer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/AddressConsumer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/AddressEmailConsumer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/AddressGroupConsumer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/CommentConsumer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/ConsumerService.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/DateConsumer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/GenericConsumer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/IdBaseConsumer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/IdConsumer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/ParameterConsumer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/QuotedStringConsumer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/Received/DomainConsumer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/Received/GenericReceivedConsumer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/Received/ReceivedDateConsumer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/ReceivedConsumer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/SubjectConsumer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/DateHeader.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/GenericHeader.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/HeaderConsts.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/HeaderFactory.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/IHeader.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/IHeaderPart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/IdHeader.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/MimeEncodedHeader.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/ParameterHeader.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Part/AddressGroupPart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Part/AddressPart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Part/CommentPart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Part/DatePart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Part/HeaderPart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Part/HeaderPartFactory.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Part/LiteralPart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Part/MimeLiteralPart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Part/MimeLiteralPartFactory.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Part/ParameterPart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Part/ReceivedDomainPart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Part/ReceivedPart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Part/SplitParameterToken.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Part/Token.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/ReceivedHeader.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/SubjectHeader.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/IMessage.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/MailMimeParser.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/Factory/IMessagePartFactory.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/Factory/IMimePartFactory.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/Factory/IUUEncodedPartFactory.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/Factory/PartChildrenContainerFactory.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/Factory/PartHeaderContainerFactory.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/Factory/PartStreamContainerFactory.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/Helper/AbstractHelper.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/Helper/GenericHelper.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/Helper/MultipartHelper.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/Helper/PrivacyHelper.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/IMessagePart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/IMimePart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/IMultiPart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/IUUEncodedPart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/MessagePart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/MimePart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/MultiPart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/NonMimePart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/PartChildrenContainer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/PartFilter.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/PartHeaderContainer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/PartStreamContainer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/UUEncodedPart.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/AbstractParser.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/HeaderParser.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/IParser.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/MessageParser.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/MimeParser.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/NonMimeParser.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/ParserManager.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Part/ParserPartChildrenContainer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Part/ParserPartChildrenContainerFactory.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Part/ParserPartStreamContainer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Part/ParserPartStreamContainerFactory.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Part/UUEncodedPartHeaderContainer.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Part/UUEncodedPartHeaderContainerFactory.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/PartBuilder.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/PartBuilderFactory.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Proxy/ParserMessageProxy.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Proxy/ParserMessageProxyFactory.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Proxy/ParserMimePartProxy.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Proxy/ParserMimePartProxyFactory.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Proxy/ParserNonMimeMessageProxy.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Proxy/ParserNonMimeMessageProxyFactory.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Proxy/ParserPartProxy.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Proxy/ParserPartProxyFactory.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Proxy/ParserUUEncodedPartProxy.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Proxy/ParserUUEncodedPartProxyFactory.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Stream/HeaderStream.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Stream/MessagePartStream.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Stream/StreamFactory.php',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/version.txt',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/.github/FUNDING.yml',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/.github/workflows/tests.yml',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/.gitignore',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/.php-cs-fixer.dist.php',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/LICENSE',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/PhpCsFixer.php',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/README.md',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/composer.json',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/phpstan.neon',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/src/MbWrapper.php',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/tests/MbWrapper/MbWrapperTest.php',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/tests/bootstrap.php',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/tests/phpunit.xml',
            'sources_custom/imap/vendor/zbateson/stream-decorators/.github/FUNDING.yml',
            'sources_custom/imap/vendor/zbateson/stream-decorators/.github/workflows/tests.yml',
            'sources_custom/imap/vendor/zbateson/stream-decorators/.php-cs-fixer.dist.php',
            'sources_custom/imap/vendor/zbateson/stream-decorators/LICENSE',
            'sources_custom/imap/vendor/zbateson/stream-decorators/PhpCsFixer.php',
            'sources_custom/imap/vendor/zbateson/stream-decorators/README.md',
            'sources_custom/imap/vendor/zbateson/stream-decorators/composer.json',
            'sources_custom/imap/vendor/zbateson/stream-decorators/phpstan.neon',
            'sources_custom/imap/vendor/zbateson/stream-decorators/src/Base64Stream.php',
            'sources_custom/imap/vendor/zbateson/stream-decorators/src/CharsetStream.php',
            'sources_custom/imap/vendor/zbateson/stream-decorators/src/ChunkSplitStream.php',
            'sources_custom/imap/vendor/zbateson/stream-decorators/src/NonClosingStream.php',
            'sources_custom/imap/vendor/zbateson/stream-decorators/src/PregReplaceFilterStream.php',
            'sources_custom/imap/vendor/zbateson/stream-decorators/src/QuotedPrintableStream.php',
            'sources_custom/imap/vendor/zbateson/stream-decorators/src/SeekingLimitStream.php',
            'sources_custom/imap/vendor/zbateson/stream-decorators/src/UUStream.php',
            'sources_custom/imap/vendor/composer/include_paths.php',
            'sources_custom/imap/vendor/pear/auth_sasl/.gitignore',
            'sources_custom/imap/vendor/pear/auth_sasl/.travis.yml',
            'sources_custom/imap/vendor/pear/auth_sasl/Auth/SASL.php',
            'sources_custom/imap/vendor/pear/auth_sasl/Auth/SASL/Anonymous.php',
            'sources_custom/imap/vendor/pear/auth_sasl/Auth/SASL/Common.php',
            'sources_custom/imap/vendor/pear/auth_sasl/Auth/SASL/CramMD5.php',
            'sources_custom/imap/vendor/pear/auth_sasl/Auth/SASL/DigestMD5.php',
            'sources_custom/imap/vendor/pear/auth_sasl/Auth/SASL/External.php',
            'sources_custom/imap/vendor/pear/auth_sasl/Auth/SASL/Login.php',
            'sources_custom/imap/vendor/pear/auth_sasl/Auth/SASL/Plain.php',
            'sources_custom/imap/vendor/pear/auth_sasl/Auth/SASL/SCRAM.php',
            'sources_custom/imap/vendor/pear/auth_sasl/README.md',
            'sources_custom/imap/vendor/pear/auth_sasl/composer.json',
            'sources_custom/imap/vendor/pear/auth_sasl/package.xml',
            'sources_custom/imap/vendor/pear/auth_sasl/phpunit.xml.dist',
            'sources_custom/imap/vendor/pear/pear_exception/LICENSE',
            'sources_custom/imap/vendor/pear/pear_exception/PEAR/Exception.php',
            'sources_custom/imap/vendor/pear/pear_exception/composer.json',
            'sources_custom/imap/.htaccess',
            'sources_custom/imap/index.html',
            'sources_custom/imap/vendor/.htaccess',
            'sources_custom/imap/vendor/composer/.htaccess',
            'sources_custom/imap/vendor/composer/index.html',
            'sources_custom/imap/vendor/guzzlehttp/psr7/.htaccess',
            'sources_custom/imap/vendor/guzzlehttp/psr7/index.html',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/.htaccess',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/Exception/.htaccess',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/Exception/index.html',
            'sources_custom/imap/vendor/guzzlehttp/psr7/src/index.html',
            'sources_custom/imap/vendor/index.html',
            'sources_custom/imap/vendor/javanile/php-imap2/.htaccess',
            'sources_custom/imap/vendor/javanile/php-imap2/contrib/.htaccess',
            'sources_custom/imap/vendor/javanile/php-imap2/contrib/index.html',
            'sources_custom/imap/vendor/javanile/php-imap2/index.html',
            'sources_custom/imap/vendor/javanile/php-imap2/src/.htaccess',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Roundcube/.htaccess',
            'sources_custom/imap/vendor/javanile/php-imap2/src/Roundcube/index.html',
            'sources_custom/imap/vendor/javanile/php-imap2/src/index.html',
            'sources_custom/imap/vendor/pear/auth_sasl/.htaccess',
            'sources_custom/imap/vendor/pear/auth_sasl/Auth/.htaccess',
            'sources_custom/imap/vendor/pear/auth_sasl/Auth/SASL/.htaccess',
            'sources_custom/imap/vendor/pear/auth_sasl/Auth/SASL/index.html',
            'sources_custom/imap/vendor/pear/auth_sasl/Auth/index.html',
            'sources_custom/imap/vendor/pear/auth_sasl/index.html',
            'sources_custom/imap/vendor/pear/pear_exception/.htaccess',
            'sources_custom/imap/vendor/pear/pear_exception/PEAR/.htaccess',
            'sources_custom/imap/vendor/pear/pear_exception/PEAR/index.html',
            'sources_custom/imap/vendor/pear/pear_exception/index.html',
            'sources_custom/imap/vendor/pimple/pimple/.github/workflows/.htaccess',
            'sources_custom/imap/vendor/pimple/pimple/.github/workflows/index.html',
            'sources_custom/imap/vendor/pimple/pimple/.htaccess',
            'sources_custom/imap/vendor/pimple/pimple/index.html',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/.htaccess',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Exception/.htaccess',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Exception/index.html',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Psr11/.htaccess',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Psr11/index.html',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Tests/.htaccess',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Tests/Fixtures/.htaccess',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Tests/Fixtures/index.html',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Tests/Psr11/.htaccess',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Tests/Psr11/index.html',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/Tests/index.html',
            'sources_custom/imap/vendor/pimple/pimple/src/Pimple/index.html',
            'sources_custom/imap/vendor/psr/container/.htaccess',
            'sources_custom/imap/vendor/psr/container/index.html',
            'sources_custom/imap/vendor/psr/container/src/.htaccess',
            'sources_custom/imap/vendor/psr/container/src/index.html',
            'sources_custom/imap/vendor/psr/http-factory/.htaccess',
            'sources_custom/imap/vendor/psr/http-factory/index.html',
            'sources_custom/imap/vendor/psr/http-factory/src/.htaccess',
            'sources_custom/imap/vendor/psr/http-factory/src/index.html',
            'sources_custom/imap/vendor/psr/http-message/.htaccess',
            'sources_custom/imap/vendor/psr/http-message/index.html',
            'sources_custom/imap/vendor/psr/http-message/src/.htaccess',
            'sources_custom/imap/vendor/psr/http-message/src/index.html',
            'sources_custom/imap/vendor/ralouphie/getallheaders/.htaccess',
            'sources_custom/imap/vendor/ralouphie/getallheaders/index.html',
            'sources_custom/imap/vendor/ralouphie/getallheaders/src/.htaccess',
            'sources_custom/imap/vendor/ralouphie/getallheaders/src/index.html',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/.htaccess',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/.htaccess',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/Resources/charset/index.html',
            'sources_custom/imap/vendor/symfony/polyfill-iconv/index.html',
            'sources_custom/imap/vendor/symfony/polyfill-mbstring/.htaccess',
            'sources_custom/imap/vendor/symfony/polyfill-mbstring/Resources/unidata/.htaccess',
            'sources_custom/imap/vendor/symfony/polyfill-mbstring/Resources/unidata/index.html',
            'sources_custom/imap/vendor/symfony/polyfill-mbstring/index.html',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/.github/.htaccess',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/.github/index.html',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/.github/workflows/.htaccess',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/.github/workflows/index.html',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/.htaccess',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/index.html',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/.htaccess',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/.htaccess',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/.htaccess',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/Received/.htaccess',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/Received/index.html',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Consumer/index.html',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Part/.htaccess',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/Part/index.html',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Header/index.html',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/.htaccess',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/Factory/.htaccess',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/Factory/index.html',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/Helper/.htaccess',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/Helper/index.html',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Message/index.html',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/.htaccess',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Part/.htaccess',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Part/index.html',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Proxy/.htaccess',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/Proxy/index.html',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Parser/index.html',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Stream/.htaccess',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/Stream/index.html',
            'sources_custom/imap/vendor/zbateson/mail-mime-parser/src/index.html',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/.github/.htaccess',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/.github/index.html',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/.github/workflows/.htaccess',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/.github/workflows/index.html',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/.htaccess',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/index.html',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/src/.htaccess',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/src/index.html',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/tests/.htaccess',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/tests/MbWrapper/.htaccess',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/tests/MbWrapper/index.html',
            'sources_custom/imap/vendor/zbateson/mb-wrapper/tests/index.html',
            'sources_custom/imap/vendor/zbateson/stream-decorators/.github/.htaccess',
            'sources_custom/imap/vendor/zbateson/stream-decorators/.github/index.html',
            'sources_custom/imap/vendor/zbateson/stream-decorators/.github/workflows/.htaccess',
            'sources_custom/imap/vendor/zbateson/stream-decorators/.github/workflows/index.html',
            'sources_custom/imap/vendor/zbateson/stream-decorators/.htaccess',
            'sources_custom/imap/vendor/zbateson/stream-decorators/index.html',
            'sources_custom/imap/vendor/zbateson/stream-decorators/src/.htaccess',
            'sources_custom/imap/vendor/zbateson/stream-decorators/src/index.html',
        ];
    }
}
