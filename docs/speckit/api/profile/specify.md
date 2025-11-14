# Specify: [API] Profile

## Purpose
Membuat endpoint untuk profile

## Endpoint
POST /api/v1/profile

## Request Body
### POST /api/v1/profile
Request Parameters:
- token: string (required)

## Response Body
### POST /api/v1/profile
- 200 OK:
  ```json
  {
    "status": "success",
    "message": "Profile retrieved successfully",
    "data": {
      "id": 1,
      "name": "John Doe",
      "email": "john.doe@example.com",
      "roles": "admin",
      "username": "johndoe",
      "institution": "Polda Banten",
      "division": "Polda Banten",
      "url_image": "https://example.com/johndoe.jpg"
    }
  }
  ```

## Model
- User
- Institution
- Division

## Rules
- Token gunakan JWT
- Gunakan service repository pattern
- Relasi data gunakan model
- Mapping data di model