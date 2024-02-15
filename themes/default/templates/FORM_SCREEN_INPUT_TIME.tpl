{$REQUIRE_JAVASCRIPT,jquery}

{$SET,time_value,{$PAD_LEFT,{HOUR},2,0}:{$PAD_LEFT,{MINUTE},2,0}}
<input name="{NAME*}" id="{NAME*}" class="form-control" type="time" size="5"{+START,IF_PASSED,TABINDEX} tabindex="{TABINDEX*}"{+END} value="{+START,IF_NON_EMPTY,{HOUR}}{$GET*,time_value}{+END}" data-tpl="formScreenInputTime" data-tpl-params="{+START,PARAMS_JSON,NAME}{_*}{+END}"{+START,IF,{READ_ONLY}} readonly="readonly"{+END} />
