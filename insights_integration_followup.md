# Insights Integration Follow-up

This note is a small follow-up after reviewing `insights_integration_notes.md`.

The service is broadly aligned already. These are only contract clarifications to tighten before BFF integration expands.

## 1. Add Machine-Readable Error Codes To Error Responses

Please document stable `code` fields on important error responses, not just status codes and free-text messages.

This matters because:

- BFF will need to log and classify upstream failures consistently
- future Flutter-facing behavior may depend on normalized BFF error handling
- machine-readable error codes are more stable than matching message text

Recommended examples:

### Unauthorized

```json
{
  "message": "Unauthorized.",
  "code": "UNAUTHORIZED"
}
```

### Validation error

```json
{
  "message": "The events.0.unexpected_field field is not allowed.",
  "code": "VALIDATION_ERROR",
  "errors": {
    "events.0.unexpected_field": [
      "The events.0.unexpected_field field is not allowed."
    ]
  }
}
```

### Placement resolution failure

```json
{
  "message": "Placement resolution failed.",
  "code": "PLACEMENT_RESOLUTION_FAILED"
}
```

Suggested error codes to document at minimum:

- `UNAUTHORIZED`
- `VALIDATION_ERROR`
- `EVENT_BATCH_INVALID`
- `EVENT_BATCH_TOO_LARGE`
- `PLACEMENT_RESOLUTION_FAILED`
- `REPORT_NOT_FOUND` if applicable

## 2. Make The Admin Web Auth Boundary Explicit

The current note already says:

- `/api/v1/*` uses `X-API-Key`
- web admin uses Laravel session auth

Please make that boundary explicit so there is no confusion during implementation.

Recommended clarification:

- service APIs remain API-key protected for BFF and trusted backend consumers
- admin web routes use session auth for human operators
- the browser-based admin should not depend on the service API key
- the admin UI can call internal application services/controllers directly rather than behaving like an API-key client

This is mostly documentation clarity, not a new feature request.

## 3. Clarify Partial Placement Resolution Behavior

Please document explicitly how `POST /api/v1/placements/resolve` behaves when:

- multiple placement codes are requested
- only some of them have eligible campaigns

Recommended behavior:

- return only the placements that resolved successfully
- omit slots with no eligible campaign
- do not fail the whole response because one slot has no eligible campaign

Example:

Requested:

```json
{
  "placements": [
    "home_inline_1",
    "home_inline_2",
    "watch_inline_1"
  ]
}
```

Valid response:

```json
{
  "placements": [
    {
      "placement_id": "home_inline_1",
      "served_event": {
        "event_name": "campaign_served",
        "event_id": "served_123",
        "campaign_id": "cmp_2026_001",
        "creative_id": "creative_01",
        "delivery_id": "delivery_123"
      },
      "creative": {
        "campaign_id": "cmp_2026_001",
        "creative_id": "creative_01",
        "creative_type": "sponsor_card",
        "label_text": "Sponsored",
        "title": "Partner Message",
        "body": "Example body",
        "image_url": "https://cdn.example.com/image.jpg",
        "logo_url": null,
        "cta_text": "Learn more",
        "cta_url": "https://example.com",
        "metadata": null
      }
    }
  ]
}
```

This will let BFF fail open cleanly while still injecting any placements that do resolve.

## Summary

No major architectural change is being requested.

This follow-up only asks for:

1. stable machine-readable error codes
2. a clearer admin-auth boundary
3. explicit partial-success behavior for placement resolution
