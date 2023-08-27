<?php exit; ?>
/*******************************************************************************

	@File:			user_manager.php
	@Plugin:		Multi User
	@Description:	Adds Multi-User Management Section
	@Subject:		Main plugin file
	@Revision:		20 Feb 2015
	@Version:		1.9.0
	@Author:		Mike Henken (http://michaelhenken.com/)
	@History:
	----------------------------------------------------------------------------
	Version 1.9.1 (June 2023) :: by sallecta (github.com/sallecta)
	----------------------------------------------------------------------------
	----------------------------------------------------------------------------
	Version 1.9.0 (February 2015) :: smartened by maf (www.jinan.cz)
	----------------------------------------------------------------------------
	+ reworked UX to better fit GS 3.3 admin interface look&feel
	+ complete localization with EN, RU and CS language files bundled
	+ returned "User's bio" in User's profile, fixed non-latin chars crashes
	+ CKeditor on demand on User Management page
	+ added switch to reveal password text
	+ added client-side username and empty password validation
	+ added client-side automatic landing page restriction/setting by permissions
	+ logged-in user cannot delete himself or remove his User Management rights
	+ default permissions for standard non-admin user when adding new users
	+ added several help hints
	+ huge increase of performance by removing multiple readings of CKeditor code
	- some of the above features do not work in IE less than 10 but plugin is still usable

	----------------------------------------------------------------------------
	Version 1.8.2+ "updated" (May 2014) :: cured by Oleg06 (www.getsimplecms.ru)
	----------------------------------------------------------------------------
	+ repaired to work with GS 3.3
	+ added server-side checks for incorrect landing page settings
	+ user with denied "Settings" rights can still edit his profile
	+ added "admin" flag in users table
	- removed User's bio from user's profile (it wrecked user on nonlatin chars)
	- incomlete localization with many Cyrillic strings

	----------------------------------------------------------------------------
	Version 1.8.2 (September 2012) :: last version by Mike Henken
	----------------------------------------------------------------------------
	+ DISCLAIMER - When I initially created this plugin I had very little
		knowledge of php and was a programming noob. I know it is in need
		of a rewrite and I will get around to it at some point
	- compromised functionality with GS 3.3

*******************************************************************************/
