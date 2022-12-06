<hr class="spaced-rule" />

<div class="clearfix">
	<div class="left">
		<label for="filedump-action-{I*}">{!ACTION}:</label>
		<select id="filedump-action-{I*}" name="filedump_action" class="form-control filedump-actions">
			{+START,IF,{$EQ,{I},1}}
				<option value="">---</option>
			{+END}
			{+START,IF,{$EQ,{I},2}}
				<option value="edit">{!EDIT_DESCRIPTIONS}</option>
			{+END}
			<option value="delete">{!DELETE_SELECTED}</option>
			{+START,LOOP,OTHER_DIRECTORIES}
				<option value="/{_loop_var*}{+START,IF_NON_EMPTY,{_loop_var}}/{+END}">{!MOVE_TO,/{_loop_var*}}</option>
			{+END}
			<option value="zip">{!FILEDUMP_ZIP}</option>
		</select>

		<button type="submit" class="btn btn-primary btn-sm buttons--proceed js-check-filedump-selections">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} {!PROCEED}</button>
	</div>
</div>
