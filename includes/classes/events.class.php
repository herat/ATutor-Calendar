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
     * This class file provides class to access personal 
     * and ATutor internal events.
     */
    
    class Events {
        /**
         * Array of modules from which dates are to be collected
         */
        public $modules;
        
        /**
         * Initialize modules array, additional modules may be inserted here
         */
        function __construct() {
            $this->modules   = array();
            
            $this->modules[] = "_core/courses";
            $this->modules[] = "_standard/assignments";
            $this->modules[] = "_standard/tests";
        }        
        
        /**
         * Retrieve personal events
         *
         * @access public
         * @param int id of user
         * @param bool FALSE=Default value, TRUE= For export purpose
         * @return mixed Array containing all personal events of a user
         */
        public function get_personal_events($userid, $export = FALSE) {
            global $db;
            $rows = array();
            $query = "SELECT * FROM `".TABLE_PREFIX."calendar_events` WHERE userid='".
                     $userid."'";
            $result = mysql_query($query,$db);
        
            //Create an empty array and push all the events in it.
            while ($row = mysql_fetch_assoc($result)) {
                if (!$export) { 
                    $row["editable"] = true;
                    $row["calendar"] = "Personal event";
                }
                array_push($rows,$row);
            }            
            return $rows;    
        }
        
        /**
         * Retrieve ATutor course events
         *
         * @access public
         * @param int id of user
         * @param int id of course
         * @param bool FALSE=Default value, TRUE= For export purpose
         * @return mixed Array containing all course related events
         */
        public function get_atutor_events($member_id, $course_id, $export = FALSE) {
            /* check if the user is enrolled in the course */
            try {
                global $db;
                
                $sql = "SELECT COUNT(*) FROM
                       `".TABLE_PREFIX."course_enrollment`
                        WHERE `member_id`='".$member_id."'
                        AND   `course_id`='".$course_id."'";
                
                $result = mysql_query($sql,$db);
                $row = mysql_fetch_row($result);
                
                if ($row[0]>0) {
                    global $moduleFactory;
                    $rows    = array();
                                    
                    foreach ($this->modules as $modulename) {
                        $module_obj = $moduleFactory -> getModule($modulename);
                        if (method_exists($module_obj,'extend_date')) {
                            $events     = $module_obj    -> extend_date($member_id, $course_id);    
                            if ($events != "") {
                                foreach ( $events as $event ) {
                                    if (!$export) {
                                        $event["calendar"]="ATutor internal";
                                    }
                                    array_push($rows, $event);
                                }
                            }
                        } else {
                            return "error";
                        }                       
                    }
                    return $rows;
                }
            } catch (Exception $e) {
                return "error";
            }
        }
        
        /**
         * Retrieve ATutor course events
         *
         * @access public
         * @param int id of user
         * @param bool FALSE=Default value, TRUE= For export purpose
         * @return mixed Array containing all course related events
         */
        public function get_atutor_events_all_courses($member_id, $export = FALSE) {
            /* check if the user is enrolled in the course */
            try {
                global $db;
                
                $sql = "SELECT * FROM
                       `".TABLE_PREFIX."course_enrollment`
                        WHERE `member_id`='".$member_id."'";
                
                $result = mysql_query($sql,$db);
                
                global $moduleFactory;
                $rows_b    = array();
                
                while ($row = mysql_fetch_array($result)) {
                    
                    foreach ($this->modules as $modulename) {
                        $module_obj = $moduleFactory -> getModule($modulename);
                        if (method_exists($module_obj,'extend_date')) {
                            $events     = $module_obj    -> extend_date($member_id, $row["course_id"]);    
                            if ($events != "") {
                                foreach ( $events as $event ) {
                                    if (!$export) {
                                        $event["calendar"]="ATutor internal";
                                    }
                                    array_push($rows_b, $event);
                                }
                            }
                        } else {
                            return "error";
                        }                       
                    }
                }
                return $rows_b;
            } catch (Exception $e) {
                return "error";
            }
        }
        
        /**
         * Retrieve JSON encoded events
         *
         * @access public
         * @param mixed Array events extracted from db
         * @return string JSON formatted string of events
         */
        public function caledar_encode($rows) {
            //Encode in JSON format.
            $str =  json_encode( $rows );
        
            //Replace "true","false" with true,false for javascript.
            $str = str_replace('"true"','true',$str);
            $str = str_replace('"false"','false',$str);
        
            //Return the events in the JSON format.
            return $str;
        }
    }
?>