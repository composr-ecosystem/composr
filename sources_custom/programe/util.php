<?php

/*
    Program E
	Copyright 2002, Paul Rydell

	This file is part of Program E.

	Program E is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Program E is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Program E; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * Util.php, Utility functions
 *
 * Contains reusable functions for Program E
 * @author Paul Rydell
 * @copyright 2002
 * @version 0.0.8
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package Interpreter
 */



/**
 * Class to hold the reponse
 *
 * Container class to hold response, input error and a bunch of other
 * bits of information that is used in other functions
 * @author Paul Rydell
 * @copyright 2002
 * @version 0.0.8
 * @package Interpreter
 */
class Response
{
    var $response;
    var $patternsmatched;
	var $inputs;
    var $errors;
	var $timer;
}

// Initialize information for search replace routines
$replacecounter=1;
$aftersearch=array();
$afterreplace=array();



/**
* This function will clean up old data in the database that is not
* needed according to user defined settings.
*
* Deletes entries in the database tables dstore, thatstack, thatstackindex
* and if set also conversationlog.
*
* @uses make_seed()
*
* @return void        nothing, deletes database entires
*/
function cleanup(){

	if ((RANDOMCHANCECLEAN == -1) || (MINUTESTOKEEPDATA == -1)){
		return;
	}

	$randval = mt_rand(1,RANDOMCHANCECLEAN);

	if ($randval==RANDOMCHANCECLEAN){

		if (MINUTESTOKEEPDATA != -1){

			$clean_dstore="delete from dstore where enteredtime < date_add(now(), interval - " . MINUTESTOKEEPDATA . " minute)";
			$clean_thatstack="delete from thatstack where enteredtime < date_add(now(), interval - " . MINUTESTOKEEPDATA . " minute)";
			$clean_thatindex="delete from thatindex where enteredtime < date_add(now(), interval - " . MINUTESTOKEEPDATA . " minute)";

			$selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $GLOBALS['SITE_DB']->connection_write[0], $clean_dstore);
			if ($selectcode){
			}
			$selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $clean_thatstack);
			if ($selectcode){
			}
			$selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $clean_thatindex);
			if ($selectcode){
			}

		}

		if (MINUTESTOKEEPCHATLOG != -1){

			$clean_convlog="delete from conversationlog where enteredtime < date_add(now(), interval - " . MINUTESTOKEEPCHATLOG . " minute) and " . whichbots();

			$selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $clean_convlog);
			if ($selectcode){
			}

		}

	}

}

/**
* Check if a tag is an old style AIML tag.
*
* If it is then return its new name and the fact that it is deprecated. This
* information is not send back through the return value, but by using call-by-reference
* variable &$ttag.
*
* @param string $tag     the tag name that needs to be checked
* @param string &$ttag   depreciated AIML tag name. Note, call-by-reference variable.
*
* @return boolean        true/false; either it is a depreciated AIML tag or it is not.
*/
function isdeprecated($tag,&$ttag){

	if ($tag=="FOR_FUN"){
		$tag="FORFUN";
	}
	if ($tag=="BOTMASTER"){
		$tag="MASTER";
	}
	if ($tag=="KIND_MUSIC"){
		$tag="KINDMUSIC";
	}
	if ($tag=="LOOK_LIKE"){
		$tag="LOOKLIKE";
	}

	if ($tag=="TALK_ABOUT"){
		$tag="TALKABOUT";
	}
	$deptags=array("NAME","BIRTHDAY","BIRTHPLACE","BOYFRIEND","FAVORITEBAND","FAVORITEBOOK","FAVORITECOLOR","FAVORITEFOOD","FAVORITESONG","FAVORITEMOVIE","FORFUN","FRIENDS","GIRLFRIEND","KINDMUSIC","LOCATION","LOOKLIKE","MASTER","QUESTION","SIGN","TALKABOUT","WEAR");

	if (in_array($tag,$deptags)){
		$ttag=$tag;
		return true;
	}
	else {
		return false;
	}

}

