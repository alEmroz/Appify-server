# Comments API

Comments support nested replies (one level deep — a reply cannot have replies).

## Endpoints

### `GET /api/posts/{post}/comments` — List comments

Returns top-level comments with their replies nested, newest first.

**Query params:**
| Param | Type | Description |
|-------|------|-------------|
| `cursor` | `string` | Cursor for pagination (the `meta.next_cursor` value from the previous response) |

**Response:** `200 OK`
```json
{
    "data": [
        {
            "uuid": "e29ebb71-bad8-4a21-9c0f-769cd451f4c9",
            "text": "Great post!",
            "user": {
                "uuid": "3992f891-5d33-4aad-9953-4f3038d338e5",
                "first_name": "Fahim",
                "last_name": "al Emroz"
            },
            "likes_count": 0,
            "is_liked": false,
            "created_at": "2026-06-14T00:06:10.000000Z",
            "replies": [
                {
                    "uuid": "98609657-be1b-4f4b-9dad-c26f865e16a3",
                    "text": "Thanks!",
                    "user": {
                        "uuid": "3992f891-5d33-4aad-9953-4f3038d338e5",
                        "first_name": "Fahim",
                        "last_name": "al Emroz"
                    },
                    "likes_count": 0,
                    "is_liked": false,
                    "created_at": "2026-06-14T00:06:17.000000Z"
                }
            ]
        }
    ],
    "links": {
        "first": null,
        "last": null,
        "prev": null,
        "next": null
    },
    "meta": {
        "path": "http://localhost:8000/api/posts/79f0c561-a0d7-40a9-9cbf-5d465679d5be/comments",
        "per_page": 15,
        "next_cursor": null,
        "prev_cursor": null
    }
}
```

**Notes:**
- `replies` is an empty array `[]` if the comment has no replies
- Replies have the same shape as comments but no `replies` key
- Only UUIDs are used — no numeric `id` fields are ever sent

---

### `POST /api/posts/{post}/comments` — Create comment

**Request (JSON):**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `text` | `string` | Yes | Max 2000 characters |

**Response:** `201 Created`
```json
{
    "uuid": "e29ebb71-bad8-4a21-9c0f-769cd451f4c9",
    "text": "Great post!",
    "user": {
        "uuid": "3992f891-5d33-4aad-9953-4f3038d338e5",
        "first_name": "Fahim",
        "last_name": "al Emroz"
    },
    "likes_count": 0,
    "is_liked": false,
    "created_at": "2026-06-14T00:06:10.000000Z"
}
```

Returns the created comment (no `data` wrapper, no `replies` key).

---

### `PUT /api/comments/{comment}` — Update comment

**Request (JSON):**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `text` | `string` | No (but at least one field required) | Max 2000 characters |

**Response:** `200 OK`
```json
{
    "uuid": "e29ebb71-bad8-4a21-9c0f-769cd451f4c9",
    "text": "Updated text!",
    "user": {
        "uuid": "3992f891-5d33-4aad-9953-4f3038d338e5",
        "first_name": "Fahim",
        "last_name": "al Emroz"
    },
    "likes_count": 0,
    "is_liked": false,
    "created_at": "2026-06-14T00:06:10.000000Z"
}
```

Only the comment author can update. Returns `404` otherwise.

---

### `POST /api/comments/{comment}/reply` — Reply to a comment

**Request (JSON):** Same as create comment (`text`).

**Response:** `201 Created`
```json
{
    "uuid": "98609657-be1b-4f4b-9dad-c26f865e16a3",
    "text": "Thanks!",
    "user": {
        "uuid": "3992f891-5d33-4aad-9953-4f3038d338e5",
        "first_name": "Fahim",
        "last_name": "al Emroz"
    },
    "likes_count": 0,
    "is_liked": false,
    "created_at": "2026-06-14T00:06:17.000000Z"
}
```

Returns `404` if trying to reply to a reply (only top-level comments can have replies).

---

### `DELETE /api/comments/{comment}` — Delete comment

**Response:** `204 No Content`

Only the comment author can delete. Returns `404` otherwise.

---

### `POST /api/comments/{comment}/like` — Toggle like

**Response:** `200 OK`
```json
{
    "is_liked": true,
    "likes_count": 1
}
```

---

### `GET /api/comments/{comment}/likes` — List who liked

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
