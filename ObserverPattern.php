<?php
class UserList implements SplSubject
{
    private $_observers;
    private $_users = array();
    
    public function __construct()
    {
        $this->_observers = new SplObjectStorage();
    }
    
    public function addUser($user)
    {
        $this->_users[] = $user;
        $this->notify();
        return $this;
    }
    
    public function getUsers()
    {
        return $this->_users;
    }
    
    public function getLastUser()
    {
        $users = $this->getUsers();
        return end($users);
    }
    
    public function attach(SplObserver $observer)
    {
        $this->_observers->attach($observer);
        return $this;
    }
    
    public function detach(SplObserver $observer)
    {
        $this->_observers->detach($observer);
        return $this;
    }
    
    public function notify()
    {
        foreach ($this->_observers as $observer) {
            $observer->update($this);
        }
        return $this;
    }
}
class UserListLogger implements SplObserver
{
    public function update(SplSubject $subject)
    {
        printf('User created : "%s"', $subject->getLastUser());
    }
}
$ul = new UserList();
$ul->attach(new UserListLogger());
$ul->addUser('Jack');
