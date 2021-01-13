<?php

/*
    Program E
    Copyright 2002, Paul Rydell
    Portions by Jay Myers

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
 * AIML loading functions
 *
 * Contains contains the functions for the actual loading of AIML.
 * @author Paul Rydell
 * @copyright 2002
 * @version 0.0.8
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package Loader
 */


/**
* The general preferences and database details.
*/

global $selectbot, $annesID;


/**
* Deletes everything about a bot.
*
* Empties the tables, bot, patterns, templates, bots and gmcache
*
* @param integer $bot             The bot's ID
*
* @return void
*/
function deletebot($bot)
{

	$q="delete from bot where bot=$bot";
    $e = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $q);
    if ($e){
    }
    $q="delete from patterns where bot=$bot";
    $e = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $q);
    if ($e){
    }
    $q="delete from templates where bot=$bot";
    $e = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $q);
    if ($e){
    }
    $q="delete from bots where id=$bot";
    $e = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $q);
    if ($e){
    }
    $q="delete from gmcache";
    $e = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $q);
    if ($e){
    }


}

/**
* Deletes information about a bot in the cache and bot tables.
*
* Used by the incremental bot loader program so it doesn't wipe out the whole
* bot on each aiml file load. Deletes everything in bot, bots and gmcache tables.
*
* @param integer $bot             The bot's ID.
*
* @return void
*/

//
//
function deletejustbot($bot){

	$q="delete from bots where id=$bot";
    $e = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $q);
    if ($e){
    }
	$q="delete from bot where bot=$bot";
	$e = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $q);
	if ($e){
	}
    $q="delete from gmcache";
    $e = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $q);
    if ($e){
    }

}

/**
* Deletes the gmcache table.
*
* This needs to be called whenever the patterns or templates table is updated.
*
* @return void
*/
function flushcache()
{
    $q="delete from gmcache";
    $e = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $q);
    if ($e){
    }
}

/**
* Makes the keys of an array uppercase.
*
* Makes the keys of an array uppercase.
*
* @param array $testa           The array where the keys need to be changed into uppercase.
*
* @return array                 An array with only uppercase keys.
*/
function upperkeysarray($testa)
{
    $newtesta=array();
    $newkeys=@array_keys($testa);
    if (is_array($newkeys)) {
        for ($x=0;$x<count($newkeys);$x++){
            $newtesta[strtoupper($newkeys[$x])]=$testa[$newkeys[$x]];
        }
    }
    return $newtesta;
}

/**
* Write the substitution include file
*
* Write the substitution arrays back into the subs.inc
*
* @global object                 The opened subs.inc file, ready to write to.
*
* @param string $string          most likely an array or all arrays turned into a big file.
*
* @return void
*/
function addtosubs($string)
{

    global $fp;

    fwrite($fp,$string);

}

/**
* Create the object for writing the substitution include file
*
* Creates the object, which is then used by addtosubs() to write to
*
* @see addtosubs()
* @see makesubscode()
* @global object
*
* @return void
*/
function createsubfile()
{

    global $fp;

    $fp = fopen (get_file_base() . "/safe_mode_temp/subs.inc", "w+");

}

/**
* Find a word in the patterns table given the word and the parent.
*
* The AIML patterns are stored in the MySQL table in a binary tree format.
* This function retrieves the next word details based upon the previous' word's ID.
* If this word doesn't exist it returns 0, else it returns it's details.
*
* @uses setnotend()
*
* @param string $word             The word that is to be searched
* @param integer $parent          The ID of the parent word
*
* @return integer                 The ID of the word that was searched
*/
function findwordid($word,$parent)
{

    $word=addslashes($word);
    $query="select id,isend from patterns where word='$word' and parent=$parent";

    $selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $query);
    if ($selectcode){
        if(!mysqli_num_rows($selectcode)){
            return 0;
        }
        else{
            while ($q = mysqli_fetch_array($selectcode)){

                if ($q[1]==1){
                    setnotend($q[0]);
                }

                return $q[0];
            }
        }
    }
}

