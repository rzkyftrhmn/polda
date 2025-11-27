# Specify: [API] Event

## Purpose
Membuat endpoint untuk event crud

## Endpoint
POST /api/v1/event
GET /api/v1/event
GET /api/v1/event/users/{event_uuid}
DELETE /api/v1/event/{event_uuid}
PUT /api/v1/event/{event_uuid}

## Request Body
### POST /api/v1/event
Request Body:
```json
{
  "name": "string",
  "location": 1,
  "description": "string",
  "start_at": "2025-01-01T00:00:00.000Z",
  "end_at": "2025-01-01T00:00:00.000Z",
  "participants": [
    {
      "division_id": 1,
      "is_required": true,
      "note": "string"
    }
  ]
}
```

### GET /api/v1/event
Request Query:
- search: string (optional)

### GET /api/v1/event/{event_uuid}
Request Query:
- event_uuid: string (required)

### DELETE /api/v1/event/{event_uuid}
Request Query:
- event_uuid: string (required)

### PUT /api/v1/event/{event_uuid}
Request Query:
- event_uuid: string (required)
Request Body:
```json
{
  "name": "string",
  "location": "string",
  "description": "string",
  "start_at": "2025-01-01T00:00:00.000Z",
  "end_at": "2025-01-01T00:00:00.000Z",
  "participants": [
    {
      "division_id": 1,
      "is_required": true,
      "note": "string"
    }
  ]
}
```

## Response Body
### POST /api/v1/event
- 200 OK:
  ```json
  {
    "status": "success",
    "message": "Event created successfully",
    "data": null
  }
  ```

### GET /api/v1/event
- 200 OK:
  ```json
  {
    "status": "success",
    "message": "Event retrieved successfully",
    "data": [
      {
        "id": 1,
        "name": "string",
        "location": "string",
        "description": "string",
        "start_at": "2025-01-01T00:00:00.000Z",
        "end_at": "2025-01-01T00:00:00.000Z",
        "total_participants": 1,
        "created_at": "2025-01-01T00:00:00.000Z",
        "updated_at": "2025-01-01T00:00:00.000Z"
      }
    ]
  }
  ```

### GET /api/v1/event/{event_uuid}
- 200 OK:
  ```json
  {
    "status": "success",
    "message": "Event retrieved successfully",
    "data": [
      {
        "id": 1,
        "name": "string",
        "location": "string",
        "description": "string",
        "start_at": "2025-01-01T00:00:00.000Z",
        "end_at": "2025-01-01T00:00:00.000Z",
        "total_participants": 1,
        "participants": [
          {
            "id": 1,
            "division_id": 1,
            "is_required": true,
            "note": "string",
            "status": "string", // Belum upload, Sudah upload
            "created_at": "2025-01-01T00:00:00.000Z",
            "updated_at": "2025-01-01T00:00:00.000Z"
          }
        ],
        "created_at": "2025-01-01T00:00:00.000Z",
        "updated_at": "2025-01-01T00:00:00.000Z"
      }
    ]
  }
  ```

### DELETE /api/v1/event/{event_uuid}
- 200 OK:
  ```json
  {
    "status": "success",
    "message": "Event deleted successfully",
    "data": null
  }
  ```

### PUT /api/v1/event/{event_uuid}
- 200 OK:
  ```json
  {
    "status": "success",
    "message": "Event updated successfully",
    "data": null
  }
  ```

## Database
- events: `id`, `uuid`, `name`, `description`, `location`, `start_at`, `end_at`, `created_by`, `created_at`, `updated_at`
- event_participants: `id`, `event_id`, `division_id`, `is_required`, `note`, `created_at`, `updated_at`
