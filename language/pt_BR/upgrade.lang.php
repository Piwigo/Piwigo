<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2009 Piwigo Team                  http://piwigo.org |
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

$lang['Upgrade'] = 'Upgrade';
$lang['introduction message'] = 'Esta página se propõe a fazer um upgrade em sua base de dados em relação a sua versão antiga do Piwigo para a versão atual.
O assistente de upgrade acha que você está atualmente rodando a<strong>versão %s</strong> (ou equivalente).';
$lang['Upgrade from %s to %s'] = 'Atualizar da versão %s para a %s';
$lang['Statistics'] = 'Estatísticas';
$lang['total upgrade time'] = 'tempo total do upgrade';
$lang['total SQL time'] = 'tempo total do SQL';
$lang['SQL queries'] = 'Consultas SQL';
$lang['Upgrade informations'] = 'Informações do upgrade';
$lang['perform a maintenance check'] = 'Executa uma avaliação de rotina em [Administração>Especiais>Manutenção] se você encontrar algum problema.';
$lang['deactivated plugins'] = 'Por precaução, os seguintes plugins foram desativados. Você deve checar por atualizações dos plugins antes de reativá-los:';
$lang['upgrade login message'] = 'Apenas administrador pode realizar o upgrade: por favor, faça o login logo abaixo.';
$lang['You do not have access rights to run upgrade'] = 'Você não tem permissões de acesso para realizar o upgrade';
$lang['in include/mysql.inc.php, before ?>, insert:'] = 'Em <i>include/mysql.inc.php</i>, antes de <b>?></b>, insira:';

// Upgrade informations from upgrade_1.3.1.php
$lang['all sub-categories of private categories become private'] = 'All sub-categories of private categories become private';
$lang['user permissions and group permissions have been erased'] = 'Permissões de usuário e de grupo foram apagadas.';
$lang['only thumbnails prefix and webmaster mail saved'] = 'Apenas o prefixo das miniaturas (thumbnails) e o endereço de email do webmaster foram salvos da configuração anterior.';

?>