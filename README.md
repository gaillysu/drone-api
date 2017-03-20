# Nevo API 

Nevo API Deployment is explained here! Nevo runs on Symfony 3. 

## Deployment

### Server configuration
First of all, checkout your repository. Change the webroot to ../root/web in your configuration files for your domain. 

Then, important stuff. Are we using Plesk? If yes, are we using nginx? Yes? Then: use the following piece of 'code' in the `Additional nginx directives` in `Apache & nginx Settings` at your domain. For Nevo this is: https://cloud.nevowatch.com.

```
location = / {
	try_files $uri /app.php$is_args$args;
}

location ~ ^/app\.php(/|$) {
	internal;
}

location ~ ^/app\.php(/|$) {
	fastcgi_pass unix:/var/run/php5-fpm.sock;
	fastcgi_split_path_info ^(.+\.php)(/.*)$;
	include fastcgi_params;
	fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
	fastcgi_param DOCUMENT_ROOT $realpath_root;
	internal;
}

location ~ ^/(app_dev|config)\.php(/|$) {
	fastcgi_pass unix:/var/run/php5-fpm.sock;
	fastcgi_split_path_info ^(.+\.php)(/.*)$;
	include fastcgi_params;
	fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
	fastcgi_param DOCUMENT_ROOT $realpath_root;
}

error_log /var/log/nginx/project_error.log;
access_log /var/log/nginx/project_access.log;
```

At nginx settings: `Proxy mode`, `Smart static files processing` and `Serve static files directly by nginx` should be turned on. In the `Common Apache settings` everything is set to `Default`.
### Permissions
Good! Time to `SSH`. First you need a sudo user. REALLY IMPORTANT. You got to set the permissions of the root of the symfony project folder. This is done by: 
`sudo chown karl:karl root`
Great! Now, the next step is setting the permissions for logs and cache for your WWW user.  In Ubuntu this is `www-data` and for CentOS this `apache`. 
And this is done by: 
```
sudo setfacl -R -m u:www-data:rwX ../root/var/cache ../root/var/logs
sudo setfacl -dR -m u:www-data:rwX ../root/var/cache ../root/var/logs
```
The second statement is used for any new directory, apply same permissions. In the end it should look something like this:
```
file: cloud.nevowatch.com/
owner: nevowatch
group: psaserv
user::rwx
user:apache:r-x
group::r-x
mask::r-x
other::---
```
Atleast, this worked. 
### Kick off Symfony
Great from here on you don't need `sudo` anymore so `SSH` with your normal account. When logged in, `export SYMFONY_ENV=prod` and go to the root directory of your application. Then, if you have composer installed, 
```
composer install --optimize-autoloader --no-dev
composer update
```
Else
```
php composer.phar install --optimize-autoloader --no-dev
php composer.phar update
```
If you have not installed php or it is somewhere else located:
```
/opt/plesk/php/7.0/bin/php composer.phar install --optimize-autoloader --no-dev
/opt/plesk/php/7.0/bin/php composer.phar update
```
Yes. Omg. After that, Symfony asks you to configure your parameters.yml by a simple wizzard. And that's about it for Symfony.
### Configure the database
Ok so now. Go to the bin folder by `cd bin`. Then: check your database by:
```
php console doctrine:schema:validate
```
If it says that it isn't configured correctly, do so by:
```
php console doctrine:generate:entities AppBundle
php console doctrine:schema:update --force
```
Again, you might want to use a different php. Then: Clear your cache and do the handshake. 
```
php console cache:clear --env=prod --no-debug
```
THATS IT! 


# API Documentation

This document describes the API specification of the nevo API

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

   "date":"2000-12-31"
   "date":"00-12-31"
   "date":"00-1-31"
   "date":"00-1-3"
```
But please keep it consistent. It should always be year/month/date. I suggest you to use `2000-12-31`.

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

# Forget password
Forget password is different from other calls. We don't send the user an e-mail because we haven't got the SMTP server yet. But! To make it a bit safer we make 2 calls. The first call is the `request_password_token` where we get a `password_token`. The request only requires one parameter which is an `email` (see postman). It returns an object of user with the `email` you requested, the `id` and a `password_token`. Don't lose this token otherwise you have to do the request again. 

After that you make a call to `forget_password`. Forget password requires the parameters of the previous response which are `id` `email` and `password_token` and obviously an extra one which is the new `password`. The response looks like what you usually get whenever you do `user/login`

# Detailed GET method for steps and sleep
A more detailed GET method is added in 0.8 for steps and sleep. Whenever a GET method is executed it is done as follows: `https://URL.com/steps/user/id`, where id is the user id. Parameters given in the GET method are: `token`, `start_date` and `end_date`. You don't have to provide `start_date` and `end_date` but you will only get the last 10 data entries. Providing `start_date` and `end_date` will give you a maximum 50 entries. `start_date` and `end_date` is specified in unix timestamp. 

## Postman
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

### Version 0.6
* Added missing information such as weight length, etc. etc.

### Version 0.7
* Added forget password

### Version 0.8
* Added a more detailed GET. method for steps and sleep. 
* Also fixed a few bugs

### Version 0.9
* Weight and Length datatype has been changed to Float/Double. Still specified by CM and KG
* Added Check e-mail for user

### Version 0.10
* Improved Update and Delete. 
* Delete everything whenever an user is deleted 

### Version 0.11
* Added Verification of e-mail

### Version 0.12
* Verification working
* Date checks for steps and sleep
* Parameters improvement
* bug fixes

## TO-DO
* Increase modularity with something like Strategy, Chain of responsibility or whatever.
* Add change user password request into something better.