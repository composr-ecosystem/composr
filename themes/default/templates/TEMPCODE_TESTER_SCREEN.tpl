{$REQUIRE_JAVASCRIPT,core_themeing}

<div data-tpl="tempcodeTesterScreen">
	{TITLE}

	<p>{!TEMPCODE_TESTER_HELP}</p>

	<form title="{!PRIMARY_PAGE_FORM}" method="post" action="#!">
		{$INSERT_SPAMMER_BLACKHOLE}

		<h2><label for="tempcode">Tempcode</label></h2>
		<div>
			<textarea id="tempcode" name="tempcode" cols="70" rows="17" class="form-control form-control-wide textarea-scroll"></textarea>
		</div>

		<h2>{!PARAMETERS}</h2>

		{+START,LOOP,1\,2\,3\,4\,5\,6\,7\,8\,9\,10}
			<p class="vertical-alignment">
				<label class="accessibility-hidden" for="key_{_loop_var*}">{!TEMPCODE_PARAMETER} #{_loop_var*}</label>
				<label class="accessibility-hidden" for="val_{_loop_var*}">{!TEMPCODE_VALUE} #{_loop_var*}</label>
				<input type="text" id="key_{_loop_var*}" class="form-control" name="key_{_loop_var*}" size="10" />
				&rarr;
				<input type="text" id="val_{_loop_var*}" class="form-control" name="val_{_loop_var*}" size="60" />
			</p>
		{+END}

		<p class="proceed-button">
			<button accesskey="p" class="btn btn-primary btn-scr tabs--preview js-click-btn-tempcode-tester-do-preview" type="submit">{!PREVIEW}</button>
		</p>
	</form>

	<h2>{!PREVIEW}</h2>

	<div class="comcode-code-wrap">
		<div class="comcode-code">
			<h4>{!RAW_PREVIEW}</h4>

			<div class="webstandards-checker-off"><div class="comcode-code-inner" id="preview-raw">
				<p class="nothing-here">{!UNSET}</p>
			</div></div>
		</div>
	</div>

	<div class="comcode-code-wrap">
		<div class="comcode-code">
			<h4>{!HTML_PREVIEW}</h4>

			<div class="webstandards-checker-off"><div class="comcode-code-inner" id="preview-html">
				<p class="nothing-here">{!UNSET}</p>
			</div></div>
		</div>
	</div>

	<div class="comcode-code-wrap">
		<div class="comcode-code">
			<h4>{!COMCODE_PREVIEW}</h4>

			<div class="webstandards-checker-off"><div class="comcode-code-inner" id="preview-comcode">
				<p class="nothing-here">{!UNSET}</p>
			</div></div>
		</div>
	</div>
</div>
