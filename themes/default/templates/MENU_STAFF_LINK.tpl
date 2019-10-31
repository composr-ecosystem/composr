{+START,IF,{$THEME_OPTION,enable_menu_editor_buttons}}
	<a class="edit-menu-link-inline" href="{EDIT_URL*}" title="{!EDIT_MENU}{+START,IF_NON_EMPTY,{NAME}}: {NAME*}{+END}">{+START,INCLUDE,ICON}NAME=menus/menu{+END}</a>
{+END}
