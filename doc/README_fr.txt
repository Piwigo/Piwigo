=============
PhpWebGallery
=============

http://phpwebgallery.net

Installation
============

1. décompresser l'archive téléchargée

2. placer les fichiers décompressés sur votre serveur web dans le répertoire
   de votre choix ("galerie" par exemple)

3. se rendre à l'URL http://votre.domaine/galerie/install.php et suivre les
   instructions

Mise à jour
===========

1. éléments à sauvegarder :

 - fichier "include/mysql.inc.php"
 - fichier "include/config_local.inc.php" s'il existe
 - répertoire "galleries"
 - votre base de données (en créant un dump, avec PhpMyAdmin par exemple)

2. supprimer tous les fichiers et répertoires de la précédente installation
   (sauf les éléments listés ci-dessus)

3. décompresser l'archive contenant la dernière version

4. placer tous les fichiers de la nouvelle version sur votre site web sauf
   pour les élements listés ci-dessus. Les seuls éléments venant de la
   précédente installation sont ceux listés ci-dessus.

5. se rendre à l'URL http://votre.domaine/galerie/upgrade.php et suivre les
   instructions

Comment commencer
=================

Une fois installée ou mise à jour, votre galerie est prête à
fonctionner. Commencez par vous rendre sur le répertoire d'installation dans
votre navigateur : 

http://votre.domaine/galerie

Ensuite, identifiez-vous en tant qu'un administrateur. Un nouveau lien dans
le menu d'identification de la page principale va apparaître :
Administration. Suivre ce lien :-)

Dans la zone d'administration, prenez tout le temps nécessaire pour
consulter les instructions, expliquant comment utiliser votre galerie.

Communication
=============

Newsletter
----------

https://gna.org/mail/?group=phpwebgallery

Il est *fortement* recommandé de souscrire à la newsletter de
PhpWebGallery. Très peu de mails sont envoyés, mais les informations sont
importantes : nouvelles versions de l'application, notification de bugs
importants (relatifs à la sécurité). Vous trouverez les listes de
discussions disponibles sur la page suivante :

Pas de spam, pas d'utilisation commerciale.

Freshmeat
---------

http://freshmeat.net/projects/phpwebgallery

Permet d'être au courant des sorties de toutes les releases, et en
exclusivité les builds de la branche de développement (ce qui n'est pas
prévu sur les mailing lists "announce").

Outil de suivi de bogues
------------------------

http://bugs.phpwebgallery.net

Gestion des bugs, mais aussi demande de nouvelles fonctionnalités. Rien de
plus efficace pour qu'un bug soit corrigé : tant qu'il ne l'est pas, la
"fiche" reste là à attendre, on ne l'oublie pas comme un topic sur le
forum.

Les demandes d'évolutions sont également gérées dans cet outil. Ce n'est pas
forcément idéal car il ne s'agit pas de la même chose, mais le suivi du dev
d'une nouvelle fonctionnalité peut se modéliser de la même façon que le
suivi de la correction d'un bug.

Wiki
----

http://phpwebgallery.net/doc

Documentation suivant le système du wiki. Chacun peut participer à
l'amélioration de la doc.

Forum de discussion
-------------------

http://forum.phpwebgallery.net

Un forum est disponible et recommandé pour toutes les questions autres que
les demandes d'évolution et rapport de bogue (installation, discussions
techniques).
