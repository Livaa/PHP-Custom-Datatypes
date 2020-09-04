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
     * Must throw a CustomDatatypeException if $this->throw_exceptions is true.    
     * Must push the exception into $this->exceptions[] if $this->throw_exceptions is false.
     * 
     * @param string $error The error string message.
     *                      It is recommanded to use a keywords strategy:
     *                      eg: "email_invalid", "authentication_failed", ...
     *                      It will make the frontend life easier.
     *                      
     * @throws CustomDataTypeException
     * 
     * @return void
     */
    function error(string $error): void;
                      

    /**
     * Must return true if they are errors, false otherwise.
     * 
     * @return bool
     */
    function hasErrors(): bool;
    
    
    /**
     * Must return the encapsulated value as a string.
     * Arrays must be converted to JSON strings.
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
