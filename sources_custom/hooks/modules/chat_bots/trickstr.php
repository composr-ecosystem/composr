<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    trickstr
 */

/*CQC: No check*/

#####################################################
# eliza.php- Final Project: Eliza program written in PHP
#
# The purpose of this program is to mimick the
# infamous Eliza program that was created some
# time ago.
#
# Note: I am not especially good with regular
# expressions at the moment, thus much of this code
# could most likely have been slimmed down.  This is
# also the reason I don't have parts of strings the
# user types stored in a variable and print them out
# when responding to the user (for ex: User: "I have
# a cat".... Eliza: "Oh you have a cat? That's nice.")
# I will be getting a regular expression book for
# X-Mas, however =)
#
# (c) 2001 Wayne Eggert
#-----------------------------------------------------
# DISCLAIMER: THIS CODE MAY NOT BE MODIFIED IN ANY MANNER
# WITHOUT MY CONSENT.  IT SHOULD BE USED FOR EDUCATIONAL
# PURPOSES ONLY.  LEARN, BUT DO NOT STEAL -- MEANING
# DO NOT COPY CODE VERBATIM JUST TO PASS A CLASS IF YOU
# EVER HAVE A SIMILAR PROJECT, ETC.  IT WILL BE WORTH
# YOUR WHILE TO PUT SOME THOUGHT INTO THE TASK AT HAND
# AND COME UP WITH YOUR OWN SOLUTION.  THAT'S ALL I'M
# GOING TO SAY -- OTHERWISE GOD HELP YOU WHEN YOU ENTER
# INTO THE REAL WORLD WITH THOSE HABITS.
######################################################

/**
 * Hook class.
 */
class Hook_chat_bot_trickstr
{
    /**
     * Give ability to reply to any communication.
     *
     * @param  AUTO_LINK $room_id The ID of the chatroom
     * @param  string $string The message used
     * @return ?string Bot reply (null: bot does not handle the command)
     */
    public function reply_to_any_communication(int $room_id, string $string) : ?string
    {
        if (!addon_installed('trickstr')) {
            return null;
        }

        if (strpos(get_db_type(), 'mysql') === false) {
            return null;
        }

        $people = get_chatters_in_room($room_id);
        if (count($people) > 2) {
            return null; // Don't spam if noone is here
        }

        if (running_script('shoutbox')) {
            return null; // Messes up shoutbox
        }

        $is_im = $GLOBALS['SITE_DB']->query_select_value('chat_rooms', 'is_im', ['id' => $room_id]);
        if ($is_im == 1) {
            return null;
        }

        $message = preg_replace('#\[[^\[\]]*\]#U', '', $string);
        if ($message == '') {
            return null;
        }

        return $this->handle_commands($room_id, $message);
    }

