{+START,IF_PASSED,QUICK_VERSION}{+START,IF_PASSED,QUICK_FILESIZE}{+START,IF_PASSED,QUICK_URL}
	<div class="dlHolder">
		<div class="dlHead grn">
			Automatic extractor <span>Recommended</span>
		</div>

		<div class="dlBody">
			<p>This package ("quick installer") will self-extract on your server and automatically set all permissions.</p>

			<p>Works on most servers (needs PHP FTP support or suEXEC on your server).</p>

			<div class="sept"></div>

			<p><a class="alLft niceLink" href="{QUICK_URL*}">Download &dtrif;</a> <a class="alRht" href="{MANUAL_URL*}">v{QUICK_VERSION*} | {QUICK_FILESIZE*}</a></p>
		</div>
	</div>
{+END}{+END}{+END}

{+START,IF_PASSED,MANUAL_VERSION}{+START,IF_PASSED,MANUAL_FILESIZE}{+START,IF_PASSED,MANUAL_URL}
	<div class="dlHolder">
		<div class="dlHead blu">
			Manual extractor <span>Slower; requires chmodding</span>
		</div>

		<div class="dlBody">
			<p>This is a ZIP containing all Composr files (several thousand). It is much slower, and only recommended if you cannot use the quick installer. Some <a target="_blank" title="Advanced installation tutorial ({!LINK_NEW_WINDOW})" href="{$TUTORIAL_URL*,tut_adv_install}">chmodding</a> is required.</p>

			<p><strong>Do not use this for upgrading.</strong></p>

			<div class="sept"></div>

			<p><a class="alLft niceLink" href="{MANUAL_URL*}">Download &dtrif;</a> <a class="alRht" href="{MANUAL_URL*}">v{MANUAL_VERSION*} | {MANUAL_FILESIZE*}</a></p>
		</div>
	</div>
{+END}{+END}{+END}

{+START,IF_PASSED,BLEEDINGMANUAL_VERSION}{+START,IF_PASSED,BLEEDINGMANUAL_FILESIZE}{+START,IF_PASSED,BLEEDINGMANUAL_URL}
{+START,IF_PASSED,BLEEDINGQUICK_VERSION}{+START,IF_PASSED,BLEEDINGQUICK_FILESIZE}{+START,IF_PASSED,BLEEDINGQUICK_URL}
	<div class="dlHolder">
		<div class="dlHead">
			Bleeding edge <span>Unstable</span>
		</div>

		<div class="dlBody">
			<p>Would you like to {$?,{$IN_STR,{BLEEDINGQUICK_VERSION},alpha},alpha,beta}-test the new version: v{BLEEDINGQUICK_VERSION*}?<br />
			It {$?,{$IN_STR,{BLEEDINGQUICK_VERSION},alpha},<strong>will be significantly less stable</strong> than,<strong>may not be as stable</strong> as} our main version{+START,IF_PASSED,QUICK_VERSION} (v{QUICK_VERSION*}){+END}.</p>
			{$?,{$IN_STR,{BLEEDINGQUICK_VERSION},alpha},<p>Warning: if you run an alpha version in production (on a live site) or attempt to upgrade from a previous version then you do so at your own risk. Otherwise we recommend waiting until a beta or stable version is released.</p>,}

			<!-- LEGACY -->
			{+START,IF,{$EQ,{BLEEDINGQUICK_VERSION},10.1 beta8,10.1 beta9,10.1 beta10,10.1 beta11,10.1 beta12,10.1 beta13,10.1 beta14,10.1 beta15,10.1 beta16,10.1 beta17,10.1 beta18,10.1 beta19,10.1 beta20,10.1 beta21,10.1 beta22,10.1 beta23}}
				<p>Warning: We decided to discontinue the v10.1 branch before it was release-ready, the functionality here will now be delivered in an upcoming longer-term branch, v11. Use v10.1 only for early testing of selected new functionality.</p>
			{+END}

			<div class="sept"></div>

			<p><a class="alLft niceLink" href="{BLEEDINGQUICK_URL*}">Download automatic extractor &dtrif;</a> <a class="alRht niceLink" href="{BLEEDINGMANUAL_URL*}">Download manual extractor &dtrif;</a></p>
		</div>
	</div>
{+END}{+END}{+END}
{+END}{+END}{+END}
