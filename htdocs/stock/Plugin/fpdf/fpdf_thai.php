<?php
/*******************************************************************************
* Software: FPDF Thai Positioning Improve                                      *
* Version:  1.0                                                                *
* Date:     2005-04-30                                                         *
* Advisor:  Mr. Wittawas Puntumchinda                                          *
* Coding:   Mr. Sirichai Fuangfoo                                              *
* License:  Freeware                                                           *
*                                                                              *
* You may use, modify and redistribute this software as you wish.              *
*******************************************************************************/

require('fpdf.php');

class FPDF_Thai extends FPDF
{
var $txt_error;	
var $s_error;
var $string_th;
var $s_th;
var $pointX;
var $pointY;
var $curPointX;
var $checkFill;
var $array_th;

/****************************************************************************************
* ประเภท: Function ของ Class FPDF_TH													
* อ้างอิง: Function MultiCell ของ Class FPDF											
* การทำงาน: ใช้ในการพิมพ์ข้อความหลายบรรทัดของเอกสาร PDF 										
* รูบแบบ: MultiCell (	$w = ความกว้างของCell,												
*						$h = ความสูงของCell,												
*						$txt = ข้อความที่จะพิมพ์,													
*						$border = กำหนดการแสดงเส้นกรอบ(0 = ไม่แสดง, 1= แสดง)	,				
*						$align = ตำแหน่งข้อความ(L = ซ้าย, R = ขวา, C = กึ่งกลาง, J = กระจาย),
*						$fill = กำหนดการแสดงสีของCell(0 = ไม่แสดง, 1 = แสดง)					
*					)			
*****************************************************************************************/
function MultiCell($w,$h,$txt,$border=0,$align='J',$fill=0)
{
	//Output text with automatic or explicit line breaks
	$cw=&$this->CurrentFont['cw'];
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	$s=str_replace("\r",'',$txt);
	$nb=strlen($s);
	if($nb>0 && $s[$nb-1]=="\n")
		$nb--;
	$b=0;
	if($border)
	{
		if($border==1)
		{
			$border='LTRB';
			$b='LRT';
			$b2='LR';
		}
		else
		{
			$b2='';
			if(strpos($border,'L')!==false)
				$b2.='L';
			if(strpos($border,'R')!==false)
				$b2.='R';
			$b=(strpos($border,'T')!==false) ? $b2.'T' : $b2;
		}
	}
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$ns=0;
	$nl=1;
	while($i<$nb)
	{
		//Get next character
		$c=$s{$i};
		if($c=="\n")
		{
			//Explicit line break
			if($this->ws>0)
			{
				$this->ws=0;
				$this->_out('0 Tw');
			}
			$this->MCell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			$ns=0;
			$nl++;
			if($border && $nl==2)
				$b=$b2;
			continue;
		}
		if($c==' ')
		{
			$sep=$i;
			$ls=$l;
			$ns++;
		}
		$l+=$cw[$c];
		if($l>$wmax)
		{
			//Automatic line break
			if($sep==-1)
			{
				if($i==$j)
					$i++;
				if($this->ws>0)
				{
					$this->ws=0;
					$this->_out('0 Tw');
				}
				$this->MCell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
			}
			else
			{
				if($align=='J')
				{
					$this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
					$this->_out(sprintf('%.3f Tw',$this->ws*$this->k));
				}
				$this->MCell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
				$i=$sep+1;
			}
			$sep=-1;
			$j=$i;
			$l=0;
			$ns=0;
			$nl++;
			if($border && $nl==2)
				$b=$b2;
		}
		else
			$i++;
	}
	//Last chunk
	if($this->ws>0)
	{
		$this->ws=0;
		$this->_out('0 Tw');
	}
	if($border && strpos($border,'B')!==false)
		$b.='B';
	$this->MCell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
	$this->x=$this->lMargin;
}








/*
function HTML2RGB($c, &$r, &$g, &$b)
{
    static $colors = array('black'=>'#000000','silver'=>'#C0C0C0','gray'=>'#808080','white'=>'#FFFFFF',
                        'maroon'=>'#800000','red'=>'#FF0000','purple'=>'#800080','fuchsia'=>'#FF00FF',
                        'green'=>'#008000','lime'=>'#00FF00','olive'=>'#808000','yellow'=>'#FFFF00',
                        'navy'=>'#000080','blue'=>'#0000FF','teal'=>'#008080','aqua'=>'#00FFFF');

    $c=strtolower($c);
    if(isset($colors[$c]))
        $c=$colors[$c];
    if($c[0]!='#')
        $this->Error('Incorrect color: '.$c);
    $r=hexdec(substr($c,1,2));
    $g=hexdec(substr($c,3,2));
    $b=hexdec(substr($c,5,2));
}

function SetDrawColor($r, $g=-1, $b=-1)
{
    if(is_string($r))
        $this->HTML2RGB($r,$r,$g,$b);
    parent::SetDrawColor($r,$g,$b);
}

function SetFillColor($r, $g=-1, $b=-1)
{
    if(is_string($r))
        $this->HTML2RGB($r,$r,$g,$b);
    parent::SetFillColor($r,$g,$b);
}

function SetTextColor($r,$g=-1,$b=-1)
{
    if(is_string($r))
        $this->HTML2RGB($r,$r,$g,$b);
    parent::SetTextColor($r,$g,$b);
}



*/













/*

//---------------------------------------------------------------------------------------------------
function Code39($x, $y, $code, $ext = true, $cks = false, $w = 0.4, $h = 20, $wide = true) {

    //Display code
    $this->SetFont('Arial', '', 10);
    $this->Text($x, $y+$h+4, $code);

    if($ext) {
        //Extended encoding
        $code = $this->encode_code39_ext($code);
    }
    else {
        //Convert to upper case
        $code = strtoupper($code);
        //Check validity
        if(!preg_match('|^[0-9A-Z. $/+%-]*$|', $code))
            $this->Error('Invalid barcode value: '.$code);
    }

    //Compute checksum
    if ($cks)
        $code .= $this->checksum_code39($code);

    //Add start and stop characters
    $code = '*'.$code.'*';

    //Conversion tables
    $narrow_encoding = array (
        '0' => '101001101101', '1' => '110100101011', '2' => '101100101011',
        '3' => '110110010101', '4' => '101001101011', '5' => '110100110101',
        '6' => '101100110101', '7' => '101001011011', '8' => '110100101101',
        '9' => '101100101101', 'A' => '110101001011', 'B' => '101101001011',
        'C' => '110110100101', 'D' => '101011001011', 'E' => '110101100101',
        'F' => '101101100101', 'G' => '101010011011', 'H' => '110101001101',
        'I' => '101101001101', 'J' => '101011001101', 'K' => '110101010011',
        'L' => '101101010011', 'M' => '110110101001', 'N' => '101011010011',
        'O' => '110101101001', 'P' => '101101101001', 'Q' => '101010110011',
        'R' => '110101011001', 'S' => '101101011001', 'T' => '101011011001',
        'U' => '110010101011', 'V' => '100110101011', 'W' => '110011010101',
        'X' => '100101101011', 'Y' => '110010110101', 'Z' => '100110110101',
        '-' => '100101011011', '.' => '110010101101', ' ' => '100110101101',
        '*' => '100101101101', '$' => '100100100101', '/' => '100100101001',
        '+' => '100101001001', '%' => '101001001001' );

    $wide_encoding = array (
        '0' => '101000111011101', '1' => '111010001010111', '2' => '101110001010111',
        '3' => '111011100010101', '4' => '101000111010111', '5' => '111010001110101',
        '6' => '101110001110101', '7' => '101000101110111', '8' => '111010001011101',
        '9' => '101110001011101', 'A' => '111010100010111', 'B' => '101110100010111',
        'C' => '111011101000101', 'D' => '101011100010111', 'E' => '111010111000101',
        'F' => '101110111000101', 'G' => '101010001110111', 'H' => '111010100011101',
        'I' => '101110100011101', 'J' => '101011100011101', 'K' => '111010101000111',
        'L' => '101110101000111', 'M' => '111011101010001', 'N' => '101011101000111',
        'O' => '111010111010001', 'P' => '101110111010001', 'Q' => '101010111000111',
        'R' => '111010101110001', 'S' => '101110101110001', 'T' => '101011101110001',
        'U' => '111000101010111', 'V' => '100011101010111', 'W' => '111000111010101',
        'X' => '100010111010111', 'Y' => '111000101110101', 'Z' => '100011101110101',
        '-' => '100010101110111', '.' => '111000101011101', ' ' => '100011101011101',
        '*' => '100010111011101', '$' => '100010001000101', '/' => '100010001010001',
        '+' => '100010100010001', '%' => '101000100010001');

    $encoding = $wide ? $wide_encoding : $narrow_encoding;

    //Inter-character spacing
    $gap = ($w > 0.29) ? '00' : '0';

    //Convert to bars
    $encode = '';
    for ($i = 0; $i< strlen($code); $i++)
        $encode .= $encoding[$code[$i]].$gap;

    //Draw bars
    $this->draw_code39($encode, $x, $y, $w, $h);
}

function checksum_code39($code) {

    //Compute the modulo 43 checksum

    $chars = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
                            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
                            'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
                            'W', 'X', 'Y', 'Z', '-', '.', ' ', '$', '/', '+', '%');
    $sum = 0;
    for ($i=0 ; $i<strlen($code); $i++) {
        $a = array_keys($chars, $code[$i]);
        $sum += $a[0];
    }
    $r = $sum % 43;
    return $chars[$r];
}

function encode_code39_ext($code) {

    //Encode characters in extended mode

    $encode = array(
        chr(0) => '%U', chr(1) => '$A', chr(2) => '$B', chr(3) => '$C',
        chr(4) => '$D', chr(5) => '$E', chr(6) => '$F', chr(7) => '$G',
        chr(8) => '$H', chr(9) => '$I', chr(10) => '$J', chr(11) => '?K',
        chr(12) => '$L', chr(13) => '$M', chr(14) => '$N', chr(15) => '$O',
        chr(16) => '$P', chr(17) => '$Q', chr(18) => '$R', chr(19) => '$S',
        chr(20) => '$T', chr(21) => '$U', chr(22) => '$V', chr(23) => '$W',
        chr(24) => '$X', chr(25) => '$Y', chr(26) => '$Z', chr(27) => '%A',
        chr(28) => '%B', chr(29) => '%C', chr(30) => '%D', chr(31) => '%E',
        chr(32) => ' ', chr(33) => '/A', chr(34) => '/B', chr(35) => '/C',
        chr(36) => '/D', chr(37) => '/E', chr(38) => '/F', chr(39) => '/G',
        chr(40) => '/H', chr(41) => '/I', chr(42) => '/J', chr(43) => '/K',
        chr(44) => '/L', chr(45) => '-', chr(46) => '.', chr(47) => '/O',
        chr(48) => '0', chr(49) => '1', chr(50) => '2', chr(51) => '3',
        chr(52) => '4', chr(53) => '5', chr(54) => '6', chr(55) => '7',
        chr(56) => '8', chr(57) => '9', chr(58) => '/Z', chr(59) => '%F',
        chr(60) => '%G', chr(61) => '%H', chr(62) => '%I', chr(63) => '%J',
        chr(64) => '%V', chr(65) => 'A', chr(66) => 'B', chr(67) => 'C',
        chr(68) => 'D', chr(69) => 'E', chr(70) => 'F', chr(71) => 'G',
        chr(72) => 'H', chr(73) => 'I', chr(74) => 'J', chr(75) => 'K',
        chr(76) => 'L', chr(77) => 'M', chr(78) => 'N', chr(79) => 'O',
        chr(80) => 'P', chr(81) => 'Q', chr(82) => 'R', chr(83) => 'S',
        chr(84) => 'T', chr(85) => 'U', chr(86) => 'V', chr(87) => 'W',
        chr(88) => 'X', chr(89) => 'Y', chr(90) => 'Z', chr(91) => '%K',
        chr(92) => '%L', chr(93) => '%M', chr(94) => '%N', chr(95) => '%O',
        chr(96) => '%W', chr(97) => '+A', chr(98) => '+B', chr(99) => '+C',
        chr(100) => '+D', chr(101) => '+E', chr(102) => '+F', chr(103) => '+G',
        chr(104) => '+H', chr(105) => '+I', chr(106) => '+J', chr(107) => '+K',
        chr(108) => '+L', chr(109) => '+M', chr(110) => '+N', chr(111) => '+O',
        chr(112) => '+P', chr(113) => '+Q', chr(114) => '+R', chr(115) => '+S',
        chr(116) => '+T', chr(117) => '+U', chr(118) => '+V', chr(119) => '+W',
        chr(120) => '+X', chr(121) => '+Y', chr(122) => '+Z', chr(123) => '%P',
        chr(124) => '%Q', chr(125) => '%R', chr(126) => '%S', chr(127) => '%T');

    $code_ext = '';
    for ($i = 0 ; $i<strlen($code); $i++) {
        if (ord($code[$i]) > 127)
            $this->Error('Invalid character: '.$code[$i]);
        $code_ext .= $encode[$code[$i]];
    }
    return $code_ext;
}

function draw_code39($code, $x, $y, $w, $h) {

    //Draw bars

    for($i=0; $i<strlen($code); $i++) {
        if($code[$i] == '1')
            $this->Rect($x+$i*$w, $y, $w, $h, 'F');
    }
}
*/

//---------------------------------------------------------------------------------------------------
function Code39($xpos, $ypos, $code, $baseline=0.5, $height=5){
    $wide = $baseline;
    $narrow = $baseline/3 ; 
    $gap = $narrow;
    $barChar['0'] = 'nnnwwnwnn';
    $barChar['1'] = 'wnnwnnnnw';
    $barChar['2'] = 'nnwwnnnnw';
    $barChar['3'] = 'wnwwnnnnn';
    $barChar['4'] = 'nnnwwnnnw';
    $barChar['5'] = 'wnnwwnnnn';
    $barChar['6'] = 'nnwwwnnnn';
    $barChar['7'] = 'nnnwnnwnw';
    $barChar['8'] = 'wnnwnnwnn';
    $barChar['9'] = 'nnwwnnwnn';
    $barChar['A'] = 'wnnnnwnnw';
    $barChar['B'] = 'nnwnnwnnw';
    $barChar['C'] = 'wnwnnwnnn';
    $barChar['D'] = 'nnnnwwnnw';
    $barChar['E'] = 'wnnnwwnnn';
    $barChar['F'] = 'nnwnwwnnn';
    $barChar['G'] = 'nnnnnwwnw';
    $barChar['H'] = 'wnnnnwwnn';
    $barChar['I'] = 'nnwnnwwnn';
    $barChar['J'] = 'nnnnwwwnn';
    $barChar['K'] = 'wnnnnnnww';
    $barChar['L'] = 'nnwnnnnww';
    $barChar['M'] = 'wnwnnnnwn';
    $barChar['N'] = 'nnnnwnnww';
    $barChar['O'] = 'wnnnwnnwn'; 
    $barChar['P'] = 'nnwnwnnwn';
    $barChar['Q'] = 'nnnnnnwww';
    $barChar['R'] = 'wnnnnnwwn';
    $barChar['S'] = 'nnwnnnwwn';
    $barChar['T'] = 'nnnnwnwwn';
    $barChar['U'] = 'wwnnnnnnw';
    $barChar['V'] = 'nwwnnnnnw';
    $barChar['W'] = 'wwwnnnnnn';
    $barChar['X'] = 'nwnnwnnnw';
    $barChar['Y'] = 'wwnnwnnnn';
    $barChar['Z'] = 'nwwnwnnnn';
    $barChar['-'] = 'nwnnnnwnw';
    $barChar['.'] = 'wwnnnnwnn';
    $barChar[' '] = 'nwwnnnwnn';
    $barChar['*'] = 'nwnnwnwnn';
    $barChar['$'] = 'nwnwnwnnn';
    $barChar['/'] = 'nwnwnnnwn';
    $barChar['+'] = 'nwnnnwnwn';
    $barChar['%'] = 'nnnwnwnwn';

	$this->SetFont('Arial','',10);
    $this->Text($xpos, $ypos + $height + 4, $code);
    $this->SetFillColor(1);

    $code = '*'.strtoupper($code).'*';
    for($i=0; $i<strlen($code); $i++){
        $char = $code[$i];
        if(!isset($barChar[$char])){
            $this->Error('Invalid character in barcode: '.$char);
        }
        $seq = $barChar[$char];
        for($bar=0; $bar<9; $bar++){
            if($seq[$bar] == 'n'){
                $lineWidth = $narrow;
            }else{
                $lineWidth = $wide;
            }
            if($bar % 2 == 0){
                $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
            }
            $xpos += $lineWidth;
        }
        $xpos += $gap;
    }
}

//---------------------------------------------------------------------------------------------------
function Codabar($xpos, $ypos, $code, $start='A', $end='A', $basewidth=0.35, $height=10) {
    $barChar = array (
        '0' => array (6.5, 10.4, 6.5, 10.4, 6.5, 24.3, 17.9),
        '1' => array (6.5, 10.4, 6.5, 10.4, 17.9, 24.3, 6.5),
        '2' => array (6.5, 10.0, 6.5, 24.4, 6.5, 10.0, 18.6),
        '3' => array (17.9, 24.3, 6.5, 10.4, 6.5, 10.4, 6.5),
        '4' => array (6.5, 10.4, 17.9, 10.4, 6.5, 24.3, 6.5),
        '5' => array (17.9,    10.4, 6.5, 10.4, 6.5, 24.3, 6.5),
        '6' => array (6.5, 24.3, 6.5, 10.4, 6.5, 10.4, 17.9),
        '7' => array (6.5, 24.3, 6.5, 10.4, 17.9, 10.4, 6.5),
        '8' => array (6.5, 24.3, 17.9, 10.4, 6.5, 10.4, 6.5),
        '9' => array (18.6, 10.0, 6.5, 24.4, 6.5, 10.0, 6.5),
        '$' => array (6.5, 10.0, 18.6, 24.4, 6.5, 10.0, 6.5),
        '-' => array (6.5, 10.0, 6.5, 24.4, 18.6, 10.0, 6.5),
        ':' => array (16.7, 9.3, 6.5, 9.3, 16.7, 9.3, 14.7),
        '/' => array (14.7, 9.3, 16.7, 9.3, 6.5, 9.3, 16.7),
        '.' => array (13.6, 10.1, 14.9, 10.1, 17.2, 10.1, 6.5),
        '+' => array (6.5, 10.1, 17.2, 10.1, 14.9, 10.1, 13.6),
        'A' => array (6.5, 8.0, 19.6, 19.4, 6.5, 16.1, 6.5),
        'T' => array (6.5, 8.0, 19.6, 19.4, 6.5, 16.1, 6.5),
        'B' => array (6.5, 16.1, 6.5, 19.4, 6.5, 8.0, 19.6),
        'N' => array (6.5, 16.1, 6.5, 19.4, 6.5, 8.0, 19.6),
        'C' => array (6.5, 8.0, 6.5, 19.4, 6.5, 16.1, 19.6),
        '*' => array (6.5, 8.0, 6.5, 19.4, 6.5, 16.1, 19.6),
        'D' => array (6.5, 8.0, 6.5, 19.4, 19.6, 16.1, 6.5),
        'E' => array (6.5, 8.0, 6.5, 19.4, 19.6, 16.1, 6.5),
    );
    $this->SetFont('Arial','',13);
    $this->Text($xpos, $ypos + $height + 4, $code);
    $this->SetFillColor(0);
    $code = strtoupper($start.$code.$end);
    for($i=0; $i<strlen($code); $i++){
        $char = $code[$i];
        if(!isset($barChar[$char])){
            $this->Error('Invalid character in barcode: '.$char);
        }
        $seq = $barChar[$char];
        for($bar=0; $bar<7; $bar++){
            $lineWidth = $basewidth*$seq[$bar]/6.5;
            if($bar % 2 == 0){
                $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
            }
            $xpos += $lineWidth;
        }
        $xpos += $basewidth*10.4/6.5;
    }
}


//---------------------------------------------------------------------------------------------------
//function EAN13($x, $y, $barcode, $h=16, $w=.35)
function EAN13($x, $y, $barcode, $h=16, $w=.45)

{
    $this->Barcode($x,$y,$barcode,$h,$w,13);
}

function UPC_A($x, $y, $barcode, $h=16, $w=.35)
{
    $this->Barcode($x,$y,$barcode,$h,$w,12);
}

function GetCheckDigit($barcode)
{
    //Compute the check digit
    $sum=0;
    for($i=1;$i<=11;$i+=2)
        $sum+=3*$barcode[$i];
    for($i=0;$i<=10;$i+=2)
        $sum+=$barcode[$i];
    $r=$sum%10;
    if($r>0)
        $r=10-$r;
    return $r;
}

function TestCheckDigit($barcode)
{
    //Test validity of check digit
    $sum=0;
    for($i=1;$i<=11;$i+=2)
        $sum+=3*$barcode[$i];
    for($i=0;$i<=10;$i+=2)
        $sum+=$barcode[$i];
    return ($sum+$barcode[12])%10==0;
}

function Barcode($x, $y, $barcode, $h, $w, $len)
{
    //Padding
    $barcode=str_pad($barcode,$len-1,'0',STR_PAD_LEFT);
    if($len==12)
        $barcode='0'.$barcode;
    //Add or control the check digit
    if(strlen($barcode)==12)
        $barcode.=$this->GetCheckDigit($barcode);
    elseif(!$this->TestCheckDigit($barcode))
        $this->Error('Incorrect check digit');
    //Convert digits to bars
    $codes=array(
        'A'=>array(
            '0'=>'0001101','1'=>'0011001','2'=>'0010011','3'=>'0111101','4'=>'0100011',
            '5'=>'0110001','6'=>'0101111','7'=>'0111011','8'=>'0110111','9'=>'0001011'),
        'B'=>array(
            '0'=>'0100111','1'=>'0110011','2'=>'0011011','3'=>'0100001','4'=>'0011101',
            '5'=>'0111001','6'=>'0000101','7'=>'0010001','8'=>'0001001','9'=>'0010111'),
        'C'=>array(
            '0'=>'1110010','1'=>'1100110','2'=>'1101100','3'=>'1000010','4'=>'1011100',
            '5'=>'1001110','6'=>'1010000','7'=>'1000100','8'=>'1001000','9'=>'1110100')
        );
    $parities=array(
        '0'=>array('A','A','A','A','A','A'),
        '1'=>array('A','A','B','A','B','B'),
        '2'=>array('A','A','B','B','A','B'),
        '3'=>array('A','A','B','B','B','A'),
        '4'=>array('A','B','A','A','B','B'),
        '5'=>array('A','B','B','A','A','B'),
        '6'=>array('A','B','B','B','A','A'),
        '7'=>array('A','B','A','B','A','B'),
        '8'=>array('A','B','A','B','B','A'),
        '9'=>array('A','B','B','A','B','A')
        );
    $code='101';
    $p=$parities[$barcode[0]];
    for($i=1;$i<=6;$i++)
        $code.=$codes[$p[$i-1]][$barcode[$i]];
    $code.='01010';
    for($i=7;$i<=12;$i++)
        $code.=$codes['C'][$barcode[$i]];
    $code.='101';
    //Draw bars
    for($i=0;$i<strlen($code);$i++)
    {
        if($code[$i]=='1')
            $this->Rect($x+$i*$w,$y,$w,$h,'F');
    }
    //Print text uder barcode
    $this->SetFont('Arial','',12);
    $this->Text($x,$y+$h+11/$this->k,substr($barcode,-$len));
}
//---------------------------------------------------------------------------------------------------
function i25($xpos, $ypos, $code, $basewidth=1, $height=10){

    $wide = $basewidth;
    $narrow = $basewidth / 3 ;

    // wide/narrow codes for the digits
    $barChar['0'] = 'nnwwn';
    $barChar['1'] = 'wnnnw';
    $barChar['2'] = 'nwnnw';
    $barChar['3'] = 'wwnnn';
    $barChar['4'] = 'nnwnw';
    $barChar['5'] = 'wnwnn';
    $barChar['6'] = 'nwwnn';
    $barChar['7'] = 'nnnww';
    $barChar['8'] = 'wnnwn';
    $barChar['9'] = 'nwnwn';
    $barChar['A'] = 'nn';
    $barChar['Z'] = 'wn';

    // add leading zero if code-length is odd
    if(strlen($code) % 2 != 0){
        $code = '0' . $code;
    }

    $this->SetFont('Arial','',10);
    $this->Text($xpos, $ypos + $height + 4, $code);
    $this->SetFillColor(0);

    // add start and stop codes
    $code = 'AA'.strtolower($code).'ZA';

    for($i=0; $i<strlen($code); $i=$i+2){
        // choose next pair of digits
        $charBar = $code[$i];
        $charSpace = $code[$i+1];
        // check whether it is a valid digit
        if(!isset($barChar[$charBar])){
            $this->Error('Invalid character in barcode: '.$charBar);
        }
        if(!isset($barChar[$charSpace])){
            $this->Error('Invalid character in barcode: '.$charSpace);
        }
        // create a wide/narrow-sequence (first digit=bars, second digit=spaces)
        $seq = '';
        for($s=0; $s<strlen($barChar[$charBar]); $s++){
            $seq .= $barChar[$charBar][$s] . $barChar[$charSpace][$s];
        }
        for($bar=0; $bar<strlen($seq); $bar++){
            // set lineWidth depending on value
            if($seq[$bar] == 'n'){
                $lineWidth = $narrow;
            }else{
                $lineWidth = $wide;
            }
            // draw every second value, because the second digit of the pair is represented by the spaces
            if($bar % 2 == 0){
                $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
            }
            $xpos += $lineWidth;
        }
    }
}

//---------------------------------------------------------------------------------------------------
function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
{
	$this->checkFill="";
	$k=$this->k;
	if($this->y+$h>$this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak())
	{
		//ขึ้นหน้าใหม่อัตโนมัต
		$x=$this->x;
		$ws=$this->ws;
		if($ws>0)
		{
			$this->ws=0;
			$this->_out('0 Tw');
		}
		$this->AddPage($this->CurOrientation);
		$this->x=$x;
		if($ws>0)
		{
			$this->ws=$ws;
			$this->_out(sprintf('%.3f Tw',$ws*$k));
		}
	}
	//กำหนดความกว้างเซลล์เท่ากับหน้ากระดาษ
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$this->s_th='';
	//กำหนดการแสดงเส้นกรอบ 4 ด้าน และสีกรอบ
	if($fill==1 || $border==1)
	{
		if($fill==1)
			$op=($border==1) ? 'B' : 'f';
		else
			$op='S';
		$this->s_th=sprintf('%.2f %.2f %.2f %.2f re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
		if($op=='f')
			$this->checkFill=$op;
	}
	//กำหนดการแสดงเส้นกรอบทีละเส้น
	if(is_string($border))
	{
		$x=$this->x;
		$y=$this->y;
		if(strpos($border,'L')!==false)
			$this->s_th.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
		if(strpos($border,'T')!==false)
			$this->s_th.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
		if(strpos($border,'R')!==false)
			$this->s_th.=sprintf('%.2f %.2f m %.2f %.2f l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		if(strpos($border,'B')!==false)
			$this->s_th.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
	}


	if($txt!=='')
	{			
		$x=$this->x;
		$y=$this->y;
		//กำหนดการจัดข้อความในเซลล์ตามแนวระดับ
		if(strpos($align,'R')!==false)
			$dx=$w-$this->cMargin-$this->GetStringWidth($txt);
		elseif(strpos($align,'C')!==false)
			$dx=($w-$this->GetStringWidth($txt))/2;
		else
			$dx=$this->cMargin;
		//กำหนดการจัดข้อความในเซลล์ตามแนวดิ่ง
		if(strpos($align,'T')!==false)
			$dy=$h-(.7*$this->k*$this->FontSize);
		elseif(strpos($align,'B')!==false)
			$dy=$h-(.3*$this->k*$this->FontSize);
		else
			$dy=.5*$h;
		//กำหนดการขีดเส้นใต้ข้อความ
		if($this->underline)
		{	
			//กำหนดบันทึกกราฟิก
			if($this->ColorFlag)
				$this->s_th.=' q '.$this->TextColor.' ';
			//ขีดเส้นใต้ข้อความ0
			$this->s_th.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
			//กำหนดคืนค่ากราฟิก
			if($this->ColorFlag)
				$this->s_th.=' Q ';
		}
		//กำหนดข้อความเชื่อมโยงไปถึง
		if($link)
			$this->Link($this->x,$this->y,$this->GetStringWidth($txt),$this->FontSize,$link);
		/*if($s)
			$this->_out($s);
		$s='';*/
		//ตัดอักษรออกจากข้อความ ทีละตัวเก็บลงอะเรย์
		$this->array_th=substr($txt,0);
		$i=0;
		$this->pointY=($this->h-($y+$dy+.3*$this->FontSize))*$k;
		$this->curPointX=($x+$dx)*$k;
		$this->string_th='';
		$this->txt_error=0;

		while($i<=strlen($txt))
		{	
			//กำหนดตำแหน่งที่จะพิมพ์อักษรในเซลล์
			$this->pointX=($x+$dx+.02*$this->GetStringWidth($this->array_th[$i-1]))*$k;
			if(($this->array_th[$i]=='่')||($this->array_th[$i]=='้')||($this->array_th[$i]=='๊')||($this->array_th[$i]=='๋')||($this->array_th[$i]=='์')||($this->array_th[$i]=='ิ')||($this->array_th[$i]=='ี')||($this->array_th[$i]=='ึ')||($this->array_th[$i]=='ื')||($this->array_th[$i]=='็')||($this->array_th[$i]=='ั')||($this->array_th[$i]=='ำ')||($this->array_th[$i]=='ุ')||($this->array_th[$i]=='ู'))
			{
				//ตรวจสอบอักษร ปรับตำแหน่งและทำการพิมพ์
				$this->_checkT($i);

				if($this->txt_error==0)
					$this->string_th.=$this->array_th[$i];
				else
				{
					$this->txt_error=0;
				}
			}
			else
				$this->string_th.=$this->array_th[$i];

			//เลื่อนตำแหน่ง x ไปที่ตัวที่จะพิมพ์ถัดไป
			$x=$x+$this->GetStringWidth($this->array_th[$i]);
			$i++;
		}
		$this->TText($this->curPointX,$this->pointY,$this->string_th);
		/*$this->s_th.=$this->s_hidden.$this->s_error;*/
		//$this->s_th.=$this->s_error;
		if($this->s_th)
			$this->_out($this->s_th);
	}
	else
		//นำค่าไปแสดงเมื่อไม่มีข้อความ
		$this->_out($this->s_th);

	$this->lasth=$h;
	//ตรวจสอบการวางตำแหน่งของเซลล์ถัดไป
	if($ln>0)
	{
		//ขึ้นบรรทัดใหม่
		$this->y+=$h;
		if($ln==1)
			$this->x=$this->lMargin;
	}
	else
		$this->x+=$w;
}

/********************************************************************************
* ใช้งาน: Function	Cell ของ Class FPDF_TH										
* การทำงาน: ใช้ในการตรวจสอบอักษร และปรับตำแหน่งก่อนที่จะทำการพิมพ์							
* ความต้องการ: $this->array_th = อะเรย์ของอักษรที่ตัดออกจากข้อความ						
*						$i = ลำดับปัจจุบันในอะเรย์ที่จะทำการตรวจสอบ						
*						$s = สายอักขระของโคด PDF
*********************************************************************************/
function _checkT($i)
{   
	$pointY=$this->pointY;
	$pointX=$this->pointX;
	//ตวจสอบการแสดงผลของตัวอักษรเหนือสระบน
	if($this->_errorTh($this->array_th[$i])==1)
	{
		//ตรวจสอบตัวอักษรก่อนหน้านั้นไม่ใช่สระบน ปรับตำแหน่งลง	
		if(($this->_errorTh($this->array_th[$i-1])!=2)&&($this->array_th[$i+1]!="ำ"))
		{
			//ถ้าตัวนั้นเป็นไม้เอกหรือไม้จัตวา
			if($this->array_th[$i]=="่"||$this->array_th[$i]=="๋")
			{
				$pointY=$this->pointY-.2*$this->FontSize*$this->k;
				$this->txt_error=1;
			}
			//ถ้าตัวนั้นเป็นไม้โทหรือไม้ตรี
			elseif($this->array_th[$i]=='้'||$this->array_th[$i]=='๊')
			{
				$pointY=$this->pointY-.23*$this->FontSize*$this->k;
				$this->txt_error=1;
			}
			//ถ้าตัวนั้นเป็นการันต์
			else
			{
				$pointY=$this->pointY-.17*$this->FontSize*$this->k;
				$this->txt_error=1;
			}
		}
			
		//ตรวจสอบตัวอักษรตัวก่อนหน้านั้นเป็นตัวอักษรหางยาวบน
		if($this->_errorTh($this->array_th[$i-1])==3)		
		{
			//ถ้าตัวนั้นเป็นไม้เอกหรือไม้จัตวา
			if($this->array_th[$i]=="่"||$this->array_th[$i]=="๋")
			{
				$pointX=$this->pointX-.17*$this->GetStringWidth($this->array_th[$i-1])*$this->k;
				$this->txt_error=1;
			}
			//ถ้าตัวนั้นเป็นไม้โทหรือไม้ตรี
			elseif($this->array_th[$i]=='้'||$this->array_th[$i]=='๊')
			{			
				$pointX=$this->pointX-.25*$this->GetStringWidth($this->array_th[$i-1])*$this->k;
				$this->txt_error=1;
			}
			//ถ้าตัวนั้นเป็นการันต์
			else
			{
				$pointX=$this->pointX-.4*$this->GetStringWidth($this->array_th[$i-1])*$this->k;
				$this->txt_error=1;
			}
		}

		//ตรวจสอบตัวอักษรตัวก่อนหน้านั้นไปอีกเป็นตัวอักษรหางยาวบน	
		if($this->_errorTh($this->array_th[$i-2])==3)	
		{					
			//ถ้าตัวนั้นเป็นไม้เอกหรือไม้จัตวา
			if($this->array_th[$i]=="่"||$this->array_th[$i]=="๋")
			{
				$pointX=$this->pointX-.17*$this->GetStringWidth($this->array_th[$i-2])*$this->k;
				$this->txt_error=1;
			}
			//ถ้าตัวนั้นเป็นไม้โทหรือไม้ตรี
			elseif($this->array_th[$i]=='้'||$this->array_th[$i]=='๊')
			{						
				$pointX=$this->pointX-.25*$this->GetStringWidth($this->array_th[$i-2])*$this->k;
				$this->txt_error=1;
			}
			//ถ้าตัวนั้นเป็นการันต์
			else
			{
				$pointX=$this->pointX-.4*$this->GetStringWidth($this->array_th[$i-2])*$this->k;						
				$this->txt_error=1;
			}
		}
	}
	//จบการตรวจสอบตัวอักษรเหนือสระบน

	//ตวจสอบการแสดงผลของตัวอักษรสระบน
	elseif($this->_errorTh($this->array_th[$i])==2)
	{
		//ตรวจสอบตัวอักษรตัวก่อนหน้านั้นเป็นตัวอักษรหางยาวบน
		if($this->_errorTh($this->array_th[$i-1])==3)	
		{
			$pointX=$this->pointX-.17*$this->GetStringWidth($this->array_th[$i-1])*$this->k;
			$this->txt_error=1;
		}
		//ถ้าตัวนั้นเป็นสระอำ
		if($this->array_th[$i]=="ำ")
			//ตรวจสอบตัวอักษรตัวก่อนหน้านั้นเป็นตัวอักษรหางยาวบน
			if($this->_errorTh($this->array_th[$i-2])==3)	
			{
				$pointX=$this->pointX-.17*$this->GetStringWidth($this->array_th[$i-2])*$this->k;
				$this->txt_error=1;
			}
	}																						
	//จบการตรวจสอบตัวอักษรสระบน

	//ตวจสอบการแสดงผลของตัวอักษรสระล่าง
	elseif($this->_errorTh($this->array_th[$i])==6)
	{
		//ตรวจสอบตัวอักษรตัวก่อนหน้านั้นเป็นตัวอักษร ญ. กับ ฐ.
		if($this->_errorTh($this->array_th[$i-1])==5)						
		{	//$this->string_th		$this->curPointX
			$this->TText($this->curPointX,$this->pointY,$this->string_th);
			$this->string_th='';
			$this->curPointX=$this->pointX;

			if($this->checkFill=='f')
				$this->s_th.=' q ';
			else
				$this->s_th.=' q 1 g ';
			//สร้างสี่เหลี่ยมไปปิดที่ฐานล่างของตัวอักษร ญ. กับ ฐ. $s.
			$this->s_th.=sprintf('%.2f %.2f %.2f %.2f re f ',$this->pointX-$this->GetStringWidth($this->array_th[$i-1])*$this->k,$this->pointY-.27*$this->FontSize*$this->k,.9*$this->GetStringWidth($this->array_th[$i-1])*$this->k,.25*$this->FontSize*$this->k);
			$this->s_th.=' Q ';

			$this->txt_error=1;
		}
		//ตรวจสอบตัวอักษรตัวก่อนหน้านั้นเป็นอักขระ ฏ. กับ ฎ.
		elseif($this->_errorTh($this->array_th[$i-1])==4)							
		{
			$pointY=$this->pointY-.25*$this->FontSize*$this->k;
			$this->txt_error=1;
		}
		//จบการตรวจสอบตัวอักษรสระล่าง
	}																						
	//จบการตรวจสอบตัวอักษระสระล่าง
		
	if($this->txt_error==1)
		$this->TText($pointX,$pointY,$this->array_th[$i]);
}

/********************************************************************************
* ใช้งาน: Function	_checkT ของ Class FPDF_TH				
* การทำงาน: ใช้ในการตรวจสอบอักษรที่อาจจะทำให้เกิดการพิมพ์ที่ผิดพลาด			
* ความต้องการ: $char_th = ตัวอักษรที่จะใช้ในการเปรียบเทียบ			
*********************************************************************************/
function _errorTh($char_th)
{	
	$txt_error=0;
	//ตัวอักษรบน-บน
	if(($char_th=='่')||($char_th=='้')||($char_th=='๊')||($char_th=='๋')||($char_th=='์'))
		$txt_error=1;
	//ตัวอักษรบน
	elseif(($char_th=='ิ')||($char_th=='ี')||($char_th=='ึ')||($char_th=='ื')||($char_th=='็')||($char_th=='ั')||($char_th=='ำ'))
		$txt_error=2;
	//ตัวอักษรกลาง-บน
	elseif(($char_th=='ป')||($char_th=='ฟ')||($char_th=='ฝ'))
		$txt_error=3;
	//ตัวอักษรกลาง-ล่าง
	elseif(($char_th=='ฎ')||($char_th=='ฏ'))
		$txt_error=4;
	//ตัวอักษรกลาง-ล่าง
	elseif(($char_th=='ญ')||($char_th=='ฐ'))
		$txt_error=5;
	//ตัวอักษรสระล่าง
	elseif(($char_th=='ุ')||($char_th=='ู'))
		$txt_error=6;
	else
		$txt_error=0;
	return $txt_error;
}

/********************************************************************************
* ใช้งาน: Function	_checkT ของ Class FPDF_TH									*
* การทำงาน: ใช้ในพิมพ์ตัวอักษรที่ตรวจสอบแล้ว									*
* ความต้องการ: $txt_th = ตัวอักษร 1 ตัว ที่ตรวจสอบแล้ว							*
*						$s = สายอักขระของโคด PDF								*
*********************************************************************************/
function TText($pX,$pY,$txt_th)
{	
	//ตวจสอบการใส่สีเซลล์
	if($this->ColorFlag)
		$this->s_th.=' q '.$this->TextColor.' ';
	$txt_th2=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt_th)));
	//ระบุตำแหน่ง และพิมพ์ตัวอักษร
	$this->s_th.=sprintf(' BT %.2f %.2f Td (%s) Tj ET ',$pX,$pY,$txt_th2);
	if($this->ColorFlag)
		$this->s_th.=' Q ';
}

