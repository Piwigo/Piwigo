<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team                  http://piwigo.org |
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

$lang['Installation'] = 'インストール';
$lang['Basic configuration'] = '基本設定';
$lang['Default gallery language'] = 'ギャラリーのデフォルトの言語';
$lang['Database configuration'] = 'データベース設定';
$lang['Admin configuration'] = '管理設定';
$lang['Start Install'] = 'インストールを開始する';
$lang['It will be shown to the visitors. It is necessary for website administration'] = 'ゲスト一覧に表示されます。ウェブサイト管理に必要です。';
$lang['Connection to server succeed, but it was impossible to connect to database'] = 'サーバへ接続することができましたが、データベースに接続できません。';
$lang['Can\'t connect to server'] = 'サーバに接続できません。';
$lang['Host'] = 'ホスト';
$lang['User'] = 'ユーザー';
$lang['user login given by your host provider'] = 'あなたのホスティング業者から提供されたデータベースユーザ名です。';
$lang['user password given by your host provider'] = 'あなたのホスティング業者から提供されたデータベースパスワードです。';
$lang['Database name'] = 'データベース名';
$lang['also given by your host provider'] = 'こちらも、あなたのホスティング業者から提供されたデータベース名です。';
$lang['Database table prefix'] = 'データベーステーブル接頭辞';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = 'データベーステーブルに接頭辞として付けられます (あなたのテーブルを管理しやすくします)。';
$lang['enter a login for webmaster'] = 'ウェブマスタのユーザIDを入力してください。';
$lang['webmaster login can\'t contain characters \' or "'] = 'ウェブマスタのユーザIDには、「\'」または「"」を含まないでください。';
$lang['please enter your password again'] = 'もう一度あなたのパスワードを入力してください。';
$lang['Keep it confidential, it enables you to access administration panel'] = 'ウェブマスタパスワードは、秘密にしてください。ウェブマスタパスワードを使用して、管理パネルにアクセスできます。';
$lang['Password [confirm]'] = 'パスワード [もう一度]';
$lang['verification'] = '確認';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = 'ヘルプが必要ですか? <a href="%s">Piwigoメッセージボード</a>にて、あなたの質問を投稿してください。';
$lang['Visitors will be able to contact site administrator with this mail'] = 'ゲストは、このメールアドレスでサイト管理者に連絡することができます。';
$lang['PHP 5 is required'] = 'PHP 5.2が必要です';
$lang['It appears your webhost is currently running PHP %s.'] = 'あなたのウェブホストは現在PHP %sを使っています。';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigoは.htaccess ファイルを作成するか変更して、PHP 5.2を設定してみます。';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = '注：自分で設定を変更し、その後Piwigoを再起動もできます。';
$lang['Try to configure PHP 5'] = 'PHP 5.2を設定してみます。';
$lang['Sorry!'] = '申し訳ありません!';
$lang['Piwigo was not able to configure PHP 5.'] = 'PiwigoはPHP 5.2を設定できませんでした。';
$lang['You may referer to your hosting provider\'s support and see how you could switch to PHP 5 by yourself.'] = 'あなたのホスティング業者のサポートに連絡し、どうやったらPHP 5.2を設定できるか確認すべきです。';
$lang['Hope to see you back soon.'] = 'またお越し下さい';
$lang['Congratulations, Piwigo installation is completed'] = 'おめでとうございました。Piwigo のインストールが完了しました。';
$lang['An alternate solution is to copy the text in the box above and paste it into the file "local/config/database.inc.php" (Warning : database.inc.php must only contain what is in the textarea, no line return or space character)'] = 'その他の解決は、上のボックスにあるテクストをコピーし、"local/config/database.inc.php"に貼り付けます。（注意：database.inc.php は上のテクスト内容以外のこと（エンターキーやスペースなど）を含まないでください。)';
$lang['Creation of config file local/config/database.inc.php failed.'] = 'local/config/database.inc.php の設定ファイル作成に失敗しました。';
$lang['Download the config file'] = '設定ファイルをダウンロードします。';
$lang['You can download the config file and upload it to local/config directory of your installation.'] = '設定ファイルをダウンロードし、インストールされた local/config ディレクトリーにアップロードすることができます。';
$lang['Don\'t hesitate to consult our forums for any help : %s'] = 'どうぞ、フォーラムで相談するのをためらわないでください: %s';
$lang['Just another Piwigo gallery'] = '他のPiwigoギャラリー';
$lang['Welcome to your new installation of Piwigo!'] = '新しくPiwigoをインストールしていただき、ありがとうざいます!';
$lang['Welcome to my photo gallery'] = '私のフォトギャラリーへようこそ';
$lang['localhost or other, supplied by your host provider'] = 'localhost または、他のホスト、あるいは、あなたのホスティング業者から提供されたホスト名です。';