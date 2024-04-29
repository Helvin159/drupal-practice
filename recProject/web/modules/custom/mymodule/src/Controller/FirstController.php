<?php 

/**
 * @file
 * Generates markup to be displayed. Functionality in this Controller is 
 * wired to Drupal in mymodule.routing.yml
 */
namespace Drupal\mymodule\Controller;

use Drupal\Core\Controller\ControllerBase;

class FirstController extends ControllerBase{

    public function simpleContent(){
      return [
        '#type' => 'markup',
        '#markup' => "Hello Drupla World. 
                        Time flies like an arrow, fruit flies like a banana",
      ];
    }

    public function variableContent($name_1, $name_2){
        return [
        '#type' => 'markup',
        '#markup' => $name_1 . " and " . $name_2 . " say hello to you!",
      ];
    }
}