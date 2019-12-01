<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    better_mail
 */

/*FORCE_ORIGINAL_LOAD_FIRST*/

/**
 * E-mail dispatcher object. Handles the actual delivery of an e-mail over PHP's mail function.
 *
 * @package    core
 */
class Mail_dispatcher_override extends Mail_dispatcher_base
{
    /**
     * Construct e-mail dispatcher.
     *
     * @param  array $advanced_parameters List of advanced parameters
     */
    public function __construct($advanced_parameters = [])
    {
        require_code('swift_mailer/lib/swift_required');

        parent::__construct($advanced_parameters);
    }

    /**
     * Find whether the dispatcher instance is capable of sending e-mails.
     *
     * @param  array $advanced_parameters List of advanced parameters
     * @return boolean Whether the dispatcher instance is capable of sending e-mails
     */
    public function is_dispatcher_available($advanced_parameters)
    {
        $smtp_sockets_use = isset($advanced_parameters['smtp_sockets_use']) ? $advanced_parameters['smtp_sockets_use'] : null; // Whether to use SMTP sockets (null: default configured)
        if ($smtp_sockets_use === null) {
            $smtp_sockets_use = (intval(get_option('smtp_sockets_use')) == 1);
        }
        return $smtp_sockets_use;
    }

    /**
     * Send out the e-mail according to the current dispatcher configuration.
     *
     * @param  string $subject_line The subject of the mail in plain text
     * @param  LONG_TEXT $message_raw The message, as Comcode
     * @param  ?array $to_emails The destination (recipient) e-mail address(es) [array of strings] (null: site staff address)
     * @param  ?mixed $to_names The recipient name(s). Array or string. (null: site name)
     * @param  EMAIL $from_email The from address (blank: site staff address)
     * @param  string $from_name The from name (blank: site name)
     * @return ?array A pair: Whether it worked, and an error message (null: skipped)
     */
    public function dispatch($subject_line, $message_raw, $to_emails = null, $to_names = null, $from_email = '', $from_name = '')
    {
        if ($from_email == '') {
            $from_email = $this->smtp_from_address;
        }

        return parent::dispatch($subject_line, $message_raw, $to_emails, $to_names, $from_email, $from_name);
    }

    /**
     * Implementation-specific e-mail dispatcher, passed with pre-prepared/tidied e-mail component details for us to use.
     *
     * @param  array $to_emails To e-mail addresses
     * @param  array $to_names To names
     * @param  EMAIL $from_email From e-mail address
     * @param  string $from_name From name
     * @param  string $subject_wrapped Subject line
     * @param  string $headers Headers to use
     * @param  string $sending_message Full MIME message
     * @param  string $charset Character set to use
     * @param  string $html_evaluated Full HTML message (is also inside $sending_message, so we won't use this unless we are not using $sending_message)
     * @param  string $message_plain Full text message (is also inside $sending_message, so we won't use this unless we are not using $sending_message)
     * @return array A pair: Whether it worked, and an error message
     */
    protected function _dispatch($to_emails, $to_names, $from_email, $from_name, $subject_wrapped, $headers, $sending_message, $charset, $html_evaluated, $message_plain)
    {
        $worked = true;
        $error = null;

        // Create the Transport
        $transport = (new Swift_SmtpTransport($this->smtp_sockets_host, $this->smtp_sockets_port))
            ->setUsername($this->smtp_sockets_username)
            ->setPassword($this->smtp_sockets_password);
        if (($this->smtp_sockets_port == 419) || ($this->smtp_sockets_port == 465) || ($this->smtp_sockets_port == 587)) {
            $transport->setEncryption('tls');
        }

        // Create the Mailer using your created Transport
        static $mailer = null;
        if ($mailer === null) {
            $mailer = new Swift_Mailer($transport);
        }

        // Create a message and basic address it
        $to_array = [];
        foreach ($to_emails as $i => $_to_email) {
            $to_array[$_to_email] = $to_names[$i];
        }
        $message = new Swift_Message($subject_wrapped);
        if ($this->sender_email !== null) {
            $message->setFrom([$this->sender_email => $from_name]);
        } // else maybe server won't let us set it due to whitelist security, and we must let it use it's default (i.e. accountname@hostname)
        $message
            ->setReplyTo([$from_email => $from_name])
            ->setTo($to_array)
            ->setDate(time())
            ->setPriority($this->priority)
            ->setCharset($charset)
            ->setBody($html_evaluated, 'text/html', $charset);
        if (!$this->in_html) {
            $message->addPart($message_plain, 'text/plain', $charset);
        }
        $message->setCc($this->cc_addresses);
        $message->setBcc($this->bcc_addresses);

        if ((count($to_emails) == 1) && ($this->require_recipient_valid_since !== null)) {
            $headers = $message->getHeaders();
            $_require_recipient_valid_since = date('r', $this->require_recipient_valid_since);
            $headers->addTextHeader('Require-Recipient-Valid-Since', $to_emails[0] . '; ' . $_require_recipient_valid_since);
        }

        // DKIM
        $signer = new Swift_Signers_DKIMSigner(get_option('dkim_private_key'), get_domain(), get_option('dkim_selector'));
        $message->attachSigner($signer);

        // Attachments
        foreach ($this->real_attachments as $r) {
            if (isset($r['path'])) {
                $attachment = Swift_Attachment::fromPath($r['path'], $r['mime']);
            } else {
                $attachment = (new Swift_Attachment())->setContentType($r['mime'])->setBody($r['contents']);
            }
            $attachment->setFilename($r['filename'])->setDisposition('attachment');
            $message->attach($attachment);
        }
        foreach ($this->cid_attachments as $r) {
            $attachment = Swift_Attachment::fromPath($r['path'], $r['mime'])->setFilename($r['filename'])->setDisposition('attachment')->setId($r['cid']);
            $message->attach($attachment);
        }

        // Send the message, and error collection
        $error = '';
        $failures = [];
        try {
            $result = $mailer->send($message, $failures);
        } catch (Exception $e) {
            $error = $e->getMessage();
            $worked = false;
        }
        if (($error == '') && (!$result)) {
            if (empty($failures)) {
                $error = 'Unknown error';
            } else {
                $error = 'Rejected addresses: ' . implode(', ', $failures);
            }
        }

        return [$worked, $error];
    }
}
