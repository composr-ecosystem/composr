{TITLE}

<div class="float_surrounder">
	<div class="whos_read">
		{+START,IF_NON_EMPTY,{WHOS_READ}}
			<div class="box box___messaging_message_screen"><div class="box_inner">
				<h2>{!THIS_HAS_BEEN_READ_BY}</h2>

				<ul class="nl">
					{+START,LOOP,WHOS_READ}
						<li><a title="{USERNAME*}" href="{MEMBER_URL*}">{$DISPLAYED_USERNAME*,{USERNAME}}</a> {+START,INCLUDE,MEMBER_TOOLTIP}SUBMITTER={MEMBER_ID}{+END} &ndash; <span class="associated_details">{DATE*}</span></li>
					{+END}
				</ul>
			</div></div>
		{+END}
	</div>

	<div class="message_main">
		<div class="box box___messaging_message_screen"><div class="box_inner">
			<h2>{MESSAGE_TITLE*} <span class="associated_details">({!BY_SIMPLE,{BY*}})</span></h2>

			{MESSAGE}
		</div></div>
	</div>
</div>

{+START,IF_NON_PASSED,RESPONSIBLE}
	<p class="responsibility_bit">{!PLEASE_TAKE_RESPONSIBILITY,<a href="{TAKE_RESPONSIBILITY_URL*}">{!TAKE_RESPONSIBILITY}</a>}</p>
{+END}
{+START,IF_PASSED,RESPONSIBLE}
	<p class="responsibility_bit">{!CANT_TAKE_RESPONSIBILITY,<strong>{RESPONSIBLE*}</strong>}</p>
{+END}

{+START,IF,{$HAS_FORUM}}
	<hr class="spaced_rule" />

	<p>
		{!DISCUSS_BELOW}
	</p>

	{COMMENT_DETAILS}
{+END}
