<?php

//Print the exceptions
set_exception_handler(function ($exception) {

    echo "<div style=background-color:#b60013;color:#ff8c82;padding:20px>Catched exception: " . $exception . "</div>";
});

  
include("vendor/autoload.php");


use 

    Foo\Bar\Email,
    Foo\Bar\Power,
    Foo\Bar\Rgb;

/*
$email      = new Email("gokusan@kamehouse.com");
$power      = new Power(34);
$hair_color = new Rgb([45, 143, 184]);

echo $email->getValue()." ".$power->getValue()." ".$hair_color->getValue()."<br>";

echo "<br>";

echo $email." ".$power." ".$hair_color."<br>";

exit;*/



$email = new Email("wrongemail@example", false);

if($email->hasErrors()){
    
   var_dump($email->getErrors());
}

exit;    


class User{

    protected

        /**
         * @var PrimaryKey
         */
        $id, 
            
        /**
         * @var Firstname
         */
        $firstname,
            
        /**
         * @var Email
         */
        $email,
            
        /**
         * @var Ip
         */
        $ip;

    
    function getId(): ?PrimaryKey{

        return $this->id;
    }
    function setId(PrimaryKey $id): self{

        $this->id = $id;

        return $this;
    }
    

    function getFirstname(): ?Firstname{

        return $this->firstname;
    }
    function setFirstname(Firstname $firstname): self{

        $this->firstname = $firstname;

        return $this;
    }
    

    function getEmail(): ?Email{

        return $this->email;
    }
    function setEmail(Email $email): self{

        $this->email = $email;

        return $this;
    }
    
    
    function getIp(): ?Ip{

        return $this->ip;
    }
    function setIp(Ip $ip): self{

        $this->ip = $ip;

        return $this;
    }
}


try {

    $id         = new PrimaryKey(34);
    $firstname  = new Firstname("GOKU");
    $email      = new Email("soN.gOKu@gmail.com");
    $ip         = new Ip("192.168.1.3");
    $user       = (new User)
                    ->setId($id)
                    ->setEmail($email)
                    ->setFirstname($firstname)
                    ->setIp($ip);


    echo $user->getId() . " " . $user->getFirstname() . " " . $user->getEmail()." ".$user->getIp();

} catch (CustomDatatypeException $e) { echo $e; }


?>
