<?php
    require_once "db-connection.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css-statistics.css"> <!-- CSS dosyasını bağlama -->
    <title>Logs</title>
</head>

<body> 
    <div class="centered-container">
        <h1>LOGS</h1>
        
        <div class="rectangle">   
            <table >
            <?php
            for ($i = 0; $i < 1; $i++) {
                echo "<tr>";
                for ($j = 0; $j < 4; $j++) {
                    if      ($j==0) echo "<td>","kiosk","</td>";
                    else if ($j==1) echo "<td>","mağaza","</td>";
                    else if ($j==2) echo "<td>","rota","</td>";
                    else if ($j==3) echo "<td>","tarih","</td>";
                }
                echo "</tr>";
            }
            $sql = "SELECT l1.name as fromWhere, l2.name as toWhere, ph.shortest_path, ph.date 
                    FROM path_history as ph, locations as l1, locations as l2 
                    WHERE l1.id = ph.start_id and l2.id = ph.target_id 
                    ORDER BY ph.path_id DESC;";
            $result = $conn->query($sql);
            $cols = 4;
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $fromWhere = $row["fromWhere"];
                    $toWhere = ($row["toWhere"]);
                    $shortestPath = $row["shortest_path"];
                    $date = ($row["date"]);

                    echo "<tr>";
                    for ($j = 0; $j < $cols; $j++) {
                        if      ($j==0) echo "<td>",$fromWhere,"</td>";
                        else if ($j==1) echo "<td>",$toWhere,"</td>";
                        else if ($j==2) echo "<td>",$shortestPath,"</td>";
                        else if ($j==3) echo "<td>",$date,"</td>";
                    }
                    echo "</tr>";
                }
            }
              




              /*$sql = "SELECT *,COUNT(*) AS total FROM path_history ";
                $result = $conn->query($sql);
                $i=0;
                $cols = 4;
                $m=0;
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $ph_pathId = ($row["path_id"]);
                        $ph_startId = $row["start_id"];
                        $ph_targetId = ($row["target_id"]);
                        $ph_shortestPath = $row["shortest_path"];
                        $ph_date = ($row["date"]);
                        $m++;
                        
                        echo "<tr>";
                            for ($j = 0; $j < $cols; $j++) {
                                if      ($i==0 && $j==0) echo "<td>","kiosk","</td>";
                                else if ($i!=0 && $j==0) echo "<td>",$ph_startId,"</td>";
                                else if ($i==0 && $j==1) echo "<td>","mağaza","</td>";
                                else if ($i!=0 && $j==1) echo "<td>",$ph_targetId,"</td>";
                                else if ($i==0 && $j==2) echo "<td>","rota","</td>";
                                else if ($i!=0 && $j==2) echo "<td>",$ph_shortestPath,"</td>";
                                else if ($i==0 && $j==3) {echo "<td>","date","</td>";$i++;}
                                else if ($i!=0 && $j==3) {echo "<td>",$ph_date,"</td>";}
                            echo "</tr>";
                        }
                    }
                    echo $m;      
                } else { // Eğer herhangi bir satır bulunamazsa, hata mesajı verir
                    echo "couldn't find any log";
                }*/
            ?>
            </table>
        </div>

    </div>
</body>
</html>