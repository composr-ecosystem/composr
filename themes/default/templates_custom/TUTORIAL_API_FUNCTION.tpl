{TITLE}

<h2>{!tutorials:API_DOC_DEFINITIONS}</h2>

{+START,LOOP,FUNCTION_DEFINITIONS}
	<h3>{PATH*}</h3>

	<ul>
		{+START,IF_NON_EMPTY,{DESCRIPTION}}
			<li>{DESCRIPTION*}</li>
		{+END}
		{+START,IF_NON_EMPTY,{VISIBILITY}}
			<li><strong>{!tutorials:API_DOC_VISIBILITY}</strong>: {VISIBILITY*}</li>
		{+END}
		{+START,IF_NON_EMPTY,{IS_ABSTRACT}}
			<li><strong>{!tutorials:API_DOC_IS_ABSTRACT}</strong>: {IS_ABSTRACT*}</li>
		{+END}
		{+START,IF_NON_EMPTY,{IS_STATIC}}
			<li><strong>{!tutorials:API_DOC_IS_STATIC}</strong>: {IS_STATIC*}</li>
		{+END}
		{+START,IF_NON_EMPTY,{IS_FINAL}}
			<li><strong>{!tutorials:API_DOC_IS_FINAL}</strong>: {IS_FINAL*}</li>
		{+END}
		{+START,IF_NON_EMPTY,{RETURN_TYPE}}
			<li><strong>{!tutorials:API_DOC_RETURN}</strong>: {RETURN_TYPE}</li>
		{+END}
		{+START,IF_NON_EMPTY,{FLAGS}}
			<li>
				<strong>{!tutorials:API_DOC_FLAGS}</strong>:
				<ul>
					{+START,LOOP,FLAGS}
						<li>{_key_var*}</li>
					{+END}
				</ul>
			</li>
		{+END}
	</ul>

	{+START,IF_NON_EMPTY,{PARAMETERS}}
		<h4>{!tutorials:API_DOC_PARAMETERS}</h4>

		{PARAMETERS}
	{+END}

	{+START,IF_NON_EMPTY,{RETURN_TYPE_CMS}{RETURN_SET}{RETURN_RANGE}{RETURN_DESCRIPTION}}
		<h4>{!tutorials:API_DOC_RETURN}</h4>

		<ul>
			{+START,IF_NON_EMPTY,{RETURN_DESCRIPTION}}
				<li>{RETURN_DESCRIPTION*}</li>
			{+END}
			{+START,IF_NON_EMPTY,{RETURN_TYPE_CMS}}
				<li><strong>{!TYPE}</strong>: {RETURN_TYPE_CMS}</li>
			{+END}
			{+START,IF_NON_EMPTY,{RETURN_SET}}
				<li><strong>{!SET}</strong>: {RETURN_SET}</li>
			{+END}
			{+START,IF_NON_EMPTY,{RETURN_RANGE}}
				<li><strong>{!tutorials:API_DOC_RANGE}</strong>: {RETURN_RANGE}</li>
			{+END}
		</ul>
	{+END}
{+END}