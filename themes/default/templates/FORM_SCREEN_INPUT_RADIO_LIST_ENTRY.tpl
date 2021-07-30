<p>
	<label for="j-{NAME|*}-{VALUE|*}"><input tabindex="{TABINDEX*}" class="input-radio" type="radio" id="j-{NAME|*}-{VALUE|*}" name="{NAME*}" value="{VALUE*}"{+START,IF,{CHECKED}} checked="checked"{+END}{+START,IF,{DISABLED}} disabled="disabled"{+END} /> {TEXT}</label>
</p>

{+START,IF_NON_EMPTY,{DESCRIPTION}}
	<div class="associated-details radio-description">{DESCRIPTION*}</div>
{+END}
