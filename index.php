<?php
$letterValues = array(
  "a" => 1, "b" => 2 , "c" => 3, "d" => 4,
  "e" => 5, "f" => 6 , "g" => 7, "h" => 8,
  "i" => 9, "j" => 10, "k" => 11             
);
$valueLetters = array(
  1 => "A", 2 => "B", 3 => "C", 4 => "D",
  5 => "E", 6 => "F", 7 => "G", 8 => "H",
  9 => "I",10 => "J",11 => "K"             
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>FIND THE ROUTE</title>
    <link rel="stylesheet" href="stil.css"> <!-- adding stil.css file -->
</head>
<body>
    <h1>FIND THE ROUTE</h1>
    
    <div class="container">
      <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <div class="form-group">
          <label for="currentLocation">Current Location</label>
          <select name="currentLocation" class="form-control">
            <?php
              require_once "db-connection.php";
              $sql = "SELECT name, id FROM locations WHERE kind = 'kiosk'";
              $result = $conn->query($sql);
              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $kioskName = ($row["name"]);
                    $kioskId = $row["id"];
                    echo "<option value='$kioskId'>$kioskName</option>";
                }
              } else { // Eğer name'i $ olan satır bulunamazsa, hata mesajı verir
                echo "couldn't find the kiosk";
              }
            ?>
          </select>
        
        </div>
        <div class="form-group">
          <label for="destination">Destination</label>
          <select name="destination" class="form-control">
          <?php
              require_once "db-connection.php";
              $sql = "SELECT name, id FROM locations WHERE kind = 'store'";
              $result = $conn->query($sql);
              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $storeName = ($row["name"]);
                    $storeId = $row["id"];
                    echo "<option value='$storeName'>$storeName</option>";
                }
              } else { // Eğer name'i $ olan satır bulunamazsa, hata mesajı verir
                echo "couldn't find the kiosk";
              }
            ?>
          </select>
        </div>
        <button type="submit" name="calculate" class="btn btn-default" >CALCULATE</button>
      </form>
    </div>

    <div class="container">
           <table class="table table-striped">
              <?php
                $finalDestination = "";
                if(isset($_POST["calculate"]))// Checking if there is a form object named 'calculate'.
                {
                    $currentLocation = $_POST["currentLocation"];
                    $destinationName = $_POST["destination"];
                    $midLocation = $currentLocation;
                    
                    // The part for retrieving the position of the target from the database.
                    $sql = "SELECT location, floor, id FROM locations WHERE name = '$destinationName'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {
                          $finalDestinationName = $destinationName;
                          $finalDestination = $row["location"];
                          $finalDestinationId = $row["id"];
                          $floor = $row["floor"];
                      }
                    } else { // Eğer name'i $destinationName olan satır bulunamazsa, hata mesajı verir
                      echo "couldn't find the destination";
                    }
                    // The part for retrieving the position of the start from the database.
                    $sqlFloor = "SELECT location, floor, id, name FROM locations WHERE id = '$currentLocation'";
                    $resultFloor = $conn->query($sqlFloor);
                    if ($resultFloor->num_rows > 0) {
                      while ($row = $resultFloor->fetch_assoc()) { 
                        $currentLocationName =  $row["name"];
                        $currentLocation = $row["location"];
                        $currentLocationId = $row["id"];
                        $currentFloor = $row["floor"];
                      }
                    } else { // Eğer location'ı $currentLocation olan satır bulunamazsa, hata mesajı verir
                      echo "couldn't find the floor";
                    }

                    // The part for retrieving the positions of obstacles from the database.
                    $sqlBlock = "SELECT location,floor FROM locations WHERE kind = 'block'";
                    $resultBlock = $conn->query($sqlBlock);
                    $blocks1 = array(); 
                    $blocks2 = array(); 
                    if ($resultBlock->num_rows > 0) {
                      while ($row = $resultBlock->fetch_assoc()) {
                        if($row["floor"]==1){array_push($blocks1, $row["location"]);}
                        if($row["floor"]==2){array_push($blocks2, $row["location"]);}
                      }
                    } else { // Eğer kind'ı 'block' olan satır bulunamazsa, hata mesajı verir
                      echo "couldn't find the floor";
                    }

                    $way2 = array();
                    $way = array();
                    //$way[] = $currentLocation;
                    //$way2[] = $currentLocation;
                    $routeText = "";
                    $shortestPath = strtoupper(substr($currentLocation, 0, 1)).substr($currentLocation, 1);
                    $midLocationName = "";                           

                    // Creating a 2D 11x11 matrix.
                    $rows = 11;
                    $cols = 11;
                    $matris = array();
                    $matris2 = array();
                    for ($i = 0; $i < $rows; $i++) {
                      $row = array();
                      $row2 = array();
                      for ($j = 0; $j < $cols; $j++) {
                          if (in_array((strtolower($valueLetters[($j)+1])).($i+1),$blocks1)) {
                              $row[] = 1;} // Sets disabled points to 1.
                          else $row[] = 0;
                          if (in_array((strtolower($valueLetters[($j)+1])).($i+1),$blocks2)) {
                              $row2[] = 1;}
                          else $row2[] = 0;
                      }
                      $matris[] = $row;
                      $matris2[] = $row2;
                    }


                    include 'aStar.php'; // PHP file containing the shortest path finding algorithm.

               
                    // IF Else Statement to check If the Target Store is on the 1st or 2nd Floor
                    if ($floor != $currentFloor){
                        // Retrieving the Elevator Location from the Database
                        $sqlElevator = "SELECT location FROM locations WHERE floor = '$currentFloor' AND kind = 'elevator'";
                        $result = $conn->query($sqlElevator);
                        $elevator = "";
                        if ($result->num_rows > 0) {
                          while ($row = $result->fetch_assoc()) {
                              $elevator = $row["location"];
                          }
                        }

                        // Retrieving the Stairway Location from the Database
                        $sqlStairway = "SELECT location FROM locations WHERE floor = '$currentFloor' AND kind = 'stairway'";
                        $result = $conn->query($sqlStairway);
                        $stairway = "";
                        if ($result->num_rows > 0) {
                          while ($row = $result->fetch_assoc()) {
                              $stairway = $row["location"];
                          }
                        }

                        // The part that calculates whether the starting point of the elevator or the stairway is closer for accessing the 2nd floor.
                        $startX  = (substr($currentLocation, 1)-1);
                        $startY  = ($letterValues[substr($currentLocation, 0, 1)]-1);
                        
                        $targetX = (substr($elevator, 1)-1);
                        $targetY = ($letterValues[substr($elevator, 0, 1)]-1);
                        if      ($currentFloor==1) $path1 = aStar($matris, $startX, $startY, $targetX, $targetY);
                        else if ($currentFloor==2) $path1 = aStar($matris2, $startX, $startY, $targetX, $targetY);
                        
                        $targetX = (substr($stairway, 1)-1);
                        $targetY = ($letterValues[substr($stairway, 0, 1)]-1);
                        if      ($currentFloor==1) $path2 = aStar($matris, $startX, $startY, $targetX, $targetY);
                        else if ($currentFloor==2) $path2 = aStar($matris2, $startX, $startY, $targetX, $targetY);
                        
                        if (count($path1)<count($path2)) {
                          $midLocation=$elevator; $midLocationName="elevator";}
                        else {
                          $midLocation=$stairway; $midLocationName="stairway";} 
                    
                        // Moving from Current Location to Elevator/Stairs
                        $routeText .= strtoupper(substr($currentLocation, 0, 1)).substr($currentLocation, 1)." (".$currentLocationName.") ";
                        $startX  = (substr($currentLocation, 1)-1);
                        $startY  = ($letterValues[substr($currentLocation, 0, 1)]-1);
                        $targetX = (substr($midLocation, 1)-1);
                        $targetY = ($letterValues[substr($midLocation, 0, 1)]-1);
                        if     ($currentFloor==1) $path = aStar($matris, $startX, $startY, $targetX, $targetY);
                        else if($currentFloor==2) $path = aStar($matris2, $startX, $startY, $targetX, $targetY);
                        if ($path != null) {
                          foreach ($path as $node) {
                            // saving the route
                            $routeText .= "-> ".$valueLetters[($node[1])+1].($node[0]+1)." ";
                            $shortestPath .= ",".$valueLetters[($node[1])+1].($node[0]+1);
                            if($currentFloor==1)array_push($way, strtolower($valueLetters[($node[1])+1]).($node[0]+1));
                            else if($currentFloor==2)array_push($way2, strtolower($valueLetters[($node[1])+1]).($node[0]+1));
                          }
                        } else {
                          echo "Couldn't reach the target!";
                        }
                        $routeText .= "(".$midLocationName.")";

                        // Moving from elevator/stairs to destination
                        $startX  = (substr($midLocation, 1)-1);
                        $startY  = ($letterValues[substr($midLocation, 0, 1)]-1);
                        $targetX = (substr($finalDestination, 1)-1);
                        $targetY = ($letterValues[substr($finalDestination, 0, 1)]-1);
                        if     ($currentFloor==1) $path = aStar($matris2, $startX, $startY, $targetX, $targetY);
                        else if($currentFloor==2) $path = aStar($matris, $startX, $startY, $targetX, $targetY);                        
                        if ($path != null) {
                          foreach ($path as $node) {
                            // saving the route
                            $routeText .= " -> ".$valueLetters[($node[1])+1].($node[0]+1)."";
                            $shortestPath .= ",".$valueLetters[($node[1])+1].($node[0]+1);
                            if($currentFloor==1)array_push($way2, strtolower($valueLetters[($node[1])+1]).($node[0]+1));
                            if($currentFloor==2)array_push($way, strtolower($valueLetters[($node[1])+1]).($node[0]+1));
                          }
                        } else {
                          echo "Couldn't reach the target!";
                        }
                        $routeText .= " (".$finalDestinationName.")";
                    }
                    
                    // IF Statement to be Executed If the Target Store is on the Same Floor
                    else {
                        $routeText .= strtoupper(substr($currentLocation, 0, 1)).substr($currentLocation, 1)." (".$currentLocationName.") ";
                        $startX  = (substr($currentLocation, 1)-1);
                        $startY  = ($letterValues[substr($currentLocation, 0, 1)]-1);
                        $targetX = (substr($finalDestination, 1)-1);
                        $targetY = ($letterValues[substr($finalDestination, 0, 1)]-1);
                        if     ($currentFloor==1) $path = aStar($matris, $startX, $startY, $targetX, $targetY);
                        else if($currentFloor==2) $path = aStar($matris2, $startX, $startY, $targetX, $targetY);  
                        if ($path != null) {
                          foreach ($path as $node) {
                            // saving the route
                            $routeText .= " -> ".$valueLetters[($node[1])+1].($node[0]+1)."";
                            $shortestPath .= ",".$valueLetters[($node[1])+1].($node[0]+1);
                            if($currentFloor==1)array_push($way, strtolower($valueLetters[($node[1])+1]).($node[0]+1));
                            if($currentFloor==2)array_push($way2, strtolower($valueLetters[($node[1])+1]).($node[0]+1));
                          } 
                        } else {
                          echo "Couldn't reach the target!";
                        }
                        $routeText .= " (".$finalDestinationName.")";
                    }
                echo "<br>".$routeText."<br>";  

                }
              ?>
            </table>

            <h2>1st FLOOR</h2>
            <table >
              <?php
                //if(!empty($way)){  
                // The part where objects on the 1st floor are retrieved from SQL and reflected as a table
                  $tempLocation=0;
                  $sqlLocations = "SELECT location,name FROM locations WHERE floor = '1'";
                  $resultLocation = $conn->query($sqlLocations);
                  $locations = array();
                  if ($resultLocation->num_rows > 0) {
                    $i=1;
                    while ($row = $resultLocation->fetch_assoc()) {
                       $locations[$row["location"]] = $row["name"];
                    }
                  }  
                  // Creating 1st floor table
                  $rows = 11;
                  $cols = 11;
                  for ($i = 0; $i <= $rows; $i++) {
                      echo "<tr>";
                      for ($j = 0; $j <= $cols; $j++) {
                          if      ($i==0 && $j!=0) echo "<td>",$valueLetters[($j)],"</td>";
                          else if ($j==0 && $i!=0) echo "<td>",($i),"</td>";
                          else if ($j!=0 && $i!=0 && !empty($way) && in_array((strtolower($valueLetters[($j)])).($i),$way) && ((strtolower($valueLetters[($j)])).($i)==((substr($finalDestination, 0, 1)).(substr($finalDestination, 1))))){echo "<td style='color: red;'>",$finalDestinationName,"</td>";}
                          else if ($j!=0 && $i!=0 && !empty($way) && in_array((strtolower($valueLetters[($j)])).($i),$way)){echo "<td style='color: red;'>","*","</td>";}
                          else if ($j!=0 && $i!=0 && isset($locations[(strtolower($valueLetters[($j)])).($i)])) {
                            if ($locations[(strtolower($valueLetters[($j)])).($i)]=="x") {echo "<td style='color: black;'>",$locations[(strtolower($valueLetters[($j)])).($i)],"</td>";}
                            else {echo "<td style='color: black;'>",$locations[(strtolower($valueLetters[($j)])).($i)],"</td>";}
                          }
                          else echo "<td>","  ","</td>";
                      }
                      echo "</tr>";
                  }
                //}
              ?>
            </table>

            <h2>2nd Floor</h2>
            <table>
              <?php
                //if(!empty($way2)){  
                // The part where objects on the 2nd floor are retrieved from SQL and reflected as a table
                foreach ($locations as $key => $value) { unset($locations[$key]);}
                $sqlLocations = "SELECT location,name FROM locations WHERE floor = '2'";
                $resultLocation = $conn->query($sqlLocations);
                if ($resultLocation->num_rows > 0) {
                  $i=1;
                  while ($row = $resultLocation->fetch_assoc()) {
                    $locations[$row["location"]] = $row["name"];
                  }
                }
                // Creating 2nd floor table 
                for ($i = 0; $i <= $rows; $i++) {
                  echo "<tr>";
                  for ($j = 0; $j <= $cols; $j++) {
                    if      ($i==0 && $j!=0) echo "<td>",$valueLetters[($j)],"</td>";
                    else if ($j==0 && $i!=0) echo "<td>",($i),"</td>";
                    else if ($j!=0 && $i!=0 && !empty($way2) && in_array((strtolower($valueLetters[($j)])).($i),$way2) && ((strtolower($valueLetters[($j)])).($i)==((substr($finalDestination, 0, 1)).(substr($finalDestination, 1))))){echo "<td style='color: red;'>",$finalDestinationName,"</td>";}
                    else if ($j!=0 && $i!=0 && !empty($way2) && in_array((strtolower($valueLetters[($j)])).($i),$way2)){echo "<td style='color: red;'>","*","</td>";}
                    else if ($j!=0 && $i!=0 && isset($locations[(strtolower($valueLetters[($j)])).($i)])) {
                      if ($locations[(strtolower($valueLetters[($j)])).($i)]=="x") {echo "<td style='color: black;'>",$locations[(strtolower($valueLetters[($j)])).($i)],"</td>";}
                      else {echo "<td style='color: black;'>",$locations[(strtolower($valueLetters[($j)])).($i)],"</td>";}}
                    else echo "<td>","  ","</td>";
                  }
                  echo "</tr>";
                }
              //}
              ?>
            </table>

            <?php 
              // Saving Route to the Database
              if(!empty($way) || !empty($way2)) {  
                $sqlShortestpathInsert = "INSERT INTO path_history (start_id, target_id, shortest_path) VALUES ('$currentLocationId', '$finalDestinationId', '$shortestPath')";
                if ($conn->query($sqlShortestpathInsert) === TRUE) {
                    //echo "added succesfully.";
                } else {
                    echo "Hata: " . $sql . "<br>" . $conn->error;
                }
                // Closing connection
                $conn->close();
              }
            ?>

            <h2>Adding New Structure</h2>
            <div class="container">
              <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
               <label for="kind">Structure Name</label>
                  <select name="kind" class="form-control">
                    <option value="kiosk">KİOSK</option>
                    <option value="store">STORE</option>
                    <option value="stairway">MERDİVEN</option>
                    <option value="elevator">ELEVATOR</option>
                    <option value="block">BLOCK</option>
                  </select>
                <label for="metin">Name</label>
                <input type="text" id="name" name="name" required>
                <label for="metin">Location</label>
                <input type="text" id="location" name="location" required>    
                Floor: <input type="number" id="floor" name="floor">    
                <button type="submit" name="add" class="btn btn-default" >ADD</button>
              </form>
            </div>

            <?php
            if(isset($_POST["add"]))// Checking if there is a form object named 'calculate'.
            {
              $additionName = $_POST["name"];
              $additionKind = $_POST["kind"];
              $additionFloor = $_POST["floor"];
              $additionLocation = $_POST["location"];
              
              require_once "db-connection.php";
              $sqlAddition = "INSERT INTO `locations` (`name`, `kind`, `location`, `floor`, `id`) VALUES ('$additionName', '$additionKind', '$additionLocation', '$additionFloor', NULL)";
              if ($conn->query($sqlAddition) === TRUE) {
                echo "The new row has been successfully added.";
              } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
              }

            }
            ?>

    </div>

    <!-- İki resim gösterilecek bölüm -->
    <img src="images/1stFloor.png" alt="1st Floor">
    <img src="images/2ndFloor.png" alt="2nd Floor">

    <div class="centered-link">
       <a href="statistics.php">Statistics</a>

       <a href="past-logs.php">Logs</a>
    </div>

</body>
</html>
