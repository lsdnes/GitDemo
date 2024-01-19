<?php

class Declarations {
    private $functionDeclarations = [];
    private $properties = [];
    private $required = [];
    private $contents = [];

    public function set_Role(string $role){        
        $this -> contents['role'] = $role;
    
        return $this;
    }

    public function set_Parts(array $parts){        
        $this -> contents['parts'] = $parts;

        return $this;   
    }

    public function get_contents(){
        
        return $this->contents;
    }
    
    
    
    public function set_Properties(array $p){
        foreach ($p as $key => $value) {
            $value[1] = ($value[1]=='')? "STRING" : $value[1];
            $this->properties[$value[0]] =array("type"=> $value[1],
                                                     "description"=>$value[2]
                                                    );
            $this->required[] = $value[0];
        }
           
        return $this;   
    }

    public function add_Declaration(string $name, string $description){
        $this->functionDeclarations[] = [
            'name' => $name,
            'description' => $description,
            'parameters' => array("type"=>"object",
                                   "properties" => $this->properties,
                                    "required" => $this->required
                                ),
            
        ];
        $this->properties = [];
        $this->required = [];
    }

    public function get_Declarations(){
        
        return $this->functionDeclarations;
    }
   

}



class ShowVariables {
    
}

?>