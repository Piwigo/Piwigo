<?php
// +-----------------------------------------------------------------------+
// | PhpWebGallery - a PHP based picture gallery                           |
// | Copyright (C) 2002-2003 Pierrick LE GALL - pierrick@phpwebgallery.net |
// | Copyright (C) 2003-2004 PhpWebGallery Team - http://phpwebgallery.net |
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

// Main words
$lang['links'] = 'Liens';
$lang['general'] = 'Général';
$lang['config'] = 'Configuration';
$lang['users'] = 'Utilisateurs';
$lang['instructions'] = 'Instructions';
$lang['history'] = 'Historique';
$lang['manage'] = 'Gestion';
$lang['waiting'] = 'En attente';
$lang['access'] = 'Accès';
$lang['groups'] = 'Groupes';
$lang['permissions'] = 'Autorisations';
$lang['update'] = 'Synchroniser';
$lang['edit'] = 'Editer';
$lang['authorized'] = 'Autorisé';
$lang['forbidden'] = 'Interdit';
$lang['free'] = 'Libre';
$lang['restricted'] = 'Restreint';
$lang['metadata']='Métadonnées';
$lang['visitors'] = 'Visiteurs';
$lang['storage'] = 'Répertoire';
$lang['lock'] = 'Verrouiller';
$lang['unlock'] = 'Déverrouiller';
$lang['up'] = 'Monter';
$lang['down'] = 'Descendre';

// Specific words
$lang['phpinfos'] = 'Informations PHP';
$lang['remote_site'] = 'Site distant';
$lang['remote_sites'] = 'Sites distant';
$lang['gallery_default'] = 'Options par défaut';
$lang['upload'] = 'Ajout d\'images';

// Remote sites management
$lang['remote_site_create'] = 'Créer un nouveau site distant : (give its URL to generate_file_listing.php)';
$lang['remote_site_uncorrect_url'] = 'Remote site url must start by http or https and must only contain characters among "/", "a-zA-Z0-9", "-" or "_"';
$lang['remote_site_already_exists'] = 'Ce site existe déjà';
$lang['remote_site_generate'] = 'générer la liste';
$lang['remote_site_generate_hint'] = 'generate file listing.xml on remote site';
$lang['remote_site_update'] = 'update';
$lang['remote_site_update_hint'] = 'read remote listing.xml and updates database';
$lang['remote_site_clean'] = 'clean';
$lang['remote_site_clean_hint'] = 'remove remote listing.xml file';
$lang['remote_site_delete'] = 'delete';
$lang['remote_site_delete_hint'] = 'delete this site and all its attached elements';
$lang['remote_site_file_not_found'] = 'file create_listing_file.php on remote site was not found';
$lang['remote_site_error'] = 'an error happened';
$lang['remote_site_listing_not_found'] = 'remote listing file was not found';
$lang['remote_site_removed'] = 'was removed on remote site';
$lang['remote_site_removed_title'] = 'Removed elements';
$lang['remote_site_created'] = 'created';
$lang['remote_site_deleted'] = 'deleted';
$lang['remote_site_local_found'] = 'A local listing.xml file has been found for ';
$lang['remote_site_local_new'] = '(new site)';
$lang['remote_site_local_update'] = 'read local listing.xml and update';

// Category words
$lang['cat_add'] = 'Ajouter une catégorie virtuelle';
$lang['cat_virtual'] = 'Catégorie virtuelle';
$lang['cat_public'] = 'Catégorie publique';
$lang['cat_private'] = 'Catégorie privée';
$lang['cat_image_info'] = 'Infos images';
$lang['editcat_status'] = 'Statut';
$lang['editcat_confirm'] = 'Les informations associées à cette catégorie ont été mises à jour.';
$lang['editcat_perm'] = 'Pour accéder aux permissions associées, cliquez';
$lang['cat_access_info'] = 'Permet de gérer l\'accès à cette catégorie.';
$lang['cat_virtual_added'] = 'Catégorie virtuelle créée';
$lang['cat_virtual_deleted'] = 'Catégorie virtuelle détruite';
$lang['cat_upload_title'] = 'Sélectionner les catégories pour lesquelles l\'ajout d\'image est autorisé';
$lang['cat_upload_info'] = 'Seules les catégories non virtuelles et non distantes sont repertoriées.';
$lang['cat_lock_title'] = 'Verrouiller les catégories';
$lang['cat_lock_info'] = 'Cela rendra la catégorie temporairement invisible pour les utilisateurs (maintenance)';
$lang['cat_comments_title'] = 'Autoriser les utilisateurs à poster des commentaires';
$lang['cat_comments_info'] = 'Par héritage, il est possible de poster des commentaires dans une sous-catégorie si cela est autorisé pour au moins une catégorie mère.';
$lang['cat_status_title'] = 'Gestion des autorisations';
$lang['cat_status_info'] = 'Les catégories sélectionnées sont privées : vous devrez permettre à vos utilisateurs et / ou groupes d\'y accéder.
<br />Si une catégorie est déclarée privée, alors toutes ses sous catégories deviennent privées.
<br />Si une catégorie est déclarée publique, alors toutes les catégories mères deviennent publiques.';

