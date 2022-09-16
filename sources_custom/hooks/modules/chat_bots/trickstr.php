<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    trickstr
 */

/*CQC: No check*/

if(!function_exists('ereg'))            { function ereg($pattern, $subject, &$matches = []) { return preg_match('/'.$pattern.'/', $subject, $matches); } }
if(!function_exists('eregi'))           { function eregi($pattern, $subject, &$matches = []) { return preg_match('/'.$pattern.'/i', $subject, $matches); } }
if(!function_exists('ereg_replace'))    { function ereg_replace($pattern, $replacement, $string) { return preg_replace('/'.$pattern.'/', $replacement, $string); } }
if(!function_exists('eregi_replace'))   { function eregi_replace($pattern, $replacement, $string) { return preg_replace('/'.$pattern.'/i', $replacement, $string); } }
if(!function_exists('split'))           { function split($pattern, $subject, $limit = -1) { return preg_split('/'.$pattern.'/', $subject, $limit); } }
if(!function_exists('spliti'))          { function spliti($pattern, $subject, $limit = -1) { return preg_split('/'.$pattern.'/i', $subject, $limit); } }

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
# ©2001 Wayne Eggert
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
     * @param  string $string The message used.
     * @return ?string Bot reply (null: bot does not handle the command)
     */
    function reply_to_any_communication($room_id, $string)
    {
        $people = get_chatters_in_room($room_id);
        if (count($people) > 2) {
            return null; // Don't spam if noone is here
        }

        if (running_script('shoutbox')) {
            return null; // Messes up shoutbox
        }

        $is_im = $GLOBALS['SITE_DB']->query_select_value('chat_rooms', 'is_im', array('id' => $room_id));
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
    function handle_commands($room_id, $string)
    {
        if (!is_writable(get_file_base() . "/safe_mode_temp")) {
            return null;
        }

        require_code('developer_tools');
        destrictify();

        if ($string == '((SHAKE))') {
            return null;
        }

        if (file_exists(get_custom_file_base() . '/sources_custom/programe')) // AliceBot, much better
        {
            if (get_value('trickstr_installed') !== '1') {
                disable_php_memory_limit();

                if (php_function_allowed('set_time_limit')) {
                    @set_time_limit(600);
                }

                $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS bot");
                $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS bots");
                $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS conversationlog");
                $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS dstore");
                $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS gmcache");
                $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS gossip");
                $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS patterns");
                $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS templates");
                $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS thatindex");
                $GLOBALS['SITE_DB']->query("DROP TABLE IF EXISTS thatstack");

                $GLOBALS['SITE_DB']->query("CREATE TABLE bot (
				  id int(11) NOT NULL auto_increment,
				  bot tinyint(4) NOT NULL default '0',
				  name varchar(255) NOT NULL default '',
				  value text NOT NULL,
				  PRIMARY KEY  (id),
				  KEY botname (bot,name(200))
				) ENGINE=MyISAM");
                $GLOBALS['SITE_DB']->query("CREATE TABLE bots (
				  id tinyint(3) unsigned NOT NULL auto_increment,
				  botname varchar(255) NOT NULL default '',
				  PRIMARY KEY  (botname(200)),
				  KEY id (id)
				) ENGINE=MyISAM");
                $GLOBALS['SITE_DB']->query("CREATE TABLE conversationlog (
				  bot tinyint(3) unsigned NOT NULL default '0',
				  id int(11) NOT NULL auto_increment,
				  input text,
				  response text,
				  uid varchar(255) default NULL,
				  enteredtime timestamp NOT NULL,
				  PRIMARY KEY  (id),
				  KEY botid (bot)
				) ENGINE=MyISAM");
                $GLOBALS['SITE_DB']->query("CREATE TABLE dstore (
				  uid varchar(255) default NULL,
				  name text,
				  value text,
				  enteredtime timestamp NOT NULL,
				  id int(11) NOT NULL auto_increment,
				  PRIMARY KEY  (id),
				  KEY nameidx (name(40))
				) ENGINE=MyISAM");
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
				) ENGINE=MyISAM");
                $GLOBALS['SITE_DB']->query("CREATE TABLE gossip (
				  bot tinyint(3) unsigned NOT NULL default '0',
				  gossip text,
				  id int(11) NOT NULL auto_increment,
				  PRIMARY KEY  (id),
				  KEY botidx (bot)
				) ENGINE=MyISAM");
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
				) ENGINE=MyISAM");
                $GLOBALS['SITE_DB']->query("CREATE TABLE templates (
				  bot tinyint(3) unsigned NOT NULL default '0',
				  id int(11) NOT NULL default '0',
				  template text NOT NULL,
				  pattern varchar(255) default NULL,
				  that varchar(255) default NULL,
				  topic varchar(255) default NULL,
				  PRIMARY KEY  (id),
				  KEY bot (id)
				) ENGINE=MyISAM");
                $GLOBALS['SITE_DB']->query("CREATE TABLE thatindex (
				  uid varchar(255) default NULL,
				  enteredtime timestamp NOT NULL,
				  id int(11) NOT NULL auto_increment,
				  PRIMARY KEY  (id)
				) ENGINE=MyISAM");
                $GLOBALS['SITE_DB']->query("CREATE TABLE thatstack (
				  thatid int(11) NOT NULL default '0',
				  id int(11) NOT NULL auto_increment,
				  value varchar(255) default NULL,
				  enteredtime timestamp NOT NULL,
				  PRIMARY KEY  (id)
				) ENGINE=MyISAM");

                $fp = "";

                $templatesinserted = 0;

                $depth = array();
                $whaton = "";

                $pattern = "";
                $topic = "";
                $that = "";
                $template = "";

                $startupwhich = "";
                $splitterarray = array();
                $inputarray = array();
                $genderarray = array();
                $personarray = array();
                $person2array = array();

                require_code('programe/botloaderfuncs');

                loadstartup();
                makesubscode();

                set_value('trickstr_installed', '1');
            }

            require_code('programe/respond');
            $response = replybotname(str_replace('?', '.', $string), get_session_id(), 'trickstr');

            restrictify();

            if (is_null($response) || $response->response == '') {
                return null;
            }
            return '[html]' . $response->response . '[/html]';
        }

        // Eliza...

        // setup initial variables and values
        $kwarray = array();
        $vararray = array();
        $resparray = array();
        $priarray = array();
        $wordarray = array();
        $kwcount = 0;
        $varcount = 0;
        $respcount = 0;
        $syncount = 0;

        mt_srand((double)microtime() * 1000000);

        // load knowledge file
        $lines_array = file(get_custom_file_base() . "/sources_custom/hooks/modules/chat_bots/knowledge.txt");

        $count = count($lines_array);

        // This for loop goes through the entire knowledge file and places
        // the elements into arrays.  This later allows us to pull the information
        // (ie. key words, variances on the keywords, and responses) out of the
        // arrays.
        for ($x = 0; $x < $count; $x++) {
            $lines_array[$x] = trim($lines_array[$x]);
            $lines_array[$x] = ereg_replace("[\]", "", $lines_array[$x]);
            if (strstr($lines_array[$x], "key:")) {
                eregi("key: (.*)", $lines_array[$x], $kw);
                $kwarray[$kwcount] = strtoupper($kw[1]);
                $currentkw = $kwcount;
                $kwcount++;
                $varcount = 0; // reset varcount to null
                $respcount = 0; // reset respcount to null
                $pricount = 0; // reset pricount to null
            } else {
                if (strstr($lines_array[$x], "var:")) {
                    eregi("var: (.*)", $lines_array[$x], $variance);
                    $vararray[$currentkw][$varcount] = strtoupper($variance[1]);
                    $varcurrent = $varcount;
                    $varcount++;
                    $respcount = 0;
                } else {
                    if (strstr($lines_array[$x], "pri:")) {
                        eregi("pri: (.*)", $lines_array[$x], $priority);
                        $priarray[$currentkw] = $priority[1];
                    } else {
                        if (strstr($lines_array[$x], "resp:")) {
                            eregi("resp: (.*)", $lines_array[$x], $response);
                            $resparray[$currentkw][$varcurrent][$respcount] = $response[1];
                            $respcount++;
                        } else {
                            if (strstr($lines_array[$x], "syn:")) {
                                eregi("syn: (.*)", $lines_array[$x], $synonym);
                                $synonymarray[$syncount] = strtoupper($synonym[1]);
                                $syncount++;
                            } else {
                                if (strstr($lines_array[$x], "goto:")) {
                                    eregi("goto: (.*)", $lines_array[$x], $goto);
                                    $goto = strtoupper($goto[1]);
                                    // find the keyword
                                    for ($zcount = 0; $zcount < count($kwarray); $zcount++) {
                                        // if the keyword already exists
                                        if (eregi($goto, $kwarray[$zcount])) {
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
            $string = "hello";
        }
        $string = strtoupper($string);

        // Figures out what word in the string has the most priority.
        // It can then check words to the left/right of this word depending
        // upon settings in the knowledge.txt file.
        while ($y < count($kwarray)) {
            // remove beginning and trailing white space, breaks, etc
            $string = trim($string);
            // remove puncuation from string
            $string = ereg_replace('[!?,.]', '', $string);
            // split the string up into seperate words
            $wordarray = explode(" ", $string);
            while ($v < count($wordarray)) {
                if (eregi($wordarray[$v] . "$", $kwarray[$y])) {
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
            if (strstr($vararray[$bestpriority][$vcount], "@")) {
                eregi("@(.*)", $vararray[$bestpriority][$vcount], $syn); // fix this
                $syn = $syn[1];
                for ($x = 0; $x < count($synonymarray); $x++) {
                    if (eregi($syn, strtoupper($synonymarray[$x]))) {
                        $sarray = explode(" ", $synonymarray[$x]);
                        for ($f = 0; $f < count($sarray); $f++) {
                            $newstring = ereg_replace("@(.*)$", $sarray[$f], $vararray[$bestpriority][$vcount]);
                            // works to this point
                            if (eregi($newstring . "$", $string)) {
                                $varray = explode(" ", $vararray[$bestpriority][$vcount]);
                                if (count($varray) > $pvarray) {
                                    $bestvariance = $vcount;
                                    $pvarray = count($varray);
                                }
                            }
                        }
                    }
                }
            } else {
                if (ereg($vararray[$bestpriority][$vcount], $string)) {
                    $varray = explode(" ", $vararray[$bestpriority][$vcount]);
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
        if ($response == "") {
            $response = "Sorry, I don't understand what you're trying to say.";
        }

        $originalstring = ereg_replace("[\]", "", $originalstring);

        restrictify();

        return $response;
    }
}


