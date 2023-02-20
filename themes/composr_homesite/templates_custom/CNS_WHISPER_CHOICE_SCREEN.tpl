{+START,SET,whisper_screen_text}
	{+START,IF,{$EQ,{$USERNAME,{$_GET,intended_solely_for}},Chris Graham}}
		{+START,BOX}
			You are sending a whisper to Chris Graham. Please don't ask for free technical support from Chris, he has a full time job and only enough free time to contribute casually to Composr development, not support users directly.
		{+END}
	{+END}
{+END}

{+START,INCLUDE,CNS_WHISPER_CHOICE_SCREEN}{+END}