/**
* Find a wildcard in the patterns table given the word and the parent.
*
* Similar as findwordid() but this will retrieve the ID if there is a wildcard that fits.
*
* @uses setnotend()
*
* @param string $word             The word that is to be searched, either _ or *
* @param integer $parent          The ID of the parent word
*
* @return integer                 The ID of the word that was searched
*/
function findwordidstar($word,$parent)
{

    if ($word=="*"){
        $val=3;
    }
    elseif ($word=="_"){
        $val=1;
    }
    $query="select id,isend from patterns where parent=$parent and word is null and ordera=$val";

    $selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $query);
    if ($selectcode){
        if(!mysqli_num_rows($selectcode)){
            return 0;
        }
        else{
            while ($q = mysqli_fetch_array($selectcode)){

                if ($q[1]==1){
                    setnotend($q[0]);
                }

                return $q[0];
            }
        }
    }
}


/**
* Set an entry in the patterns table to not be flagged as the last word in its context.
*
* Update a record in the patterns table, change isend column from 1 to 0. Given a particular word ID.
*
* @param integer $wordid
*
* @return void
*/
function setnotend($wordid)
{

    $query="update patterns set isend=0 where id=$wordid";
    $q=mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $query);
    if ($q){

    }

}

/**
* Inserts the pattern into the patterns table.
*
* inserts the pattern, that and topic as individual words in binary tree format into the patterns table
*
* @uses findwordid()
* @uses insertwordpattern()
* @uses findwordidstar()
*
* @global string
* @global integer
*
* @param string $mybigsentence          Contains the pattern, that and topic in the following format <input>word word<that>word word<topic>word word.
*/
function insertmysentence($mybigsentence)
{
    global $selectbot, $annesID;

    $sentencepart="";

    $newstarted=0;
    if($annesID != ""){
    $parent = $annesID;
    } else {
    $parent=-$selectbot;
    }

    //Parse into invidividual words
    //Use split
    $allwords=explode(" ",$mybigsentence);
    $qadd="";
    for ($x=0;$x<count($allwords)+1;$x++){

        // Last word in context
        $lwic=0;

        if ($x==count($allwords)){
            $word="";
        }
        else {
            $word=$allwords[$x];
        }

        if (strtoupper($word)=="<INPUT>"){
            $sentencepart="INPUT";
        } elseif (strtoupper($word)=="<THAT>"){
            $sentencepart="THAT";
        } elseif (strtoupper($word)=="<TOPIC>"){
            $sentencepart="TOPIC";
        }

        // Find out if it is the last word in its context
        if ($x==(count($allwords)-1)){
            $lwic=1;
        }
		// Prevent some warnings by checking this first.
		elseif (($x+1) >= (count($allwords))){

		}
        elseif ((strtoupper($allwords[$x+1])=="<THAT>") || (strtoupper($allwords[$x+1])=="<TOPIC>")){
            $lwic=1;
        }

        if (($word!="*")&&($word!="_")){

            if ($newstarted!=1){
                $wordid=findwordid($word,$parent);
            }

            if (($wordid!=0) && ($newstarted!=1)){
                $parent=$wordid;
            }
            else {

                $newstarted=1;

                $sword=addslashes($word);
                $qadd="($selectbot, null,'$sword',2,$parent,$lwic)";

				$parent = insertwordpattern($qadd);



            }
        }
        elseif (($word=="*")||($word=="_")){

            if ($newstarted!=1){
                $wordid=findwordidstar($word,$parent);
            }

            if (($wordid!=0) && ($newstarted!=1)){
                $parent=$wordid;
            }
            else {

                $newstarted=1;

                if ($word=="*"){
                    $val=3;
                }
                elseif ($word=="_"){
                    $val=1;
                }

                $qadd="($selectbot, null,null,$val,$parent,$lwic)";

				$parent = insertwordpattern($qadd);



            }
        }
    }

    return $parent;

}

/**
* Inserts an entry into the patterns table. Returns the ID of the new row inserted.
*
* insert a word into the patterns table, returns the id of the record so it can be
* used as the parent ID of the next word that's to be inserted.
*
* @param string $qadd                 The word of the pattern to be inserted
*
* @return integer                     The record ID of the inserted word.
*/
function insertwordpattern($qadd)
{

    $qcode=mysqli_query($GLOBALS['SITE_DB']->connection_write[0], "insert into patterns(bot,id,word,ordera,parent,isend) values $qadd");

	if ($qcode){

		return mysqli_insert_id($GLOBALS['SITE_DB']->connection_write[0]);
	}

}

