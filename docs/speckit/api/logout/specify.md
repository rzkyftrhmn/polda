# Specify: [API] Logout

## Purpose
Membuat endpoint untuk logout

## Endpoint
POST /api/v1/logout

## Request Body
### POST /api/v1/logout
Request Parameters:
- token: string (required)

## Response Body
### POST /api/v1/logout
- 200 OK:
  ```json
  {
    "status": "success",
    "message": "Logout successful",
    "data": null
  }
  ```

## Model
- User

## Rules
- Token gunakan JWT
- Gunakan service repository pattern
- Relasi data gunakan model
- Mapping data di model