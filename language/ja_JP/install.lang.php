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
$lang['Basic configuration'] = '基本設定';
$lang['Default gallery language'] = 'ギャラリーのデフォルト言語';
$lang['Database configuration'] = 'データベース設定';
$lang['Admin configuration'] = '管理設定';
$lang['Start Install'] = 'インストールを開始する';
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = 'メールアドレスは、 xxx@yyy.eee のような形式にしてください (例: jack@altern.org)。';

$lang['Webmaster login'] = 'ウェブマスタログイン';
$lang['It will be shown to the visitors. It is necessary for website administration'] = 'ウェブマスタは、ビジターに表示されます。ウェブサイト管理に必要です。';

$lang['Parameters are correct'] = 'パラメータに問題はありません。';
$lang['Connection to server succeed, but it was impossible to connect to database'] = 'サーバへ接続することができましたが、データベースに接続できません。';
$lang['Can\'t connect to server'] = 'サーバに接続できません。';
$lang['The next step of the installation is now possible'] = 'インストールの次のステップへ進むことができます。';
$lang['next step'] = '次のステップ';
$lang['Copy the text in pink between hyphens and paste it into the file "local/config/database.inc.php"(Warning : database.inc.php must only contain what is in pink, no line return or space character)'] = 'ハイフンの間のピンクのテキストをコピーして、ファイル「include/mysql.inc.php」の中に貼り付けてください (警告 : mysql.inc.phpには、ピンクのテキストのみ貼り付けてください。改行またはスペースを含まないでください)。';

$lang['Host'] = 'MySQLホスト';
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = '例) localhost、sql.multimania.com、toto.freesurf.fr';
$lang['User'] = 'ユーザ';
$lang['user login given by your host provider'] = 'あなたのホストプロバイダから提供されたデータベースユーザ名です。';
$lang['Password'] = 'パスワード';
$lang['user password given by your host provider'] = 'あなたのホストプロバイダから提供されたデータベースパスワードです。';
$lang['Database name'] = 'データベース名';
$lang['also given by your host provider'] = 'こちらも、あなたのホストプロバイダから提供されたデータベース名です。';
$lang['Database table prefix'] = 'データベーステーブル接頭辞';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = 'データベーステーブルに接頭辞として付けられます (あなたのテーブルを管理しやすくします)。';
$lang['enter a login for webmaster'] = 'ウェブマスタのユーザIDを入力してください。';
$lang['webmaster login can\'t contain characters \' or "'] = 'ウェブマスタのユーザIDには、「\'」または「"」を含まないでください。';
$lang['please enter your password again'] = 'もう一度あなたのパスワードを入力してください。';
$lang['Installation finished'] = 'インストールが終了しました。';
$lang['Webmaster password'] = 'ウェブマスタパスワード';
$lang['Keep it confidential, it enables you to access administration panel'] = 'ウェブマスタパスワードは、内密にしてください。ウェブマスタパスワードを使用して、あなたは管理パネルにアクセスすることができます。';
$lang['Password [confirm]'] = 'パスワード [もう一度]';
$lang['verification'] = '確認';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = 'ヘルプが必要ですか? <a href="%s">Piwigoメッセージボード</a>にて、あなたの質問を投稿してください。';
$lang['install_end_message'] = 'Piwigoの設定が完了しました。次のステップへ移動します。<br /><br />
* アイデンティフィケーションページにアクセスします : [ <a href="identification.php">アイデンティフィケーション</a> ] ウェブマスタに設定したユーザIDおよびパスワードを使用してください。<br />
* このログインにより、あなたは管理パネルにアクセスすることができます。また、あなたのディレクトリに写真をアップロードするためのインストラクションにもアクセスすることができます。';
$lang['Webmaster mail address'] = 'ウェブマスタメールアドレス';
$lang['Visitors will be able to contact site administrator with this mail'] = 'ビジターは、このメールアドレスでサイト管理者に連絡することができます。';
?>