/**
* Inserts a template into the template table.
*
* Insert the template into the template database. <br/>
* This version has been adapted to also insert the pattern, that and topic into the additionally added columns
*
* @uses templateexists()
*
* @global integer
* @global integer              The ID of the record in the patterns table that links to this table
* @global string               The pattern, including variables like _ and *.
* @global string               the topic, including variable like _ and *
* @global string               the that, including variables like _ and *
*
* @param integer $idused       The ID of the record in the patterns table that links to the template table.
* @param string $template      The contents inbetween <template/> tags.
*
* @return void
*/
function insertmytemplate($idused,$template)
{

	global $selectbot,$templatesinserted, $pattern, $topic, $that;

    if (!templateexists($idused)){
        $templatesinserted++;

        $template=addslashes($template);
        $query="insert into templates (bot,id,template,pattern,that,topic) values ($selectbot, $idused,'$template','$pattern','$that','$topic')";

        $qcode=mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $query);
        if ($qcode){
        }
    }

}

/**
* Checks if a template exists for a given pattern
*
* Does a query on the database to see if an ID is used by a template in the templates table.
*
* @param integer $idused         The ID number that corresponds to the number in the ID column, if it exists.
*
* @return boolean                true/false
*/
function templateexists($idused)
{
    $query="select id from templates where id=$idused";

    $qcode=mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $query);

    if ($qcode){
        if(!mysqli_num_rows($qcode)){
            return false;
        }
    }

    return true;

}

/**
* Called by the XML parser that is parsing the startup.xml file.
*
* startS and endS are two functions that cooperate. startS looks for the start of an XML tag, endS for the end tag.
* It fills the global variabls with info to be processed further on. Inserts the bots into the bots table, inserts the bot predicates into its respective table.
*
* @todo Find out what Global $areinc does
*
* @uses upperkeysarray()
* @uses botexists()
* @uses getbotid()
* @uses deletebot()
*
* @global integer
* @global string
* @global array
* @global array                  contains the splitter characters which are used to split the sentences. For example period, comma, semi-colon, exclamation mark, question mark.
* @global array                  contains the input, split into sentences
* @global array                  contains the gender words or phrases.
* @global array                  contains the person words or phrases
* @global array                  contains the person2 words or phrases
* @global array                  contains the names of all the bots, or their ID's
* @global array                  contains something unknown
*
* @param resource $parser        SAX XML resource handler
* @param string $name            name of the encountered tag.
* @param array $attrs            contains the additional attribues of a tag, like value, id, find.
*
* @return void
*/
function startS($parser,$name,$attrs)
{

    global $selectbot, $whaton, $startupwhich, $splitterarray,
    $inputarray, $genderarray, $personarray, $person2array, $allbots, $areinc;

    $attrs=upperkeysarray($attrs);

    if (strtoupper($name)=='LEARN') {
        $whaton = 'LEARN';
    }
    if (strtoupper($name)=="GENDER"){
        $startupwhich="GENDER";
    }
    elseif (strtoupper($name)=="INPUT"){
        $startupwhich="INPUT";
    }
    elseif (strtoupper($name)=="PERSON"){
        $startupwhich="PERSON";
    }
    elseif (strtoupper($name)=="PERSON2"){
        $startupwhich="PERSON2";
    }

    if (strtoupper($name)=="PROPERTY"){
        $q="insert into bot (bot,name,value) values ($selectbot,'" . addslashes($attrs["NAME"]) . "','" . addslashes($attrs["VALUE"]) . "')";

        $qcode=mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $q);
        if ($qcode){
        }

    }
    elseif (strtoupper($name)=="BOT") {
		$bot = $attrs["ID"];
		if (botexists($bot)){
			$existbotid = getbotid($bot);
			if ($areinc==1){
				deletebot($existbotid);
			}
		}

		$asbot=addslashes($bot);
		$q="insert into bots (id,botname) values (null,'$asbot')";
		$qcode=mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $q);

		if ($areinc==1){
			if ($qcode){
			}
			$newbotid=mysqli_insert_id($GLOBALS['SITE_DB']->connection_write[0]);
		}
		else {
			$newbotid=$existbotid;
		}

		$selectbot=$newbotid;
		$allbots[]=$selectbot;

		#print "<font size='3'><b>Loading bot: $bot ($selectbot)<BR></b></font>\n";
		flush();
    }
    elseif (strtoupper($name)=="SPLITTER"){
        $splitterarray[]=$attrs["VALUE"];
    }
    elseif (strtoupper($name)=="SUBSTITUTE"){
        if (trim($attrs["FIND"])!=""){
            if ($startupwhich=="INPUT"){
                $inputarray[]=array($attrs["FIND"],$attrs["REPLACE"]);
            }
            elseif ($startupwhich=="GENDER"){
                $genderarray[]=array($attrs["FIND"],$attrs["REPLACE"]);
            }
            elseif ($startupwhich=="PERSON"){
                $personarray[]=array($attrs["FIND"],$attrs["REPLACE"]);
            }
            elseif ($startupwhich=="PERSON2"){
                $person2array[]=array($attrs["FIND"],$attrs["REPLACE"]);
            }
        }
    }
}