/**
* Substitution routine
*
* Is used in combination with for example {@link firstthird()} {@link gender()}. The myfunc() is called from these functions from the arrays generated in {@link subs.inc} by {@link makesrphp()}.
*
* When doing substitution myfunc replaces the words to be substituted with ~~x~~
* where x is an incremented integer instead of what should eventually be substituted.
* Then when all substitution is done another function will go through and replace the
* ~~x~~ with the real value.
*
* @todo Analyse if a straight replace will be more effective and efficient. Hard to see why this two-stage replace has any advantages. If not, then rename the function.
*
* @global integer replacecounter  The incremented integer number for substitution.
* @global array aftersearch       Contains ~~replacecounter number~~
* @global array afterreplace      Countains the input. (Anne:Have no idea how this works, yet.)
*
* @param string $input    The new word replacing the old word.
*
* @return string          The user's input, with ~~number~~ where the words in the substitution list
*                         should be.
*/
function myfunc($input){

	global $replacecounter,$aftersearch,$afterreplace;

	$aftersearch[]="~~" . $replacecounter . "~~";
	$afterreplace[]=$input;

	return "~~" . $replacecounter++ . "~~";

}

/**
* Get the ID, or IP of the user
*
* Uses the server global REMOTE_ADDR.
*
* @global REMOTE_ADDR     The user's remote address, i.e. LAN address.
*
* @return string          IP number
*/
function getid(){

	return getenv("REMOTE_ADDR");

}

/**
* get the current date formatted.
*
* Should look like: Wed Nov 14 18:09:55 CST 2002
*
* @return string          formated date string, Day name, Month name, day number month, time, time zone and year.
*
*/
function getfdate(){

	return date("D M j G:i:s T Y");

}

/**
* Gets the numer of AIML categories stored in the database
*
* The size of the AIML knowledgebase is counted in categories. This function
* will count the number of categories based upon the number of records in the
* templates table.
*
* @uses whichbots()
*
* @return integer         the number of templates in the template database.
*/
function getsize(){

	$query="select count(*) from templates where " . whichbots();

	$selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $query);
	if ($selectcode){
		if(!mysqli_num_rows($selectcode)){
			return 0;
		}
		else{
			while ($q = mysqli_fetch_array($selectcode)){
				return $q[0];
			}
		}
	}
	return "";

}

/**
* Get information about the bot that was entered in the startup.xml
*
* In startup.xml there are a number of bot-varables called in AIML as <bot name="foo"/>
* These are stored in the table 'bot' and this function retrieves (ex.) the value of
* bot-variable "foo".
*
* @global integer uid            is created in talk.php as $myuniqueid then transformed
*                                in respond.php into $uid by reply()
* @global integer selectbot     is created in respond.php:replybotname() as $botid
*                                which in return calls reply() that turns it into $selectbot
*
* @see talk.php
* @see reply()
* @see replybotname()
*
* @param string $name            The name of the bot-variable of which the value is requested.
*
* @return string                 The value of requested bot variable.
*/
function botget($name){

	global $uid, $selectbot;

	$name=addslashes($name);

	$query="select value from bot where name='$name' and bot = $selectbot";

	$selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $query);
	if ($selectcode){
		if(!mysqli_num_rows($selectcode)){
			return "";
		}
		else{
			while ($q = mysqli_fetch_array($selectcode)){
				return $q[0];
			}
		}
	}
	return "";

}

/**
* Get a value for some variable set by the user in AIML.
*
* In the template (<template> ... </template>) variables can be set and used during matching.
* after matching these variables are stored in the table dstore. This function retrieves the
* value of a stored variable. This type of variable is also called a 'predicate'.
*
* @global integer
*
* @return string            Either the value of the stored predicate, or the constand default
*                           value of a predicate
*/
function bget($name){

	global $uid;

	$name=addslashes($name);

	$query="select value from dstore where name='$name' and uid='$uid' order by id desc limit 1";

	$selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $query);
	if ($selectcode){
		if(!mysqli_num_rows($selectcode)){
			return DEFAULTPREDICATEVALUE;
		}
		else{
			while ($q = mysqli_fetch_array($selectcode)){
				return $q[0];
			}
		}
	}
	return DEFAULTPREDICATEVALUE;

}

