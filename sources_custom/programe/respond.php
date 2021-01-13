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
 * Respond functions 
 * 
 * Second layer functions that are prior to entering the AIML match routine.
 * @author Paul Rydell
 * @copyright 2002
 * @version 0.0.8
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package Interpreter
 */

function init__programe__respond()
{
    if(!function_exists('ereg'))            { function ereg($pattern, $subject, &$matches = []) { return preg_match('/'.$pattern.'/', $subject, $matches); } }
    if(!function_exists('eregi'))           { function eregi($pattern, $subject, &$matches = []) { return preg_match('/'.$pattern.'/i', $subject, $matches); } }
    if(!function_exists('ereg_replace'))    { function ereg_replace($pattern, $replacement, $string) { return preg_replace('/'.$pattern.'/', $replacement, $string); } }
    if(!function_exists('eregi_replace'))   { function eregi_replace($pattern, $replacement, $string) { return preg_replace('/'.$pattern.'/i', $replacement, $string); } }
    if(!function_exists('split'))           { function split($pattern, $subject, $limit = -1) { return preg_split('/'.$pattern.'/', $subject, $limit); } }
    if(!function_exists('spliti'))          { function spliti($pattern, $subject, $limit = -1) { return preg_split('/'.$pattern.'/i', $subject, $limit); } }
    	/** 
	* The errormessage when a loop is detected. 
	*/
	define("LOOPINGERRORMSG", "Oops. I wasn't paying attention. Tell me again what is going on.");

	/**
	* The number of times a loop may occur before the error is thrown. -1 equals to no limit.
	*/
	define("LOOPINGLIMIT",150); // -1 for no limit

	/**
	* Has something to do with the random generator
	*/
	define("RANDOMCHANCECLEAN",100); // -1 to never check

	/**
	* the amount of minutes certain data needs to be kept.
	*/
	define("MINUTESTOKEEPDATA",120); // -1 to keep forever

	/**
	* Minutes to keep the chatlot, standard set to -1, meaning forever.
	*/
	define("MINUTESTOKEEPCHATLOG",-1); // -1 to keep forever

	/**
	* standard value when a user predicate hasn't been set. 
	*/ 
	define("DEFAULTPREDICATEVALUE", $GLOBALS['FORUM_DRIVER']->get_username(get_member(),true));


	/**
	* wether or not to use the resonse caching mechanism. 1=yes, 0=no
	*/
	define('CACHE_CONTROL', 0);

	/**
	* version number of the application.
	*/
	define("PROGRAMEVERSION","v0.09-cms");

	/**
	* The matching engine functions of the AIML interpreter.
	*/
	require_code('programe/graphnew');

	/**
	* A collection of generally useful utility functions
	*/
	require_code('programe/util');

	/**
	* The file containing the function that process custom, non AIML 1.0.x specified, tags.
	*/
	require_code('programe/customtags');
}



/**
* Start function for retrieving bot reply
*
* Checks to see if bot exists, if so calls reply() to get the repons to the user's input.
*
* @uses lookupbotid()
* @uses reply()
*
* @param string $userinput          The user's input
* @param integer $uniqueid          The user's session ID
* @param string $botname            The bot's name, if no name selected the default value is "trickstr".
*
* @return string                    The bot's reply. 
*/
function replybotname($userinput,$uniqueid,$botname = "trickstr"){

	$botid = lookupbotid($botname);

	if ($botid==-1){
		//print "I don't know that bot: $botname<BR>\n";
	}
	else {
		return reply($userinput,$uniqueid,$botid);
	}

}


