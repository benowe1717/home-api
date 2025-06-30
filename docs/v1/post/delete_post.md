# /api/v1/post/id

Create a Post

## Methods

- DELETE

## Parameters

## Body

## Responses

- 204: Successful deletion of Post

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
