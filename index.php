<?php
    session_start();
    require('connect.php');
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mons</title>
    <link rel='stylesheet' href='css/bootstrap.min.css'>
    <link rel='stylesheet' href='css/style.css'>
    <script defer src="js/script.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
</head>
<body>
<audio src="OST/titleScreen.mp3" id="musicBG" loop="loop"></audio>

<div id="overlay" onclick="exitPopup()"></div>
<div class='title'>
<button onclick ="turnOffBGM()"></button>
    <span> MONS </span>
</div>
<div class='trainerInfo'>
<form action="chUser.php" method="post">

      Pick user: <select name="Login" id="" onchange="this.form.submit()">
      <option value="."> </option>
      <option value="." onclick="turnOffBGM()">none</option>
      <?php
          


              $queryLogins = "select Login from users";
              $resultLogins= $conn->query($queryLogins);
          while($rowLogins = $resultLogins->fetch_assoc()) {
                  echo $rowLogins["Login"];
                 echo '<option value='.$rowLogins["Login"].'>'.$rowLogins["Login"].'</option>';
                }
 
      ?>
      </select>
  </form>
</div>
<?php
      if(!isset($_COOKIE["UserId"]))
      {
        echo "User not set";

      }
      else
      {
        setcookie("UserId",$_SESSION["UserId"], time() + 300);
  ?>
<div class='addMon'>
<form method="post" action="addmon.php">
      DexID: <input type="number" name="PokedexId" id="pokedexnum">
      Level: <input type="number" name="Level" id="pokedexnum">
<input type="submit" value="Add a pokemon!" name='addMonButt'>
    </form>
    <form method="post" action="index.php">
      pokemonID: <input type="number" name="PokemonId" id="PokemonId">
      Exp: <input type="number" name="exp" id="exp">
<input type="submit" value="Add some exp!" name='addExpButt'>
    </form>
<?php

if(isset($_POST['addExpButt'])){
    require('func.php');
    addExp($_POST['PokemonId'],$_POST['exp']);
}
?>
</div>


<?php
 

    $trainerSql = "SELECT UserId,Login,Status,Created from users where UserId=" . $_SESSION["UserId"];
    $result1 = $conn->query($trainerSql);

    if ($result1) {
      // output data of trainer
     $row = $result1->fetch_assoc();
        echo "Current Trainer: " . $row["Login"]. "</br>"; 
        echo "Start date: " . $row["Created"]. "</br>";
        $userId= $row['UserId'];
    } else {
      echo "Invalid user";
    }
    

    echo "Your pokemon: </br> <ol>";
    $pokemonSql = " CALL showPartyMonMoves(".$userId.")";
    $result = $conn->query($pokemonSql);
    $monjson = array();
    if ($result->num_rows > 0) {
        // output data of each mon
        while($rowmons = $result->fetch_assoc()) {
          array_push($monjson,$rowmons);
        }


        $getmonids = array();
        foreach($monjson as $row){
          if(!in_array($row['PokemonId'],$getmonids))
      array_push($getmonids, $row['PokemonId']);
 
         }



      $currentmon = array();
      $index = 0;
      foreach ($getmonids as $ids) {
        foreach ($monjson as $piece) {
          if ($piece["PokemonId"] == $ids) {
            array_push($currentmon, $piece);
          } 
        }

//give index to buttonids
$index += 1;

echo "<li> #" . $currentmon[0]["PokedexId"] . " " . $currentmon[0]["Name"] . ", Type: "
. $currentmon[0]["Type1"];
if ($currentmon[0]["Type2"]) {
echo "/" . $currentmon[0]["Type2"];
}
echo ", Level: " . $currentmon[0]["Level"] . ", HP: " . $currentmon[0]["HPLeft"] . "/" . $currentmon[0]["HP"] .
"</br></li>";
        echo "<input type='button' onclick='popupMonsData(";
echo  json_encode($currentmon).",".json_encode($getmonids);
echo ")' id=monid".$index."  value='SUMMARY'>";

//clear array after returning data to return new data
$currentmon = [];
      }
      } else {
        echo "You have no Pokemon in the Party!";
      }
//PDO Calling a procedure (!)


      echo "</ol>Pokemon in Box: </br> <ol>";
      try {
        
          $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
          // execute the stored procedure
          $sql = 'CALL showBoxData('.$userId.')';
          // call the stored procedure
          $q = $pdo->query($sql);
          $q->setFetchMode(PDO::FETCH_ASSOC);
      } catch (PDOException $e) {
          die("Error occurred:" . $e->getMessage());
      }
      if ($q->rowCount() > 0) {
 while ($row = $q->fetch()){
  echo "<li> #" . $row["PokedexId"]." ". $row["Name"]. ", Type: " 
  . $row["Type1"];
  if($row["Type2"]) {
    echo "/" . $row["Type2"];
  }
  echo ", Level: ".$row["Level"].", HP: ".$row["HPLeft"]."/".$row["HP"]." </br></li>";

 }
      }
      else{
        echo "You have no Pokemon in the Box!";
      }
    }

