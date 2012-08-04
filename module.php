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
    
    /*******
     * doesn't allow this file to be loaded with a browser.
     */
    if (!defined('AT_INCLUDE_PATH')) { exit; }

    /******
     * this file must only be included within a Module obj
     */
    if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { 
        exit(__FILE__ . ' is not a Module'); 
    }
    /******
    * modules sub-content to display on course home detailed view
    */
    $this->_list['calendar'] = array('title_var'=>'calendar_header','file'=>'mods/calendar/sublinks.php');

    /*******
     * assign the instructor and admin privileges to the constants.
     */
    define('AT_PRIV_CALENDAR',       $this->getPrivilege());
    define('AT_ADMIN_PRIV_CALENDAR', $this->getAdminPrivilege());
    global $_custom_head;
    $_custom_head .='
    <script language="javascript" type="text/javascript" src="'.AT_BASE_HREF.
    'jscripts/infusion/InfusionAll.js"></script>
    <script language="javascript" type="text/javascript" src="'.AT_BASE_HREF.
    'jscripts/lib/calendar.js"></script>
    <link href="'.AT_BASE_HREF.'mods/calendar/lib/jquery/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css"/>
    <link href="'.AT_BASE_HREF.'jscripts/infusion/lib/jquery/plugins/tooltip/css/jquery.tooltip.css" rel="stylesheet" type="text/css"/>';

    /*******
     * create a side menu box/stack.
     */
    if( !stristr( $_SERVER["REQUEST_URI"], "calendar") )
    $this->_stacks['calendar_header'] = array('title_var' => 'calendar_header', 'file' => AT_INCLUDE_PATH.'../mods/calendar/side_menu.inc.php');
    // ** possible alternative: **
    // $this->addStack('calendar', array('title_var' => 'calendar', 'file' => './side_menu.inc.php');

    /*******
     * if this module is to be made available to students on the Home or Main Navigation.
     */
    $_student_tool = 'mods/calendar/index.php';
    // ** possible alternative: **
    // $this->addTool('./index.php');


    /*******
     * instructor Manage section:
     
    $this->_pages['mods/calendar/index_instructor.php']['title_var'] = 'ATutor Calendar';
    $this->_pages['mods/calendar/index_instructor.php']['parent']   = 'tools/index.php';
    */


    /*******
     * student page.
     */
    $this->_pages['mods/calendar/index.php']['title_var'] = 'calendar_header';
    $this->_pages['mods/calendar/index.php']['img']       = 'mods/calendar/img/calendar.png';

    /*******
     * import page
     */
    $this->_pages['mods/calendar/file_import.php']['title_var']= 'calendar_import_file';
    $this->_pages['mods/calendar/file_import.php']['parent']   = 'mods/calendar/index.php';
    
    /*******
     * Email calendar link
     */
    $this->_pages['mods/calendar/send_mail.php']['title_var'] = 'calendar_share';
    $this->_pages['mods/calendar/send_mail.php']['parent']    = 'mods/calendar/index.php';
    
    /*******
     * Public page to display shared calendar
     */
    $this->_pages[AT_NAV_PUBLIC] = array('mods/calendar/index_public.php');
    $this->_pages['mods/calendar/index_public.php']['title_var'] = 'calendar';
?>