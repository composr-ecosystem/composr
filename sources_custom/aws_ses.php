<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    aws_ses
 */

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;

function init__aws_ses()
{
    require_code('aws_ses/vendor/autoload');
}

function amazon_sns_topic_handler_script()
{
    if (!addon_installed('aws')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('aws')));
    }

    if (!function_exists('curl_init')) {
        warn_exit(do_lang_tempcode('NO_CURL_ON_SERVER'));
    }

    header('X-Robots-Tag: noindex');

    if ($GLOBALS['DEV_MODE'] && get_param_integer('test', 0) == 1) {
        $notification_data = '{
          "notificationType": "Bounce",
          "mail": {
            "timestamp": "2014-05-28T22:40:59.638Z",
            "messageId": "0000014644fe5ef6-9a483358-9170-4cb4-a269-f5dcdf415321-000000",
            "source": "test@ses-example.com",
            "destination": [
              "success@simulator.amazonses.com",
              "recipient@ses-example.com"
            ]
          },
          "bounce": {
             "bounceType":"Permanent",
             "bounceSubType": "General",
             "bouncedRecipients":[
                {
                   "status":"5.0.0",
                   "action":"failed",
                   "diagnosticCode":"smtp; 550 user unknown",
                   "emailAddress":"recipient1@example.com"
                },
                {
                   "status":"4.0.0",
                   "action":"delayed",
                   "emailAddress":"recipient2@example.com"
                }
             ],
             "reportingMTA": "example.com",
             "timestamp":"2012-05-25T14:59:38.605-07:00",
             "feedbackId":"000001378603176d-5a4b5ad9-6f30-4198-a8c3-b1eb0c270a1d-000000"
          }
        }
        ';
        $data = '{
          "Type" : "Notification",
          "MessageId" : "165545c9-2a5c-472c-8df2-7ff2be2b3b1b",
          "Token" : "2336412f37fb687f5d51e6e241d09c805a5a57b30d712f794cc5f6a988666d92768dd60a747ba6f3beb71854e285d6ad02428b09ceece29417f1f02d609c582afbacc99c583a916b9981dd2728f4ae6fdb82efd087cc3b7849e05798d2d2785c03b0879594eeac82c01f235d0e717736",
          "TopicArn" : "arn:aws:sns:us-west-2:123456789012:MyTopic",
          "Message" : ' . json_encode($notification_data) . ',
          "SubscribeURL" : "https://sns.us-west-2.amazonaws.com/?Action=ConfirmSubscription&TopicArn=arn:aws:sns:us-west-2:123456789012:MyTopic&Token=2336412f37fb687f5d51e6e241d09c805a5a57b30d712f794cc5f6a988666d92768dd60a747ba6f3beb71854e285d6ad02428b09ceece29417f1f02d609c582afbacc99c583a916b9981dd2728f4ae6fdb82efd087cc3b7849e05798d2d2785c03b0879594eeac82c01f235d0e717736",
          "Timestamp" : "2012-04-26T20:45:04.751Z",
          "SignatureVersion" : "1",
          "Signature" : "EXAMPLEpH+DcEwjAPg8O9mY8dReBSwksfg2S7WKQcikcNKWLQjwu6A4VbeS0QHVCkhRS7fUQvi2egU3N858fiTDN6bkkOxYDVrY0Ad8L10Hs3zH81mtnPk5uvvolIC1CXGu43obcgFxeL3khZl8IKvO61GWB6jI9b5+gLPoBc1Q=",
          "SigningCertURL" : "https://sns.us-west-2.amazonaws.com/SimpleNotificationService-f3ecfb7224c7233fe7bb5f59f96de52f.pem"
        }
        ';
    } else {
        $data = null;

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

                $bounces = [];

                $bounce = $message_body['bounce'];

                $recipients = $bounce['bouncedRecipients'];
                foreach ($recipients as $recipient) {
                    if ((isset($recipient['action'])) && ($recipient['action'] == 'delayed')) {
                        // Don't consider this a bounce
                    }

                    $bounces[] = $recipient['emailAddress'];
                }

                remove_email_bounces($bounces);
            }
            break;
    }
}
