# /api/v1/login

Provides a temporary Access Token for authentication to all other endpoints.

## Methods

- POST

## Parameters

## Body

- username: The email address of your account
- password: The password to your account

### Format
`{"username": <username>, "password": <password>}`

## Responses

- 200: Successful authentication

### Sample response
`
{
    "result": "success",
    "data": {
        "access_token": <access_token>
    }
}
`
- 401: Unauthorized
- 429: Too many authentication attempts
- 500: Server error
