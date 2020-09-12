PHP Custom Datatypes
-------------------

- An oriented-object pattern to process the users inputs & to represent the data into your app.
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

This is what the NewsletterManager class would look like :

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

Thanks to the ____toString()__ method, a custom datatype can behave as a string when needed. 
(It is important to understand that PDO will trigger it, so you can send directly your datatypes objects without the extra getValue() call inside your prepared requests.
it does help to make the code a bit cleaner)
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
# If you found this a bit mind boggling, just remember that calling getValue() is the right way to do.

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
If the datatype is called with the second parameter ($throw_exceptions) to false , the exception won't be thrown but collected and accessible thru $this->getErrors();

```php
$email = new EmailAddress("www.github.com", false); //$throw_exceptions on false

print_r($email->getErrors());
//Array([0] => error_message)
```
Note that when $throw_exceptions is false, $this->error() won't stop the execution.

So you may collect multiple exceptions, depending how you did code the validation process.
This example will collect multiple exceptions if an empty string is given : 
```php

use Livaa\CustomDatatypes\CustomDatatype;

class   EmailAddress
extends CustomDatatype
{
    function validate(): void{
    
        $this->value = trim($this->value);
        
        //this one will be collected
        if( strlen($this->value) === 0 ){
            
            $this->error("email_is_empty");
        }                

        //this one too
        if ( !filter_var($value, FILTER_VALIDATE_EMAIL) ){
            
           $this->error("email_is_empty");
        }
    }
}
```

While this example will only collect one

```php

use Livaa\CustomDatatypes\CustomDatatype;

class   EmailAddress
extends CustomDatatype
{
    function validate(): void{
    
        $this->value = trim($this->value);
        
        //this one will be collected
        if( strlen($this->value) === 0 ){
            
            $this->error("email_is_empty");
            
        }else{                

            //this one won't
            if ( !filter_var($value, FILTER_VALIDATE_EMAIL) ){
            
                $this->error("email_is_malformed");
            }
       }
    }
}
```
