<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2005 PhpWebGallery Team - http://phpwebgallery.net |
// +-----------------------------------------------------------------------+
// | branch        : BSF (Best So Far)
// | file          : $RCSfile$
// | last update   : $Date$
// | last modifier : $Author$
// | revision      : $Revision$
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

// Admin FAQ
$lang['help_images_title'] = 'Ajouts d\'éléments';
$lang['help_images'] =
array(
  'Les répertoires représentant les catégories sont dans le répertoire
"galleries". Ci-dessous l\'arbre des répertoires d\'une très petite galerie
(mais utilisant de nombreuses fonctionnalités) : <br />
<pre>
.
|-- admin
|-- doc
|-- galleries
|   |-- categorie-1
|   |   |-- categorie-1.1
|   |   |   |-- categorie-1.1.1
|   |   |   |   |-- categorie-1.1.1.1
|   |   |   |   |   |-- pwg_high
|   |   |   |   |   |   +-- mariage.jpg
|   |   |   |   |   |-- thumbnail
|   |   |   |   |   |   +-- TN-mariage.jpg
|   |   |   |   |   +-- mariage.jpg
|   |   |   |   +-- categorie-1.1.1.2
|   |   |   +-- categorie-1.1.2
|   |   |-- categorie-1.2
|   |   |   |-- pookie.jpg
|   |   |   +-- thumbnail
|   |   |       +-- TN-pookie.jpg
|   |   +-- categorie-1.3
|   +-- categorie-2
|       |-- porcinet.gif
|       |-- pwg_representative
|       |   +-- video.jpg
|       |-- thumbnail
|       |   +-- TN-porcinet.jpg
|       +-- video.avi
|-- include
|-- install
|-- language
|-- template
+-- tool
</pre>',

  'Fondamentalement, une catégorie est représentée par un répertoire à
n\'importe quel niveau sous le répertoire "galleries" de votre installation de
PhpWebGallery. Chaque catégorie peut contenir autant de sous-niveaux que
désiré. Dans l\'exemple ci-dessus, categorie-1.1.1.1 est à un niveau 4 de
profondeur.',

  'Fondamentalement, un élément est représenté par un fichier. Un fichier peut
être un élément pour PhpWebGallery si l\'extension du nom du fichier est parmi
la liste $conf[\'file_ext\'] (voir fichier include/config.inc.php). Un fichier
peut être une image si son extension est parmi $conf[\'picture_ext\'] (voir
fichier include/config.inc.php).',

  'Les éléments de type image doivent avoir une miniature associée (voir la
section suivante à propos des miniatures).',

  'Les éléments de type image peuvent avoir un image en grand format associé.
Comme pour le fichier mariage.jpg dans l\'exemple ci-dessus. Aucun préfix
n\'est nécessaire sur le nom du fichier.',

  'Les éléments non image (vidéos, sons, fichiers texte, tout ce que vous
voulez...) sont par défaut représentés par un icône correspondant à
l\'extension du nom du fichier. Optionnellement, une miniature et un
représentant peuvent être associés (voir le fichier video.avi dans
l\'exemple)',

  'Attention : le nom d\'un répertoire ou d\'un fichier ne doit être composé
que de lettres, de chiffres, de "-", "_" ou ".". Pas d\'espace ou de
caractères accentués.',

  'Conseil : une catégorie peut contenir des éléments et des sous-catégories à
la fois. Néanmoins, il est fortement conseillé pour chaque catégorie de choisir
entre contenir des éléments OU BIEN des sous-catégories.',
  );

$lang['help_thumbnails_title'] = 'Miniatures';
$lang['help_thumbnails'] =
array(
  'Comme mentionné précédemment, chaque élément de type image doit être
associé à une miniature.',

  'Les miniatures sont stockées dans le sous-répertoire "thumbnail" de chaque
répertoire représentant une catégorie. Une miniature est un fichier de type
image (même extension du nom du fichier) dont le nom de fichier est préfixé par
le paramètre "Préfixe miniature" (voir zone administration, Configuration,
Général)',

  'Les miniatures n\'ont pas besoin d\'avoir la même extension que leur image
associée (une image avec ".jpg" comme extension peut avoir une miniature en
".GIF" par exemple).',

  'Il est conseillé d\'utiliser un outil externe pour la création des
miniatures (comme ThumbClic ou PhpMyVignettes, voir le site de présentation
de PhpWebGallery).',

  'Vous pouvez également utiliser l\'outil de création de miniature intégré à
PhpWebGallery mais cela est déconseillé car la qualité risque d\'être décevante
et cela utilise inutilement les ressources du serveur (ce qui peut être un
grave problème sur un serveur mutualisé).',

  'Si vous choisissez d\'utiliser le serveur web pour générer les miniatures,
vous devez donner les droits en écriture sur tous les répertoires représentant
les catégories pour tous les utilisateurs (propriétaire, groupe, autre)'
  );

$lang['help_database_title'] =
'Synchroniser le système de fichiers et la base';
$lang['help_database'] =
array(
  'Une fois que les fichiers, miniatures, représentants ont été correctement
placés dans les répertoires, se rendre sur : zone administration, Général,
Synchroniser',

  'Il existe 2 types de synchronisations : structure et meta-données.
Synchroniser la structure revient à synchroniser votre arbre des répertoires
et fichiers avec la représentation de la structure dans la base de données.
Synchroniser les méta-données permet de mettre à jour les informations comme
le poids du fichier, les dimensions, les données EXIF ou IPTC.',

  'La première synchronisation à effectuer doit être celle sur la structure.',

  'Le processus de synchronisation peut prendre du temps (en fonction de la
charge du serveur et de la quantité de fichiers à gérer), il est donc
possible d\'avancer pas à pas : catégorie par catégorie.'
  
  );

