<div data-view="AddonInstallConfirmScreen">
{TITLE}

{+START,IF_PASSED,PING_URL}
	{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}

{WARNINGS}

<form title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post" data-submit-modsecurity-workaround="1">
	{$INSERT_FORM_POST_SECURITY}

	<div class="box box---addon-install-confirm-screen"><div class="box-inner">
		<h2>{!ADDON_FILES}</h2>

		<div class="not-too-tall">
			<ul class="tick-list">
				{FILES}
			</ul>
		</div>
	</div></div>

	<p class="proceed-button">
		<button class="btn btn-primary btn-scr buttons--back" data-cms-btn-go-back="1" type="button">{+START,INCLUDE,ICON}NAME=buttons/back{+END} <span>{!GO_BACK}</span></button>
		<button data-disable-on-click="1" class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} {!PROCEED}</button>
	</p>

	<h2>{!DETAILS}</h2>

	<table class="map-table results-table wide-table autosized-table responsive-table">
		<tbody>
			<tr>
				<th>{!NAME}</th>
				<td>{NAME*}</td>
			</tr>
			<tr>
				<th>{!AUTHOR}</th>
				<td>{AUTHOR*}</td>
			</tr>
			<tr>
				<th>{!ORGANISATION}</th>
				<td>{ORGANISATION*}</td>
			</tr>
			{+START,IF_NON_EMPTY,{COPYRIGHT_ATTRIBUTION}}
				<tr>
					<th>{!COPYRIGHT_ATTRIBUTION}</th>
					<td><div class="whitespace-visible">{COPYRIGHT_ATTRIBUTION*}</div></td>
				</tr>
			{+END}
			<tr>
				<th>{!LICENCE}</th>
				<td>{LICENCE*}</td>
			</tr>
			<tr>
				<th>{!VERSION}</th>
				<td>{VERSION*}</td>
			</tr>
			<tr>
				<th>{!DESCRIPTION}</th>
				<td>{DESCRIPTION}</td>
			</tr>
			<tr>
				<th>{!CATEGORY}</th>
				<td>{CATEGORY*}</td>
			</tr>
		</tbody>
	</table>

	<input type="hidden" name="file" value="{FILE*}" />
</form>
</div>