/**
* Set the value for an AIML variable
*
* Function to store the variable name and it's contents into the dstore table. It doens't
* update the table, just the first insert.
*
* @global integer
*
* @param string $name     The name in which the value should be stored
* @param string $value    The contents of the variable.
*
* @return void      doesn't return anything
*/
function bset($name,$value){

	global $uid;

	$value=trim($value);
	$name=addslashes($name);
	$value=addslashes($value);

	$query="insert into dstore (uid,name,value) values ('$uid','$name','$value')";
	$selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $query);
	if ($selectcode){
	}

}

/**
* Store the clients inputs into the database.
*
* This is an array because it separates on .'s and other sentence splitters. Stores these in the
* dstore table of the database.
*
* @global integer
*
* @param array $inputsarray   Contains multiple inputs (i.e. sentences) of a user
*
* @return void                nothing
*/
function addinputs($inputsarray){

	global $uid;

	$query="insert into dstore (uid,name,value) values ";

	for ($x=0;$x<count($inputsarray);$x++){

		$value=addslashes(trim($inputsarray[$x]));

		$query.="('$uid','input','$value'),";

	}

	$query=substr($query,0,(strlen($query)-1));
	$selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $query);
	if ($selectcode){
	}

}

/**
* Store the bots responses into the database
*
* This is an array because it separates on .'s and other sentence splitters. Stores these in the
* thatindex table of the database.
*
* @global integer
*
* @param array $inputsarray   Contains multiple inputs (sentences) of the bot
*
* @return void                nothing
*/
function addthats($inputsarray){

	global $uid;


	$query="insert into thatindex (uid) values ('$uid')";

	$selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $query);
	if ($selectcode){
	}
	$thatidx=mysqli_insert_id($GLOBALS['SITE_DB']->connection_write[0]);

	$query="insert into thatstack (thatid,value) values ";

	for ($x=0;$x<count($inputsarray);$x++){

		$value=trim($inputsarray[$x]);

		$value=strip_tags($value);
		$value=addslashes($value);

		$query.="($thatidx,'$value'),";

	}

	$query=substr($query,0,(strlen($query)-1));

	$selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $query);
	if ($selectcode){
	}

}

/**
* Logs the whole input and response
*
* Saves the input and response into the conversationlog table of the database.
*
* @param string $input      The user's input.
* @param string $reponse    The bot's response to the user's input.
*
* @global integer
* @global integer

* @return void              nothing
*/
function logconversation($input,$response){

	global $uid, $selectbot;

	$input=addslashes($input);
	$response=addslashes($response);

	$query="insert into conversationlog (uid,input,response,bot) values ('$uid','$input','$response',$selectbot)";
	$selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $query);
	if ($selectcode){
	}

}

/**
* Get the previous thing the bot said
*
* Retrieve from the thatindex table the previous output, per sentence, of what the bot said from the thatindex en thatstack table.
*
* @global integer
*
* @param integer $index      Number between 1-5, 1 representing the previous bot output and 5 the 5th last bot output.
* @param integer $offset     Bot output is saved per sentence. So 1 would be last sentence, 3 would be third last sentence of that bot output.
*
* @return string             The requested sentence of the bot's output
*/
function getthat($index,$offset){

	global $uid;

	$index=$index-1;
	$offset=$offset-1;

	$query="select id from thatindex where uid='$uid' order by id desc limit $index,1";


	$selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $query);
	if ($selectcode){
		if(!mysqli_num_rows($selectcode)){
			return "";
		}
		else{
			while ($q = mysqli_fetch_array($selectcode)){
				$thatid=$q[0];
			}
		}
	}


	$query="select value from thatstack where thatid=$thatid order by id desc limit $offset,1";


	$selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $query);
	if ($selectcode){
		if(!mysqli_num_rows($selectcode)){
			return "";
		}
		else{
			while ($q = mysqli_fetch_array($selectcode)){
				return $q[0];
			}
		}
	}
	return "";

}