/**
* Called by the XML parser that is parsing the startup.xml file.
*
* @global string                 Which start tag was processed last
*
* @param resource $parser        SAX XML resource handler
* @param string $name            name of the encountered tag.
*
* @return void
*/
function endS($parser,$name)
{
    global $whaton;
    if (strtoupper($name)=='LEARN') {
        $whaton = '';
    }
}

/**
* Process contents <learn> tag in startup.xml
*
* Called by the XML parser that is parsing the startup.xml file. * -> all files in directory, or single file.
*
* @todo When using * it should process AIML in subdirectories too. This is currently only supported by using multiple <learn> tag entries for every folder containing AIML files.
*
* @uses learnallfiles()
*
* @global string
* @global array       contains the AIML files to learn.
* @global integer
*
* @param resource $parser        SAX XML resource handler
* @param string $data            assuming it contains the path to the file.
*/
function handlemeS($parser, $data)
{
    global $whaton, $learnfiles, $selectbot;
    if (strtoupper($whaton)=="LEARN"){
        if (trim($data)=="*"){
            learnallfiles($selectbot);
        }
        else {
			$learnfiles[$selectbot][]=trim($data);
        }

    }
}

/**
* Checks if a bot already exists.
*
* Does a check to see if a bot with a particular name already exists. The reliance on unique names if crucial for the bot to work.
*
* @param string $name            the name of the bot that is checked.
*
* @return boolean                true/false
*/
function botexists($name){

    // search to get existing id
	$name=addslashes($name);
    $q="select id from bots where botname='$name'";
    $selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $q);
    if ($selectcode) {
        while ($q = mysqli_fetch_array($selectcode)){
			return true;
        }
    }

    return false;

}

/**
* Gets a bot's property value.
*
* Retrieves the value of a particular bot predicate. If the predicate isn't set, then it will return the value 'default'. This has been hardcoded
*
*
* @global integer
*
* @param string $name        the name of the bot predicate to be retrieved.
*
* @return string             the value of the bot predicate.
*/
function getbotvalue($name)
{
    global $selectbot;

    $q="select value from bot where name=" . addslashes($name) . " and bot=$selectbot";

    $selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $q);
    if ($selectcode){
        if(!mysqli_num_rows($selectcode)){
                return DEFAULTPREDICATEVALUE;
        }
        else{
            while ($q = mysqli_fetch_array($selectcode)){
                return $q["value"];
            }
        }
    }
}

/**
* Gets the ID of a bot given its name.
*
* Gets the ID of a bot given its name
*
* @todo move this function to a common include file shared by both the Loader and the Interpreter.
*
* @param string $name              The name of the bot
*
* @ return integer                 The bot's ID.
*/
function getbotid ($name)
{
    // search to get existing id
	$name=addslashes($name);
    $q="select id from bots where botname='$name'";
    $selectcode = mysqli_query($GLOBALS['SITE_DB']->connection_write[0], $q);
    if ($selectcode) {
        while ($q = mysqli_fetch_array($selectcode)){
                return $q["id"];
        }
    }

}

