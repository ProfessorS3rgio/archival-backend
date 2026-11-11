# Sangguniang Panlalawigan API usage

The archive exposes a read-only JSON API at `https://YOUR_ARCHIVE_HOST/api/v1`. Visitors to the Sangguniang Panlalawigan website need no archive account. Its **backend** uses a private API token to list, search, inspect, preview, and download **visible** documents and present them publicly. Records with `is_visible = false` return no list item and a 404 for direct reads or downloads. Draft ordinances and draft resolutions are reserved for a later publishing decision and are unavailable to integration clients.

The existing document tables default `is_visible` to `true`. Review records before enabling the public website and mark anything that should remain internal as hidden. The API's JSON serializers list fields explicitly and do not return the stored `extracted_text`, but a visible document's file can be downloaded in full.

Staff users may read and download every record. They may edit or delete only records created by their own account.

## Set up the integration

Run the new migration on the archive server before issuing a token:

```bash
php artisan migrate --force --path=database/migrations/2026_11_12_010000_create_api_clients_table.php
php artisan archival:issue-api-token archive-client@YOUR_DOMAIN --name="Sangguniang Panlalawigan" --expires=90
```

The command creates or reuses a dedicated API client and prints a token **once**. It grants only `documents:read`. Store the token in the Sangguniang Panlalawigan backend environment as `ARCHIVAL_API_TOKEN`; store the base URL as `ARCHIVAL_API_URL`. Do not put the token in browser JavaScript, a public repository, a URL, or a frontend `.env`. A subsequent command run issues another token; revoke the old token after the new one is working. The client has no password or web login.

Send these headers on every request, including file requests:

```http
Authorization: Bearer YOUR_TOKEN
Accept: application/json
```

`401` means the token is missing, invalid, or expired; `403` means it lacks `documents:read`; `404` means the item is absent, not visible, or a draft reserved from this integration; `422` means a parameter failed validation; `429` means the rate limit was reached. The v1 limit is 120 requests per minute per client. Retry after `Retry-After` on `429`.

## Endpoints

The type names below work with `GET /api/v1/documents/{type}`. The endpoint `GET /api/v1/documents/types` returns available names. The capitalized forms under `/api/v1/Documents/` are also supported; use the lowercase type endpoints for new list integrations.

| Type | Document |
| --- | --- |
| `resolutions` | Resolutions |
| `ordinances` | Ordinances |
| `accomplishments` | Administrative cases |
| `projects` | Boundary disputes |
| `moas` | Memorandums of agreement |
| `committee-reports` | Committee reports |
| `municipal-communications` | Municipal communications |
| `national-offices` | National office communications |
| `provincial-offices` | Provincial office communications |

## Sangguniang Panlalawigan officials

The same backend token can read officials and profile pictures. These endpoints
are read-only:

```text
GET /api/v1/sangguniang-panlalawigan/terms
GET /api/v1/sangguniang-panlalawigan/{term_no}/officials
GET /api/v1/sangguniang-panlalawigan/{term_no}/officials/{member_id}
GET /api/v1/sangguniang-panlalawigan/{term_no}/officials/{member_id}/picture
```

The official response contains the member's name, extension, position, district,
term, and optional picture filename, MIME type, and authenticated picture URL.
The Sangguniang backend should request the JSON and image with its private
token, then cache or stream the result to public visitors. Archive storage
credentials are never needed in the public website.

For a single record, use the `GET /api/v1/Documents/{SingularType}/{id}` route, for example `GET /api/v1/Documents/Resolution/42` or `GET /api/v1/Documents/Project/42`. The list response contains each document's `id` and its `files` with authenticated `download_url` and `preview_url` values. Follow those URLs from the backend with the same bearer token. Do not give the bare URL to a browser unless that browser also has an authorized archive session.

## Search and pagination

List endpoints accept `search`, `year`, `page`, and `per_page`. Some types also accept `author`, `status`, `type`, `district`, `source_fund`, or `municipality`. `per_page` defaults to 15 and must be an integer from 1 to 100. Use the returned `data.links.next` to fetch subsequent pages. Search may match indexed document text, but extracted text is not returned in the JSON payload.

Example:

```bash
curl --fail-with-body \
  -H "Authorization: Bearer $ARCHIVAL_API_TOKEN" \
  -H 'Accept: application/json' \
  "$ARCHIVAL_API_URL/documents/resolutions?search=budget&per_page=25&page=1"
```

Response shape:

```json
{
  "success": true,
  "data": {
    "items": [{ "id": 42, "title": "Example", "files": [{ "download_url": "https://YOUR_ARCHIVE_HOST/api/v1/Documents/Resolution/42/files/7/download", "preview_url": "https://YOUR_ARCHIVE_HOST/api/v1/Documents/Resolution/42/files/7/download?preview=1" }] }],
    "meta": { "current_page": 1, "per_page": 25, "total": 1, "last_page": 1, "from": 1, "to": 1 },
    "links": { "first": "...", "last": "...", "prev": null, "next": null }
  },
  "message": "Resolutions retrieved successfully"
}
```

Download or preview through the Sangguniang backend, which can stream the returned bytes to a public visitor after applying its own cache and response headers:

```bash
curl --fail-with-body \
  -H "Authorization: Bearer $ARCHIVAL_API_TOKEN" \
  -H 'Accept: application/pdf' \
  -o resolution.pdf \
  "$ARCHIVAL_API_URL/Documents/Resolution/42/files/7/download"
```

The `?preview=1` URL returns the same file with inline disposition. File bytes are served from private storage; there is no public object URL. Keep the returned file name and MIME type for display.

## Rollout and rotation

Deploy the code and run migrations first. Issue a token on the archive server, configure the Sangguniang backend environment, then test `GET /api/v1/documents/types`, a list query, a visible record, a hidden record (404), and a download. Restart the consumer after changing its environment. For rotation, issue a new token, switch the consumer, verify it, then delete the old entry from `personal_access_tokens` for that API client. Keep the archive `.env` and the consumer `.env` separate.

The detailed field and route reference is in [API_DOCUMENTATION.md](API_DOCUMENTATION.md).
