<?php
    session_start();

    if(isset($_POST['addMonButt'])){
        require('connect.php');
        $PokedexId=$_POST['PokedexId'];
        $Level=$_POST['Level'];

        $query3 = "select count(PokemonId) from pokemon where inParty=true and UserId=".$_SESSION["UserId"]." group by UserId ";
        $result3= mysqli_query($conn, $query3);
        $row3 = mysqli_fetch_row($result3);
        
        if ($row3[0]==NULL)
        {
          $inParty=1;
        }

        if($row3[0]>=6){
            $inParty=0;
        }
        else{
          $inParty=1;
        }

        if ($row3[0]==NULL)
        {
          $inParty=1;
        }

        $query = "insert into pokemon (PokedexId,Level,InParty,UserId,ItemHeld) 
        Values( $PokedexId, $Level, $inParty,". $_SESSION['UserId'] .",NULL)";
            $result = mysqli_query($conn, $query);

            $_SESSION['info']="Dodano wpis!";
            echo $_SESSION['info'];

        $query2 = "select PokemonId, pk.ExpType from pokemon p JOIN pokedex pk 
        ON pk.PokedexId=p.PokedexId order by PokemonId desc limit 1";
        $result2= mysqli_query($conn, $query2);
        $row = mysqli_fetch_assoc($result2);


        $query1 = "CALL fillMonData(".$row['PokemonId'].",$Level)"; 
                $result = mysqli_query($conn, $query1);
                //fill exp
  echo $row['ExpType'];
  $tnl = $row['ExpType'] . "TNL";
        $query4 = "select `".$row['ExpType']."` as 'Total',`$tnl` as 'Left' from exptype where Level=$Level";
  echo $query4;
                $result = mysqli_query($conn, $query4);
                $totalexp = mysqli_fetch_assoc($result);

        $query5 = "UPDATE pokemon SET Exp=".$totalexp['Total'].", ExpTNL=".$totalexp['Left']." where PokemonId=".$row['PokemonId'];
                $result = mysqli_query($conn, $query5);

                $_SESSION['info1']="Wypełniono dane";

        echo $_SESSION['info1'];

  header("Location:index.php");
    }
?>