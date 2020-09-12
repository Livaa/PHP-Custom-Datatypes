<?php

namespace Foo\Bar;

use Livaa\CustomDatatypes\CustomDatatype;

class   EmailAddress
extends CustomDatatype
{

    function validate(): void{

        //--- normalization
        $this->value = trim(strtolower($this->value));
        
        //--- verifications
        error_log(strlen($this->value));
        
        if(strlen($this->value) === 0){
            
            $this->error("empty_email");
        }        
        else if ( !filter_var($this->value, FILTER_VALIDATE_EMAIL) ){

            $this->error("invalid_email");
        }
                
    }
}




