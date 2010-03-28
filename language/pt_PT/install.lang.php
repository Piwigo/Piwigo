<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
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

$lang['Installation'] = 'Instalação';
$lang['Basic configuration'] = 'Configuração Básica';
$lang['Default gallery language'] = 'Idioma padrão da galeria';
$lang['Database configuration'] = 'Configuração da base de dados';
$lang['Admin configuration'] = 'Configuração da administração';
$lang['Start Install'] = 'Iniciar a instalação';
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = 'o endereço de email deve ser do tipo xxx@yyy.eee (exemplo : jack@altern.org)';

$lang['Webmaster login'] = 'Login do Webmaster';
$lang['It will be shown to the visitors. It is necessary for website administration'] = 'Será mostrado aos visitantes. É necessário para a administração do website';

$lang['Parameters are correct'] = 'Os parâmetros estão corretos';
$lang['Connection to server succeed, but it was impossible to connect to database'] = 'A conexão com o servidor sucedeu, porém não foi possível se conectar à base de dados';
$lang['Can\'t connect to server'] = 'Não foi possível se conectar ao servidor';
$lang['The next step of the installation is now possible'] = 'O próximo passo da instalação é possível agora';
$lang['next step'] = 'próximo passo';
$lang['Copy the text in pink between hyphens and paste it into the file "local/config/database.inc.php"(Warning : database.inc.php must only contain what is in pink, no line return or space character)'] = 'Copie o texto em rosa entre os hifens e cole-o no arquivo "include/mysql.inc.php"(Cuidado : mysql.inc.php deve conter apenas o que está em rosa, sem caracteres de espaço)';

$lang['Host'] = 'MySQL host';
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['User'] = 'Usuário';
$lang['user login given by your host provider'] = 'login de usuário fornecido pelo seu provedor de hospedagem';
$lang['Password'] = 'Palavra-passe';
$lang['user password given by your host provider'] = 'Palavra-passe fornecida pelo seu provedor de hospedagem';
$lang['Database name'] = 'Nome da base de dados';
$lang['also given by your host provider'] = 'também forncido pelo seu provedor de hospedagem';
$lang['Database table prefix'] = 'Prefixo da tabela da base de dados';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = 'Os nomes das tabelas da base de dados terão este prefixo (isto possibilita que você gerencie melhor suas tabelas)';
$lang['enter a login for webmaster'] = 'entre um login para o webmaster';
$lang['webmaster login can\'t contain characters \' or "'] = 'o login do webmaster não pode conter os caracteres \' ou "';
$lang['please enter your password again'] = 'por favor, entre com a sua palavra-passe novamente';
$lang['Installation finished'] = 'A instalação terminou';
$lang['Webmaster password'] = 'Palavra-passe do Webmaster';
$lang['Keep it confidential, it enables you to access administration panel'] = 'mantenha-a bem guardada, é ela que lhe permite acessar o painel da administração';
$lang['Password [confirm]'] = 'Palavra-passe [confirmar]';
$lang['verification'] = 'verificação';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = 'Precisa de ajuda ? Faça a sua pergunta no <a href="%s">Piwigo message board</a>.';
$lang['install_end_message'] = 'A configuração do Piwigo acabou, aqui vai o próximo passo<br /><br />
* vá até a página de identificação e use o login/palavra-passe fornecidos para webmaster<br />
* este login lhe permmitirá acessar o painel da administração e as instruções, para poder enviar as imagens nos seus diretórios';
$lang['Webmaster mail address'] = 'endereço de email do Webmaster';
$lang['Visitors will be able to contact site administrator with this mail'] = 'Os visitantes poderão entrar em contato com o administrador do site através desse email';

$lang['PHP 5 is required'] = 'É necessário PHP 5';
$lang['It appears your webhost is currently running PHP %s.'] = 'Parece que a sua hospedagem usa atualmente PHP %s.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo pode tentar mudar sua configuração para PHP 5 através da criação ou modificação de um arquivo .htaccess.';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'Note que você pode alterar sua configuração por conta própria e reiniciar o Piwigo depois disso.';
$lang['Try to configure PHP 5'] = 'Tentar configurar PHP 5';
$lang['Sorry!'] = 'Lamento!';
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo não pôde configurar o PHP 5.';
$lang["You may referer to your hosting provider's support and see how you could switch to PHP 5 by yourself."] = "Você deve entrar em contato com o suporte do seu provedor e ver como você poderia mudar para PHP 5 por conta própria.";
$lang['Hope to see you back soon.'] = 'Espero te ver de volta em breve.';
?>