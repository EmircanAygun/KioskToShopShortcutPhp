<?php

  function h($x, $y, $targetX, $targetY) {
    // Calculating Manhattan Distance
    return abs($targetX - $x) + abs($targetY - $y);
  }
  
  function aStar($matris, $startX, $startY, $targetX, $targetY) {
    $openList = array();
    $closedList = array();
  
    $startNode = array('x' => $startX, 'y' => $startY, 'g' => 0, 'h' => 0, 'f' => 0, 'parent' => null);
    array_push($openList, $startNode);
  
    while (!empty($openList)) {
        $currentNode = array_shift($openList);
  
        if ($currentNode['x'] == $targetX && $currentNode['y'] == $targetY) {
            $path = array();
            while ($currentNode['parent'] != null) {
                array_unshift($path, array($currentNode['x'], $currentNode['y']));
                $currentNode = $currentNode['parent'];
            }
            return $path;
        }
  
        array_push($closedList, $currentNode);
  
        $directions = array(
            array(0, 1), // go right
            array(1, 0), // go down
            array(0, -1), // go left
            array(-1, 0) // go up
        );
  
        foreach ($directions as $dir) {
            $neighborX = $currentNode['x'] + $dir[0];
            $neighborY = $currentNode['y'] + $dir[1];
  
            if ($neighborX >= 0 && $neighborX < 11 && $neighborY >= 0 && $neighborY < 11 &&
                $matris[$neighborX][$neighborY] != 1 && !inClosedList($closedList, $neighborX, $neighborY)) {
                $gScore = $currentNode['g'] + 1;
                $hScore = h($neighborX, $neighborY, $targetX, $targetY);
                $fScore = $gScore + $hScore;
  
                $neighborNode = array('x' => $neighborX, 'y' => $neighborY, 'g' => $gScore, 'h' => $hScore, 'f' => $fScore, 'parent' => $currentNode);
  
                if (!inOpenList($openList, $neighborX, $neighborY)) {
                    array_push($openList, $neighborNode);
                } else {
                    $existingNode = getNodeFromOpenList($openList, $neighborX, $neighborY);
                    if ($gScore < $existingNode['g']) {
                        $existingNode['g'] = $gScore;
                        $existingNode['f'] = $fScore;
                        $existingNode['parent'] = $currentNode;
                    }
                }
            }
        }
  
        usort($openList, function($a, $b) {
            return $a['f'] <=> $b['f'];
        });
    }
  
    return null; // couldn't reach the target
  }
  
  function inClosedList($closedList, $x, $y) {
    foreach ($closedList as $node) {
        if ($node['x'] == $x && $node['y'] == $y) {
            return true;
        }
    }
    return false;
  }
  
  function inOpenList($openList, $x, $y) {
    foreach ($openList as $node) {
        if ($node['x'] == $x && $node['y'] == $y) {
            return true;
        }
    }
    return false;
  }
  
  function getNodeFromOpenList($openList, $x, $y) {
    foreach ($openList as $node) {
        if ($node['x'] == $x && $node['y'] == $y) {
            return $node;
        }
    }
    return null;
  }

?>