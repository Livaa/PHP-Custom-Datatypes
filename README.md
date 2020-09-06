PHP Custom Datatypes
-------------------

- An oriented-object pattern to process the users inputs & to represent the data into your app.
- Does avoid the duplication of your validation & normalization rules.
- A clear view of all the data that traverse & compose your app, they are part of the app architecture !
- Data integrity, data integrity and data integrity.
- Force the developper to make a strong use of type hinting with all the benefits that come with it.

The main idea:
-----------------
Wrap every data into their own relative object.
An age, an url, a city, a login, a height, a width, an rgb color, an id, ... type them all !

How to use ?
---------------
To build a datatype just extends Livaa\CustomDatatypes\CustomDatatype and write the validate() method.

```php
namespace Foo\Types;

use Livaa\CustomDatatypes\CustomDatatype;

class   EmailAddress
extends CustomDatatype
{
    function validate(): void{
    
        // - Write here the validation/normalization process.
        // - Don't forget to cast the value to the right type.       
        // - Call $this->error("email_malformatted"); in case of error
    }
}
```

So you can use it in your controllers :

```php
$email = isset($_POST['email']) ? new EmailAddress($_POST["email"]) 
                                : throw new InvalidArgumentException("email_address_missing");

(new NewsletterManager)->subscribe($email);
```

This is what the User an NewsletterManager class would look like :

```php
use 
    Foo\Types\EmailAddress,
    Foo\Types\PrimaryKey;

class User
{        
    function __construct(PrimaryKey $id){ 
    
        // $res = ... some SQL to get the email
        
        $this->email = new EmailAddress($sql_res["email"]);
    }
}
```
```php

use Foo\Types\EmailAddress;

class NewsletterManager
{   
    // Type hinting makes sure the methods receive what they expect.
    // The code is more reliable.
    function subscribe(EmailAddress $email_address){ 
    
        // some code to save the email adress
    }
}
```
 
To extract the data from the custom datatype, use the __getValue()__ method

```php
$email  = new EmailAddress("sangoku@namek.com");

echo $email->getValue(); // output: sangoku@namek.com
```

Actually, a custom datatype will behave as a string when needed thanks to the ____toString()__ method.
That may help

```php
$email = new EmailAddress("sangoku@namek.com");

echo $email; // output: sangoku@namek.com

// But don't be confuse !
echo $email == "sangoku@namek.com" ? true : false; // -> true
echo $email === "sangoku@namek.com" ? true : false; // -> false
echo $email->getValue() == "sangoku@namek.com" ? true : false; // -> true
echo $email->getValue() === "sangoku@namek.com" ? true : false; // -> true
```

Keep it simple, avoid writing more methods into your datatypes.
They are just supposed to verify and represent the value it does encapsulate, nothing else.        
```php
namespace MyApp\CustomDataTypes;

use Livaa\CustomDatatypes\CustomDatatype;

class   Rgb
extends CustomDatatype
{
    function validate(): void{
    
        // ... verification process
    }
    
    /* 
     * DON'T DO THAT
     *
     * If you feel the need to do so, you need something like a Factory:
     * eg: 
     * $rgb  = new Rgb([174, 189, 216]);
     * $rgba = (new ColorsFactory)->rgbToRgba($rgb);
     */  
    function toRgba(){
        
        //add a 4th channel
        $this->value[] = 1;
        
        return $this->value;
    }
}
```

Error handling
--------------

A custom datatype can be called with the second parameter to false (bool $thow_exceptions)

```php
//Will throw a CustomDatatypeException
$email = new EmailAddress("www.github.com"); 

//The exception is collected and accessible thru $this->getErrors() but not thrown
$email = new Email("www.github.com", false); 
When writing your datatypes, call $this->error("error_message") in case of error
```php
namespace Foo\Types;

use Livaa\CustomDatatypes\CustomDatatype;

class   Login
extends CustomDatatype
{
    function validate(): void{
    
        $this->value = trim($this->value);
        
        if(strlen($this->value) < 5){
        
            $this->error("login_too_short");
        }
    }
}
```
