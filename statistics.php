<?php
    require_once "db-connection.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css-statistics.css"> <!-- CSS file adding -->
    <title>Statistics</title>
</head>

<body> 
    <div class="centered-container">
        <h1>Statistics</h1>
        
        <!-- SQL QUERY 1 -->
        <div class="rectangle">
            <form action="statistics.php" method="post">
                <label for="selectedKiosk">From kiosk 'x', which store has received the most directions?</label>
                <select name="selectedKiosk" id="selectedKiosk">
                    <?php
                        $sql = "SELECT name, id FROM locations WHERE kind = 'kiosk'";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                            $kioskName = ($row["name"]);
                            $kioskId = $row["id"];
                            echo "<option value='$kioskId'>$kioskName</option>";
                            }
                        } else { // Eğer kind'ı 'kiosk' olan satır bulunamazsa, hata mesajı verir
                            echo "couldn't find any kiosk";
                        }
                    ?>
                </select>
                <input type="submit" name="sqlQuery1" value=">">
            </form>
            <?php
                if (isset($_POST['sqlQuery1'])) {
                    $kioskId = $_POST['selectedKiosk'];
                    $sql = "SELECT l.name
                            FROM path_history ph
                            INNER JOIN locations l
                            WHERE start_id = $kioskId AND l.id=ph.target_id
                            GROUP BY ph.target_id
                            ORDER BY COUNT(*) DESC
                            LIMIT 1
                            ";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {
                          $target_name = $row["name"];
                          //$shortest_path = $row["shortest_path"];
                          //echo $shortest_path."<br/>";
                      }
                    } else { 
                      echo "couldn't find any store search from ".$kioskId;
                    }
                    echo "->".$target_name;
                } 
            ?>
        </div>
        
        <!-- SQL QUERY 2 -->
        <div class="rectangle">
        <form action="statistics.php" method="post">
                <label for="selectedStore">What is the most frequently used route to get to the store 'x'?</label>
                <select name="selectedStore" id="selectedStore">
                    <?php
                        $sql = "SELECT name, id FROM locations WHERE kind = 'store'";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                            $storeName = ($row["name"]);
                            $storeId = $row["id"];
                            echo "<option value='$storeId'>$storeName</option>";
                            }
                        } else { // Eğer kind'ı 'store' olan satır bulunamazsa, hata mesajı verir
                            echo "couldn't find the store";
                        }
                    ?>
                </select>
                <input type="submit" name="sqlQuery2" value=">">
            </form>
            <?php
                if (isset($_POST['sqlQuery2'])) {
                    $kioskId = $_POST['selectedStore'];
                    $sql = "SELECT ph.shortest_path, l.name
                            FROM path_history ph
                            INNER JOIN locations l ON ph.target_id = l.id
                            WHERE ph.start_id = $kioskId
                                AND ph.target_id = (
                                SELECT target_id
                                FROM path_history
                                WHERE start_id = $kioskId
                                ORDER BY COUNT(*) DESC
                                LIMIT 1
                                )";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {
                          $target_name = $row["name"];
                          $shortest_path = $row["shortest_path"];
                          //echo $shortest_path."<br/>";
                      }
                    } else { 
                      echo "couldn't find any store search from ".$kioskId;
                    }
                    echo "->".$target_name;
                }  
            ?>
        </div>

        <!-- SQL QUERY 3 -->        
        <div class="rectangle">
        <form action="statistics.php" method="post">
                <label for="selectedKiosk">What is the most asked date for directions to the 'x' store?</label>
                <select name="selected3" id="selected3">
                    <?php
                        $sql = "SELECT name, id FROM locations WHERE kind = 'store'";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                            $storeName = ($row["name"]);
                            $storeId = $row["id"];
                            echo "<option value='$storeId'>$storeName</option>";
                            }
                        } else { 
                            echo "couldn't find the store";
                        }
                    ?>
                </select>
                <input type="submit" name="sqlQuery3" value=">">
            </form>
            <?php
                if (isset($_POST['sqlQuery3'])) {
                    $kioskId = $_POST['selected3'];
                    $sql = "SELECT ph.date
                            FROM path_history ph
                            INNER JOIN locations l
                            WHERE target_id = $storeId AND l.id=ph.target_id
                            GROUP BY ph.date
                            ORDER BY COUNT(*) DESC
                            LIMIT 1
                            ";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {
                          $date = $row["date"];
                          //$shortest_path = $row["shortest_path"];
                          //echo $shortest_path."<br/>";
                      }
                    } else { 
                      echo "couldn't find ";
                    }
                    echo "->".$date;
                }
            ?>
        </div>
        
        <!-- SQL QUERY 3 -->        
        <div class="rectangle">
        <form action="statistics.php" method="post">
                <label for="selectedKiosk">---</label>
                <select name="selected4" id="selected4">
                    <?php
                        /*$sql = "SELECT name, id FROM locations WHERE kind = 'store'";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                            $storeName = ($row["name"]);
                            $storeId = $row["id"];
                            echo "<option value='$storeId'>$storeName</option>";
                            }
                        } else { 
                            echo "couldn't find the store";
                        }*/
                    ?>
                </select>
                <input type="submit" name="sqlQuery4" value=">">
            </form>
            <?php
                /*if (isset($_POST['sqlQuery4'])) {
                    $kioskId = $_POST['selected4'];
                    $sql = "SELECT ph.date
                            FROM path_history ph
                            INNER JOIN locations l
                            WHERE target_id = $storeId AND l.id=ph.target_id
                            GROUP BY ph.date
                            ORDER BY COUNT(*) DESC
                            LIMIT 1
                            ";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {
                          $date = $row["date"];
                          //$shortest_path = $row["shortest_path"];
                          //echo $shortest_path."<br/>";
                      }
                    } else { 
                      echo "couldn't find ";
                    }
                    echo "->".$date;
                }*/
            ?>
        </div>

    </div>
</body>
</html>