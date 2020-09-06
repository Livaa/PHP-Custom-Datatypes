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
        else{
                           
            // rgb are 3 channels
            if( sizeof($this->value) !== 3 ){

                $this->error("rgb_out_of_range");
            }

            // checking the integrity of each channel
            foreach($this->value as $channel){

                if($channel > 255 || $channel < 0){

                    $this->error("rgb_channel_value_invalid");
                }
            } 
        }
    }
    
    
    /* KEEP IT SIMPLE.
     *      
     * This method is a bad idea.
     * A CustomDatatype is object is just supposed to verify and represent 
     * the data it does encapsulates.
     * It should not be doing anything else !          
     * If you feel the need to do something in this mood,    
     * you probably need something like a factory:
     * 
     * eg: (new ColorsFactory)->rgbToRgba( new Rgb([174, 189, 216]) );
     */  
    function toRgba(){
        
        //add a 4th channel
        $this->value[] = 1;
        
        return $this->value;
    }
}




