MCAuth
==========

Minecraft PHP Authentication for Mojang Yggdrasil (http://wiki.vg/Authentication) authentication scheme 

####Example:

Create new MCAuth Object
```php
<?php
$MCAuth = new MCAuth\Api();
```

Authentication:
```php
<?php
try {
    $account = $MCAuth->sendAuth("myemail@example.org", "mypassword");
    var_dump($account);
} catch (Exception $e) {
    echo $e->getMessage();
}
```

Get UUID from username
```php
<?php
try {
    $uuid = $MCAuth->usernameToUuid("_Cyb3r");
    var_dump($uuid);
} catch (Exception $e) {
    echo $e->getMessage();
}
```

Get username from UUID
```php
<?php
try {
    $uuid = $MCAuth->uuidToUsername("be1cac3b60f04e0dba12c77cc8e0ec21");
    var_dump($uuid);
} catch (Exception $e) {
    echo $e->getMessage();
}
```


####Warning!
Mojang authentication system permits only one active session as reported in the wiki: 
"Only the one with the latest session ID for your account are allowed to join servers.", so you will be disconnected from the server where you are playing if you try to login via MCAuth.