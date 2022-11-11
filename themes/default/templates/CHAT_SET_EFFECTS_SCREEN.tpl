{TITLE}

{CHAT_SOUND}

<p>
	{!CHOOSE_SOUND_EFFECTS}
</p>

<form class="chat-set-effects" title="{!PRIMARY_PAGE_FORM}" action="{POST_URL*}" method="post" enctype="multipart/form-data" data-view="SubmissionFlow" data-view-params="{+START,INCLUDE,FORM_STANDARD_START}{+END}">
	{$INSERT_FORM_POST_SECURITY}

	{HIDDEN}

	<div>
		{SETTING_BLOCKS}

		{+START,INCLUDE,FORM_STANDARD_END}{+END}
	</div>
</form>