/**
* Get the previous thing the client said
*
* Retrieve the entire previous input of the user from the dstore table.
*
* @global integer
*
* @return string         The previous input of the user.
*/
function getinput($index){

	global $uid;

	$offset=1;

	$query="select value from dstore where uid='$uid' and name='input' order by id desc limit $index,$offset";

	$selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $query);
	if ($selectcode){
		if(!mysqli_num_rows($selectcode)){
			return "";
		}
		else{
			while ($q = mysqli_fetch_array($selectcode)){
				return $q[0];
			}
		}
	}
	return "";

}

/**
* Take the user input and do all substitutions and split it into sentences
*
* The matching process searches a match for every sentence and in the end combines all
* those individual matches into one reply. This funtion splits the sentences of the user's
* input, replaces the words that need to be subtituted (found in startup.xml).
*
*
* @global array contractsearch
* @global array contractreplace
* @global mixed abbrevsearch             not used in this function
* @global mixed removepunct              used in this function, but code is commented out.
* @global array likeperiodsearch
* @global array likeperiodreplace
* @global array aftersearch
* @global array afterreplace
* @global integer replacecounter
*
* @param string $input                   The unchanged user input.
*
* @return array                          each individual sentence, ready for matching.
*/
function normalsentences($input){

	global $contractsearch,$contractreplace,$abbrevsearch,$abbrevreplace,$removepunct,$likeperiodsearch,$likeperiodreplace,$aftersearch,$afterreplace,$replacecounter;

	$cfull=$input;

	//$cfull=preg_replace($contractsearch,$contractreplace,$cfull);

	$cfull=str_replace($aftersearch,$afterreplace,$cfull);

	$replacecounter=1;
	$aftersearch=array();
	$afterreplace=array();

	//$cfull=str_replace($removepunct,"",$cfull);
	$cfull=str_replace($likeperiodsearch,$likeperiodreplace,$cfull);

	$newsentences=array();

	// Now split based on .'s
	$cfulls=explode(".",$cfull);

	for ($x=0;$x<count($cfulls);$x++){
		if (trim($cfulls[$x])==""){

		}
		else {
			$newsentences[]=$cfulls[$x];
		}
	}

	return $newsentences;
}


/**
* Reverse the gender of a phrase
*
* Replaces, for example, 'he' in the string to 'she'. The gender related words and phrases that are used
* can be found in subs.inc and originally in the startup.xml
*
* @global array gendersearch
* @global array genderreplace
* @global array
* @global array
* @global integer
*
* @param string $input           The string where the gender related words/phrases need to be replaced
*
* @return string                 The string containing where the gender related words have been replaced.
*/
function gender($input){

	global $gendersearch,$genderreplace,$aftersearch,$afterreplace,$replacecounter;

	$newinput=preg_replace($gendersearch,$genderreplace,$input);

	$newinput=str_replace($aftersearch,$afterreplace,$newinput);

	$replacecounter=1;
	$aftersearch=array();
	$afterreplace=array();

	return $newinput;

}

/**
* Do a first to third person replacement
*
* Replaces, for example, "I was" to "he or she was". he gender related words and phrases that are used
* can be found in subs.inc and originally in the startup.xml
*
* @global array firstthirdsearch
* @global array firstthirdreplace
* @global array
* @global array
* @global array
* @global array
* @global integer
*
* @param string $input           The string where the first->third related words/phrases need to be replaced
*
* @return string                 The string containing where the first->third related words have been replaced.
*/
function firstthird($input){

	global $firstthirdsearch,$firstthirdreplace,$aftersearch,$afterreplace,$contractsearch,$contractreplace,$replacecounter;

	$newinput=preg_replace($firstthirdsearch,$firstthirdreplace,$input);

	$newinput=str_replace($aftersearch,$afterreplace,$newinput);

	$replacecounter=1;
	$aftersearch=array();
	$afterreplace=array();

	return $newinput;

}

