<section class="box box---block-main-quotes"><div class="box-inner">
	{+START,IF_NON_EMPTY,{CHANNEL_TITLE}}<h3>{CHANNEL_TITLE*}</h3>{+END}
	{+START,IF_EMPTY,{CHANNEL_ERROR}}
		<div class="webstandards-checker-off">
			{CONTENT`}
		</div>
	{+END}
	{+START,IF_NON_EMPTY,{CHANNEL_ERROR}}
		<div class="webstandards-checker-off">
			{+START,IF,{$IS_ADMIN}}
				<b>Channel Name:</b> <a href='{CHANNEL_URL}' target='_blank'>{CHANNEL_NAME}</a> <br />
				{CHANNEL_ERROR} <br />
				You are seeing the block error message(s) because you are an admin.<br />
				Normal web site guests and members won't see this.<br />
			{+END}
			{+START,IF,{$NOT,{$IS_ADMIN}}}
				Sorry, we are experiencing technical difficulties with YouTube.<br />
				Please check back later.<br />
			{+END}
		</div>
	{+END}
</div></section>
