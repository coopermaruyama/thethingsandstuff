<?php

$installer = $this;

$installer->startSetup();
try {
    $installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('blog/blog')} (
  `post_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `post_content` text NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `identifier` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `update_user` varchar(255) NOT NULL DEFAULT '',
  `meta_keywords` text NOT NULL,
  `meta_description` text NOT NULL,
  `comments` tinyint(11) NOT NULL,
  `tags` text NOT NULL,
  `short_content` text NOT NULL,
  `banner_content` text NOT NULL,
  PRIMARY KEY (`post_id`),
  UNIQUE KEY `identifier` (`identifier`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('blog/blog')} (`post_id`,`title`,`post_content`,`status`,`image`,`created_time`,`update_time`,`identifier`,`user`,`update_user`,`meta_keywords`,`meta_description`,`comments`,`tags`,`short_content`,`banner_content`) values (3,'Nam facilisis adipiscing.','<p>Duis tincidunt augue purus, sagittis consectetur risus. Nulla consequat risus vel nibh elementum vitae adipiscing magna. Nam tincidunt magna quis libero aliquam eu hendrerit nisivarius. Proin at magna sem praesent.&nbsp;</p>\r\n<p>&nbsp; &nbsp;&nbsp;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas non justo a neque rhoncus luctus adipiscing sed sapien. Etiam molestie ante leo, sed lacinia nunc. Praesent eleifend nulla quis felis volutpat pretium. Morbi quis ligula mi. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n<p>&nbsp; &nbsp; Vestibulum non nisi at est convallis dignissim. Ut dignissim tellus vestibulum nulla vulputate sed consectetur libero pretium. Ut tempus odio vitae nisi blandit ut pretium ipsum.</p>\r\n<p>&nbsp; &nbsp;&nbsp;Praesent in lorem justo. Aliquam sem ipsum, placerat ut sodales vehicula, volutnon mauris. Duis scelerisque sagittis congue. Vivamus at sapien eros, id tincidunt nulla. Sed aliquam suscipit mi quis malesuada. Vivamus tellus ante, facilisis ut condimentum et, varius eu nibh. Sed odio odio, eleifend vitae lacinia eget, feugiat non tellus. Phasellus feugiat enim sed arcu consectetur ac vulputate erat congue. Etiam porta ultrices massa vel tempor. Suspendisse interdum, velit eu commodo lacinia. Ipsum nunc molestie turpis, non aliquet massa tellus non urna. Sed rhoncus molestie velit, ac lobortis lacus ultrices vel.</p>',1,'trego/blog/b01.jpg','2013-10-15 16:45:36','2013-10-15 23:19:47','nam_facillisis_adipiscing','Dmitry','Dmitry','','',0,'Clothing,Blog,Photography,Women','<p>Duis tincidunt augue purus, sagittis consectetur risus. Nulla consequat risus vel nibh elementum vitae adipiscing magna. Nam tincidunt magna quis libero aliquam eu hendrerit nisivarius. Proin at magna sem praesent.&nbsp;</p>','<img src=\"{{media url=\"wysiwyg/trego/blog/01.jpg\"}}\" alt=\"\" />'),(4,'Curabitur sed nulla metus.','<p>&nbsp; &nbsp;&nbsp;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas non justo a neque rhoncus luctus adipiscing sed sapien. Etiam molestie ante leo, sed lacinia nunc. Praesent eleifend nulla quis felis volutpat pretium. Morbi quis ligula mi. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n<p>&nbsp; &nbsp;Vestibulum non nisi at est convallis dignissim. Ut dignissim tellus vestibulum nulla vulputate sed consectetur libero pretium. Ut tempus odio vitae nisi blandit ut pretium ipsum.</p>',1,'trego/blog/b02.jpg','2013-10-09 01:53:14','2013-10-16 08:01:10','curabitur_sed_nulla_metus','Martin','Dmitry','','',0,'Fashion,Bags','<p>Duis tincidunt augue purus, sagittis consectetur risus. Nulla consequat risus vel nibh elementum vitae adipiscing magna. Nam tincidunt magna quis libero aliquam eu hendrerit nisivarius. Proin at magna sem praesent.&nbsp;</p>','<div id=\"post_2_banner\"><img src=\"{{media url=\"wysiwyg/trego/blog/02.jpg\"}}\" alt=\"\" /><img src=\"{{media url=\"wysiwyg/trego/blog/03.jpg\"}}\" alt=\"\" /></div>\r\n<script type=\"text/javascript\">// <![CDATA[\r\n    jQuery(document).ready(function(){\r\n        jQuery(\"#post_2_banner\").slidesjs({width: 575, height: 240, play: {active: false,auto:true}, navigation:{active:true},pagination:{active:false}});\r\n    });\r\n// ]]></script>'),(5,'Aenean fringilla pretium auctor.','<p>Duis tincidunt augue purus, sagittis consectetur risus. Nulla consequat risus vel nibh elementum vitae adipiscing magna. Nam tincidunt magna quis libero aliquam eu hendrerit nisivarius. Proin at magna sem praesent.&nbsp;</p>\r\n<p>Duis tincidunt augue purus, sagittis consectetur risus. Nulla consequat risus vel nibh elementum vitae adipiscing magna. Nam tincidunt magna quis libero aliquam eu hendrerit nisivarius. Proin at magna sem praesent.&nbsp;</p>\r\n<p>Duis tincidunt augue purus, sagittis consectetur risus. Nulla consequat risus vel nibh elementum vitae adipiscing magna. Nam tincidunt magna quis libero aliquam eu hendrerit nisivarius. Proin at magna sem praesent.&nbsp;</p>',1,'trego/blog/b03.jpg','2013-09-11 02:14:00','2013-10-16 11:47:06','aenean_fringilla_pretium_auctor','Anna','Dmitry','','',0,'Clother,Fashion','<p>Duis tincidunt augue purus, sagittis consectetur risus. Nulla consequat risus vel nibh elementum vitae adipiscing magna. Nam tincidunt magna quis libero aliquam eu hendrerit nisivarius. Proin at magna sem praesent.&nbsp;</p>','<p><iframe src=\"//www.youtube.com/embed/FbpE0QQzDnM\" frameborder=\"0\" width=\"100%\" height=\"300px\"></iframe></p>'),(6,'Pellentesque accumsan aliquam.','<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duispharetra magna id augue pellentesque non tempus nunc consectetur. Sed vel urna ut ante placerat euismod sit amet imperdiet orci. Curabitur sed nulla.</p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duispharetra magna id augue pellentesque non tempus nunc consectetur. Sed vel urna ut ante placerat euismod sit amet imperdiet orci. Curabitur sed nulla.</p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duispharetra magna id augue pellentesque non tempus nunc consectetur. Sed vel urna ut ante placerat euismod sit amet imperdiet orci. Curabitur sed nulla.</p>',1,NULL,'2013-07-31 02:21:00','2013-10-16 02:27:22','pellentesque_accumsan_aliquam','Admin','Dmitry','','',0,'Fashion,Shoes','<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duispharetra magna id augue pellentesque non tempus nunc consectetur. Sed vel urna ut ante placerat euismod sit amet imperdiet orci. Curabitur sed nulla.</p>','<p><img src=\"{{media url=\"wysiwyg/trego/blog/04.jpg\"}}\" alt=\"\" /></p>'),(7,'Maecenas et rutrum.','<p>Ut eros elit, blandit non malesuada et, tristique quis elit. Donec ultrices, magna a accumsan consectetur, erat est eleifend ante, ac rhoncus turpis odio vitae risus. Cras blanditenim at erat vehicula at viverra.</p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duispharetra magna id augue pellentesque non tempus nunc consectetur. Sed vel urna ut ante placerat euismod sit amet imperdiet orci. Curabitur sed nulla.</p>',1,NULL,'2013-06-14 02:23:00','2013-10-16 02:26:35','maecenas_et_rutrum','Roberto','Dmitry','','',0,'Fashio,Dresses','<p>Ut eros elit, blandit non malesuada et, tristique quis elit. Donec ultrices, magna a accumsan consectetur, erat est eleifend ante, ac rhoncus turpis odio vitae risus. Cras blanditenim at erat vehicula at viverra.</p>','<p><img src=\"{{media url=\"wysiwyg/trego/blog/05.jpg\"}}\" alt=\"\" /></p>');

CREATE TABLE IF NOT EXISTS {$this->getTable('blog/cat')} (
  `cat_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `identifier` varchar(255) NOT NULL DEFAULT '',
  `sort_order` tinyint(6) NOT NULL,
  `meta_keywords` text NOT NULL,
  `meta_description` text NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('blog/cat')} (`cat_id`,`title`,`identifier`,`sort_order`,`meta_keywords`,`meta_description`) values (2,'All about clothing','all-about-clothing',0,'',''),(3,'Make-up & beauty','make-up-beauty',1,'',''),(4,'Accessories','accessories',2,'',''),(5,'Fashion trends','fashion-trends',3,'',''),(6,'Haircuts & hairstyles','haircuts-hairstyles',4,'','');

