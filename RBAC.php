<?php
class Permissions
{
	protected $permissions;
    public function __construct(){
		if(!file_exists('permissions.json.php'))
			file_put_contents('permissions.json.php', json_encode("{}"));
        $this->permissions = json_decode(file_get_contents('permissions.json.php'));
    }
	
	public function add($perm_name, $perm_desc = null) {
		if(isset($this->permissions[$perm_name])){
			return false;
		}else{
			$this->permissions[$perm_name] = $perm_desc;
			file_put_contents('permissions.json.php', json_encode($this->permissions));
			return true;
		}
	}
	
	public function delete($perm_name){
		if(isset($this->permissions[$perm_name])){
			if(file_exists('rols.json.php')){
				$roles = json_decode(file_get_contents('rols.json.php'));
				$out = [];
				$perm_key = $this->key($perm_name);
				$perms_len = count($this->permissions)-1;
				foreach($roles as $key=>$value){
					$out[$key] = $value;
					if(isset($value[$perm_key]))
						$out[$key] = substr($value, $perm_key, 1);
					if($perms_len != strlen($out[$key]))
						$out[$key] = str_pad('', $perms_len, '0');
				}
				file_put_contents('rols.json.php', json_encode($out));
			}
			unset($this->permissions[$perm_name]);
			file_put_contents('permissions.json.php', json_encode($this->permissions));
			return true;
		}else{
			return false;
		}
	}
	
    protected function key($value)
    {
        return array_search($value, $this->permissions);
    }
}
Class Roles extends Permissions
{
	protected $roles;
	protected $roles_file;
	
    public function __construct(){
		$this->roles_file = 'rols.json.php';
		if(!file_exists($this->roles_file)){
			$this->roles = "{}";
			$this->put();
		}
        $this->roles = $this->get();
		parent::__construct();
    }
	
	public function hasPerm($role_name, $perm_name){
		if(!isset($this->roles[$role_name]))
			return false;
		if(!isset($this->roles[$role_name][$this->key($perm_name)]))
			return false;
		if($this->roles[$role_name][$this->key($perm_name)] != 1)
			return false;
		else
			return true;
			
	}
	
	public function hasRole($role_name){
		if(!isset($this->roles[$role_name]))
			return false;
		else
			return true;
			
	}
	
	public function add($role_name){
		$role_perm = str_pad('', count($this->permissions), '0');
        foreach($this->permissions as $value){
            if(isset($_POST[$value])){
                $role_perm[$this->key($value)] = 1;
            }
        }
		$this->roles[$role_name] = $role_perm;
		$this->put();
	}
	
	public function edit($role_name){
		$this->add($role_name);
	}
	
	public function delete($role_name){
		if(isset($this->roles[$role_name]))
			unset($this->roles[$role_name]);
		$this->put();	
	}
	
	private function get(){
		return json_decode(file_get_contents($this->roles_file));
	}
	
	private function put(){
		file_put_contents($this->roles_file, json_decode($this->roles));
	}
}
class UserRoles
{
	protected $users_roles;
	protected $users_roles_file;
	protected $user_roles;
	protected $user_id;
	
    public function __construct($user_id){
		$this->user_id = $user_id;
		$this->users_roles_file = 'users_roles.json.php';
		if(!file_exists($this->users_roles_file)){
			$this->users_roles = "{}";
			$this->put();
		}
        $this->users_roles = $this->get();
        $this->user_roles = $this->getUserRoles();
    }
	
	
	public function hasPerm($perm_name){
		$roles = new Roles();
		foreach($this->user_roles as $role){
			if($roles->($role ,$perm_name))
				return true;
		}
		return false;
	}
	
	public function add($role_name, $role_desc = null){
		$roles = new Roles();
		if(!$roles->hasRole($role_name))
			return false;
		if(!isset($this->users_roles[$this->user_id])){
			$this->users_roles[$this->user_id] = [$role_name=>$role_desc];
		}else{
			$this->users_roles[$this->user_id][$role_name] = $role_desc;
		}
		$this->user_roles = $this->users_roles[$this->user_id];
		$this->put();
		return true;	
	}
	
	public function delete($role_name){
		$roles = new Roles();
		if(isset($this->users_roles[$this->user_id][$role_name])){
			unset($this->users_roles[$this->user_id][$role_name]);
			$this->user_roles = $this->users_roles[$this->user_id];
			$this->put();
			return true;
		}
		return false;
	}
	
	private function getUserRoles(){
		if(isset($this->users_roles[$this->id])){
			return $this->users_roles[$this->id];
		}else{
			return [];
		}
	}
	private function get(){
		return json_decode(file_get_contents($this->users_roles_file));
	}
	
	private function put(){
		file_put_contents($this->users_roles_file, json_decode($this->users_roles));
	}
}
