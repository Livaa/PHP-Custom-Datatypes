<?php

include("vendor/autoload.php");


use     
    Foo\Bar\EmailAddress,
    Foo\Bar\Rgb,
    PHPUnit\Framework\TestCase,
    Livaa\CustomDatatypes\CustomDatatypeException;

class CustomDatatypesTest extends TestCase
{
    
    public function testEmail(){
            
        // --- Check lowercase normalization
        $email = new EmailAddress("SANGOKU@NAMEK.COM");
                
        $this->assertSame("sangoku@namek.com", $email->getValue());
        
                             
        // --- Check empty email with $throw_exceptions at false
        $email = new EmailAddress("", false);
         
        $this->assertTrue($email->hasErrors());        
        $this->assertCount(1, $email->getErrors());
        
        $this->assertSame("empty_email", $email->getErrors()[0]->getMessage());
        
        
        // --- Check invalid email with $throw_exceptions at false
        $email = new EmailAddress("sangoku.namek.com", false);
         
        $this->assertTrue($email->hasErrors());        
        $this->assertCount(1, $email->getErrors());
        
        $this->assertSame("invalid_email", $email->getErrors()[0]->getMessage());

    }
    
  
    
    public function testRgb(){        
        
        // --- Check __toString() json conversion when the value is an array
        $rgb = new Rgb([123,93,60]);
        
        $this->assertSame( json_encode([123,93,60]), $rgb->__toString() );
        
        
        // --- check wrong rgb channel value with $throw_exceptions at false
        $rgb = new Rgb([123,93,333], false);
        
        $this->assertTrue($rgb->hasErrors());        
        $this->assertCount(1, $rgb->getErrors());
        
        $exception = $rgb->getErrors()[0];
        
        $this->assertSame("rgb_channel_value_invalid", $exception->getMessage());
        
        
        // --- Try feeding with a string with $throw_exceptions at false
        $rgb = new Rgb("123,93,333", false);
        
        $this->assertTrue($rgb->hasErrors());        
        $this->assertCount(1, $rgb->getErrors());
        
        $exception = $rgb->getErrors()[0];
        
        $this->assertSame("rgb_must_be_an_array", $exception->getMessage());

    }
}