CREATE TABLE IF NOT EXISTS {$this->getTable('blog/cat_store')} (
  `cat_id` smallint(6) unsigned DEFAULT NULL,
  `store_id` smallint(6) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('blog/cat_store')} (`cat_id`,`store_id`) values (2,0),(3,0),(4,0),(5,0),(6,0);

CREATE TABLE IF NOT EXISTS {$this->getTable('blog/comment')} (
  `comment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` smallint(11) NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  `created_time` datetime DEFAULT NULL,
  `user` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('blog/comment')} (`comment_id`,`post_id`,`comment`,`status`,`created_time`,`user`,`email`) values (2,3,'Cgestas metus id nunc vestibulum dictum. Etiam dapibus nunc nec risus egestas vel bibendum eros vehicula. Suspendisse facilisisneque in augue feugiat tempor donec velit diam pharetra.',2,'2013-10-16 13:28:09','Elen Aliquam','elen@gmail.com'),(3,3,'Aliquam eu augue dolor, eget commodo lacus. Nullam diam lorem, pellentesque dignissim tempor id, interdum quis nisi. Duis tempor, mauris nec interdum molestie, elit erat porta dui, quis sagittis sapien ante nec nibh.',2,'2013-10-16 13:31:16','Martin Doe','martin@gmail.com');

