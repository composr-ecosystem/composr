<div {+START,IF,{$NOT,{$_GET,keep_show_timings}}} data-tpl="installerStepLog"{+END}>
	<p>
		{!INSTALL_LOG_BELOW,{CURRENT_STEP*}}:
	</p>

	<div class="actions-list installer-main-min">
		<div class="install-log-table">
			<p class="lonely-label">{!INSTALL_LOG}:</p>
			<ul>
				{LOG}
			</ul>
		</div>
	</div>

	<form title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post">
		<div>
			{HIDDEN}

			<p class="proceed-button">
				<button id="proceed-button" class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span class="js-button-label">{!PROCEED}</span></button>
			</p>
		</div>
	</form>
</div>
