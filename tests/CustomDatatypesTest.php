<?php

include("vendor/autoload.php");

use     
    Foo\Bar\EmailAddress,
    Foo\Bar\Rgb,
    PHPUnit\Framework\TestCase;

class CustomDatatypesTest extends TestCase
{
    
    public function testEmail(){
            
        // --- Check lowercase normalization
        $email = new EmailAddress("SANGOKU@NAMEK.COM");
                
        $this->assertSame("sangoku@namek.com", $email->getValue());
        
               
        // --- Check empty email with $throw_exceptions to false
        $email = new EmailAddress("", false);
        
        $this->assertFalse($email->isValid());     
        
        $exception = $email->getErrors()[0];
        
        $this->assertSame("email_is_empty", $exception->getMessage());
        
        
        // --- Check invalid email with $throw_exceptions to false
        $email = new EmailAddress("sangoku.namek.com", false);
         
        $this->assertFalse($email->isValid());
        
        $exception = $email->getErrors()[0];
        
        $this->assertSame("email_is_invalid", $exception->getMessage());

    }
    
  
    
    public function testRgb(){        
        
        // --- Check __toString() json conversion when the value is an array
        $rgb = new Rgb([123,93,60]);
        
        $this->assertSame(json_encode([123,93,60]), $rgb->__toString());
        
        
        // --- Try feeding with a string with $throw_exceptions to false
        $rgb = new Rgb("123,93,333", false);
        
        $this->assertFalse($rgb->isValid());  
        
        $exception = $rgb->getErrors()[0];
        
        $this->assertSame("rgb_must_be_an_array", $exception->getMessage());
        
        
        // --- check wrong channels count with $throw_exceptions to false
        $rgb = new Rgb([123,93,60,0.5], false);
        
        $this->assertFalse($rgb->isValid());        
        
        $exception = $rgb->getErrors()[0];
        
        $this->assertSame("rgb_wrong_channels_count", $exception->getMessage());
        
                
        // --- check wrong rgb value with $throw_exceptions to false
        $rgb = new Rgb([123,93,333], false);
        
        $this->assertFalse($rgb->isValid());        
        
        $exception = $rgb->getErrors()[0];
        
        $this->assertSame("rgb_channel_value_out_of_range", $exception->getMessage());                             
    }
}
