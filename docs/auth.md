Auth Methods
====================

Sign In Method
--------------

 - *Description*: Login
 - *URL*: `/api/auth/signin`
 - *Method*: `POST`
 - *Request Data*:

    * `code`: string, code verification


 - *Response Data*:

    * true or false

*Request Example*:

```json
{"code": "12345"}
```

*Success Response Example*:

```json
{
  true
}
```

Change Registration Method
--------------------------

 - *Description*: Create code verification
 - *URL*: `/api/auth/registration`
 - *Method*: `POST`
 - *Request Data*:

    * `name`: string, user's name
    * `phone`: string, user's phone required


 - *Response Data*:

*Request Example*:

```json
{"code": "admin"}
```