/**
* Do a first to second person replacement
*
* Replaces, for example, "with you" to "with me". he gender related words and phrases that are used
* can be found in subs.inc and originally in the startup.xml
*
* @global array
* @global array
* @global array
* @global array
* @global integer
*
* @param string $input           The string where the first->second related words/phrases need to be replaced
*
* @return string                 The string containing where the first->second related words have been replaced.
*/
function firstsecond($input){

	global $firstsecondsearch,$firstsecondreplace,$aftersearch,$afterreplace,$replacecounter;

//	$newinput=preg_replace($firstsecondsearch,$firstsecondreplace,$input);
$newinput=$input;
	$newinput=str_replace($aftersearch,$afterreplace,$newinput);

	$replacecounter=1;
	$aftersearch=array();
	$afterreplace=array();

	return $newinput;

}

/**
* Insert gossip into the database
*
* Gossip is an AIML tag where the user can store bits of information from one user and then
* reuse is in the conversation of another user. It is the only tag that behaves this way. Stores
* value into the gossip table of the database.
*
* @param string $gossip          The string that needs to be saved.
*
* @return void                   Nothing.
*/
function insertgossip($gossip){

	global $selectbot;

	$gossip=addslashes($gossip);

	$query="insert into gossip (gossip,bot) values ('$gossip'," . $selectbot . ")";
	$selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $query);
	if ($selectcode){
	}

}

/**
* Get the child nodes of the XML tree
*
* Retrieve the child nodes of the XML tree. This is a recursive function.
*
* @uses GetChildren()
*
* @param array $vals
* @param integer &$i                  call-by-reference variable for, most likely, GetXMLTree()
*
* @return array                       The child nodes in an array.
*/
function GetChildren($vals, &$i) {

	$children = array();

	if (isset($vals[$i]['value']))
		array_push($children, $vals[$i]['value']);

	while (++$i < count($vals)) {

	if (!isset($vals[$i]['attributes'])){
		$vals[$i]['attributes']="";
	}
	if (!isset($vals[$i]['value'])){
		$vals[$i]['value']="";
	}

		switch ($vals[$i]['type']) {
			case 'cdata':
			array_push($children, $vals[$i]['value']);
			break;

			case 'complete':
			array_push($children, array('tag' => $vals[$i]['tag'], 'attributes' => $vals[$i]['attributes'], 'value' => $vals[$i]['value']));
			break;

			case 'open':
			array_push($children, array('tag' => $vals[$i]['tag'],'attributes' => $vals[$i]['attributes'], 'children' => GetChildren($vals,$i)));
			break;

			case 'close':
			return $children;
		}
	}
}

/**
* Get an XML tree
*
* Create an XML tree in array form from a large string.
*
* @uses GetChildren()
*
* @param string $data               Large string that needs to be turned into XML data.
*
* @return array                     Multi dimensional array in an XML way containing XML data.
*/
function GetXMLTree($data) {

	$p = xml_parser_create();
	xml_parser_set_option($p,XML_OPTION_CASE_FOLDING,0);

	xml_parse_into_struct($p, $data, $vals, $index);
	xml_parser_free($p);

	$tree = array();
	$i = 0;

	if (!isset($vals[$i]['attributes'])){
		$vals[$i]['attributes']="";
	}

	array_push($tree, array('tag' => $vals[$i]['tag'], 'attributes' => $vals[$i]['attributes'], 'children' => GetChildren($vals, $i)));
	return $tree;

}

/**
* Start a timer
*
* Save the start time of the to be timed script
*
* @global array ss_timing_start_times          contains the start moments of the script
*
* @param string $name                          default value is 'default' Makes it possibe to time more than
*                                              event in one script.
*
* @return void                                 nothing
*/
function ss_timing_start ($name = 'default') {
    global $ss_timing_start_times;
    $ss_timing_start_times[$name] = explode(' ', microtime());
}

/**
* Stop a timer
*
* Save the stop time of the to be timed script
*
* @global array ss_timing_stop_times          contains the stop moments of a script
*
* @param string $name                          default value is 'default' Makes it possibe to time more than
*                                              event in one script.
*
* @return void                                 nothing
*/
function ss_timing_stop ($name = 'default') {
    global $ss_timing_stop_times;
    $ss_timing_stop_times[$name] = explode(' ', microtime());
}

