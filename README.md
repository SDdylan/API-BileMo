
# API BileMo / Projet 7 OpenClassrooms

Ce projet est une API permettant de fournir un accès au catalogue de BileMo, une entreprise offrant toute une sélection de téléphones mobiles haut de gamme.

## Fonctionnalités

Un système d'authentification par Token permet aux client différentes d'effectuer différentes actions :
* Consulter la liste des produits BileMo
* Consulter les détails d’un produit BileMo
* Consulter la liste des utilisateurs inscrits
* Consulter le détail d’un utilisateur inscrit
* Ajouter un nouvel utilisateur
* Supprimer un utilisateur ajouté.


## Technologies

* WampServer 3.2.6
    * Apache 2.4.51
    * PHP 7.4.26
    * MySQL 5.7.36
* Composer 2.3.5 
* Symfony 5.3.4 

### Libraries

* [willdurand/hateoas-bundle](https://github.com/willdurand/BazingaHateoasBundle) 2.4
* [zircote/swagger-php](https://github.com/zircote/swagger-php) 6.5
* [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv) 5.4
* [lexik/jwt-authentication-bundle](https://github.com/lexik/LexikJWTAuthenticationBundle) 2.15
* [jms/serializer-bundle](https://packagist.org/packages/jms/serializer-bundle) 4.0
* [fakerphp/faker](https://github.com/FakerPHP/Faker) 1.19

## Installation

### Configuration de l'environnement

Il est nécessaire d'avoir un environnement local avec MySQL, PHP et Apache.

### Déploiement du projet

Téléchargez manuellement le contenu de ce dépôt GitHub dans un dossier de votre système.
Vous pouvez également utiliser Git avec un terminal à la racine d'un dossier de votre choix :
```
git clone https://github.com/SDdylan/API-BileMo
```
Pour la prochaine étapes, vous aurez besoin de [**Composer**](https://getcomposer.org/download/), veillez à l'installer si vous ne disposez pas déjà de ce dernier sur votre système.  
Installez ensuite les librairies de ce projet à l'aide d'un terminal à la racine de l'application avec la commande ci-dessous :
```
composer install
```

### Base de données

Veillez dans un premier temps à changer la valeur de la connexion dans le fichier **.env**, il s'agit de la variable *DATABASE_URL*.

Dans un terminal à la racine du projet executez la commande suivante pour créer la base de donnée :
```
php bin/console doctrine:database:create
```
Créez ensuite la structure de cette base de donnée :
```
php bin/console doctrine:migrations:migrate
```
Chargez les données initiales:
```
php bin/console doctrine:fixtures:load
```

## Lancez l'application

Lancez le serveur à l'aide de la commande suivante :
```
symfony serve
```

Vous pourrez de cette manière accèder à l'API avec le lien qui vous sera indiquer dans le terminal, vous pourrez l'utiliser dans [**PostMan**](https://www.postman.com/) par exemple pour naviguer dans l'API.

## Documentation

La documentation est accessible en lançant votre serveur, au lien suivant :
```
http://127.0.0.1:8000/swagger/
```

## Auteurs

[@SDdylan](https://github.com/SDdylan) sous la supervision de [@aurek](https://github.com/aurelienk).

