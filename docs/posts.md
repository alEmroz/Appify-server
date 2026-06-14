# Posts API

## Endpoints

### `GET /api/posts` — List posts

Returns public posts + the authenticated user's own private posts, newest first.

**Query params:**
| Param | Type | Description |
|-------|------|-------------|
| `cursor` | `string` | Cursor for pagination (the `meta.next_cursor` value from the previous response) |

**Response:** `200 OK`
```json
{
    "data": [
        {
            "uuid": "79f0c561-a0d7-40a9-9cbf-5d465679d5be",
            "text": "Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.",
            "visibility": "public",
            "user": {
                "uuid": "3992f891-5d33-4aad-9953-4f3038d338e5",
                "first_name": "Fahim",
                "last_name": "al Emroz"
            },
            "media": null,
            "likes_count": 0,
            "comments_count": 0,
            "is_liked": false,
            "created_at": "2026-06-13T23:31:42.000000Z"
        }
    ],
    "links": {
        "first": null,
        "last": null,
        "prev": null,
        "next": "http://localhost:8000/api/posts?cursor=eyJjcmVhdGVkX2F0IjoiMjAyNi0wNi0xMyAyMzozMTo0MiIsImlkIjo1MCwiX3BvaW50c1RvTmV4dEl0ZW1zIjp0cnVlfQ"
    },
    "meta": {
        "path": "http://localhost:8000/api/posts",
        "per_page": 15,
        "next_cursor": "eyJjcmVhdGVkX2F0IjoiMjAyNi0wNi0xMyAyMzozMTo0MiIsImlkIjo1MCwiX3BvaW50c1RvTmV4dEl0ZW1zIjp0cnVlfQ",
        "prev_cursor": null
    }
}
```

**Notes:**
- `media` is `null` if no image was uploaded with the post
- `is_liked` indicates whether the authenticated user has liked the post
- Only UUIDs are used — no numeric `id` fields are ever sent
- Pagination uses Laravel's cursor pagination; metadata is inside `meta`

---

### `POST /api/posts` — Create post

**Request (multipart/form-data):**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `text` | `string` | Yes | Max 5000 characters |
| `image` | `file` | No | JPEG, PNG, JPG, GIF, WebP. Max 10MB |
| `visibility` | `string` | No | `"public"` (default) or `"private"` |

**Response:** `201 Created`
```json
{
    "uuid": "ef5e9b13-fb1a-4bcd-a165-ba660d55709a",
    "text": "This is a test post from the docs update",
    "visibility": "public",
    "user": {
        "uuid": "3992f891-5d33-4aad-9953-4f3038d338e5",
        "first_name": "Fahim",
        "last_name": "al Emroz"
    },
    "media": null,
    "likes_count": 0,
    "comments_count": 0,
    "is_liked": false,
    "created_at": "2026-06-14T00:06:02.000000Z"
}
```

Returns the created post object (same shape as list item, no `data` wrapper).

---

### `GET /api/posts/{post}` — Show single post

**Response:** `200 OK`
```json
{
    "uuid": "79f0c561-a0d7-40a9-9cbf-5d465679d5be",
    "text": "Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.",
    "visibility": "public",
    "user": {
        "uuid": "3992f891-5d33-4aad-9953-4f3038d338e5",
        "first_name": "Fahim",
        "last_name": "al Emroz"
    },
    "media": null,
    "likes_count": 0,
    "comments_count": 0,
    "is_liked": false,
    "created_at": "2026-06-13T23:31:42.000000Z"
}
```

Returns `404` if the post is private and not owned by the authenticated user.

---

### `PUT /api/posts/{post}` — Update post

**Request (multipart/form-data):**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `text` | `string` | No | Max 5000 characters. Only provided fields are updated |
| `image` | `file` | No | JPEG, PNG, JPG, GIF, WebP. Max 10MB. Replaces existing image |
| `visibility` | `string` | No | `"public"` or `"private"` |

**Response:** `200 OK`
```json
{
    "uuid": "ef5e9b13-fb1a-4bcd-a165-ba660d55709a",
    "text": "Updated post text",
    "visibility": "private",
    "user": {
        "uuid": "3992f891-5d33-4aad-9953-4f3038d338e5",
        "first_name": "Fahim",
        "last_name": "al Emroz"
    },
    "media": null,
    "likes_count": 0,
    "comments_count": 0,
    "is_liked": false,
    "created_at": "2026-06-14T00:06:02.000000Z"
}
```

Only the post author can update. Uses `_method: "PUT"` inside the multipart form. Returns `404` if not the owner.

---

### `DELETE /api/posts/{post}` — Delete post

**Response:** `204 No Content`

Only the post author can delete. Returns `404` otherwise.

---

### `POST /api/posts/{post}/like` — Toggle like

**Response:** `200 OK`
```json
{
    "is_liked": true,
    "likes_count": 1
}
```

---

### `GET /api/posts/{post}/likes` — List who liked

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