/**
* Used by the AIML XML parser
*
* Looks for the start tag used in AIML files for category, template, that, topic.
*
* @uses upperkeysarray()
* @uses getbotvalue()
*
* @global string                  which tag is being processed
* @global string
* @global string
* @global integer                 to indicate if recursion is allowed
* @global string                  the topic at hand in the AIML file
*
* @param resource $parser        SAX XML resource handler
* @param string $name            name of the encountered tag.
* @param array $attrs            contains the additional attribues of a tag, like value, id, find.
*
* @return void
*/
function startElement($parser, $name, $attrs)
{
    global $whaton,$template,$pattern,$recursive,$topic;

    if (strtoupper($name)=="CATEGORY"){
        $whaton="CATEGORY";
    }
    elseif (strtoupper($name)=="TEMPLATE"){
        $whaton="TEMPLATE";
        $template="";
        $recursive=0;
    }
    elseif (strtoupper($name)=="PATTERN"){
        $whaton="PATTERN";
    }
    elseif ((strtoupper($name)=="THAT")&&(strtoupper($whaton)!="TEMPLATE")){
        $whaton="THAT";
    }
    elseif (strtoupper($name)=="TOPIC"){
        $whaton="TOPIC";
    }

    if ((strtoupper($whaton)=="PATTERN")&&(strtoupper($name)!="PATTERN")){


        if (strtoupper($name)=="BOT"){

            $attrs = upperkeysarray($attrs);
            $pattern .= getbotvalue($attrs["NAME"]);

        }
        else{
            $pattern .= "<$name";

            while (list ($key, $val) = each ($attrs)) {
                $pattern .= " $key=\"$val\" ";
            }

            $pattern .= ">";
        }

    }
    elseif ((strtoupper($whaton)=="TEMPLATE")&&(strtoupper($name)!="TEMPLATE")){

        $template .="<$name";

        while (list ($key, $val) = each ($attrs)) {
            $template .= " $key=\"$val\" ";
        }

        $template .=">";
    }
    elseif (strtoupper($whaton)=="TOPIC"){

        $attrs = upperkeysarray($attrs);
        $topic=$attrs["NAME"];

    }
}

/**
* Used by the AIML XML parser
*
* Looks for the end tags of 'topic' and 'category'.
*
* @uses insertmysentence()
* @uses insertmytemplate()
*
* @global string                  which tag is being processed
* @global string
* @global string
* @global integer                 to indicate if recursion is allowed
* @global string                  the topic at hand in the AIML file
* @global string                  the that of the category.
*
* @param resource $parser        SAX XML resource handler
* @param string $name            name of the encountered tag.
*
* @return void
*/
function endElement($parser, $name)
{

	global $whaton,$pattern,$template,$recursive,$topic,$that;

    if (strtoupper($name)=="TOPIC"){
        $topic="";
    }

    if (strtoupper($name)=="CATEGORY"){
        $template=trim($template);
        $topic=trim($topic);
        $that=trim($that);
        $pattern=trim($pattern);

        if ($that==""){
            $that="*";
        }
        if ($topic==""){
            $topic="*";
        }
        if ($pattern==""){
            $pattern="*";
        }


        $mybigsentence="<input> $pattern <that> $that <topic> $topic";

//echo $mybigsentence;

     $idused=insertmysentence($mybigsentence);

        insertmytemplate($idused,$template);

        // IIS doesn't flush properly unless it has a bunch of characters in it. This fills it with spaces.
		print "                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             ";
        flush();

        $pattern="";
        $template="";
        $that="";

    }
    else {
        if ((strtoupper($whaton)=="PATTERN")&&(strtoupper($name)!="PATTERN")){
            if (strtoupper($name)!="BOT"){
                $pattern .="</$name>";
            }
        }
        elseif ((strtoupper($whaton)=="TEMPLATE")&&(strtoupper($name)!="TEMPLATE")){
            $template .="</$name>";
        }
    }

}


/**
* Part of the AIML XML parser.
*
* Checks for start tags of pattern, topic, that, template and CDATA
*
* @global string
* @global string
* @global string
* @global string
* @global string
*
* @param resource $parser        SAX XML resource handler
* @param string $data            containing what needs to be parsed, I guess.
*
* @return void
*/
function handleme($parser, $data)
{
    global $whaton,$pattern,$template,$topic,$that;

    if (strtoupper($whaton)=="PATTERN"){
        $pattern .= $data;
    }
    elseif (strtoupper($whaton)=="TOPIC"){
        $topic .= $data;
    }
    elseif (strtoupper($whaton)=="THAT"){
        $that .= $data;
    }
    elseif (strtoupper($whaton)=="TEMPLATE"){
        $template .= $data;
    }
}


