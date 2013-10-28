MCAuth
==========

Minecraft PHP Authentication for new Mojang Yggdrasil (http://wiki.vg/Authentication) authentication scheme 

Example:

```php
<?php
include("MCAuth.class.php");
$MCAuth = new MCAuth();
if ($MCAuth->authenticate('username or email', 'password') == TRUE) {
	echo $MCAuth->account['id'];			// hexadecimal user ID
	echo $MCAuth->account['username'];		// account username
	echo $MCAuth->account['token'];			// access token
} else {
	echo $MCAuth->autherr;					// print error
}
?>
```
