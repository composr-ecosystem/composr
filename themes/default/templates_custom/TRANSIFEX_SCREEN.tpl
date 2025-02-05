{TITLE}

{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}

<p>
	Use this page to download translations direct from the translation platform (Transifex).<br />
	Periodically we package translations into formal language addons.<br />
</p>

<p>
	Download is a little slow due to the speed of the Transifex API. Stats are cached for 10 minutes.
</p>

<table class="columned-table wide-table results-table autosized-table responsive-table">
	<thead>
		<tr>
			<th>Language</th>
			<th>Translators</th>
			<th>Percentage&nbsp;&dagger;</th>
			<th>Links</th>
		</tr>
	</thead>
	<tbody>
		{+START,LOOP,LANGUAGES}
			<tr>
				<td>
					<abbr title="{LANGUAGE_CODE*}">{LANGUAGE_NAME*}</abbr>
				</td>
				<td>
					{TRANSLATORS*}
				</td>
				<td>
					{PERCENTAGE*}
				</td>
				<td>
					{+START,IF,{$NOT,{$IS_GUEST}}}
						<ul class="horizontal-links">
							<li><a rel="nofollow" href="{DOWNLOAD_CORE_URL*}">Download</a></li>
							<li><a rel="nofollow" href="{DOWNLOAD_URL*}">Download&nbsp;with&nbsp;non-bundled&nbsp;addon&nbsp;translations</a></li>
						</ul>
					{+END}
					{+START,IF,{$IS_GUEST}}
						<em>Log in to download</em>
					{+END}
				</td>
			</tr>
		{+END}
	</tbody>
</table>

<p>
	&dagger; 29% typically means translated fully apart from administrative strings
</p>
