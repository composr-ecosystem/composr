{TITLE}

{$GET,whisper_screen_text}

<div class="cns-whisper-lead-in">
	{+START,INCLUDE,ICON}
		NAME=buttons/add_topic
		ICON_SIZE=48
		ICON_CLASS=right
	{+END}

	{+START,IF,{$HAS_PRIVILEGE,use_pt}}<p>{!WHISPER_TEXT}</p>{+END}
</div>


<div class="clearfix">
	{+START,IF,{$HAS_PRIVILEGE,use_pt}}
		<div class="cns-whisper-choose-box right">
			<div class="box box---cns-whisper-choice-screen"><div class="box-inner">
				<h2>{!PRIVATE_TOPIC}</h2>

				<form title="{!PRIVATE_TOPIC}" action="{$URL_FOR_GET_FORM*,{URL}}" method="get">
					{$HIDDENS_FOR_GET_FORM,{URL}}

					<div>
						<p>{!WHISPER_PT,{$DISPLAYED_USERNAME*,{USERNAME}}}</p>

						<input type="hidden" name="type" value="new_pt" />

						<p class="proceed-button">
							<button class="btn btn-primary btn-scr buttons--add-topic" type="submit" data-disable-on-click="1">{+START,INCLUDE,ICON}NAME=buttons/add_topic{+END} <span>{!QUOTE_TO_PT}</span></button>
						</p>
					</div>
				</form>
			</div></div>
		</div>
	{+END}

	<div class="cns-whisper-choose-box">
		<div class="box box---cns-whisper-choice-screen"><div class="box-inner">
			<h2>{!PERSONAL_POST}</h2>

			<form title="{!PERSONAL_POST}" action="{$URL_FOR_GET_FORM*,{URL}}" method="get">
				{$HIDDENS_FOR_GET_FORM,{URL}}

				<div>
					<p>{!WHISPER_PP,{$DISPLAYED_USERNAME*,{USERNAME}}}</p>

					<input type="hidden" name="type" value="new_post" />

					<p class="proceed-button">
						<button class="btn btn-primary btn-scr buttons--new-post-full" type="submit" data-disable-on-click="1">{+START,INCLUDE,ICON}NAME=buttons/new_post_full{+END} <span>{!IN_TOPIC_PP}</span></button>
					</p>
				</div>
			</form>
		</div></div>
	</div>
</div>

<p class="back-button">
	<a href="#!" data-cms-btn-go-back="1" title="{!NEXT_ITEM_BACK}">{+START,INCLUDE,ICON}
		NAME=admin/back
		ICON_SIZE=48
	{+END}</a>
</p>