/**
* Retrieve timer data
*
* Get the running time the timed script
*
* @global array ss_timing_start_times          contains the start and finish moments of the script
* @global array ss_timing_stop_times          contains the stop moments of a script
*
* @param string $name                          default value is 'default' Makes it possibe to time more than
*                                              event in one script.
*
* @return string                                the formated running time of the timed stript.
*/
function ss_timing_current ($name = 'default') {
    global $ss_timing_start_times, $ss_timing_stop_times;
    if (!isset($ss_timing_start_times[$name])) {
        return 0;
    }
    if (!isset($ss_timing_stop_times[$name])) {
        $stop_time = explode(' ', microtime());
    }
    else {
        $stop_time = $ss_timing_stop_times[$name];
    }
    // do the big numbers first so the small ones aren't lost
    $current = $stop_time[1] - $ss_timing_start_times[$name][1];
    $current += $stop_time[0] - $ss_timing_start_times[$name][0];
    return $current;
}

/**
* Change the case of the keys of an array to all uppercase
*
* Array keys can be number or strings. If strings then it's a good habit to use only uppercase.
* This is sadly not always the case. This function makes sure that they are.
*
* @param array              Array suspected of having mixed case array keys.
*
* @return array             Array containing only uppercase array keys.
*/
if (!function_exists('upperkeysarray'))
{
	function upperkeysarray($testa){

        $newtesta=array();
        $newkeys=@array_keys($testa);
        if (is_array($newkeys)) {
            for ($x=0;$x<count($newkeys);$x++){
                $newtesta[strtoupper($newkeys[$x])]=$testa[$newkeys[$x]];
            }
        }
        return $newtesta;

	}
}

/**
* Check to see if a tag is a custom tag.
*
* Program E supports additional, non AIML 1.0x specified, custom tags. This function checks to see if
* the encountered XML tag is indeed a custom AIML tag. If it is a custom tag, it will then the
* appropriate function to use in &$functicall.
*
* @global array cttags            contains all the custom tags. Array is created by loadcustomtags()
*
* @see loadcustomtags()
*
* @param string $tagname          name of the tag to be checked
* @param &$functocall             call-by-reference variable used to send back the function name that
*                                 needs to be called to process the tag's contents.
*
* @return boolean				  true/false
*/
function iscustomtag($tagname,&$functocall){

	global $cttags;

	if (in_array(strtoupper($tagname),$cttags)){
		$functocall="ct_" . $tagname;
		return true;
	}
	else {
		return false;
	}

}

/**
* Load all custom tags
*
* Create a global accessible array with all custom tags, based upon the custom tag functions (ct_FunctionName()).
*
* @see customtags.php
*
* @return void
*/
function loadcustomtags(){

	global $cttags;
	$cttags=array();

	$definedfuncs = get_defined_functions();
	$definedfuncs=$definedfuncs["user"];

	// find all funcs in ["user"] funcs that match ct_??? and register each function and tag name
	foreach($definedfuncs as $x){
		if (substr($x,0,3)=="ct_"){
			$cttags[]=strtoupper(substr($x,3,strlen($x)));
		}
	}

}

/**
* Look up the bot's ID number
*
* From the bot's name, retrieve it's bot ID number from the bots table in the database. This
* funtion requires that each bot has a uniqe name to function.
*
* @param string $botname                The bot's name.
*
* @return integer                       The bot ID that belongs to the unique botname.
*/
function lookupbotid($botname){

	$name=addslashes($botname);
    $q="select id from bots where botname='$name'";
    $selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $q);
    if ($selectcode) {
        while ($q = mysqli_fetch_array($selectcode)){
                return $q["id"];
        }
    }
	return -1;

}


/**
* Which bot is selected
*
* Returns the bit of SQL query that limits the results to the selected bot.
*
* @global string selectbot              The currently selected bot. The bot the user is chatting to.
*
* @return string                        The bit of SQL query that limits the results to the selected bot.
*/
function whichbots()
{
	global $selectbot;

	return "bot=$selectbot";
}

