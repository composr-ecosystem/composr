<div class="installer-main-min">
	<p>
		{!INSTALL_LOG_BELOW,{CURRENT_STEP*}}:
	</p>

	<div><div class="install-log-table">
		<p class="lonely-label">{!INSTALL_LOG}:</p>
		<nav>
			<ul class="actions-list">
				{LOG}
			</ul>
		</nav>
	</div></div>

	<p>
		{FINAL}
	</p>

	<p>
		{!FINAL_INSTRUCTIONS_B}
	</p>

	<form id="form-installer-step-10" title="{!PRIMARY_PAGE_FORM}" action="{URL*}" method="post">
		<nav class="installer-completed-calltoaction">
			<div>
				<label for="next_setupwizard" class="vertical-alignment">
					<input type="radio" id="next_setupwizard" name="next" value="setupwizard" checked="checked" />
					<span>{!CONFIGURE} ({!RECOMMENDED})</span>
				</label>
			</div>

			<div>
				<label for="next_testcontent" class="vertical-alignment">
					<input type="radio" id="next_testcontent" name="next" value="testcontent" />
					<span>{!LAUNCH_WITH_TEST_CONTENT}</span>
				</label>
			</div>

			<div>
				<label for="next_blank" class="vertical-alignment">
					<input type="radio" id="next_blank" name="next" value="blank" />
					<span>{!LAUNCH_IN_BLANK_STATE}</span>
				</label>
			</div>
		</nav>

		<p class="proceed-button">
			<button class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <span>{!GO}</span></button>
		</p>

		{HIDDEN}
	</form>

	<p class="installer-done-thanks">
		<em>{!THANKS}</em>
	</p>
</div>
