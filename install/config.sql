-- initial configuration for Piwigo

INSERT INTO piwigo_config (param,value,comment) VALUES ('nb_comment_page','10','number of comments to display on each page');
INSERT INTO piwigo_config (param,value,comment) VALUES ('log','false','keep an history of visits on your website');
INSERT INTO piwigo_config (param,value,comment) VALUES ('comments_validation','false','administrators validate users comments before becoming visible');
INSERT INTO piwigo_config (param,value,comment) VALUES ('comments_forall','false','even guest not registered can post comments');
INSERT INTO piwigo_config (param,value,comment) VALUES ('user_can_delete_comment','false','administrators can allow user delete their own comments');
INSERT INTO piwigo_config (param,value,comment) VALUES ('user_can_edit_comment','false','administrators can allow user edit their own comments');
INSERT INTO piwigo_config (param,value,comment) VALUES ('email_admin_on_comment_edition','false','Send an email to the administrators when a comment is modified');
INSERT INTO piwigo_config (param,value,comment) VALUES ('email_admin_on_comment_deletion','false','Send an email to the administrators when a comment is deleted');
INSERT INTO piwigo_config (param,value,comment) VALUES ('gallery_locked','false','Lock your gallery temporary for non admin users');
INSERT INTO piwigo_config (param,value,comment) VALUES ('gallery_title','Piwigo demonstration site','Title at top of each page and for RSS feed');
INSERT INTO piwigo_config (param,value,comment) VALUES ('gallery_url','http://piwigo.org/demo','URL given in RSS feed');
INSERT INTO piwigo_config (param,value,comment) VALUES ('rate','true','Rating pictures feature is enabled');
INSERT INTO piwigo_config (param,value,comment) VALUES ('rate_anonymous','true','Rating pictures feature is also enabled for visitors');
INSERT INTO piwigo_config (param,value,comment) VALUES ('page_banner','<h1>Piwigo demonstration site</h1><p>My photos web site</p>','html displayed on the top each page of your gallery');
INSERT INTO piwigo_config (param,value,comment) VALUES ('history_admin','false','keep a history of administrator visits on your website');
INSERT INTO piwigo_config (param,value,comment) VALUES ('history_guest','true','keep a history of guest visits on your website');
INSERT INTO piwigo_config (param,value,comment) VALUES ('allow_user_registration','true','allow visitors to register?');
INSERT INTO piwigo_config (param,value,comment) VALUES ('nbm_send_html_mail','true','Send mail on HTML format for notification by mail');
INSERT INTO piwigo_config (param,value,comment) VALUES ('nbm_send_mail_as','','Send mail as param value for notification by mail');
INSERT INTO piwigo_config (param,value,comment) VALUES ('nbm_send_detailed_content','true','Send detailed content for notification by mail');
INSERT INTO piwigo_config (param,value,comment) VALUES ('nbm_complementary_mail_content','','Complementary mail content for notification by mail');
INSERT INTO piwigo_config (param,value,comment) VALUES ('nbm_send_recent_post_dates','true','Send recent post by dates for notification by mail');
INSERT INTO piwigo_config (param,value,comment) VALUES ('email_admin_on_new_user','false','Send an email to theadministrators when a user registers');
INSERT INTO piwigo_config (param,value,comment) VALUES ('email_admin_on_comment','false','Send an email to the administrators when a valid comment is entered');
INSERT INTO piwigo_config (param,value,comment) VALUES ('email_admin_on_comment_validation','false','Send an email to the administrators when a comment requires validation');
INSERT INTO piwigo_config (param,value,comment) VALUES ('email_admin_on_picture_uploaded','false','Send an email to the administrators when a picture is uploaded');
INSERT INTO piwigo_config (param,value,comment) VALUES ('obligatory_user_mail_address','false','Mail address is obligatory for users');
INSERT INTO piwigo_config (param,value,comment) VALUES ('c13y_ignore',null,'List of ignored anomalies');
INSERT INTO piwigo_config (param,value,comment) VALUES ('upload_link_everytime','false','Show upload link every time');
INSERT INTO piwigo_config (param,value,comment) VALUES ('upload_user_access',2 /*ACCESS_CLASSIC*/,'User access level to upload');
INSERT INTO piwigo_config (param,value,comment) VALUES ('extents_for_templates','a:0:{}','Actived template-extension(s)');
INSERT INTO piwigo_config (param,value,comment) VALUES ('blk_menubar','','Menubar options');
INSERT INTO piwigo_config (param,value,comment) VALUES ('menubar_filter_icon','true','Display filter icon');
INSERT INTO piwigo_config (param,value,comment) VALUES ('index_sort_order_input','true','Display image order selection list');
INSERT INTO piwigo_config (param,value,comment) VALUES ('index_flat_icon','true','Display flat icon');
INSERT INTO piwigo_config (param,value,comment) VALUES ('index_posted_date_icon','true','Display calendar by posted date');
INSERT INTO piwigo_config (param,value,comment) VALUES ('index_created_date_icon','true','Display calendar by creation date icon');
INSERT INTO piwigo_config (param,value,comment) VALUES ('index_slideshow_icon','true','Display slideshow icon');
INSERT INTO piwigo_config (param,value,comment) VALUES ('picture_metadata_icon','true','Display metadata icon on picture page');
INSERT INTO piwigo_config (param,value,comment) VALUES ('picture_slideshow_icon','true','Display slideshow icon on picture page');
INSERT INTO piwigo_config (param,value,comment) VALUES ('picture_favorite_icon','true','Display favorite icon on picture page');
INSERT INTO piwigo_config (param,value,comment) VALUES ('picture_download_icon','true','Display download icon on picture page');
INSERT INTO piwigo_config (param,value,comment) VALUES ('picture_navigation_icons','true','Display navigation icons on picture page');
INSERT INTO piwigo_config (param,value,comment) VALUES ('picture_navigation_thumb','true','Display navigation thumbnails on picture page');
INSERT INTO piwigo_config (param,value,comment)
  VALUES (
    'picture_informations',
    'a:11:{s:6:"author";b:1;s:10:"created_on";b:1;s:9:"posted_on";b:1;s:10:"dimensions";b:1;s:4:"file";b:1;s:8:"filesize";b:1;s:4:"tags";b:1;s:10:"categories";b:1;s:6:"visits";b:1;s:12:"average_rate";b:1;s:13:"privacy_level";b:1;}',
    'Information displayed on picture page'
  );

INSERT INTO piwigo_themes (id, name) VALUES ('Sylvia', 'Sylvia');
INSERT INTO piwigo_themes (id, name) VALUES ('clear', 'clear');
INSERT INTO piwigo_themes (id, name) VALUES ('dark', 'dark');
