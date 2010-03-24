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

$lang['Upgrade'] = 'アップグレード';
$lang['introduction message'] = 'あなたの古いバージョンのPiwigoから最新バージョンへアップグレードします。
あなたは、現在 <strong>リリース %s</strong> (または同等のバージョン) を使用しています。';
$lang['Upgrade from version %s to %s'] = 'バージョン %s から %s にアップグレードする';
$lang['Statistics'] = '統計';
$lang['total upgrade time'] = '合計アップグレード時間';
$lang['total SQL time'] = '合計SQL時間';
$lang['SQL queries'] = 'SQLクエリー';
$lang['Upgrade informations'] = '更新情報';
$lang['Perform a maintenance check in [Administration>Specials>Maintenance] if you encounter any problem.'] = '問題がある場合、[管理 > 特別 > メンテナンス] でメンテナンスチェックを実行してください。';
$lang['As a precaution, following plugins have been deactivated. You must check for plugins upgrade before reactiving them:'] = '事前チェックで以下のプラグインが検出されました。再度有効にする前にプラグインのアップグレードを確認してください:';
$lang['Only administrator can run upgrade: please sign in below.'] = '管理者のみアップグレードを実行できます: 以下でログインしてください。';
$lang['You do not have access rights to run upgrade'] = 'あなたには、アップグレードを実行する権限がありません。';
$lang['in include/mysql.inc.php, before ?>, insert:'] = '<i>include/mysql.inc.php</i>内にある<b>?></b>の前に次の行を挿入してください:';

// Upgrade informations from upgrade_1.3.1.php
$lang['All sub-categories of private categories become private'] = 'プライベートカテゴリのすべてのサブカテゴリがプライベートにされました。';
$lang['User permissions and group permissions have been erased'] = 'ユーザパーミッションおよびグループパーミッションが削除されました。';
$lang['Only thumbnails prefix and webmaster mail address have been saved from previous configuration'] = '以前の設定より、サムネイル接頭辞およびウェブマスターのメールアドレスのみ保存されました。';

?>