//Titles
$lang['admin_panel'] = 'Panneau d\'administration';
$lang['default_message'] = 'Zone d\'administration de PhpWebGallery';
$lang['title_liste_users'] = 'Liste des utilisateurs';
$lang['title_history'] = 'Historique';
$lang['title_update'] = 'Mise à jour de la base de données';
$lang['title_configuration'] = 'Configuration de PhpWebGallery';
$lang['title_instructions'] = 'Instructions';
$lang['title_categories'] = 'Gestion des catégories';
$lang['title_edit_cat'] = 'Editer une catégorie';
$lang['title_info_images'] = 'Modifier les informations sur les images d\'une catégorie';
$lang['title_thumbnails'] = 'Création des miniatures';
$lang['title_thumbnails_2'] = 'pour';
$lang['title_default'] = 'Administration de PhpWebGallery';
$lang['title_waiting'] = 'Images en attente de validation';
$lang['title_upload'] = 'Sélectionner les catégories pour lesquelles l\'ajout d\'image est autorisé';
$lang['title_cat_options'] = 'Options relatives aux catégories';
$lang['title_groups'] = 'Gestion des groupes';

//Error messages
$lang['conf_confirmation'] = 'Informations enregistrées dans la base de données';
$lang['cat_error_name'] = 'Le nom d\'une catégorie ne doit pas être nul';

// Configuration
$lang['conf_default'] = 'Affichage par défaut';
$lang['conf_cookie'] = 'Session & Cookie';

// Configuration -> general
$lang['conf_general_title'] = 'Configuration générale';
$lang['conf_mail_webmaster'] = 'Adresse e-mail de l\'Administrateur';
$lang['conf_mail_webmaster_info'] = 'Les visiteurs pourront vous contacter par ce mail';
$lang['conf_mail_webmaster_error'] = 'Adresse email non valide. Elle doit être de la forme : nom@domaine.com';
$lang['conf_prefix'] = 'Préfixe thumbnail';
$lang['conf_prefix_info'] = 'Les noms des fichiers miniatures en sont préfixé. Laissez vide en cas de doute.';
$lang['conf_prefix_thumbnail_error'] = 'Le préfixe doit être uniquement composé des caractères suivant : a-z, "-" ou "_"';
$lang['conf_access'] = 'Type d\'acces';
$lang['conf_access_info'] = '- libre : n\'importe qui peut accéder à vos photos, tous les visiteurs peuvent se créer un compte pour pouvoir personnaliser l\'affichage<br />
- restreint : l\'administrateur s\'occupe de créer des comptes, seuls les personnes membres peuvent accéder au site';
$lang['conf_log_info'] = 'historiser les visites sur le site ? Les visites seront visibles dans l\'historique de l\'administration';
$lang['conf_notification'] = 'Notification par mail';
$lang['conf_notification_info'] = 'Notification automatique par mail des administrateurs (seuls les administrateurs) lors de l\'ajout d\'un commentaire, ou lors de l\'ajout d\'une image.';