CREATE TABLE IF NOT EXISTS {$this->getTable('blog/post_cat')} (
  `cat_id` smallint(6) unsigned DEFAULT NULL,
  `post_id` smallint(6) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('blog/post_cat')} (`cat_id`,`post_id`) values (2,3),(6,7),(5,6),(2,4),(5,5);

CREATE TABLE IF NOT EXISTS {$this->getTable('blog/store')} (
  `post_id` smallint(6) unsigned DEFAULT NULL,
  `store_id` smallint(6) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('blog/store')} (`post_id`,`store_id`) values (3,1),(7,1),(6,1),(4,1),(5,1);

CREATE TABLE IF NOT EXISTS  {$this->getTable('blog/tag')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) NOT NULL,
  `tag_count` int(11) NOT NULL DEFAULT '0',
  `store_id` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tag` (`tag`,`tag_count`,`store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8;

INSERT INTO {$this->getTable('blog/tag')} (`id`,`tag`,`tag_count`,`store_id`) values (12,'Accessories',0,1),(63,'Bags',1,1),(22,'Blog',1,1),(73,'Clother',1,1),(2,'Clothing',1,1),(103,'Dresses',1,1),(83,'Fashio',1,1),(53,'Fashion',3,1),(32,'Photography',1,1),(93,'Shoes',1,1),(43,'Women',1,1);
");
} catch (Exception $e) {
    
}

$installer->endSetup();

