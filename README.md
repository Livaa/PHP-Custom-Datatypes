Install : ```composer require livaa/custom-datatypes```

PHP Custom Datatypes
-------------------

- An efficient __oriented-object pattern__ to process the users inputs & to represent the data into your app.
- Does avoid the duplication of your validation & normalization rules.
- A clear view of all the data that traverse & compose your app, they are part of the app architecture !
- Data integrity, data integrity and data integrity. 
- Force the developper to make a strong use of __type hinting__ with all the benefits that come with it.
  
The main idea
-----------------
Every each data must be wrap into it's own relative object.
An age, an url, a city, a login, a height, a width, an rgb color, an id... type them all !

How to use ?
---------------
To build a custom datatype just extend Livaa\CustomDatatypes\CustomDatatype and write the validate() method.

```php
namespace Foo\Types;

use Livaa\CustomDatatypes\CustomDatatype;

class   EmailAddress
extends CustomDatatype
{
    function validate(): void{
    
        // - Write here the validation/normalization process.      
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

This is what the NewsletterManager class would eventually looks like.\
That's just an example but the idea is to type-hint your classes to receive custom datatypes objects rather that natives typed variables.
It does make the code more readable, easier to maintain and secure.
Also it makes the whole app more reliable as the received values are always good.
Eventually you'll finally get rid off all those dirty validation processes you have there and there (Utils::iKnowYouKnowWhatImTalkingAbout() !)

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

Thanks to the ____toString()__ method, a custom datatype can behave as a string when needed.\
Note that PDO will trigger the __toString()__ method, so you can directly send your datatypes objects into your prepared requests.

```php

// let's say we have an EmailAddress, a Rgb and an Energy custom datatypes

$email        = new EmailAddress("sangoku@namek.com"); 
$hair_color   = new Rgb([34,54,93]); // This one wants an array
$energy_left  = new Energy(12); // obviously this one wants an int

echo $email; 
output: sangoku@namek.com

echo $hair_color;
output: {34,54,93} 

echo $energy_left;
output: 12 as a string

// but don't be confuse !
$email == "sangoku@namek.com" -> true
$email === "sangoku@namek.com" -> false
$email->getValue() == "sangoku@namek.com"  -> true
$email->getValue() === "sangoku@namek.com" -> true
```
If you find this a bit mind boggling, just remember to use getValue() everytime except 


Keep it simple, avoid writing more methods into your datatypes.
They are just supposed to __verify__ and __represent__ the value it does encapsulate, nothing else.        
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
If the datatype is called with the second parameter ($throw_exceptions) to false, the exception(s) won't be throw but collected then accessible via $this->getErrors().\
Note that in this case, you may be collecting multiple exceptions (depends how is coded the validation process), that's why getErrors() is plural.
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
