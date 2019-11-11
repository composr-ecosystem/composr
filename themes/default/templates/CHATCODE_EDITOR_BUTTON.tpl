{$REQUIRE_JAVASCRIPT,chat}
{+START,IF,{$NEQ,{B},invite,new_room}}<input type="image" data-click-do-input="['{B;}', 'post']" data-click-pd="1" title="{TITLE}" alt="{TITLE}" height="34" src="{$IMG*,chatcode_editor/{B}}" />{+END}
