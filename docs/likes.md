# Likes API

Likes use a **polymorphic** relationship supporting both posts and comments. Calling the toggle endpoint when already liked will unlike it (and vice versa).

## Endpoints

### `POST /api/posts/{post}/like` — Toggle like on a post

**Response:** `200 OK`
```json
{
    "is_liked": true,
    "likes_count": 6
}
```

---

### `GET /api/posts/{post}/likes` — Users who liked a post

**Response:** `200 OK`
```json
{
    "data": [
        {
            "uuid": "a10af5af-3581-42a5-8020-e79de7bd3b1b",
            "first_name": "John",
            "last_name": "Doe"
        }
    ]
}
```

---

### `POST /api/comments/{comment}/like` — Toggle like on a comment

**Response:** `200 OK`
```json
{
    "is_liked": true,
    "likes_count": 4
}
```

---

### `GET /api/comments/{comment}/likes` — Users who liked a comment

**Response:** `200 OK`
```json
{
    "data": [
        {
            "uuid": "a10af5af-3581-42a5-8020-e79de7bd3b1b",
            "first_name": "Jane",
            "last_name": "Doe"
        }
    ]
}
```

## Database schema

The `likes` table uses Laravel's polymorphic relation:

| Column | Type | Description |
|--------|------|-------------|
| `user_id` | FK → users | Who liked |
| `likeable_id` | bigint | ID of the liked item |
| `likeable_type` | string | `"App\Models\Post"` or `"App\Models\Comment"` |

Unique constraint on `(user_id, likeable_id, likeable_type)` prevents duplicate likes.
