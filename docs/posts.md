# Posts API

## Endpoints

### `GET /api/posts` — List posts

Returns public posts + the authenticated user's own private posts, newest first.

**Query params:**
| Param | Type | Description |
|-------|------|-------------|
| `cursor` | `string` | Cursor for pagination (the `next_cursor` value from the previous response) |

**Response:** `200 OK`
```json
{
    "data": [
        {
            "uuid": "d422f22f-c062-4f0f-a071-5a737abc5f1b",
            "text": "Hello world",
            "visibility": "public",
            "user": {
                "uuid": "a10af5af-3581-42a5-8020-e79de7bd3b1b",
                "first_name": "Jane",
                "last_name": "Doe"
            },
            "media": {
                "uuid": "26404863-25ac-46c3-badc-802cde99aeb8",
                "url": "http://localhost:8000/storage/posts/abc123.jpg",
                "sort_order": 0
            },
            "likes_count": 5,
            "comments_count": 3,
            "is_liked": false,
            "created_at": "2026-06-13T13:01:11.000000Z"
        }
    ],
    "path": "http://localhost:8000/api/posts",
    "per_page": 15,
    "next_cursor": "eyJpZCI6MTV9",
    "next_page_url": "http://localhost:8000/api/posts?cursor=eyJpZCI6MTV9",
    "prev_cursor": null,
    "prev_page_url": null
}
```

**Notes:**
- `media` is `null` if no image was uploaded with the post
- `is_liked` indicates whether the authenticated user has liked the post
- No numeric `id` fields are ever sent — only UUIDs
- Pagination uses Laravel's cursor pagination

---

### `POST /api/posts` — Create post

**Request (multipart/form-data):**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `text` | `string` | Yes | Max 5000 characters |
| `image` | `file` | No | JPEG, PNG, JPG, GIF, WebP. Max 10MB |
| `visibility` | `string` | No | `"public"` (default) or `"private"` |

**Response:** `201 Created` — Returns the created post object (same shape as list item, no `data` wrapper).

---

### `GET /api/posts/{post}` — Show single post

**Response:** `200 OK` — Returns the post object (no `data` wrapper).

Returns `404` if the post is private and not owned by the authenticated user.

---

### `DELETE /api/posts/{post}` — Delete post

**Response:** `204 No Content`

Only the post author can delete. Returns `404` otherwise.

---

### `POST /api/posts/{post}/like` — Toggle like

**Request body:** None

**Response:** `200 OK`
```json
{
    "is_liked": true,
    "likes_count": 6
}
```

---

### `GET /api/posts/{post}/likes` — List who liked

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