/**
* Parses startup.xml if the bot is loaded incrementally - one file at a time.
*
* Parses the startup.xml if the bot id loaded incrementally. One file at a time. This is very hacky and may not work properly.
*
* @uses learn()
*
* @global string                 The root directory of the AIML files?
* @global array                  This array will hold the files to LEARN
* @global boolean
* @global array                  Contains all the botnames.
* @global integer                contains the selected bot ID.
*
* @param integer $fileid         the ID of the file to be processed.
*/
function loadstartupinc($fileid){

	global $learnfiles,$areinc,$allbots,$selectbot;

	$areinc=0;

	if ($fileid==1){
		$areinc=1;
	}

//	print "<font size='3'>Loading startup.xml<BR></font>\n";
	$learnfiles = array(); # This array will hold the files to LEARN

	$file = get_custom_file_base()."/sources_custom/programe/aiml/" . "startup.xml";
	$xml_parser = xml_parser_create();
	xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,0);
	xml_set_element_handler($xml_parser, "startS", "endS");
	xml_set_character_data_handler ($xml_parser, "handlemeS");
	if (!($fp = fopen($file, "r"))) {
		die("could not open XML input");
	}
	while ($data = fread($fp, 4096)) {
		if (!xml_parse($xml_parser, $data, feof($fp))) {
			die(sprintf("XML error: %s at line %d",
						xml_error_string(xml_get_error_code($xml_parser)),
						xml_get_current_line_number($xml_parser)));
		}
	}
	xml_parser_free($xml_parser);

	# For each of the bots learn all of the files

	$totalcounter=1;

	foreach ($allbots as $bot){

		# print "<font size='3'><b>Loading bot: $bot<BR></b></font>\n";

	    $single_learnfiles = $learnfiles[$bot];
		$single_learnfiles = array_unique($single_learnfiles);

		foreach ($single_learnfiles as $file) {

			$selectbot=$bot;

			if ($totalcounter==$fileid){
				learn($file);
				return 0;
			}

			$totalcounter++;

		}
	}

	return 1;


}

/**
* Parses startup.xml
*
* Loads the startup.xml in the botloading process.
* XML parses the startup.xml file.
*
* @todo Seperate the XML reading from the processing code. Perhaps making a seperate class for this.
*
* @see botloader.php
* @see botloaderinc.php
* @uses learn()
* @uses startS()
* @uses endS()
* @uses handlemeS()
*
* @global string               the rootdirectory
* @global array                Files to be learned
* @global array                All the different bots in the startup.xml file
* @global integer              ID of the selected bot
* @global boolean              ????
*
* @return void                 prints HTML
*/
function loadstartup()
{

    global $learnfiles,$allbots,$selectbot,$areinc;

	$areinc=1;

//    print "<font size='3'>Loading startup.xml<BR></font>\n";
    $learnfiles = array(); // This array will hold the files to LEARN


    $file = get_custom_file_base()."/sources_custom/programe/aiml/" . "startup.xml";
    $xml_parser = xml_parser_create();
    xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,0);
    xml_set_element_handler($xml_parser, "startS", "endS");
    xml_set_character_data_handler ($xml_parser, "handlemeS");
    if (!($fp = fopen($file, "r"))) {
        die("could not open XML input");
    }
    while ($data = fread($fp, 4096)) {
        if (!xml_parse($xml_parser, $data, feof($fp))) {
            die(sprintf("XML error: %s at line %d",
                        xml_error_string(xml_get_error_code($xml_parser)),
                        xml_get_current_line_number($xml_parser)));
        }
    }
    xml_parser_free($xml_parser);

	# For each of the bots learn all of the files
	foreach ($allbots as $bot){
//		print "<font size='3'><b>Loading bot: $bot<BR></b></font>\n";
	    $single_learnfiles = $learnfiles[$bot];
		$single_learnfiles = array_unique($single_learnfiles);
		foreach ($single_learnfiles as $file) {
			$selectbot=$bot;
			learn($file);
		}
	}

}

