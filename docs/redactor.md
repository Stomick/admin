Upload image
-----------------------------
 - *Access*: authorized only
 - *URL*: `/api/redactor/upload-image`
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


Upload file
-----------
 - *Access*: authorized only
 - *URL*: `/api/redactor/upload-file`
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