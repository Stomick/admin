SportCenter API Methods
=====================

List of sportCenters
--------------------

 - *Description*: Returns the list of sportCenters
 - *Access*: authorized only
 - *URL*: `/api/sport-centers`
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
        * `name`
        * `createdAt`
        * `updatedAt`

 - *Response Headers*:

    * `X-Pagination-Current-Page`: integer, the number of current page
    * `X-Pagination-Page-Count`: integer, total pages count
    * `X-Pagination-Per-Page`: integer, current `per-page` value (may be not equal to real count of returned items)
    * `X-Pagination-Total-Count`: integer, total items count
    * `Link`: string, you may see examples of prev/self/next pages here, [specification](https://tools.ietf.org/html/rfc5988).

 - *Response Data*:

    * `items`: array of user s, each of them may keeps key-value pairs:

        * `id`: integer
        * `email`: string
        * `active`: boolean
        * `draft`: boolean
        * `type`: string "portfolio" or "opportunity"
        * `name`: string
        * `image`: null|object with key `origin` - url of image
        * `categories`: array of objects of category
        * `industries`: array of objects of industry
        * `infos`: array of objects info, that user has access
        * `presentations`: array of objects presentation, that user has access
        * `position`: integer
        * `positionDesc`: integer
        * `positionDoc`: integer
        * `positionFaq`: integer
        * `positionPrese`: integer
        * `descriptions`: array of objects description, that user has access
        * `documents`: array of objects documents, that user has access
        * `faqs`: array of objects faqs, that user has access
        * `createdAt`: integer
        * `updatedAt`: integer
        * `admins`: array of objects user
        * `owner`: objects of user
    
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
      "id": 1,
      "email": "schafets@targetglobal.vc",
      "active": 1,
      "draft": 0,
      "type": "opportunity",
      "name": "2name company #1",
      "image": null,
      "categories": [
        {
          "id": 1,
          "companyId": 1,
          "name": "2category1",
          "position": 1,
          "createdAt": 1481138889,
          "updatedAt": 1481138889
        },
        {
          "id": 2,
          "companyId": 1,
          "name": "2category2",
          "position": 2,
          "createdAt": 1481138889,
          "updatedAt": 1481138889
        },
        {
          "id": 3,
          "companyId": 1,
          "name": "2category3",
          "position": 3,
          "createdAt": 1481138889,
          "updatedAt": 1481138889
        }
      ],
      "industries": [
        {
          "id": 1,
          "companyId": 1,
          "name": "2industry1",
          "position": 1,
          "createdAt": 1481138889,
          "updatedAt": 1481138889
        },
        {
          "id": 2,
          "companyId": 1,
          "name": "2industry2",
          "position": 2,
          "createdAt": 1481138889,
          "updatedAt": 1481138889
        }
      ],
      "infos": [
        {
          "public": 1,
          "id": 1,
          "companyId": 1,
          "title": "2Info a",
          "text": "2Textetetej asdf asdfasdf asdfasdf  a",
          "position": 1,
          "createdAt": 1481138889,
          "updatedAt": 1481138889
        }
      ],
      "presentations": [],
      "position": null,
      "positionDesc": 3,
      "positionDoc": 0,
      "positionFaq": 2,
      "positionPrese": 1,
      "descriptions": [],
      "documents": [],
      "faqs": [
        {
          "public": 1,
          "id": 2,
          "companyId": 1,
          "title": "FAAAAAAAAQ2",
          "text": "asdf",
          "position": 2,
          "createdAt": 1481138889,
          "updatedAt": 1481138889
        }
      ],
      "createdAt": 1481138889,
      "updatedAt": 1481138889,
      "admins": [
              {
                "id": 81,
                "username": "Shanny Harel",
                "email": "rel@targetglobal.vc",
                "agree": 0,
                "role": "admin",
                "token": "xEu_rTTWU5YId92Qsc88whLH4vyGK5bsLVJ4MMmLXl-cseLxfN8tRK-Ag1M__QtP",
                "phone": "",
                "description": "",
                "portfolioIds": [],
                "opportunityIds": [],
                "twitter": "",
                "facebook": "",
                "google": null,
                "linkedin": "",
                "createdAt": 1485547302,
                "updatedAt": 1485791948
              }
            ],
            "owner": {
              "id": 80,
              "username": "Shmuel Chafets",
              "email": "fets@targetglobal.vc",
              "agree": 0,
              "role": "admin",
              "token": "Fp0k9fNUjaWNG_tHs_V2JuucXrNcTJjll2Ww13SkqZ8qAk6nCiN5V1Ean1sqQR-a",
              "phone": "",
              "description": "",
              "portfolioIds": [],
              "opportunityIds": [],
              "twitter": "",
              "facebook": "",
              "google": null,
              "linkedin": "",
              "createdAt": 1485547263,
              "updatedAt": 1485547263
            }
    },
    {
      "id": 2,
      "name": "2name company #1",
      "image": null,
      "categories": [
        {
          "id": 4,
          "companyId": 2,
          "name": "2category1",
          "position": 1,
          "createdAt": 1481139557,
          "updatedAt": 1481139557
        },
        {
          "id": 5,
          "companyId": 2,
          "name": "2category2",
          "position": 2,
          "createdAt": 1481139557,
          "updatedAt": 1481139557
        },
        {
          "id": 6,
          "companyId": 2,
          "name": "2category3",
          "position": 3,
          "createdAt": 1481139557,
          "updatedAt": 1481139557
        }
      ],
      "industries": [
        {
          "id": 3,
          "companyId": 2,
          "name": "2industry1",
          "position": 1,
          "createdAt": 1481139557,
          "updatedAt": 1481139557
        },
        {
          "id": 4,
          "companyId": 2,
          "name": "2industry2",
          "position": 2,
          "createdAt": 1481139557,
          "updatedAt": 1481139557
        }
      ],
      "infos": [
        {
          "public": 1,
          "id": 2,
          "companyId": 2,
          "title": "2Info a",
          "text": "2Textetetej asdf asdfasdf asdfasdf  a",
          "position": 1,
          "createdAt": 1481139557,
          "updatedAt": 1481139557
        }
      ],
      "documents": [],
      "faqs": [
        {
          "public": 0,
          "id": 3,
          "companyId": 2,
          "title": "FAQ",
          "text": "asdfasdf asdfasdf adsf asdf asdf asd asd a",
          "position": 1,
          "createdAt": 1481139557,
          "updatedAt": 1481139557
        },
        {
          "public": 1,
          "id": 4,
          "companyId": 2,
          "title": "FAAAAAAAAQ2",
          "text": "asdf",
          "position": 2,
          "createdAt": 1481139557,
          "updatedAt": 1481139557
        }
      ],
      "createdAt": 1481139557,
      "updatedAt": 1481139557
    },
    {
      "id": 3,
      "name": " company #2",
      "image": {
        "origin": "http://finance-fox.dev/upload/files/company-image/7RiP/report-00.png",
        "formats": []
      },
      "categories": [
        {
          "id": 7,
          "companyId": 3,
          "name": "2category1",
          "position": 1,
          "createdAt": 1481139709,
          "updatedAt": 1481139709
        }
      ],
      "industries": [
        {
          "id": 5,
          "companyId": 3,
          "name": "2industry1",
          "position": 1,
          "createdAt": 1481139709,
          "updatedAt": 1481139709
        }
      ],
      "infos": [
        {
          "public": 1,
          "id": 3,
          "companyId": 3,
          "title": "2Info a",
          "text": "2Textetetej asdf asdfasdf asdfasdf  a",
          "position": 1,
          "createdAt": 1481139709,
          "updatedAt": 1481139709
        }
      ],
      "documents": [],
      "faqs": [
        {
          "public": 1,
          "id": 6,
          "companyId": 3,
          "title": "FAAAAAAAAQ2",
          "text": "asdf",
          "position": 2,
          "createdAt": 1481139709,
          "updatedAt": 1481139709
        }
      ],
      "createdAt": 1481139709,
      "updatedAt": 1481139709
    },
    {
      "id": 7,
      "name": " company #10",
      "image": null,
      "categories": [],
      "industries": [],
      "infos": [],
      "documents": [
        {
          "id": 1,
          "name": "doc1txt",
          "public": 1,
          "file": {
            "origin": "http://finance-fox.dev/upload/files/company-document/eE28/Vtar.txt"
          },
          "createdAt": 1481169006,
          "updatedAt": 1481169942
        }
      ],
      "faqs": [
        {
          "public": 1,
          "id": 9,
          "companyId": 7,
          "title": "FAQ",
          "text": "asdfasdf asdfasdf adsf asdf asdf asd asd a",
          "position": 1,
          "createdAt": 1481169006,
          "updatedAt": 1481169942
        }
      ],
      "createdAt": 1481169006,
      "updatedAt": 1481169662
    }
  ],
  "_links": {
    "self": {
      "href": "http://finance-fox.dev/api/companies?page=1"
    }
  },
  "_meta": {
    "totalCount": 4,
    "pageCount": 1,
    "currentPage": 1,
    "perPage": 20
  }
}
```

View Company
-----------

 - *Access*: authorized only
 - *URL*: `/api/companies/<id>`
 - *URL params*:

    * `<id>`: integer, company's ID

 - *Method*: `GET`
 - *Note*: Server will throw 404 exception if company was not found

 - *Response Data*:

    * object, it keeps key-value pairs:

        * `id`: integer
        * `email`: string
        * `active`: boolean
        * `draft`: boolean
        * `type`: string "portfolio" or "opportunity"
        * `name`: string
        * `image`: null|object with key `origin` - url of image
        * `categories`: array of objects of category
        * `industries`: array of objects of industry
        * `infos`: array of objects info, that user has access
        * `presentations`: array of objects presentation, that user has access
        * `position`: integer
        * `positionDesc`: integer
        * `positionDoc`: integer
        * `positionFaq`: integer
        * `positionPrese`: integer
        * `descriptions`: array of objects description, that user has access
        * `documents`: array of objects documents, that user has access
        * `faqs`: array of objects faqs, that user has access
        * `createdAt`: integer
        * `updatedAt`: integer
        * `admins`: array of objects user
        * `owner`: objects of user

Create Company
-----------
 - *Access*: authorized only
 - *URL*: `/api/companies`
 - *Method*: `POST`

 - *POST params*:
    
    * `name`: string(255)|required
    * `image`: string (hash-value)
    * `categoryModels`: array of objects with keys:
        * `name`: string(255)|required
    * `industryModels`: array of objects with keys:
        * `name`: string(255)|required
    * `infoModels`: array of objects with keys:
        * `title`: string(255)|required
        * `text': string(65535)|required
        * `public`: 0|1 (bool)
    * `documentModels`: array of objects with keys:
        * `name`: string(255)
        * `file': string (hash-value)|required
        * `public`: 0|1 (bool)
    * `faqModels`: array of objects with keys:
        * `title`: string(255)|required
        * `text': string(65535)|required
        * `public`: 0|1 (bool)
    * `descriptionModels`: array of objects with keys:
        * `title`: string(255)|required
        * `text': string(65535)|required
        * `public`: 0|1 (bool)
        
 - *Note*: Server will throw 422 status code if validation fails or 201, when user was created
 - *Response Data*:
    
    * if success: object of company
    
    * if validation fails: array of objects with properties:
        * `field`: name of  attribute
        * `message`: string of error
        


Upload image
-----------------------------
 - *Access*: authorized only
 - *URL*: `/api/companies/upload-image`
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


Upload document
-----------------------------
 - *Access*: authorized only
 - *URL*: `/api/companies/upload-document`
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