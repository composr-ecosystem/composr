<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class aws_ses_test_set extends cms_test_case
{
    public function testAwsSesApi()
    {
        if (!addon_installed('aws_ses')) {
            $this->assertTrue(false, 'The aws_ses addon must be installed for this test to run');
            return;
        }

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

        require_code('aws_ses');
        $bounces = amazon_sns_topic_handler_script($data);
        $this->assertTrue($bounces == ['recipient1@example.com', 'recipient2@example.com']);
    }
}
