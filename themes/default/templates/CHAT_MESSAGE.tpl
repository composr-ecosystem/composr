<div class="chat-message {+START,IF,{OLD_MESSAGES}} chat-message-old{+END}">
	{+START,IF_NON_EMPTY,{AVATAR_URL}}
		<img class="chat-avatar" src="{$ENSURE_PROTOCOL_SUITABILITY*,{AVATAR_URL}}" alt="{!AVATAR}" />
	{+END}

	{+START,IF,{$NOT,{SYSTEM_MESSAGE}}}
		<blockquote style="{+START,IF_NON_EMPTY,{FONT_COLOUR}}color: #{FONT_COLOUR'}; {+END}{+START,IF_NON_EMPTY,{FONT_FACE}}font-family: {FONT_FACE|}{+END}">{MESSAGE}</blockquote>

		<div><span class="chat-message-by{+START,IF,{STAFF}} chat-operator-staff{+END}">{!BY_SIMPLE,{MEMBER_LINK}}</span> <span class="horiz-field-sep associated-details">({DATE*})</span> {STAFF_ACTIONS}</div>
	{+END}

	{+START,IF,{SYSTEM_MESSAGE}}
		<blockquote style="{+START,IF_NON_EMPTY,{FONT_COLOUR}}color: #{FONT_COLOUR'}; {+END}{+START,IF_NON_EMPTY,{FONT_FACE}}font-family: {FONT_FACE|}{+END}"><strong>{MESSAGE}</strong></blockquote>

		<div><span class="chat-message-by{+START,IF,{STAFF}} chat-operator-staff{+END}">{!BY_SIMPLE,{!SYSTEM}}</span> <span class="horiz-field-sep associated-details">({DATE*})</span></div>
	{+END}
</div>