/**
* Main container function in creating the bot's reply.
*
* This function is the 'manager' of all the sub-funtions that do the real processing. It creates a class
* called Response that is used throughout the application. 
* 
* @uses addinputs()
* @uses addthats()
* @uses bget()
* @uses cleanup()
* @uses getthat()
* @uses loadcustomtags()
* @uses logconversation()
* @uses normalsentences()
* @uses respond()
* @uses ss_timing_current()
* @uses ss_timing_start()
* @uses ss_timing_stop()
* 
* @global string that                   The conversation's previous bot output
* @global string topic                  The contents of the AIML tag 'Topic'
* @global integer uid                   The session ID of the user (previously $uniqueid)
* @global integer loopcounter           Counts the number of time a particular category is used in the same match trace.
* @global array patternmatched          The pattern's that matched the 
*
* @param string $userinput              The user's input.
* @param integer $uniqueid              The user's session ID.
* @param integer $bot                   The bot's ID.
*
* @return object                        A class link to 'Response'. 
*/
function reply($userinput,$uniqueid, $bot = 1){	

	global $that,$topic,$uid,$loopcounter,$patternmatched,$inputmatched,$selectbot;	

	cleanup();

	ss_timing_start("all");

	$patternmatched=array();
	$inputmatched=array();

	$myresponse = new Response;

	$myresponse->errors="";

	$uid=$uniqueid;
	$selectbot=$bot;	

	// Load the custom plugin tags
	loadcustomtags();

	// Get the "that" and the "topic"
	$that=getthat(1,1);
	$topic=bget("TOPIC");

	// Normalize the input
	$allinputs=normalsentences($userinput);

	// If nothing said then use INACTIVITY special input
	if (count($allinputs)==0){
		$allinputs[]="INACTIVITY";
	}

	// Put all the inputs into the <input> stack.
	addinputs($allinputs);

	$finalanswer="";
	// Build our response to all of the inputs.
	for ($x=0;$x<count($allinputs);$x++){
		$finalanswer.=respond($allinputs[$x]);
	}

	if (($loopcounter>LOOPINGLIMIT)&&(LOOPINGLIMIT!=-1)){
		$finalanswer=LOOPINGERRORMSG;
		$myresponse->errors="LOOPINGLIMIT";
	}

	// Put the final answers into the <that> stack.
	addthats(normalsentences($finalanswer));

	// Log the conversation
	logconversation($userinput,$finalanswer);


	$myresponse->response=$finalanswer;
	$myresponse->patternsmatched=$patternmatched;
	$myresponse->inputs=$inputmatched;

	ss_timing_stop("all");

	$myresponse->timer=ss_timing_current("all");

	return $myresponse;

}
/**
* This is the second level response function. 
*
* After reply() this function is the second level function to get the answer to the user's input.
*
* @uses bget()
* @uses debugger()
* @uses gettemplate()
* @uses GetXMLTree()
* @uses recursechildren()
*
* @global string
* @global integer
* @global array
* @global array
* 
* @param string $sentence        The sentence to be matched.
*
* #return string                 The response to the user's input.
*/
function respond($sentence){

	global $that,$loopcounter,$patternmatched,$inputmatched;

	$topic = bget("topic");

	$loopcounter++;
	if (($loopcounter>LOOPINGLIMIT)&&(LOOPINGLIMIT != -1)){
		return "";
	}

	$inputstarvals=array();
	$thatstarvals=array();
	$topicstarvals=array();

	debugger("respond called with sentence: $sentence",3);
	//flush();

	if ($that==""){
		$that="<nothing>";
	}
	if ($topic==""){
		$topic="<nothing>";
	}


	if ($sentence==""){
		return "";
	}
	else{

		//If we found a template
		$template=gettemplate($sentence,$that,$topic,$inputstarvals,$thatstarvals,$topicstarvals,$s_patternmatched,$s_inputmatched);

		$patternmatched[]=$s_patternmatched;
		$inputmatched[]=$s_inputmatched;

		if ($template!=""){

			$template="<xml><TEMPLATE>" . $template . "</TEMPLATE></xml>";		
			debugger ("found template: $template",2);			


			$root=GetXMLTree($template);


			if 	(!isset($root[0]['children'][0]['value'])){
				$root=$root[0]['children'][0]['children'];
			}
			else {
				$root=$root[0]['children'][0]['value'];
			}

/*
			if 	($root[0]['children'][0]['value']==""){
				$root=$root[0]['children'][0]['children'];
			}
			else {
				$root=$root[0]['children'][0]['value'];
			}
*/

			$myresponse=recursechildren($root,$inputstarvals,$thatstarvals,$topicstarvals);
			debugger("recursechildren ret: $myresponse",3);

			return $myresponse;

		}
	}
}


