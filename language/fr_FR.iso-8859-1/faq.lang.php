<?php
// +-----------------------------------------------------------------------+
// |                           fr_FR/faq.lang.php                           |
// +-----------------------------------------------------------------------+
// | application   : PhpWebGallery <http://phpwebgallery.net>              |
// | branch        : BSF (Best So Far)                                                 |
// +-----------------------------------------------------------------------+
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
$lang['help_images_title'] = 'Ajout d\'images';
$lang['help_images_intro'] = 'Voici comment il faut placer les images dans vos répertoires';
$lang['help_images'][0] = 'dans le répertoire "galleries" placer des repertoires qui vont représenter vos futures catégories';
$lang['help_images'][1] = 'dans chacun de ces répertoires, vous avez le droit de créer autant de niveau de sous-répertoire que vous le souhaitez';
$lang['help_images'][2] = 'vous avez le droit à un nombre illimité de catégories et de sous catégories pour chaque catégorie';	
$lang['help_images'][3] = 'les fichiers images doivent être au format jpg (extension jpg ou JPG), gif (GIF ou gif) ou encore png (PNG ou png)';
$lang['help_images'][4] = 'Evitez d\'utiliser des espaces " " ou des tirets "-" dans les noms de fichiers ou de catégorie, je conseille d\'utiliser le caractère underscore "_" qui est géré par l\'application et donnera des résultats plus appréciables';
$lang['help_thumbnails_title'] = 'Miniatures';
$lang['help_thumbnails'][0] = 'dans chaque répertoire contenant des images à afficher sur le site, il y a un sous-répertoire nommé "thumbnail", s\'il n\'existe pas, créez-le pour placer vos miniatures dedans';
$lang['help_thumbnails'][1] = 'les miniatures n\'ont pas besoin d\'avoir la même extension que les images associées (une image en .jpg peut avoir sa miniature en .GIF par exemple)';
$lang['help_thumbnails'][2] = 'la miniature associée à une image doit être préfixée par le préfixe donné sur la page de configuration générale (image.jpg -> TN_image.GIF par exemple).';
$lang['help_thumbnails'][3] = 'il est conseillé d\'utiliser le module pour windows téléchargeable sur le site de PhpWebGallery pour la création des miniatures.';
$lang['help_thumbnails'][4] = 'vous pouvez utilisez la gestion de création de miniatures, intégrée à PhpWebGallery, mais ce n\'est pas conseillé, car la qualité des miniatures sera moindre qu\'avec un véritable outil de manipulation d\'images et que cela consommera des ressources sur le serveur, ce qui peut se révéler gênant pour un hébergement gratuit.';
$lang['help_thumbnails'][5] = 'si vous choisissez d\'utiliser votre hébergeur pour créer les miniatures, il faut avant cela passer le répertoire "galleries" en 775 ainsi que tous ses sous-répertoires.';
$lang['help_database_title'] = 'Remplissage de la base de données';
$lang['help_database'][0] = 'Une fois les fichiers placés correctement et les miniatures placées ou créées, cliquez sur "MaJ base d\'images" dans le menu de la zone d\'administration.';
$lang['help_remote_title'] = 'Site distant';
$lang['help_remote'][0] = 'PhpWebGallery offre la possibilité d\'utiliser plusieurs serveurs pour stocker les images qui composeront votre galerie. Cela peut être utile si votre galerie est installée sur une espace limité et que vous avez une grande quantité d\'images à montrer. Suivez la procédure suivante :';
$lang['help_remote'][1] = '1. éditez le fichier "create_listing_file.php" (vous le trouverez dans le répertoire "admin"), en modifiant la ligne "$prefix_thumbnail = "TN-";" si le préfixe pour vos miniatures n\'est pas "TN-".';
$lang['help_remote'][2] = '2. placez le fichier "create_listing_file.php" modifié sur votre site distant, dans le répertoire racine de vos répertoires d\'images (comme le répertoire "galleries" du présent site) par ftp.';
$lang['help_remote'][3] = '3. lancez le script en allant à l\'url http://domaineDistant/repGalerie/create_listing_file.php, un fichier listing.xml vient de se créer.';
$lang['help_remote'][4] = '4. récupérez le fichier listing.xml de votre site distant pour le placer dans le répertoire "admin" du présent site.';
$lang['help_remote'][5] = '5. lancez une mise à jour de la base d\'images par l\'interface d\'administration, une fois le fichier listing.xml utilisé, supprimez le du répertoire "admin".';
$lang['help_remote'][6] = 'Vous pouvez mettre à jour le contenu d\'un site distant en refaisant la manipulation décrite. Vous pouvez également supprimer un site distant en choisissant l\'option dans la section configuration du panneau d\'administration.';
$lang['help_upload_title'] = 'Ajout d\'images par les utilisateurs';
$lang['help_upload'][0] = 'PhpWebGallery offre la possibilité aux visiteurs d\'uploader des images. Pour cela :';
$lang['help_upload'][1] = '1. autorisez l\'option dans la zone configuration du panneau d\'administration';
$lang['help_upload'][2] = '2. autorisez les droits en écriture sur les répertoires d\'images';
$lang['help_infos_title'] = 'Informations complémentaires';
$lang['help_infos'][1] = 'Dès que vous avez créé votre galerie, allez dans la gestion des utilisateurs et modifiez les permissions pour l\'utilisateur visiteur. En effet, tous les utilisateurs qui s\'enregistrent eux-même auront par défaut les mêmes permissions que l\'utilisateur "visiteur".';
$lang['help_database'][1] = 'Afin d\'éviter la mise à jour d\'un trop grand nombre d\'images, commencez par mettre à jour uniquement les catégories, puis sur la page des catégories, mettre à jour chaque catégorie individuellement grâce au lien "mise à jour"';
$lang['help_upload'][3] = 'La catégorie doit elle-même être autorisée pour l\'upload.';
$lang['help_upload'][4] = 'Les images uploadées par les visiteurs ne sont pas directement visibles sur le site, elles doivent être validées par un administrateur. Pour cela, un administrateur doit se rendre sur la page "en attente" du panneau d\'administration, valider ou refuser les images proposée, puis lancer une mise à jour de la base d\'images.';
$lang['help_virtual_title'] = 'Liens images vers catégories et catégories virtuelles';
$lang['help_virtual'][0] = 'PhpWebGallery permet de dissocier les catégories où sont stockées les images et les catégories où les images apparaissent.';
$lang['help_virtual'][1] = 'Par défaut, les images apparaissent uniquement dans leurs catégories réelles : celles qui correspondent à des répertoires sur le serveur web.';
$lang['help_virtual'][2] = 'Pour lier une image à une catégorie, il suffit de l\'y associer via la page de modification d\'une image ou par lot sur la page de modification des images d\'une catégorie.';
$lang['help_virtual'][3] = 'En partant de ce principe, il est possible de créer des catégories virtuelles dans PhpWebGallery : aucun répertoire "réel" n\'y est rattaché sur le disque du serveur. Il suffit simplement de créer la catégorie sur la page de la liste des catégories existantes dans la zone d\'administration.';
$lang['help_groups_title'] = 'Groupes d\'utilisateurs';
$lang['help_groups'][0] = 'PhpWebGallery permet de gérer des groupes d\'utilisateurs, cela est très utile pour regrouper les autorisations d\'accès aux catégories privées.';
$lang['help_groups'][1] = '1. Créez un groupe "famille" sur la page des groupes de la zone d\'administration.';
$lang['help_groups'][2] = '2. Sur la page de la liste des utilisateurs, en éditer un, et l\'associer au groupe "famille".';
$lang['help_groups'][3] = '3. En modifiant les permissions pour une catégorie, ou pour un groupe, vous verrez que toutes les catégories autorisées à un groupe le sont pour les membres de ce groupe.';
$lang['help_groups'][4] = 'Un utilisateurs peut appartenir à plusieurs groupes. L\'autorisation est plus forte que l\'interdiction : si l\'utilisateur "paul" appartient au groupe "famille" et "amis", et que seule le groupe "famille" est autorisée à consulter la catégorie privée "Noël 2003", alors "paul" y aura accès.';
$lang['help_access_title'] = 'Autorisations d\'accès';
$lang['help_access'][0] = 'PhpWebGallery dispose d\'un système de restrictions d\'accès aux catégories souhaitées. Les catégories sont soit publiques, soit privées. Pour interdire l\'accès par défaut à une catégorie :';
$lang['help_access'][1] = '1. Editez la catégorie (depuis la page des catégories dans la zone d\'administration) et rendez la "privée".';
$lang['help_access'][2] = '2. Sur les pages des permissions (d\'un groupe, d\'utilisateur) la catégorie apparaîtra et vous pourrez autoriser l\'accès ou non.';
$lang['help_infos'][2] = 'Pour n\'importe quelle question, n\'hésitez pas à consulter le <a href="'.PHPWG_FORUM_URL.'" style="text-decoration:underline">forum</a> ou à y poser une question, sur le site';
?>