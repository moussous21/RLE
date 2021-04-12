<?php
switch ($argv[1]) {
case "encode":
    if (encode_advanced_rle($argv[2], $argv[3]) == 0) {
        echo "OK";
    }else{
        echo "KO";
    }
    echo "\n";
    break;
case "decode":
    if (decode_advanced_rle($argv[2], $argv[3]) == 0) {
        echo "OK";
    }else{
        echo "KO";
    }
    echo "\n";
    break;
default:
    return ("$$$");
}

function readbmp(string $input_path) {
    $op = fopen($input_path, 'r+b');
    $size = filesize($input_path);
    $red = bin2hex(fread($op, $size));
    $hex = wordwrap($red, 2, ' ', true);
    return $hex;
}

function encode_advanced_rle(string $input_path, string $output_path) {
    if (filesize($input_path) == 0){
        $w = fopen($output_path, 'a+b');
        fclose($w);
        return 0;
    }
    $str = readbmp($input_path);
    if (file_exists($output_path) == true){
        unlink($output_path);
    }
    $i = 0;
    $hex;
    $c = 0;
    $rle = "";
    $duo = $str[$i];
    $duo .= $str[$i+1];
    $dif = "";
    $num = "";
    $token = 0;
    if(strlen($str) > 2){
        $tres = $str[$i+3];
        $tres .= $str[$i+4];
    }
    if(strlen($str) == 2){
        $rle .= chr(0);
        $rle .= chr(1);
        $rle .= hex2bin($duo);
        $w = fopen($output_path, 'a+b');
        fwrite($w, $rle);
        return(0);
    }
    while ($i < strlen($str)) {
        if (strlen($str) == 2){
            $rle .= $duo;
            $i = $i + 3;
            $w = fopen($output_path, 'a+b');
            fwrite($w, hex2bin($rle));
            return 0;
        }
        if ($duo != $tres) {
            while($duo != $tres && $i < strlen($str)) {
                $dif .= $duo;
                $c++;
                $i = $i + 3;
                if ($i+4 < strlen($str)){
                    $tres = $str[$i+3];
                    $tres .= $str[$i+4];
                    $duo = $str[$i];
                    $duo .= $str[$i+1];
                }else{
                    $dif .= $tres;
                    $i = $i + 3;
                    $c++;
                }
            }
            if($duo != $tres || $c != 1){
                if ($c > 10){
                    $num = 0;
                    $rle .= hexdec($num);
                    if($token == 1){
                        if($c >= 11 && $c <= 16){
                            $rle .= chr($c-1);
                        }else{
                            $rle .= chr($c-1);
                        }
                        $rle .= substr(hex2bin($dif), 2);
                    }else{
                        $rle .= chr($c);
                        $rle .= hex2bin($dif);
                    }
                }else{
                    $num = 0;
                    $rle .= chr($num);
                    if($token == 1){
                        $num = "0";
                        $num .= $c-1;
                        $rle .= chr($num);
                        $rle .= substr(hex2bin($dif), 1);
                    }else{
                        $num = "0";
                        $num .= $c;
                        $rle .= chr($num);
                        $rle .= hex2bin($dif);
                    }
                }
            }
            $c = 0;
            $dif = "";
        }else if ($duo == $tres) {
            while($duo == $tres && $i < strlen($str)) {
                $c++;
                $i = $i + 3;
                if ($i+4 < strlen($str)){
                    $tres = $str[$i+3];
                    $tres .= $str[$i+4];
                    $duo = $str[$i];
                    $duo .= $str[$i+1];
                }else{
                    $c + 2;
                    $i = $i + 2;
                }
            }
            $c = $c + 1;
            if ($c > 9){
                if($c >= 10 && $c <= 15){
                    $rle .= chr($c);
                }else if($c > 255){
                    $rle .= chr(255);
                    $rle .= hex2bin($duo);
                    $rle .= chr($c+1);
                }else{
                    $rle .= chr($c);
                }
                $rle .= hex2bin($duo);
            }else{
                $num = "0";
                $num .= $c;
                $rle .= chr($num);
                $rle .= hex2bin($duo);
            }
            $token = 1;
            $c = 0;
        }else{
            return 1;
        }
    }
    $w = fopen($output_path, 'a+b');
    fwrite($w, $rle);
    return 0;
}

function decode_advanced_rle(string $input_path, string $output_path) {
    if (filesize($input_path) == 0){
        $w = fopen($output_path, 'a+b');
        fclose($w);
        return 0;
    }
    $str = readbmp($input_path);
    if (file_exists($output_path) == true){
        unlink($output_path);
    }
    if (strlen($str) == 2){
        return 1;
    }
    $i = 0;
    $c = 0;
    $rle = "";
    $sub = "";
    $duo = $str[$i];
    $duo .= $str[$i+1];
    $tres = $str[$i+3];
    $tres .= $str[$i+4];
    while ($i < strlen($str)) {
        if($duo == "00"){
            $i = $i + 3;
            $duo = $str[$i];
            $duo .= $str[$i+1];
            $duo = hexdec($duo);
            if(strlen($str)> $i+4){
                $tres = $str[$i+3];
                $tres .= $str[$i+4];
            }
            if ($tres == dechex(255)){
                return 1;
            }
            while($c <= $duo){
                $sub .= $tres;
                $i = $i + 3;
                if($i+4 < strlen($str)) {
                    $tres = $str[$i+3];
                    $tres .= $str[$i+4];
                }
                $c++;
            }
            if ($i+1 < strlen($str)) {
                $duo = $str[$i];
                $duo .= $str[$i+1];
            }
            $rle .= hex2bin(substr($sub, 0, -2));
            $sub = "";
            $c = 0;
        }else{
            if ($duo == dechex(255) && strlen($str)%2 == 0){
                $c = hexdec($duo);
                $i = $i + 3;
                $duo = $str[$i];
                $duo .= $str[$i+1];
                $tres = $str[$i+3];
                $tres .= $str[$i+4];
            }
            $duo = hexdec($duo);
            $duo = $c + $duo;
            $c = 0;
            if ($tres == dechex(255)){
                return 1;
            }
            while($c <= $duo){
                $sub .= $tres;
                $c++;
            }
            $rle .= hex2bin(substr($sub, 0, -2));
            $sub = "";
            $i = $i + 6;
            $c = 0;
            if ($i+4 < strlen($str)){
                $duo = $str[$i];
                $duo .= $str[$i+1];
                $tres = $str[$i+3];
                $tres .= $str[$i+4];
            }
        }
    }
    $w = fopen($output_path, 'a+b'); 
    fwrite($w, $rle);
    return 0;
}
?>
