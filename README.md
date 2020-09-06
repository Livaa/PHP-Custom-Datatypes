PHP Custom Datatypes
-------------------

- An oriented-object pattern to process the users inputs & to represent the data into your app.
- Does avoid the duplication of your validation & normalization rules.
- A clear view of all the data that traverse & compose your app, because they are part of your app architecture too ! 
- Data integrity, data integrity and data integrity.
- Force the developper to make a strong use of type hinting with all the benefits that come with it.


The main idea:

Wrap every data into their own relative object.
An email address shouldn't be a string but an EmailAddress object. 
An age, an url, a city, a login, a height, a width, an rgb color ... type them all !


How to use ?

To build a datatype just extends Livaa\CustomDatatypes\CustomDatatype and write the validate() method.

```php
namespace Foo\Types;

use Livaa\CustomDatatypes\CustomDatatype;

class   EmailAddress
extends CustomDatatype
{
    function validate(): void{
    
        // Write here the validation/normalization process.
        // - Don't forget to cast the value to the right type       
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

Actually, Custom datatypes can behave as strings when needed thanks to the ____toString()__ method

```php
$email = new EmailAddress("sangoku@namek.com");

echo $email; // output: sangoku@namek.com
```

Keep it simple, do not have extra methods in your custom datatype.
  
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
     * A customType is just supposed to verify and represent the data it does encapsulate, nothing else.      
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
  
