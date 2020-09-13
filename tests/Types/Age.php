<?php

namespace Foo\Bar;

use Livaa\CustomDatatypes\CustomDatatype;

class   Age
extends CustomDatatype
{

    function validate(): void{
        
        // The cast is important so we make sure getValue() 
        // will return the value with the right type.
        $this->value = (int)$this->value;
                
        if($this->value <= 0){

            $this->error("age_is_invalid");
        }            
            
    }
    
}




