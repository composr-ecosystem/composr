<div class="{$,{RET},global-middle,}">
	{TITLE}

	{+START,IF_NON_EMPTY,{RETURN_URL}}
		<p class="back-button">
			<a href="{RETURN_URL*}" title="{MSG}">{+START,INCLUDE,ICON}
				NAME=admin/back
				ICON_SIZE=48
			{+END}</a>
		</p>
	{+END}

	{+START,IF_NON_EMPTY,{MESSY_URL}}
		<ul class="actions-list">
			<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} {!WEBSTANDARDS_MESSAGE,{MESSY_URL*}}</li>
		</ul>
	{+END}

	<div class="webstandards-div">
		<table class="map-table autosized-table webstandards-table">
			<tbody>
