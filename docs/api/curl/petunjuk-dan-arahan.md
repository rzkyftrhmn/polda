# Petunjuk dan Arahan — API v1

## GET /api/v1/petunjuk-dan-arahan/{report_uuid}

Ambil daftar petunjuk dan arahan berdasarkan `report_uuid` (path parameter).

Contoh:

```bash
curl -X GET \
  'http://localhost/api/v1/petunjuk-dan-arahan/2f0f7f4b-1f4e-4b73-8c2a-9b5b9c0e5f10' \
  -H 'Authorization: Bearer <JWT_TOKEN>' \
  -H 'Accept: application/json'
```

Response 200:

```json
{
  "status": "success",
  "message": "Petunjuk dan arahan retrieved successfully",
  "errors": null,
  "data": [
    {
      "id": 1,
      "report_uuid": "2f0f7f4b-1f4e-4b73-8c2a-9b5b9c0e5f10",
      "user": {
        "name": "John Doe",
        "email": "john.doe@example.com",
        "division": {
          "name": "Polda Banten"
        }
      },
      "message": "Segera koordinasikan dengan Unit Provos",
      "created_at": "2025-01-01T00:00:00.000Z",
      "updated_at": "2025-01-01T00:00:00.000Z"
    }
  ]
}
```

## GET /api/v1/petunjuk-dan-arahan/users/{report_uuid}

Ambil daftar user yang dapat menerima instruksi untuk laporan tertentu.
Sumber data: gabungan user yang memiliki akses dari `access_datas` + user dengan role Admin (guard `web`).

Contoh:

```bash
curl -X GET \
  'http://localhost/api/v1/petunjuk-dan-arahan/users/2f0f7f4b-1f4e-4b73-8c2a-9b5b9c0e5f10' \
  -H 'Authorization: Bearer <JWT_TOKEN>' \
  -H 'Accept: application/json'
```

Response 200:

```json
{
  "status": "success",
  "message": "Users retrieved successfully",
  "errors": null,
  "data": [
    {
      "id": 12,
      "name": "Jane Doe",
      "email": "jane.doe@example.com",
      "division": {
        "name": "Unit Propam Provos - Polres Cianjur"
      }
    }
  ]
}
```

## POST /api/v1/petunjuk-dan-arahan

Buat petunjuk dan arahan baru untuk sebuah laporan.

Contoh:

```bash
curl -X POST 'http://localhost/api/v1/petunjuk-dan-arahan' \
  -H 'Authorization: Bearer <JWT_TOKEN>' \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{
    "report_uuid": "2f0f7f4b-1f4e-4b73-8c2a-9b5b9c0e5f10",
    "user_id": 123,
    "message": "Segera koordinasikan dengan Unit Provos"
  }'
```

Body:

```json
{
  "report_uuid": "string",
  "user_id": 1,
  "message": "string"
}
```

Response 200:

```json
{
  "status": "success",
  "message": "Petunjuk dan arahan retrieved successfully",
  "errors": null,
  "data": []
}
```
