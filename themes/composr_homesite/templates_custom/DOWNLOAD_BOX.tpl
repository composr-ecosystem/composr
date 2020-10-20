{+START,IF,{$EQ,{_GUID},top_downloads}}
	{+START-,INCLUDE,DOWNLOAD_BOX_top_downloads}{+END}
{+END}

{+START,IF,{$EQ,{_GUID},module}}
	{+START-,INCLUDE,DOWNLOAD_BOX_module}{+END}
{+END}

{+START,IF,{$NEQ,{_GUID},top_downloads,module}}
	{+START-,INCLUDE,DOWNLOAD_BOX_other}{+END}
{+END}
