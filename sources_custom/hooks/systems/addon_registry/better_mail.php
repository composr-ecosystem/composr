<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

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
     * Get a list of file permissions to set
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return array();
    }

    /**
     * Get the version of Composr this addon is for
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the addon category
     *
     * @return string The category
     */
    public function get_category()
    {
        return 'Third Party Integration';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array();
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Replaces Composr\'s built in mailer with one based around Swift Mailer. This may help workaround problems with buggy/complex SMTP servers, or ones that require SSL (e.g. gmail). If you\'re not have mail problems there\'s no point using this.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array();
    }

    /**
     * Get a mapping of dependency types
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array(),
            'recommends' => array(),
            'conflicts_with' => array()
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/48x48/menu/_generic_admin/component.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/better_mail.php',
            'sources_custom/mail.php',
            'sources_custom/swift_mailer/.htaccess',
            'sources_custom/swift_mailer/CHANGES',
            'sources_custom/swift_mailer/index.html',
            'sources_custom/swift_mailer/lib/.htaccess',
            'sources_custom/swift_mailer/lib/classes/.htaccess',
            'sources_custom/swift_mailer/lib/classes/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/Attachment.php',
            'sources_custom/swift_mailer/lib/classes/Swift/ByteStream/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/ByteStream/AbstractFilterableInputStream.php',
            'sources_custom/swift_mailer/lib/classes/Swift/ByteStream/ArrayByteStream.php',
            'sources_custom/swift_mailer/lib/classes/Swift/ByteStream/FileByteStream.php',
            'sources_custom/swift_mailer/lib/classes/Swift/ByteStream/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/ByteStream/TemporaryFileByteStream.php',
            'sources_custom/swift_mailer/lib/classes/Swift/CharacterReader/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/CharacterReader/GenericFixedWidthReader.php',
            'sources_custom/swift_mailer/lib/classes/Swift/CharacterReader/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/CharacterReader/UsAsciiReader.php',
            'sources_custom/swift_mailer/lib/classes/Swift/CharacterReader/Utf8Reader.php',
            'sources_custom/swift_mailer/lib/classes/Swift/CharacterReader.php',
            'sources_custom/swift_mailer/lib/classes/Swift/CharacterReaderFactory/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/CharacterReaderFactory/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/CharacterReaderFactory/SimpleCharacterReaderFactory.php',
            'sources_custom/swift_mailer/lib/classes/Swift/CharacterReaderFactory.php',
            'sources_custom/swift_mailer/lib/classes/Swift/CharacterStream/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/CharacterStream/ArrayCharacterStream.php',
            'sources_custom/swift_mailer/lib/classes/Swift/CharacterStream/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/CharacterStream/NgCharacterStream.php',
            'sources_custom/swift_mailer/lib/classes/Swift/CharacterStream.php',
            'sources_custom/swift_mailer/lib/classes/Swift/ConfigurableSpool.php',
            'sources_custom/swift_mailer/lib/classes/Swift/DependencyContainer.php',
            'sources_custom/swift_mailer/lib/classes/Swift/DependencyException.php',
            'sources_custom/swift_mailer/lib/classes/Swift/EmbeddedFile.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Encoder/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/Encoder/Base64Encoder.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Encoder/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/Encoder/QpEncoder.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Encoder/Rfc2231Encoder.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Encoder.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Encoding.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Events/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/Events/CommandEvent.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Events/CommandListener.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Events/Event.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Events/EventDispatcher.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Events/EventListener.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Events/EventObject.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Events/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/Events/ResponseEvent.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Events/ResponseListener.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Events/SendEvent.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Events/SendListener.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Events/SimpleEventDispatcher.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Events/TransportChangeEvent.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Events/TransportChangeListener.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Events/TransportExceptionEvent.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Events/TransportExceptionListener.php',
            'sources_custom/swift_mailer/lib/classes/Swift/FailoverTransport.php',
            'sources_custom/swift_mailer/lib/classes/Swift/FileSpool.php',
            'sources_custom/swift_mailer/lib/classes/Swift/FileStream.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Filterable.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Image.php',
            'sources_custom/swift_mailer/lib/classes/Swift/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/InputByteStream.php',
            'sources_custom/swift_mailer/lib/classes/Swift/IoException.php',
            'sources_custom/swift_mailer/lib/classes/Swift/KeyCache/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/KeyCache/ArrayKeyCache.php',
            'sources_custom/swift_mailer/lib/classes/Swift/KeyCache/DiskKeyCache.php',
            'sources_custom/swift_mailer/lib/classes/Swift/KeyCache/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/KeyCache/KeyCacheInputStream.php',
            'sources_custom/swift_mailer/lib/classes/Swift/KeyCache/NullKeyCache.php',
            'sources_custom/swift_mailer/lib/classes/Swift/KeyCache/SimpleKeyCacheInputStream.php',
            'sources_custom/swift_mailer/lib/classes/Swift/KeyCache.php',
            'sources_custom/swift_mailer/lib/classes/Swift/LoadBalancedTransport.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mailer/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/Mailer/ArrayRecipientIterator.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mailer/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/Mailer/RecipientIterator.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mailer.php',
            'sources_custom/swift_mailer/lib/classes/Swift/MailTransport.php',
            'sources_custom/swift_mailer/lib/classes/Swift/MemorySpool.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Message.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/Attachment.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/CharsetObserver.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/ContentEncoder/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/ContentEncoder/Base64ContentEncoder.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/ContentEncoder/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/ContentEncoder/NativeQpContentEncoder.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/ContentEncoder/PlainContentEncoder.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/ContentEncoder/QpContentEncoder.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/ContentEncoder/QpContentEncoderProxy.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/ContentEncoder/RawContentEncoder.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/ContentEncoder.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/EmbeddedFile.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/EncodingObserver.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/Grammar.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/Header.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/HeaderEncoder/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/HeaderEncoder/Base64HeaderEncoder.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/HeaderEncoder/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/HeaderEncoder/QpHeaderEncoder.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/HeaderEncoder.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/HeaderFactory.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/Headers/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/Headers/AbstractHeader.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/Headers/DateHeader.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/Headers/IdentificationHeader.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/Headers/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/Headers/MailboxHeader.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/Headers/OpenDKIMHeader.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/Headers/ParameterizedHeader.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/Headers/PathHeader.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/Headers/UnstructuredHeader.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/HeaderSet.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/Message.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/MimeEntity.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/MimePart.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/ParameterizedHeader.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/SimpleHeaderFactory.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/SimpleHeaderSet.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/SimpleMessage.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Mime/SimpleMimeEntity.php',
            'sources_custom/swift_mailer/lib/classes/Swift/MimePart.php',
            'sources_custom/swift_mailer/lib/classes/Swift/NullTransport.php',
            'sources_custom/swift_mailer/lib/classes/Swift/OutputByteStream.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/AntiFloodPlugin.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/BandwidthMonitorPlugin.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/Decorator/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/Decorator/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/Decorator/Replacements.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/DecoratorPlugin.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/ImpersonatePlugin.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/Logger.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/LoggerPlugin.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/Loggers/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/Loggers/ArrayLogger.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/Loggers/EchoLogger.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/Loggers/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/MessageLogger.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/Pop/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/Pop/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/Pop/Pop3Connection.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/Pop/Pop3Exception.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/PopBeforeSmtpPlugin.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/RedirectingPlugin.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/Reporter.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/ReporterPlugin.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/Reporters/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/Reporters/HitReporter.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/Reporters/HtmlReporter.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/Reporters/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/Sleeper.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/ThrottlerPlugin.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Plugins/Timer.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Preferences.php',
            'sources_custom/swift_mailer/lib/classes/Swift/ReplacementFilterFactory.php',
            'sources_custom/swift_mailer/lib/classes/Swift/RfcComplianceException.php',
            'sources_custom/swift_mailer/lib/classes/Swift/SendmailTransport.php',
            'sources_custom/swift_mailer/lib/classes/Swift/SignedMessage.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Signer.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Signers/BodySigner.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Signers/DKIMSigner.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Signers/DomainKeySigner.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Signers/HeaderSigner.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Signers/OpenDKIMSigner.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Signers/SMimeSigner.php',
            'sources_custom/swift_mailer/lib/classes/Swift/SmtpTransport.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Spool.php',
            'sources_custom/swift_mailer/lib/classes/Swift/SpoolTransport.php',
            'sources_custom/swift_mailer/lib/classes/Swift/StreamFilter.php',
            'sources_custom/swift_mailer/lib/classes/Swift/StreamFilters/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/StreamFilters/ByteArrayReplacementFilter.php',
            'sources_custom/swift_mailer/lib/classes/Swift/StreamFilters/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/StreamFilters/StringReplacementFilter.php',
            'sources_custom/swift_mailer/lib/classes/Swift/StreamFilters/StringReplacementFilterFactory.php',
            'sources_custom/swift_mailer/lib/classes/Swift/SwiftException.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/AbstractSmtpTransport.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/Esmtp/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/Esmtp/Auth/.htaccess',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/Esmtp/Auth/CramMd5Authenticator.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/Esmtp/Auth/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/Esmtp/Auth/LoginAuthenticator.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/Esmtp/Auth/NTLMAuthenticator.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/Esmtp/Auth/PlainAuthenticator.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/Esmtp/Auth/XOAuth2Authenticator.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/Esmtp/Authenticator.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/Esmtp/AuthHandler.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/Esmtp/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/EsmtpHandler.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/EsmtpTransport.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/FailoverTransport.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/index.html',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/IoBuffer.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/LoadBalancedTransport.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/MailInvoker.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/MailTransport.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/NullTransport.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/SendmailTransport.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/SimpleMailInvoker.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/SmtpAgent.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/SpoolTransport.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport/StreamBuffer.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Transport.php',
            'sources_custom/swift_mailer/lib/classes/Swift/TransportException.php',
            'sources_custom/swift_mailer/lib/classes/Swift/Validate.php',
            'sources_custom/swift_mailer/lib/classes/Swift.php',
            'sources_custom/swift_mailer/lib/dependency_maps/.htaccess',
            'sources_custom/swift_mailer/lib/dependency_maps/cache_deps.php',
            'sources_custom/swift_mailer/lib/dependency_maps/index.html',
            'sources_custom/swift_mailer/lib/dependency_maps/message_deps.php',
            'sources_custom/swift_mailer/lib/dependency_maps/mime_deps.php',
            'sources_custom/swift_mailer/lib/dependency_maps/transport_deps.php',
            'sources_custom/swift_mailer/lib/index.html',
            'sources_custom/swift_mailer/lib/mime_types.php',
            'sources_custom/swift_mailer/lib/preferences.php',
            'sources_custom/swift_mailer/lib/swift_init.php',
            'sources_custom/swift_mailer/lib/swift_required.php',
            'sources_custom/swift_mailer/lib/swift_required_pear.php',
            'sources_custom/swift_mailer/lib/swiftmailer_generate_mimes_config.php',
            'sources_custom/swift_mailer/LICENSE',
            'sources_custom/swift_mailer/README',
            'sources_custom/swift_mailer/VERSION',
        );
    }
}
