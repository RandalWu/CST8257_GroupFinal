<?php
Class User {
    private $id;
    private $name;
    private $phone;
    private $password;
    
    public function __construct($id, $name, $phone, $password) {
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
        $this->password = $password;
    }
    
    public function getID() {
        return $this->id;
    }
    
    public function getName () {
        return $this->name;
    }
    
    public function getStrippedName () {
        return str_replace(' ', '', $this->name);
    }
    
    public function getPhone() {
        return $this->phone;
    }
    
    public function getPassword() {
        return $this->password;
    }
}

Class FriendDisplay {
    private $id;
    private $name;
    private $sharedAlbums;

    public function __construct($id, $name, $sharedAlbums)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sharedAlbums = $sharedAlbums;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSharedAlbums()
    {
        return $this->sharedAlbums;
    }

    public function setSharedAlbums($sharedAlbums)
    {
        $this->sharedAlbums = $sharedAlbums;
    }

}