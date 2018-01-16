API documentation for application
=============================================

Documentation Sections
----------------------

- [Auth API Methods](auth.md)
- [User API methods](users.md)
- [Company API methods](companies.md)

The general logic for all requests:
-----------------------------------

- The method returns `token` in the response if signing in/up was successful.
In the future this token must be passed as a header with name `Authorization` in each request.
Example:
```
Authorization: Bearer RECEIVED-TOKEN-HASH-HERE
```

- Server will return exception with `name` === `Unauthorized`
if user was not authorized and tried to perform any private method.
This exception also will be thrown when auth token was expired.

- Server allows the followings content types for POST/PUT methods:

    * `application/json` (recommended)
    * `application/x-www-form-urlencoded`
    * `multipart/form-data`

- When any server error will be occurred server will return non-20* HTTP status
and json object with elements:

    * `message`: string, error message text. Only errors with 422 code may be shown to the end user.
    * `errors`: array (optional), array of occurred errors, ONLY for developers

- HTTP Status Codes:

    * `200 Ok`: Successful GET, PUT, PATCH or DELETE call
    * `201 Created`: New resource added successfully
    * `400 Bad Request`: Indicates that the server cannot or will not process the request due to something which is perceived to be a client error
    * `401 Unauthorized`: Authentication credentials were missing or incorrect.
    * `403 Forbidden`: The request is understood, but it has been refused or access is not allowed. An accompanying error message will explain why.
    * `422 Data Validation Failed`: Business logic error
    * `50* Internal Server Error`: Any server-side problem
