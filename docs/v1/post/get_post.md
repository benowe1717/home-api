# /api/v1/post/id

Returns a single Post by the ID

## Methods

- GET

## Parameters

## Body

## Responses

- 200: Successful return of Post

### Sample response
`
{
    "id": 1 # The ID of the Post
    "created_at": "YYYY-MM-DDTHH:mm+TZ:TZ" # The datetime of when the Post was created
    "updated_at": "YYYY-MM-DDTHH:mm+TZ:TZ" # The datetime of when the Post was updated 
    "author": "parts@domain.tld" # The email address of the User that created the Post
    "content": "This is a Post!" # The content of the Post
    }
}
`

- 400: Bad Request

### Sample response
`
{
    "result": "failed",
    "reason": "<reason>"
}
`

- 401: Unauthorized
- 429: Rate limit exceeded
- 500: Server error
