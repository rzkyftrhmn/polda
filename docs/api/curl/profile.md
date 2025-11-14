# Profile API (cURL)

Endpoint: POST /api/v1/profile

Authorization: gunakan header Bearer token
Request Body: tidak diperlukan

Example:

curl -X POST \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <JWT_TOKEN_HERE>" \
  http://localhost:8080/api/v1/profile

Response (200):
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