Users API Methods
===================

List of users
---------------

 - *Description*: Returns the list of users
 - *Access*: authorized only
 - *URL*: `/api/users`
 - *Method*: `GET`

 - *Query (GET) params*:

    * `fields`: string, specified comma separated list of fields that should be returned
    * `expand`: string, comma separated list of extra fields that should be returned too.
        See `Response Data` section below to know which of fields are extra.
    * `per-page`: integer, between 1 and 50 (default is 20), count of items per page
    * `page`: integer, page number
    * `sort`: string, the name of sort field, you may add prefix `-` before it to set descending order by this field.
        Possible sort fields:

        * `id`
        * `username`
        * `email`
        * `phone` 
        * `role` 
        * `description`
        * `twitter`
        * `facebook`
        * `google`
        * `linkedin`
        * `createdAt`
        * `updatedAt`

 - *Query URL example*: `/api/users?fields=id,username&expand=phone,description`

 - *Response Headers*:

    * `X-Pagination-Current-Page`: integer, the number of current page
    * `X-Pagination-Page-Count`: integer, total pages count
    * `X-Pagination-Per-Page`: integer, current `per-page` value (may be not equal to real count of returned items)
    * `X-Pagination-Total-Count`: integer, total items count
    * `Link`: string, you may see examples of prev/self/next pages here, [specification](https://tools.ietf.org/html/rfc5988).

 - *Response Data*:

    * `items`: array of user s, each of them may keeps key-value pairs:

        * `id`: integer
        * `username`: string
        * `email`: string
        * `token`: string
        * `role`: string (`inverstor` for now)
        * `phone`: string|null
        * `description`: string|null
        * `twitter`: string|null
        * `facebook`: string|null
        * `google`: string|null
        * `linkedin`: string|null
        * `companiesIds`: array of `company.id` that user has access
        * `createdAt`: integer
        * `updatedAt`: integer
    
    * `_meta`: object of pagination:
    
        * `totalCount`: integer
        * `pageCount`: integer
        * `currentPage`: integer
        * `perPage`: integer
    * `_links`: object, you may see examples of prev/self/next pages here, [specification](https://tools.ietf.org/html/rfc5988
 
 - *Response Example*:

```json
{
  "items": [
    {
      "id": 2,
      "username": "test1",
      "email": "test@fin.dev",
      "role": "investor",
      "token": "Aqtkq9dT",
      "phone": null,
      "description": null,
      "twitter": "http://twitter.com/",
      "facebook": null,
      "google": null,
      "companiesIds": [
          "2",
          "7"
      ],
      "linkedin": null,
      "createdAt": 1480433813,
      "updatedAt": 1480433841
    },
  ...
  ],
  "_links": {
      "self": {
        "href": "http://finance-fox.dev/api/users?page=1"
      }
    },
  "_meta": {
      "totalCount": 3,
      "pageCount": 1,
      "currentPage": 1,
      "perPage": 20
  }
}
```

View User
-----------

 - *Access*: authorized only
 - *URL*: `/api/users/<id>`
 - *URL params*:

    * `<id>`: integer, user's ID

 - *Method*: `GET`
 - *Note*: Server will throw 404 exception if user was not found

 - *Response Data*:

    * object, it keeps key-value pairs:

        * `id`: integer
        * `username`: string
        * `email`: string
        * `token`: string
        * `role`: string (`inverstor` for now)
        * `phone`: string|null
        * `description`: string|null
        * `twitter`: string|null
        * `facebook`: string|null
        * `google`: string|null
        * `linkedin`: string|null
        * `companiesIds`: array
        * `createdAt`: integer, [UNIX timestamp](https://en.wikipedia.org/wiki/Unix_time) of report creating on the server
        * `updatedAt`: integer, [UNIX timestamp](https://en.wikipedia.org/wiki/Unix_time) of the last report updating on the server


Create User
-----------
 - *Access*: authorized only
 - *URL*: `/api/users`
 - *Method*: `POST`

 - *POST params*:
    
    * username: string(255)|unique|required
    * email: string|email|unique|required
    * password: string(255)|required
    * phone: string(255)
    * description: string(65535)
    * twitter: string(255)
    * facebook: string(255)
    * companiesIds: array of `company.id`
    * google: string(255)
    * linkedin: string(255)
    * sendUserPasswordEmail: bool, whether it is need to send email to user, default false.
    
 - *Note*: Server will throw 422 status code if validation fails or 201, when user was created
 - *Response Data*:
    
    * if success: object, it keeps key-value pairs:
    
        * `id`: integer
        * `username`: string
        * `email`: string
        * `token`: string
        * `role`: string (`inverstor` for now)
        * `phone`: string|null
        * `description`: string|null
        * `twitter`: string|null
        * `facebook`: string|null
        * `companiesIds`: array of `company.id`
        * `google`: string|null
        * `linkedin`: string|null
        * `createdAt`: integer, [UNIX timestamp](https://en.wikipedia.org/wiki/Unix_time) of report creating on the server
        * `updatedAt`: integer, [UNIX timestamp](https://en.wikipedia.org/wiki/Unix_time) of the last report updating on the server
    
    * if validation fails: array of objects with properties:
        * `field`: name of  attribute
        * `message`: string of error
        
        
        
Update User
-----------
 - *Access*: authorized only
 - *URL*: `/api/users/<id>`
 - *Method*: `PATCH` or `PUT`

 - *Note*: Server will throw 422 status code if validation fails or 200, when user was updated
 - *POST params*: same as in `Create User`
 - *Response Data*: same as in `Create User`