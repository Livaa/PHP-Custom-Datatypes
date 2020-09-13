<?php

namespace Foo\Bar;

use Livaa\CustomDatatypes\CustomDatatype;

class   Rgb
extends CustomDatatype
{

    function validate(): void{
        
        // the given value must be an array
        if( !is_array($this->value) ){
            
            $this->error("rgb_must_be_an_array");
        }
        // Rgb must be 3 channels
        else if(sizeof($this->value) !== 3){

            $this->error("rgb_wrong_channels_count");
        }
        //A channel value cannot be greater than 255
        else{
            
            foreach($this->value as $channel){

                if($channel > 255 || $channel < 0){

                    $this->error("rgb_channel_value_out_of_range");
                }
            } 
        }
    }
    
}




