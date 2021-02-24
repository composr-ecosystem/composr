{+START,INCLUDE,QUIZ_DONE_SCREEN}{+END}

{+START,IF,{$ADDON_INSTALLED,points}}
	{+START,IF,{$GT,{POINTS_DIFFERENCE},0}}
		<hr />

		<p>You have gained <strong>{$NUMBER_FORMAT*,{POINTS_DIFFERENCE}}</strong> points. Congrats!</p>
	{+END}
	{+START,IF,{$LT,{POINTS_DIFFERENCE},0}}
		<hr />

		<p>Oh dear, you have lost <strong>{$NUMBER_FORMAT*,{$MOD,{POINTS_DIFFERENCE}}}</strong> points.</p>
	{+END}
{+END}
