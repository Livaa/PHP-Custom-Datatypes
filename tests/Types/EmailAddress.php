<?php

namespace Foo\Bar;

use Livaa\CustomDatatypes\CustomDatatype;

class   EmailAddress
extends CustomDatatype
{

    function validate(): void{

        //--- normalization & casting
        $this->value = trim(strtolower($this->value));
        
        //--- verifications       
        if(strlen($this->value) === 0){
            
            $this->error("email_is_empty");
        }        
        else if ( !filter_var($this->value, FILTER_VALIDATE_EMAIL) ){

            $this->error("email_is_invalid");
        }
                
    }
}




