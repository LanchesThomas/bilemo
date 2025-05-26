# OpenClassrooms
- Développeur d'application
- Parcours PHP/Symfony
- Projet 7


[![Codacy Badge](https://app.codacy.com/project/badge/Grade/615cab37b17b4c5ea8c02de3ba468905)](https://app.codacy.com/gh/LanchesThomas/bilemo/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

### À propos

Bonjour et bienvenue sur le dépôt de mon travail, qui traite du septième projet d'OpenClassrooms, intitulé **Créez un web service exposant une API** ! Vous trouverez ici la procédure d'installation pour prendre en main le code du projet, ainsi que la base de données et sa structure, conçue pour fonctionner avec.

Vous trouverez également, dans le dossier **ressources**, les diagrammes UML conçus en amont du projet ainsi que la base de données au format SQL.

---

## Pré-requis

- PHP ≥ 8.3
- Symfony ≥ 7
- Composer
- MySQL ou PostgreSQL
- OpenSSL
- Postman
- Serveur local type WAMP/MAMP ou Docker

---

## Installation

1. Clonez le dépôt :

```shell
git clone https://github.com/LanchesThomas/bilemo.git
cd bilemo
```


2. Installez les dépendances : :

```shell
composer install
```


3. Configurez les variables d’environnement :

- Dupliquez le fichier .env en .env.local
- Renseignez vos identifiants de base de données :

```php
DATABASE_URL="mysql://utilisateur:motdepasse@127.0.0.1:3306/bilemo_db?serverVersion=8.0.32&charset=utf8mb4"
```

4. Générez les clés JWT :

```bash
mkdir -p config/jwt
openssl genrsa -out config/jwt/private.pem -aes256 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

```

5. Ajouter dans votre .env/local
```php
###> lexik/jwt-authentication-bundle ###
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=votre_passphrase
###< lexik/jwt-authentication-bundle ###
```

5. Créez et migrez la base de données :

```shell
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```


6. Chargez les fixtures (données de test) :

```shell
php bin/console doctrine:fixtures:load
```


7.Démarrez le serveur local :

```shell
symfony server:start
```

8. Vous pouvez tester l'api sur https://127.0.0.1:8000/api/doc

### Merci pour votre attention !