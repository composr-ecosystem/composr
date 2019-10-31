{+START,IF,{SYSTEM_MESSAGE}}
	<p>{MESSAGE}</p>
{+END}
{+START,IF,{$NOT,{SYSTEM_MESSAGE}}}
	<div class="box box---chat-private"><div class="box-inner">
		<div><span class="chat-message-by">{USERNAME*}</span></div>
		<p class="chat-private-message">{MESSAGE}</p>
	</div></div>
{+END}