// Configuration -> comments
$lang['conf_comments_title'] = 'Configuration des commentaires';
$lang['conf_comments_forall'] = 'Autoriser pour tous ?';
$lang['conf_comments_forall_info'] = 'Même les invités non enregistrés peuvent déposer les messages';
$lang['conf_nb_comment_page'] = 'Nombre de commentaires par page';
$lang['conf_nb_comment_page_info'] = 'Nombre de commentaire à afficher sur chaque page. Le nombre de commentaires pour une image reste illimité. Entrer un nombre entre 5 et 50.';
$lang['conf_nb_comment_page_error'] = 'Le nombre de commentaires par page doit être compris entre 5 et 50 inclus.';
$lang['conf_comments_validation'] = 'Validation';
$lang['conf_comments_validation_info'] = 'L\'administrateur valide les commentaires avant qu\'ils apparaissent sur le site';

// Configuration -> default
$lang['conf_default_title'] = 'Configuration de l\'affichage par défaut';
$lang['conf_default_language_info'] = 'Langue par défaut';
$lang['conf_default_theme_info'] = 'Thème par défaut';
$lang['conf_nb_image_line_info'] = 'Nombre d\'images par ligne par défaut';
$lang['conf_nb_line_page_info'] = 'Nombre de lignes par page par défaut';
$lang['conf_recent_period_info'] = 'En nombre de jours. Période pendant laquelle l\'image est notée comme récente. La durée doit au moins être d\'un jour.';
$lang['conf_default_expand_info'] = 'Développer toutes les catégories par défaut dans le menu ?';
$lang['conf_show_nb_comments_info'] = 'Montrer le nombre de commentaires pour chaque image sur la page des miniatures';
$lang['conf_default_maxwidth_info'] = 'Largeur maximum affichable pour les images : les images ne seront redimensionnées que pour l\'affichage, les fichiers images resteront intacts. 
Laisser vide si vous ne souhaitez pas mettre de limite.';
$lang['conf_default_maxheight_info'] = 'Idem mais pour la hauteur des images';

// Configuration -> upload
$lang['conf_upload_title'] = 'Configuration de l\'envoi d\'images par les utilisateurs';
$lang['conf_upload_maxfilesize'] = 'Poids maximum';
$lang['conf_upload_maxfilesize_info'] = 'Poids maximum autorisé pour les images uploadées. Celui-ci doit être un entier compris entre 10 et 1000, en Ko.';
$lang['conf_upload_maxfilesize_error'] = 'Le poids maximum pour les images uploadés doit être un entier compris entre 10 et 1000.';
$lang['conf_upload_maxwidth'] = 'Largeur maximum';
$lang['conf_upload_maxwidth_info'] = 'Largeur maximum autorisée pour les images. Celle-ci doit être un entier supérieur à 10, en pixels.';
$lang['conf_upload_maxwidth_error'] = 'la largeur maximum des images uploadées doit être un entier supérieur à 10.';
$lang['conf_upload_maxheight'] = 'Hauteur maximum';
$lang['conf_upload_maxheight_info'] = 'Hauteur maximum autorisée pour les images. Celle-ci doit être un entier supérieur à 10, en pixels.';
$lang['conf_upload_maxheight_error'] = 'La hauteur maximum des images uploadées doit être un entier supérieur à 10.';
$lang['conf_upload_tn_maxwidth'] = 'Largeur maximum miniatures.';
$lang['conf_upload_tn_maxwidth_info'] = 'Largeur maximum autorisée pour les miniatures. Celle-ci doit être un entier supérieur à 10, en pixels.';
$lang['conf_upload_maxwidth_thumbnail_error'] = 'La largeur maximum des miniatures uploadées doit être un entier supérieur à 10.';
$lang['conf_upload_tn_maxheight'] = 'Hauteur maximum miniatures';
$lang['conf_upload_tn_maxheight_info'] = 'Hauteur maximum autorisée pour les miniatures. Celle-ci doit être un entier supérieur à 10, en pixels.';
$lang['conf_upload_maxheight_thumbnail_error'] = 'La hauteur maximum des miniatures uploadées doit être un entier supérieur à 10.';

// Configuration -> session
$lang['conf_session_title'] = 'Configuration des sessions';
$lang['conf_authorize_remembering'] = 'Connexion automatique';
$lang['conf_authorize_remembering_info'] = 'Les utilisateurs ne devront plus s\'identifier à chaque nouvelle visiste du site';

