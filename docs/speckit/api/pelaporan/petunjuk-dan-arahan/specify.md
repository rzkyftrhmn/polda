# Specify: [API] Petunjuk dan Arahan

## Purpose
Membuat endpoint untuk petunjuk dan arahan

## Endpoint
POST /api/v1/petunjuk-dan-arahan
GET /api/v1/petunjuk-dan-arahan/{report_uuid}
GET /api/v1/petunjuk-dan-arahan/users/{report_uuid}

## Request Body
### POST /api/v1/petunjuk-dan-arahan
Request Body:
```json
{
  "report_uuid": "string",
  "user_id": 1,
  "message": "string"
}
```

### GET /api/v1/petunjuk-dan-arahan/{report_uuid}
Request Parameters:
- report_uuid: string (required)

### GET /api/v1/petunjuk-dan-arahan/users/{report_uuid}
Request Parameters:
- report_uuid: string (required)

## Response Body
### POST /api/v1/petunjuk-dan-arahan
- 200 OK:
  ```json
  {
    "status": "success",
    "message": "Petunjuk dan arahan retrieved successfully",
    "data": null
  }
  ```

### GET /api/v1/petunjuk-dan-arahan
- 200 OK:
  ```json
  {
    "status": "success",
    "message": "Petunjuk dan arahan retrieved successfully",
    "data": [
      {
        "id": 1,
        "report_uuid": "string",
        "user": {
          "name": "John Doe",
          "email": "john.doe@example.com",
          "division": {
            "name": "Polda Banten",
          }
        },
        "message": "string",
        "created_at": "2025-01-01T00:00:00.000Z",
        "updated_at": "2025-01-01T00:00:00.000Z"
      }
    ]
  }
  ```

### GET /api/v1/petunjuk-dan-arahan/users/{report_uuid}
- 200 OK:
  ```json
  {
    "status": "success",
    "message": "Petunjuk dan arahan retrieved successfully",
    "data": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john.doe@example.com",
      }
    ]
  }
  ```

## Model
- InstructionsAndDirection
- AccessData

## Rules
- Token gunakan JWT
- Gunakan service repository pattern
- Relasi data gunakan model
- Mapping data di model
- Get Users selain yang ada di table access_datas, juga get users role Admin