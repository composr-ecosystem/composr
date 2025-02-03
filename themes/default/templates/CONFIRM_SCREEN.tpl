<div data-tpl="confirmScreen" data-tpl-params="{+START,PARAMS_JSON,JAVASCRIPT}{_*}{+END}">
	{TITLE}

	{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
	{+START,IF_PASSED,WARNING_DETAILS}
		{WARNING_DETAILS}
	{+END}

	{+START,IF_NON_PASSED,TEXT}
		<p>
			{!CONFIRM_TEXT}
		</p>

		{+START,IF,{$IN_STR,{PREVIEW},class="box"}}
			<div class="box box---confirm-screen"><div class="box-inner">
				{PREVIEW}
			</div></div>
		{+END}

		{+START,IF,{$NOT,{$IN_STR,{PREVIEW},class="box"}}}
			{PREVIEW}
		{+END}
	{+END}

	{+START,IF_PASSED,TEXT}
		{TEXT}
	{+END}

	<form title="{!PRIMARY_PAGE_FORM}"{+START,IF_NON_PASSED_OR_FALSE,GET} method="post" action="{URL*}"{+END}{+START,IF_PASSED_AND_TRUE,GET} method="get" action="{$URL_FOR_GET_FORM*,{URL}}"{+END}>
		{+START,IF_NON_PASSED_OR_FALSE,GET}{$INSERT_FORM_POST_SECURITY}{+END}

		{+START,IF_PASSED_AND_TRUE,GET}{$HIDDENS_FOR_GET_FORM,{URL}}{+END}

		{+START,IF_PASSED,HIDDEN}{HIDDEN}{+END}

		<div>
			{FIELDS}

			<p class="proceed-button">
				{+START,IF_NON_PASSED,BACK_URL}
					<button class="btn btn-primary btn-scr buttons--back" type="button" data-cms-btn-go-back="1">{+START,INCLUDE,ICON}NAME=buttons/back{+END} <span>{!GO_BACK}</span></button>
				{+END}

				<button data-disable-on-click="1" accesskey="u" class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} {!PROCEED}</button>
			</p>
		</div>
	</form>

	{+START,IF_PASSED,BACK_URL}
		<form class="back-button" title="{!NEXT_ITEM_BACK}" action="{BACK_URL*}" method="post">
			{$INSERT_FORM_POST_SECURITY}

			<div>
				{FIELDS}
				<button class="button-icon" type="submit" title="{!NEXT_ITEM_BACK}">
					{+START,INCLUDE,ICON}
						NAME=admin/back
						ICON_SIZE=48
					{+END}
				</button>
			</div>
		</form>
	{+END}
</div>
