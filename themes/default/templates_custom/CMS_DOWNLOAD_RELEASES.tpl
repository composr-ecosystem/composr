{+START,IF_PASSED,QUICK_VERSION}{+START,IF_PASSED,QUICK_FILESIZE}{+START,IF_PASSED,QUICK_URL}
	{+START,BOX}
		<h3>Automatic Extractor (Recommended)</h3>

		<p>This package ("quick installer") will self-extract on your server and automatically set all permissions.</p>

		<p>Works on most servers (needs PHP FTP support or something similar to suEXEC on your server).</p>

		<div>
			<p class="right">v{QUICK_VERSION*} | {QUICK_FILESIZE*}</p>
			<p><a class="btn btn-primary btn-scri buttons--more" href="{QUICK_URL*}">Download</a></p>
		</div>
	{+END}
{+END}{+END}{+END}

<hr>

{+START,IF_PASSED,MANUAL_VERSION}{+START,IF_PASSED,MANUAL_FILESIZE}{+START,IF_PASSED,MANUAL_URL}
	{+START,BOX}
		<h3>Manual Extractor (Slower; requires chmodding)</h3>

		<p>This is a ZIP containing all Composr files (several thousand). It is much slower, and only recommended if you cannot use the quick installer. Some <a target="_blank" title="File permissions tutorial ({!LINK_NEW_WINDOW})" href="{$TUTORIAL_URL*,tut_install_permissions}">chmodding</a> is required.</p>

		<div>
			<p class="right">v{MANUAL_VERSION*} | {MANUAL_FILESIZE*}</p>
			<p><a class="btn btn-secondary btn-scri buttons--more" href="{MANUAL_URL*}">Download</a></p>
		</div>
	{+END}
{+END}{+END}{+END}

<hr>

{+START,IF_PASSED,BLEEDINGMANUAL_VERSION}{+START,IF_PASSED,BLEEDINGMANUAL_FILESIZE}{+START,IF_PASSED,BLEEDINGMANUAL_URL}
{+START,IF_PASSED,BLEEDINGQUICK_VERSION}{+START,IF_PASSED,BLEEDINGQUICK_FILESIZE}{+START,IF_PASSED,BLEEDINGQUICK_URL}
	{+START,BOX}
		<h3>Bleeding Edge (Unstable)</h3>

		<p>Are you able to {$?,{$IN_STR,{BLEEDINGQUICK_VERSION},alpha},alpha,beta}-test the new version: v{BLEEDINGQUICK_VERSION*}?<br />
		It {$?,{$IN_STR,{BLEEDINGQUICK_VERSION},alpha},<strong>will not be stable</strong> like,<strong>may not be as stable</strong> as} our main version{+START,IF_PASSED,QUICK_VERSION} (v{QUICK_VERSION*}){+END}.</p>

		<div>
			<p class="right"><a class="btn btn-secondary btn-scri buttons--more" href="{BLEEDINGMANUAL_URL*}">Manual Installer</a></p>
			<p><a class="btn btn-primary btn-scri buttons--more" href="{BLEEDINGQUICK_URL*}">Quick Installer</a></p>
		</div>
	{+END}
{+END}{+END}{+END}
{+END}{+END}{+END}