/**
* Third level response processing
*
* This function is the 'manager' function of the template processing. 
*
* @uses handlenode()
*
* @param mixed $xmlnode               Getting either a string or an array from respond() func.
* @param array $inputstar             If a matched pattern includes *'s then what is covere by the * is found here.
* @param array $thatstar              if a used that contains a star, then what is covered by the * is found here.
* @param array $topicstar             if a used topic contains a star, then what is covered by the * is found here.
*
* @return string                      The bot's response.
*/
function recursechildren($xmlnode,$inputstar,$thatstar,$topicstar){

	// Getting either a string or an array from respond() func.
	$response="";

	if (is_array($xmlnode)){


//		if ($xmlnode['value']==""){
		if (!isset($xmlnode['value'])){
			for ($x=0;$x<count($xmlnode);$x++){

				$response .= handlenode($xmlnode[$x],$inputstar,$thatstar,$topicstar);

			}

		}
		else {
			$response .= handlenode($xmlnode['value'],$inputstar,$thatstar,$topicstar);
		}	
	}
	else {
		$response .= handlenode($xmlnode,$inputstar,$thatstar,$topicstar);
	}


	return $response;

}


/**
* Get the real XML child
*
* Get the real XML child which is used for processing AIML tags that may contain other AIML tags, such as SRAI, CONDITION etc. 
*
*
* @param array $xmlnode          
* 
* @return mixed 
*/
function realchild($xmlnode){

	if (!isset($xmlnode["value"])){

		if (isset($xmlnode["children"])){
			return($xmlnode["children"]);
		}
		else {
			return "";
		}

	}
	else {
		return($xmlnode["value"]);
	}

/*
	if ($xmlnode["value"]==""){
		return($xmlnode["children"]);
	}
	else {
		return($xmlnode["value"]);
	}
*/

}

/**
* Handles the actual XML between the <template/> tags. 
*
* Recognises the different tags, access the different functions to process each individual tag. Notes by the original developer: <br/>
* Why isn't this a huge switch statement? Because it has to do more comlicated checking than just string comparison to figure out what it should do. <br/>
* How can I organize this better? Good question.
*
* @todo It seems to me that this function could modelled similarly to the custom tag system. Where there is a seperate function for each tag.
*
* @uses getid()
* @uses getfdate()
* @uses getsize()
* @uses upperkeysarray()
* @uses debugger()
* @uses recursechildren()
* @uses respond()
* @uses botget()
* @uses gender()
* @uses getinput()
* @uses bset()
* @uses insertgossip()
* @uses firstthird()
* @uses firstsecond()
* @uses getthat()
* @uses realchild()
*
* @param mixed $xmlnode               Getting either a string or an array from recursechildren() func.
* @param array $inputstar             If a matched pattern includes *'s then what is covere by the * is found here.
* @param array $thatstar              if a used that contains a star, then what is covered by the * is found here.
* @param array $topicstar             if a used topic contains a star, then what is covered by the * is found here.
*
* @return string                      The bot's response.
*/

