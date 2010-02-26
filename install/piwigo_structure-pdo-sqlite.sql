-----------------------------------------------------------------------------
-- piwigo_caddie
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_caddie;
CREATE TABLE "piwigo_caddie"
(
  "user_id" INTEGER default 0 NOT NULL,
  "element_id" INTEGER default 0 NOT NULL,
  PRIMARY KEY ("user_id","element_id")
);

-----------------------------------------------------------------------------
-- piwigo_categories
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_categories;
CREATE TABLE "piwigo_categories"
(
  "id" INTEGER NOT NULL,
  "name" VARCHAR(255) default '' NOT NULL,
  "id_uppercat" INTEGER,
  "comment" TEXT,
  "dir" VARCHAR(255),
  "rank" INTEGER,
  "status" VARCHAR(50) default 'public',
  "site_id" INTEGER default 1,
  "visible" BOOLEAN default true,
  "uploadable" BOOLEAN default false,
  "representative_picture_id" INTEGER,
  "uppercats" TEXT,
  "commentable" BOOLEAN default true,
  "global_rank" VARCHAR(255),
  "image_order" VARCHAR(128),
  "permalink" VARCHAR(64),
  PRIMARY KEY ("id"),
  CONSTRAINT "categories_i3" UNIQUE ("permalink")
);

CREATE INDEX "categories_i2" ON "piwigo_categories" ("id_uppercat");

-----------------------------------------------------------------------------
-- piwigo_config
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_config;
CREATE TABLE piwigo_config
(
  "param" VARCHAR(40) default '' NOT NULL,
  "value" TEXT,
  "comment" VARCHAR(255),
  PRIMARY KEY ("param")
);

-----------------------------------------------------------------------------
-- piwigo_favorites
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_favorites;
CREATE TABLE piwigo_favorites
(
  "user_id" INTEGER default 0 NOT NULL,
  "image_id" INTEGER default 0 NOT NULL,
  PRIMARY KEY ("user_id","image_id")
);


-----------------------------------------------------------------------------
-- piwigo_group_access
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_group_access;
CREATE TABLE piwigo_group_access
(
  "group_id" INTEGER default 0 NOT NULL,
  "cat_id" INTEGER default 0 NOT NULL,
  PRIMARY KEY ("group_id","cat_id")
);

-----------------------------------------------------------------------------
-- piwigo_groups
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_groups;
CREATE TABLE piwigo_groups
(
  "id" INTEGER NOT NULL,
  "name" VARCHAR(255) default '' NOT NULL,
  "is_default" BOOLEAN default false,
  PRIMARY KEY ("id"),
  CONSTRAINT "groups_ui1" UNIQUE ("name")
);

-----------------------------------------------------------------------------
-- piwigo_history
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_history;
CREATE TABLE piwigo_history
(
  "id" INTEGER NOT NULL,
  "date" DATE NOT NULL,
  "time" TIME NOT NULL,
  "user_id" INTEGER default 0 NOT NULL,
  "ip" VARCHAR(15) default '' NOT NULL,
  "section" VARCHAR(50) default NULL,
  "category_id" INTEGER,
  "tag_ids" VARCHAR(50),
  "image_id" INTEGER,
  "summarized" BOOLEAN default false,
  "image_type" VARCHAR(50) default NULL,
  PRIMARY KEY ("id")
);


CREATE INDEX "history_i1" ON "piwigo_history" ("summarized");

-----------------------------------------------------------------------------
-- piwigo_history_summary
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_history_summary;
CREATE TABLE piwigo_history_summary
(
  "year" INTEGER default 0 NOT NULL,
  "month" INTEGER,
  "day" INTEGER,
  "hour" INTEGER,
  "nb_pages" INTEGER,
  "id" INTEGER NOT NULL,
  PRIMARY KEY ("id"),
  CONSTRAINT "history_summary_ymdh" UNIQUE ("year","month","day","hour")
);

