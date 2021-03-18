{+START,INCLUDE,QUIZ_BOX}
	INSERT_BEFORE: </div></section> ~~> {+START,IF,{$EQ,{_TYPE},TEST}}{+START,IF,{$NEQ,{POINTS},0}}{+START,IF,{$ADDON_INSTALLED,points}}<p>You will win <strong>{$INTEGER_FORMAT*,{POINTS},0}</strong> points if you pass this test. You will spend <strong>{$INTEGER_FORMAT*,{$DIV,{POINTS},2},0}</strong> points to enter this test.<br />Put your points on the line and your knowledge to the test!</p>{+END}{+END}{+END}
{+END}
