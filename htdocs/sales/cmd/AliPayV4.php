<?php
define("LIBVER",4.0);
//define("HOSTNAME","http://203.154.233.14:7749?");
//define("HOSTNAME","http://210.213.57.141:7749/v2/?");
//define("HOSTNAME","http://203.154.233.20:7749/v2/?");
define("HOSTNAME","http://171.99.133.30:7749/v2/?");


//Sign Type
define("SHA256","sha256");
define("SHA512","sha512");

//Request Type
define("REQ_TEST","test");
define("REQ_ECHO","echo");
define("REQ_SALE","sale");
define("REQ_CANCEL","cancel");
define("REQ_REFUND","refund");
define("REQ_INQUIRY","inquiry");
define("REQ_NEWSIGNKEY","newsignkey");

//DEFINED PARAMS REQUEST KEY
define("RK_REQUESTTYPE","reqtype");
define("RK_BRAND","brand");
define("RK_STOREID","storeid");
define("RK_DEVICEID","deviceid");
define("RK_REQUESTID","reqid");
define("RK_REQUESTDATETIME","reqdt");
define("RK_AMOUNT","amt");
define("RK_CUSTOMERCODE","custcode");
define("RK_CURRENCY","curr");
define("RK_ORIGINALREQUESTID","origreqid");
define("RK_ORIGINALREQUESTDATETIME","origreqdt");
define("RK_ORIGINALTRANSACTIONID","origtransid");
define("RK_REFUNDREQUESTID","refundreqid");
define("RK_REFUNDREQUESTDATETIME","refundreqdt");
define("RK_REFUNDAMOUNT","refundamt");

//DEFINE RESPONSE KEY
define('RESPONSE_CODE','respcode');
define('RESPONSE_ERRORMESSAGE','errmsg');
define('RESPONSE_VALIDATEON','fieldname');

//ARRAY DEFAULT
define('DEFAULT_MAXLENGTH','@[skip]@');
define('DEFAULT_PATTERN','@[skip]@');
//DEFINED PATTERN
define('PATTERN_NUMERIC','^[0-9]*$');
define('PATTERN_ALPHABETIC_ENG','^[a-zA-Z]*$');
define('PATTERN_ALPHABETIC_ENG_CAPITAL','^[A-Z]*$');
define('PATTERN_ALPHANUMERIC_ENG','^[0-9a-zA-Z]*$');
define('PATTERN_ALPHANUMERIC_ENG_SPECIAL','^[0-9a-zA-Z+\-*\/_@#$%^&*=]*$');
define('PATTERN_DATETIME','^(?:(?:(?:(?:(?:[13579][26]|[2468][048])00)|(?:[0-9]{2}(?:(?:[13579][26])|(?:[2468][048]|0[48]))))(?:(?:(?:09|04|06|11)(?:0[1-9]|1[0-9]|2[0-9]|30))|(?:(?:01|03|05|07|08|10|12)(?:0[1-9]|1[0-9]|2[0-9]|3[01]))|(?:02(?:0[1-9]|1[0-9]|2[0-9]))))|(?:[0-9]{4}(?:(?:(?:09|04|06|11)(?:0[1-9]|1[0-9]|2[0-9]|30))|(?:(?:01|03|05|07|08|10|12)(?:0[1-9]|1[0-9]|2[0-9]|3[01]))|(?:02(?:[01][0-9]|2[0-8])))))(?:0[0-9]|1[0-9]|2[0-3])(?:[0-5][0-9]){2}$');
define('PATTERN_DECIMALFORMAT','^[0-9]{1,12}+(\.[0-9][0-9]?)?$');
define("LOCAL_VALIDATION","LIBRARY_INVALIDATED_PARAMETERS");

class SSUP_Controller_Plugin_AliPayV4 extends Zend_Controller_Plugin_Abstract{
	const _LOGMOTTO = 'SCYapilaWSLib';
    private $signkey;
    private $signtype;
    private $resp = array(RESPONSE_CODE => '', RESPONSE_ERRORMESSAGE => '');
    private $default_validateRules = array('require' => true, 'maxlength' => DEFAULT_MAXLENGTH, 'pattern' => DEFAULT_PATTERN);
    private $validateRulesSize = 0;
    private $hostname;

    function __construct($signType = SHA256)
    {
		//$this->signkey = "212589b1-13b5-4d28-be13-06f4d92d02f4";
		//$this->signkey = "cc031ff1-9337-43c3-a148-2912d28975f2";//test
		//$this->signkey = "bd3eabad-ef95-43fb-8da2-ea22067888f9";
		$this->signkey = "689090b5-a773-4074-95d5-c697e11ae03d";
		
		
		$this->signtype = SHA256;
		
		//$this->merid = "9990000059";
		$this->brand = "OP";
        $this->hostname = HOSTNAME;
        $this->validateRulesSize = count($this->default_validateRules);
    }

