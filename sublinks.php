<?php
	/****************************************************************/
    /* ATutor Calendar Module                                       */
    /* https://atutorcalendar.wordpress.com/                        */
    /*                                                              */
    /* This module provides standard calendar features in ATutor.   */
    /*                                                              */
    /* Author: Anurup Raveendran, Herat Gandhi                      */
    /* This program is free software. You can redistribute it and/or*/
    /* modify it under the terms of the GNU General Public License  */
    /* as published by the Free Software Foundation.                */
    /****************************************************************/

	/**
	 * This file fills the box created for course on homepage of course.
	 */
	if (!defined('AT_INCLUDE_PATH')) { exit; }
	
	/*****
	* Free form PHP can appear here to retreive current information
	* from the module, or a text description of the module where there is
	* not current information
	*****/
	
	$list[0] = _AT('calendar_mod_def');
	return $list;
?>
