{TITLE}

{+START,IF_NON_EMPTY,{CLASS_DEFINITIONS}}
	<h2>{!tutorials:API_DOC_DEFINITIONS}</h2>

	<ul>
		{+START,LOOP,CLASS_DEFINITIONS}
			<li>
				<strong>{PATH*}</strong>
				<ul>
					<li><strong>{!TYPE}</strong>: {TYPE*}</li>
					<li><strong>{!tutorials:API_DOC_PACKAGE}</strong>: {PACKAGE*}</li>
					<li><strong>{!tutorials:API_DOC_IS_ABSTRACT}</strong>: {IS_ABSTRACT*}</li>
					{+START,IF_NON_EMPTY,{IMPLEMENTS}}
						<li>
							<strong>{!tutorials:API_DOC_IMPLEMENTS}</strong>:
							<ul>
								{+START,LOOP,IMPLEMENTS}
									<li>{_key_var}</li>
								{+END}
							</ul>
						</li>
					{+END}
					{+START,IF_NON_EMPTY,{EXTENDS}}
						<li><strong>{!tutorials:API_DOC_EXTENDS}</strong>: {EXTENDS}</li>
					{+END}
					{+START,IF_NON_EMPTY,{TRAITS}}
						<li>
							<strong>{!tutorials:API_DOC_TRAITS}</strong>:
							<ul>
								{+START,LOOP,TRAITS}
									<li>{_key_var*}</li>
								{+END}
							</ul>
						</li>
					{+END}
				</ul>
			</li>
		{+END}
	</ul>
{+END}

<h2>{!tutorials:API_DOC_CLASS_FUNCTIONS}</h2>

{+START,IF_PASSED,FILTERCODE_BOX}
	{+START,INCLUDE,FILTER_BOX}{+END}
{+END}

{CLASS_FUNCTIONS}
