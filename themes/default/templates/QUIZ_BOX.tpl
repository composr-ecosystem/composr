<section class="box box---quiz-box"><div class="box-inner">
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

	<div class="meta-details" role="note">
		<dl class="meta-details-list">
			{+START,IF_NON_EMPTY,{TIMEOUT}}
				<dt class="field-name">{!TIMEOUT}:</dt> <dd>{TIMEOUT*}</dd>
			{+END}
			{+START,IF_NON_EMPTY,{REDO_TIME}}
				<dt class="field-name">{!REDO_TIME}:</dt> <dd>{REDO_TIME*}</dd>
			{+END}
			<dt class="field-name">{!ADDED}:</dt> <dd>{DATE*}</dd>
			{+START,IF_NON_EMPTY,{POINTS}}
				<dt class="field-name">{!POINTS}:</dt> <dd>{POINTS*}</dd>
			{+END}
		</dl>
	</div>

	{+START,IF_NON_EMPTY,{START_TEXT}}
		<p>
			{START_TEXT}
		</p>
	{+END}

	<div class="shunted-button">
		<form title="{!START} {!QUIZ}: {NAME*}" method="post" action="{URL*}">
			<button class="btn btn-primary btn-scri buttons--more" type="submit">{+START,INCLUDE,ICON}NAME=buttons/more{+END} {!START}</button>
		</form>
	</div>
</div></section>