// Configuration -> metadata
$lang['conf_metadata_title'] = 'Configuration des métadonnées des images';
$lang['conf_use_exif'] = 'Analyse des EXIF';
$lang['conf_use_exif_info'] = 'Analyse les données EXIF durant la synchronisation des images';
$lang['conf_use_iptc'] = 'Analyse des IPTC';
$lang['conf_use_iptc_info'] = 'Analyse les données IPTC durant la synchronisation des images';
$lang['conf_show_exif'] = 'Montrer les EXIF';
$lang['conf_show_exif_info'] = 'Affiche les métadonnées EXIF';
$lang['conf_show_iptc'] = 'Montrer les IPTC';
$lang['conf_show_iptc_info'] = 'Affiche les métadonnées IPTC';

// Image informations
$lang['infoimage_general'] = 'Options générale pour la catégorie';
$lang['infoimage_useforall'] = 'utiliser pour toutes les images ?';
$lang['infoimage_creation_date'] = 'Date de création';
$lang['infoimage_detailed'] = 'Options pour chaque image / photo';
$lang['infoimage_title'] = 'Titre';
$lang['infoimage_keyword_separation'] = '(séparer avec des ",")';
$lang['infoimage_addtoall'] = 'ajouter à tous';
$lang['infoimage_removefromall'] = 'retirer à tous';
$lang['infoimage_associate'] = 'Associer à la catégorie';

// Thumbnails
$lang['tn_width'] = 'largeur';
$lang['tn_height'] = 'hauteur';
$lang['tn_no_support'] = 'Image inexistante ou aucun support';
$lang['tn_format'] = 'pour le format';
$lang['tn_thisformat'] = 'pour ce format de fichier';
$lang['tn_err_width'] = 'la largeur doit être un entier supérieur à';
$lang['tn_err_height'] = 'la hauteur doit être un entier supérieur à';
$lang['tn_results_title'] = 'Résultats de la miniaturisation';
$lang['tn_picture'] = 'image';
$lang['tn_results_gen_time'] = 'généré en';
$lang['tn_stats'] = 'Statistiques générales';
$lang['tn_stats_nb'] = 'nombre d\'images miniaturisées';
$lang['tn_stats_total'] = 'temps total';
$lang['tn_stats_max'] = 'temps max';
$lang['tn_stats_min'] = 'temps min';
$lang['tn_stats_mean'] = 'temps moyen';
$lang['tn_err'] = 'Vous avez commis des erreurs';
$lang['tn_params_title'] = 'Paramètres de miniaturisation';
$lang['tn_params_GD'] = 'version de GD';
$lang['tn_params_GD_info'] = '- GD est la bibliothèque de manipulation graphique pour PHP<br />
- cochez la version de GD installée sur le serveur. Si vous choisissez l\'une et que vous obtenez ensuite des messages d\'erreur, choisissez l\'autre version. 
Si aucune version ne marche, cela signifie que GD n\'est pas installé sur le serveur.';
$lang['tn_params_width_info'] = 'largeur maximum que peut prendre les miniatures';
$lang['tn_params_height_info'] = 'hauteur maximum que peut prendre les miniatures';
$lang['tn_params_create'] = 'en créer';
$lang['tn_params_create_info'] = 'N\'essayez pas de lancer directement un grand nombre de miniaturisation.<br />
En effet la miniaturisation est coûteuse en ressources processeur pour le serveur. 
Si vous êtes chez un hébergeur gratuit, une trop forte occupation processeur peut amener l\'hébergeur à supprimer votre compte.';
$lang['tn_params_format'] = 'format';
$lang['tn_params_format_info'] = 'seul le format jpeg est supporté pour la création des miniatures';
$lang['tn_alone_title'] = 'images sans miniatures (format jpg et png uniquement)';
$lang['tn_dirs_title'] = 'Liste des répertoires';
$lang['tn_dirs_alone'] = 'images sans miniatures';

