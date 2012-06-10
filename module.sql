CREATE TABLE `full_calendar_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256),
  `start` datetime,
  `end` datetime,
  `allDay` varchar(20),
  PRIMARY KEY (`id`)
);

INSERT INTO `language_text` VALUES ('en', '_module','calendar','Calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_header','ATutor Calendar',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_next','Next',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_prev','Previous',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_today','Today',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_month','Month',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_week','Week',NOW(),'');
INSERT INTO `language_text` VALUES ('en', '_module','at_cal_day','Day',NOW(),'');