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
    public static $MISSING_PARAMS= 'Missing Parameters';
    public static $OK= 'OK';

    // Messages
    public static $MESSAGE_COULD_NOT_FIND_USER= 'Could not find user';
    public static $MESSAGE_DELETED_USER = 'Deleted user';

    //Status Codes
    public static $STATUS_OK= 200;
    public static $STATUS_FOUND= 302;
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
    public static $STEPS_ID = "id";
    public static $STEPS_USER_ID = "user_id";
    public static $STEPS_STEPS = "steps";
    public static $STEPS_DATE = "date";

    // Watches
    public static $WATCHES_ID = "id";
    public static $WATCHES_USER_ID = "user_id";
    public static $WATCHES_SERIAL = "serial";

}