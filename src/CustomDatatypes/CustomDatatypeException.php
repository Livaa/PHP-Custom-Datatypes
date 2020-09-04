<?php

namespace Livaa\CustomDatatypes;

class   CustomDatatypeException
extends \InvalidArgumentException
{
    protected 
            
        $message,
        $file,
        $line;
    
    function __construct($message, $file, $line){
        
        // To trigger the parent __construct() is recommanded by the doc.
        parent::__construct(); 
        
        $this->message  = $message;
        $this->file     = $file;
        $this->line     = $line;
    }
   
    
    function __toString(){
        
        return (string)$this->message;
    }
    
} 

