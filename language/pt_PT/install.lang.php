<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2011 Piwigo Team                  http://piwigo.org |
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

$lang['Connection to server succeed, but it was impossible to connect to database'] = 'A ligação ao servidor foi bem sucedida, mas foi impossível ligar à base de dados';
$lang['Can\'t connect to server'] = 'Não foi possível ligar ao servidor';

$lang['Host'] = 'Servidor MySQL';
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['User'] = 'Utilizador';
$lang['user login given by your host provider'] = 'Login de utilizador fornecido pelo seu provedor de hospedagem';
$lang['Password'] = 'Palavra-passe';
$lang['user password given by your host provider'] = 'Palavra-passe fornecida pelo seu provedor de hospedagem';
$lang['Database name'] = 'Nome da base de dados';
$lang['also given by your host provider'] = 'Também fornecido pelo seu provedor de hospedagem';
$lang['Database table prefix'] = 'Prefixo da tabela da base de dados';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = 'Os nomes das tabelas da base de dados terão este prefixo (isto possibilita uma melhor gestão das tabelas)';
$lang['enter a login for webmaster'] = 'Introduza um nome para o webmaster';
$lang['webmaster login can\'t contain characters \' or "'] = 'o nome do webmaster não pode conter os caracteres \' ou "';
$lang['please enter your password again'] = 'Por favor, escreva a sua palavra-passe novamente';
$lang['Webmaster password'] = 'Palavra-passe do Webmaster';
$lang['Keep it confidential, it enables you to access administration panel'] = 'Mantenha-a bem guardada, é ela que lhe permite aceder ao painel administrativo';
$lang['Password [confirm]'] = 'Palavra-passe [confirmação]';
$lang['verification'] = 'Verificar Palavra-passe inserida';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = 'Precisa de ajuda ? Faça a sua pergunta no <a href="%s">Fórum Piwigo</a>.';
$lang['Webmaster mail address'] = 'Endereço de email do Webmaster';
$lang['Visitors will be able to contact site administrator with this mail'] = 'Os visitantes poderão entrar em contacto com o administrador da galeria através desse email';

$lang['PHP 5 is required'] = 'PHP 5 é necessário';
$lang['It appears your webhost is currently running PHP %s.'] = 'Parece que o seu provedor de hospedagem usa actualmente PHP %s.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo pode tentar mudar a sua configuração para PHP 5 através da criação ou modificação de um arquivo .htaccess.';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'Note que você pode alterar sua configuração por conta própria e reiniciar o Piwigo depois disso.';
$lang['Try to configure PHP 5'] = 'Tentar configurar PHP 5';
$lang['Sorry!'] = 'Lamento!';
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo não pôde configurar o PHP 5.';
$lang["You may referer to your hosting provider's support and see how you could switch to PHP 5 by yourself."] = "Você deve entrar em contacto com o suporte do seu provedor e saber como pode mudar para PHP 5.";
$lang['Hope to see you back soon.'] = 'Espero que volte em breve.';

$lang['Database type'] = 'Tipo de Base de Dados';
$lang['The type of database your piwigo data will be store in'] = 'O tipo de base de dados em que a sua informação Piwigo será guardada';
$lang['Congratulations, Piwigo installation is completed'] = 'Parabéns, a sua instalação Piwigo está completa';
$lang['An alternate solution is to copy the text in the box above and paste it into the file "local/config/database.inc.php" (Warning : database.inc.php must only contain what is in the textarea, no line return or space character)'] = 'Uma solução alternativa será copiar o texto na caixa abaixo e colá-lo no ficheiro "local/config/database.inc.php" (Atenção : database.inc.php apenas deverá conter o que está na área de texto, não insira nenhuma linha nova ou espaço em branco)';
$lang['Creation of config file local/config/database.inc.php failed.'] = 'A criação do ficheiro de configuração local/config/database.inc.php falhou.';
$lang['Download the config file'] = 'Guardar ficheiro de configuração';
$lang['You can download the config file and upload it to local/config directory of your installation.'] = 'Pode guardar o ficheiro de configuração no seu computador e de seguida enviá-lo para a pasta /local/config da sua instalação.';
$lang['SQLite and PostgreSQL are currently in experimental state.'] = 'SQLite e PostgreSQL ainda estão em fase experimental.';
$lang['Learn more'] = 'Saber mais';
?>