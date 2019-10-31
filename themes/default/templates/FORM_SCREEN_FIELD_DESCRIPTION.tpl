{+START,IF,{$NOT,{$MATCH_KEY_MATCH,_WILD:quiz}}}
	{+START,IF_PASSED,DESCRIPTION}{+START,IF_NON_EMPTY,{DESCRIPTION}}
		{$REQUIRE_JAVASCRIPT,core_form_interfaces}
		<a data-cms-rich-tooltip="{ haveLinks: true }" class="leave-native-tooltip help-icon{+START,IF_PASSED_AND_TRUE,LEFT} left{+END}{+START,IF_PASSED_AND_TRUE,RIGHT} right{+END}" title="{DESCRIPTION=}" href="#!">{+START,INCLUDE,ICON}
			NAME=help
			ICON_SIZE=24
		{+END}</a>
	{+END}{+END}
{+END}

{+START,IF,{$MATCH_KEY_MATCH,_WILD:quiz}}
	{DESCRIPTION}
{+END}
