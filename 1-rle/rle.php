<?php
if ($argc > 3) {
    return ("$$$");
}
switch ($argv[1]) {
case "encode":
    echo encode_rle($argv[2]);
    echo "\n";
    break;
case "decode":
    echo decode_rle($argv[2]);
    echo "\n";
    break;
default:
    return ("$$$");
}

function encode_rle(string $str) {
    if ($str == ''){
            return ('');
    }
    if (ctype_alpha($str)) {
		$i = 0;
		$z = strlen($str) - 1;
		$c = 0;
		$a = 0;
		$rle;
        if ($z + 1 == 1){
            $rle[$a] = 1;
            $rle[$a+1] = $str[$i];
            return (implode($rle));
        }
		while ($i < $z) {
			while ($str[$i] == $str[$i + 1] && $i < $z - 1) {
				$i++;
				$c++;
			}
			$c++;
			if ($i == $z - 1 && $str[$i + 1] == $str[$i]) {
				$i++;
				$c++;
			}
			$rle[$a] = $c;
			$a++;
			$rle[$a] = $str[$i];
			$a++;
			if ($i == $z - 1 && $str[$i + 1] != $str[$i]) {
				$i++;
				$c = 1;
				$rle[$a] = $c;
				$a++;
				$rle[$a] = $str[$i];
			}
			$c = 0;
			$i++;
		}
		return (implode($rle));
	} else {
		return ("$$$");
	}
}

function decode_rle(string $str) {
    if ($str == ''){
        return ('');
    }
    
    $i = 0;
    $z = strlen($str) - 1;
    $a = 0;
    $c = 0;
    $y = 0;
    $rle;
    $nbr;
    
    while ($i < $z) {
        $a = $str[$i];
        if (ctype_digit($a) && ctype_alpha($str[$i+1])) {
                $i++;
            while ($c < $a) {
                $rle[$y] = $str[$i];
                $c++;
                $y++;
            }
            $c = 0;
            $i++;
        }elseif (ctype_digit($a) && ctype_digit($str[$i+1])){
            while (ctype_digit($str[$i])) {
                    $nbr[$c] = $str[$i];
                    $i++;
                    $c++;
            }
            $c = 0;
            while ($c < implode($nbr)) {
                $rle[$y] = $str[$i];
                $c++;
                $y++;
            }
            $nbr = NULL;
            $c = 0;
            $i++;
        }else {
            return ("$$$");
        }
        if ($i == $z - 2 && ctype_digit($str[$i + 1]) == false){
            return("$$$");
        }
    }
    return (implode($rle));
}
?>
