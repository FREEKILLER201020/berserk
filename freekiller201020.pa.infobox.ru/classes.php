<?php
 class Clan
 {
     public $id; // id клана
     public $name; // имя клана
     public $players=array(); // массив игроков состаящих в клане
     public $killers=array();
     public $deads=array();

     public function __construct($i, $n)
     {
         $this->id=$i;
         $this->name=$n;
     }
     public function UpdatePlayers()
     {
         $data=GetClanData($this->id);
         // print_r($data);
         foreach ($data["players"] as $player_tmp) {
             $pl=new Player($player_tmp["id"], $player_tmp["nick"], $player_tmp["frags"], $player_tmp["deaths"], $player_tmp["level"], $this->id);
             $was=0;
             foreach ($this->players as $player) {
                 if ($player->id==$pl->id) {
                     $was=1;
                     $tmp=$player->Update($pl);
                     for ($i=0;$i<$tmp["frags"];$i++) {
                         // echo $player->nick." is a killer!\n";
                         array_push($this->killers, null);
                         $this->killers[count($this->killers)-1]=$player;
                     }
                     for ($i=0;$i<$tmp["deaths"];$i++) {
                         // echo $player->nick." is a dead!\n";
                         array_push($this->deads, null);
                         $this->deads[count($this->deads)-1]=$player;
                     }
                 }
             }
             if ($was !=1) {
                 array_push($this->players, $pl);
             }
         }
     }

     public function unsett_killer($id)
     {
         foreach ($this->killers as $key => $player) {
             if ($player->id==$id) {
                 unset($this->killers[$key]);
             }
         }
     }
     public function unsett_dead($id)
     {
         foreach ($this->dead as $key => $player) {
             if ($player->id==$id) {
                 unset($this->dead[$key]);
             }
         }
     }
 }

 class Player
 {
     public $id; // id игрока
     public $nick; // ник игрока
     public $frags; // количество его фрагов
     public $deaths; // количество его смертей
     public $level; // его уровень (пока не используется)
     public $clan_id; // id клана, в котором состоит игрок

     public function __construct($i, $n, $f, $d, $l, $c)
     {
         $this->id=$i;
         $this->nick=$n;
         $this->frags=$f;
         $this->deaths=$d;
         $this->level=$l;
         $this->clan_id=$c;
     }
     public function Update($pl)
     {
         $ret=["frags"=>$pl->frags-$this->frags,"deaths"=>$pl->deaths-$this->deaths];
         $this->nick=$pl->nick;
         $this->frags=$pl->frags;
         $this->deaths=$pl->deaths;
         $this->level=$pl->level;
         return $ret;
     }
     public function CP()
     {
         $pl=new Player($this->id, $this->nick, $this->frags, $this->deaths, $this->level, $this->clan_id);
         return $pl;
     }
 }

 class Time
 {
     public $saved_time; // сохраненное время

     public function __construct() // при создании объекта, запоминаем время, когда он был создан
     {
         $this->saved_time=time();
     }
     public function Update() // установить сохраненное время на  текущий момент
     {
         $this->saved_time=time();
         return $this->saved_time;
     }
     public function DeltaTime() // вернуть разницу между текущим времинем и сохраненным (в юникс секундах)
     {
         return time()-$this->saved_time;
     }
 }

 class Fight
 {
     public $id;
     public $attacker_id; // id атакующего клана
     public $defender_id; // id защищающегося клана
     public $declared; // время, когда был объявлен бой
     public $resolved; // вермя, когда состаится бой
     public $in_progress; // флаг, активен ли бой
     public $type="null";
     public $for_delete=0;
     public $updated=0;

     public $log=array();
     public $tickets=array();

     public function __construct($id, $a, $d, $de, $r, $u)
     {
         $this->id=$id;
         $this->attacker_id=$a;
         $this->defender_id=$d;
         $this->declared=$de;
         $this->resolved=$r;
         $this->in_progress=0;
         $this->updated=$u;
     }
 }
