<?php

namespace Livaa\CustomDatatypes;

interface InterfaceCustomDatatype
{

    /**
     * @param mixed $value The value to encapsulate.
     * @param bool $throw_exceptions Enable/disable exceptions during the validation process. True by default.
     */
    function __construct($value = null, bool $throw_exceptions = true);

    
    /**
     * It must verify and normalize this->value.
     * Invoke $this->error("error_message") in case of error.
     * See examples for more details.
     * 
     * @return void
     */
    function validate(): void;
     
     
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
    function error(string $error): void;
       
        
    /**
     * Write the given exception details into the server logs.  
     * If for some reason you are not using the logs of the server, you'll need
     * to overwrite this method.
     * 
     * @param string $error
     * 
     * @return void
     */
    function logError(CustomDataTypeException $error): void;
    

    /**
     * Return true if they are errors, false otherwise.
     * 
     * @return bool
     */
    function hasErrors(): bool;
    
    
    /**
     * Return the value as a string.
     * Arrays are converted to JSON strings.
     * 
     * @return string
     */
    function __toString();
    
    
    /**
     * $this->value getter.    
     * 
     * @return mixed
     */
    function getValue();
    
    
    /**
     * $this->exceptions getter.
     * 
     * @return array An array of CustomDatatypeException.
     */
    function getErrors(): array;
        
}
