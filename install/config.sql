-- initial configuration for Piwigo

INSERT INTO piwigo_config (param,value,comment) VALUES ('activate_comments','false','Global parameter for usage of comments system');
INSERT INTO piwigo_config (param,value,comment) VALUES ('nb_comment_page','10','number of comments to display on each page');
INSERT INTO piwigo_config (param,value,comment) VALUES ('log','true','keep an history of visits on your website');
INSERT INTO piwigo_config (param,value,comment) VALUES ('comments_validation','false','administrators validate users comments before becoming visible');
INSERT INTO piwigo_config (param,value,comment) VALUES ('comments_forall','false','even guest not registered can post comments');
INSERT INTO piwigo_config (param,value,comment) VALUES ('comments_order','ASC','comments order on picture page and cie');
INSERT INTO piwigo_config (param,value,comment) VALUES ('comments_author_mandatory','false','Comment author is mandatory');
INSERT INTO piwigo_config (param,value,comment) VALUES ('comments_email_mandatory','false','Comment email is mandatory');
INSERT INTO piwigo_config (param,value,comment) VALUES ('comments_enable_website','true','Enable "website" field on add comment form');
INSERT INTO piwigo_config (param,value,comment) VALUES ('user_can_delete_comment','false','administrators can allow user delete their own comments');
INSERT INTO piwigo_config (param,value,comment) VALUES ('user_can_edit_comment','false','administrators can allow user edit their own comments');
INSERT INTO piwigo_config (param,value,comment) VALUES ('email_admin_on_comment_edition','false','Send an email to the administrators when a comment is modified');
INSERT INTO piwigo_config (param,value,comment) VALUES ('email_admin_on_comment_deletion','false','Send an email to the administrators when a comment is deleted');
INSERT INTO piwigo_config (param,value,comment) VALUES ('gallery_locked','false','Lock your gallery temporary for non admin users');
INSERT INTO piwigo_config (param,value,comment) VALUES ('gallery_title','','Title at top of each page and for RSS feed');
INSERT INTO piwigo_config (param,value,comment) VALUES ('rate','false','Rating pictures feature is enabled');
INSERT INTO piwigo_config (param,value,comment) VALUES ('rate_anonymous','true','Rating pictures feature is also enabled for visitors');
INSERT INTO piwigo_config (param,value,comment) VALUES ('page_banner','','html displayed on the top each page of your gallery');
INSERT INTO piwigo_config (param,value,comment) VALUES ('history_admin','false','keep a history of administrator visits on your website');
INSERT INTO piwigo_config (param,value,comment) VALUES ('history_guest','true','keep a history of guest visits on your website');
INSERT INTO piwigo_config (param,value,comment) VALUES ('allow_user_registration','true','allow visitors to register?');
INSERT INTO piwigo_config (param,value,comment) VALUES ('allow_user_customization','true','allow users to customize their gallery?');
INSERT INTO piwigo_config (param,value,comment) VALUES ('nb_categories_page','12','Param for categories pagination');
INSERT INTO piwigo_config (param,value,comment) VALUES ('nbm_send_html_mail','true','Send mail on HTML format for notification by mail');
INSERT INTO piwigo_config (param,value,comment) VALUES ('nbm_send_mail_as','','Send mail as param value for notification by mail');
INSERT INTO piwigo_config (param,value,comment) VALUES ('nbm_send_detailed_content','true','Send detailed content for notification by mail');
INSERT INTO piwigo_config (param,value,comment) VALUES ('nbm_complementary_mail_content','','Complementary mail content for notification by mail');
INSERT INTO piwigo_config (param,value,comment) VALUES ('nbm_send_recent_post_dates','true','Send recent post by dates for notification by mail');
INSERT INTO piwigo_config (param,value,comment) VALUES ('email_admin_on_new_user','false','Send an email to theadministrators when a user registers');
INSERT INTO piwigo_config (param,value,comment) VALUES ('email_admin_on_comment','false','Send an email to the administrators when a valid comment is entered');
INSERT INTO piwigo_config (param,value,comment) VALUES ('email_admin_on_comment_validation','true','Send an email to the administrators when a comment requires validation');
INSERT INTO piwigo_config (param,value,comment) VALUES ('obligatory_user_mail_address','false','Mail address is obligatory for users');
INSERT INTO piwigo_config (param,value,comment) VALUES ('c13y_ignore',null,'List of ignored anomalies');
INSERT INTO piwigo_config (param,value,comment) VALUES ('extents_for_templates','a:0:{}','Actived template-extension(s)');
INSERT INTO piwigo_config (param,value,comment) VALUES ('blk_menubar','','Menubar options');
INSERT INTO piwigo_config (param,value,comment) VALUES ('menubar_filter_icon','false','Display filter icon');
INSERT INTO piwigo_config (param,value,comment) VALUES ('index_sort_order_input','true','Display image order selection list');
INSERT INTO piwigo_config (param,value,comment) VALUES ('index_flat_icon','false','Display flat icon');
INSERT INTO piwigo_config (param,value,comment) VALUES ('index_posted_date_icon','true','Display calendar by posted date');
INSERT INTO piwigo_config (param,value,comment) VALUES ('index_created_date_icon','true','Display calendar by creation date icon');
INSERT INTO piwigo_config (param,value,comment) VALUES ('index_slideshow_icon','true','Display slideshow icon');
INSERT INTO piwigo_config (param,value,comment) VALUES ('index_new_icon','true','Display new icons next albums and pictures');
INSERT INTO piwigo_config (param,value,comment) VALUES ('picture_metadata_icon','true','Display metadata icon on picture page');
INSERT INTO piwigo_config (param,value,comment) VALUES ('picture_slideshow_icon','true','Display slideshow icon on picture page');
INSERT INTO piwigo_config (param,value,comment) VALUES ('picture_favorite_icon','true','Display favorite icon on picture page');
INSERT INTO piwigo_config (param,value,comment) VALUES ('picture_download_icon','true','Display download icon on picture page');
INSERT INTO piwigo_config (param,value,comment) VALUES ('picture_navigation_icons','true','Display navigation icons on picture page');
INSERT INTO piwigo_config (param,value,comment) VALUES ('picture_navigation_thumb','true','Display navigation thumbnails on picture page');
INSERT INTO piwigo_config (param,value,comment) VALUES ('picture_menu','false','Show menubar on picture page');
INSERT INTO piwigo_config (param,value,comment)
  VALUES (
    'picture_informations',
    'a:11:{s:6:"author";b:1;s:10:"created_on";b:1;s:9:"posted_on";b:1;s:10:"dimensions";b:0;s:4:"file";b:0;s:8:"filesize";b:0;s:4:"tags";b:1;s:10:"categories";b:1;s:6:"visits";b:1;s:12:"rating_score";b:1;s:13:"privacy_level";b:1;}',
    'Information displayed on picture page'
  );
