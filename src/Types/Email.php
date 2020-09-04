<?php

namespace Foo\Bar;

use Livaa\CustomDatatypes\CustomDatatype;

class   Email
extends CustomDatatype
{

    function validate(): void{

        //--- normalization
        $this->value = trim(strtolower($this->value));
        
        //--- verifications
        if($this->value === ""){
            
            $this->error("empty_email");
        }
        
        if ( !filter_var($this->value, FILTER_VALIDATE_EMAIL) ){

            $this->error("invalid_email");
        }
                
    }
}




