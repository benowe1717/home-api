# /api/v1/posts

Returns all Posts

## Methods

- GET

## Parameters

- limit
    - Default Value: 10
    - Minimum Value: 1
    - Maximum value: 100

- page
    - Default Value: 1

### Examples

To return only 5 Posts at a time:
`/api/v1/posts?limit=5`

To return the 4th page of results:
`/api/v1/posts?page=4`

These can be combined, to say, result the 2nd page of 50 results:
`/api/v1/posts?limit=50&page=2`

## Body

## Responses

- 200: Successful return of Posts

### Sample response
`
{
    "total": 10,
    "pages": 1,
    "limit": 10,
    "data": [{
        "id": 1 # The ID of the Post
        "created_at": "YYYY-MM-DDTHH:mm+TZ:TZ" # The datetime of when the Post was created
        "updated_at": "YYYY-MM-DDTHH:mm+TZ:TZ" # The datetime of when the Post was updated 
        "author": "parts@domain.tld" # The email address of the User that created the Post
        "content": "This is a Post!" # The content of the Post
    }]
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
