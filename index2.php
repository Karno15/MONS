<?php
    session_start();
    require('connect.php');

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
</head>
<body>
<button data-modal-target="#modal">Open Modal</button>
  <div class="modal" id="modal">
    <div class="modal-header">
      <div class="title">Example Modal</div>
      <button data-close-button class="close-button">&times;</button>
    </div>
    <div class="modal-body">
      Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse quod alias ut illo doloremque eum ipsum obcaecati distinctio debitis reiciendis quae quia soluta totam doloribus quos nesciunt necessitatibus, consectetur quisquam accusamus ex, dolorum, dicta vel? Nostrum voluptatem totam, molestiae rem at ad autem dolor ex aperiam. Amet assumenda eos architecto, dolor placeat deserunt voluptatibus tenetur sint officiis perferendis atque! Voluptatem maxime eius eum dolorem dolor exercitationem quis iusto totam! Repudiandae nobis nesciunt sequi iure! Eligendi, eius libero. Ex, repellat sapiente!
    </div>
  </div>
  <div id="overlay"></div>
<div class='title'>

    <span> MONS </span>
</div>
<div class='trainerInfo'>
<form action="chUser.php" method="post">

      Pick user: <select name="Login" id="" onchange="this.form.submit()">
      <option value="."> </option>
      <option value=".">none</option>
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
      if(!isset($_SESSION["UserId"]))
      {
        echo "User not set";
      }
      else
      {
  ?>
<div class='addMon'>
<form method="post" action="addmon.php">
      DexID: <input type="number" name="PokedexId" id="pokedexnum">
      Level: <input type="number" name="Level" id="pokedexnum">
<input type="submit" value="Add a pokemon!" name='addMonButt'>
    </form>


</div>


<?php
 

    $trainerSql = "SELECT UserId,Login,Status,Created from users where UserId=" . $_SESSION["UserId"];
    $result = $conn->query($trainerSql);

    if ($result) {
      // output data of each row
     $row = $result->fetch_assoc();
        echo "Current Trainer: " . $row["Login"]. "</br>"; 
        echo "Start date: " . $row["Created"]. "</br>";
        $userId= $row['UserId'];
    } else {
      echo "Invalid user";
    }
    

    echo "Your pokemon: </br> <ol>";
    $pokemonSql = "SELECT p.PokedexId, pk.Name,t.Name  as `Type1`,t2.Name as `Type2` 
    ,p.Level, p.PokemonId, p.HPLeft, p.HP
    FROM pokemon p JOIN users u ON u.UserId=p.UserId 
    JOIN pokedex pk on pk.PokedexId=p.PokedexId 
    JOIN types t ON t.TypeId=pk.Type1 LEFT JOIN types t2 
    ON t2.TypeId=pk.Type2 where p.UserId=".$userId." and p.inParty=true";
    $result = $conn->query($pokemonSql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
          echo "<li> #" . $row["PokedexId"]." ". $row["Name"]. ", Type: " 
          . $row["Type1"];
          if($row["Type2"]) {
            echo "/" . $row["Type2"];
          }
          echo ", Level: ".$row["Level"].", HP: ".$row["HPLeft"]."/".$row["HP"].
          "</br></li>";

          // output the moves
          echo "Moveset: </br> <ul>";
          $movesetsql = "SELECT m.Name, t.Name as 'Type', ms.PPValue, m.PP, m.Description FROM `movesets` ms JOIN
          moves m on m.MoveId=ms.MoveId JOIN
          pokemon p on p.PokemonId=ms.PokemonId JOIN
          types t on t.TypeId=m.Type where p.PokemonId=".$row["PokemonId"];
          $result2 = $conn->query($movesetsql);
          while($row2 = $result2->fetch_assoc()) {
            echo "<li>".$row2["Name"]." - ".$row2["Type"].", PP:".
            $row2["PPValue"]."/".$row2["PP"].", Info: ";
          }
          echo "</ul>";
        }
      } else {
        echo "You have no Pokemon in the Party!";
      }
?>

<?php
      echo "</ol>Pokemon in Box: </br> <ol>";
      $pokemonSql = "SELECT p.PokedexId, pk.Name,t.Name  as `Type1`,t2.Name as `Type2`
      ,p.Level, p.HPLeft, p.HP
      FROM pokemon p JOIN users u ON u.UserId=p.UserId 
      JOIN pokedex pk on pk.PokedexId=p.PokedexId 
      JOIN types t ON t.TypeId=pk.Type1 LEFT JOIN types t2 
      ON t2.TypeId=pk.Type2 where p.UserId=".$userId." and p.inParty=false";
      $result = $conn->query($pokemonSql);
  
      if ($result->num_rows > 0) {
          // output data of each row
          while($row = $result->fetch_assoc()) {
            echo "<li> #" . $row["PokedexId"]." ". $row["Name"]. ", Type: " 
            . $row["Type1"];
            if($row["Type2"]) {
              echo "/" . $row["Type2"];
            }
            echo ", Level: ".$row["Level"].", HP: ".$row["HPLeft"]."/".$row["HP"]." </br></li>";
          }
        } else {
          echo "You have no Pokemon in the Box!";
        }

      }
//   header('location:index.pxp')
$conn->close();
?>


</body>
</html>