    public function setHost($host)
    {
        $this->hostname = $host;
    }

    public function testRequest($storeid, $deviceid)
    {
        $params = array(
            RK_REQUESTTYPE => REQ_TEST,
            RK_BRAND => urldecode($this->brand),
            RK_STOREID => $storeid,
            RK_DEVICEID => $deviceid,
        );


        $validateRules = array(
            RK_REQUESTTYPE => $this->addValidateRules(array('maxlength' => 10, 'pattern' => PATTERN_ALPHABETIC_ENG)),
            RK_BRAND => $this->addValidateRules(array('maxlength' => 20, 'pattern' => PATTERN_ALPHANUMERIC_ENG)),
            RK_STOREID => $this->addValidateRules(array('maxlength' => 10, 'pattern' => PATTERN_NUMERIC)),
            RK_DEVICEID => $this->addValidateRules(array('maxlength' => 10, 'pattern' => PATTERN_ALPHANUMERIC_ENG_SPECIAL)),
        );

        return $this->makeExecuion($params, $validateRules, 30);
    }

    public function echoRequest($storeid, $deviceid)
    {
        $params = array(
            RK_REQUESTTYPE => REQ_ECHO,
            RK_BRAND => urldecode($this->brand),
            RK_STOREID => $storeid,
            RK_DEVICEID => $deviceid,
        );


        $validateRules = array(
            RK_REQUESTTYPE => $this->addValidateRules(array('maxlength' => 10, 'pattern' => PATTERN_ALPHABETIC_ENG)),
            RK_BRAND => $this->addValidateRules(array('maxlength' => 20, 'pattern' => PATTERN_ALPHANUMERIC_ENG)),
            RK_STOREID => $this->addValidateRules(array('maxlength' => 10, 'pattern' => PATTERN_NUMERIC)),
            RK_DEVICEID => $this->addValidateRules(array('maxlength' => 10, 'pattern' => PATTERN_ALPHANUMERIC_ENG_SPECIAL)),
        );

        return $this->makeExecuion($params, $validateRules, 30);
    }

    public function saleRequest($storeid, $deviceid, $reqid, $reqdt, $custcode, $amt, $curr = 'THB')
    {
        $params = array(
            RK_REQUESTTYPE => REQ_SALE,
            RK_BRAND => urldecode($this->brand),
            RK_STOREID => $storeid,
            RK_DEVICEID => $deviceid,
            RK_REQUESTID => $reqid,
            RK_REQUESTDATETIME => $reqdt,
            RK_CUSTOMERCODE => $custcode,
            RK_AMOUNT => $amt,
            RK_CURRENCY => $curr
        );
		
        $validateRules = array(
            RK_REQUESTTYPE => $this->addValidateRules(array('maxlength' => 10, 'pattern' => PATTERN_ALPHABETIC_ENG)),
            RK_BRAND => $this->addValidateRules(array('maxlength' => 20, 'pattern' => PATTERN_ALPHANUMERIC_ENG)),
            RK_STOREID => $this->addValidateRules(array('maxlength' => 10, 'pattern' => PATTERN_NUMERIC)),
            RK_DEVICEID => $this->addValidateRules(array('maxlength' => 10, 'pattern' => PATTERN_ALPHANUMERIC_ENG_SPECIAL)),
            RK_REQUESTID => $this->addValidateRules(array('maxlength' => 20, 'pattern' => PATTERN_NUMERIC)),
            RK_REQUESTDATETIME => $this->addValidateRules(array('maxlength' => 14, 'pattern' => PATTERN_DATETIME)),
            RK_CUSTOMERCODE => $this->addValidateRules(array('maxlength' => 20, 'pattern' => PATTERN_NUMERIC)),
            RK_AMOUNT => $this->addValidateRules(array('maxlength' => 13, 'pattern' => PATTERN_DECIMALFORMAT)),
            RK_CURRENCY => $this->addValidateRules(array('maxlength' => 3, 'pattern' => PATTERN_ALPHABETIC_ENG_CAPITAL))
        );

        return $this->makeExecuion($params, $validateRules, 30);
    }

