# /api/v1/post/create

Create a Post

## Methods

- POST

## Parameters

## Body

- content: "The content of the Post" # This has a limit of 140 characters

### Format
`
{
    "content": "This is a Post!"
}
`

## Responses

- 200: Successful creation of Post

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
