{TITLE}

{+START,IF_NON_PASSED,TEXT}
	<p>
		{!CONFIRM_TEXT}
	</p>

	<div class="box box___confirm_screen"><div class="box_inner">
		{PREVIEW}
	</div></div>
{+END}

{+START,IF_PASSED,TEXT}
	{TEXT}
{+END}

<form title="{!PRIMARY_PAGE_FORM}"{+START,IF_NON_PASSED_OR_FALSE,GET} method="post" action="{URL*}"{+END}{+START,IF_PASSED_AND_TRUE,GET} method="get" action="{$URL_FOR_GET_FORM*,{URL}}"{+END} autocomplete="off">
	{+START,IF_NON_PASSED_OR_FALSE,GET}{$INSERT_SPAMMER_BLACKHOLE}{+END}

	{+START,IF_PASSED_AND_TRUE,GET}{$HIDDENS_FOR_GET_FORM,{URL}}{+END}

	{+START,IF_PASSED,HIDDEN}{HIDDEN}{+END}

	<div>
		{FIELDS}

		<p class="proceed_button">
			{+START,IF_NON_PASSED,BACK_URL}
				{+START,IF,{$JS_ON}}
					<input class="button_screen buttons__back" type="button" onclick="history.back(); return false;" value="{!GO_BACK}" />
				{+END}
			{+END}

			<input onclick="disable_button_just_clicked(this);" accesskey="u" class="button_screen buttons__proceed" type="submit" value="{!PROCEED}" />
		</p>
	</div>
</form>

{+START,IF_PASSED,BACK_URL}
	<form class="back_button" title="{!NEXT_ITEM_BACK}" action="{BACK_URL*}" method="post" autocomplete="off">
		{$INSERT_SPAMMER_BLACKHOLE}
		<div>
			{FIELDS}
			<button class="button_icon" type="submit"><img title="{!NEXT_ITEM_BACK}" alt="{!NEXT_ITEM_BACK}" src="{$IMG*,icons/48x48/menu/_generic_admin/back}" /></button>
		</div>
	</form>
{+END}

{+START,IF_PASSED,JAVASCRIPT}
	<script>// <![CDATA[
		{JAVASCRIPT/}
	//]]></script>
{+END}

