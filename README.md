# SnowTricks

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/0f656d90e0354bca9fc9f1163b0eb103)](https://app.codacy.com/gh/bigben35/SnowTricks/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

# Mon Projet Symfony 
SnowTricks - Projet 6 Formation DA PHP/SYMFONY OpenClassrooms

# Description
SnowTricks est un site collaboratif pour faire connaître le Snowboard auprès du grand public et aider à l'apprentissage des figures (tricks).

# Prérequis
PHP 8.0.12  
Symfony 6

# Installation
Cloner le dépôt Git sur votre machine locale : **git clone https://github.com/bigben35/SnowTricks.git**   
Naviguer dans le dossier du projet : **cd mon-projet-symfony**   
Installer les dépendances avec Composer : **composer install**    
Faire un composer require **symfony/console** pour pouvoir utiliser la commande symfony console au lieu de php/bin console (au choix de la personne).    
Créer la base de données : **symfony console doctrine:database:create**      
Effectuer les migrations : **symfony console doctrine:migrations:migrate**     
Charger les fixtures (données de démonstration) : **symfony console doctrine:fixtures:load**, puis éditer une figure pour rajouter une ou plusieurs illustrations et une ou plusieurs vidéos.   
Démarrer le serveur Symfony : **symfony server:start**    

Et voilà ! Vous pouvez maintenant accéder à l'application en naviguant vers http://localhost:8000 dans votre navigateur.  
Vous pouvez passer en https avec la commande : **symfony server:ca:install**

N'oubliez pas de modifier le fichier .env avec vos informations de configuration avant de lancer l'installation.

Vous pouvez vous connecter en tant qu'administrateur avec username:**admin** -  mdp:**admin** ou créer votre propre compte utilisateur. 

# Email
Pour l'inscription et l'envoi d'un email de confirmation, j'ai utiliser mailhog : **https://github.com/mailhog/MailHog** et voici la version que j'ai utilisé: **MailHog_windows_amd64.exe**  
 


