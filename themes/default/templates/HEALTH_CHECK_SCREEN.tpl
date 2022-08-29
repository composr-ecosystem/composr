{TITLE}

<form action="{$SELF_URL*}" method="post" title="{!PRIMARY_PAGE_FORM}">
	{$INSERT_FORM_POST_SECURITY}

	<input type="hidden" name="submitting" value="1" />

	<div>
		<label for="sections_to_run" class="lonely-label">{!SECTIONS}:</label>
		<select name="sections_to_run[]" id="sections_to_run" multiple="multiple" size="30" class="form-control form-control-wide" data-submit-on-enter="1">
			{SECTIONS}
		</select>
	</div>

	<div class="clearfix force-margin">
		<div class="left float-separation">
			<label for="fails">{!SHOW_FAILS}:</label>
			<input type="checkbox" name="fails" id="fails" value="1" checked="checked" disabled="disabled" />
		</div>

		<div class="left float-separation">
			<label for="passes">{!SHOW_PASSES}:</label>
			<input type="checkbox" name="passes" id="passes" value="1"{+START,IF,{PASSES}} checked="checked"{+END} />
		</div>

		<div class="left float-separation">
			<label for="skips">{!SHOW_SKIPS}:</label>
			<input type="checkbox" name="skips" id="skips" value="1"{+START,IF,{SKIPS}} checked="checked"{+END} />
		</div>

		<div class="left float-separation">
			<label for="manual_checks">{!SHOW_MANUAL_CHECKS}:</label>
			<input type="checkbox" name="manual_checks" id="manual_checks" value="1"{+START,IF,{MANUAL_CHECKS}} checked="checked"{+END} />
		</div>
	</div>

	<p class="proceed-button">
		<button class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!HEALTH_CHECK}</span></button>
	</p>
</form>

{+START,IF_PASSED,RESULTS}
	{RESULTS}
{+END}
