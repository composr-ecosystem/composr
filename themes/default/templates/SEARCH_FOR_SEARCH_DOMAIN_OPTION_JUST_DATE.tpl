<div class="search-option clearfix">
	{+START,IF_NON_PASSED_OR_FALSE,HAS_RANGE}
		<label for="{NAME*}">{DISPLAY*}:</label>

		{+START,INCLUDE,FORM_SCREEN_INPUT_DATE}
			NAME={NAME}
			TYPE=date
			REQUIRED=0
			UNLIMITED=1
			READ_ONLY=0
			DAY={$PREG_REPLACE,^\d\d\d\d\-\d\d\-(\d\d)$,$1,{SPECIAL}}
			MONTH={$PREG_REPLACE,^\d\d\d\d\-(\d\d)\-\d\d$,$1,{SPECIAL}}
			YEAR={$PREG_REPLACE,^(\d\d\d\d)\-\d\d\-\d\d$,$1,{SPECIAL}}
			HOUR=
			MINUTE=
			MIN_DATE_DAY=
			MIN_DATE_MONTH=
			MIN_DATE_YEAR=
			MAX_DATE_DAY=
			MAX_DATE_MONTH=
			MAX_DATE_YEAR=
		{+END}
	{+END}

	{+START,IF_PASSED_AND_TRUE,HAS_RANGE}
		<label for="{NAME*}_from">{DISPLAY*}<span class="accessibility-hidden">, {!FROM}</span>:</label>
		<label for="{NAME*}_to" class="accessibility-hidden">>{DISPLAY*}, {!TO}:</label>

		{+START,INCLUDE,FORM_SCREEN_INPUT_DATE}
			NAME={NAME}_from
			TYPE=date
			REQUIRED=0
			UNLIMITED=1
			READ_ONLY=0
			DAY={$PREG_REPLACE,^\d\d\d\d\-\d\d\-(\d\d)$,$1,{$PREG_REPLACE,;.*$,,{SPECIAL}}}
			MONTH={$PREG_REPLACE,^\d\d\d\d\-(\d\d)\-\d\d$,$1,{$PREG_REPLACE,;.*$,,{SPECIAL}}}
			YEAR={$PREG_REPLACE,^(\d\d\d\d)\-\d\d\-\d\d$,$1,{$PREG_REPLACE,;.*$,,{SPECIAL}}}
			HOUR=
			MINUTE=
			MIN_DATE_DAY=
			MIN_DATE_MONTH=
			MIN_DATE_YEAR=
			MAX_DATE_DAY=
			MAX_DATE_MONTH=
			MAX_DATE_YEAR=
		{+END}

		&ndash;

		{+START,INCLUDE,FORM_SCREEN_INPUT_DATE}
			NAME={NAME}_to
			TYPE=date
			REQUIRED=0
			UNLIMITED=1
			READ_ONLY=0
			DAY={$PREG_REPLACE,^\d\d\d\d\-\d\d\-(\d\d)$,$1,{$PREG_REPLACE,^.*;,,{SPECIAL}}}
			MONTH={$PREG_REPLACE,^\d\d\d\d\-(\d\d)\-\d\d$,$1,{$PREG_REPLACE,^.*;,,{SPECIAL}}}
			YEAR={$PREG_REPLACE,^(\d\d\d\d)\-\d\d\-\d\d$,$1,{$PREG_REPLACE,^.*;,,{SPECIAL}}}
			HOUR=
			MINUTE=
			MIN_DATE_DAY=
			MIN_DATE_MONTH=
			MIN_DATE_YEAR=
			MAX_DATE_DAY=
			MAX_DATE_MONTH=
			MAX_DATE_YEAR=
		{+END}
	{+END}
</div>
