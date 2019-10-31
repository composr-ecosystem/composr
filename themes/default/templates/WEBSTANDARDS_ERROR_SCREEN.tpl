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

	<h2>{!WEBSTANDARDS_ERROR}</h2>

	<ul>
		{ERRORS}
	</ul>

	{+START,IF_NON_EMPTY,{MESSY_URL}}
		<h2>{!ACTIONS}</h2>

		<ul>
			<li>{!WEBSTANDARDS_IGNORE,{IGNORE_URL*}}</li>
			<li>{!WEBSTANDARDS_IGNORE_2,{IGNORE_URL_2*}}</li>
			<li>{!WEBSTANDARDS_MESSAGE,{MESSY_URL*}}</li>
		</ul>
	{+END}

	<h2>{!CODE}</h2>

	<div class="webstandards-div">
		<table class="map-table autosized-table webstandards-table">
			<tbody>
