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

$lang['Installation'] = 'Instalação';
$lang['Initial_config'] = 'Configuração Básica';
$lang['Default_lang'] = 'Idioma padrão da galeria';
$lang['step1_title'] = 'Configuração da base de dados';
$lang['step2_title'] = 'Configuração da administração';
$lang['Start_Install'] = 'Iniciar a instalação';
$lang['reg_err_mail_address'] = 'o endereço de email deve ser do tipo xxx@yyy.eee (exemplo : jack@altern.org)';

$lang['install_webmaster'] = 'Login do Webmaster';
$lang['install_webmaster_info'] = 'Será mostrado aos visitantes. É necessário para a administração do website';

$lang['step1_confirmation'] = 'Os parâmetros estão corretos';
$lang['step1_err_db'] = 'A conexão com o servidor foi bem sucedida, porém não foi possível se conectar à base de dados';
$lang['step1_err_server'] = 'Não foi possível se conectar ao servidor';
$lang['step1_err_copy_2'] = 'O próximo passo da instalação é possível agora';
$lang['step1_err_copy_next'] = 'próximo passo';
$lang['step1_err_copy'] = 'Copie o texto em rosa entre os hifens e cole-o no arquivo "include/mysql.inc.php"(Cuidado : mysql.inc.php deve conter apenas o que está em rosa, sem caracteres de espaço)';

$lang['step1_host'] = 'MySQL host';
$lang['step1_host_info'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['step1_user'] = 'Usuário';
$lang['step1_user_info'] = 'login de usuário fornecido pelo seu provedor de hospedagem';
$lang['step1_pass'] = 'Senha';
$lang['step1_pass_info'] = 'senha de usuário fornecida pelo seu provedor de hospedagem';
$lang['step1_database'] = 'Nome da base de dados';
$lang['step1_database_info'] = 'também forncido pelo seu provedor de hospedagem';
$lang['step1_prefix'] = 'Prefixo da tabela da base de dado';
$lang['step1_prefix_info'] = 'Os nomes das tabelas da base de dados terão este prefixo (isto possibilita que você gerencie melhor suas tabelas)';
$lang['step2_err_login1'] = 'entre um login para o webmaster';
$lang['step2_err_login3'] = 'o login do webmaster não pode conter os caracteres \' ou "';
$lang['step2_err_pass'] = 'por favor, entre com a sua senha novamente';
$lang['install_end_title'] = 'A instalação terminou';
$lang['step2_pwd'] = 'senha do Webmaster';
$lang['step2_pwd_info'] = 'mantenha-a bem guardada, é ela que lhe permite acessar o painel da administração';
$lang['step2_pwd_conf'] = 'Senha [confirmar]';
$lang['step2_pwd_conf_info'] = 'verificação';
$lang['install_help'] = 'Precisa de ajuda ? Faça a sua pergunta no <a href="%s">Piwigo message board</a>.';
$lang['install_end_message'] = 'A configuração do Piwigo acabou, aqui vai o próximo passo<br /><br />
* vá até a página de identificação e use o login/senha fornecido para webmaster<br />
* este login lhe permmitirá acessar o painel da administração e as instruções, para poder enviar as imagens nos seus diretórios';
$lang['conf_mail_webmaster'] = 'endereço de email do Webmaster';
$lang['conf_mail_webmaster_info'] = 'Os visitantes poderão entrar em contato com o administrador do site através desse email';

$lang['PHP 5 is required'] = 'É necessário PHP 5';
$lang['It appears your webhost is currently running PHP %s.'] = 'Parece que a sua hospedagem está atualmente usando PHP %s.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo pode tentar mudar sua configuração para PHP 5 através da criação ou modificação de um arquivo .htaccess.';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'Note que você pode alterar sua configuração por conta própria e reiniciar o Piwigo depois disso.';
$lang['Try to configure PHP 5'] = 'Tentar configurar PHP 5';
$lang['Sorry!'] = 'Lamento!';
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo não teve condições de configurar o PHP 5.';
$lang["You may referer to your hosting provider's support and see how you could switch to PHP 5 by yourself."] = "Você deve entrar em contato com o suporte do seu provedor e ver como você poderia mudar para PHP 5 por conta própria.";
$lang['Hope to see you back soon.'] = 'Espero te ver de volta em breve.';
?>