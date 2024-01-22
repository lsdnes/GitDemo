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

    public function GeminiPro($post_fields){
        $ch = curl_init();
        $api_key = $api_keys[rand(0,1)];
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=$api_key";
        $header  = [
                    'Content-Type: application/json'
        ];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_fields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        $string =  $result;
        if (curl_errno($ch)) {
            $string = 'Error: ' . curl_error($ch);
        }
        curl_close($ch);
        return $string;
    }
   

}



class ShowVariables {
    
}

?>