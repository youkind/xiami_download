<?

$page = intval($argv[1]);

$init = "http://www.xiami.com/space/lib-song/u/518003/page/{$page}";

mkdir($page);

$f = file_get_contents($init);

$reg = "/play\(\'(\d+)\'\)/";
$c = preg_match_all($reg, $f, $out);

$xml_url_t = "http://www.xiami.com/song/playlist/id/%d/object_name/default/object_id/0";

if(!empty($out)){
    foreach($out[1] as $s_id){
        $xml_url = sprintf($xml_url_t, $s_id);
        
        $xml = file_get_contents($xml_url);
        
        if(empty($xml)){
            echo $xml_url." link useless\n";
            continue;
        }
        echo $xml_url." link start\n";
        $xml_obj = new SimpleXMLElement($xml);
        
        //var_dump($xml_obj);
        $title = $xml_obj->trackList->track->title;
        $location = $xml_obj->trackList->track->location;
        echo $location." \n";
        $mp3_link = DecodeLocation($location);
        echo $mp3_link." \n";
        if(!empty($mp3_link)){
            $title = iconv("utf-8", "gbk", $title);
            exec("curl --header \"User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:15.0) Gecko/20100101 Firefox/15.0.1\" -o '{$page}/{$title}.mp3' '{$mp3_link}'");
        }
    }
}

//$str = '9hFaF2FF8_mt%m4F3%93pt2i%635443pF.212E%4%fnF5815133e187_E7A.t834124%x%3%%7682i2%2264.';
//$link = DecodeLocation($str);
//echo $link."\n";

function DecodeLocation($str){
    $head = $str[0];
    
    $str = substr($str, 1);
    $rows = $head;
    $cols = floor(strlen($str)/$rows)+1;
    
    //$cols = 10;
    //$rows = strlen($str) / $cols + 1;
    
    $out = "";
    $ful_row = strlen($str) % $head;
    for($c = 0; $c < $cols; $c++){
        for($r = 0; $r < $rows; $r ++){
            if($c == $cols-1 && $r>=$ful_row){
                continue;
            }
            
            if($r<$ful_row)
                $char = $str[$r*$cols+$c];
            else
                $char = $str[$cols*$ful_row+($r-$ful_row)*($cols-1)+$c];
            $out.=$char;
        }
    }
    $out = urldecode($out);
    $out = str_replace("^", "0", $out);
    if(substr($out, 0, 7) != "http://"){
        break;
    }
    return $out;
}