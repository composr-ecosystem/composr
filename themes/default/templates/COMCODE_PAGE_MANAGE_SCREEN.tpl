{TITLE}

{$PARAGRAPH,{TEXT}}

{TABLE}

<form title="{!PRIMARY_PAGE_FORM}" action="{$URL_FOR_GET_FORM*,{URL}}" method="get" autocomplete="off">
	{$HIDDENS_FOR_GET_FORM,{URL}}

	<p>
		<label for="filter">{!FILTER}:</label>
		<input type="text" id="filter" name="filter" value="{FILTER*}" onkeypress="if (enter_pressed(event)) { this.form.submit(); return false; }" />
		<input class="button_micro buttons__filter" type="submit" value="{!FILTER}" />
	</p>
</form>

{+START,IF_PASSED,EXTRA}
	{+START,IF_PASSED,SUB_TITLE}<h2 class="force_margin">{SUB_TITLE*}</h2>{+END}

	{EXTRA}
{+END}

{+START,IF_NON_EMPTY,{LINKS}}
	<h2>{!ADVANCED}</h2>

	<ul class="actions_list">
		{+START,LOOP,LINKS}
			<li style="background-image: url('{LINK_IMAGE;*}'); background-size: 18px 18px; background-position: 0 0; padding-left: 20px"><a href="{LINK_URL*}">{LINK_TEXT*}</a></li>
		{+END}
	</ul>
{+END}
