Falilou TOURRE 
Hugo PROST-PINEAU

Nous avons eu un problème lors de nos tests avec la création du token. Les routes n'arrivait pas à le récupérer.

# API Formule 1 – gestion des infractions

Ce projet Symfony 6.4 expose une API JSON sécurisée par JWT pour gérer les écuries, pilotes et infractions d’un championnat de Formule 1.

## Pré-requis

- PHP 8.2 + Composer
- MySQL/MariaDB
- OpenSSL (utilisé pour générer les clés JWT)

## Installation

```bash
composer install

# mettre à jour le fichier .env.local avec vos paramètres de base de données :
# DATABASE_URL="mysql://user:password@127.0.0.1:3306/tp2_symfony"

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Générer/mettre à jour les fixtures (écuries, pilotes, admin, infractions)
php bin/console doctrine:fixtures:load --no-interaction

# Génération des clés JWT (si besoin)
set OPENSSL_CONF=C:\wamp64\bin\php\php8.2.26\extras\ssl\openssl.cnf
php scripts\generate_jwt_keys.php

php -S localhost:8000 -t public
```

## Authentification JWT

1. POST `/api/login_check`

   ```json
   {
     "email": "admin@example.com",
     "password": "adminpass"
   }
   ```

   Réponse : `{ "token": "…" }`

2. Ajouter l’en-tête `Authorization: Bearer <TOKEN>` pour toutes les routes protégées (`/api/...`).

### Scripts utiles

- `scripts/generate_jwt_keys.php` génère `config/jwt/private.pem` et `config/jwt/public.pem` (passphrase `lexik_jwt_pass`).

## Données initiales

`AppFixtures` crée :
- 3 écuries (Ferrari, Mercedes AMG, Red Bull Racing)
- 3 pilotes par écurie (statut titulaire/réserviste, 12 points de licence)
- Un compte administrateur (admin@example.com / adminpass)

`InfractionFixtures` ajoute 3 infractions représentatives (pénalité pilote, amende écurie, collision).

## Routes principales

| Méthode | URL                                | Description                                     | Sécurité |
|---------|------------------------------------|-------------------------------------------------|----------|
| POST    | `/api/login_check`                 | Obtenir un token JWT                            | Public   |
| POST    | `/api/user/create`                 | Créer un utilisateur (roles optionnel)          | Public   |
| PUT     | `/api/teams/{id}/drivers`          | Remplacer les pilotes d’une écurie              | JWT      |
| POST    | `/api/infractions`                 | Ajouter une infraction (points/amende)          | JWT + ROLE_ADMIN |
| GET     | `/api/infractions`                 | Lister et filtrer les infractions (team/driver/date) | JWT |

> Toute autre route sous `/api` nécessite un token valide.

## Tests rapides (Postman / HTTP clients)

1. **Login** – récupère le token JWT.
2. **Créer un utilisateur**
   ```json
   { "email": "steward@example.com", "password": "ChangeMe123!", "roles": ["ROLE_ADMIN"] }
   ```
3. **Modifier les pilotes d’une écurie**
   ```json
   PUT /api/teams/1/drivers
   { "drivers": [1, 2] }
   ```
4. **Créer une infraction**
   ```json
   POST /api/infractions
   {
     "driver": 1,
     "description": "Collision évitable",
     "points": 3,
     "race": "Monaco 2025",
     "date": "2025-06-01T14:30:00+00:00"
   }
   ```
5. **Lister les infractions**
   ```
   GET /api/infractions?team=1&driver=1&date=2025-06-01
   ```

Des exemples prêts à l’emploi sont disponibles dans `tests/http/`.

## Notes / limitations

- Les statuts HTTP tiennent compte des erreurs courantes (400 JSON invalide, 404 entité inexistante, 401/403 accès refusé).
- Les contrôles métiers restent basiques (pas de validation Symfony). Ils peuvent être enrichis selon les besoins.
- Les 401 rencontrés durant nos tests étaient dus à des headers manquants ; pensez à vérifier l’en-tête `Authorization` dans Postman.

Bon test !