# /api/v1/post/id

Create a Post

## Methods

- PUT

## Parameters

## Body

- content: "The content of the Post" # This has a limit of 140 characters

NOTE: This is not an edit in-place. You are overwriting the content that exists with the content of the request.

### Format
`
{
    "content": "This is a Post that I updated!"
}
`

## Responses

- 200: Successful update of Post

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
