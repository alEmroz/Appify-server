# Likes API

Likes use a **polymorphic** relationship supporting both posts and comments. Calling the toggle endpoint when already liked will unlike it (and vice versa).

## Endpoints

### `POST /api/posts/{post}/like` — Toggle like on a post

**Response:** `200 OK`
```json
{
    "is_liked": true,
    "likes_count": 1
}
```

---

### `GET /api/posts/{post}/likes` — Users who liked a post

**Response:** `200 OK`
```json
{
    "data": [
        {
            "uuid": "3992f891-5d33-4aad-9953-4f3038d338e5",
            "first_name": "Fahim",
            "last_name": "al Emroz"
        }
    ],
    "meta": {
        "next_cursor": null,
        "prev_cursor": null,
        "per_page": 15
    }
}
```

---

### `POST /api/comments/{comment}/like` — Toggle like on a comment

**Response:** `200 OK`
```json
{
    "is_liked": true,
    "likes_count": 1
}
```

---

### `GET /api/comments/{comment}/likes` — Users who liked a comment

**Response:** `200 OK`
```json
{
    "data": [
        {
            "uuid": "3992f891-5d33-4aad-9953-4f3038d338e5",
            "first_name": "Fahim",
            "last_name": "al Emroz"
        }
    ],
    "meta": {
        "next_cursor": null,
        "prev_cursor": null,
        "per_page": 15
    }
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
