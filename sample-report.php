<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

use Automattic\WooCommerce\Client;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function find_by_pattern($page, $string){
    $text_vars = preg_match('/meta\sname="token"\sid="token"\svalue="(.*?)"/i', $page, $matches);
    $temp = $matches?$matches[1]:'';
    return $temp;
}
 function wh_log($log_msg) {
    $log_filename = $_SERVER['DOCUMENT_ROOT']."/log";
    if (!file_exists($log_filename))
    {
        // create directory/folder uploads.
        mkdir($log_filename, 0777, true);
    }
    $log_file_data = $log_filename.'/log_' . date('d-M-Y') . '.log';
    file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
}

function get_reports($details){
 
    include 'connection.php';
    include_once 'simple_html_dom.php';
    require_once 'autoload.php';
    
     wh_log("------------------log-------------------------<br>hello database currect");
     wh_log("order_id------------->".$details['id']);

    $excludingPath = "public_html";
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    //$rootPath = str_replace($excludingPath, "", $_SERVER['DOCUMENT_ROOT']);
    $accessPath = $rootPath . "/get-pdf/";

    $payload = $details;
    wh_log( "payload----->" .json_encode($payload)."<br>");
    if('hmrp2' == $details['sku'] || 'hmpr3s'== $details['sku'])
    {
       $getdocsfile='docx'; 
    }
    else{
        $getdocsfile='pdf';
    }

    if ($details['status'] != "processing") exit("Order status is not processing, the order should be cancelled or completed already!");
    $desktop_agents = array(
        'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.99 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.99 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.99 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_1) AppleWebKit/602.2.14 (KHTML, like Gecko) Version/10.0.1 Safari/602.2.14',
        'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.98 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.98 Safari/537.36',
        'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36',
        'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.99 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0',
        'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0'
    );
    $order_id = $details['id'];
    $order_total = $details['total'];
    $item_price = $details['item_price'];

    if ($order_total % $item_price == 0){
        $num_report = $details['qty'];
    }else{
        exit("number of report have calculation error");
    }

    for ($z = 0;$z < $num_report;$z++){
        $clientname = $details['clientname'];
        $Year = $details['year'];
        $Month = $details['month'];
        $Day = $details['day'];
        $Hour = $details['hour'];
        $Minute = $details['minute'];
        $email = $details['email'];
        $City = "Hong Kong";
        $State = "HONG KONG";
        $date_tran = new DateTime($Year . '-' . $Month . '-' . $Day . ' ' . $Hour . ':' . $Minute . ':00');
        if ($details['gmt'] == '(GMT +8:00) 橙色區' || '(GMT +8:00) 香港' || '(GMT +8:00) 台灣'){
            
             wh_log("gmt------->8:00:".$details['gmt']);
        }else if ($details['gmt'] == '(GMT +9:00) 藍色區' || '(GMT +9:00) 日本, 韓國, 雅庫茨克')
        {
            $date_tran->add(new DateInterval('PT1H'));
        }
        else if ($details['gmt'] == '(GMT +7:00) 綠色區' || '(GMT +7:00) Bangkok, Hanoi, Jakarta')
        {
            $date_tran->sub(new DateInterval('PT1H'));
        }
        else if ($details['gmt'] == '(GMT +6:00) 粉色區' || '(GMT +6:00) Almaty, Dhaka, Colombo')
        {
            $date_tran->sub(new DateInterval('PT2H'));
        }
        else if ($details['gmt'] == '(GMT +5:00) 紫色區' || '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent')
        {
            $date_tran->sub(new DateInterval('PT3H'));
        }
        else if ($details['gmt'] == '(GMT -12:00) Eniwetok, Kwajalein')
        {
            $date_tran->sub(new DateInterval('PT20H'));
        }
        else if ($details['gmt'] == '(GMT -11:00) Midway Island, Samoa')
        {
            $date_tran->sub(new DateInterval('PT19H'));
        }
        else if ($details['gmt'] == '(GMT -10:00) Hawaii')
        {
            $date_tran->sub(new DateInterval('PT18H'));
        }
        else if ($details['gmt'] == '(GMT -9:00) Alaska')
        {
            $date_tran->sub(new DateInterval('PT17H'));
        }
        else if ($details['gmt'] == '(GMT -8:00) Pacific Time (US &amp Canada)')
        {
            $date_tran->sub(new DateInterval('PT16H'));
        }
        else if ($details['gmt'] == '(GMT -7:00) Mountain Time (US &amp Canada)')
        {
            $date_tran->sub(new DateInterval('PT15H'));
        }
        else if ($details['gmt'] == '(GMT -6:00) Central Time (US &amp Canada), Mexico City')
        {
            $date_tran->sub(new DateInterval('PT14H'));
        }
        else if ($details['gmt'] == '(GMT -5:00) Eastern Time (US &amp Canada), Bogota, Lima')
        {
            $date_tran->sub(new DateInterval('PT13H'));
        }
        else if ($details['gmt'] == '(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz')
        {
            $date_tran->sub(new DateInterval('PT12H'));
        }
        else if ($details['gmt'] == '(GMT -3:30) Newfoundland')
        {
            $date_tran->sub(new DateInterval('PT11H30M'));
        }
        else if ($details['gmt'] == '(GMT -3:00) Brazil, Buenos Aires, Georgetown')
        {
            $date_tran->sub(new DateInterval('PT11H'));
        }
        else if ($details['gmt'] == '(GMT -2:00) Mid-Atlantic')
        {
            $date_tran->sub(new DateInterval('PT10H'));
        }
        else if ($details['gmt'] == '(GMT -1:00) Azores, Cape Verde Islands')
        {
            $date_tran->sub(new DateInterval('PT9H'));
        }
        else if ($details['gmt'] == '(GMT  0:00) Western Europe Time, London, Lisbon, Casablanca')
        {
            $date_tran->sub(new DateInterval('PT8H'));
        }
        else if ($details['gmt'] == '(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris')
        {
            $date_tran->sub(new DateInterval('PT7H'));
        }
        else if ($details['gmt'] == '(GMT +2:00) Kaliningrad, South Africa')
        {
            $date_tran->sub(new DateInterval('PT6H'));
        }
        else if ($details['gmt'] == '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg')
        {
            $date_tran->sub(new DateInterval('PT5H'));
        }
        else if ($details['gmt'] == '(GMT +3:30) Tehran')
        {
            $date_tran->sub(new DateInterval('PT4H30M'));
        }
        else if ($details['gmt'] == '(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi')
        {
            $date_tran->sub(new DateInterval('PT4H'));
        }
        else if ($details['gmt'] == '(GMT +4:30) Kabul')
        {
            $date_tran->sub(new DateInterval('PT3H30M'));
        }
        else if ($details['gmt'] == '(GMT +5:30) Bombay, Calcutta, Madras, New Delhi')
        {
            $date_tran->sub(new DateInterval('PT2H30M'));
        }
        else if ($details['gmt'] == '(GMT +9:30) Adelaide, Darwin')
        {
            $date_tran->add(new DateInterval('PT1H30M'));
        }
        else if ($details['gmt'] == '(GMT +10:00) Eastern Australia, Guam, Vladivostok')
        {
            $date_tran->add(new DateInterval('PT2H'));
        }
        else if ($details['gmt'] == '(GMT +11:00) Magadan, Solomon Islands, New Caledonia')
        {
            $date_tran->add(new DateInterval('PT3H'));
        }
        else if ($details['gmt'] == '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka')
        {
            $date_tran->add(new DateInterval('PT4H'));
        }

        $Year = $date_tran->format('Y');
        $Month = $date_tran->format('m');
        $Day = $date_tran->format('d');
        $Hour = $date_tran->format('H');
        $Minute = $date_tran->format('i');
        $refer_time = 604630368000000000;
        $client_day = date_create($Year . $Month . $Day);
        $refer_day = date_create("1917-01-01");
        $diff = date_diff($refer_day, $client_day);
        $hour_in_sec = $Hour * 3600;
        $minute_in_sec = $Minute * 60;
        $final_in_sec = (($hour_in_sec + $minute_in_sec + ($diff->days * 24 * 3600)) * 10000000) + $refer_time;
        $url = "https://cdn.jovianarchive.com/RaveChartGenerator.php?newchart=true&Time={$final_in_sec}";
        $ch = curl_init();
        $dom = new simple_html_dom();
        curl_setopt($ch, CURLOPT_USERAGENT, $desktop_agents[rand(0, 10) ]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "X-Requested-With: XMLHttpRequest",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8"
        ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $str = curl_exec($ch);
        $dom->load($str);
        curl_close($ch);

        // CHANGE 6 DESC 2020 - START 
        /*
        $chImg = curl_init();
        $urlTime = "https://cdn.jovianarchive.com/RaveChartGenerator.php?Time={$final_in_sec}";
        curl_setopt($chImg, CURLOPT_USERAGENT, $desktop_agents[rand(0, 10) ]);
        curl_setopt($chImg, CURLOPT_HTTPHEADER, array(
            "Accept: image/webp,-/-",
            "Accept-Language: en-US,en;q=0.5",
            "Accept-Encoding: gzip, deflate, br"
        ));
        curl_setopt($chImg, CURLOPT_URL, $urlTime);
        curl_setopt($chImg, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($chImg, CURLOPT_HEADER, 0);
        $chartImg = curl_exec($chImg);
        $chartImg;
        curl_close($chImg);
        */
        // CHANGE 6 DESC 2020 - FINISH 

        $gate = array();
        $channel = array();
        $center = array();
        $money = array();
        //black
        $SunP = array();
        $EarthP = array();
        $NNodeP = array();
        $SNodeP = array();
        $MoonP = array();
        $MercuryP = array();
        $VenusP = array();
        $MarsP = array();
        $JupiterP = array();
        $SaturnP = array();
        $UranusP = array();
        $NeptuneP = array();
        $PlutoP = array();
        //red
        $SunD = array();
        $EarthD = array();
        $NNodeD = array();
        $SNodeD = array();
        $MoonD = array();
        $MercuryD = array();
        $VenusD = array();
        $MarsD = array();
        $JupiterD = array();
        $SaturnD = array();
        $UranusD = array();
        $NeptuneD = array();
        $PlutoD = array();

        $subject = 'humandesign_report';
        $message = date('Y-m-d H:i:s') . $email;
        foreach ($dom->find('g[id=defGatesCircles] text') as $element) $gate[] = (int)$element->innertext;
        foreach ($gate as $key => $value) if ($key & 1) unset($gate[$key]);
        $gate = array_values(array_filter($gate));
        // Black Node
        foreach ($dom->find('g[id=SunP] text') as $element) $SunP[] = $element->innertext;
        foreach ($dom->find('g[id=EarthP] text') as $element) $EarthP[] = $element->innertext;
        foreach ($dom->find('g[id=NNodeP] text') as $element) $NNodeP[] = $element->innertext;
        foreach ($dom->find('g[id=SNodeP] text') as $element) $SNodeP[] = $element->innertext;
        foreach ($dom->find('g[id=MoonP] text') as $element) $MoonP[] = $element->innertext;
        foreach ($dom->find('g[id=MercuryP] text') as $element) $MercuryP[] = $element->innertext;
        foreach ($dom->find('g[id=VenusP] text') as $element) $VenusP[] = $element->innertext;
        foreach ($dom->find('g[id=MarsP] text') as $element) $MarsP[] = $element->innertext;
        foreach ($dom->find('g[id=JupiterP] text') as $element) $JupiterP[] = $element->innertext;
        foreach ($dom->find('g[id=SaturnP] text') as $element) $SaturnP[] = $element->innertext;
        foreach ($dom->find('g[id=UranusP] text') as $element) $UranusP[] = $element->innertext;
        foreach ($dom->find('g[id=NeptuneP] text') as $element) $NeptuneP[] = $element->innertext;
        foreach ($dom->find('g[id=PlutoP] text') as $element) $PlutoP[] = $element->innertext;
        // Red Node
        foreach ($dom->find('g[id=SunD] text') as $element) $SunD[] = $element->innertext;
        foreach ($dom->find('g[id=EarthD] text') as $element) $EarthD[] = $element->innertext;
        foreach ($dom->find('g[id=NNodeD] text') as $element) $NNodeD[] = $element->innertext;
        foreach ($dom->find('g[id=SNodeD] text') as $element) $SNodeD[] = $element->innertext;
        foreach ($dom->find('g[id=MoonD] text') as $element) $MoonD[] = $element->innertext;
        foreach ($dom->find('g[id=MercuryD] text') as $element) $MercuryD[] = $element->innertext;
        foreach ($dom->find('g[id=VenusD] text') as $element) $VenusD[] = $element->innertext;
        foreach ($dom->find('g[id=MarsD] text') as $element) $MarsD[] = $element->innertext;
        foreach ($dom->find('g[id=JupiterD] text') as $element) $JupiterD[] = $element->innertext;
        foreach ($dom->find('g[id=SaturnD] text') as $element) $SaturnD[] = $element->innertext;
        foreach ($dom->find('g[id=UranusD] text') as $element) $UranusD[] = $element->innertext;
        foreach ($dom->find('g[id=NeptuneD] text') as $element) $NeptuneD[] = $element->innertext;
        foreach ($dom->find('g[id=PlutoD] text') as $element) $PlutoD[] = $element->innertext;

        /* these lines are to be added in report.php by EMIZENTECH start here */

        $allNodes = array(
            'sunp' => $SunP,
            'earthp' => $EarthP,
            'nnodep' => $NNodeP,
            'snodep' => $SNodeP,
            'moonp' => $MoonP,
            'mercuryp' => $MercuryP,
            'venusp' => $VenusP,
            'marsp' => $MarsP,
            'jupiterp' => $JupiterP,
            'saturnp' => $SaturnP,
            'uranusp' => $UranusP,
            'neptunep' => $NeptuneP,
            'plutop' => $PlutoP,
            'sund' => $SunD,
            'earthd' => $EarthD,
            'nnoded' => $NNodeD,
            'snoded' => $SNodeD,
            'moond' => $MoonD,
            'mercuryd' => $MercuryD,
            'venusd' => $VenusD,
            'marsd' => $MarsD,
            'jupiterd' => $JupiterD,
            'saturnd' => $SaturnD,
            'uranusd' => $UranusD,
            'neptuned' => $NeptuneD,
            'plutod' => $PlutoD
        );
        /* return $allNodes;
         exit;*/
        $objetcsBody = array();
       

        foreach ($allNodes as $nodeKey => $nodeValue)
        {
            
            $sql = 'SELECT numbergate_body FROM hd_numbergate where numbergate_name = "' . floatval(strip_tags($nodeValue['0'])) . '"';
            $result = mysqli_query($conn, $sql);
            
            while ($row = mysqli_fetch_assoc($result))
            {
                $objetcsBody[$nodeKey] = $row['numbergate_body'];
                
                
                
            }
        }
      
        /*
        if any error occurs then please comment the uncommented lines which are between the EMIZENTECH
        
        example to use these objects in template.php file
        
        for sunp use    <?php echo $objetcsBody['sunp']; ?>
        for earthp use  <?php echo $objetcsBody['earthp']; ?>
        for moonp use   <?php echo $objetcsBody['moonp']; ?>
        
        note : always use lowercase letter with single quote inside of the Brackets of $objetcsBody
        
        */
        /* these lines are to be added in report.php by EMIZENTECH end here */
        
        // CHANGE 6 DESC 2020 - START 
        $url_main = 'https://www.jovianarchive.com/Get_Your_Chart';
        $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.16; rv:83.0) Gecko/20100101 Firefox/83.0';
        $cookieLocation = $accessPath . "cookies.txt";
        // STEP 1 - Send request to catch cookies
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url_main);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieLocation );
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieLocation );
        curl_setopt($ch, CURLOPT_ENCODING , "");
        curl_setopt($ch, CURLOPT_REFERER, $url_main);
        $response = curl_exec($ch);
        curl_close($ch);

        $pattern = "#<input name=\"__RequestVerificationToken\" type=\"hidden\" value=\"(.*?)\" />#is";
        preg_match_all($pattern, $response, $c);
        $token = trim($c[1][0]);
        wh_log('token--------------->'.$token);

        $fields_query = array(
            'Name' => $clientname,
            'BirthDate' => $date_tran->format('Y-m-d\TH:i:s'),
            'IsTimeUTC' => false,
            'Country' => 'Taiwan',
            'City' => 'Taipei',
        );
        $fields_json = base64_encode(json_encode($fields_query));
        //$url_post = 'https://www.jovianarchive.com/Get_Your_Chart?data='.$fields_json;
        $url_post = 'https://www.jovianarchive.com/Get_Your_Chart';

        // Post fields
        $fields_post = array(
            '__RequestVerificationToken' => $token,
            'IsVariableChart' => 'False',
            'Name' => $clientname,
            'Day' => $Day,
            'Month' => $Month,
            'Year' => $Year,
            'Hour' => $Hour,
            'Minute' => $Minute,
            'Country' => 'Taiwan',
            'City' => 'Taipei',
            'IsTimeUTC' => 'False',
        );
        $fields_string = '';
        foreach($fields_post as $key=>$value) {
            $fields_string .= $key . "=" . $value . "&";
        }
        $fields_string = rtrim($fields_string,'&');
        $fields_string .= '&IsTimeUTC=true&IsTimeUTC=false';

        $headers = array(
            'Host: www.jovianarchive.com',
            'User-Agent: '.$user_agent,
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Accept-Encoding: gzip, deflate, br',
            'Content-Type: application/x-www-form-urlencoded',
            'Content-Length: '.strlen($fields_string),
            'Origin: https://www.jovianarchive.com',
            'Connection: keep-alive',
            'Referer: '.$url_post,
            'Upgrade-Insecure-Requests: 1',
            'Pragma: no-cache',
            'Cache-Control: no-cache'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url_post);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieLocation );
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieLocation );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_ENCODING , "");
        $response = curl_exec($ch);
        curl_close($ch);
        
        $pattern = "#<img alt=\"Chart BodyGrpah\" src=\"(.*?)\" /></span>#is";
        preg_match_all($pattern, $response, $c);
        $image_file_url = trim($c[1][0]);
        if ($image_file_url != '') {
            $image_url = 'https://www.jovianarchive.com/'.$image_file_url;
            // save image file
            $saveto = $accessPath . "graph.png";
            $ch = curl_init ($image_url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       
            curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieLocation );
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieLocation );
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            $raw = curl_exec($ch);
            curl_close ($ch);
            if(file_exists($saveto)){
                unlink($saveto);
            }
            $fp = fopen($saveto,'x');
            fwrite($fp, $raw);
            fclose($fp);
            $image = $accessPath . "graph.png";
        }
        
        // Chart Properties here
        $pattern = "#<div class=\"chart_properties\">(.*?)<a href=\"/Get_Your_Chart#is";
        preg_match_all($pattern, $response, $c);
        $chart_properties = trim($c[1][0]);

        $data = new stdClass();
        $pattern = "#<li><b>(.*?): </b> (.*?)</li>#is";
        preg_match_all($pattern, $chart_properties, $c);
        $chart_property_keys = $c[1];
        $chart_property_vals = $c[2];
        if (count($chart_property_keys) > 0) {
            for ($i = 0; $i < count($chart_property_keys); $i++) {
                $chart_property_key = trim($chart_property_keys[$i]);
                $chart_property_val = trim($chart_property_vals[$i]);
                if ($chart_property_key == 'Type') {
                    $data->HdType = $chart_property_val;
                } else if ($chart_property_key == 'Strategy') {
                    $data->HdStrategy = $chart_property_val;
                } else if ($chart_property_key == 'Not-Self Theme') {
                    $data->HdNotselftheme = $chart_property_val;
                } else if ($chart_property_key == 'Inner Authority') {
                    $data->HdAuthority = $chart_property_val;
                } else if ($chart_property_key == 'Profile') {
                    $data->HdProfile = $chart_property_val;
                } else if ($chart_property_key == 'Definition') {
                    $data->HdDefinition = $chart_property_val;
                } else if ($chart_property_key == 'Incarnation Cross') {
                    $data->HdCross = $chart_property_val;
                }
            }
        }        
        // CHANGE 6 DESC 2020 - FINISH 
       

        //print_r($temp);
        //die;
    
        //print_r($data );
        //die;
        wh_log( "dataarpit----->" .json_encode($data)."<br>");

        $category = 0;
        $authority = 0;
        $cross = 0;
        $profile = 0;
        $definition = 0;
        $c = 0;
        //$category
        if ($data->HdType == "Generator") $category = 1; //生產者
        else if ($data->HdType == "Manifesting Generator") $category = 2; //顯示生產者
        else if ($data->HdType == "Projector") $category = 3; //投射者
        else if ($data->HdType == "Manifestor") $category = 4; //顯示者
        else if ($data->HdType == "Reflector") $category = 5; //反映者
        //else $category = 1;
        wh_log("category----->" .json_encode($category)."<br>");
       $str = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
        }, $data->HdAuthority);
        $str = $data->HdAuthority;
         $str1 = preg_replace_callback('/u([0-9a-fA-F]{4})/', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
        }, $str);
        wh_log("data authority----->" .$str."<br>");
        wh_log("data authority1----->" .$str1."<br>");

        //$authority
        if ($data->HdAuthority == "Sacral") $authority = 1; //薦骨中心
        else if ($data->HdAuthority == "Emotional - Solar Plexus" && ($data->HdType == "Generator" || $data->HdType == "Manifesting Generator")) $authority = 2; // 情緒(薦骨)
        else if ($data->HdAuthority == "Emotional - Solar Plexus" && ($data->HdType == "Projector" || $data->HdType == "Manifestor")) $authority = 3; // 情緒
        else if ($data->HdAuthority == "Splenic") $authority = 4; //直覺
        else if ($data->HdAuthority == "Self Projected") $authority = 5; //自我投射
        else if ($data->HdAuthority == "Ego" && $data->HdType == "Manifestor") $authority = 8; //意志力型權威 (顯示者 )
        else if ($data->HdAuthority == "Ego Projected" && $data->HdType == "Projector") $authority = 6; //意志力型權威 (投射者)
        else if ($data->HdAuthority == "None" && $data->HdType == "反映者") $authority = 9; //月亮週期(Moon Cycle)
        else if ($data->HdAuthority == "None") $authority = 7; //無內在權威
        wh_log("authority----->" .json_encode($authority)."<br>");
        //else $authority = 1;
        wh_log("Cross_curl----->" .$data->HdCross."<br>");
        //cross
        if ($data->HdCross == "Right Angle Cross of The Sphinx  (13/7 | 1/2)") $cross = 1;
        else if ($data->HdCross == "Right Angle Cross of The Sphinx  (2/1 | 13/7)") $cross = 2;
        else if ($data->HdCross == "Right Angle Cross of The Sphinx  (7/13 | 2/1)") $cross = 3;
        else if ($data->HdCross == "Right Angle Cross of The Sphinx  (1/2 | 7/13)") $cross = 4;
        else if ($data->HdCross == "Right Angle Cross of Maya (42/32 | 61/62)") $cross = 5;
        else if ($data->HdCross == "Right Angle Cross of Maya (62/61 | 42/32)") $cross = 6;
        else if ($data->HdCross == "Right Angle Cross of Maya (32/42 | 62/61)") $cross = 7;
        else if ($data->HdCross == "Right Angle Cross of Maya (61/62 | 32/42)") $cross = 8;
        else if ($data->HdCross == "Right Angle Cross of The Unexpected (27/28 | 41/31)") $cross = 9;
        else if ($data->HdCross == "Right Angle Cross of The Unexpected (31/41 | 27/28)") $cross = 10;
        else if ($data->HdCross == "Right Angle Cross of The Unexpected (28/27 | 31/41)") $cross = 11;
        else if ($data->HdCross == "Right Angle Cross of The Unexpected (41/31 | 28/27)") $cross = 12;
        else if ($data->HdCross == "Right Angle Cross of Planning (37/40 | 9/16)") $cross = 13;
        else if ($data->HdCross == "Right Angle Cross of Planning (16/9 | 37/40)") $cross = 14;
        else if ($data->HdCross == "Right Angle Cross of Planning (40/37 | 16/9)") $cross = 15;
        else if ($data->HdCross == "Right Angle Cross of Planning (9/16 | 40/37)") $cross = 16;
        else if ($data->HdCross == "Right Angle Cross of The Four Ways (24/44 | 19/33)") $cross = 17;
        else if ($data->HdCross == "Right Angle Cross of The Four Ways (33/19 | 24/44)") $cross = 18;
        else if ($data->HdCross == "Right Angle Cross of The Four Ways (44/24 | 33/19)") $cross = 19;
        else if ($data->HdCross == "Right Angle Cross of The Four Ways (19/33 | 44/24)") $cross = 20;
        else if ($data->HdCross == "Right Angle Cross of Eden (36/6 | 11/12)") $cross = 21;
        else if ($data->HdCross == "Right Angle Cross of Eden (12/11 | 36/6)") $cross = 22;
        else if ($data->HdCross == "Right Angle Cross of Eden (6/36 | 12/11)") $cross = 23;
        else if ($data->HdCross == "Right Angle Cross of Eden (11/12 | 6/36)") $cross = 24;
        else if ($data->HdCross == "Right Angle Cross of Contagion (30/29 | 14/8)") $cross = 25;
        else if ($data->HdCross == "Right Angle Cross of Contagion (8/14 | 30/29)") $cross = 26;
        else if ($data->HdCross == "Right Angle Cross of Contagion (29/30 | 8/14)") $cross = 27;
        else if ($data->HdCross == "Right Angle Cross of Contagion (14/8 | 29/30)") $cross = 28;
        else if ($data->HdCross == "Right Angle Cross of Tension (21/48 | 38/39)") $cross = 29;
        else if ($data->HdCross == "Right Angle Cross of Tension (39/38 | 21/48)") $cross = 30;
        else if ($data->HdCross == "Right Angle Cross of Tension (48/21 | 39/38)") $cross = 31;
        else if ($data->HdCross == "Right Angle Cross of Tension (38/39 | 48/21)") $cross = 32;
        else if ($data->HdCross == "Right Angle Cross of The Sleeping Phoenix (55/59 | 34/20)") $cross = 33;
        else if ($data->HdCross == "Right Angle Cross of The Sleeping Phoenix (20/34 | 55/59)") $cross = 34;
        else if ($data->HdCross == "Right Angle Cross of The Sleeping Phoenix (59/55 | 20/34)") $cross = 35;
        else if ($data->HdCross == "Right Angle Cross of The Sleeping Phoenix  (34/20 | 59/55)") $cross = 36;
        else if ($data->HdCross == "Right Angle Cross of Service (17/18 | 58/52)") $cross = 37;
        else if ($data->HdCross == "Right Angle Cross of Service (52/58 | 17/18)") $cross = 38;
        else if ($data->HdCross == "Right Angle Cross of Service (18/17 | 52/58)") $cross = 39;
        else if ($data->HdCross == "Right Angle Cross of Service (58/52 | 18/17)") $cross = 40;
        else if ($data->HdCross == "Right Angle Cross of Laws (3/50 | 60/56)") $cross = 41;
        else if ($data->HdCross == "Right Angle Cross of Laws (56/60 | 3/50)") $cross = 42;
        else if ($data->HdCross == "Right Angle Cross of Laws (50/3 | 56/60)") $cross = 43;
        else if ($data->HdCross == "Right Angle Cross of Laws (60/56 | 50/3)") $cross = 44;
        else if ($data->HdCross == "Right Angle Cross of Penetration (51/57 | 54/53)") $cross = 45;
        else if ($data->HdCross == "Right Angle Cross of Penetration (53/54 | 51/57)") $cross = 46;
        else if ($data->HdCross == "Right Angle Cross of Penetration (57/51 | 53/54)") $cross = 47;
        else if ($data->HdCross == "Right Angle Cross of Penetration (54/53 | 57/51)") $cross = 48;
        else if ($data->HdCross == "Right Angle Cross of Rulership (22/47 | 26/45)") $cross = 49;
        else if ($data->HdCross == "Right Angle Cross of Rulership (45/26 | 22/47)") $cross = 50;
        else if ($data->HdCross == "Right Angle Cross of Rulership (47/22 | 45/26)") $cross = 51;
        else if ($data->HdCross == "Right Angle Cross of Rulership (26/45 | 47/22)") $cross = 52;
        else if ($data->HdCross == "Right Angle Cross of The Vessel of Love (25/46 | 10/15)") $cross = 53;
        else if ($data->HdCross == "Right Angle Cross of The Vessel of Love(15/10 | 25/46)") $cross = 54;
        else if ($data->HdCross == "Right Angle Cross of The Vessel of Love (46/25 | 15/10)") $cross = 55;
        else if ($data->HdCross == "Right Angle Cross of The Vessel of Love (10/15 | 46/25)") $cross = 56;
        else if ($data->HdCross == "Right Angle Cross of Consciousness (63/64 | 5/35)") $cross = 57;
        else if ($data->HdCross == "Right Angle Cross of Consciousness (35/5 | 63/64)") $cross = 58;
        else if ($data->HdCross == "Right Angle Cross of Consciousness (64/63 | 35/5)") $cross = 59;
        else if ($data->HdCross == "Right Angle Cross of Consciousness (5/35 | 64/63)") $cross = 60;
        else if ($data->HdCross == "Right Angle Cross of Explanation (49/4 | 43/23)") $cross = 61;
        else if ($data->HdCross == "Right Angle Cross of Explanation (23/43 | 49/4)") $cross = 62;
        else if ($data->HdCross == "Right Angle Cross of Explanation (4/49 | 23/43)") $cross = 63;
        else if ($data->HdCross == "Right Angle Cross of Explanation (43/23 | 4/49)") $cross = 64;
        else if ($data->HdCross == "Left Angle Cross of Duality (20/34 | 37/40)") $cross = 65;
        else if ($data->HdCross == "Left Angle Cross of Duality (34/20 | 40/37)") $cross = 66;
        else if ($data->HdCross == "Left Angle Cross of Individualism (39/38 | 51/57)") $cross = 67;
        else if ($data->HdCross == "Left Angle Cross of Individualism (38/39 | 57/51)") $cross = 68;
        else if ($data->HdCross == "Left Angle Cross of Industry (30/29 | 34/20)") $cross = 69;
        else if ($data->HdCross == "Left Angle Cross of Industry (29/30 | 20/34)") $cross = 70;
        else if ($data->HdCross == "Left Angle Cross of Uncertainty  (8/14 | 55/59)") $cross = 71;
        else if ($data->HdCross == "Left Angle Cross of Uncertainty  (14/8 | 59/55)") $cross = 72;
        else if ($data->HdCross == "Left Angle Cross of Distraction (56/60 | 27/28)") $cross = 73;
        else if ($data->HdCross == "Left Angle Cross of Distraction (60/56 | 28/27)") $cross = 74;
        else if ($data->HdCross == "Left Angle Cross of Seperation (35/5 | 22/47)") $cross = 75;
        else if ($data->HdCross == "Left Angle Cross of Seperation (5/35 | 47/22)") $cross = 76;
        else if ($data->HdCross == "Left Angle Cross of Dominion (63/64 | 26/45)") $cross = 77;
        else if ($data->HdCross == "Left Angle Cross of Dominion (64/63 | 45/26)") $cross = 78;
        else if ($data->HdCross == "Left Angle Cross of The Clarion (51/57 | 61/62)") $cross = 79;
        else if ($data->HdCross == "Left Angle Cross of The Clarion (57/51 | 62/61)") $cross = 80;
        else if ($data->HdCross == "Left Angle Cross of Confrontation (45/26 | 36/6)") $cross = 81;
        else if ($data->HdCross == "Left Angle Cross of Confrontation (26/45 | 6/36)") $cross = 82;
        else if ($data->HdCross == "Left Angle Cross of The Alpha (31/41 | 24/44)") $cross = 83;
        else if ($data->HdCross == "Left Angle Cross of The Aplha (41/31 | 44/24)") $cross = 84;
        else if ($data->HdCross == "Left Angle Cross of Upheaval  (17/18 | 38/39)") $cross = 85;
        else if ($data->HdCross == "Left Angle Cross of Upheaval  (18/17 | 39/38)") $cross = 86;
        else if ($data->HdCross == "Left Angle Cross of The Plane (36/6 | 10/15)") $cross = 87;
        else if ($data->HdCross == "Left Angle Cross of The Plane (6/36 | 15/10)") $cross = 88;
        else if ($data->HdCross == "Left Angle Cross of Endeavor (21/48 | 54/53)") $cross = 89;
        else if ($data->HdCross == "Left Angle Cross of Endeavor (48/21 | 53/54)") $cross = 90;
        else if ($data->HdCross == "Left Angle Cross of Informing (22/47 | 11/12)") $cross = 91;
        else if ($data->HdCross == "Left Angle Cross of Informing (47/22 | 12/11)") $cross = 92;
        else if ($data->HdCross == "Left Angle Cross of Wishes (3/50 | 41/31)") $cross = 93;
        else if ($data->HdCross == "Left Angle Cross of Wishes (50/3 | 31/41)") $cross = 94;
        else if ($data->HdCross == "Left Angle Cross of Spirit  (55/59 | 9/16)") $cross = 95;
        else if ($data->HdCross == "Left Angle Cross of Spirit  (59/55 | 16/9)") $cross = 96;
        else if ($data->HdCross == "Left Angle Cross of Healing (25/46 | 58/52)") $cross = 97;
        else if ($data->HdCross == "Left Angle Cross of Healing (46/25 | 52/58)") $cross = 98;
        else if ($data->HdCross == "Left Angle Cross of Migration (37/40 | 5/35)") $cross = 99;
        else if ($data->HdCross == "Left Angle Cross of Migration (40/37 | 35/5)") $cross = 100;
        else if ($data->HdCross == "Left Angle Cross of Dedication (23/43 | 30/29)") $cross = 101;
        else if ($data->HdCross == "Left Angle Cross of Dedication (43/23 | 29/30)") $cross = 102;
        else if ($data->HdCross == "Left Angle Cross of Incarnation (24/44 | 13/7)") $cross = 103;
        else if ($data->HdCross == "Left Angle Cross of Incarnation (44/24 | 7/13)") $cross = 104;
        else if ($data->HdCross == "Left Angle Cross of Identification (16/9 | 63/64)") $cross = 105;
        else if ($data->HdCross == "Left Angle Cross of Identification (9/16 | 64/63)") $cross = 106;
        else if ($data->HdCross == "Left Angle Cross of Defiance (2/1 | 49/4)") $cross = 107;
        else if ($data->HdCross == "Left Angle Cross of Defiance (1/2 | 4/49)") $cross = 108;
        else if ($data->HdCross == "Left Angle Cross of Demands (52/58 | 21/48)") $cross = 109;
        else if ($data->HdCross == "Left Angle Cross of Demands (58/52 | 48/21)") $cross = 110;
        else if ($data->HdCross == "Left Angle Cross of Limitation (42/32 | 60/56)") $cross = 111;
        else if ($data->HdCross == "Left Angle Cross of Limitation (32/42 | 56/60)") $cross = 112;
        else if ($data->HdCross == "Left Angle Cross of Masks (13/7 | 43/23)") $cross = 113;
        else if ($data->HdCross == "Left Angle Cross of Masks (7/13 | 23/43)") $cross = 114;
        else if ($data->HdCross == "Left Angle Cross of Revolution (49/4 | 14/8)") $cross = 115;
        else if ($data->HdCross == "Left Angle Cross of Revolution (4/49 | 8/14)") $cross = 116;
        else if ($data->HdCross == "Left Angle Cross of Alignment (27/28 | 19/33)") $cross = 117;
        else if ($data->HdCross == "Left Angle Cross of Alignment (28/27 | 33/19)") $cross = 118;
        else if ($data->HdCross == "Left Angle Cross of Prevention (15/10 | 17/18)") $cross = 119;
        else if ($data->HdCross == "Left Angle Cross of Prevention (10/15 | 18/17)") $cross = 120;
        else if ($data->HdCross == "Left Angle Cross of Education (12/11 | 25/46)") $cross = 121;
        else if ($data->HdCross == "Left Angle Cross of Education (11/12 | 46/25)") $cross = 122;
        else if ($data->HdCross == "Left Angle Cross of Cycles (53/54 | 42/32)") $cross = 123;
        else if ($data->HdCross == "Left Angle Cross of Cycles (54/53 | 32/42)") $cross = 124;
        else if ($data->HdCross == "Left Angle Cross of Refinement  (33/19 | 2/1)") $cross = 125;
        else if ($data->HdCross == "Left Angle Cross of Refinement  (19/33 | 1/2)") $cross = 126;
        else if ($data->HdCross == "Left Angle Cross of Obscuration (62/61 | 3/50)") $cross = 127;
        else if ($data->HdCross == "Left Angle Cross of Obscuration (61/62 | 50/3)") $cross = 128;
        else if ($data->HdCross == "Juxtaposition Cross of Power (34/20 | 40/37)") $cross = 129;
        else if ($data->HdCross == "Juxtaposition Cross of Habits (5/35 | 47/22)") $cross = 130;
        else if ($data->HdCross == "Juxtaposition Cross of Focus (9/16 | 64/63)") $cross = 131;
        else if ($data->HdCross == "Juxtaposition Cross of Formulization (4/49 | 8/14)") $cross = 132;
        else if ($data->HdCross == "Juxtaposition Cross of Innocence (25/46 | 58/52)") $cross = 133;
        else if ($data->HdCross == "Juxtaposition Cross of Fantasy (41/31 | 44/24)") $cross = 134;
        else if ($data->HdCross == "Juxtaposition Cross of Beginnings (53/54 | 42/32)") $cross = 135;
        else if ($data->HdCross == "Juxtaposition Cross of Risks (28/27 | 33/19)") $cross = 136;
        else if ($data->HdCross == "Juxtaposition Cross of Possession (45/26 | 36/6)") $cross = 137;
        else if ($data->HdCross == "Juxtaposition Cross of Articulation (12/11 | 25/46)") $cross = 138;
        else if ($data->HdCross == "Juxtaposition Cross of Opposition (38/39 | 57/51)") $cross = 139;
        else if ($data->HdCross == "Juxtaposition Cross of Bargains (37/40 | 5/35)") $cross = 140;
        else if ($data->HdCross == "Juxtaposition Cross of Values (50/3 | 31/41)") $cross = 141;
        else if ($data->HdCross == "Juxtaposition Cross of Grace (22/47 | 11/12)") $cross = 142;
        else if ($data->HdCross == "Juxtaposition Cross of Conflict (6/36 | 15/10)") $cross = 143;
        else if ($data->HdCross == "Juxtaposition Cross of Crisis (36/6 | 10/15)") $cross = 144;
        else if ($data->HdCross == "Juxtaposition Cross of Oppression (47/22 | 12/11)") $cross = 145;
        else if ($data->HdCross == "Juxtaposition Cross of Assimilation (23/43 | 30/29)") $cross = 146;
        else if ($data->HdCross == "Juxtaposition Cross of The Now (20/34 | 37/40)") $cross = 147;
        else if ($data->HdCross == "Juxtaposition Cross of Serendipity (46/25 | 52/58)") $cross = 148;
        else if ($data->HdCross == "Juxtaposition Cross of Self-expression (1/2 | 4/49)") $cross = 149;
        else if ($data->HdCross == "Juxtaposition Cross of Behavior (10/15 | 18/17)") $cross = 150;
        else if ($data->HdCross == "Juxtaposition Cross of Rationalization (24/44 | 13/7)") $cross = 151;
        else if ($data->HdCross == "Juxtaposition Cross of Confusion (64/63 | 45/26)") $cross = 152;
        else if ($data->HdCross == "Juxtaposition Cross of Completion (42/32 | 60/56)") $cross = 153;
        else if ($data->HdCross == "Juxtaposition Cross of Doubts (63/64 | 26/45)") $cross = 154;
        else if ($data->HdCross == "Juxtaposition Cross of Extremes (15/10 | 17/18)") $cross = 155;
        else if ($data->HdCross == "Juxtaposition Cross of Contribution (8/14 | 55/59)") $cross = 156;
        else if ($data->HdCross == "Juxtaposition Cross of Stimulation (56/60 | 27/28)") $cross = 157;
        else if ($data->HdCross == "Juxtaposition Cross of Fates (30/29 | 34/20)") $cross = 158;
        else if ($data->HdCross == "Juxtaposition Cross of Experimentation (16/9 | 63/64)") $cross = 159;
        else if ($data->HdCross == "Juxtaposition Cross of Commitment (29/30 | 20/34)") $cross = 160;
        else if ($data->HdCross == "Juxtaposition Cross of Ambition (54/53 | 32/42)") $cross = 161;
        else if ($data->HdCross == "Juxtaposition Cross of Denial (40/37 | 35/5)") $cross = 162;
        else if ($data->HdCross == "Juxtaposition Cross of Intuition (57/51 | 62/61)") $cross = 163;
        else if ($data->HdCross == "Juxtaposition Cross of Detail (62/61 | 3/50)") $cross = 164;
        else if ($data->HdCross == "Juxtaposition Cross of Experience (35/5 | 22/47)") $cross = 165;
        else if ($data->HdCross == "Juxtaposition Cross of the Driver (2/1 | 49/4)") $cross = 166;
        else if ($data->HdCross == "Juxtaposition Cross of Conservation (32/42 | 56/60)") $cross = 167;
        else if ($data->HdCross == "Juxtaposition Cross of Thinking (61/62 | 50/3)") $cross = 168;
        else if ($data->HdCross == "Juxtaposition Cross of Provocation (39/38 | 51/57)") $cross = 169;
        else if ($data->HdCross == "Juxtaposition Cross of Insight (43/23 | 29/30)") $cross = 170;
        else if ($data->HdCross == "Juxtaposition Cross of Vitality (58/52 | 48/21)") $cross = 171;
        else if ($data->HdCross == "Juxtaposition Cross of Interaction (7/13 | 23/43)") $cross = 172;
        else if ($data->HdCross == "Juxtaposition Cross of Mutation (3/50 | 41/31)") $cross = 173;
        else if ($data->HdCross == "Juxtaposition Cross of Limitation (60/56 | 28/27)") $cross = 174;
        else if ($data->HdCross == "Juxtaposition Cross of Correction (18/17 | 39/38)") $cross = 175;
        else if ($data->HdCross == "Juxtaposition Cross of Listening (13/7 | 43/23)") $cross = 176;
        else if ($data->HdCross == "Juxtaposition Cross of Principles (49/4 | 14/8)") $cross = 177;
        else if ($data->HdCross == "Juxtaposition Cross of Moods (55/59 | 9/16)") $cross = 178;
        else if ($data->HdCross == "Juxtaposition Cross of Shock (51/57 | 61/62)") $cross = 179;
        else if ($data->HdCross == "Juxtaposition Cross of Control (21/48 | 54/53)") $cross = 180;
        else if ($data->HdCross == "Juxtaposition Cross of Depth (48/21 | 53/54)") $cross = 181;
        else if ($data->HdCross == "Juxtaposition Cross of Strategy (59/55 | 16/9)") $cross = 182;
        else if ($data->HdCross == "Juxtaposition Cross of Retreat (33/19 | 2/1)") $cross = 183;
        else if ($data->HdCross == "Juxtaposition Cross of Ideas (11/12 | 46/25)") $cross = 184;
        else if ($data->HdCross == "Juxtaposition Cross of Opinions (17/18 | 38/39)") $cross = 185;
        else if ($data->HdCross == "Juxtaposition Cross of Caring (27/28 | 19/33)") $cross = 186;
        else if ($data->HdCross == "Juxtaposition Cross of Need (19/33 | 1/2)") $cross = 187;
        else if ($data->HdCross == "Juxtaposition Cross of Stillness (52/58 | 21/48)") $cross = 188;
        else if ($data->HdCross == "Juxtaposition Cross of Influence (31/41 | 24/44)") $cross = 189;
        else if ($data->HdCross == "Juxtaposition Cross of Empowering (14/8 | 59/55)") $cross = 190;
        else if ($data->HdCross == "Juxtaposition Cross of Alertness (44/24 | 7/13)") $cross = 191;
        else if ($data->HdCross == "Juxtaposition Cross of The Trickster (26/45 | 6/36)") $cross = 192;
        wh_log("cross----->" .json_encode($cross)."<br>");

        //else  $cross = 1;
        //$profile
        if ($data->HdProfile == "1 / 3") $profile = 1;
        else if ($data->HdProfile == "1 / 4") $profile = 2;
        else if ($data->HdProfile == "2 / 4") $profile = 3;
        else if ($data->HdProfile == "2 / 5") $profile = 4;
        else if ($data->HdProfile == "3 / 5") $profile = 5;
        else if ($data->HdProfile == "3 / 6") $profile = 6;
        else if ($data->HdProfile == "4 / 1") $profile = 7;
        else if ($data->HdProfile == "4 / 6") $profile = 8;
        else if ($data->HdProfile == "5 / 1") $profile = 9;
        else if ($data->HdProfile == "5 / 2") $profile = 10;
        else if ($data->HdProfile == "6 / 2") $profile = 11;
        else if ($data->HdProfile == "6 / 3") $profile = 12;
       // else $profile = 1;
        wh_log("profile----->" .json_encode($profile)."<br>");

        //$definition
        if ($data->HdDefinition == "Single Definition") $definition = 1;
        else if ($data->HdDefinition == "Split Definition") $definition = 2;
        else if ($data->HdDefinition == "Triple Split Definition") $definition = 3;
        else if ($data->HdDefinition == "Quadruple Split Definition") $definition = 4;
        else if ($data->HdDefinition == "No Definition") $definition = 5;
       // else $definition = 5;
        //$channel
                wh_log("definition----->" .json_encode($definition)."<br>");


        $center_check = array(0,0,0,0,0,0,0,0,0); //0-頭腦中心 1-邏輯中心 2-喉嚨中心 3-G中心 4-意志力中心 5-情緒中心 6-薦骨中心 7-直覺中心 8-根部中心
        
        if (in_array(1, $gate) && in_array(8, $gate)){
            array_push($channel, 1);
            $center_check[2] = 1;
            $center_check[3] = 1;
        }
        if (in_array(2, $gate) && in_array(14, $gate)){
            array_push($channel, 2);
            $center_check[3] = 1;
            $center_check[6] = 1;
        }
        if (in_array(3, $gate) && in_array(60, $gate)){
            array_push($channel, 3);
            $center_check[6] = 1;
            $center_check[8] = 1;
        }
        if (in_array(63, $gate) && in_array(4, $gate)){
            array_push($channel, 4);
            $center_check[0] = 1;
            $center_check[1] = 1;
        }
        if (in_array(15, $gate) && in_array(5, $gate)){
            array_push($channel, 5);
            $center_check[3] = 1;
            $center_check[6] = 1;
        }
        if (in_array(59, $gate) && in_array(6, $gate)){
            array_push($channel, 6);
            $center_check[6] = 1;
            $center_check[5] = 1;
        }
        if (in_array(31, $gate) && in_array(7, $gate)){
            array_push($channel, 7);
            $center_check[2] = 1;
            $center_check[3] = 1;
        }
        if (in_array(9, $gate) && in_array(52, $gate)){
            array_push($channel, 8);
            $center_check[6] = 1;
            $center_check[8] = 1;
        }
        if (in_array(10, $gate) && in_array(20, $gate)){
            array_push($channel, 9);
            $center_check[2] = 1;
            $center_check[3] = 1;
        }
        if (in_array(10, $gate) && in_array(34, $gate)){
            array_push($channel, 10);
            $center_check[3] = 1;
            $center_check[6] = 1;
        }
        if (in_array(10, $gate) && in_array(57, $gate)){
            array_push($channel, 11);
            $center_check[3] = 1;
            $center_check[7] = 1;
        }
        if (in_array(11, $gate) && in_array(56, $gate)){
            array_push($channel, 12);
            $center_check[1] = 1;
            $center_check[2] = 1;
        }
        if (in_array(12, $gate) && in_array(22, $gate)){
            array_push($channel, 13);
            $center_check[2] = 1;
            $center_check[5] = 1;
        }
        if (in_array(13, $gate) && in_array(33, $gate)){
            array_push($channel, 14);
            $center_check[2] = 1;
            $center_check[3] = 1;
        }
        if (in_array(48, $gate) && in_array(16, $gate)){
            array_push($channel, 15);
            $center_check[7] = 1;
            $center_check[2] = 1;
        }
        if (in_array(17, $gate) && in_array(62, $gate)){
            array_push($channel, 16);
            $center_check[1] = 1;
            $center_check[2] = 1;
        }
        if (in_array(18, $gate) && in_array(58, $gate)){
            array_push($channel, 17);
            $center_check[7] = 1;
            $center_check[8] = 1;
        }
        if (in_array(19, $gate) && in_array(49, $gate)){
            array_push($channel, 18);
            $center_check[5] = 1;
            $center_check[8] = 1;
        }
        if (in_array(20, $gate) && in_array(34, $gate)){
            array_push($channel, 19);
            $center_check[2] = 1;
            $center_check[6] = 1;
        }
        if (in_array(20, $gate) && in_array(57, $gate)){
            array_push($channel, 20);
            $center_check[2] = 1;
            $center_check[7] = 1;
        }
        if (in_array(45, $gate) && in_array(21, $gate)){
            array_push($channel, 21);
            $center_check[2] = 1;
            $center_check[4] = 1;
        }
        if (in_array(43, $gate) && in_array(23, $gate)){
            array_push($channel, 22);
            $center_check[1] = 1;
            $center_check[2] = 1;
        }
        if (in_array(61, $gate) && in_array(24, $gate)){
            array_push($channel, 23);
            $center_check[0] = 1;
            $center_check[1] = 1;
        }
        if (in_array(25, $gate) && in_array(51, $gate)){
            array_push($channel, 24);
            $center_check[3] = 1;
            $center_check[4] = 1;
        }
        if (in_array(44, $gate) && in_array(26, $gate)){
            array_push($channel, 25);
            $center_check[7] = 1;
            $center_check[4] = 1;
        }
        if (in_array(50, $gate) && in_array(27, $gate)){
            array_push($channel, 26);
            $center_check[6] = 1;
            $center_check[7] = 1;
        }
        if (in_array(28, $gate) && in_array(38, $gate)){
            array_push($channel, 27);
            $center_check[7] = 1;
            $center_check[8] = 1;
        }
        if (in_array(46, $gate) && in_array(29, $gate)){
            array_push($channel, 28);
            $center_check[3] = 1;
            $center_check[6] = 1;
        }
        if (in_array(41, $gate) && in_array(30, $gate)){
            array_push($channel, 29);
            $center_check[8] = 1;
            $center_check[5] = 1;
        }
        if (in_array(54, $gate) && in_array(32, $gate)){
            array_push($channel, 30);
            $center_check[7] = 1;
            $center_check[8] = 1;
        }
        if (in_array(34, $gate) && in_array(57, $gate)){
            array_push($channel, 31);
            $center_check[6] = 1;
            $center_check[7] = 1;
        }
        if (in_array(35, $gate) && in_array(36, $gate)){
            array_push($channel, 32);
            $center_check[2] = 1;
            $center_check[5] = 1;
        }
        if (in_array(37, $gate) && in_array(40, $gate)){
            array_push($channel, 33);
            $center_check[4] = 1;
            $center_check[5] = 1;
        }
        if (in_array(39, $gate) && in_array(55, $gate)){
            array_push($channel, 34);
            $center_check[5] = 1;
            $center_check[8] = 1;
        }
        if (in_array(42, $gate) && in_array(53, $gate)){
            array_push($channel, 35);
            $center_check[6] = 1;
            $center_check[8] = 1;
        }
        if (in_array(64, $gate) && in_array(47, $gate)){
            array_push($channel, 36);
            $center_check[0] = 1;
            $center_check[1] = 1;
        }
        wh_log("channel----->" .json_encode($channel)."<br>");

        //$center
        if ($center_check[0] == 1) array_push($center, 1);
        else if ($center_check[0] == 0) array_push($center, 2);
        if ($center_check[1] == 1) array_push($center, 3);
        else if ($center_check[1] == 0) array_push($center, 4);
        if ($center_check[2] == 1) array_push($center, 5);
        else if ($center_check[2] == 0) array_push($center, 6);
        if ($center_check[3] == 1) array_push($center, 7);
        else if ($center_check[3] == 0) array_push($center, 8);
        if ($center_check[4] == 1) array_push($center, 9);
        else if ($center_check[4] == 0) array_push($center, 10);
        if ($center_check[5] == 1) array_push($center, 11);
        else if ($center_check[5] == 0) array_push($center, 12);
        if ($center_check[6] == 1) array_push($center, 13);
        else if ($center_check[6] == 0) array_push($center, 14);
        if ($center_check[7] == 1) array_push($center, 15);
        else if ($center_check[7] == 0) array_push($center, 16);
        if ($center_check[8] == 1) array_push($center, 17);
        else if ($center_check[8] == 0) array_push($center, 18);
        wh_log("center----->" .json_encode($center)."<br>");
        //$background
        if ((int)$NNodeD[1] == 2 || (int)$NNodeD[1] == 1) $background = 1;
        else if ((int)$NNodeD[1] == 3 || (int)$NNodeD[1] == 50) $background = 2;
        else if ((int)$NNodeD[1] == 8 || (int)$NNodeD[1] == 14) $background = 3;
        else if ((int)$NNodeD[1] == 12 || (int)$NNodeD[1] == 11) $background = 4;
        else if ((int)$NNodeD[1] == 13 || (int)$NNodeD[1] == 7) $background = 5;
        else if ((int)$NNodeD[1] == 15 || (int)$NNodeD[1] == 10) $background = 6;
        else if ((int)$NNodeD[1] == 16 || (int)$NNodeD[1] == 9) $background = 7;
        else if ((int)$NNodeD[1] == 17 || (int)$NNodeD[1] == 18) $background = 8;
        else if ((int)$NNodeD[1] == 20 || (int)$NNodeD[1] == 34) $background = 9;
        else if ((int)$NNodeD[1] == 21 || (int)$NNodeD[1] == 48) $background = 10;
        else if ((int)$NNodeD[1] == 22 || (int)$NNodeD[1] == 47) $background = 11;
        else if ((int)$NNodeD[1] == 24 || (int)$NNodeD[1] == 44) $background = 12;
        else if ((int)$NNodeD[1] == 25 || (int)$NNodeD[1] == 46) $background = 13;
        else if ((int)$NNodeD[1] == 27 || (int)$NNodeD[1] == 28) $background = 14;
        else if ((int)$NNodeD[1] == 29 || (int)$NNodeD[1] == 30) $background = 15;
        else if ((int)$NNodeD[1] == 31 || (int)$NNodeD[1] == 41) $background = 16;
        else if ((int)$NNodeD[1] == 33 || (int)$NNodeD[1] == 19) $background = 17;
        else if ((int)$NNodeD[1] == 35 || (int)$NNodeD[1] == 5) $background = 18;
        else if ((int)$NNodeD[1] == 36 || (int)$NNodeD[1] == 6) $background = 19;
        else if ((int)$NNodeD[1] == 37 || (int)$NNodeD[1] == 40) $background = 20;
        else if ((int)$NNodeD[1] == 38 || (int)$NNodeD[1] == 39) $background = 21;
        else if ((int)$NNodeD[1] == 42 || (int)$NNodeD[1] == 32) $background = 22;
        else if ((int)$NNodeD[1] == 43 || (int)$NNodeD[1] == 23) $background = 23;
        else if ((int)$NNodeD[1] == 45 || (int)$NNodeD[1] == 26) $background = 24;
        else if ((int)$NNodeD[1] == 49 || (int)$NNodeD[1] == 4) $background = 25;
        else if ((int)$NNodeD[1] == 51 || (int)$NNodeD[1] == 57) $background = 26;
        else if ((int)$NNodeD[1] == 52 || (int)$NNodeD[1] == 58) $background = 27;
        else if ((int)$NNodeD[1] == 53 || (int)$NNodeD[1] == 54) $background = 28;
        else if ((int)$NNodeD[1] == 55 || (int)$NNodeD[1] == 59) $background = 29;
        else if ((int)$NNodeD[1] == 56 || (int)$NNodeD[1] == 60) $background = 30;
        else if ((int)$NNodeD[1] == 61 || (int)$NNodeD[1] == 62) $background = 31;
        else if ((int)$NNodeD[1] == 63 || (int)$NNodeD[1] == 64) $background = 32;
         wh_log("background----->" .json_encode($background)."<br>");
        //$money
        array_push($money, (int)$SunP[1]);
        array_push($money, (int)$EarthP[1]);
        if ($NNodeP[2] == ".3") array_push($money, (int)$NNodeP[1]);
        if ($SNodeP[2] == ".3") array_push($money, (int)$SNodeP[1]);
        if ($MoonP[2] == ".3") array_push($money, (int)$MoonP[1]);
        if ($MercuryP[2] == ".3") array_push($money, (int)$MercuryP[1]);
        if ($VenusP[2] == ".3") array_push($money, (int)$VenusP[1]);
        if ($MarsP[2] == ".3") array_push($money, (int)$MarsP[1]);
        if ($JupiterP[2] == ".3") array_push($money, (int)$JupiterP[1]);
        if ($SaturnP[2] == ".3") array_push($money, (int)$SaturnP[1]);
        if ($UranusP[2] == ".3") array_push($money, (int)$UranusP[1]);
        if ($NeptuneP[2] == ".3") array_push($money, (int)$NeptuneP[1]);
        if ($PlutoP[2] == ".3") array_push($money, (int)$PlutoP[1]);
        if ($SunD[2] == ".3") array_push($money, (int)$SunD[1]);
        if ($EarthD[2] == ".3") array_push($money, (int)$EarthD[1]);
        if ($NNodeD[2] == ".3") array_push($money, (int)$NNodeD[1]);
        if ($SNodeD[2] == ".3") array_push($money, (int)$SNodeD[1]);
        if ($MoonD[2] == ".3") array_push($money, (int)$MoonD[1]);
        if ($MercuryD[2] == ".3") array_push($money, (int)$MercuryD[1]);
        if ($VenusD[2] == ".3") array_push($money, (int)$VenusD[1]);
        if ($MarsD[2] == ".3") array_push($money, (int)$MarsD[1]);
        if ($JupiterD[2] == ".3") array_push($money, (int)$JupiterD[1]);
        if ($SaturnD[2] == ".3") array_push($money, (int)$SaturnD[1]);
        if ($UranusD[2] == ".3") array_push($money, (int)$UranusD[1]);
        if ($NeptuneD[2] == ".3") array_push($money, (int)$NeptuneD[1]);
        if ($PlutoD[2] == ".3") array_push($money, (int)$PlutoD[1]);
        wh_log("money----->" .json_encode($money)."<br>");
        //C
        if ($category == 1 && $authority == 1) $c = 1;
        else if ($category == 1 && $authority == 2) $c = 2;
        else if ($category == 2 && $authority == 1) $c = 3;
        else if ($category == 2 && $authority == 2) $c = 4;
        else if ($category == 3 && $authority == 3) $c = 5;
        else if ($category == 3 && $authority == 4) $c = 6;
        else if ($category == 3 && $authority == 5) $c = 7;
        else if ($category == 3 && $authority == 6) $c = 8;
        else if ($category == 3 && $authority == 7) $c = 9;
        else if ($category == 4 && $authority == 3) $c = 10;
        else if ($category == 4 && $authority == 4) $c = 11;
        else if ($category == 4 && $authority == 8) $c = 12;
        else if ($category == 5 && $authority == 9) $c = 13;
        else  $c = 1;

            wh_log("c----->" .$c."<br>");

        if (isset($clientname)){

            ob_start();
            $comment = "";
            //Category=============================
            $sql = "SELECT * from hd_category where category_id='" . $category . "'";
            $category = $conn->query($sql);
            $cat_row = $category->fetch_object();
            $category_body1 = $cat_row->category_body1;
            //$category_body2=$cat_row->category_body2;
            $notself = $cat_row->notself;
            $strategy = $cat_row->strategy;
            $category_name = $cat_row->category_name;
            wh_log("catename----->" .$category_name."<br>");

            //echo $category_name;exit;
            //C=============================
            $c_sql = "SELECT * from c where id=$c";
            $c = $conn->query($c_sql);
            $c_row = $c->fetch_object();
            $category_body2 = $c_row->body_2;
            $category_body3 = $c_row->body_3;
            wh_log("catebody----->" .$category_body3."<br>");
            //Authority=============================
            $auth_sql = "SELECT * from  hd_authority where authority_id=$authority";
            $authority = $conn->query($auth_sql);
            $auth_row = $authority->fetch_object();
            $authority_name = $auth_row->authority_name;
            $authority_body1 = $auth_row->authority_body1;
            wh_log("authobody----->" .$authority_body1."<br>");
            //$authority_body2=$auth_row->authority_body2;
            //Part C
            //Defination============================
            $def_sql = "SELECT * from  hd_definition where definition_id=$definition";
            $definition = $conn->query($def_sql);
            $def_row = $definition->fetch_object();
            $definition_name = $def_row->definition_name;
            $definition_body = $def_row->definition_body;
            wh_log( "difinobody----->" .$definition_body."<br>");

            //Cross================================
            $cross_sql = "SELECT * from  hd_cross where  cross_id=$cross";
            wh_log("crossdetails----->" .$cross_sql."<br>");
            $cross = $conn->query($cross_sql);
            $cross_row = $cross->fetch_object();
            $cross_name = $cross_row->cross_name;
            $cross_body1 = $cross_row->cross_body1;
            $cross_body2 = $cross_row->cross_body2;
            $cross_body3 = $cross_row->cross_body3;
            wh_log("crossbody----->" .$cross_body3."<br>");

            //profile=============================
            $profile_sql = "SELECT * from  hd_profile where profile_id=$profile";
            $profile = $conn->query($profile_sql);
            $profile_row = $profile->fetch_object();
            $profile_name = $profile_row->profile_name;
            $profile_body1 = $profile_row->profile_body1;
            $profile_body2 = $profile_row->profile_body2;
            $profile_body3 = $profile_row->profile_body3;
            wh_log( "profilebody----->" .$profile_body3."<br>");
            
            //background=============================
            

            if (!empty($background)){
                //print_r($background);
                $background_sql = "SELECT * from  hd_background where background_id=$background";
                $background = $conn->query($background_sql);
                $background_row = $background->fetch_object();
                $background_body = $background_row->background_body;
            }else{
                $background_body = '';
            }
            wh_log( "background_body----->" .$background_body."<br>");
            //center==============================
            if (!empty($gate)){
                $center = implode(",", $center);
                $center_sql = "SELECT * from  hd_center where center_id IN($center)";
                $center = $conn->query($center_sql);
                $center_names = array();
                while ($center_row = $center->fetch_object())
                {
                    $center_names[] = $center_row->center;
                }
                $center_names = implode(" ", $center_names);
            }else{
                $center_names = '';
            }
             wh_log( "center_names----->" .$center_names."<br>");

            if (!empty($channel)){
                $channel = implode(",", $channel);
                $channel_sql = "SELECT * from  hd_channel where channel_id IN($channel)";
                $channel_obj = $conn->query($channel_sql);
                $channel_names1 = array();
                $channel_names2 = array();
                while ($channel_row = $channel_obj->fetch_object()){
                    $channel_names1[] = $channel_row->channel_body1;
                    $channel_names2[] = $channel_row->channel_body2;
                }
                $channel_body1 = implode("\n", $channel_names1);
                $channel_body2 = implode("\n", $channel_names2);
            }else{
                $channel_body1 = '';
                $channel_body2 = '';
            }
            wh_log( "channel_body1----->" .$channel_body1."<br>");
            wh_log( "channel_body2----->" .$channel_body2."<br>");
            //gate==============================
            if (!empty($gate)){
                $gate = implode(",", $gate);
                $gate_sql = "SELECT * from  hd_gates where gate_id IN($gate)";
                $gate_obj = $conn->query($gate_sql);
                $gate_names = array();
                while ($gate_row = $gate_obj->fetch_object())
                {
                    $gate_names[] = $gate_row->gate_body;
                }
                $gate_body = implode("\n", $gate_names);
            }else{
                $gate_body = '';
            }
            wh_log( "gate_body----->" .$gate_body."<br>");

            //money==============================
            if (!empty($money)){
                $money = implode(",", $money);

                $money_sql = "SELECT * from  hd_money where money_id IN($money)";
                $money_obj = $conn->query($money_sql);
                $money_names = array();
                while ($money_row = $money_obj->fetch_object()){
                    $money_names[] = $money_row->money_body;
                }

                $money_body = implode("\n", $money_names);
            }else{
                $money_body = '';
            }
            wh_log( "money_body----->" .$money_body."<br>");

            if ($data->HdType == "反映者"){
                
                if($getdocsfile == 'docx')
                {
                   include "template_reflector_trad _preading.php";
                }
                else
                {
                    include "template_reflector_trad.php";
                    wh_log("templatereflect----->reflect<br>");
                }
            }else{
                if($getdocsfile == 'docx')
                {
                    include "template_trad_preading.php";
                }
            else{
                     include "template_trad.php";
                     wh_log("template----->template<br>");
                }
            }

            $html = ob_get_contents();
            $preBodyHtml = '<div >
                        <div class="firstPage first-page-border" style="height:100%;">
                            <div class="" style="top: 0px;position: absolute; bottom:0;">
                                <div>
                                    <img src="' . $accessPath . 'Logo1_Chinese(Traditional).png" style="margin-top:30px;margin-left: 80px;margin-right: 80px;height: 200px;" />
                                </div>
                                <div style="margin-top:160px; font-size: 30px; transform: scaleX(1);text-align: center;font-weight:bold;">專屬於</div>
                
                                <div style="margin-top:80px; font-size: 30px; transform: scaleX(0.777778); text-align: center;font-weight:bold;">' . $clientname . '</div>
                
                                <div style="margin-top:122px; font-size: 30px; transform: scaleX(1); text-align: center;font-weight:bold;font-family: msjh">的人類圖報告書</div>
                
                                <div style="margin-top:150px; font-size: 20px; transform: scaleX(1); text-align: center;font-weight:bold;">從瞭解自己，愛自己出發</div>
                
                                <div style="margin-top:149px; font-size: 18px; transform: scaleX(1); text-align: center; font-weight:bold;">www.humandesign.com.hk</div>
                
                            </div>
                        </div>
                    </div>';
            ob_end_clean();

            //set font
            $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];
            $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];
            $mpdf = new \Mpdf\Mpdf(['autoScriptToLang' => true, 'fontDir' => array_merge($fontDirs, [__DIR__ . '/font', ]) , 'fontdata' => $fontData + ['msjh' => ['R' => 'msjh.ttf', 'B' => 'msjhbd.ttf']], 'default_font' => 'msjh', 'margin_top' => 0, 'margin_bottom' => 0, 'margin_left' => 0, '']);

            $mpdf->WriteHTML('<!DOCTYPE html>
            <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
               
                <style type="text/css">
                .first-page-border {
                        border-left: 50px;
                        border-style: solid;
                        border-color:#fdb858;
                        /*border-image: linear-gradient(to top, #fdb858, #FFFFFF) 1 100%;*/
                }
                body{
                    font-weight:bold;
                }
                p.normal{font-size:14px;line-height:25px;text-align:left;text-indent:30px}p.in-normal{font-size:15px;line-height:25px;text-align:left;margin-top:0;font-weight:700}p.bold{font-weight:700;font-size:14px;line-height:25px;text-align:left;margin-top:0}p.indent{text-indent:30px}p.notindent{text-indent:0}.notopmargin{margin-top:0}.nobottommargin{margin-bottom:0}strong{font-weight:700}div.red{color:#ba3205;font-size:17px;line-height:25px;text-align:left;float:left;font-weight:700}div.blue{width:100%;background-color:#cce4eb;border-bottom:1px solid #000;float:left;text-align:left;font-weight:700;font-size:16px;margin-bottom:0;padding-top:3px;padding-bottom:3px}div.center1{width:100%;background-color:#ffe161;border-bottom:1px solid #000;float:left;text-align:left;font-weight:700;font-size:16px;margin-bottom:0;padding-top:3px;padding-bottom:3px}div.center2{width:100%;background-color:#6ec038;border-bottom:1px solid #000;float:left;text-align:left;font-weight:700;font-size:16px;margin-bottom:0;padding-top:3px;padding-bottom:3px}div.center3{width:100%;background-color:#ab7a42;border-bottom:1px solid #000;float:left;text-align:left;font-weight:700;font-size:16px;margin-bottom:0;padding-top:3px;padding-bottom:3px}div.center4{width:100%;background-color:#ff2c22;border-bottom:1px solid #000;float:left;text-align:left;font-weight:700;font-size:16px;margin-bottom:0;padding-top:3px;padding-bottom:3px}div.center5{width:100%;background-color:#bfbfbf;border-bottom:1px solid #000;float:left;text-align:left;font-weight:700;font-size:16px;margin-bottom:0;padding-top:3px;padding-bottom:3px}hr.yellowline{width:100%;background-color:#f9ac18;color:#f9ac18;border:none;height:3px}div.test{float:left;font-weight:700;font-size:21px;color:#175778;text-align:left;margin-bottom:0}
                
                .firstPage{background-image: url("' . $accessPath . 'grey.png");background-repeat: no-repeat;background-position: center;background-size:100% 100%}
            </style>
            </head>
            <body style="font-family: msjh">' . $preBodyHtml . '');
            $mpdf->SetHTMLHeader('<div style="border-style: solid;border-width: 3px;border-color:#fdb858;background-color: #3e94af;text-align: center;color:#ffffff;font-weight: bold;;padding-top: 7px; padding-bottom: 7px;font-size: 14px;">專屬於 ' . $clientname . ' 的人類圖報告書</div>', 'O');
            $mpdf->SetHTMLHeader('<div style="border-style: solid;border-width: 3px;border-color:#fdb858;background-color: #3e94af;text-align: center;color:#ffffff;font-weight: bold;;padding-top: 7px; padding-bottom: 7px; font-size: 14px;">專屬於 ' . $clientname . ' 的人類圖報告書</div>', 'E');
            $mpdf->AddPage('', '', 1, '', 'on', 15, 15, 14, 16, 4, 9);
            $mpdf->WriteHTML($html);
            $mpdf->WriteHTML('</body></html>');
            $mpdf->showWatermarkText = true;
            $mpdf->watermarkTextAlpha = 0.1;
            $mpdf->autoLangToFont = true;
            $mpdf->allow_charset_conversion = false;
            $mpdf->SetDisplayMode('fullpage');
            $test = array(2);
            $y = $z + 1;

            $
            {
                "emailAttachment" . $y
            } = $mpdf->Output('', 'S');
        }else{
            echo "Something go wrong!";
        }
    }
    try
    {
         $sku1 = 'hmrp12';
        $sku2 = 'hmrp2';
        $sku3 = 'hmpr3s';
        if ($sku2 == $details['sku'] || $sku3 == $details['sku']){
           $extension = '.pdf';
            $appType = 'application/pdf';
          $email = "report@humandesign.com.hk";
           
           
            
            }else{
            $extension = '.pdf';
            $appType = 'application/pdf';
        }

        $mail = new PHPMailer(true);
        $mail->Charset = 'UTF-8';
        $mail->AddAddress('report@humandesign.com.hk');
        $mail->AddBCC('report@renleitu.com');
        $mail->SetFrom('report@renleitu.com');
        $mail->Subject = "=?UTF-8?B?" . base64_encode("你的人類圖分析報告已經做好了！") . "?=";

        $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
        $mail->MsgHTML("
<img src='https://humandesign.com.hk/get-pdf/Logo1_Chinese(Traditional).png' style='width:446px;height:150px;'>

<p style='font-size:16px; font-family:roboto;'>你好，<br><br>

你訂制的人類圖報告書已做好。<br><br>
請查看附件。<br><br>

精細解釋與賺錢方法，在報告書最後的位置。<br><br>

我們強烈建議你把它列印出來，較容易閱讀之余，也能親手回答裡面的小測驗，學得更多。另外，希望你能把它留在身旁，每次空閒，或迷茫時，拿出來看一看，說不定會有新發現！<br><br></p>

<p style='font-size:16px; color:#ff9900; font-family:roboto;'>願你每天過得幸福，快樂！<br><br><font style='font-size:22px;'><strong>人類圖報告書</strong></p><br><br>

");
    
    if ($sku1 == $details['sku']){
        
            for ($z = 1;$z <= $num_report;$z++){
                $mail->AddStringAttachment($
                {
                    "emailAttachment" . $z
                }
                , "=?UTF-8?B? " .str_replace( '/', '',base64_encode("$clientname")) ."?==?UTF-8?B? " . base64_encode("的人類圖報告書") . "?=".$z.$extension, $encoding = 'base64', $type = $appType); // attachment
            }
        }else{
            for ($z = 1;$z <= $num_report;$z++)
            {
                 $mail->AddStringAttachment($
                {
                    "emailAttachment" . $z
                }
                , "=?UTF-8?B? " .str_replace( '/', '',base64_encode("$clientname")) ."?==?UTF-8?B? " . base64_encode("的人類圖報告書") . "?=".$z.$extension, $encoding = 'base64', $type = $appType);
            }
        }
    
        // $extension = '.pdf';
        // $appType = 'application/pdf';
        //  for ($z = 1;$z <= $num_report;$z++){
        //     $mail->AddStringAttachment($
        //     {
        //         "emailAttachment" . $z
        //     }
        //     ,$clientname."=?UTF-8?B?" . base64_encode("的人類圖報告書") . "?=".$z.$extension, $encoding = 'base64', $type = $appType); // attachment
        // }

       $is_sent = $mail->Send();
        //echo "123ee<pre>"; print_r($is_sent); echo "</pre>";
       if ($sku1 == $details['sku']){
        if ($details['current_count'] == $details['orderItemTotal']){
            $woocommerce = new Client('https://tempsite.humandesign.com.hk/', // Your store URL
            'ck_0ff4f76bc16a8a30dc7a3a768535218eafc6bef1', // Your consumer key
            'cs_6c50b02f1326a3a47eb4027a787e54251bcb8163', // Your consumer secret
            [
             'wp_api' => true, // Enable the WP REST API integration
             'version' => 'wc/v2'
            
             ]);
            //print_r($woocommerce);
            $order_data = ['status' => 'completed'];
            $woocommerce->put('orders/' . $order_id, $order_data);
        }
    }
    }
    catch(phpmailerException $e)
    {
        echo $e->errorMessage(); //Pretty error messages from PHPMailer
        
    }
    catch(Exception $e)
    {
        echo $e->getMessage(); //Boring error messages from anything else!
        
    }
}
?>
