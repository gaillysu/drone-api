<?php

namespace AppBundle\Resources;

class Strings{

    // Content Types
    public static $CONTENT_TYPE = "Content-Type";
    public static $CONTENT_TYPE_JSON = "application/json";

    // protocol
    public static $PARAMS= "params";
    public static $MESSAGE= "message";
    public static $STATUS= "status";
    public static $VERSION= "version";
    public static $TOKEN = "token";

    // Messages
    public static $MESSAGE_COULD_NOT_FIND_USER = "Could not find user.";
    public static $MESSAGE_COULD_NOT_FIND_STEPS = "Could not find steps data.";
    public static $MESSAGE_COULD_NOT_FIND_WATCH = "Could not find watch.";
    public static $MESSAGE_COULD_NOT_FIND_SLEEP = "Could not find sleep data.";
    public static $MESSAGE_ACCESS_DENIED = "Access Denied.";
    public static $MESSAGE_NO_TOKEN = "Token not right or not provided.";

    public static $MESSAGE_DELETED_WATCH = "Deleted watch.";
    public static $MESSAGE_DELETED_USER = "Deleted user.";
    public static $MESSAGE_DELETED_STEPS = "Deleted steps.";
    public static $MESSAGE_DELETED_SLEEP = "Deleted steps.";

    public static $MESSAGE_MISSING_PARAMS= "Missing Parameters.";
    public static $MESSAGE_NOT_AUTHENTICATED= "Not authenticated.";

    public static $MESSAGE_OK= "OK";

    public static $MESSAGE_DATE_NOT_RIGHT = "Date should be midnight.";

    public static $MESSAGE_STEPS_DATA_ALREADY_EXIST_UPDATED_INSTEAD = "Steps already exist. Updated for time stamp instead.";
    public static $MESSAGE_SLEEP_DATA_ALREADY_EXIST_UPDATED_INSTEAD = "Sleep already exist. Updated for time stamp instead.";

    public static $MESSAGE_WATCH_OWNED_BY_SOMEONE_ELSE = "Watch is owned by someone else.";
    public static $MESSAGE_WATCH_ALREADY_REGISTERED = "Watch is already registered.";

    //Token
    public static $TOKEN_KEY = "ZQpFYPBMqFbUQq8E99FztS2x6yQ2v1Ei";

    //Status Codes
    public static $STATUS_OK= 200;
    public static $STATUS_NOT_FOUND= 404;
    public static $STATUS_NOT_AUTHENTICATED= 403;
    public static $STATUS_BAD_REQUEST = 400;

    // Users
    public static $USER = "user";
    public static $USER_ID = "id";
    public static $USER_LAST_NAME = "last_name";
    public static $USER_FIRST_NAME = "first_name";
    public static $USER_AGE = "age";
    public static $USER_LENGTH = "length";
    public static $USER_LAST_LONGITUDE = "last_longitude";
    public static $USER_LAST_LATITUDE = "last_latitude";
    public static $USER_WATCH_LIST = "watch_list";
    public static $USER_EMAIL = "email";
    public static $USER_PASSWORD = "password";

    // Steps
    public static $STEPS = "steps";
    public static $STEPS_ID = "id";
    public static $STEPS_USER_ID = "uid";
    public static $STEPS_STEPS = "steps";
    public static $STEPS_DATE = "date";

    // Sleep
    public static $SLEEP = "sleep";
    public static $SLEEP_ID = "id";
    public static $SLEEP_USER_ID = "uid";
    public static $SLEEP_WAKE_TIME = "wake_time";
    public static $SLEEP_DEEP_SLEEP = "deep_sleep";
    public static $SLEEP_LIGHT_SLEEP = "light_sleep";
    public static $SLEEP_DATE = "date";

    // Watches
    public static $WATCHES = "watches";
    public static $WATCHES_ID = "id";
    public static $WATCHES_USER_ID = "uid";
    public static $WATCHES_SERIAL = "serial";

    // Object
    public static $STANDARD_OBJECT_KEY= "object";

    // ApppBundles
    public static $APP_BUNDLE_USER = "AppBundle:Users";
    public static $APP_BUNDLE_STEPS = "AppBundle:Steps";
    public static $APP_BUNDLE_WATCHES = "AppBundle:Watches";
    public static $APP_BUNDLE_SLEEP = "AppBundle:Sleep";

    public static $VERSION_NUMBER = 0.2;


}