/****************************************************************************************
* ใช้งาน: called by function MultiCell within this class								
* อ้างอิง: Function Cell	ของ Class FPDF												
* การทำงาน: ใช้ในการพิมพ์ข้อความทีละบรรทัดของเอกสาร PDF 											
* รูบแบบ: MCell (	$w = ความกว้างของCell,													
*					$h = ความสูงของCell,													
*					$txt = ข้อความที่จะพิมพ์,													
*					$border = กำหนดการแสดงเส้นกรอบ(0 = ไม่แสดง, 1= แสดง),					
*					$ln = ตำแหน่งที่อยู่ถัดไปจากเซลล์(0 = ขวา, 1 = บรรทัดถัดไป, 2 = ด้านล่าง),
*					$align = ตำแหน่งข้อความ(L = ซ้าย, R = ขวา, C = กึ่งกลาง, T = บน, B = ล่าง),	
*					$fill = กำหนดการแสดงสีของCell(0 = ไม่แสดง, 1 = แสดง)			
*					$link = URL ที่ต้องการให้ข้อความเชื่อมโยงไปถึง		
*				)
*****************************************************************************************/
function MCell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link='')
{
	$this->checkFill="";
	$k=$this->k;
	if($this->y+$h>$this->PageBreakTrigger && !$this->InFooter && $this->AcceptPageBreak())
	{
		//ขึ้นหน้าใหม่อัตโนมัต
		$x=$this->x;
		$ws=$this->ws;
		if($ws>0)
		{
			$this->ws=0;
			$this->_out('0 Tw');
		}
		$this->AddPage($this->CurOrientation);
		$this->x=$x;
		if($ws>0)
		{
			$this->ws=$ws;
			$this->_out(sprintf('%.3f Tw',$ws*$k));
		}
	}
	//กำหนดความกว้างเซลล์เท่ากับหน้ากระดาษ
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$this->s_th='';
	//กำหนดการแสดงเส้นกรอบ 4 ด้าน และสีกรอบ
	if($fill==1 || $border==1)
	{
		if($fill==1)
			$op=($border==1) ? 'B' : 'f';
		else
			$op='S';
		$this->s_th=sprintf('%.2f %.2f %.2f %.2f re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
		if($op=='f')
			$this->checkFill=$op;
	}
	//กำหนดการแสดงเส้นกรอบทีละเส้น
	if(is_string($border))
	{
		$x=$this->x;
		$y=$this->y;
		if(strpos($border,'L')!==false)
			$this->s_th.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
		if(strpos($border,'T')!==false)
			$this->s_th.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
		if(strpos($border,'R')!==false)
			$this->s_th.=sprintf('%.2f %.2f m %.2f %.2f l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
		if(strpos($border,'B')!==false)
			$this->s_th.=sprintf('%.2f %.2f m %.2f %.2f l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
	}


	if($txt!=='')
	{			
		$x=$this->x;
		$y=$this->y;
		//กำหนดการจัดข้อความในเซลล์ตามแนวระดับ
		if(strpos($align,'R')!==false)
			$dx=$w-$this->cMargin-$this->GetStringWidth($txt);
		elseif(strpos($align,'C')!==false)
			$dx=($w-$this->GetStringWidth($txt))/2;
		else
			$dx=$this->cMargin;
		//กำหนดการจัดข้อความในเซลล์ตามแนวดิ่ง
		if(strpos($align,'T')!==false)
			$dy=$h-(.7*$this->k*$this->FontSize);
		elseif(strpos($align,'B')!==false)
			$dy=$h-(.3*$this->k*$this->FontSize);
		else
			$dy=.5*$h;
		//กำหนดการขีดเส้นใต้ข้อความ
		if($this->underline)
		{	
			//กำหนดบันทึกกราฟิก
			if($this->ColorFlag)
				$this->s_th.='q '.$this->TextColor.' ';
			//ขีดเส้นใต้ข้อความ0
			$this->s_th.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
			//กำหนดคืนค่ากราฟิก
			if($this->ColorFlag)
				$this->s_th.=' Q';
		}
		//กำหนดข้อความเชื่อมโยงไปถึง
		if($link)
			$this->Link($this->x,$this->y,$this->GetStringWidth($txt),$this->FontSize,$link);
		if($this->s_th)
			$this->_out($this->s_th);
		$this->s_th='';
		//ตัดอักษรออกจากข้อความ ทีละตัวเก็บลงอะเรย์
		$this->array_th=substr($txt,0);
		$i=0;

		while($i<=strlen($txt))
		{	
			//กำหนดตำแหน่งที่จะพิมพ์อักษรในเซลล์
			$this->pointX=($x+$dx+.02*$this->GetStringWidth($this->array_th[$i-1]))*$k;
			$this->pointY=($this->h-($y+$dy+.3*$this->FontSize))*$k;
			//ตรวจสอบอักษร ปรับตำแหน่งและทำการพิมพ์
			$this->_checkT($i);
			if($this->txt_error==0)
				$this->TText($this->pointX,$this->pointY,$this->array_th[$i]);
			else
			{
				$this->txt_error=0;
			}
			//ตรวจสอบการใส่เลขหน้า
			if($this->array_th[$i]=='{'&&$this->array_th[$i+1]=='n'&&$this->array_th[$i+2]=='b'&&$this->array_th[$i+3]=='}')
				$i=$i+3;
			//เลื่อนตำแหน่ง x ไปที่ตัวที่จะพิมพ์ถัดไป
			$x=$x+$this->GetStringWidth($this->array_th[$i]);
			$i++;
		}
		$this->_out($this->s_th);
	}
	else
		//นำค่าไปแสดงเมื่อไม่มีข้อความ
		$this->_out($this->s_th);

	$this->lasth=$h;
	//ตรวจสอบการวางตำแหน่งของเซลล์ถัดไป
	if($ln>0)
	{
		//ขึ้นบรรทัดใหม่
		$this->y+=$h;
		if($ln==1)
			$this->x=$this->lMargin;
	}
	else
		$this->x+=$w;
}
//End of class
}

?>