?>

<div id='popup'>
        <div id="popup-header"></div>
        <div id="popup-body">
          <div id="imag"><img id='monSprite' src="" alt="monSprite"><br>
          No.<span id="monNo">112</span></div>
          <div id="hps"><h2 id='monName'>RHYDON</h2>
            L<span id='level'>14</span>
            <div id="BarHP">

  <div id="HPBar-left">
 <div id="HPBar"></div> 
</div>
    </div>
    <b>HP:</b>  <span id='hpcount'>36/36</span> <br> 
            <b>STATUS:</b> <span id="status">OK</span>
          </div>
          <div id="stats">
            <table>
              <tr>
                <td>Attack</td><td id='attack'>6</td>
              </tr>
              <tr>
                <td>Defense</td><td id='defense'>19</td>
              </tr>
              <tr>
                <td>SpAtk</td><td id='spatk'>12</td>
              </tr>
              <tr>
                <td>SpDef</td><td id='spdef'>13</td>
              </tr>
              <tr>
                <td>Speed</td><td id='speed'>55</td>
              </tr>
            </table>
          </div>
          <div id="types">
           <B> TYPE1:</b> <span id="type1">GROUND</span><br>
            <b id='type2text'>TYPE2:</b> <span id='type2'>ROCK</span><br>
           <b> IDNo:</b> <span id="otid">38170</span><br>
            <b>OT:</b> <span id="otname">MET<br>
          </div>
        </div>
        <div id="buttonbar">
          </div>
</div>
<!-- next page of popup- moves-->
<div id="popupmoves">
<div id="popup-header"><b>MONS MOVES</b>
<button onclick='exitPopup()' id='exit'>EXIT</button></div>
        <div id="popup-body">
          <div id="imag"><img id='mmonSprite' src="https://www.pokencyclopedia.info/sprites/misc/missingno/spr_1_000.png" alt="monSprite"><br>
          No.<span id="mmonNo">112</span></div>
          <div id="lvl"><h2 id='mmonName'>RHYDON</h2>
          <b>EXP POINTS:</b> <div id="exptotal"> 2216     
</div>
<b>LEVEL UP: </b>
<div id="exptnl">528 to L14</div>
<b>STATUS:</b> <span id="mstatus">OK</span>
          </div>
          <div id="moves">
            <table>
              <tr>
                <td id='movename1'>-</td><td id='movepp1'>--</td>
              </tr>
              <tr>
                <td id='movename2'>-</td><td id='movepp2'>--</td>
              </tr>
              <tr>
                <td id='movename3'>-</td><td id='movepp3'>--</td>
              </tr>
              <tr>
                <td id='movename4'>-</td><td id='movepp4'>--</td>
              </tr>
           </table>
          </div>
        </div>
        <div id="buttonbarmoves">
          </div>
</div>
</body>
</html>