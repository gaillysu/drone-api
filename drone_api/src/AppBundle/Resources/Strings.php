<?php

namespace AppBundle\Resources;

class Strings{

    // Content Types
    public static $CONTENT_TYPE = 'Content-Type';
    public static $CONTENT_TYPE_JSON = 'application/json';

    // protocol
    public static $PARAMS= 'params';
    public static $MESSAGE= 'message';
    public static $STATUS= 'status';

    // Messages
    public static $MESSAGE_COULD_NOT_FIND_USER = 'could not find user';
    public static $MESSAGE_COULD_NOT_FIND_STEPS = 'could not find steps';
    public static $MESSAGE_COULD_NOT_FIND_WATCH = 'could not find watch';
    public static $MESSAGE_DELETED_WATCH = 'deleted watch';
    public static $MESSAGE_DELETED_USER = 'deleted user';
    public static $MESSAGE_DELETED_STEPS = 'deleted steps';
    public static $MESSAGE_MISSING_PARAMS= 'missing Parameters';
    public static $MESSAGE_OK= 'OK';
    public static $MESSAGE_DATE_NOT_RIGHT = "Date should be midnight";
    public static $MESSAGE_STEPS_ALREADY_EXIST_UPDATED_INSTEAD = "steps already exist. Updated for time stamp instead.";
    public static $MESSAGE_WATCH_OWNED_BY_SOMEONE_ELSE = "watch is owned by someone else";
    public static $MESSAGE_WATCH_ALREADY_REGISTERED = "watch is already registered";

    //Status Codes
    public static $STATUS_OK= 200; 
    public static $STATUS_NOT_FOUND= 404;
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
}