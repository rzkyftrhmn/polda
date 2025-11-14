# Specify: [API] Login

## Purpose
Membuat endpoint untuk login

## Endpoint
POST /api/v1/login

## Request Body
### POST /api/v1/login
Request Parameters:
- username: string (required)
- password: string (required)

## Response Body
### POST /api/v1/login
- 200 OK:
  ```json
  {
    "status": "success",
    "message": "Login successful",
    "data": {
      "user": {
        "id": 1,
        "name": "Taufik Pirdian",
        "email": "pik@example.com",
        "role": "admin",
        "url_image": "https://example.com/image.jpg",
      },
      "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6..."
    }
  }
  ```

## Model
- User

## Rules
- Token gunakan JWT
- Gunakan service repository pattern
- Relasi data gunakan model
- Mapping data di model
- login username by email or username
- Buatkan Helper Formatter supaya response body menjadi lebih rapi dan usable