# home-api

home-api is a REST API designed to create a safe and simple learning environment for those new to APIs, and specifically REST APIs, to learn and mess around in without having to put in a credit card or offer up personal information.

This API still does require an account, and is intended to be used alongside of the free training I have created on [Project TIY Training](https://training.projecttiy.com). If you would like to use this for another purpose, you can raise an issue in this repo with your request.

## Authenticating

To get access, raise an issue in this repo or send an email to <support@projecttiy.com> requesting access.

This API offers three different methods of authentication:
- HTTP Basic
Add the `Authorization: Basic` header to any API call with a valid username:password in base64 format to use HTTP Basic.

- Access Token
POST a JSON request to `/api/v1/login` using your username and password to obtain a temporary Access Token. You can then include that Access Token in the `X-Auth-Token` Header.

- API Key
Add the `Authorization: Bearer` header to any API call with the API Key you were given on account creation to use this format.

All API calls support all three methods of authentication.

## Rate Limiting

I run this API as a free service and a free add-on to the training website I've mentioned above. With that being said, I am asking for those of you using this service to respect the resources at hand so that anyone who wants to use this service has the capacity to do so.

Rate Limits are in affect and are applied in two ways:
- /api/v1/login has a rate limit on the number of attempts to login, successful or not
- All other endpoints are rate limited per user account. Each account has 5,000 allotted calls per hour. At the 5,001st API call, you will start receiving 429 responses until that hour is up.

I am considering applying a rate limit per IP Address as well, but will only do this if I find abuse of the service.

## Documentation

All API calls are documented in the [docs](https://github.com/benowe1717/home-api/tree/main/docs) where you can find out how to build the API call, what to expect as a response to the API call, and what the API calls are for.

## Contributing to home-api

To contribute to home-api, follow these steps:

1. Fork this repository
2. Create a branch: `git checkout -b <branch_name>`
3. Make your changes and commit them: `git commit -m '<commit_message>'`
4. Push to the original branch: `git push origin <project_name>/<location>`
5. Create the Pull Request

Alternatively see the GitHub documentation on [creating a pull request](https://help.github.com/en/github/collaborating-with-issues-and-pull-requests/creating-a-pull-request).

## Contributors

Thanks to the following people who have contributed to this project:

- [Benjamin Owen](https://github.com/benowe1717)

## Contact

For help or support on this repository, follow these steps:

- Fill out an issue [here](https://github.com/benowe1717/home-api/issues)

## License

This project uses the following license: [GPLv3](https://choosealicense.com/licenses/gpl-3.0/)

## Sources

- https://github.com/scottydocs/README-template.md/blob/master/README.md
- https://choosealicense.com/
- https://www.freecodecamp.org/news/how-to-write-a-good-readme-file/
