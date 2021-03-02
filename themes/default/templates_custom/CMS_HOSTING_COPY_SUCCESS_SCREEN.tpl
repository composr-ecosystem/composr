{TITLE}

<p>
	{!HOSTING_COPY_SUCCESS}
</p>

<form action="{INSTALL_URL*}" method="post">
	{$INSERT_FORM_POST_SECURITY}

	{HIDDEN}
	<input type="hidden" name="ftp_folder" value="{FTP_FOLDER*}" />

	<div class="proceed-button">
		<button class="btn btn-primary btn-scr buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} {!PROCEED}</button>
	</div>
</form>
