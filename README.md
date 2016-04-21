# API Documentation

This document describes the API specification of the drone API

## Authentication

We use basic authentication to get authenticate for the API. After that we have to provide a token for every request.

## API Specification

It's a complete rest API. Getting data will be done through `GET`, creating data will be done through `POST`, changing data will be done by `PUT` and deleting data will be done by `DEL`

### Request
The API has a basic structure request body structure which looks like:

```
{
    "token":"...",
    "params":{
     ....
    }
}
```

Within `params` you specify what you want to request, for example `steps`.

```
{
    "token":"ZQpFYPBMqFbUQq8E99FztS2x6yQ2v1Ei",
    "params":{
        "steps":{
            "uid":...,
            "steps":"...",
            "date":"..."
        }
    }
}
```

For sleep it's obviously instead of `steps`, `sleep` and for watches `watches` and users `user`

The `date` can be specified in the following formats:
```
   "date":"2000-01-01"
   "date":"00-01-01"
   "date":"00-1-01"
   "date":"00-1-1"
```
But please keep it consistent. It should always be year/month/date.

It's possible to process multiple entries. Instead of giving an json object you just fetch a json array which looks like this:

```
{
    "token":"...",
    "params":{
        "steps":[{
            "uid":...,
            "steps":"...",
            "date":"..."
        },{
            "uid":...,
            "steps":"...",
            "date":"..."
        },
        ....]
    }
}
```

### Response

The response of a request is also in a standard format which looks like this:
```
{
    "some_response":{...
    },
    "version": 0.5,
    "message": "OK",
    "status": 1
}
```
So `some_response` is based on what you request. It's either `steps`, `sleep` etc. etc. etc.. Version is based on the API version. See the version changelog in the following section. The `message` gives a description about the status of the response in text based on what is requested. The `status` is specified with a number which represents the status of the response.

Here are the following response status codes:

| Status  | Meaning                                                |
| :-----: |:-------------------------------------------------------|
| 1       | Success request                                        |
| -1      | Requested data not found                               |
| -2      | Not authenticated                                      |
| -3      | Bad request, missing parameters or not well formed json|

Again whenever you request a single object your response will be a json object like this
```
{
  "sleep": {
    "id": 1,
    "uid": 2,
    "wake_time": "[123,123,12,12]",
    "light_sleep": "[123,123,12,12]",
    "deep_sleep": "[123,123,12,12]",
    "date": {
      "date": "-0001-11-30 00:00:00.000000",
      "timezone_type": 3,
      "timezone": "Europe/Berlin"
    }
  },
  "version": 0.5,
  "message": "OK",
  "status": 1
}
```

Please note how `date` is specified as response.

Whenever your request requests an array of objects you get an array back which looks like this
```
{
  "sleep": [
    {...},
    {...},
    {...},
    {...},
  ],
  "version": 0.5,
  "message": "OK",
  "status": 1
}
```

Please ask me for the postman file so you can have the credentials and the URLS. Notice that the URLS are not permanent if it's the API is put on my domain.

## CHANGELOG

### Version 0.1

* Initial api with basic get put post and del of all entries
* Basic auth and token for authentication

### Version 0.2

* Added responses to root.

### Version 0.3

* Login added for user
* Removed password for all responses
* Removed user get since that's not really necessary

### Version 0.4
* An attempt to fix dates

### Version 0.5
* Changed date representation from timestamp to DateTime (from PHP)

## TO-DO
* Make forget password