    public function cancelRequest($storeid, $deviceid, $origreqid, $origreqdt, $amt)
    {
        $params = array(
            RK_REQUESTTYPE => REQ_CANCEL,
            RK_BRAND => $this->brand,
            RK_STOREID => $storeid,
            RK_DEVICEID => $deviceid,
            RK_ORIGINALREQUESTID => $origreqid,
            RK_ORIGINALREQUESTDATETIME => $origreqdt,
            RK_AMOUNT => $amt,
        );

        $validateRules = array(
            RK_REQUESTTYPE => $this->addValidateRules(array('maxlength' => 10, 'pattern' => PATTERN_ALPHABETIC_ENG)),
            RK_BRAND => $this->addValidateRules(array('maxlength' => 20, 'pattern' => PATTERN_ALPHANUMERIC_ENG)),
            RK_STOREID => $this->addValidateRules(array('maxlength' => 10, 'pattern' => PATTERN_NUMERIC)),
            RK_DEVICEID => $this->addValidateRules(array('maxlength' => 10, 'pattern' => PATTERN_ALPHANUMERIC_ENG_SPECIAL)),
            RK_ORIGINALREQUESTID => $this->addValidateRules(array('maxlength' => 20, 'pattern' => PATTERN_NUMERIC)),
            RK_ORIGINALREQUESTDATETIME => $this->addValidateRules(array('maxlength' => 14, 'pattern' => PATTERN_DATETIME)),
            RK_AMOUNT => $this->addValidateRules(array('maxlength' => 13, 'pattern' => PATTERN_DECIMALFORMAT)),
        );

        return $this->makeExecuion($params, $validateRules, 60);
    }

    public function refundRequest($storeid, $deviceid, $origtransid, $origreqdt, $refundreqid, $refundamt)
    {
        $params = array(
            RK_REQUESTTYPE => REQ_REFUND,
            RK_BRAND => urldecode($this->brand),
            RK_STOREID => $storeid,
            RK_DEVICEID => $deviceid,
            RK_ORIGINALTRANSACTIONID => $origtransid,
            RK_ORIGINALREQUESTDATETIME => $origreqdt,
            RK_REFUNDREQUESTID => $refundreqid,
            RK_REFUNDAMOUNT => $refundamt,
        );
		
        $validateRules = array(
            RK_REQUESTTYPE => $this->addValidateRules(array('maxlength' => 10, 'pattern' => PATTERN_ALPHABETIC_ENG)),
            RK_BRAND => $this->addValidateRules(array('maxlength' => 20, 'pattern' => PATTERN_ALPHANUMERIC_ENG)),
            RK_STOREID => $this->addValidateRules(array('maxlength' => 10, 'pattern' => PATTERN_NUMERIC)),
            RK_DEVICEID => $this->addValidateRules(array('maxlength' => 10, 'pattern' => PATTERN_ALPHANUMERIC_ENG_SPECIAL)),
            RK_ORIGINALTRANSACTIONID => $this->addValidateRules(array('maxlength' => 20, 'pattern' => PATTERN_NUMERIC)),
            RK_ORIGINALREQUESTDATETIME => $this->addValidateRules(array('maxlength' => 14, 'pattern' => PATTERN_DATETIME)),
            RK_REFUNDREQUESTID => $this->addValidateRules(array('maxlength' => 10, 'pattern' => PATTERN_NUMERIC)),
            RK_REFUNDAMOUNT => $this->addValidateRules(array('maxlength' => 13, 'pattern' => PATTERN_DECIMALFORMAT)),
        );


        return $this->makeExecuion($params, $validateRules, 60);
    }

    public function inquiryRequest($storeid, $deviceid, $origreqid, $origreqdt)
    {
        $params = array(
            RK_REQUESTTYPE => REQ_INQUIRY,
            RK_BRAND => urldecode($this->brand),
            RK_STOREID => $storeid,
            RK_DEVICEID => $deviceid,
            RK_ORIGINALREQUESTID => $origreqid,
            RK_ORIGINALREQUESTDATETIME => $origreqdt,
        );

        $validateRules = array(
            RK_REQUESTTYPE => $this->addValidateRules(array('maxlength' => 10, 'pattern' => PATTERN_ALPHABETIC_ENG)),
            RK_BRAND => $this->addValidateRules(array('maxlength' => 20, 'pattern' => PATTERN_ALPHANUMERIC_ENG)),
            RK_STOREID => $this->addValidateRules(array('maxlength' => 10, 'pattern' => PATTERN_NUMERIC)),
            RK_DEVICEID => $this->addValidateRules(array('maxlength' => 10, 'pattern' => PATTERN_ALPHANUMERIC_ENG_SPECIAL)),
            RK_ORIGINALREQUESTID => $this->addValidateRules(array('maxlength' => 20, 'pattern' => PATTERN_NUMERIC)),
            RK_ORIGINALREQUESTDATETIME => $this->addValidateRules(array('maxlength' => 14, 'pattern' => PATTERN_DATETIME)),
        );


        return $this->makeExecuion($params, $validateRules, 60);
    }


    private function addValidateRules($rules)
    {
        return array_merge($this->default_validateRules, $rules);
    }

