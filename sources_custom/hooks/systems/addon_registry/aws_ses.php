<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    aws_ses
 */

/**
 * Hook class.
 */
class Hook_addon_registry_aws_ses
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
        return 'Chris Graham';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return ['Amazon'];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Apache license';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'This addon is a handler to remove bounce messages detected within the Amazon SES e-mail framework.

[title]Amazon SES[/title]

Amazon SES allows very high volume bulk e-mailing, while conventional web hosts will put low limits on how many e-mails you can send. SES is by far the best priced bulk e-mailing provider.

To configure SES we recommend you specifically set the newsletter configuration options so that only newsletters send via your Amazon SES SMTP server. This has a number of advantages:
1) You can make use of the SES sandbox to test your infrastructure separate to your other e-mailing activities
2) SES is subject to an approval step, so you can\'t switch over to it immediately anyway
3) SES has particular policies that you must comply with, and if they judge you as non-compliant you don\'t want all your e-mail to stop
4) Usually your host-based e-mail is free, while SES is paid -- so it makes sense to only use SES for when you really do need the high-volume stuff
5) SES is more likely to be on spam blocklists, so it makes sense to isolate its use to bulk e-mail only
6) Putting your bulk e-mail through a separate queue will avoid clogging up the delivery of your other e-mails, which may be more time-sensitive

To use SES via SMTP, you need to use TLS, which means you need the [tt]better_mail[/tt] addon.

It is not optional that you have to remove bounced addresses from your newsletter: Amazon may penalise you if you don\'t. You therefore need to listen to the bounces that Amazon picks up.
You need to:
1) Set up an Amazon SNS topic
2) Set up an HTTP subscription to the topic to [tt]http://baseurl/data_custom/amazon_sns_topic_handler.php[/tt]
3) Set up SNS to publish to the topic
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
            'requires' => [
                'PHP curl extension',
                'PHP openssl extension',
            ],
            'recommends' => [
                'better_mail',
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
        return 'themes/default/images/icons/admin/tool.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'data_custom/amazon_sns_topic_handler.php',
            'sources_custom/aws_ses.php',
            'sources_custom/aws_ses/.htaccess',
            'sources_custom/aws_ses/composer.json',
            'sources_custom/aws_ses/composer.lock',
            'sources_custom/aws_ses/index.html',
            'sources_custom/aws_ses/vendor/.htaccess',
            'sources_custom/aws_ses/vendor/autoload.php',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/.github/.htaccess',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/.github/ISSUE_TEMPLATE/.htaccess',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/.github/ISSUE_TEMPLATE/feature_request.md',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/.github/ISSUE_TEMPLATE/general_issue.md',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/.github/ISSUE_TEMPLATE/index.html',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/.github/PULL_REQUEST_TEMPLATE.md',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/.github/index.html',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/.gitignore',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/.htaccess',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/.travis.yml',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/CODE_OF_CONDUCT.md',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/CONTRIBUTING.md',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/LICENSE.md',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/README.md',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/composer.json',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/index.html',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/phpunit.xml.dist',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/src/.htaccess',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/src/Exception/.htaccess',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/src/Exception/InvalidSnsMessageException.php',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/src/Exception/index.html',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/src/Message.php',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/src/MessageValidator.php',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/src/index.html',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/tests/.htaccess',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/tests/FunctionalValidationsTest.php',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/tests/MessageTest.php',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/tests/MessageValidatorTest.php',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/tests/MockPhpStream.php',
            'sources_custom/aws_ses/vendor/aws/aws-php-sns-message-validator/tests/index.html',
            'sources_custom/aws_ses/vendor/composer/.htaccess',
            'sources_custom/aws_ses/vendor/composer/ClassLoader.php',
            'sources_custom/aws_ses/vendor/composer/LICENSE',
            'sources_custom/aws_ses/vendor/composer/autoload_classmap.php',
            'sources_custom/aws_ses/vendor/composer/autoload_namespaces.php',
            'sources_custom/aws_ses/vendor/composer/autoload_psr4.php',
            'sources_custom/aws_ses/vendor/composer/autoload_real.php',
            'sources_custom/aws_ses/vendor/composer/autoload_static.php',
            'sources_custom/aws_ses/vendor/composer/index.html',
            'sources_custom/aws_ses/vendor/composer/installed.json',
            'sources_custom/aws_ses/vendor/index.html',
            'sources_custom/aws_ses/vendor/psr/http-message/.htaccess',
            'sources_custom/aws_ses/vendor/psr/http-message/CHANGELOG.md',
            'sources_custom/aws_ses/vendor/psr/http-message/LICENSE',
            'sources_custom/aws_ses/vendor/psr/http-message/README.md',
            'sources_custom/aws_ses/vendor/psr/http-message/composer.json',
            'sources_custom/aws_ses/vendor/psr/http-message/index.html',
            'sources_custom/aws_ses/vendor/psr/http-message/src/.htaccess',
            'sources_custom/aws_ses/vendor/psr/http-message/src/MessageInterface.php',
            'sources_custom/aws_ses/vendor/psr/http-message/src/RequestInterface.php',
            'sources_custom/aws_ses/vendor/psr/http-message/src/ResponseInterface.php',
            'sources_custom/aws_ses/vendor/psr/http-message/src/ServerRequestInterface.php',
            'sources_custom/aws_ses/vendor/psr/http-message/src/StreamInterface.php',
            'sources_custom/aws_ses/vendor/psr/http-message/src/UploadedFileInterface.php',
            'sources_custom/aws_ses/vendor/psr/http-message/src/UriInterface.php',
            'sources_custom/aws_ses/vendor/psr/http-message/src/index.html',
            'sources_custom/hooks/systems/addon_registry/aws_ses.php',
        ];
    }
}
