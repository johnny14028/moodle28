<?php

class Model {
    
    public function getUser(){
        global $USER, $DB;
        $returnValue = NULL;
        $user = $DB->get_record('user', array('id'=>$USER->id));
        if(is_object($user)){
            $returnValue = $user;
        }
        return $returnValue;
    }
    
}