# Login API (cURL)

Endpoint: POST /api/v1/login

Request Body (JSON):
{
  "username": "user@example.com",
  "password": "secret"
}

Example:

curl -X POST \
  -H "Content-Type: application/json" \
  -d '{"username":"user@example.com","password":"secret"}' \
  http://localhost:8080/api/v1/login

Response (200):
{
  "status": "success",
  "message": "Login successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin",
      "email": "admin@example.com",
      "role": "admin",
      "url_image": "https://..."
    },
    "token": "<JWT>"
  }
}

Response (401):
{
  "status": "error",
  "message": "Invalid credentials",
  "errors": []
}