function handlenode($xmlnode,$inputstar,$thatstar,$topicstar){

	if (!is_array($xmlnode)){
		return $xmlnode;
	}
	elseif (strtoupper($xmlnode["tag"])=="ID"){

		return getid();

	}
	elseif (strtoupper($xmlnode["tag"])=="DATE"){

		return getfdate();

	}
	elseif (strtoupper($xmlnode["tag"])=="VERSION"){

		return PROGRAMEVERSION;

	}
	elseif (strtoupper($xmlnode["tag"])=="SIZE"){

		return getsize();

	}
	elseif (strtoupper($xmlnode["tag"])=="STAR"){

		$mynode=upperkeysarray($xmlnode["attributes"]);

		//$starindex=$xmlnode["attributes"]["INDEX"];


		if (!((is_array($mynode))&&(isset($mynode["INDEX"])))){
			$mynode["INDEX"]="";
		}

		$starindex=$mynode["INDEX"];
		if ($starindex==""){
			$starindex="1";
		}
		debugger("starindex: $starindex",3);
		//print_r($inputstar);
		return $inputstar[$starindex-1];


	}
	elseif (strtoupper($xmlnode["tag"])=="THATSTAR"){

		$mynode=upperkeysarray($xmlnode["attributes"]);

		//$starindex=$xmlnode["attributes"]["INDEX"];

		if (!((is_array($mynode))&&(isset($mynode["INDEX"])))){
			$mynode["INDEX"]="";
		}

		$starindex=$mynode["INDEX"];
		if ($starindex==""){
			$starindex="1";
		}
		debugger("starindex: $starindex",3);
		//print_r($inputstar);
		return $thatstar[$starindex-1];


	}
	elseif (strtoupper($xmlnode["tag"])=="TOPICSTAR"){


		$mynode=upperkeysarray($xmlnode["attributes"]);

		//$starindex=$xmlnode["attributes"]["INDEX"];

		if (!((is_array($mynode))&&(isset($mynode["INDEX"])))){
			$mynode["INDEX"]="";
		}

		$starindex=$mynode['INDEX'];
		if ($starindex==""){
			$starindex="1";
		}
		debugger("starindex: $starindex",3);
		//print_r($inputstar);
		return $topicstar[$starindex-1];


	}
	elseif (strtoupper($xmlnode["tag"])=="SRAI"){

		// Build up a new response inside of here (using recursechildren function and then call response with it.

		$newresponse=recursechildren(realchild($xmlnode),$inputstar,$thatstar,$topicstar);

		debugger("newresponts: $newresponse",3);
		return respond($newresponse);

	}
	elseif (strtoupper($xmlnode["tag"])=="SR"){

		return respond($inputstar[0]);

	}
	elseif (strtoupper($xmlnode["tag"])=="RANDOM"){

		$liarray=array();

		$children = $xmlnode["children"];

		for ($randomc=0;$randomc<count($children);$randomc++){
			if (is_array($children[$randomc]) && strtoupper($children[$randomc]["tag"]) == "LI"){
				$liarray[]=$randomc;
			}
		}

		// Pick a random number from 0 to count($liarray)-1
		mt_srand ((int) microtime() * 1000000);
		$lirandom= mt_rand(0,(count($liarray)-1));


		return recursechildren(realchild($children[$liarray[$lirandom]]),$inputstar,$thatstar,$topicstar);

	}
	elseif (strtoupper($xmlnode["tag"])=="THINK"){

		recursechildren(realchild($xmlnode),$inputstar,$thatstar,$topicstar);
		return "";

	}
	elseif (strtoupper($xmlnode["tag"])=="BOT"){

		$mynode=upperkeysarray($xmlnode["attributes"]);

		//$name=$xmlnode["attributes"]["NAME"];

		$name=$mynode["NAME"];

		return botget($name);

	}
	elseif (strtoupper($xmlnode["tag"])=="GET"){

		$mynode=upperkeysarray($xmlnode["attributes"]);

		//$name=$xmlnode["attributes"]["NAME"];

		$name=$mynode["NAME"];

		return bget($name);

	}
	elseif (strtoupper($xmlnode["tag"])=="SET"){

		//$name=$xmlnode["attributes"]["NAME"];
		$mynode=upperkeysarray($xmlnode["attributes"]);
		$name=$mynode["NAME"];

		$value=recursechildren(realchild($xmlnode),$inputstar,$thatstar,$topicstar);

		bset($name,$value);

		return $value;

	}
	elseif (strtoupper($xmlnode["tag"])=="UPPERCASE"){

		$value=recursechildren(realchild($xmlnode),$inputstar,$thatstar,$topicstar);

		return strtoupper($value);

	}
	elseif (strtoupper($xmlnode["tag"])=="FORMAL"){

		$nvalue="";

		$value=recursechildren(realchild($xmlnode),$inputstar,$thatstar,$topicstar);

		$value=strtolower($value);

		$words=explode(" ",$value);
		for ($x=0;$x<count($words);$x++){
			if ($x!=0){
				$nvalue.=" ";
			}
			$nvalue .= ucfirst($words[$x]);
		}

		return $nvalue;

	}
	elseif (strtoupper($xmlnode["tag"])=="LOWERCASE"){

		$value=recursechildren(realchild($xmlnode),$inputstar,$thatstar,$topicstar);

		return strtolower($value);

	}
	elseif (strtoupper($xmlnode["tag"])=="GENDER"){

		$value=recursechildren(realchild($xmlnode),$inputstar,$thatstar,$topicstar);

		return gender($value);

	}
	elseif (strtoupper($xmlnode["tag"])=="SENTENCE"){

		$value=recursechildren(realchild($xmlnode),$inputstar,$thatstar,$topicstar);

		return ucfirst($value);

	}

	elseif (strtoupper($xmlnode["tag"])=="INPUT"){

		$mynode=upperkeysarray($xmlnode["attributes"]);

		//$index = $xmlnode["attributes"]["INDEX"];

		if (!((is_array($mynode))&&(isset($mynode["INDEX"])))){
			$mynode["INDEX"]="";
		}		

		$index = $mynode["INDEX"];


		if ($index==""){
			$index=1;
		}

		$index=$index-1;

		return getinput($index);


	}
	elseif (strtoupper($xmlnode["tag"])=="GOSSIP"){

		$value=recursechildren(realchild($xmlnode),$inputstar,$thatstar,$topicstar);

		insertgossip($value);

		return $value;

	}
	elseif (strtoupper($xmlnode["tag"])=="PERSON"){

		$value=recursechildren(realchild($xmlnode),$inputstar,$thatstar,$topicstar);

		if ($value==""){
			$value=$inputstar[0];
		}

		return firstthird($value);

	}
	elseif (strtoupper($xmlnode["tag"])=="PERSON2"){

		$value=recursechildren(realchild($xmlnode),$inputstar,$thatstar,$topicstar);

		if ($value==""){
			$value=$inputstar[0];
		}

		return firstsecond($value);

	}
	elseif (strtoupper($xmlnode["tag"])=="THAT"){


		$mynode=upperkeysarray($xmlnode["attributes"]);
		//$indexes = $xmlnode["attributes"]["INDEX"];

		if ((is_array($mynode))&&(isset($mynode["INDEX"]))){
			$indexes = $mynode["INDEX"];
		}
		else {
			$indexes="";
		}

		$indexes = explode (",", $indexes);

		if (count($indexes)<2){
			$indexes=array();
			$indexes[]=1;
			$indexes[]=1;
		}

		return getthat($indexes[0],$indexes[1]);


	}
	elseif (strtoupper($xmlnode["tag"])=="CONDITION"){


		$mynode=upperkeysarray($xmlnode["attributes"]);

		// First do multi condition name=value
		if ((is_array($mynode))&&(isset($mynode["NAME"]))){
			$condname=$mynode["NAME"];
		}
		else {
			$condname = "";
		}
		if ((is_array($mynode))&&(isset($mynode["VALUE"]))){
			$condvalue=$mynode["VALUE"];
		}
		else {
			$condvalue = "";
		}
		if ((is_array($mynode))&&(isset($mynode["CONTAINS"]))){
			$condcontains=$mynode["CONTAINS"];
		}
		else {
			$condcontains = "";
		}
		if ((is_array($mynode))&&(isset($mynode["EXISTS"]))){
			$condexists=$mynode["EXISTS"];
		}
		else {
			$condexists = "";
		}
/*
		$condname=$mynode["NAME"];
		$condvalue=$mynode["VALUE"];
		$condcontains=$mynode["CONTAINS"];
		$condexists=$mynode["EXISTS"];
*/
		// If this is a multi condition
		if (($condname!="")&&($condvalue!="")){


			if ($condvalue!=""){
				$condtype="VALUE";
			}
			elseif ($condcontains!=""){
				$condtype="CONTAINS";
			}
			elseif ($condexists!=""){
				$condtype="EXISTS";
			}

			if ($condtype=="VALUE"){

				$condvalue="^" . str_replace("*","(.*)",$condvalue);
				if (eregi($condvalue,bget($condname))){
				//if ((bget($condname))==$condvalue){

					return recursechildren(realchild($xmlnode),$inputstar,$thatstar,$topicstar);

				}

			}


		}
		// Else condname not blank and value is blank then it goes to <li>'s that have conditions in them.
		elseif (($condname!="")&&($condvalue=="")){


			$children = $xmlnode["children"];

			$checkval=bget($condname);


			// After a match break. If no match then execute last if no name or val			
			for ($randomc=0;$randomc<count($children);$randomc++){

				if (strtoupper($children[$randomc]["tag"]) == "LI"){

					$mynode=upperkeysarray($children[$randomc]["attributes"]);

					//$condvalue=$children[$randomc]["attributes"]["VALUE"];

					if (!((is_array($mynode))&&(isset($mynode["VALUE"])))){
						$mynode["VALUE"]="";
					}

					$condvalue=$mynode["VALUE"];


					$condvalue="^" . str_replace("*","(.*)",$condvalue) . "$";

					if ((eregi($condvalue,$checkval))||($condvalue=="^\$")){

						return recursechildren(realchild($children[$randomc]),$inputstar,$thatstar,$topicstar);

					}

				}
			}

		}
		// Else condname and value both blank then the <li>'s inside have both
		elseif (($condname=="")&&($condvalue=="")){



			$children = $xmlnode["children"];

			// After a match break. If no match then execute last if no name or val
			for ($randomc=0;$randomc<count($children);$randomc++){
				if (@strtoupper($children[$randomc]["tag"]) == "LI"){

					$mynode=upperkeysarray($children[$randomc]["attributes"]);

					if ((is_array($mynode))&&(isset($mynode["NAME"]))){
						$condname=$mynode["NAME"];
					}
					else {
						$condname = "";
					}
					if ((is_array($mynode))&&(isset($mynode["VALUE"]))){
						$condvalue=$mynode["VALUE"];
					}
					else {
						$condvalue = "";
					}



					$condvalue="^" . str_replace("*","(.*)",$condvalue) . "$";


					if ((eregi($condvalue,bget($condname)))||(($condvalue=="^\$")&&($condname==""))){

						return recursechildren(realchild($children[$randomc]),$inputstar,$thatstar,$topicstar);

					}


				}
			}


		}

	}
	elseif (strtoupper($xmlnode["tag"])=="SYSTEM"){

		$command=recursechildren(realchild($xmlnode),$inputstar,$thatstar,$topicstar);

		exec($command,$execoutput);

		for ($x=0;$x<count($execoutput);$x++){
			$allout=$allout . $execoutput[$x];
		}

		return $allout;

	}
	// For JavaScript to work you need:
	//				1. Java.
	//				2. PHP compiled with Java support.
	//				3. js.jar in your php/java directory.
	//				   (you can get js.jar from the Program D distribution - http://www.alicebot.org)
	//				4. php.ini's java.class.path to point to js.jar.
	// A much easier alternative is to write PHP code and embed it in <php></php> tags.
	/*
	elseif (strtoupper($xmlnode["tag"])=="JAVASCRIPT"){

		$jscode=recursechildren(realchild($xmlnode),$inputstar,$thatstar,$topicstar);

		$context = new Java("org.mozilla.javascript.Context");
		$newContext = $context->enter();
		$scope = $newContext->initStandardObjects(null);
		$script = $jscode;
		$evaluate = $newContext->evaluateString($scope, $script, "<cmd>", 1, null);
		$context->exit();
		return $newContext->toString($evaluate);

	}
	*/
	elseif (strtoupper($xmlnode["tag"])=="PHP"){

		$phpcode=recursechildren(realchild($xmlnode),$inputstar,$thatstar,$topicstar);

		ob_start();
		eval($phpcode);
		$evaled = ob_get_contents(); 
		ob_end_clean(); 

		return $evaled;

	}
	elseif (strtoupper($xmlnode["tag"])=="JUSTBEFORETHAT"){

		$indexes=array();
		$indexes[]=2;
		$indexes[]=1;

		return getthat($indexes[0],$indexes[1]);

	}
	elseif (strtoupper($xmlnode["tag"])=="JUSTTHAT"){

		$index=2;

		$index=$index-1;

		return getinput($index);

	}
	elseif (strtoupper($xmlnode["tag"])=="BEFORETHAT"){

		$index=3;

		$index=$index-1;

		return getinput($index);

	}
	elseif (strtoupper($xmlnode["tag"])=="GET_IP"){

		return getid();

	}
	elseif (strtoupper($xmlnode["tag"])=="GETNAME"){

		$name="NAME";

		return bget($name);

	}
	elseif (strtoupper($xmlnode["tag"])=="GETSIZE"){

		return getsize();

	}
	elseif (strtoupper($xmlnode["tag"])=="GETTOPIC"){

		$name="TOPIC";

		return bget($name);

	}
	elseif (strtoupper($xmlnode["tag"])=="GETVERSION"){

		return PROGRAMEVERSION;

	}
	elseif (substr(strtoupper($xmlnode["tag"]),0,4)=="GET_"){

		$name=substr($xmlnode["tag"],4);

		return bget($name);

	}
	elseif (strtoupper($xmlnode["tag"])=="SETNAME"){

		$name="NAME";

		$value=recursechildren(realchild($xmlnode),$inputstar,$thatstar,$topicstar);

		bset($name,$value);

		return $value;

	}
	elseif (strtoupper($xmlnode["tag"])=="SETTOPIC"){

		$name="TOPIC";

		$value=recursechildren(realchild($xmlnode),$inputstar,$thatstar,$topicstar);

		bset($name,$value);

		return $value;

	}
	elseif (substr(strtoupper($xmlnode["tag"]),0,4)=="SET_"){

		$name=substr($xmlnode["tag"],4);
		$value=recursechildren(realchild($xmlnode),$inputstar,$thatstar,$topicstar);

		bset($name,$value);

		return $value;

	}
	elseif (isdeprecated(strtoupper($xmlnode["tag"]),$ttag)){

		$name=$ttag;

		return botget($name);

	}
	elseif (iscustomtag(strtoupper($xmlnode["tag"]),$ctfunction)){

		return $ctfunction($xmlnode,$inputstar,$thatstar,$topicstar);

	}
	// Else we do not know how to handle this. Assume it is HTML and just output it.
	// This code fixed by Stefan Humnig
	else {

		$name = $xmlnode["tag"];

		$atts=$xmlnode["attributes"];

		$atttext="";

		if ($atts != NULL)
		{
			foreach ($atts as $key => $value)
			{
				$atttext .= " $key=\"$value\"";
			}
		}

		$value="<$name" . $atttext;

		if (isset($xmlnode["children"]) || strcmp($xmlnode["value"], "") != 0) {
			$value .= ">" . recursechildren(realchild($xmlnode),$inputstar,$thatstar,$topicstar) . "</$name>";
		}
		else {
			$value .= "/>";
		}

		return $value;

	}

}
