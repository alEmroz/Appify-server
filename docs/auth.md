# Auth API

### Register

**POST** `/api/register`

- Creates a new user account and returns an API token.
- The token must be sent as `Authorization: Bearer <token>` on subsequent requests.

Request body:

```json
{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "password": "password123"
}
```

Validation:

- `first_name` required, string, max 255
- `last_name` required, string, max 255
- `email` required, string, email, max 255, unique in `users`
- `password` required, string, min 8

Response `201`:

```json
{
    "token": "1|abc123def456...",
    "user": {
        "uuid": "2d1745c9-2f2c-48df-a667-4bb8a383301a",
        "first_name": "John",
        "last_name": "Doe",
        "email": "john@example.com",
        "created_at": "2026-06-13T11:08:16.000000Z",
        "updated_at": "2026-06-13T11:08:16.000000Z"
    }
}
```

---

### Login

**POST** `/api/login`

- Authenticates an existing user and returns a new API token.
- Previous tokens remain valid unless explicitly revoked via logout.

Request body:

```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

Validation:

- `email` required, string, email
- `password` required, string

Response `200`:

```json
{
    "token": "2|xyz789ghi012...",
    "user": {
        "uuid": "2d1745c9-2f2c-48df-a667-4bb8a383301a",
        "first_name": "John",
        "last_name": "Doe",
        "email": "john@example.com",
        "email_verified_at": null,
        "created_at": "2026-06-13T11:08:16.000000Z",
        "updated_at": "2026-06-13T11:08:16.000000Z"
    }
}
```

Response `422` (invalid credentials):

```json
{
    "message": "The provided credentials are incorrect.",
    "errors": {
        "email": ["The provided credentials are incorrect."]
    }
}
```

---

### Logout

**POST** `/api/logout` 🔐

- Revokes the current API token.
- The token can no longer be used after this request.

Headers: `Authorization: Bearer <token>`

Response `204` (no content).

---

### Get Authenticated User

**GET** `/api/user` 🔐

- Returns the user associated with the current token.

Headers: `Authorization: Bearer <token>`

Response `200`:

```json
{
    "uuid": "2d1745c9-2f2c-48df-a667-4bb8a383301a",
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com"
}
```
