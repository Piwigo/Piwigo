<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008      Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

$lang['Installation'] = 'インストレーション';
$lang['Initial_config'] = '基本設定';
$lang['Default_lang'] = 'ギャラリーのデフォルト言語';
$lang['step1_title'] = 'データベース設定';
$lang['step2_title'] = '管理設定';
$lang['Start_Install'] = 'インストールを開始する';
$lang['reg_err_mail_address'] = 'メールアドレスは、 xxx@yyy.eee のような形式にしてください (例: jack@altern.org)。';

$lang['install_webmaster'] = 'ウェブマスタログイン';
$lang['install_webmaster_info'] = 'ウェブマスタは、ビジターに表示されます。ウェブサイト管理に必要です。';

$lang['step1_confirmation'] = 'パラメータに問題はありません。';
$lang['step1_err_db'] = 'サーバへ接続することができましたが、データベースに接続できません。';
$lang['step1_err_server'] = 'サーバに接続できません。';
$lang['step1_err_copy_2'] = 'インストールの次のステップへ進むことができます。';
$lang['step1_err_copy_next'] = '次のステップ';
$lang['step1_err_copy'] = 'ハイフンの間のピンクのテキストをコピーして、ファイル「include/mysql.inc.php」の中に貼り付けてください (警告 : mysql.inc.phpには、ピンクのテキストのみ貼り付けてください。改行またはスペースを含まないでください)。';

$lang['step1_host'] = 'MySQLホスト';
$lang['step1_host_info'] = '例) localhost、sql.multimania.com、toto.freesurf.fr';
$lang['step1_user'] = 'ユーザ';
$lang['step1_user_info'] = 'あなたのホストプロバイダから提供されたデータベースユーザ名です。';
$lang['step1_pass'] = 'パスワード';
$lang['step1_pass_info'] = 'あなたのホストプロバイダから提供されたデータベースパスワードです。';
$lang['step1_database'] = 'データベース名';
$lang['step1_database_info'] = 'こちらも、あなたのホストプロバイダから提供されたデータベース名です。';
$lang['step1_prefix'] = 'データベーステーブル接頭辞';
$lang['step1_prefix_info'] = 'データベーステーブルに接頭辞として付けられます (あなたのテーブルを管理しやすくします)。';
$lang['step2_err_login1'] = 'ウェブマスタのユーザIDを入力してください。';
$lang['step2_err_login3'] = 'ウェブマスタのユーザIDには、「\'」または「"」を含まないでください。';
$lang['step2_err_pass'] = 'もう一度あなたのパスワードを入力してください。';
$lang['install_end_title'] = 'インストールが終了しました。';
$lang['step2_pwd'] = 'ウェブマスタパスワード';
$lang['step2_pwd_info'] = 'ウェブマスタパスワードは、内密にしてください。ウェブマスタパスワードを使用して、あなたは管理パネルにアクセスすることができます。';
$lang['step2_pwd_conf'] = 'パスワード [もう一度]';
$lang['step2_pwd_conf_info'] = '確認';
$lang['install_help'] = 'ヘルプが必要ですか? <a href="%s">Piwigoメッセージボード</a>にて、あなたの質問を投稿してください。';
$lang['install_end_message'] = 'Piwigoの設定が完了しました。次のステップへ移動します。<br /><br />
* アイデンティフィケーションページにアクセスします : [ <a href="identification.php">アイデンティフィケーション</a> ] ウェブマスタに設定したユーザIDおよびパスワードを使用してください。<br />
* このログインにより、あなたは管理パネルにアクセスすることができます。また、あなたのディレクトリに写真をアップロードするためのインストラクションにもアクセスすることができます。';
$lang['conf_mail_webmaster'] = 'ウェブマスタメールアドレス';
$lang['conf_mail_webmaster_info'] = 'ビジターは、このメールアドレスでサイト管理者に連絡することができます。';
?>