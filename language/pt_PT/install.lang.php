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

$lang['Installation'] = 'Instalação ';
$lang['Basic configuration'] = 'Configuração de base';
$lang['Default gallery language'] = 'Lingua por defeito da galeria';
$lang['Database configuration'] = 'Configuração da base de dados';
$lang['Admin configuration'] = 'Configuração da conta administrador';
$lang['Start Install'] = 'Iniciar a instalação';
$lang['mail address must be like xxx@yyy.eee (example : jack@altern.org)'] = 'O endresso email deve ser da forma xxx@yyy.eee (exemplo : jack@altern.org)';

$lang['Webmaster login'] = 'Administrador';
$lang['It will be shown to the visitors. It is necessary for website administration'] = 'Este identificante aparcerá a todos os visitantes.Vos servirá para administrar o sitio';

$lang['Parameters are correct'] = 'Os parâmetros entardos estão correctos';
$lang['Connection to server succeed, but it was impossible to connect to database'] = 'A conecção ao servidor esta OK, mas impossível de conectar-se a esta base de dados.';
$lang['Can\'t connect to server'] = 'Impossível de conectar-se ao servidor';

$lang['Host'] = 'Hóspede MySQL';
$lang['localhost, sql.multimania.com, toto.freesurf.fr'] = 'localhost, sql.multimania.com, toto.freesurf.fr';
$lang['User'] = 'Utilizador';
$lang['user login given by your host provider'] = 'nome de utilizador por seu Hóspede';
$lang['Password'] = 'Palavra passe';
$lang['user password given by your host provider'] = 'fornecido pelo seu Hóspede';
$lang['Database name'] = 'Nome da base';
$lang['also given by your host provider'] = 'fornecido pelo seu Hóspede';
$lang['Database table prefix'] = 'Prefixo dos nomes das tabelas';
$lang['database tables names will be prefixed with it (enables you to manage better your tables)'] = 'O nome das tabelas aparcerá com este prefixo.(Permite de gerir melhor a sua base de dados)';
$lang['enter a login for webmaster'] = 'Faça favor escolher um pseudónimo para o webmaster';
$lang['webmaster login can\'t contain characters \' or "'] = 'O pseudónimo do webmasterle nao deve conter os carácters " et \'';
$lang['please enter your password again'] = 'Faça favor escrever de novo a sua palavra passe';
$lang['Installation finished'] = 'Instalação acabada';
$lang['Webmaster password'] = 'Palavra passe';
$lang['Keep it confidential, it enables you to access administration panel'] = 'Deve ficar confidencial, permite de acedir ao panel de administração.';
$lang['Password [confirm]'] = 'Palavra passe [ Confirmar ]';
$lang['verification'] = 'Verificação';
$lang['Copy the text in pink between hyphens and paste it into the file "local/config/database.inc.php"(Warning : database.inc.php must only contain what is in pink, no line return or space character)'] = 'Copais o texto core de rosa entre os tracinhos e colais-o no ficheiro mysql.inc.php no repertorio "include" à raiz de onde instalou Piwigo (O ficheiro mysql.inc.php só deve compter o que é core de rose entre os tracinhos, nenhum retorno de linha ou esapço são autorizados)';
$lang['Need help ? Ask your question on <a href="%s">Piwigo message board</a>.'] = 'precisa de ajuda ? faça a sua pergunta <a href="%s">forum de Piwigo</a>.';
$lang['install_end_message'] = 'A configuração da aplicação correu bem, vamos a proxima fase<br /><br />
* Ide à página de indentificação e conecte-se com o pseudónimo escolhido para o webmaster<br />
* Perite-vos acedir à parte de administração e as instruções para meter as imagens nos repertorios.';
$lang['Webmaster mail address'] = 'Endresso Email do administrador';
$lang['Visitors will be able to contact site administrator with this mail'] = 'Os visitantes poderam contactar-vos pelo este Email';

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