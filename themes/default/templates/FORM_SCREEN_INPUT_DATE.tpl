{$REQUIRE_JAVASCRIPT,jquery}

{$SET,date_value,{$PAD_LEFT,{YEAR},4,0}-{$PAD_LEFT,{MONTH},2,0}-{$PAD_LEFT,{DAY},2,0}}
{$SET,date_value_min,{$?,{$IS_NON_EMPTY,{MIN_DATE_YEAR}},{$PAD_LEFT,{MIN_DATE_YEAR},4,0}-{$PAD_LEFT,{MIN_DATE_MONTH},2,0}-{$PAD_LEFT,{MIN_DATE_DAY},2,0}}}
{$SET,date_value_max,{$?,{$IS_NON_EMPTY,{MAX_DATE_YEAR}},{$PAD_LEFT,{MAX_DATE_YEAR},4,0}-{$PAD_LEFT,{MAX_DATE_MONTH},2,0}-{$PAD_LEFT,{MAX_DATE_DAY},2,0}}}
<input {+START,IF_PASSED,AUTOCOMPLETE} autocomplete="{AUTOCOMPLETE*}"{+END} name="{NAME*}" id="{NAME*}" type="date" size="10"{+START,IF_PASSED,TABINDEX} tabindex="{TABINDEX*}"{+END} value="{+START,IF_NON_EMPTY,{YEAR}}{$GET*,date_value}{+END}"{+START,IF_NON_EMPTY,{$GET,date_value_min}} min="{$GET*,date_value_min}"{+END}{+START,IF_NON_EMPTY,{$GET,date_value_max}} max="{$GET*,date_value_max}"{+END} class="form-control input-date{$?,{REQUIRED},-required}"{+START,IF,{READ_ONLY}} readonly="readonly"{+END} />

{+START,IF,{$EQ,{TYPE},datetime}}
	{$SET,time_value,{$PAD_LEFT,{HOUR},2,0}:{$PAD_LEFT,{MINUTE},2,0}}
	<label class="accessibility-hidden" for="{NAME*}_time">{!TIME}</label>
	<input name="{NAME*}_time" id="{NAME*}_time" type="time" size="5"{+START,IF_PASSED,TABINDEX} tabindex="{TABINDEX*}"{+END} value="{+START,IF_NON_EMPTY,{HOUR}}{$GET*,time_value}{+END}" class="form-control input-time{$?,{REQUIRED},-required}" data-tpl="formScreenInputDate" data-tpl-params="{+START,PARAMS_JSON,NAME}{_*}{+END}"{+START,IF,{READ_ONLY}} readonly="readonly"{+END} />
{+END}

{+START,IF_PASSED,TIMEZONE}
	<input name="{NAME*}_timezone" id="{NAME*}_timezone" type="hidden" value="{TIMEZONE*}" />
{+END}
