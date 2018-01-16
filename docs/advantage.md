Advantage Methods
================

List of advantages
------------------

- *Description*: Returns the list of advantages
- *Access*: authorized only
- *URL*: `/api/advantages`
- *Method*: `GET`

- *Query (GET) params*:

- *Response Data*:

* `id`: integer
* `name`: string
* `icon`: string

- *Response Example*:

```json
[
  {
    "id": 1,
    "name": "Отопление",
    "icon": null
  },
  {
    "id": 2,
    "name": "Лифт",
    "icon": null
  }
]
```

View Advantage
--------------

 - *Access*: authorized only
 - *URL*: `/api/advantages/<id>`
 - *URL params*:

    * `<id>`: integer, advantage ID

 - *Method*: `GET`
 - *Note*: Server will throw 404 exception if company was not found

 - *Response Data*:

    * object, it keeps key-value pairs:

        * `id`: integer
        * `name`: string
        * `icon`: boolean

Create Advantage
----------------
 - *Access*: authorized only
 - *URL*: `/api/advantages`
 - *Method*: `POST`

 - *POST params*:
    
    * `name`: string(255)|required
    * `icon`: string (hash-value)
        
 - *Note*: Server will throw 422 status code if validation fails or 201, when user was created
 - *Response Data*:
    
    * if success: object of advantage
    
    * if validation fails: array of objects with properties:
        * `field`: name of  attribute
        * `message`: string of error
        


Upload image
-----------------------------
 - *Access*: authorized only
 - *URL*: `/api/advantages/upload-image`
 - *Method*: `POST`
#####Request content type: [`multipart/form-data`](https://ru.wikipedia.org/wiki/Multipart/form-data)

#####Description:

This method must be used for uploading pictures on the server. Than in other 
API methods for changing photos or pictures must be used hash-key returned by this method.

#####POST parameters:

* `file`: file, picture that must be saved on the server

#####Response: 

* `success`: boolean, true if success,
* `file`: object, with the followings keys:
    * `value`: string, it is hash-key of file, this value must be used in other API methods
    * `url`: string, absolute url of this uploaded file
    * `formats`: object, formatted versions of this file, keeps key-value pairs,
        where key is the name of formatted version, value is absolute url
    * `name`: string, name of the saved file
    * `size`: integer, file size in bytes
    * `ext`: string|null, extension of this file,
    * `type`: string, mime-type of uploaded file

-----------------------------