// Update
$lang['update_missing_tn'] = 'Il manque la miniature pour';
$lang['update_disappeared_tn'] = 'La miniature n\'existe pas';
$lang['update_disappeared'] = 'n\'existe pas';
$lang['update_part_deletion'] = 'Suppression des images de la base qui n\'ont pas de thumbnail ou qui n\'existent pas';
$lang['update_part_research'] = 'Recherche des nouvelles images dans les répertoires';
$lang['update_research_added'] = 'ajouté';
$lang['update_research_tn_ext'] = 'miniature en';
$lang['update_default_title'] = 'Type de mise à jour';
$lang['update_nb_new_elements'] = 'élément(s) ajouté(s)';
$lang['update_nb_del_elements'] = 'élément(s) effacé(s)';
$lang['update_nb_new_categories'] = 'catégorie(s) ajoutée(s)';
$lang['update_nb_del_categories'] = 'catégorie(s) effacée(s)';
$lang['update_sync_files'] = 'Synchroniser la structure';
$lang['update_sync_dirs'] = 'Seulement les catégories';
$lang['update_sync_all'] = 'Catégories et fichiers';
$lang['update_sync_metadata'] = 'Synchroniser les méta-donnnées';
$lang['update_sync_metadata_new'] = 'Seulement sur les nouveaux éléments';
$lang['update_sync_metadata_all'] = 'Sur tous les éléments';
$lang['update_cats_subset'] = 'Limiter la synchronisation aux catégories suivantes';

// History
$lang['stats_title'] = 'Historique de l\'année écoulée';
$lang['stats_month_title'] = 'Historique mois par mois';
$lang['stats_pages_seen'] = 'Pages vues';
$lang['stats_empty'] = 'vider l\'historique';
$lang['stats_global_graph_title'] = 'Nombre de pages vues par mois';
$lang['stats_visitors_graph_title'] = 'Nombre de visiteurs par jour';

// Users
$lang['user_err_modify'] = 'Cet utilisateur ne peut pas être modifé ou supprimé';
$lang['user_err_unknown'] = 'Cet utilisateur n\'existe pas dans la base de données';
$lang['user_management'] = 'Champs spéciaux pour l\'administrateur';
$lang['user_status'] = 'Statut de l\'utilisateur';
$lang['user_status_admin'] = 'Administrateur';
$lang['user_status_guest'] = 'Utilisateur';
$lang['user_delete'] = 'Supprimer l\'utilisateur';
$lang['user_delete_hint'] = 'Cliquez ici pour supprimer définitivement l\'utilisateur. Attention cette opération ne pourra être rétablie.';

// Groups
$lang['group_list_title'] = 'Liste des groupes existants';
$lang['group_confirm_delete']= 'Confirmer la destruction du groupe';
$lang['group_add'] = 'Ajouter un groupe';
$lang['group_add_error1'] = 'Le nom du groupe ne doit pas comporter de " ou de \' et ne pas être vide.';
$lang['group_add_error2'] = 'Ce nom de groupe est déjà utilisé.';
$lang['group_edit'] = 'Edition des utilisateurs appartenant au groupe';
$lang['group_deny_user'] = 'Supprimer la sélection';
$lang['group_add_user']= 'Ajouter le membre';


// To be done


$lang['permuser_info_message'] = 'Permissions enregistrées';
$lang['permuser_title'] = 'Restrictions pour l\'utilisateur';
$lang['permuser_warning'] = 'Attention : un "<span style="font-weight:bold;">accès interdit</span>" à la racine d\'une catégorie empêche l\'accès à toute la catégorie';
$lang['permuser_parent_forbidden'] = 'catégorie parente interdite';




$lang['title_add'] = 'Ajouter un utilisateur';
$lang['title_modify'] = 'Modifier un utilisateur';

$lang['title_user_perm'] = 'Modifier les permissions pour l\'utilisateur';
$lang['title_cat_perm'] = 'Modifier les permissions pour la catégorie';
$lang['title_group_perm'] = 'Modifier les permissions pour le groupe';
$lang['title_picmod'] = 'Modifier les informations d\'une image';
$lang['waiting_update'] = 'Les images validées ne seront visibles qu\'après mise à jour de la base d\'images.';
$lang['permuser_only_private'] = 'Seules les catégories privées sont représentées';

$lang['comments_last_title'] = 'Derniers commentaires';
$lang['comments_non_validated_title'] = 'Commentaires non validés';
$lang['cat_unknown_id'] = 'Cette catégorie n\'existe pas dans la base de données';
$lang['conf_remote_site_delete_info'] = 'Supprimer un site revient à supprimer toutes les images et les catégories en relation avec ce site.';
?>