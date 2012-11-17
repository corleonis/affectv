<?php 

global $linesCount, $rawData;

function main() {
    global $linesCount, $rawData;
    
    $data = getInput();
    
    // Now we have the data let's solve some problems :)
    separator('Problem One');
    problemOne($data);
    separator('Problem Two');
    $bands = problemTwo($data);
    separator('Problem Three');
    problemThree($data, $bands);
    separator('Problem Four');
    problemFour($data);
}

function problemFour($data) {
    $colleagues = array();
    foreach($data as $me => $myBands) {
        foreach($data as $friend => $theirBands) {
            if($me == $friend) continue;
            if(($cnt = count(array_intersect($myBands, $theirBands))) > 1) {
                $colleagues[$me][$cnt][] = $friend;
            } else if(empty($colleagues[$me])) {
                $colleagues[$me][] = array();
            }
        }
        
    }
    
    foreach($colleagues as $colleague => $friends) {
        echo $colleague . ': ' . implode(', ', end($friends)) . "\n";
    }
}

function problemThree($data, $bands) {
    $songLength = 5;
    // In minutes
    $dayLength = 8 * 60;
    // Max 96 songs
    $maxSongsPerDay = (int)($dayLength / $songLength);
    
    // Custom sort of our array to get 
    // most unique matches first
    $mSort = function($a, &$b) {
        global $colleagues;
        
        $diff1 = array_diff($a, $b);
        $diff2 = array_diff($b, $a);
        $intersect = array_intersect($a, $b);
        $diff = count($diff1) > count($diff2);
        
        // Our arrays are equal move them down
        if ($a == $b) {
            return 1;
        }
        
        return ($diff) ? -1 : 1;
    };
    uasort($bands, $mSort);
    
    $cnt = 1;
    $includedColleagues = array();
    $playedBands = array();
    foreach($bands as $band => $people) {
        // We can't play more than 96 song 
        // for 1 working day
        ++$cnt;
        if($cnt > $maxSongsPerDay) break;
        foreach($people as $person) {
            if(!in_array($person, $includedColleagues)) {
                $includedColleagues[] = $person;
                if(!in_array($band, $playedBands)) {
                    $playedBands[] = $band;
                }
            }
        }
    }
    
    echo implode("\n", $playedBands);
}

function problemTwo($data) {
    $allBands = array();
    foreach($data as $colleague => $bands) {
        foreach($bands as $band) {
            $allBands[$band][] = $colleague;
        }
    }

    foreach($allBands as $band => $colleagues) {
        echo $band . ': ' . implode(', ', $colleagues) . "\n";
    }
    
    return $allBands;
}

function problemOne($data) {
    $mostLiked = array();
    foreach($data as $bands) {
        foreach($bands as $band) {
            if(array_key_exists($band, $mostLiked)) {
                $mostLiked[$band] += 1;
            } else {
                $mostLiked[$band] = 1;
            }
        }
    }
    asort($mostLiked);
    $minLiked = array_shift(array_slice($mostLiked, -2, 1));
    $minLikedKey = array_search($minLiked, $mostLiked);
    $bands = array_keys($mostLiked); 
    $result = array_slice($bands, array_search($minLikedKey, $bands));
    
    $printArray = function($el) {echo $el . "\n";};
    array_walk($result, $printArray);
}

function getInput() {
    global $linesCount, $rawData;
    
    $input = '';
    $in = fopen('php://stdin', 'r');
    while(!feof($in)){
        $input = $input . fgets($in, 4096);
    }
    
    $rawData = $input;
    
    $input = explode("\n", $input);
    $linesCount = array_shift($input);

    $data = array();
    foreach($input as $row) {
        $matches = array();
        preg_match('/([^:]+):\s(.*)/i', $row, $matches);
        if(!empty($matches[1])) {
            $name = $matches[1];
        } else {
            continue;
        }
        if(!empty($matches[2])) {
            $bands = array_map('trim',explode(',', $matches[2]));
        } else {
            continue;
        }
        
        // I assume that each colleague has unique name
        // if that is not the case this won't work
        $data[$name] = $bands;
    }
    
    return $data;
}

function separator($label) {
    echo "\n\n";
    echo '********************************' . "\n";
    echo '**         ' . $label . '        **' . "\n";
    echo '********************************' . "\n";
    echo "\n";
}

function dump($var, $exit = true) {
    if(empty($var) || is_bool($var)) {
        var_dump($var);
    } else {
        print_r($var);
    }
    
    if($exit) {
        exit;
    }
}

// Start our app
main();