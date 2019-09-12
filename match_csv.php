<?php

//$r_info = small_query("SELECT teamgame, t0, t1, t2, t3, t0score, t1score, t2score, t3score FROM uts_match WHERE id = '$mid'");
//if (!$r_info) die("Match not found");



function getPlayerName($id, $players){

    
    for($i = 0; $i < count($players); $i++){

        if($players[$i]['id'] == $id){
            return $players[$i]['name'];
        }
    }

    return 'Not Found';
}

function getTeamName($value){

    switch($value){

        case 0: {   return "Red Team";  } break;
        case 1: {   return "Blue Team";  } break;
        case 2: {   return "Green Team";  } break;
        case 3: {   return "Yellow Team";  } break;
        default: {  return "None Team"; } break;
    }
}


function convertArrayToCSV($data, $players){

    $string = "";


    $offset = 0;

    $titles = ["Name","Team","Country","Score","Frags","Deaths","Kills","Suicides","Efficiency","Accuracy","Monster Kills", "Godlikes",
    "Flag Grabs", "Flag Drops", "Flag Returns", "Flag Captures", "Flag Covers", "Flag Seals", "Flag Assists", "Flag Kills", "Flags Pickedup"];
    $badTitles = ["pid","team","country","gamescore","frags","deaths","kills","suicides","eff","accuracy","spree_monster","spree_god",
    "flag_taken","flag_dropped","flag_return","flag_capture","flag_cover","flag_seal","flag_assist","flag_kill","flag_pickedup"];

   

    $players = getPlayerNames($data);

    for($i = 0; $i < count($data); $i++){

        if($i == 0){

            foreach($data[$i] as $key => $value){

                $titleIndex = array_search($key,$badTitles, true);
               // echo 'TITLEINDEX = '.$titleIndex;
                if($titleIndex !== false){
                    $string.=ucwords($titles[$titleIndex]);
                }else{
                    $string.= ucwords($key);
                }

                if($offset < count($data[$i]) - 1){
                    $string.=",";
                }
                $offset++;
            }
            $string.="\n";
        }

        $offset = 0;
        
        foreach($data[$i] as $key => $value){

            if($key != "pid" && $key != "team"){

                $string.=$value;

            }else if($key == 'pid'){

                $string.=getPlayerName($value, $players);

            }else if($key == "team"){
                $string.=getTeamName($value);
            }

            if($offset < count($data[$i]) - 1){
                $string.=",";
            }

            $offset++;
        }
        $string.="\n";

    }
    //foreach($data as $key => $value){
      //  echo '['.$key.'] => '.$value.'<br>';
    //};
    return $string;
}


function getMatchPlayerData($mid){

    

    $query = "SELECT pid,team,country,gamescore,frags,deaths,kills,suicides,eff,accuracy,spree_monster,spree_god,
    flag_taken,flag_dropped,flag_return,flag_capture,flag_cover,flag_seal,flag_assist,flag_kill,flag_pickedup
    FROM uts_player WHERE matchid=".$mid;

    $stmt = mysql_query($query);

    $data = [];

   // $playerIds = [];

   //echo $query;

    while($d = mysql_fetch_array($stmt, true)){
        $data[] = $d;
       // $playerIds[] = $d['pid'];
    }

   // echo '<pre>';
   // print_r($data);
   // echo '</pre>';

    return $data;
}


function getPlayerNames($data){

    $playerIds = [];

    for($i = 0; $i < count($data); $i++){

        $playerIds[] = preg_replace("/\D/","",$data[$i]['pid']);
    }


  // .. echo 'Player Ids = <pre>';
   // print_r($playerIds);
   // echo '</pre>';


    $string = "";

    for($i = 0; $i < count($playerIds); $i++){

        $string.=$playerIds[$i];

        if($i < count($playerIds) - 1){
            $string.=",";
        }
    }

    //echo $string;

    $query = "SELECT id,name,country FROM uts_pinfo WHERE id IN(".$string.")";

    $players = [];

    if($stmt = mysql_query($query)){

        while($d = mysql_fetch_array($stmt, true)){

            $players[] = $d;
        }
    }else{
        echo '<br>MYSQL error';
    }

    return $players;

}


if(isset($_GET['mid'])){

    $mid = preg_replace("/\D/","",$mid);

    if($mid == ""){
        return;
    }

    


    //echo 'Match id = '.$mid;
    

    $data = getMatchPlayerData($mid);

    $players = getPlayerNames($data);

    //echo '<pre>';
    //print_r($players);
    //echo '</pre>';

    //echo '<pre>';
    //echo convertArrayToCSV($data, $players);
    //echo '</pre>';


}