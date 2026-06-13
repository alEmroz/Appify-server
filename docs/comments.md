# Comments API

Comments support nested replies (one level deep — a reply cannot have replies).

## Endpoints

### `GET /api/posts/{post}/comments` — List comments

Returns top-level comments with their replies nested, newest first.

**Query params:**
| Param | Type | Description |
|-------|------|-------------|
| `cursor` | `string` | Cursor for pagination |

**Response:** `200 OK`
```json
{
    "data": [
        {
            "uuid": "2608f86c-0492-4af7-af34-f390984da6c5",
            "text": "Great post!",
            "user": {
                "uuid": "a10af5af-3581-42a5-8020-e79de7bd3b1b",
                "first_name": "John",
                "last_name": "Doe"
            },
            "likes_count": 3,
            "is_liked": true,
            "created_at": "2026-06-13T13:01:56.000000Z",
            "replies": [
                {
                    "uuid": "48e535e7-e46f-40d6-8567-dec23dd4451a",
                    "text": "Thanks!",
                    "user": {
                        "uuid": "a10af5af-3581-42a5-8020-e79de7bd3b1b",
                        "first_name": "Jane",
                        "last_name": "Doe"
                    },
                    "likes_count": 1,
                    "is_liked": false,
                    "created_at": "2026-06-13T13:01:56.000000Z"
                }
            ]
        }
    ],
    "path": "http://localhost:8000/api/posts/{uuid}/comments",
    "per_page": 15,
    "next_cursor": null,
    "next_page_url": null,
    "prev_cursor": null,
    "prev_page_url": null
}
```

**Note:** No numeric `id` fields are ever sent — only UUIDs. `parent_id` is internal only.

---

### `POST /api/posts/{post}/comments` — Create comment

**Request (JSON):**
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `text` | `string` | Yes | Max 2000 characters |

**Response:** `201 Created` — Returns the created comment (no `data` wrapper, no `replies` key).

---

### `POST /api/comments/{comment}/reply` — Reply to a comment

**Request (JSON):** Same as create comment (`text`).

**Response:** `201 Created` — Returns the created reply object.

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
    "likes_count": 4
}
```

---

### `GET /api/comments/{comment}/likes` — List who liked

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