/**
* Learn all the files in a directory ending with ".aiml"
*
* Read all the AIML filenames into the $learnfile Array. This array is then later on used by {@link learn()} in {@link handlemeS()}
*
* @global string           the rootdirectory
* @global array            contains all the filenames that need to be loaded into the bot.
*
* @param integer $curbot   ID of the current bot.
*
* @return void
*/
function learnallfiles($curbot)
{
    global $learnfiles;

    $dir=opendir (get_custom_file_base()."/sources_custom/programe/aiml/");
    while ($file = readdir($dir)) {

        if (substr($file,strpos($file,"."))==".aiml"){

            $learnfiles[$curbot][]=$file;
        }
    }

    closedir($dir);
}


/**
* Learn the AIML string.
*
* XML parse the AIML string. It seems to have a hardcoded time limit of 600, presume seconds.
*
* @uses startElement()
* @uses endElement()
* @uses handleme()
*
* @param string $xmlstring               the AIML category XML string
*
* @return void
*/
function learnstring($xmlstring)
{

    if (php_function_allowed('set_time_limit')) @set_time_limit(600);
    $xml_parser = xml_parser_create();
    xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,0);
    xml_set_element_handler($xml_parser, "startElement", "endElement");
    xml_set_character_data_handler ($xml_parser, "handleme");

    if (!xml_parse($xml_parser, $xmlstring)) {
        die(sprintf("XML error: %s at line %d",
                    xml_error_string(xml_get_error_code($xml_parser)),
                    xml_get_current_line_number($xml_parser)));
    }

    xml_parser_free($xml_parser);
}


/**
* Learn an AIML file.
*
* Learn a single AIML XML file. Again, the hardcoded time limit is set to 600, presumably seconds.
*
* @uses startElement()
* @uses endElement()
* @uses handleme()
*
* @global string               The root directory
*
* @param string $file          The AIML file name.
*/
function learn($file)
{

    if (php_function_allowed('set_time_limit')) @set_time_limit(600);
    $xml_parser = xml_parser_create();
    xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,0);
    xml_set_element_handler($xml_parser, "startElement", "endElement");
    xml_set_character_data_handler ($xml_parser, "handleme");
//    print "<font size='3'>Loading data aiml file: $file<BR></font>\n";
    flush();

    if (strtoupper(substr($file,0,7))=="HTTP://"){
        $file=$file;
    }
    else {
        $file=get_custom_file_base()."/sources_custom/programe/aiml/" . $file;
    }

    if (!($fp = fopen($file, "r"))) {
        die("could not open XML input");
    }

    while ($data = fread($fp, 4096)) {
        if (!xml_parse($xml_parser, $data, feof($fp))) {
            die(sprintf("XML error: %s at line %d",
                        xml_error_string(xml_get_error_code($xml_parser)),
                        xml_get_current_line_number($xml_parser)));
        }
    }
    fclose($fp);
    xml_parser_free($xml_parser);
}

/**
* Creates the PHP array code for subs.inc.
*
* Subs.inc contains PHP code written during the bot load process. This function creates the array PHP code and returns the code as a string.
*
* @uses cleanforsearch()
* @uses cleanforreplace()
*
* @param array          is an array with either gender, person(2), input and splitter details
* @param string         which array is being processed, is also the array namein subs.inc.
*
* @return string        the array PHP code
*/
function makesrphp($inarray,$sname)
{

    $myphp="\$" . $sname . "search=array(\n";

    for ($x=0;$x<count($inarray);$x++){

        $searchvar=cleanforsearch($inarray[$x][0]);

        $beginsearch="";
        $endsearch="";

        if (substr($searchvar,0,1)==" "){
            $beginsearch="\\b";
        }
        if ((substr($searchvar,strlen($searchvar)-1,1))==" "){
            $endsearch="\\b";
        }
        $myphp.="\"/$beginsearch" . trim($searchvar) . "$endsearch/ie\",\n";

    }

    $myphp.=");\n";

    $myphp.="\$" . $sname . "replace=array(\n";

    for ($x=0;$x<count($inarray);$x++){
        $myphp.="\"myfunc('" . cleanforreplace($inarray[$x][1]) . "')\",\n";
    }

    $myphp.=");\n";
    return $myphp;

}


