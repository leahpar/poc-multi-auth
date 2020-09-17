# POC Multi Authentification / Authorisation

POC de gestion de rôles (authorisation) contextuels pour un même utilisateur (authenficiation)

## Entités

### `src/Entity/User.php`

Pour l'authentification des utilisateurs.
Contient l'email et mot de passe.

### `src/Entity/Casquette.php`

Pour les rôles (authorisation) des utilisateurs.
Chaque utilisateur peut avoir plusieurs casquettes.
Contient le token de connexion et les rôles associés.

## Config

````yaml
# config/security.yaml
security:
    enable_authenticator_manager: true
    ...
    main:
        stateless: true
        custom_authenticators:
            - App\Security\ApiKeyAuthenticator
````

## Gestion de l'authentification par email/password

`src/Controller/SecurityController.php`.

Le controleur `login()` vérifie l'email et mot de passe,
et génère des tokens pour les différentes casquettes de l'utilisateur.

Les tokens sont ensuite utilisables avec l'authenfication par token.

@INFO : pas de gestion ici de l'expiration et renouvellement du token.


## Gestion de l'authenfication par token

https://symfony.com/doc/master/security/experimental_authenticators.html
`src/Security/ApiKeyAuthenticator.php`

Lors de la récupération du token,
la casquette correspondante est stockée sur l'utilisateur (non persisté) :
```php
$user->setActiveCasquette($casquette);
```

Les rôles d'un utilisateur ne sont pas stockés, 
mais récupérés depuis la casquette en question.


## Installation

```shell
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --dump-sql
php bin/console doctrine:schema:update --force
symfony server:start
```

## Utilisation

### Initialisation quelques utilisateurs

```shell
curl -I -X GET https://localhost:8000/init
```

### Connexion annonyme (401)

```shell
curl -I -X GET https://localhost:8000/ 
```

### Connexion bad token (401)

```shell
curl -I -X GET --header "X-AUTH-TOKEN: badToken" https://localhost:8000/
```

### Connexion utilisateur KO (401)

```shell
curl -X POST -D - \
     --header "Content-Type: application/json" \
     --header "Accept: application/json" \
     --data   '{"email":"toto1@gmail.com","password":"bad_password"}' \
     https://localhost:8000/login
```

### Connexion utilisateur OK

```shell
curl -X POST -D - \
     --header "Content-Type: application/json" \
     --header "Accept: application/json" \
     --data   '{"email":"toto2@gmail.com","password":"azeaze"}' \
     https://localhost:8000/login
```

### Connexion avec token

```shell
curl -I -X GET --header "X-AUTH-TOKEN: <INSERT ANY TOKEN HERE>" https://localhost:8000/
curl -I -X GET --header "X-AUTH-TOKEN: <INSERT AGENCE TOKEN HERE>" https://localhost:8000/agence
curl -I -X GET --header "X-AUTH-TOKEN: <INSERT DIRECTEUR TOKEN HERE>" https://localhost:8000/directeur
curl -I -X GET --header "X-AUTH-TOKEN: <INSERT BENEFICIAURE TOKEN HERE>" https://localhost:8000/beneficiaire
```

### Déconnexion

```shell
curl -I -X GET --header "X-AUTH-TOKEN: <INSERT ANY TOKEN HERE>" https://localhost:8000/logout
```
