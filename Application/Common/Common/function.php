<?php
    
    //去除html标签及空格换行
    function delete_html($str) {
        $str = strip_tags($str);
        $str = str_replace(array("\t", "\r\n", "\r", "\n", " "), "", $str);
        return $str;
    }
    
    //utf8编码字符串截取函数
    function cut_str($string, $sublen, $start = 0, $code = 'UTF-8') {
        if ($code == 'UTF-8') {
            $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
            preg_match_all($pa, $string, $t_string);

            if (count($t_string[0]) - $start > $sublen)
                return join('', array_slice($t_string[0], $start, $sublen)) . "...";
            return join('', array_slice($t_string[0], $start, $sublen));
        } else {
            $start = $start * 2;
            $sublen = $sublen * 2;
            $strlen = strlen($string);
            $tmpstr = '';
            for ($i = 0; $i < $strlen; $i++) {
                if ($i >= $start && $i < ($start + $sublen)) {
                    if (ord(substr($string, $i, 1)) > 129)
                        $tmpstr .= substr($string, $i, 2);
                    else
                        $tmpstr .= substr($string, $i, 1);
                }
                if (ord(substr($string, $i, 1)) > 129)
                    $i++;
            }
            if (strlen($tmpstr) < $strlen)
                $tmpstr .= "...";
            return $tmpstr;
        }
    }
    
    //curl
    function curl_request($url,$data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    //注册时验证码函数
    function send_reg_msg($mobile,$msg_content){return true;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://api.weimi.cc/2/sms/send.html");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'uid=GoHGupcRHpt5&pas=xf7zpy69&mob='.$mobile.'&cid=an8VIPQddbLA&p1='.$msg_content.'&type=json');
        $res = curl_exec($ch);
        curl_close($ch);
        
        $res = json_decode($res,true);;
        if($res['code'] === 0){
            return true;
        }else{
            return false;
        }
    }
    
    //随机函数
    function code_random($length = 6 , $numeric = 0) {
        PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
        if($numeric) {
            $hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
        } else {
            $hash = '';
            $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
            $max = strlen($chars) - 1;
            for($i = 0; $i < $length; $i++) {
                $hash .= $chars[mt_rand(0, $max)];
            }
        }
        return $hash;
    }
    
    function xml_to_array($xml){
        $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
        if(preg_match_all($reg, $xml, $matches)){
            $count = count($matches[0]);
            for($i = 0; $i < $count; $i++){
            $subxml= $matches[2][$i];
            $key = $matches[1][$i];
                if(preg_match( $reg, $subxml )){
                    $arr[$key] = xml_to_array( $subxml );
                }else{
                    $arr[$key] = $subxml;
                }
            }
        }
        return $arr;
    }
    
    //格式化日期 2011-03-02 18:22:34或unix时间戳 转换为：3月2日 18时22分
    function fmt_date($date, $time_flg = TRUE) {
        $dateOri = $date;
        if (!strstr($date, '-')) {
            $date = date("Y-m-d H:i:s", $date);
        }
        $date = substr($date, 5, 11);
        $date_array1 = explode('-', $date);
        $month = (int)$date_array1[0];
        $date_array2 = explode(' ', $date_array1[1]);
        $day = (int)$date_array2[0];
        
        $date2 = substr($dateOri,11);
        $dateArr2 = explode(':', $date2);
        $otherdate = $dateArr2[0].'时'.$dateArr2[1].'分';
        if ($time_flg) {
            return $month . '月' . $day . '日 ' . $otherdate;
        } else {
            return $month . '月' . $day . '日 ';
        }
    }
