# Event — API v1

## POST /api/v1/event

Buat event baru beserta daftar peserta (participants).

Contoh:

```bash
curl -X POST \
  -H 'Authorization: Bearer <JWT_TOKEN>' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  'http://localhost:8080/api/v1/event' \
  -d '{
    "name": "Apel Gelar Pasukan",
    "location": "Lapangan Mapolda",
    "description": "Kegiatan apel se-Banten",
    "start_at": "2025-12-01T07:00:00Z",
    "end_at": "2025-12-01T09:00:00Z",
    "participants": [
      { "division_id": 101, "is_required": true, "note": "Wajib hadir" },
      { "division_id": 202, "is_required": false }
    ]
  }'
```

Response 200:

```json
{
  "status": "success",
  "message": "Event created successfully",
  "errors": null,
  "data": []
}
```

## GET /api/v1/event

Ambil daftar event, dukung pencarian dengan query `search`.

Contoh:

```bash
curl -X GET \
  -H 'Authorization: Bearer <JWT_TOKEN>' \
  -H 'Accept: application/json' \
  'http://localhost:8080/api/v1/event?search=apel'
```

Response 200:

```json
{
  "status": "success",
  "message": "Event retrieved successfully",
  "errors": null,
  "data": [
    {
      "id": 1,
      "uuid": "f4c1e1f8-7eaa-4c7a-bc91-4f282f1b91f3",
      "name": "Apel Gelar Pasukan",
      "location": "Lapangan Mapolda",
      "description": "Kegiatan apel se-Banten",
      "start_at": "2025-12-01T07:00:00.000Z",
      "end_at": "2025-12-01T09:00:00.000Z",
      "total_participants": 2,
      "created_at": "2025-11-26T00:00:00.000Z",
      "updated_at": "2025-11-26T00:00:00.000Z"
    }
  ]
}
```

## GET /api/v1/event/{event_uuid}

Ambil detail event berdasarkan `event_uuid`, beserta daftar peserta dan status upload.

Contoh:

```bash
curl -X GET \
  -H 'Authorization: Bearer <JWT_TOKEN>' \
  -H 'Accept: application/json' \
  'http://localhost:8080/api/v1/event/f4c1e1f8-7eaa-4c7a-bc91-4f282f1b91f3'
```

Response 200:

```json
{
  "status": "success",
  "message": "Event retrieved successfully",
  "errors": null,
  "data": {
    "id": 1,
    "uuid": "f4c1e1f8-7eaa-4c7a-bc91-4f282f1b91f3",
    "name": "Apel Gelar Pasukan",
    "location": "Lapangan Mapolda",
    "description": "Kegiatan apel se-Banten",
    "start_at": "2025-12-01T07:00:00.000Z",
    "end_at": "2025-12-01T09:00:00.000Z",
    "total_participants": 2,
    "participants": [
      {
        "id": 10,
        "division_id": 101,
        "is_required": true,
        "note": "Wajib hadir",
        "status": "Sudah upload",
        "created_at": "2025-11-26T00:00:00.000Z",
        "updated_at": "2025-11-26T00:00:00.000Z"
      },
      {
        "id": 11,
        "division_id": 202,
        "is_required": false,
        "note": null,
        "status": "Belum upload",
        "created_at": "2025-11-26T00:00:00.000Z",
        "updated_at": "2025-11-26T00:00:00.000Z"
      }
    ],
    "created_at": "2025-11-26T00:00:00.000Z",
    "updated_at": "2025-11-26T00:00:00.000Z"
  }
}
```

## PUT /api/v1/event/{event_uuid}

Perbarui event beserta daftar peserta.

Contoh:

```bash
curl -X PUT \
  -H 'Authorization: Bearer <JWT_TOKEN>' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  'http://localhost:8080/api/v1/event/f4c1e1f8-7eaa-4c7a-bc91-4f282f1b91f3' \
  -d '{
    "name": "Apel Gelar Pasukan (Update)",
    "location": "Lapangan Mapolda",
    "description": "Kegiatan apel se-Banten",
    "start_at": "2025-12-01T07:00:00Z",
    "end_at": "2025-12-01T09:30:00Z",
    "participants": [
      { "division_id": 101, "is_required": true },
      { "division_id": 303, "is_required": false, "note": "Opsional" }
    ]
  }'
```

Response 200:

```json
{
  "status": "success",
  "message": "Event updated successfully",
  "errors": null,
  "data": []
}
```

## DELETE /api/v1/event/{event_uuid}

Hapus event berdasarkan `event_uuid`.

Contoh:

```bash
curl -X DELETE \
  -H 'Authorization: Bearer <JWT_TOKEN>' \
  -H 'Accept: application/json' \
  'http://localhost:8080/api/v1/event/f4c1e1f8-7eaa-4c7a-bc91-4f282f1b91f3'
```

Response 200:

```json
{
  "status": "success",
  "message": "Event deleted successfully",
  "errors": null,
  "data": []
}
```

## GET /api/v1/event/users/{event_uuid}

Ambil daftar `user_id` yang terkait dengan event (berdasarkan `division_id` pada participants).

Contoh:

```bash
curl -X GET \
  -H 'Authorization: Bearer <JWT_TOKEN>' \
  -H 'Accept: application/json' \
  'http://localhost:8080/api/v1/event/users/f4c1e1f8-7eaa-4c7a-bc91-4f282f1b91f3'
```

Response 200:

```json
{
  "status": "success",
  "message": "Users retrieved successfully",
  "errors": null,
  "data": [12, 34, 56]
}
```
