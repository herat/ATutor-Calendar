CREATE TABLE `full_calendar_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256),
  `start` datetime,
  `end` datetime,
  `allDay` varchar(20),
  `userid` int(8),
  PRIMARY KEY (`id`)
);

CREATE TABLE `google_sync` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(256),
  `userid` int(8),
  `calids` text,
  PRIMARY KEY (`id`)
);

CREATE TABLE `bookmark_cal` (
  `memberid` int(11),
  `ownerid` int(8),
  `calname` varchar(256)
);

INSERT INTO `language_text` VALUES ('en', '_module','calendar','Calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_header','ATutor Calendar',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','at_cal_import_file','Import ics file',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_upload_file','Upload ics file',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_submit','Submit',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_export_file','Export ics file',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','at_cal_options','Calendar Options',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','at_cal_disconnect_gcal','Disconnect from Google Calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_connect_gcal','Connect with Google Calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_gcals','Google Calendars',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_internal_events','ATutor Internal Events',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_events_persnl','Personal Events',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_events_assign_due','Assignment Due Date',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_events_assign_cut','Assignment Cut off Date',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_events_course_rel','Course Release Date',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_events_course_end','Course End Date',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_events_test_start','Test Start Date',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_events_test_end','Test End Date',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','at_cal_tooltip_cell','Click or press enter to create event',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_tooltip_event','Click or press enter to edit event',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','at_cal_form_title','Event Title',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_form_start_d','Start Date (yyyy-mm-dd)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_form_start_t','Start Time (24hours)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_form_end_d','End Date (yyyy-mm-dd)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_form_end_t','End Time (24hours)',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_form_title_def','Event Name',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','at_cal_next','Next',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_prev','Previous',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_today','Today',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_month','Month',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_week','Week',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_day','Day',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','at_cal_nxt_mnth','Next Month',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_prv_mnth','Previous Month',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_nxt_week','Next Week',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_prv_week','Previous Week',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_nxt_day','Next Day',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_prv_day','Previous Day',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','at_cal_creat_e','Create Event',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_cancel_e','Cancel',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_edit_e','Edit Event',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_del_e','Delete Event',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_module','at_cal_share','Share Calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_bookmarkd','Bookmarked Calendars',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_of','Calendar of',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_viewcal','View',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_sendall','Send to all',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_sellist','Select from list',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_manemail','Enter email address',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_mailtxt','Enter email',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_membrselect','Select member',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_titletxt','Title of Calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_bookmark_this','Bookmark this Calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_del_bookmark','Remove Bookmark',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_save','Save',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_edit_title','Edit Title',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_view_title','View Shared Calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_mail_title','Shared Calendar',NOW(),'');

INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_INVALID_EMAIL','Email address is invalid.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_ALREADY_BOOKMARKED','Calendar is already bookmarked.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_CAL_FILE_ERROR','Error in file processing.',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_msgs','AT_ERROR_CAL_FILE_DELETE','Error in removing duplicate file.',NOW(),'');