$lang['help_access_title'] = 'Autorisations';
$lang['help_access'] =
array(
  'Vous pouvez interdire l\'accès aux catégories. Les catégories peuvent être
publiques ou privées. Les autorisations (valables pour les groupes et les
utilisateurs) sont gérables uniquement pour les catégories privées.',
  
  'Vous pouvez rendre une catégorie privée en l\'éditant (zone administration,
Catégories, Gestion, Editer) ou en gérant les options pour votre arbre complet
des catégories (zone administration, Catégories, Sécurité)',

  'Une fois que certaines catégories sont privées, vous pouvez gérer les
autorisations pour les groupes et les utilisateurs (zone administration,
Autorisations).'
  );

$lang['help_groups_title'] = 'Groupes d\'utilisateurs';
$lang['help_groups'] =
array(

  'PhpWebGallery peut gérer des groupes d\'utilisateurs. Très pratique pour
gérer des autorisations communes sur les catégories privées.',

  'Vous pouvez créer des groupes et y ajouter des utilisateurs dans la zone
administration, Identification, Groupes',

  'Un utilisateur peut appartenir à plusieurs groupes. L\'autorisation est
plus forte que l\'interdiction : si l\'utilisateur "pierre" appartient aux
groupes "famille" et "amis", et que seul le groupe "famille" peut visiter la
catégorie "Noël 2003", alors "pierre" peut visiter cette catégorie.'
  
  );

$lang['help_remote_title'] = 'Sites distant';
$lang['help_remote'] =
array(

  'PhpWebGallery offre la possibilité d\'utiliser plusieurs sites pour
stocker les fichiers qui composeront votre galerie. Cela peut être utile si
votre galerie est installée sur un espace de stockage limité et que vous avez
de nombreux fichiers à montrer.',

  '1. éditer le fichier tools/create_listing_file.php en modifiant la section
des paramètres comme $conf[\'prefix_thumbnail\'] ou $conf[\'use_exif\'].',

  '2. placer le fichier "tools/create_listing_file.php" modifié sur votre
site distant, dans le même répertoire que les répertoires représentant vos
catégories (comme le répertoire "galleries" de ce site) par FTP. Par exemple,
disons que vous pouvez accéder à
http://exemple.com/galleries/create_listing_file.php.',

  '3. zone administration, Général, Sites distant. Demander à créer un nouveau
site, par exemple http://exemple.com/galleries',

  '4. un nouveau site distant est enregistré. 4 actions possibles :

<ol>

  <li>générer la liste : lance une requête distant pour générer le fichier
  de listing distant</li>

  <li>mettre à jour : lit le fichier distant listing.xml et synchronise avec
  la base de données locale</li>

  <li>nettoyer : supprime le fichier distant de listing</li>

  <li>détruire : supprime le site (et tous les éléments qui y sont associés)
  dans la base de données</li>

</ol>',

  'Vous pouvez également effectuer ces opérations manuellement en éditant le
fichier listing.xml à la main et en le déplaçant vers votre répertoire
racine. Se rendre sur zone administration, Général, Sites distant :
PhpWebGallery détecte le fichier et propose de s\'en servir.'
  
  );

$lang['help_upload_title'] = 'Ajout de fichiers par les utilisateurs';
$lang['help_upload'] =
array(
  'Pour permettre aux utilisateurs d\'ajouter des fichiers :',

  '1. autoriser l\'ajout d\'images sur n\'importe quelle catégorie (zone
administation, Catégories, Gestion, Edit ou zone administration, Catégories,
Ajout d\'images)',

  '2. donner les droits en écriture (pour tous les utilisateurs) sur les
répertoires correspondant aux catégories qui sont autorisées à l\'ajout',

  'Les fichiers ajoutés par les utilisateurs ne sont pas directement visibles
sur le site, ils doivent être validés par un administrateur. Pour cela, un
administrateur doit se rendre dans zone administration, Images, En attente
afin de valider ou rejeter les fichiers proposés. Il est ensuite nécessaire
de synchroniser le système de fichier avec la base de données.'
  );

$lang['help_virtual_title'] = 'Liens entre les éléments et les catégories, catégories virtuelles';
$lang['help_virtual'] =
array(
  'PhpWebGallery dissocie les catégories qui stockent les éléments et les
catégories où les éléments sont montrés.',

  'Par défaut, les élement ne sont montrés que dans leurs catégories réelles :
celles qui correspondent à leurs répertoires sur le serveur.',

  'Pour lier un élément à une catégorie, il suffit de faire une association sur
la page d\'édition de l\'élément (un lien existe vers cette page lorsque
vous êtes connecté en tant qu\'administrateur) ou sur la page regroupant les
informations sur tous les éléments d\'une catégorie.',

  'En partant de ce principe, il est possible de créer des catégories
virtuelles : aucun répertoire ne correspond à ces catégories. Vous pouvez
créer des catégories virtuelle sur zone administration, Catégorie, Gestion.'
  );

$lang['help_infos_title'] = 'Informations diverses';
$lang['help_infos'] =
array(
  'Dès que vous aurez créer votre galerie, configurez l\'affichage par défaut
tel que désiré dans zone administation, Configuration, Affichage par
défaut. En effet, chaque nouvel utilisateur héritera de ces propriétés
d\'affichage.',

  'Pour tout question, n\'hésitez pas à visiter le forum ou à y poser une
question si votre recherche est infructueuse. Le <a
href="http://forum.phpwebgallery.net"
style="text-decoration:underline">forum</a> est disponible sur le site de
PhpWebGallery. Consulter également la <a href="http://doc.phpwebgallery.net"
style="text-decoration:underline">documentation officielle de
PhpWebGallery</a> pour obtenir plus de détails.'
  );
?>