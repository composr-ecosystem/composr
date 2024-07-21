<div data-tpl="installerForumChoice" data-tpl-params="{+START,PARAMS_JSON,VERSIONS}{_*}{+END}">
	<label for="{CLASS*}">
		<input {EXTRA}{+START,IF,{$EQ,{CLASS},{REC}}} checked="checked"{+END} type="radio" name="forum" id="{CLASS*}" value="{CLASS*}" class="js-click-do-forum-choose" />
		<span>{TEXT*}</span>
	</label>
</div>
