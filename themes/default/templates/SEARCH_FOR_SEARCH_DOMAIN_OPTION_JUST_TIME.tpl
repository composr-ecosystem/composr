<div class="search-option clearfix">
	{+START,IF_NON_PASSED_OR_FALSE,HAS_RANGE}
		<label for="{NAME*}">{DISPLAY*}:</label>

		{+START,INCLUDE,FORM_SCREEN_INPUT_TIME}
			NAME={NAME}
			TYPE=time
			REQUIRED=0
			UNLIMITED=1
			READ_ONLY=0
			HOUR={$PREG_REPLACE,^(\d+):\d\d$,$1,{SPECIAL}}
			MINUTE={$PREG_REPLACE,^\d+:(\d\d)$,$1,{SPECIAL}}
		{+END}
	{+END}

	{+START,IF_PASSED_AND_TRUE,HAS_RANGE}
		<label for="{NAME*}_from">{DISPLAY*}<span class="accessibility-hidden">, {!FROM}</span>:</label>
		<label for="{NAME*}_to" class="accessibility-hidden">>{DISPLAY*}, {!TO}:</label>

		{+START,INCLUDE,FORM_SCREEN_INPUT_TIME}
			NAME={NAME}_from
			TYPE=time
			REQUIRED=0
			UNLIMITED=1
			READ_ONLY=0
			HOUR={$PREG_REPLACE,^(\d+):\d\d$,$1,{$PREG_REPLACE,;.*$,,{SPECIAL}}}
			MINUTE={$PREG_REPLACE,^\d+:(\d\d)$,$1,{$PREG_REPLACE,;.*$,,{SPECIAL}}}
		{+END}

		&ndash;

		{+START,INCLUDE,FORM_SCREEN_INPUT_TIME}
			NAME={NAME}_to
			TYPE=time
			REQUIRED=0
			UNLIMITED=1
			READ_ONLY=0
			HOUR={$PREG_REPLACE,^(\d+):\d\d$,$1,{$PREG_REPLACE,^.*;,,{SPECIAL}}}
			MINUTE={$PREG_REPLACE,^\d+:(\d\d)$,$1,{$PREG_REPLACE,^.*;,,{SPECIAL}}}
		{+END}
	{+END}
</div>
