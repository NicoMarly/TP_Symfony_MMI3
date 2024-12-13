# TP_Symfony_MMI3

Cette application n'est malheureusement pas terminée, il manque le système d'authentification et d'autorisation d'accès. Cependant toutes les requêtes vers la base de données sont oppérationnelles.

Ceci étant dis, voici la notice d'utilisation et d'installation de cette application.


1. INSTALLATION
- Dans un premier temps, clonez ce répository dans le dossier "htdocs" (Xampp) ou "www" (Wampp) du programme d'hébergement local que vous utilisez.
- Ouvrez le dossier créé dans VsCode ou dans un invite de commande (auquel cas atteignez le via des "cd nom_du_dossier")
- Allez dans le dossier Api (cd Api)
- Faites "composer install" pour installer toutes les dépendances et librairies du projet
- Créer la base de données avec "php bin/console doctrine:database:create"
- Les migrations ont délibéremment été supprimées pour éviter les problèmes lors de la mise en place de la base de données. Faites donc dans un premier temps "php bin/console make:migration", puis faites "php bin/console doctrine:migrations:migrate".
- Si tout s'est bien passé, vous verrez trois tables dans la base de données sur phpmyadmin : migrations, user et reservation
- Lancez maintenant le serveur Symfony avec "symfony server:start"
- L'application est désormais prête à être utilisée


2. UTILISATION
- Pour l'utilisation de cette application, nous passerons par Postman
- L'adresse IP du serveur symfony est généralement 127.0.0.1:8000 (le port peut changer si d'autre serveur symfony sont en cours d'exécution)
- Les routes a utilisé se décompose de la manière suivante : /nom_de_la_table/id (si il y en a un)/nom_de_la_method (create, read, update ou delete)
- Attention ! Les méthodes update et delete nécessite obligatoirement un id pour fonctionner, la méthode read demande un id si vous cherchez un élément spécifique de la table.
- Pour les méthodes create et update, des informations sont nécessaires
- Pour commencer avec l'application, deux fichiers d'exemples vous sont fournis dans le repository
- En suivant leur format, vous serez en mesure d'utilisez l'application