/**
* Load an AIML string. String must be valid XML.
*
* This is going to need to take bot as a parameter
*
* @uses flushcache()
* @uses learnstring()
*
* @global integer           bot ID.
*
* @param string $aimlstring    The AIML string to be loaded.
* @param integer $botid        The bot's ID.
*
* @return void
*/
function loadaimlstring($aimlstring,$botid)
{

	global $selectbot;

	$selectbot=$botid;

	flushcache();
	learnstring($aimlstring);

}


/**
* Load an AIML string that is just a category.
*
* This is going to need to take bot as a parameter
*
* @uses flushcache()
* @uses learnstring()
*
* @global integer           bot ID.
*
* @param string $aimlstring    The AIML string to be loaded.
* @param integer $botid        The bot's ID.
*
* @return void
*/
function loadaimlcategory($aimlstring,$botid)
{

	global $selectbot;

	$selectbot=$botid;

	$aimlstring="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"" . "?" . "><aiml version=\"1.0\">" . $aimlstring . "</aiml>";


	flushcache();

	learnstring($aimlstring);

}


/**
* Create the array PHP code for the sentence splitters
*
* In AIML it is custom to return a reply for every sentence the user inputs.
* This means the sentence input needs to be split into individual sentences.
* However, some people find that the semi-colon should also be added, while
* others think it should. The user will define the sentence splitters in
* startup.xml and these will then be stored in a PHP file included by the
* application.
*
* @param array $splitterarray       containing the sentence splitters found in startup.xml
*
* @return string                    PHP array code for storing the sentence splitters
*/
function makesplitterphp($splitterarray)
{
    $splitterphp="\$likeperiodsearch=array(\n";
    for ($x=0;$x<count($splitterarray);$x++){

        $splitterphp.="\"" . $splitterarray[$x] . "\",\n";

    }
    $splitterphp.=");\n";

    $splitterphp.="\$likeperiodreplace=array(\n";
    for ($x=0;$x<count($splitterarray);$x++){

        $splitterphp.="\"" . "." . "\",\n";

    }
    $splitterphp.=");\n";

    return $splitterphp;
}



/**
* Add slashes for replacement purposes
*
* Making a string safe to preform a replacement function on.
*
* @param string $input           string to be cleaned.
*
* @return string                 cleaned string.
*/
function cleanforreplace($input)
{
    $input = str_replace("\\", "\\\\", $input);
    $input = str_replace("\"", "\\\"", $input);
    $input = str_replace("'", "\'", $input);
    return trim($input);
}



/**
* Add slashes for database query purpose
*
* Making a string safe to put the text in an SQL query.
*
* @param string $input           string to be cleaned.
*
* @return string                 cleaned string.
*/
function cleanforsearch($input)
{
    $input = str_replace("\\", "\\\\\\\\", $input);
    $input = str_replace("\"", "\\\"", $input);
    $input = str_replace("'", "\'", $input);
    $input = str_replace("/", "\/", $input);
    $input = str_replace("(", "\(", $input);
    $input = str_replace(")", "\)", $input);
    $input = str_replace(".", "\.", $input);
    return $input;
}


/**
* Creates the subs.inc file.
*
* Creates the subs.inc file and fills it with 5 arrays that correspond to the 4 substitution sections and sentence splitters.
*
* @uses makesrphp()
* @uses makesplitterphp()
* @uses createsubfile()
* @uses addtosubs()
*
* @global array              contains the gender substitutions
* @global array              contains the person substitutions
* @global array              contains the person2 substitutions
* @global array              ?????
* @global array              contains the sentence splitters
*
* @return void               Writes a file.
*/

function makesubscode()
{

    global $genderarray,$personarray,$person2array,$inputarray,$splitterarray;

    $genderphp = makesrphp($genderarray, "gender");
    $personphp = makesrphp($personarray, "firstthird");
    $person2php = makesrphp($person2array, "firstsecond");
    $inputphp = makesrphp($inputarray, "contract");
    $splitterphp = makesplitterphp($splitterarray);

    createsubfile();
    addtosubs("<"."?\n");
    addtosubs($genderphp);
    addtosubs($personphp);
    addtosubs($person2php);
    addtosubs($inputphp);
    addtosubs($splitterphp);
    addtosubs("\n?".">");

}

