# Logout API (cURL)

Endpoint: POST /api/v1/logout

Authorization: gunakan header Bearer token
Request Body: tidak diperlukan

Example:

curl -X POST \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <JWT_TOKEN_HERE>" \
  http://localhost:8080/api/v1/logout

Response (200):
{
  "status": "success",
  "message": "Logout successful",
  "data": null
}

Response (400):
{
  "status": "error",
  "message": "Invalid token",
  "errors": []
}