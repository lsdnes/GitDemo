<?php
// Example usage:
require_once('functiontools.php');
require_once('userinfo.php');


$declarations = new Declarations();


$p = array("location" => "The city and state, e.g. San Francisco, CA or a zip code e.g. 95616",
            "movie" => "ny movie title",
            "go" => "abc13"
        );
$declarations->setProperties($p);
$declarations->addDeclaration("1234", "whore are you");

$p = array("location" => "city and state, e.g. San Francisco, CA or a zip code e.g. 95616",
            "movie" => "ny title"
        );
$declarations->setProperties($p);
$declarations->addDeclaration("789", "whor you");

$tools = $declarations->getDeclarations();
$post_fields['tools']=[array("function_declarations"=>$tools)];

echo json_encode($post_fields);



?>
