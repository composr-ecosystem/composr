<section class="box box___quiz_box"><div class="box_inner">
	{+START,SET,content_box_title}
		{+START,IF,{GIVE_CONTEXT}}
			{!CONTENT_IS_OF_TYPE,{TYPE*},{NAME*}}
		{+END}

		{+START,IF,{$NOT,{GIVE_CONTEXT}}}
			{+START,FRACTIONAL_EDITABLE,{NAME},title,_SEARCH:cms_quiz:__edit:{ID},0}{NAME*}{+END}
		{+END}
	{+END}
	{+START,IF,{$NOT,{$GET,skip_content_box_title}}}
		<h3>{$GET,content_box_title}</h3>
	{+END}

	<div class="meta_details" role="note">
		<dl class="meta_details_list">
			{+START,IF_NON_EMPTY,{TIMEOUT}}
				<dt class="field_name">{!TIMEOUT}:</dt> <dd>{TIMEOUT*}</dd>
			{+END}
			{+START,IF_NON_EMPTY,{REDO_TIME}}
				<dt class="field_name">{!REDO_TIME}:</dt> <dd>{REDO_TIME*}</dd>
			{+END}
			<dt class="field_name">{!ADDED}:</dt> <dd>{DATE*}</dd>
			{+START,IF_NON_EMPTY,{POINTS}}
				<dt class="field_name">{!POINTS}:</dt> <dd>{POINTS*}</dd>
			{+END}
		</dl>
	</div>

	{+START,IF_NON_EMPTY,{START_TEXT}}
		<p>
			{START_TEXT}
		</p>
	{+END}

	<div class="shunted_button">
		<form title="{!START} {!QUIZ}: {NAME*}" method="post" action="{URL*}" autocomplete="off">
			<input class="button_screen_item buttons__more" type="submit" value="{!START}" />
		</form>
	</div>
</div></section>
