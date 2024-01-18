<?php

class Declarations {
    private $functionDeclarations = [];
    private $properties = [];
    private $required = [];
    private $contents = [];

    public function setContents(array $c){
        foreach ($p as $key => $value) {
            $value[1] = ($value[1]=='')? "STRING" : $value[1];
            $this->properties[$value[0]] =array("type"=> $value[1],
                                                     "description"=>$value[2]
                                                    );
            $this->required[] = $value[0];
        }
           
        return $this;   
    }


    public function setProperties(array $p){
        foreach ($p as $key => $value) {
            $value[1] = ($value[1]=='')? "STRING" : $value[1];
            $this->properties[$value[0]] =array("type"=> $value[1],
                                                     "description"=>$value[2]
                                                    );
            $this->required[] = $value[0];
        }
           
        return $this;   
    }

    public function addDeclaration(string $name, string $description){
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

    public function getDeclarations(){
        
        return $this->functionDeclarations;
    }

    

}



?>