{TITLE}

<h3>{!GENERATING_SPRITE,{SPRITE_PATH_MONOCHROME*}}</h3>

<ul>
	{+START,LOOP,ICONS_ADDED_MONOCHROME}
		<li>{!ADDED_SIMPLE,<kbd>{_loop_var*}</kbd>}</li>
	{+END}
</ul>

<h3>{!GENERATING_SPRITE,{SPRITE_PATH_COLOUR*}}</h3>

<ul>
	{+START,LOOP,ICONS_ADDED_COLOUR}
		<li>{!ADDED_SIMPLE,<kbd>{_loop_var*}</kbd>}</li>
	{+END}
</ul>
