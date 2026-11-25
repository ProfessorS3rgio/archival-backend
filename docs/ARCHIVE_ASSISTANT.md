# Sangguniang Panlalawigan Archive Assistant

This project uses a button-first assistant for public archive searches. Menu actions are deterministic database queries, so they are fast, auditable, and easy to reuse in another Sangguniang Panlalawigan system or Facebook Messenger integration.

## Design

1. The user selects a menu button.
2. The client sends an `intent` and a small `input` object to the assistant endpoint.
3. Laravel validates the intent and queries only visible, approved records through the local read-only MySQL connection.
4. The API returns a short message and structured document items.
5. The UI renders the results and links to the public document page.

The assistant does not require an LLM for these menu actions. The public/admin assistant page is button-only; the legacy free-text RAG endpoint is retained server-side temporarily so existing integrations do not break while they migrate.

## Endpoint

`POST /api/archive-chat/assistant`

Example:

```json
{
  "intent": "resolution_search",
  "input": { "query": "on the job trainees" }
}
```

Supported intents:

- `recent_resolutions`
- `recent_ordinances`
- `resolution_search` and `ordinance_search` (title, subject, author, and extracted text)
- `resolution_by_number` and `ordinance_by_number`
- `records_by_year`

Responses contain `message` and an `items` array. Each item includes the record type, ID, document number, title, approval date, and public URL. Only `is_visible = 1` records with a non-null approval date are returned.

## Facebook Messenger reuse

Messenger buttons should send the same intent names as postback payloads. The webhook can forward the payload to this endpoint and format the returned `items` as Messenger buttons or generic templates. Keep the endpoint behind authentication or a signed webhook between the Messenger adapter and Laravel; never expose database credentials to the client.

## Adding a menu action

1. Add the intent to the controller validation allow-list.
2. Add a method or `match` arm in `ArchiveAssistant`.
3. Return the standard `{ intent, message, items }` shape.
4. Add the button to `Settings/Chatbot.vue`.
5. Add an API test using a local benchmark database.

## Performance notes

- Use indexed equality/date filters for document numbers and years.
- Use the FULLTEXT indexes for title/topic searches; the service has a `LIKE` fallback for older schemas.
- Cap result counts and validate all input.
- Keep the connection local/read-only and do not query production directly from the public page.
