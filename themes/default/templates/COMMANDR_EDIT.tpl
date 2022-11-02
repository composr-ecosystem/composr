{$SET,rndx,{$RAND}}
{$REQUIRE_JAVASCRIPT,core_form_interfaces}
{$REQUIRE_JAVASCRIPT,commandr}

<div data-tpl="commandrEdit" data-tpl-params="{+START,PARAMS_JSON,FILE,rndx}{_*}{+END}">
	<form title="{!EDIT}" action="{SUBMIT_URL*}" method="post">
		{$INSERT_FORM_POST_SECURITY}

		<div>
			<p class="lonely-label"><label for="edit_content{$GET%,rndx}">{!EDIT}:</label></p>
			<div><textarea class="form-control form-control-wide textarea-scroll" cols="60" rows="10" id="edit_content{$GET%,rndx}" name="edit_content">{FILE_CONTENTS*}</textarea></div>

			<p>
				<button class="btn btn-primary btn-scri buttons--proceed js-commandr-edit" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED}</span></button>
			</p>
		</div>
	</form>
</div>