    private function validate($params, $validateRules)
    {

        foreach ($params as $k => $v) {
            $inputData = array_key_exists($k, $params) ? $params[$k] : null;
            $rule = array_key_exists($k, $validateRules) ? $validateRules[$k] : null;
            $result = $this->isValidate($k, $inputData, $rule);
//            $resultMsg = $result?'[Passed]':'[Failed] caused by => '.$this->resp[RESPONSE_ERRORMESSAGE];
            if (!$result) {
//                echo 'Key ['.$k.'] ';
//                echo 'Input Value = ['.$inputData.'] check => '.$resultMsg;
//                echo '<br/>';
                return false;
            }
        }
        return true;
    }

    private function isValidate($key, $data, $rules)
    {
        if ($rules == null || $rules == '') {
            $this->resp = array(RESPONSE_CODE => '-1', RESPONSE_ERRORMESSAGE => 'Validate Rules for [' . $key . '] is not set propperly');
            return false;
        } else {

            if ($this->keyExist('require', $rules)) {
                if ($rules['require'] && $this->isNull($data)) {
                    $this->setValidateErrorMessage($key, $key . ' is required');
                    return false;
                }
            }

            if ($this->keyExist('maxlength', $rules) && $rules['maxlength'] != DEFAULT_MAXLENGTH) {
                if (strlen($data) > $rules['maxlength']) {
                    $this->setValidateErrorMessage($key, $key . ' maxlength is ' . $rules['maxlength']);
                    return false;
                }
            }


            if ($this->keyExist('pattern', $rules) == 1 && $rules['pattern'] != DEFAULT_PATTERN) {
                if (!@preg_match("/" . $rules['pattern'] . "/", $data) == 1) {
                    $this->setValidateErrorMessage($key, $key . ' invalid pattern');
                    return false;
                }
            }

        }
        return true;
    }

    private function setValidateErrorMessage($key, $message)
    {
        $this->resp = array(RESPONSE_CODE => LOCAL_VALIDATION, RESPONSE_ERRORMESSAGE => $message, RESPONSE_VALIDATEON => $key);
    }

    private function isNull($data)
    {
        return ($data == '' || $data == null || strlen($data) == 0);
    }

    private function keyExist($key, $array)
    {
        if (is_array($array) && array_key_exists($key, $array)) {
            return true;
        }

        return false;
    }

    /**
     * @param $url string a wll-formed url
     * @param int $timout time in second
     * @return string of http get result
     */
    private function urlExecution($params, $timeout = 30)
    {
        $preSign = $this->appendUrl($params);
        $signUrl = $this->encrypt($preSign);

        if ($this->hostname == '') {
            throw new Exception('Host is not set');
        }
        $url = $this->hostname . "$preSign&signtype=$this->signtype&sign=$signUrl";

        $ctx = stream_context_create(array('http' =>
            array(
                'timeout' => $timeout,
            )
        ));

        //echo $url;
        //echo "\r\n";


        @$content = file_get_contents($url, false, $ctx);
        if (@$content === FALSE) {
            $this->resp = array(RESPONSE_CODE => 'GW0012', RESPONSE_ERRORMESSAGE => 'Inquiry timed out');
            return $this->resp;
        } else {
            return json_decode($content, true);
        }
    }

    /**
     * @param $params array of parameters
     * @param $validateRules array of validate
     * @param int $timeout timeout in second
     * @return string
     */
    private function makeExecuion($params, $validateRules, $timeout = 30)
    {
        if (!$this->validate($params, $validateRules)) {
            return $this->resp;
        } else {
            //Pass validate
            return $this->urlExecution($params, $timeout);
        }
    }

    /**
     * @param array $params validated data
     * @return string text of serialized array
     */
    private function appendUrl($params = array())
    {
        $presigned = "";
        ksort($params);
        foreach ($params as $key => $val) {
            if ($val === "" || $val == null || strlen(trim($val)) == 0) {
                continue;
            }
            $presigned .= "&$key=" . urlencode($val);
        }
        $presigned = substr($presigned, 1);
        return $presigned;
    }

    /**
     * @param $prependUrl string serialized text of validated data
     * @return string append signkey for encryption propose
     */
    private function appendSignKey($prependUrl)
    {
        $prependUrl .= '&signkey=' . $this->signkey;
        return $prependUrl;
    }

    /**
     * @param $sortedUrl string serialized string of sorted params
     * @return string encrypted text of defined algorithm
     */
    private function encrypt($sortedUrl)
    {
//        echo 'Before Append Key => '.$sortedUrl.'<br/>';
        $sortedUrl = $this->appendSignKey($sortedUrl);
//        echo 'After Append Key => '.$sortedUrl.'<br/>';
        if ($this->signtype == SHA256) {
            return hash('sha256', $sortedUrl);
        } else if ($this->signtype == SHA512) {
            return hash('sha512', $sortedUrl);
        } else {
            die('This hash algorithm is not allowed');
        }
    }


    public function getLibraryVersion()
    {
        return LIBVER;
    }
	
}

?>