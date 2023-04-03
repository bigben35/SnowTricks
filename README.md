# SnowTricks

# Mon Projet Symfony 
SnowTricks - Projet 6 Formation DA PHP/SYMFONY OpenClassrooms

# Description
SnowTricks est un site collaboratif pour faire connaître le Snowboard auprès du grand public et aider à l'apprentissage des figures (tricks).

# Installation
Cloner le dépôt Git sur votre machine locale : git clone https://github.com/bigben35/SnowTricks.git
Naviguer dans le dossier du projet : cd mon-projet-symfony
Installer les dépendances avec Composer : composer install
Faire un composer require symfony/console pour pouvoir utiliser la commande symfony console au lieu de php/bin console (au choix de la personne).
Créer la base de données : symfony console doctrine:database:create
Effectuer les migrations : symfony console doctrine:migrations:migrate
Charger les fixtures (données de démonstration) : symfony console doctrine:fixtures:load
Démarrer le serveur Symfony : symfony server:start

Et voilà ! Vous pouvez maintenant accéder à l'application en naviguant vers http://localhost:8000 dans votre navigateur.
Vous pouvez passer en https avec la commande : symfony server:ca:install

N'oubliez pas de modifier le fichier .env avec vos informations de configuration avant de lancer l'installation.
