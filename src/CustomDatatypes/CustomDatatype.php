<?php

namespace Livaa\CustomDatatypes;

abstract class  CustomDatatype
implements      InterfaceCustomDatatype
{   
    
    public
                    
        /**
         * Represents the encapsulated value.
         * It has a getter, getValue(), that i personaly 
         * prefer to use in my code. But to have it into
         * the public scope makes sense as the object can 
         * be programaticaly called by reflection
         * or get_object_vars() and so ...
         *
         * @var mixed $value
         */
        $value,
            
    protected
        
        /**
         * Enable/Disable exceptions throwing.
         *
         * @var bool $throw_exceptions 
         */
        $throw_exceptions,            
            
            
        /**
         * @var array $exceptions
         */
        $exceptions = [];
           
    
    /**
     * @param mixed $value The value to encapsulate.
     * @param bool $throw_exceptions Enable/disable exceptions during the validation process. True by default.
     */
    function __construct($value = null, $throw_exceptions = true){
        
        $this->value             = $value;
        $this->throw_exceptions  = $throw_exceptions;
  
        $this->validate();
    }       

    
    /**
     * It must verify and normalize $this->value.
     * Invoke $this->error("error_message") in case of error.
     * See examples for more details.
     * 
     * @return void
     */
    abstract function validate(): void;
     
     
    /**
     * Log the exception details into the server logs then 
     * throw the exception or collect it.
     *
     * @param string $error
     * @throws CustomDatatypeException
     * 
     * @return void
     */
    function error(string $error): void{        

        $trace           = debug_backtrace(); 
        $source_error    = array_pop($trace);
        $exception       = New CustomDatatypeException($error, $source_error["file"], $source_error["line"]);                
  
        $this->logError($exception);

        if($this->throw_exceptions){
            
            throw $exception;
        }
        else{
            
            $this->exceptions[] = $exception;
        }
    }
       
        
    /**
     * Write the given exception details into the server logs.  
     * If for some reason you are not using the logs of the server, you'll need
     * to overwrite this method.
     * 
     * @param string $error
     * 
     * @return void
     */
    function logError(CustomDatatypeException $error): void{          

        error_log("Custom datatype error: ".$error->getMessage()." at ".$error->getFile()." line ".$error->getLine());
        error_log("Custom datatype value was: (".gettype($this->getValue()).") ".$this->__toString());
    }
    

    /**
     * Return true if they are errors, false otherwise.
     * 
     * @return bool
     */
    function isValid(): bool{                

        return sizeof($this->exceptions) === 0;
    }
    
    
    /**
     * Return the value as a string.
     * Arrays are converted to JSON strings.
     * 
     * @return string
     */
    function __toString(){
        
        $value = $this->value;

        return is_array($value) ? json_encode($value) : (string)$value;
    }            
    
    
    /**
     * $this->value getter.    
     * 
     * @return mixed
     */
    function getValue(){
        
        return $this->value;
    }
    
    
    /**
     * $this->exceptions getter.
     * 
     * @return array An array of CustomDatatypeException.
     */
    function getErrors() : array{
  
        return $this->exceptions;
    }    
    
}
