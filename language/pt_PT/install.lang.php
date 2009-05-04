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

$lang['Installation'] = 'Instalação ';
$lang['Initial_config'] = 'Configuração de base';
$lang['Default_lang'] = 'Lingua por defeito da galeria';
$lang['step1_title'] = 'Configuração da base de dados';
$lang['step2_title'] = 'Configuração da conta administrador';
$lang['Start_Install'] = 'Iniciar a instalação';
$lang['reg_err_mail_address'] = 'O endresso email deve ser da forma xxx@yyy.eee (exemplo : jack@altern.org)';

$lang['install_webmaster'] = 'Administrador';
$lang['install_webmaster_info'] = 'Este identificante aparcerá a todos os visitantes.Vos servirá para administrar o sitio';

$lang['step1_confirmation'] = 'Os parâmetros entardos estão correctos';
$lang['step1_err_db'] = 'A conecção ao servidor esta OK, mas impossível de conectar-se a esta base de dados.';
$lang['step1_err_server'] = 'Impossível de conectar-se ao servidor';

$lang['step1_host'] = 'Hóspede MySQL';
$lang['step1_host_info'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['step1_user'] = 'Utilizador';
$lang['step1_user_info'] = 'nome de utilizador por seu Hóspede';
$lang['step1_pass'] = 'Palavra passe';
$lang['step1_pass_info'] = 'fornecido pelo seu Hóspede';
$lang['step1_database'] = 'Nome da base';
$lang['step1_database_info'] = 'fornecido pelo seu Hóspede';
$lang['step1_prefix'] = 'Prefixo dos nomes das tabelas';
$lang['step1_prefix_info'] = 'O nome das tabelas aparcerá com este prefixo.(Permite de gerir melhor a sua base de dados)';
$lang['step2_err_login1'] = 'Faça favor escolher um pseudónimo para o webmaster';
$lang['step2_err_login3'] = 'O pseudónimo do webmasterle nao deve conter os carácters " et \'';
$lang['step2_err_pass'] = 'Faça favor escrever de novo a sua palavra passe';
$lang['install_end_title'] = 'Instalação acabada';
$lang['step2_pwd'] = 'Palavra passe';
$lang['step2_pwd_info'] = 'Deve ficar confidencial, permite de acedir ao panel de administração.';
$lang['step2_pwd_conf'] = 'Palavra passe [ Confirmar ]';
$lang['step2_pwd_conf_info'] = 'Verificação';
$lang['step1_err_copy'] = 'Copais o texto core de rosa entre os tracinhos e colais-o no ficheiro mysql.inc.php no repertorio "include" à raiz de onde instalou Piwigo (O ficheiro mysql.inc.php só deve compter o que é core de rose entre os tracinhos, nenhum retorno de linha ou esapço são autorizados)';
$lang['install_help'] = 'precisa de ajuda ? faça a sua pergunta <a href="%s">forum de Piwigo</a>.';
$lang['install_end_message'] = 'A configuração da aplicação correu bem, vamos a proxima fase<br /><br />
* Ide à página de indentificação e conecte-se com o pseudónimo escolhido para o webmaster<br />
* Perite-vos acedir à parte de administração e as instruções para meter as imagens nos repertorios.';
$lang['conf_mail_webmaster'] = 'Endresso Email do administrador';
$lang['conf_mail_webmaster_info'] = 'Os visitantes poderam contactar-vos pelo este Email';

$lang['PHP 5 is required'] = 'PHP 5 é requerido';
$lang['It appears your webhost is currently running PHP %s.'] = 'Aparentemente, a versão PHP do seu  Apparemment, a versão PHP do seu hospede é PHP %s.';
$lang['Piwigo may try to switch your configuration to PHP 5 by creating or modifying a .htaccess file.'] = 'Piwigo vai tentar passar em PHP 5 criando ou modificando o ficheiro .htaccess.';
$lang['Note you can change your configuration by yourself and restart Piwigo after that.'] = 'notei que pode modificar a configuração PHP e lançar de novo Pigiwo depois.';
$lang['Try to configure PHP 5'] = 'Tente configurar PHP 5Essayer de configurer PHP 5';
$lang['Sorry!'] = 'Infelizmente!';
$lang['Piwigo was not able to configure PHP 5.'] = 'Piwigo não pôde configurar PHP 5.';
$lang["You may referer to your hosting provider's support and see how you could switch to PHP 5 by yourself."] = 'Deve contactar seu hospede afins de saber como configurar PHP 5.';
$lang['Hope to see you back soon.'] = 'Eperando vê-lo de novo em breve...';
?>