    /**
     * Handle hooks supported bot commands. Note multiple bots may support the same commands, and all respond. It is recommended all bots support the command 'help'.
     *
     * @param  AUTO_LINK $room_id The ID of the chatroom
     * @param  string $string The command used. This is just the chat message, so you can encode and recognise your own parameter scheme if you like.
     * @return ?string Bot reply (null: bot does not handle the command)
     */
    protected function handle_commands(int $room_id, string $string) : ?string
    {
        if (!addon_installed('trickstr')) {
            return null;
        }

        if (!is_writable(get_file_base() . '/temp')) {
            return null;
        }

        require_code('developer_tools');
        destrictify();

        if ($string == '((SHAKE))') {
            return null;
        }

        if (file_exists(get_custom_file_base() . '/sources_custom/programe')) { // AliceBot, much better
            if (get_value('trickstr_installed') !== '1') {
                disable_php_memory_limit();

                $old_limit = cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

                $GLOBALS['SITE_DB']->query('DROP TABLE IF EXISTS bot');
                $GLOBALS['SITE_DB']->query('DROP TABLE IF EXISTS bots');
                $GLOBALS['SITE_DB']->query('DROP TABLE IF EXISTS conversationlog');
                $GLOBALS['SITE_DB']->query('DROP TABLE IF EXISTS dstore');
                $GLOBALS['SITE_DB']->query('DROP TABLE IF EXISTS gmcache');
                $GLOBALS['SITE_DB']->query('DROP TABLE IF EXISTS gossip');
                $GLOBALS['SITE_DB']->query('DROP TABLE IF EXISTS patterns');
                $GLOBALS['SITE_DB']->query('DROP TABLE IF EXISTS templates');
                $GLOBALS['SITE_DB']->query('DROP TABLE IF EXISTS thatindex');
                $GLOBALS['SITE_DB']->query('DROP TABLE IF EXISTS thatstack');

                $table_type = (get_value('innodb') == '1') ? 'InnoDB' : 'MyISAM';

                $GLOBALS['SITE_DB']->query("CREATE TABLE bot (
                    id int(11) NOT NULL auto_increment,
                    bot tinyint(4) NOT NULL default '0',
                    name varchar(255) NOT NULL default '',
                    value text NOT NULL,
                    PRIMARY KEY  (id),
                    KEY botname (bot,name(200))
                ) ENGINE=" . $table_type);
                $GLOBALS['SITE_DB']->query("CREATE TABLE bots (
                    id tinyint(3) unsigned NOT NULL auto_increment,
                    botname varchar(255) NOT NULL default '',
                    PRIMARY KEY  (botname(200)),
                    KEY id (id)
                ) ENGINE=" . $table_type);
                $GLOBALS['SITE_DB']->query("CREATE TABLE conversationlog (
                    bot tinyint(3) unsigned NOT NULL default '0',
                    id int(11) NOT NULL auto_increment,
                    input text,
                    response text,
                    uid varchar(255) default NULL,
                    enteredtime timestamp NOT NULL,
                    PRIMARY KEY  (id),
                    KEY botid (bot)
                ) ENGINE=" . $table_type);
                $GLOBALS['SITE_DB']->query("CREATE TABLE dstore (
                    uid varchar(255) default NULL,
                    name text,
                    value text,
                    enteredtime timestamp NOT NULL,
                    id int(11) NOT NULL auto_increment,
                    PRIMARY KEY  (id),
                    KEY nameidx (name(40))
                ) ENGINE=" . $table_type);
                $GLOBALS['SITE_DB']->query("CREATE TABLE gmcache (
                    id int(11) NOT NULL auto_increment,
                    bot tinyint(3) unsigned NOT NULL default '0',
                    template int(11) NOT NULL default '0',
                    inputstarvals text,
                    thatstarvals text,
                    topicstarvals text,
                    patternmatched text,
                    inputmatched text,
                    combined text NOT NULL,
                    PRIMARY KEY  (id),
                    KEY combined (bot,combined(200))
                ) ENGINE=" . $table_type);
                $GLOBALS['SITE_DB']->query("CREATE TABLE gossip (
                    bot tinyint(3) unsigned NOT NULL default '0',
                    gossip text,
                    id int(11) NOT NULL auto_increment,
                    PRIMARY KEY  (id),
                    KEY botidx (bot)
                ) ENGINE=" . $table_type);
                $GLOBALS['SITE_DB']->query("CREATE TABLE patterns (
                    bot tinyint(3) unsigned NOT NULL default '0',
                    id int(11) NOT NULL auto_increment,
                    word varchar(255) default NULL,
                    ordera tinyint(4) NOT NULL default '0',
                    parent int(11) NOT NULL default '0',
                    isend tinyint(4) NOT NULL default '0',
                    PRIMARY KEY  (id),
                    KEY wordparent (parent,word(200)),
                    KEY botid (bot)
                ) ENGINE=" . $table_type);
                $GLOBALS['SITE_DB']->query("CREATE TABLE templates (
                    bot tinyint(3) unsigned NOT NULL default '0',
                    id int(11) NOT NULL default '0',
                    template text NOT NULL,
                    pattern varchar(255) default NULL,
                    that varchar(255) default NULL,
                    topic varchar(255) default NULL,
                    PRIMARY KEY  (id),
                    KEY bot (id)
                ) ENGINE=" . $table_type);
                $GLOBALS['SITE_DB']->query("CREATE TABLE thatindex (
                    uid varchar(255) default NULL,
                    enteredtime timestamp NOT NULL,
                    id int(11) NOT NULL auto_increment,
                    PRIMARY KEY  (id)
                ) ENGINE=" . $table_type);
                $GLOBALS['SITE_DB']->query("CREATE TABLE thatstack (
                    thatid int(11) NOT NULL default '0',
                    id int(11) NOT NULL auto_increment,
                    value varchar(255) default NULL,
                    enteredtime timestamp NOT NULL,
                    PRIMARY KEY  (id)
                ) ENGINE=" . $table_type);

                $fp = '';

                $templatesinserted = 0;

                $depth = [];
                $whaton = '';

                $pattern = '';
                $topic = '';
                $that = '';
                $template = '';

                $startupwhich = '';
                $splitterarray = [];
                $inputarray = [];
                $genderarray = [];
                $personarray = [];
                $person2array = [];

                require_code('programe/botloaderfuncs');

                loadstartup();
                makesubscode();

                set_value('trickstr_installed', '1');

                cms_set_time_limit($old_limit);
            }

            require_code('programe/respond');
            $response = replybotname(str_replace('?', '.', $string), get_session_id(), 'trickstr');

            restrictify();

            if (($response === null) || $response->response == '') {
                return null;
            }
            return '[html]' . $response->response . '[/html]';
        }

        // Eliza...

        // setup initial variables and values
        $kwarray = [];
        $vararray = [];
        $resparray = [];
        $priarray = [];
        $wordarray = [];
        $kwcount = 0;
        $varcount = 0;
        $respcount = 0;
        $syncount = 0;

        // load knowledge file
        $lines_array = cms_file_safe(get_custom_file_base() . '/sources_custom/hooks/modules/chat_bots/knowledge.txt');

        $count = count($lines_array);

        // This for loop goes through the entire knowledge file and places
        // the elements into arrays.  This later allows us to pull the information
        // (ie. key words, variances on the keywords, and responses) out of the
        // arrays.
        for ($x = 0; $x < $count; $x++) {
            $lines_array[$x] = trim($lines_array[$x]);
            $lines_array[$x] = preg_replace('#[\]#', '', $lines_array[$x]);
            if (strstr($lines_array[$x], 'key:')) {
                preg_match('#key: (.*)#i', $lines_array[$x], $kw);
                $kwarray[$kwcount] = cms_strtoupper_ascii($kw[1]);
                $currentkw = $kwcount;
                $kwcount++;
                $varcount = 0; // reset varcount to null
                $respcount = 0; // reset respcount to null
                $pricount = 0; // reset pricount to null
            } else {
                if (strstr($lines_array[$x], 'var:')) {
                    preg_match('#var: (.*)#i', $lines_array[$x], $variance);
                    $vararray[$currentkw][$varcount] = cms_strtoupper_ascii($variance[1]);
                    $varcurrent = $varcount;
                    $varcount++;
                    $respcount = 0;
                } else {
                    if (strstr($lines_array[$x], 'pri:')) {
                        preg_match('#pri: (.*)#i', $lines_array[$x], $priority);
                        $priarray[$currentkw] = $priority[1];
                    } else {
                        if (strstr($lines_array[$x], 'resp:')) {
                            preg_match('#resp: (.*)#i', $lines_array[$x], $response);
                            $resparray[$currentkw][$varcurrent][$respcount] = $response[1];
                            $respcount++;
                        } else {
                            if (strstr($lines_array[$x], 'syn:')) {
                                preg_match('#syn: (.*)#i', $lines_array[$x], $synonym);
                                $synonymarray[$syncount] = cms_strtoupper_ascii($synonym[1]);
                                $syncount++;
                            } else {
                                if (strstr($lines_array[$x], 'goto:')) {
                                    preg_match('#goto: (.*)#i', $lines_array[$x], $goto);
                                    $goto = cms_strtoupper_ascii($goto[1]);
                                    // find the keyword
                                    for ($zcount = 0; $zcount < count($kwarray); $zcount++) {
                                        // if the keyword already exists
                                        if (preg_match('#' . $goto . '#i', $kwarray[$zcount])) {
                                            // then we assign properties of the keyword
                                            $vararray[$currentkw][0] = $kwarray[$currentkw];
                                            $resparray[$currentkw] = $resparray[$zcount];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $y = 0;
        $z = 0;
        $v = 0;
        $bestpriority = -2;
        $originalstring = $string;
        if (!$string) {
            $string = 'hello';
        }
        $string = cms_strtoupper_ascii($string);

        // Figures out what word in the string has the most priority.
        // It can then check words to the left/right of this word depending
        // upon settings in the knowledge.txt file.
        while ($y < count($kwarray)) {
            // remove beginning and trailing white space, breaks, etc
            $string = trim($string);
            // remove puncuation from string
            $string = preg_replace('#[!?,.]#', '', $string);
            // split the string up into seperate words
            $wordarray = explode(' ', $string);
            while ($v < count($wordarray)) {
                if (preg_match('#' . $wordarray[$v] . '$#', $kwarray[$y])) {
                    // find which word holds the most weight in the sentance
                    if ($bestpriority == -2) {
                        $bestpriority = $y;
                    } else {
                        if ($priarray[$bestpriority] < $priarray[$y]) {
                            $bestpriority = $y;
                        }
                    }
                }
                $v++;
            }
            $v = 0;
            $y++;
        }

        // find the variance with the most matching words
        $vcount = 0;
        while ($vcount < count($vararray[$bestpriority])) {
            if (strstr($vararray[$bestpriority][$vcount], '@')) {
                preg_match('#@(.*)#i', $vararray[$bestpriority][$vcount], $syn); // fix this
                $syn = $syn[1];
                for ($x = 0; $x < count($synonymarray); $x++) {
                    if (preg_match('#' . $syn . '#i', cms_strtoupper_ascii($synonymarray[$x]))) {
                        $sarray = explode(' ', $synonymarray[$x]);
                        for ($f = 0; $f < count($sarray); $f++) {
                            $newstring = preg_match('#@(.*)$#', $sarray[$f], $vararray[$bestpriority][$vcount]);
                            // works to this point
                            if (preg_match('#' . $newstring . '$#i', $string)) {
                                $varray = explode(' ', $vararray[$bestpriority][$vcount]);
                                if (count($varray) > $pvarray) {
                                    $bestvariance = $vcount;
                                    $pvarray = count($varray);
                                }
                            }
                        }
                    }
                }
            } else {
                if (preg_match('#' . $vararray[$bestpriority][$vcount], $string) . '#i') {
                    $varray = explode(' ', $vararray[$bestpriority][$vcount]);
                    if (count($varray) > $pvarray) {
                        $bestvariance = $vcount;
                        $pvarray = count($varray);
                    }
                }
            }
            $vcount++;
        }

        // Using the bestpriority (aka the keyword (key:) with the most weight in the sentence)
        // and the bestvariance (aka, the variance (var:) phrase that most fits the context of
        // the original sentence, we form a response.
        if (count($resparray[$bestpriority][$bestvariance]) > 1) {
            $random = mt_rand(0, count($resparray[$bestpriority][$bestvariance]) - 1);
        } else {
            $random = 0;
        }
        $response = $resparray[$bestpriority][$bestvariance][$random];
        if ($response == '') {
            $response = 'Sorry, I don\'t understand what you\'re trying to say.';
        }

        $originalstring = preg_match('#[\]#', '', $originalstring);

        restrictify();

        return $response;
    }
}
