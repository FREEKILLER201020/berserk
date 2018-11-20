<?php

class Web_Clan
{
    public $id; // id клана
    public $name; // имя клана
    public $img;
    public $link;
    public $spy_link;
    public $created;

    public function __construct($i, $n, $c)
    {
        $this->id=$i;
        $this->name=$n;
        $this->created=$c;
    }

    public function FindImg(){
      $file  = file_get_contents(realpath(dirname(__FILE__))."/../clans.json");
      $img = json_decode($file, true);
      $this->img="clans/{$img[$this->id]}.jpg";
    }
    public function FindLink(){
      $file  = file_get_contents(realpath(dirname(__FILE__))."/../clans_links.json");
      $links = json_decode($file, true);
      $this->link=$links[$this->id];
    }
    public function FindSLink(){
      $file  = file_get_contents(realpath(dirname(__FILE__))."/../s_clans_links.json");
      $links = json_decode($file, true);
      $this->spy_link=$links[$this->id];
    }
}
 ?>
