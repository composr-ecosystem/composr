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

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;

function init__aws_ses()
{
    require_code('aws_ses/vendor/autoload');
}

function amazon_sns_topic_handler_script($data = null)
{
    if (!addon_installed('aws_ses')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('aws_ses')));
    }

    if (!function_exists('curl_init')) {
        warn_exit(do_lang_tempcode('NO_CURL_ON_SERVER'));
    }

    if ($data === null) {
        header('X-Robots-Tag: noindex');

        // Make sure the request is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Must be POST request');
        }
    }

    try {
        // Create a message from the post data and validate its signature
        if ($data === null) {
            $message = Message::fromRawPostData();

            $validator = new MessageValidator();
            $validator->validate($message);
        } else {
            $message = new Message(json_decode($data, true));
        }
    } catch (Exception $e) {
        // Pretend we're not here if the message is invalid
        http_response_code(404);
        exit($e->getMessage());
    }

    $bounces = [];

    $type = $message['Type'];
    switch ($type) {
        case 'SubscriptionConfirmation':
            http_get_contents($message['SubscribeURL']);
            break;

        case 'Notification':
            $message_body = json_decode($message['Message'], true);
            $notification_type = $message_body['notificationType'];
            if (($notification_type === 'Bounce') && (addon_installed('newsletter'))) {
                require_code('newsletter');

                $bounce = $message_body['bounce'];

                $recipients = $bounce['bouncedRecipients'];
                foreach ($recipients as $recipient) {
                    if ((isset($recipient['action'])) && ($recipient['action'] == 'delayed')) {
                        // Don't consider this a bounce
                    }

                    $bounces[] = $recipient['emailAddress'];
                }

                require_code('newsletter2');
                remove_email_bounces($bounces);
            }
            break;
    }

    return $bounces;
}