-----------------------------------------------------------------------------
-- piwigo_image_category
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_image_category;
CREATE TABLE piwigo_image_category
(
  "image_id" INTEGER default 0 NOT NULL,
  "category_id" INTEGER default 0 NOT NULL,
  "rank" INTEGER,
  PRIMARY KEY ("image_id","category_id")
);


CREATE INDEX "image_category_i1" ON "piwigo_image_category" ("category_id");

-----------------------------------------------------------------------------
-- piwigo_image_tag
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_image_tag;
CREATE TABLE piwigo_image_tag
(
  "image_id" INTEGER default 0 NOT NULL,
  "tag_id" INTEGER default 0 NOT NULL,
  PRIMARY KEY ("image_id","tag_id")
);


CREATE INDEX "image_tag_i1" ON "piwigo_image_tag" ("tag_id");

-----------------------------------------------------------------------------
-- piwigo_images
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_images;
CREATE TABLE piwigo_images
(
  "id" INTEGER NOT NULL,
  "file" VARCHAR(255) default '' NOT NULL,
  "date_available" TIMESTAMP NOT NULL,
  "date_creation" TIMESTAMP,
  "tn_ext" VARCHAR(4) default '',
  "name" VARCHAR(255),
  "comment" TEXT,
  "author" VARCHAR(255),
  "hit" INTEGER default 0 NOT NULL,
  "filesize" INTEGER,
  "width" INTEGER,
  "height" INTEGER,
  "representative_ext" VARCHAR(4),
  "date_metadata_update" DATE,
  "average_rate" FLOAT,
  "has_high" BOOLEAN default false,
  "path" VARCHAR(255) default '' NOT NULL,
  "storage_category_id" INTEGER,
  "high_filesize" INTEGER,
  "level" INTEGER default 0 NOT NULL,
  "md5sum" CHAR(32),
  PRIMARY KEY ("id")
);


CREATE INDEX "images_i2" ON "piwigo_images" ("date_available");

CREATE INDEX "images_i3" ON "piwigo_images" ("average_rate");

CREATE INDEX "images_i4" ON "piwigo_images" ("hit");

CREATE INDEX "images_i5" ON "piwigo_images" ("date_creation");

CREATE INDEX "images_i1" ON "piwigo_images" ("storage_category_id");

-----------------------------------------------------------------------------
-- piwigo_old_permalinks
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_old_permalinks;
CREATE TABLE piwigo_old_permalinks
(
  "cat_id" INTEGER default 0 NOT NULL,
  "permalink" VARCHAR(64) default '' NOT NULL,
  "date_deleted" TIMESTAMP NOT NULL,
  "last_hit" TIMESTAMP,
  "hit" INTEGER default 0 NOT NULL,
  PRIMARY KEY ("permalink")
);


-----------------------------------------------------------------------------
-- piwigo_plugins
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_plugins;
CREATE TABLE piwigo_plugins
(
  "id" VARCHAR(64) default '' NOT NULL,
  "state" VARCHAR(50) default 'inactive',
  "version" VARCHAR(64) default '0' NOT NULL,
  PRIMARY KEY ("id")
);


-----------------------------------------------------------------------------
-- piwigo_rate
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_rate;
CREATE TABLE piwigo_rate
(
  "user_id" INTEGER default 0 NOT NULL,
  "element_id" INTEGER default 0 NOT NULL,
  "anonymous_id" VARCHAR(45) default '' NOT NULL,
  "rate" INTEGER default 0 NOT NULL,
  "date" DATE  NOT NULL,
  PRIMARY KEY ("user_id","element_id","anonymous_id")
);


-----------------------------------------------------------------------------
-- piwigo_search
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_search;
CREATE TABLE piwigo_search
(
  "id" INTEGER NOT NULL,
  "last_seen" DATE,
  "rules" TEXT,
  PRIMARY KEY ("id")
);


