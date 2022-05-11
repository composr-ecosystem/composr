{TITLE}

{$SET,COUNT,{COUNT}}

<form title="{!PRIMARY_PAGE_FORM}" method="post" action="{URL*}" autocomplete="off" onsubmit="return modsecurity_workaround(this);" data-modsecurity-workaround="1">
	{$INSERT_SPAMMER_BLACKHOLE}

	<div>
		<input type="hidden" name="theme" value="{THEME*}" />
		<input type="hidden" name="template_preview_op" value="1" />

		{+START,IF,{MULTIPLE}}
			<div class="float_surrounder">
				<p class="right">
					<label for="template_switcher">{!TEMPLATE}</label>:
					<select onchange="document.getElementById('template_editing_'+(window.previous_value?window.previous_value:{FIRST_ID*})).style.display='none'; document.getElementById('template_editing_'+this.options[this.selectedIndex].value).style.display='block'; window.previous_value=this.options[this.selectedIndex].value; /* Workaround to make editarea render right */ eAL.toggle('f'+this.options[this.selectedIndex].value+'_new'); eAL.toggle('f'+this.options[this.selectedIndex].value+'_new');" id="template_switcher" name="template_switcher">
						{+START,LOOP,TEMPLATES}
							<option value="{I*}">{FILE*}</option>
						{+END}
					</select>
				</p>
			</div>
		{+END}

		{TEMPLATE_EDITORS}

		<div class="float_surrounder buttons_group">
			{+START,IF,{$EQ,{$GET,COUNT},1}}
				{$GET,button}
			{+END}

			<div class="right">
				<input onclick="disable_button_just_clicked(this); this.form.target='_self'; this.form.action='{URL;*}';" class="button_screen buttons__save" type="submit" value="{!SAVE}" />
			</div>
			{+START,IF,{$JS_ON}}
				<div class="right">
					<input onclick="disable_button_just_clicked(this); this.form.target='save_frame'; this.form.action='{URL;*}{$?,{$IN_STR,{URL},?},&amp;,?}save_and_stay=1';" accesskey="U" class="button_screen buttons__save_and_stay" type="submit" value="{!SAVE_AND_STAY}" />
				</div>
			{+END}
		</div>
	</div>
</form>

<iframe name="save_frame" id="save_frame" title="{!SAVE_AND_STAY}" class="hidden_save_frame" src="{$BASE_URL*}/uploads/index.html">{!SAVE_AND_STAY}</iframe>
