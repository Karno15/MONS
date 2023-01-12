<?php
function addExp($PokemonId,$Exp){
  if ($PokemonId != NULL && $Exp != NULL) {
    require("connect.php");
    $pokemonSql = "select pk.Name,p.PokemonId,p.Level,p.Exp,pk.ExpType  from pokemon p
   JOIN pokedex pk ON p.PokedexId=pk.PokedexId where PokemonId=$PokemonId;";
    $result = $conn->query($pokemonSql);
    $row = $result->fetch_assoc();
    $tnlstr = $row['ExpType'] . "TNL";
    $lvl = $row['Level'];

    if ($lvl != 100) {
      //the big exp calculations
      $getlvlreq = "SELECT `" . $row['ExpType'] . "` as 'Total'  ,`$tnlstr` as 'Left' from exptype where Level=$lvl;";
      $resultreq = $conn->query($getlvlreq);
      $row1 = $resultreq->fetch_assoc();
      $memexp = $Exp;

      echo "<br>" . $row['Name'] . " (id:" . $PokemonId . ") gained " . $memexp . " exp!";
      
      //for example if mon has 8tnl and has 3 exp and get 5 he should get another level and 0 exp from new level
      $fromzero = $row['Exp'] - $row1['Total'];
      $tnl = $row1['Left'] - $fromzero;
      $newlevel = $lvl;

      while ($Exp >= $tnl) {
        $newlevel = $newlevel + 1;
        $Exp = $Exp - $tnl;
        $getlvlreq = "SELECT `" . $row['ExpType'] . "` as 'Total'  ,`$tnlstr` as 'Left' from exptype where Level=$newlevel;";
        $resultreq = $conn->query($getlvlreq);
        $row2 = $resultreq->fetch_assoc();
        $execute = $conn->query('UPDATE pokemon SET Level= ' . $newlevel . ' where PokemonId=' . $PokemonId);
        $execute = $conn->query('CALL fillMonData(' . $PokemonId . ', ' . $newlevel . ')');
        if (isset($row2['Left'])) {
          $tnl = $row2['Left'];
        } else {
          break;
        }
      }

      if ($newlevel == 100) {
        $newexp = $row2['Total'];
        $tnlmem = 0;
      } else {
        $newexp = $memexp + $row['Exp'];
        //need to get how much is left to next level after calculations
        $getlvlreq = "SELECT `" . $row['ExpType'] . "` as 'Total'  ,`$tnlstr` as 'Left' from exptype where Level=" . ($newlevel);
        $resultreq = $conn->query($getlvlreq);
        $row3 = $resultreq->fetch_assoc();
        $fromzerotnl = $newexp - $row3['Total'];
        $tnlmem = $row3['Left'] - $fromzerotnl;
      }

      //check if theres an evolution
      if ($newlevel != $lvl) {
        checkEvo($PokemonId, $newlevel);
      }
      $execute = $conn->query('UPDATE pokemon SET Exp= ' . $newexp . ' , ExpTNL='.$tnlmem.' where PokemonId=' . $PokemonId);

    } else {
      echo "No exp gained! Level Max!";
    }
  }
  return true;
}

function checkEvo($PokemonId, $newlevel){
  require("connect.php");
  $getevoreq = "SELECT e.PokedexId, e.Name, e.LevelReq, e.NameNew, e.PokedexIdNew FROM evos e
  JOIN pokemon p ON p.PokedexId=e.PokedexId where EvoType='EXP' AND p.ItemHeld!=5
  and p.PokemonId=$PokemonId";
  $result = $conn->query($getevoreq);
  if ($result->num_rows > 0) {
    $row3 = $result->fetch_assoc();
    if ($row3['LevelReq'] <= $newlevel) {
      echo "<br>What? " . $row3['Name'] . " is evolving!";
      $execute = $conn->query('UPDATE pokemon SET PokedexId=' . $row3['PokedexIdNew'] . ' where PokemonId=' . $PokemonId);
      echo "<br>Congratulations! your " . $row3['Name'] . " evolved into " . $row3['NameNew'] . "!";
    }else {
      echo "<br>No evolution yet!";
    }
  }else {
    echo "<br>No further evolution!";
  }
}
?>