-----------------------------------------------------------------------------
-- piwigo_sessions
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_sessions;
CREATE TABLE piwigo_sessions
(
  "id" VARCHAR(255) default '' NOT NULL,
  "data" TEXT  NOT NULL,
  "expiration" TIMESTAMP NOT NULL,
  PRIMARY KEY ("id")
);


-----------------------------------------------------------------------------
-- piwigo_sites
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_sites;
CREATE TABLE piwigo_sites
(
  "id" INTEGER NOT NULL,
  "galleries_url" VARCHAR(255) default '' NOT NULL,
  PRIMARY KEY ("id"),
  CONSTRAINT "sites_ui1" UNIQUE ("galleries_url")
);


-----------------------------------------------------------------------------
-- piwigo_stuffs
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_stuffs;
CREATE TABLE piwigo_stuffs
(
  "id" INTEGER  NOT NULL,
  "pos" INTEGER  NOT NULL,
  "name" TEXT  NOT NULL,
  "descr" VARCHAR(255),
  "type" VARCHAR(255)  NOT NULL,
  "datas" TEXT,
  "users" VARCHAR(255),
  "groups" VARCHAR(255),
  "show_title" CHAR  NOT NULL,
  "on_home" CHAR  NOT NULL,
  "on_cats" CHAR  NOT NULL,
  "on_picture" CHAR  NOT NULL,
  "id_line" VARCHAR(1),
  "width" INTEGER,
  PRIMARY KEY ("id")
);


CREATE INDEX "on_home" ON "piwigo_stuffs" ("on_home");

CREATE INDEX "on_cats" ON "piwigo_stuffs" ("on_cats");

CREATE INDEX "on_picture" ON "piwigo_stuffs" ("on_picture");

-----------------------------------------------------------------------------
-- piwigo_tags
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_tags;
CREATE TABLE piwigo_tags
(
  "id" INTEGER NOT NULL,
  "name" VARCHAR(255) default '' NOT NULL,
  "url_name" VARCHAR(255) default '' NOT NULL,
  PRIMARY KEY ("id")
);


CREATE INDEX "tags_i1" ON "piwigo_tags" ("url_name");

-----------------------------------------------------------------------------
-- piwigo_upgrade
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_upgrade;
CREATE TABLE piwigo_upgrade
(
  "id" VARCHAR(20) default '' NOT NULL,
  "applied" TIMESTAMP NOT NULL,
  "description" VARCHAR(255),
  PRIMARY KEY ("id")
);


-----------------------------------------------------------------------------
-- piwigo_user_access
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_user_access;
CREATE TABLE piwigo_user_access
(
  "user_id" INTEGER default 0 NOT NULL,
  "cat_id" INTEGER default 0 NOT NULL,
  PRIMARY KEY ("user_id","cat_id")
);


-----------------------------------------------------------------------------
-- piwigo_user_cache
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_user_cache;
CREATE TABLE piwigo_user_cache
(
  "user_id" INTEGER default 0 NOT NULL,
  "need_update" BOOLEAN default true,
  "cache_update_time" INTEGER default 0 NOT NULL,
  "forbidden_categories" TEXT,
  "nb_total_images" INTEGER,
  "image_access_type" VARCHAR(50) default 'NOT IN',
  "image_access_list" TEXT,
  PRIMARY KEY ("user_id")
);


-----------------------------------------------------------------------------
-- piwigo_user_cache_categories
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_user_cache_categories;
CREATE TABLE piwigo_user_cache_categories
(
  "user_id" INTEGER default 0 NOT NULL,
  "cat_id" INTEGER default 0 NOT NULL,
  "date_last" TIMESTAMP,
  "max_date_last" TIMESTAMP,
  "nb_images" INTEGER default 0 NOT NULL,
  "count_images" INTEGER default 0,
  "count_categories" INTEGER default 0,
  PRIMARY KEY ("user_id","cat_id")
);


-----------------------------------------------------------------------------
-- piwigo_user_feed
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_user_feed;
CREATE TABLE piwigo_user_feed
(
  "id" VARCHAR(50) default '' NOT NULL,
  "user_id" INTEGER default 0 NOT NULL,
  "last_check" TIMESTAMP,
  PRIMARY KEY ("id")
);


