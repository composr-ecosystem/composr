<div data-tpl="chatSiteWideImPopup">
	{CONTENT}

	<form class="chat-sound-effects-checkbox inline" title="{!SOUND_EFFECTS}" action="#" method="post">
		{$INSERT_FORM_POST_SECURITY}

		<p>
			<label for="play_sound">{!SOUND_EFFECTS}:</label> <input type="checkbox" id="play_sound" name="play_sound" checked="checked" />
		</p>
	</form>

	<ul class="actions-list">
		<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} {!GOTO_CHAT_LOBBY_FOR_MORE,{$PAGE_LINK*,_SEARCH:chat}}</li>
	</ul>

	{CHAT_SOUND}
</div>
