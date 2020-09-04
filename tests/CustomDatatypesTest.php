<?php

include("vendor/autoload.php");


use 
    Foo\Bar\Email,
    Foo\Bar\Rgb,
    PHPUnit\Framework\TestCase,
    Livaa\CustomDatatypes\CustomDatatypeException;

class CustomDatatypesTest extends TestCase
{
    
    public function testEmail(){
            
        // --- Check lowercase normalization
        $email = new Email("SanGoku@Namek.Com");
                
        $this->assertSame("sangoku@namek.com", $email->getValue());
        
        
        // --- Check exception throw
        $this->expectException(CustomDatatypeException::class);
        
        $email = new Email("sangoku@namek");
                     
        
        // --- Check invalid email + disable exception & read error
        $email = new Email("", false);
         
        $this->assertTrue($email->hasErrors());        
        $this->assertCount(1, $email->getErrors());
        
        $exception = $email->getErrors()[0];
        
        $this->assertSame("invalid_email", $exception->getMessage());
    }
    
    
    public function testRgb(){        
        
        // --- Check __toString() json conversion when the value is an array
        $rgb = new Rgb([123,93,60]);
        
        $this->assertSame( json_encode([123,93,60]), $rgb->__toString() );
        
        
        // --- check wrong rgb channel value + disable exception & read error
        $rgb = new Rgb([123,93,333], false);
        
        $this->assertTrue($rgb->hasErrors());        
        $this->assertCount(1, $rgb->getErrors());
        
        $exception = $rgb->getErrors()[0];
        
        $this->assertSame("rgb_channel_value_invalid", $exception->getMessage());
        
        
        // --- Try feeding with a string + disable exception & read error
        $rgb = new Rgb("123,93,333", false);
        
        $this->assertTrue($rgb->hasErrors());        
        $this->assertCount(1, $rgb->getErrors());
        
        $exception = $rgb->getErrors()[0];
        
        $this->assertSame("rgb_must_be_an_array", $exception->getMessage());

    }
}