INSERT INTO piwigo_config (param,value,comment) VALUES ('week_starts_on','monday','Monday may not be the first day of the week');
INSERT INTO piwigo_config (param,value,comment) VALUES ('updates_ignored','a:3:{s:7:"plugins";a:0:{}s:6:"themes";a:0:{}s:9:"languages";a:0:{}}','Extensions ignored for update');
INSERT INTO piwigo_config (param,value,comment) VALUES ('order_by','ORDER BY date_available DESC, file ASC, id ASC','default photo order');
INSERT INTO piwigo_config (param,value,comment) VALUES ('order_by_inside_category','ORDER BY date_available DESC, file ASC, id ASC','default photo order inside category');
INSERT INTO piwigo_config (param,value) VALUES ('original_resize','false');
INSERT INTO piwigo_config (param,value) VALUES ('original_resize_maxwidth','2016');
INSERT INTO piwigo_config (param,value) VALUES ('original_resize_maxheight','2016');
INSERT INTO piwigo_config (param,value) VALUES ('original_resize_quality','95');
INSERT INTO piwigo_config (param,value) VALUES ('mobile_theme',null);
INSERT INTO piwigo_config (param,value) VALUES ('mail_theme','clear');
INSERT INTO piwigo_config (param,value) VALUES ('picture_sizes_icon','true');
INSERT INTO piwigo_config (param,value) VALUES ('index_sizes_icon','true');
INSERT INTO piwigo_config (param,value) VALUES ('index_edit_icon','true');
INSERT INTO piwigo_config (param,value) VALUES ('index_caddie_icon','true');
INSERT INTO piwigo_config (param,value) VALUES ('display_fromto','false');
INSERT INTO piwigo_config (param,value) VALUES ('picture_edit_icon','true');
INSERT INTO piwigo_config (param,value) VALUES ('picture_caddie_icon','true');
INSERT INTO piwigo_config (param,value) VALUES ('picture_representative_icon','true');
