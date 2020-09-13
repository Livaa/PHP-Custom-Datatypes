PHP Custom Datatypes
-------------------

- An efficient oriented-object pattern to process the users inputs & to represent the data into your app.
- Does avoid the duplication of your validation & normalization rules.
- A clear view of all the data that traverse & compose your app, they are part of the app architecture !
- Data integrity, data integrity and data integrity.
- Force the developper to make a strong use of type hinting with all the benefits that come with it.

The main idea
-----------------
Wrap every data into their own relative object.
An age, an url, a city, a login, a height, a width, an rgb color, an id, ... type them all !

How to use ?
---------------
To build a datatype just extend Livaa\CustomDatatypes\CustomDatatype and write the validate() method.

```php
namespace Foo\Types;

use Livaa\CustomDatatypes\CustomDatatype;

class   EmailAddress
extends CustomDatatype
{
    function validate(): void{
    
        // - Write here the validation/normalization process.
        // - Don't forget to cast the value to the right type.       
        // - Call $this->error("error_message"); in case of error
    }
}
```

So you can use it in your controllers :

```php
$email = isset($_POST['email']) ? new EmailAddress($_POST["email"]) 
                                : throw new InvalidArgumentException("email_address_missing");

(new NewsletterManager)->subscribe($email);
```

This is what the NewsletterManager class would eventually look like :

```php

use Foo\Types\EmailAddress;

class NewsletterManager
{   
    // Type hinting makes sure the methods receive what they expect.
    // The code is more reliable.
    function subscribe(EmailAddress $email_address):bool{ 
    
        // some code to save the email adress
    }

    function unsubscribe(EmailAddress $email_address):bool{

        // some code to remove the email address from the db
    }
}
```
 
To extract the data from the custom datatype, use the __getValue()__ method

```php
$email  = new EmailAddress("sangoku@namek.com");

echo $email->getValue(); // output: sangoku@namek.com
```

Thanks to the ____toString()__ method, a custom datatype can behave as a string when needed. 
Note that PDO will trigger it, so you can directly send your datatypes objects into your prepared requests.

```php
$email  = new EmailAddress("sangoku@namek.com");

echo $email; // sangoku@namek.com, __toString() was triggered
is_string($email); // false, is an EmailAddress object
is_string($email->getValue()); // true

$age = new Age(34); 

echo $age; // 34 as string because echo triggered __toString()
echo "his age is".$age; // his age is 34. Concatenation triggered __toString()
is_string($age); //false, is an Age object
is_int($age); //false, same reason
is_int($age->getValue()); // true

// But don't be confuse !
echo $email == "sangoku@namek.com" ? true : false; // -> true
echo $email === "sangoku@namek.com" ? true : false; // -> false
echo $email->getValue() == "sangoku@namek.com" ? true : false; // -> true
echo $email->getValue() === "sangoku@namek.com" ? true : false; // -> true
```
If you find this a bit mind boggling, just remember that calling getValue() is the right way to do.


Keep it simple, avoid writing more methods into your datatypes.
They are just supposed to verify and represent the value it does encapsulate, nothing else.        
```php
namespace Foo\Types;

use Livaa\CustomDatatypes\CustomDatatype;

class   Rgb
extends CustomDatatype
{
    function validate(): void{
    
        // ... verification process
    }
    
    /* 
     * DON'T DO THAT
     * You better use something like a Factory:
     * eg: 
     * $rgb  = new Rgb([174, 189, 216]);
     * $rgba = (new ColorsFactory)->rgbToRgba($rgb);
     */  
    function toRgba(){ // just don't
        
        //add a 4th channel
        $this->value[] = 1;
        
        return $this->value;
    }
}
```

Error handling
--------------

Call $this->error("error_message") inside the validate() method in case of error, a CustomDatatypeException will be throw by default.

```php
use Livaa\CustomDatatypes\CustomDatatypeException;

try{

    $email = new EmailAddress("www.github.com");

catch(CustomDatatypeException $e){ // this will be triggered

    echo $e->getMessage(); 
}

```
If the datatype is called with the second parameter ($throw_exceptions) to false, the exception(s) won't be throw but collected and accessible via $this->getErrors();
Note that in this case, the execution won't be stopped, so the validation process may be collecting multiple exceptions, that's why getErrors() is plural.
```php
use Livaa\CustomDatatypes\CustomDatatypeException;

try{

    $email = new EmailAddress("www.github.com", false); //$throw_exceptions to false
    
    if( !$email->isValid() ){

        $first_error = $email->getErrors()[0];

        echo "an error happened: ".$error->getMessage(); 
    }

catch(CustomDatatypeException $e){ // this won't be triggered

    echo $e->getMessage(); 
}

```
