<section class="box box---block-side-language"><div class="box-inner">
	<h3>{!LANGUAGE}</h3>

	<form title="{!LANGUAGE} ({!FORM_AUTO_SUBMITS})" method="get" action="{$URL_FOR_GET_FORM*,{$SELF_URL,1}}">
		{$HIDDENS_FOR_GET_FORM,{$SELF_URL,1},keep_lang}
		<div>
			<p class="accessibility-hidden"><label for="keep_lang">{!LANGUAGE}</label></p>
			<select id="keep_lang" name="keep_lang" class="form-control form-control-wide" data-change-submit-form="">
				{LANGS}
			</select>
		</div>
	</form>
</div></section>
