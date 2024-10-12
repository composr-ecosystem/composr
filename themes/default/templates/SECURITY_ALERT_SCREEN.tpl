{TITLE}

<h2>{!REASON}</h2>
<p>
	{REASON*}
</p>

<h2>{!DETAILS}</h2>

<table class="map-table wide-table results-table">
	<colgroup>
		<col class="field-name-column" />
		<col class="field-value-column" />
	</colgroup>

	<tbody>
		<tr>
			<th>{!USERNAME}</th>
			<td>{USERNAME}</td>
		</tr>
		<tr>
			<th>{!IP_ADDRESS}</th>
			<td>{IP}</td>
		</tr>
		<tr>
			<th>{!URL}</th>
			<td>
				{URL*} {$*,Do not make this a clickable URL or you risk creating an attack vector}
			</td>
		</tr>
		<tr>
			<th>{!REFERER}</th>
			<td>
				{+START,IF_NON_EMPTY,{REFERER}}
					{REFERER*} {$*,Do not make this a clickable URL or you risk creating an attack vector}
				{+END}
				{+START,IF_EMPTY,{REFERER}}
					{!NONE_EM}
				{+END}
			</td>
		</tr>
		<tr>
			<th>{!USER_AGENT}</th>
			<td><kbd>{USER_AGENT*}</kbd></td>
		</tr>
		<tr>
			<th>{!USER_OS}</th>
			<td><kbd>{USER_OS*}</kbd></td>
		</tr>
		<tr>
			<th>{!RISK}</th>
			<td>{RISK_SCORE*}</td>
		</tr>
	</tbody>
</table>

{+START,IF_NON_EMPTY,{POST}}
	<h2>{!POST_DATA}</h2>

	<p>{!POST_DATA_EXPLANATION}</p>

	<div class="box box---security-alert-screen"><div class="box-inner">
		{POST}
	</div></div>
{+END}
