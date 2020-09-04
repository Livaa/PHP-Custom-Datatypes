<?php

namespace Livaa\CustomTypes;

interface InterfaceCustomType
{


    /**
     * @param mixed The value to encapsulate.
     * @param bool Throw exceptions in case of errors, true by default
     */
    function __construct($value=null, bool $throw_exceptions = true);


    /**
     * Verify if the encapsulated value is valid.
     * In case of error, invoke: $this->error("the_error_message");     
     *
     * If a normalization has to be applied on the value, this method is the
     * good place to do so.
     *
     * @return void
     */
    function validate(): void;


    /**
     * Must return the value in it's primitive type.
     * eg: An email would be returned as a string, an RGB as an array, an age as an int, ...
     *
     * @return mixed
     */
    function getValue();
    

    /**
     * Must return the encapsulated value as a string.
     * Arrays should be returned as JSON strings.
     * 
     * @return string
     */
    function __toString();
    
    
    /**
     * Should be called when the encapsulated value is not correct.
     * 
     * 1 - Must save the error in $this->errors
     * 2 - Must call $this->logError();
     * 3 - If $this->throw_exceptions is true, must throw an exception
     * 
     * @return void
     * @throw CustomTypeException
     */
    function error(string $error_message):void;
    
    
    /**
     * Return this->error
     *
     * @return null or string
     */
    function getError(): ?string;
    
    
    /**
     * Should log the context where the error happened, like the file and the line number.
     * 
     * @return void
     */
    function logError(string $error): void;

}
