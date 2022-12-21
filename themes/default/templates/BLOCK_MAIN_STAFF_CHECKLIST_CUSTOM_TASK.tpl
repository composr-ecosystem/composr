{$SET,confirm_delete_message,{!CONFIRM_DELETE,{$STRIP_TAGS,{TASK_TITLE}}}}
<div data-view="BlockMainStaffChecklistCustomTask" data-view-params="{+START,PARAMS_JSON,ID,confirm_delete_message}{_*}{+END}" data-vw-task-done="{TASK_DONE*}" class="checklist-row">
	<div class="clearfix">
		<p class="checklist-task-status">
			<span>{!ADDED_SIMPLE,<strong>{ADD_TIME*}</strong>}{+START,IF_NON_EMPTY,{RECUR_INTERVAL}}, {!RECUR_EVERY,{RECUR_INTERVAL*},{RECUR_EVERY*}}{+END}</span>

			<a class="js-click-confirm-delete" href="#!" title="{!DELETE}">{+START,INCLUDE,ICON}
				NAME=admin/delete2
				ICON_SIZE=12
				ICON_CLASS=checklist-delete
			{+END}</a>
		</p>
		<div class="checklist-task js-click-mark-task js-keypress-mark-task">
			{+START,INCLUDE,ICON}
				NAME=checklist/{TASK_DONE}
				ICON_SIZE=12
				ICON_CLASS=js-icon-checklist-status
				ICON_TITLE={!MARK_TASK_DONE}
			{+END}
			<span>{TASK_TITLE}</span>
		</div>
	</div>
</div>
