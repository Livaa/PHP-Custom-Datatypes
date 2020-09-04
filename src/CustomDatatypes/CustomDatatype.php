<?php

namespace Livaa\CustomDatatypes;

abstract class CustomDatatype
//@todo: implements      InterfaceCustomDatatype
{   
    
    protected
                    
        /**
         * @var mixed $value
         */
        $value,
            
            
        /**
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
    function __construct($value = null, bool $throw_exceptions = true){
        
        $this->value             = $value;
        $this->throw_exceptions  = $throw_exceptions;
  
        $this->validate();
    }       

    
    /**
     * It must verify and normalize this->value.
     * Invoke $this->error("error_message") in case of error.
     * See examples for more details.
     * 
     * @return void
     */
    abstract function validate(): void;
     
     
    /**
     * - Build a new CustomDatatypeException from the given error & the debug_trace() details.
     * - Log the exception on the logs of the server ($this->logError()).
     * - Throws the exception if $this->throw_exceptions is true.    
     * - Push the exception into $this->exceptions[] if $this->throw_exceptions is false.
     * 
     * @param string $error
     * @throws CustomDataTypeException
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
    function logError(CustomDataTypeException $error): void{          

        error_log("CustomDataTypes error: ".$error->getMessage()." at ".$error->getFile()." line ".$error->getLine());
        error_log("CustomDataTypes value: ".$this->__toString());
    }
    

    /**
     * Return true if they are errors, false otherwise.
     * 
     * @return bool
     */
    function hasErrors(): bool{                

        return sizeof($this->exceptions) > 0;
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