-----------------------------------------------------------------------------
-- piwigo_user_group
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_user_group;
CREATE TABLE piwigo_user_group
(
  "user_id" INTEGER default 0 NOT NULL,
  "group_id" INTEGER default 0 NOT NULL,
  PRIMARY KEY ("user_id","group_id")
);


-----------------------------------------------------------------------------
-- piwigo_user_infos
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_user_infos;
CREATE TABLE piwigo_user_infos
(
  "user_id" INTEGER default 0 NOT NULL,
  "nb_image_line" INTEGER default 5 NOT NULL,
  "nb_line_page" INTEGER default 3 NOT NULL,
  "status" VARCHAR(50) default 'guest',
  "adviser" BOOLEAN default false,
  "language" VARCHAR(50) default 'en_UK' NOT NULL,
  "maxwidth" INTEGER,
  "maxheight" INTEGER,
  "expand" BOOLEAN default false,
  "show_nb_comments" BOOLEAN default false,
  "show_nb_hits" BOOLEAN default false,
  "recent_period" INTEGER default 7 NOT NULL,
  "template" VARCHAR(255) default 'yoga/Sylvia' NOT NULL,
  "registration_date" TIMESTAMP NOT NULL,
  "enabled_high" BOOLEAN default true,
  "level" INTEGER default 0 NOT NULL,
  PRIMARY KEY ("user_id"),
  CONSTRAINT "user_infos_ui1" UNIQUE ("user_id")
);


-----------------------------------------------------------------------------
-- piwigo_user_mail_notification
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_user_mail_notification;
CREATE TABLE piwigo_user_mail_notification
(
  "user_id" INTEGER default 0 NOT NULL,
  "check_key" VARCHAR(16) default '' NOT NULL,
  "enabled" BOOLEAN default false,
  "last_send" TIMESTAMP,
  PRIMARY KEY ("user_id"),
  CONSTRAINT "user_mail_notification_ui1" UNIQUE ("check_key")
);


-----------------------------------------------------------------------------
-- piwigo_users
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_users;
CREATE TABLE piwigo_users
(
  "id" INTEGER NOT NULL,
  "username" VARCHAR(100) default '' NOT NULL,
  "password" VARCHAR(32),
  "mail_address" VARCHAR(255),
  PRIMARY KEY ("id"),
  CONSTRAINT "users_ui1" UNIQUE ("username")
);


-----------------------------------------------------------------------------
-- piwigo_comments
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_comments;
CREATE TABLE piwigo_comments
(
  "id" INTEGER NOT NULL,
  "image_id" INTEGER default 0 NOT NULL,
  "date" TIMESTAMP  NOT NULL,
  "author" VARCHAR(255),
  "content" TEXT,
  "validated" BOOLEAN default false,
  "validation_date" TIMESTAMP,
  "author_id" INTEGER REFERENCES "piwigo_users" (id),
  PRIMARY KEY ("id")
);

CREATE INDEX "comments_i2" ON "piwigo_comments" ("validation_date");
CREATE INDEX "comments_i1" ON "piwigo_comments" ("image_id");

-----------------------------------------------------------------------------
-- piwigo_waiting
-----------------------------------------------------------------------------

DROP TABLE IF EXISTS piwigo_waiting;
CREATE TABLE piwigo_waiting
(
  "id" INTEGER NOT NULL,
  "storage_category_id" INTEGER default 0 NOT NULL,
  "file" VARCHAR(255) default '' NOT NULL,
  "username" VARCHAR(255) default '' NOT NULL,
  "mail_address" VARCHAR(255) default '' NOT NULL,
  "date" INTEGER default 0 NOT NULL,
  "tn_ext" CHAR(3),
  "validated" BOOLEAN default false,
  "infos" TEXT,
  PRIMARY KEY ("id")
);

