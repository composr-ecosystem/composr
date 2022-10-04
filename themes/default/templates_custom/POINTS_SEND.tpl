{$REQUIRE_JAVASCRIPT,idolisr}

{+START,INCLUDE,POINTS_SEND}
	INSERT_AFTER: <!--LAST-FIELD--> ~~> <span class="send-fragment" id="points-role-span" style="display: none;"><label for="send-reason-pre">performing in the role of</label> <select id="send-reason-pre" class="form-control" name="give_reason_pre"><option value="">(Please select)</option>{+START,LOOP,{$CONFIG_OPTION,idolisr_roles}}<option value="{_loop_var}">{_loop_var}</option>{+END}</select></span>
{+END}
