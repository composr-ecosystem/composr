<div class="chat_message{+START,IF,{OLD_MESSAGES}} chat_message_old{+END}">
	{+START,IF_NON_EMPTY,{AVATAR_URL}}
		<img class="chat_avatar" src="{$ENSURE_PROTOCOL_SUITABILITY*,{AVATAR_URL}}" alt="{!AVATAR}" />
	{+END}

	{+START,IF,{$NOT,{SYSTEM_MESSAGE}}}
		<blockquote style="{+START,IF_NON_EMPTY,{FONT_COLOUR}}color: {FONT_COLOUR'}; {+END}{+START,IF_NON_EMPTY,{FONT_FACE}}font-family: {FONT_FACE|}{+END}">{MESSAGE}</blockquote>

		<div><span class="chat_message_by{+START,IF,{STAFF}} chat_operator_staff{+END}">{!BY_SIMPLE,{MEMBER}}</span> <span class="horiz_field_sep associated_details">({TIME*})</span> {STAFF_ACTIONS}</div>
	{+END}

	{+START,IF,{SYSTEM_MESSAGE}}
		<blockquote style="{+START,IF_NON_EMPTY,{FONT_COLOUR}}color: {FONT_COLOUR'}; {+END}{+START,IF_NON_EMPTY,{FONT_FACE}}font-family: {FONT_FACE|}{+END}"><strong>{MESSAGE}</strong></blockquote>

		<div><span class="chat_message_by{+START,IF,{STAFF}} chat_operator_staff{+END}">{!BY_SIMPLE,{!SYSTEM}}</span> <span class="horiz_field_sep associated_details">({TIME*})</span></div>
	{+END}
</div>
