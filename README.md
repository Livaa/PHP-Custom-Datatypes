# CustomTypes for PHP
(customTypes for JS are even better, check it out: Custom)

"CustomTypes" is an object pattern to process the users inputs & to represent the data into your app.
The benefits:

- Avoid the duplication of your validation/normalization rules.
- Data integrity, data integrity and data integrity.
- A clear view of all the data that compose your app.
- Force the developper to make a strong use of type hinting which increase the quality of the code.

How ?
Why ?
Error handling
Downsides

The main idea is to wrap **every each** data into their respecting validating classes.
... i know it doesn't sound good at first, let me convince you:

```php
$email         = new Email($_POST["email"]);
$firstname     = new Firstname($_POST["firstname"]);
$lastname      = new Lastname($_POST["firstname"]);
$ip            = new Ip($_SERVER["REMOTE_ADDR"]);
$login         = new Login($_POST["login"]);
$fav_color     = new Rgb( json_decode($_POST["fav_color"], true) );
$screen_height = new Height($_POST["screen_height"]);
$screen_width  = new Width($_POST["screen_width"]);
...
``` 

To access the value, just use the getValue() method

```php

echo $email->getValue(); 

if( $screen_width->getValue() < 1024 ){
 ...
}
``` 

All CustomTypes can behave as strings when needed thanks to the __toString() method.

```php
echo $email.": ".$country." ".".$ip.": ".$screen_width."x".$screen_height;

// No matter how you are requesting your database,
// you probably won't need to invoke the getValue() method

$request = DB::update("users", ["fav_color" => $fav_color], ["email = ?" => $email]);
```

CustomTypes are easy to build (and to use from one project to another).  
Just extend the CustomType class and code the validate() method

```php
namespace Foo\Bar\CustomTypes;

use Livaa\CustomTypes\CustomType;

class TheNameOfYourCustomType extends CustomType{
    
    function validate(): void {
               
        // Add here your code to verify & normalize the given data
        // - The value is accessible thru $this->value or its getter $this->getValue()
        // - Call $this->error("error_happens"); to trigger an error.
    }
}
```

Example of a CustomType for email addresses
```php
namespace Foo\Bar\CustomTypes;

use Livaa\CustomTypes\CustomType;

class Email extends CustomType{
    
    function validate(): void{
               
        //the first step is to cast and normalize your data
        $this->value = strtolower( trim($this->value) );
        
        //we can check the length and throw an exception length check
        if(strlen($value) === 0){
            
            // This is important to call this->error rather than throwing an exception here
            // we'll see why later
            $this->error("email_is_empty"); 
        }
        
        //validity check
        if ( !filter_var($value, FILTER_VALIDATE_EMAIL) ){
            
           $this->error("invalid_email");
        }              
    }
}
```

It does improve the type hinting of the whole app with all the benefits that come with it.

Before :

```php
namespace Foo\Bar\Users;

class User { 
 
    public 
      
        // class variables type hinting is allowed since PHP7.4    
        int       $id,
        string    $email,
        string    $firstname     
      
                  
    function __construct(int $id){               
        
        $data = Database::select(....);
        
        if($data){
        
            $this->id         = $id;
            $this->email      = $data['email'];
            $this->firstname  = $data['firstname'];
        }
    }
    
    
    function getId():int{
    
        return $this->id;
    }
       
       
    function getEmail():string{
    
        return $this->email;
    }        
    function setEmail(string $email):void{
    
        if(Database::update(...)){
        
          $this->email = $email
       }
    } 
    
    ...
}   

```

After:
```php
namespace Foo\Bar\Users;

use 
    Foo\Bar\CustomTypes\PrimaryKey,
    Foo\Bar\CustomTypes\Email,
    Foo\Bar\CustomTypes\Firstname,
    
class User { 
 
    public 
          
        PrimaryKey  $id,
        Email       $email,
        Firstname   $firstname,     
        City        $city,
        Timestamp   $sub_timestamp;
     
     
    function __construct(PrimaryKey $id){               
        
        $data = Database::select(...);
        
        if($data){
        
            $this->id         = $id;
            $this->email      = new Email($data['email']);
            $this->firstname  = new Firstname($data['firstname']);           
        }
    }
  
  
    function getId():PrimaryKey{
    
        return $this->id;
    }
    
    
    function getEmail():Email{
    
        return $this->email;
    }    
    function setEmail(Email $email):void{
               
        if(Database::update(....)){
        
          $this->email = $email
       }
    }
        
    ...   
}   

```

By default, CustomTypes will throw a CustomTypeException when $this->error() is triggered.
Also, a line is written into the server log with the filename and the line the error happened.
```php
use 
    Foo\Bar\CustomTypes\Email,
    For\Bar\CustomTypes\CustomTypeException;
    
try{

    $email = new Email("123");
    
}catch(CustomTypeException $e){

    echo "catched:".$e->getMessage(); // this will be triggered
}

// error.log: [client localhost:49740] CustomTypes error: invalid_email at /var/www/examples/CustomTypes/index.php line 7
```
Both can be disallowed independently
```php
use 
    Foo\Bar\CustomTypes\Email,
    For\Bar\CustomTypes\CustomTypeException;
    
try{
    //$options = ["throw_exception" => false];
    //$options = ["log_error" => false];
    $options = ["throw_exception" => false, "log_error" => false];
    $email   = new Email("123", $options);
    
}catch(CustomTypeException $e){

    echo "catched:".$e->getMessage(); // this will NOT be triggered
}

// nothing was written into the log.
// You can now get the error with getError();

if($email->getError()){

    echo "an error happened :".$email->getError();
}
```

Deeper & downsides

- Because of their nature, i like to keep booleans as native bool.    
  They are easy to verify, no need to be normalized and i'm not sure a __toString()__ method on a bool value would make any sense.
  Anyway feel free to explore by yourself and find what fits best to your own needs.

- A small downside is devs failing at compairing data because they are now dancing with objects where they are normally used to deal with native types. It may be mind buggling at first, so error prone.  I found myself trapped more than few times ... 🤫   The trick is to remember to use the getValue() method.

  
  ```php
  
  $email = new Email("dolphin@sea.com");
  
  echo $email; //output: doplhin@sea.com
  
   //true because weak comparaison cast $email as a string
   //which triggers the __toString() method
   var_dump($email == "dolphin@sea.com");
  
  //false because $email is an object and dolphin@sea.com a string
  var_dump($email === "dolphin@sea.com"); 
  
  //remember to use getValue() which is supposed to return
  //the value with the right type
  var_dump($email->getValue() == "dolphin@sea.com"); //true
  var_dump($email->getValue() === "dolphin@sea.com"); //true
  
    
  ```

- Another downside is class coupling.  
  In fact the whole app will be coupled with them, *CustomTypes everywhere !* 👽  
  Tho, they are easy to portate ... So, again, you find what fits to what you need.    
  But if low coupling is a real matter for you, CustomTypes may not be a good idea.
 
- Keep your CustomTypes simple !  They are just supposed to encapsulate a value and do nothing else.
```php
use Livaa\CustomTypes\CustomType;

class   Rgb
extends CustomType
{

    function validate(): void{
        
        // ... some code to validate the value
    }
    
    /* KEEP IT SIMPLE !.
     *      
     * This next method is a bad idea.
     * A customType is just supposed to verify and represent the data it does encapsulate.
     * It should not be doing anything else !          
     * If you feel the need to do something in this mood,    
     * you probably need something like a factory:
     * 
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
