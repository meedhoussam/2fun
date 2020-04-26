<?php
class Easy_Geo_Redirect
{
    public static $country_list = array(
        "AF" => "Afghanistan",   // Afghanistan
        "AL" => "Albania",   // Albania
        "DZ" => "Algeria",   // Algeria
        "AS" => "American Samoa",   // American Samoa
        "AD" => "Andorra",   // Andorra 
        "AO" => "Angola",   // Angola
        "AI" => "Anguilla",   // Anguilla
        "AQ" => "Antarctica",   // Antarctica
        "AG" => "Antigua and Barbuda",   // Antigua and Barbuda
        "AR" => "Argentina",   // Argentina
        "AM" => "Armenia",   // Armenia
        "AW" => "Aruba",   // Aruba 
        "AU" => "Australia",   // Australia 
        "AT" => "Austria",   // Austria
        "AZ" => "Azerbaijan",   // Azerbaijan
        "BS" => "Bahamas",   // Bahamas
        "BH" => "Bahrain",   // Bahrain 
        "BD" => "Bangladesh",   // Bangladesh
        "BB" => "Barbados",   // Barbados 
        "BY" => "Belarus",   // Belarus 
        "BE" => "Belgium",   // Belgium
        "BZ" => "Belize",   // Belize
        "BJ" => "Benin",   // Benin
        "BM" => "Bermuda",   // Bermuda
        "BT" => "Bhutan",   // Bhutan
        "BO" => "Bolivia",   // Bolivia
        "BA" => "Bosnia and Herzegovina",   // Bosnia and Herzegovina
        "BW" => "Botswana",   // Botswana
        "BV" => "Bouvet Island",   // Bouvet Island
        "BR" => "Brazil",   // Brazil
        "IO" => "British Indian Ocean Territory",   // British Indian Ocean Territory
        "VG" => "British Virgin Islands",   // British Virgin Islands,
        "BN" => "Brunei Darussalam",   // Brunei Darussalam
        "BG" => "Bulgaria",   // Bulgaria
        "BF" => "Burkina Faso",   // Burkina Faso
        "BI" => "Burundi",   // Burundi
        "KH" => "Cambodia",   // Cambodia 
        "CM" => "Cameroon",   // Cameroon
        "CA" => "Canada",   // Canada 
        "CV" => "Cape Verde",   // Cape Verde
        "KY" => "Cayman Islands",   // Cayman Islands
        "CF" => "Central African Republic",   // Central African Republic
        "TD" => "Chad",   // Chad
        "CL" => "Chile",   // Chile
        "CN" => "China",   // China
        "CX" => "Christmas Island",   // Christmas Island
        "CC" => "Cocos (Keeling Islands)",   // Cocos (Keeling Islands)
        "CO" => "Colombia",   // Colombia
        "KM" => "Comoros",   // Comoros
        "CG" => "Congo",   // Congo 
        "CK" => "Cook Islands",   // Cook Islands
        "CR" => "Costa Rica",   // Costa Rica 
        "HR" => "Croatia (Hrvatska)",   // Croatia (Hrvatska
        "CY" => "Cyprus",   // Cyprus
        "CZ" => "Czech Republic",   // Czech Republic
        "CG" => "Democratic Republic of Congo",   // Democratic Republic of Congo,
        "DK" => "Denmark",   // Denmark
        "DJ" => "Djibouti",   // Djibouti
        "DM" => "Dominica",   // Dominica
        "DO" => "Dominican Republic",   // Dominican Republic
        "TP" => "East Timor",   // East Timor
        "EC" => "Ecuador",   // Ecuador
        "EG" => "Egypt",   // Egypt 
        "SV" => "El Salvador",   // El Salvador 
        "GQ" => "Equatorial Guinea",   // Equatorial Guinea
        "ER" => "Eritrea",   // Eritrea 
        "EE" => "Estonia",   // Estonia 
        "ET" => "Ethiopia",   // Ethiopia
        "FK" => "Falkland Islands (Malvinas)",   // Falkland Islands (Malvinas)
        "FO" => "Faroe Islands",   // Faroe Islands 
        "FM" => "Federated States of Micronesia",   // Federated States of Micronesia,
        "FJ" => "Fiji",   // Fiji
        "FI" => "Finland",   // Finland
        "FR" => "France",   // France
        "GF" => "French Guiana",   // French Guiana
        "PF" => "French Polynesia",   // French Polynesia
        "TF" => "French Southern Territories",   // French Southern Territories
        "GA" => "Gabon",   // Gabon
        "GM" => "Gambia",   // Gambia
        "GE" => "Georgia",   // Georgia
        "DE" => "Germany",   // Germany
        "GH" => "Ghana",   // Ghana
        "GI" => "Gibraltar",   // Gibraltar
        "GR" => "Greece",   // Greece
        "GL" => "Greenland",   // Greenland
        "GD" => "Grenada",   // Grenada 
        "GP" => "Guadeloupe",   // Guadeloupe
        "GU" => "Guam",   // Guam 
        "GT" => "Guatemala",   // Guatemala
        "GN" => "Guinea",   // Guinea
        "GW" => "Guinea-Bissau",   // Guinea-Bissau
        "GY" => "Guyana",   // Guyana
        "HT" => "Haiti",   // Haiti
        "HM" => "Heard and McDonald Islands",   // Heard and McDonald Islands
        "HN" => "Honduras",   // Honduras
        "HK" => "Hong Kong",   // Hong Kong
        "HU" => "Hungary",   // Hungary
        "IS" => "Iceland",   // Iceland
        "IN" => "India",   // India
        "ID" => "Indonesia",   // Indonesia
        "IR" => "Iran",   // Iran
        "IQ" => "Iraq",   // Iraq
        "IE" => "Ireland",   // Ireland
        "IL" => "Israel",   // Israel
        "IT" => "Italy",   // Italy
        "CI" => "Ivory Coast",   // Ivory Coast,
        "JM" => "Jamaica",   // Jamaica
        "JP" => "Japan",   // Japan 
        "JO" => "Jordan",   // Jordan 
        "KZ" => "Kazakhstan",   // Kazakhstan
        "KE" => "Kenya",   // Kenya 
        "KI" => "Kiribati",   // Kiribati 
        "KW" => "Kuwait",   // Kuwait
        "KG" => "Kuwait",   // Kyrgyzstan
        "LA" => "Laos",   // Laos
        "LV" => "Latvia",   // Latvia
        "LB" => "Lebanon",   // Lebanon
        "LS" => "Lesotho",   // Lesotho
        "LR" => "Liberia",   // Liberia 
        "LY" => "Libya",   // Libya
        "LI" => "Liechtenstein",   // Liechtenstein
        "LT" => "Lithuania",   // Lithuania
        "LU" => "Luxembourg",   // Luxembourg 
        "MO" => "Macau",   // Macau
        "MK" => "Macedonia",   // Macedonia
        "MG" => "Madagascar",   // Madagascar
        "MW" => "Malawi",   // Malawi
        "MY" => "Malaysia",   // Malaysia
        "MV" => "Maldives",   // Maldives
        "ML" => "Mali",   // Mali
        "MT" => "Malta",   // Malta
        "MH" => "Marshall Islands",   // Marshall Islands
        "MQ" => "Martinique",   // Martinique
        "MR" => "Mauritania",   // Mauritania
        "MU" => "Mauritius",   // Mauritius
        "YT" => "Mayotte",   // Mayotte
        "MX" => "Mexico",   // Mexico
        "MD" => "Moldova",   // Moldova
        "MC" => "Monaco",   // Monaco
        "MN" => "Mongolia",   // Mongolia
        "MS" => "Montserrat",   // Montserrat
        "MA" => "Morocco",   // Morocco
        "MZ" => "Mozambique",   // Mozambique
        "MM" => "Myanmar",   // Myanmar
        "NA" => "Namibia",   // Namibia
        "NR" => "Nauru",   // Nauru
        "NP" => "Nepal",   // Nepal
        "NL" => "Netherlands",   // Netherlands
        "AN" => "Netherlands Antilles",   // Netherlands Antilles
        "NC" => "New Caledonia",   // New Caledonia
        "NZ" => "New Zealand",   // New Zealand
        "NI" => "Nicaragua",   // Nicaragua
        "NE" => "Nicaragua",   // Niger
        "NG" => "Nigeria",   // Nigeria
        "NU" => "Niue",   // Niue
        "NF" => "Norfolk Island",   // Norfolk Island
        "KP" => "Korea (North)",   // Korea (North)
        "MP" => "Northern Mariana Islands",   // Northern Mariana Islands
        "NO" => "Norway",   // Norway
        "OM" => "Oman",   // Oman
        "PK" => "Pakistan",   // Pakistan
        "PW" => "Palau",   // Palau
        "PA" => "Panama",   // Panama
        "PG" => "Papua New Guinea",   // Papua New Guinea
        "PY" => "Paraguay",   // Paraguay
        "PE" => "Peru",   // Peru
        "PH" => "Philippines",   // Philippines
        "PN" => "Pitcairn",   // Pitcairn
        "PL" => "Poland",   // Poland
        "PT" => "Portugal",   // Portugal
        "PR" => "Puerto Rico",   // Puerto Rico
        "QA" => "Qatar",   // Qatar
        "RE" => "Reunion",   // Reunion
        "RO" => "Romania",   // Romania
        "RU" => "Russian Federation",   // Russian Federation
        "RW" => "Rwanda",   // Rwanda
        "SH" => "Saint Helena and Dependencies",   // Saint Helena and Dependencies,
        "KN" => "Saint Kitts and Nevis",   // Saint Kitts and Nevis
        "LC" => "Saint Lucia",   // Saint Lucia
        "VC" => "Saint Vincent and The Grenadines",   // Saint Vincent and The Grenadines
        "VC" => "Saint Vincent and the Grenadines",   // Saint Vincent and the Grenadines,
        "WS" => "Samoa",   // Samoa
        "SM" => "San Marino",   // San Marino
        "ST" => "Sao Tome and Principe",   // Sao Tome and Principe 
        "SA" => "Saudi Arabia",   // Saudi Arabia
        "SN" => "Senegal",   // Senegal
		"RS" => "Serbia",   // Serbia
        "SC" => "Seychelles",   // Seychelles
        "SL" => "Sierra Leone",   // Sierra Leone
        "SG" => "Singapore",   // Singapore
        "SK" => "Slovak Republic",   // Slovak Republic
        "SI" => "Slovenia",   // Slovenia
        "SB" => "Solomon Islands",   // Solomon Islands
        "SO" => "Somalia",   // Somalia
        "ZA" => "South Africa",   // South Africa
        "GS" => "S. Georgia and S. Sandwich Isls.",   // S. Georgia and S. Sandwich Isls.
        "KR" => "South Korea",   // South Korea,
        "ES" => "Spain",   // Spain
        "LK" => "Sri Lanka",   // Sri Lanka
        "SR" => "Suriname",   // Suriname
        "SJ" => "Svalbard and Jan Mayen Islands",   // Svalbard and Jan Mayen Islands
        "SZ" => "Swaziland",   // Swaziland
        "SE" => "Sweden",   // Sweden
        "CH" => "Switzerland",   // Switzerland
        "SY" => "Syria",   // Syria
        "TW" => "Taiwan",   // Taiwan
        "TJ" => "Tajikistan",   // Tajikistan
        "TZ" => "Tanzania",   // Tanzania
        "TH" => "Thailand",   // Thailand
        "TG" => "Togo",   // Togo
        "TK" => "Tokelau",   // Tokelau
        "TO" => "Tonga",   // Tonga
        "TT" => "Trinidad and Tobago",   // Trinidad and Tobago
        "TN" => "Tunisia",   // Tunisia
        "TR" => "Turkey",   // Turkey
        "TM" => "Turkmenistan",   // Turkmenistan
        "TC" => "Turks and Caicos Islands",   // Turks and Caicos Islands
        "TV" => "Tuvalu",   // Tuvalu
        "UG" => "Uganda",   // Uganda
        "UA" => "Ukraine",   // Ukraine
        "AE" => "United Arab Emirates",   // United Arab Emirates
        "UK" => "United Kingdom",   // United Kingdom
        "US" => "United States",   // United States
        "UM" => "US Minor Outlying Islands",   // US Minor Outlying Islands
        "UY" => "Uruguay",   // Uruguay
        "VI" => "US Virgin Islands",   // US Virgin Islands,
        "UZ" => "Uzbekistan",   // Uzbekistan
        "VU" => "Vanuatu",   // Vanuatu
        "VA" => "Vatican City State (Holy See)",   // Vatican City State (Holy See)
        "VE" => "Venezuela",   // Venezuela
        "VN" => "Viet Nam",   // Viet Nam
        "WF" => "Wallis and Futuna Islands",   // Wallis and Futuna Islands
        "EH" => "Western Sahara",   // Western Sahara
        "YE" => "Yemen",   // Yemen
        "ZM" => "Zambia",   // Zambia
        "ZW" => "Zimbabwe",   // Zimbabwe
        "CU" => "Cuba",   // Cuba,
        "IR" => "Iran",   // Iran,
    );
    
    public static $country_type_list = array(
        "AF" => "all 3rdcountry",   // Afghanistan
        "AL" => "all",   // Albania
        "DZ" => "all",   // Algeria
        "AS" => "all",   // American Samoa
        "AD" => "all",   // Andorra 
        "AO" => "all",   // Angola
        "AI" => "all",   // Anguilla
        "AQ" => "all",   // Antarctica
        "AG" => "all",   // Antigua and Barbuda
        "AR" => "all",   // Argentina
        "AM" => "all",   // Armenia
        "AW" => "all",   // Aruba 
        "AU" => "all",   // Australia 
        "AT" => "all europe",   // Austria
        "AZ" => "all",   // Azerbaijan
        "BS" => "all",   // Bahamas
        "BH" => "all",   // Bahrain 
        "BD" => "all",   // Bangladesh
        "BB" => "all",   // Barbados 
        "BY" => "all",   // Belarus 
        "BE" => "all europe",   // Belgium
        "BZ" => "all",   // Belize
        "BJ" => "all",   // Benin
        "BM" => "all",   // Bermuda
        "BT" => "all",   // Bhutan
        "BO" => "all",   // Bolivia
        "BA" => "all",   // Bosnia and Herzegovina
        "BW" => "all",   // Botswana
        "BV" => "all",   // Bouvet Island
        "BR" => "all",   // Brazil
        "IO" => "all",   // British Indian Ocean Territory
        "VG" => "all",   // British Virgin Islands,
        "BN" => "all",   // Brunei Darussalam
        "BG" => "all europe",   // Bulgaria
        "BF" => "all",   // Burkina Faso
        "BI" => "all 3rdcountry",   // Burundi
        "KH" => "all",   // Cambodia 
        "CM" => "all",   // Cameroon
        "CA" => "all",   // Canada 
        "CV" => "all",   // Cape Verde
        "KY" => "all",   // Cayman Islands
        "CF" => "all",   // Central African Republic
        "TD" => "all",   // Chad
        "CL" => "all",   // Chile
        "CN" => "all",   // China
        "CX" => "all",   // Christmas Island
        "CC" => "all",   // Cocos (Keeling Islands)
        "CO" => "all",   // Colombia
        "KM" => "all",   // Comoros
        "CG" => "all 3rdcountry",   // Congo 
        "CK" => "all",   // Cook Islands
        "CR" => "all",   // Costa Rica 
        "HR" => "all europe",   // Croatia (Hrvatska
        "CY" => "all europe",   // Cyprus
        "CZ" => "all europe",   // Czech Republic
        "CG" => "all",   // Democratic Republic of Congo,
        "DK" => "all europe",   // Denmark
        "DJ" => "all",   // Djibouti
        "DM" => "all",   // Dominica
        "DO" => "all",   // Dominican Republic
        "TP" => "all",   // East Timor
        "EC" => "all",   // Ecuador
        "EG" => "all",   // Egypt 
        "SV" => "all",   // El Salvador 
        "GQ" => "all",   // Equatorial Guinea
        "ER" => "all 3rdcountry",   // Eritrea 
        "EE" => "all europe",   // Estonia 
        "ET" => "all 3rdcountry",   // Ethiopia
        "FK" => "all",   // Falkland Islands (Malvinas)
        "FO" => "all",   // Faroe Islands 
        "FM" => "all",   // Federated States of Micronesia,
        "FJ" => "all",   // Fiji
        "FI" => "all europe",   // Finland
        "FR" => "all europe",   // France
        "GF" => "all",   // French Guiana
        "PF" => "all",   // French Polynesia
        "TF" => "all",   // French Southern Territories
        "GA" => "all",   // Gabon
        "GM" => "all",   // Gambia
        "GE" => "all",   // Georgia
        "DE" => "all europe",   // Germany
        "GH" => "all",   // Ghana
        "GI" => "all",   // Gibraltar
        "GR" => "all europe",   // Greece
        "GL" => "all",   // Greenland
        "GD" => "all",   // Grenada 
        "GP" => "all",   // Guadeloupe
        "GU" => "all",   // Guam 
        "GT" => "all",   // Guatemala
        "GN" => "all",   // Guinea
        "GW" => "all 3rdcountry",   // Guinea-Bissau
        "GY" => "all",   // Guyana
        "HT" => "all",   // Haiti
        "HM" => "all",   // Heard and McDonald Islands
        "HN" => "all",   // Honduras
        "HK" => "all",   // Hong Kong
        "HU" => "all europe",   // Hungary
        "IS" => "all",   // Iceland
        "IN" => "all",   // India
        "ID" => "all",   // Indonesia
        "IR" => "all",   // Iran
        "IQ" => "all",   // Iraq
        "IE" => "all europe",   // Ireland
        "IL" => "all",   // Israel
        "IT" => "all europe",   // Italy
        "CI" => "all",   // Ivory Coast,
        "JM" => "all",   // Jamaica
        "JP" => "all",   // Japan 
        "JO" => "all",   // Jordan 
        "KZ" => "all",   // Kazakhstan
        "KE" => "all",   // Kenya 
        "KI" => "all",   // Kiribati 
        "KW" => "all",   // Kuwait
        "KG" => "all",   // Kyrgyzstan
        "LA" => "all",   // Laos
        "LV" => "all europe",   // Latvia
        "LB" => "all",   // Lebanon
        "LS" => "all",   // Lesotho
        "LR" => "all 3rdcountry",   // Liberia 
        "LY" => "all",   // Libya
        "LI" => "all",   // Liechtenstein
        "LT" => "all europe",   // Lithuania
        "LU" => "all europe",   // Luxembourg 
        "MO" => "all",   // Macau
        "MK" => "all",   // Macedonia
        "MG" => "all 3rdcountry",   // Madagascar
        "MW" => "all 3rdcountry",   // Malawi
        "MY" => "all",   // Malaysia
        "MV" => "all",   // Maldives
        "ML" => "all",   // Mali
        "MT" => "all europe",   // Malta
        "MH" => "all",   // Marshall Islands
        "MQ" => "all",   // Martinique
        "MR" => "all",   // Mauritania
        "MU" => "all",   // Mauritius
        "YT" => "all",   // Mayotte
        "MX" => "all",   // Mexico
        "MD" => "all",   // Moldova
        "MC" => "all",   // Monaco
        "MN" => "all",   // Mongolia
        "MS" => "all",   // Montserrat
        "MA" => "all",   // Morocco
        "MZ" => "all",   // Mozambique
        "MM" => "all",   // Myanmar
        "NA" => "all",   // Namibia
        "NR" => "all",   // Nauru
        "NP" => "all",   // Nepal
        "NL" => "all europe",   // Netherlands
        "AN" => "all",   // Netherlands Antilles
        "NC" => "all",   // New Caledonia
        "NZ" => "all",   // New Zealand
        "NI" => "all",   // Nicaragua
        "NE" => "all 3rdcountry",   // Niger
        "NG" => "all",   // Nigeria
        "NU" => "all",   // Niue
        "NF" => "all",   // Norfolk Island
        "KP" => "all",   // Korea (North)
        "MP" => "all",   // Northern Mariana Islands
        "NO" => "all",   // Norway
        "OM" => "all",   // Oman
        "PK" => "all",   // Pakistan
        "PW" => "all",   // Palau
        "PA" => "all",   // Panama
        "PG" => "all",   // Papua New Guinea
        "PY" => "all",   // Paraguay
        "PE" => "all",   // Peru
        "PH" => "all",   // Philippines
        "PN" => "all",   // Pitcairn
        "PL" => "all europe",   // Poland
        "PT" => "all europe",   // Portugal
        "PR" => "all",   // Puerto Rico
        "QA" => "all",   // Qatar
        "RE" => "all",   // Reunion
        "RO" => "all europe",   // Romania
        "RU" => "all",   // Russian Federation
        "RW" => "all",   // Rwanda
        "SH" => "all",   // Saint Helena and Dependencies,
        "KN" => "all",   // Saint Kitts and Nevis
        "LC" => "all",   // Saint Lucia
        "VC" => "all",   // Saint Vincent and The Grenadines
        "VC" => "all",   // Saint Vincent and the Grenadines,
        "WS" => "all",   // Samoa
        "SM" => "all",   // San Marino
        "ST" => "all",   // Sao Tome and Principe 
        "SA" => "all",   // Saudi Arabia
        "SN" => "all",   // Senegal
		"RS" => "all",   // Serbia
        "SC" => "all",   // Seychelles
        "SL" => "all 3rdcountry",   // Sierra Leone
        "SG" => "all",   // Singapore
        "SK" => "all europe",   // Slovak Republic
        "SI" => "all europe",   // Slovenia
        "SB" => "all",   // Solomon Islands
        "SO" => "all",   // Somalia
        "ZA" => "all",   // South Africa
        "GS" => "all",   // S. Georgia and S. Sandwich Isls.
        "KR" => "all",   // South Korea,
        "ES" => "all europe",   // Spain
        "LK" => "all",   // Sri Lanka
        "SR" => "all",   // Suriname
        "SJ" => "all",   // Svalbard and Jan Mayen Islands
        "SZ" => "all",   // Swaziland
        "SE" => "all europe",   // Sweden
        "CH" => "all",   // Switzerland
        "SY" => "all",   // Syria
        "TW" => "all",   // Taiwan
        "TJ" => "all",   // Tajikistan
        "TZ" => "all 3rdcountry",   // Tanzania
        "TH" => "all",   // Thailand
        "TG" => "all",   // Togo
        "TK" => "all",   // Tokelau
        "TO" => "all",   // Tonga
        "TT" => "all",   // Trinidad and Tobago
        "TN" => "all",   // Tunisia
        "TR" => "all",   // Turkey
        "TM" => "all",   // Turkmenistan
        "TC" => "all",   // Turks and Caicos Islands
        "TV" => "all",   // Tuvalu
        "UG" => "all",   // Uganda
        "UA" => "all",   // Ukraine
        "AE" => "all",   // United Arab Emirates
        "UK" => "all europe",   // United Kingdom
        "US" => "all",   // United States
        "UM" => "all",   // US Minor Outlying Islands
        "UY" => "all",   // Uruguay
        "VI" => "all",   // US Virgin Islands,
        "UZ" => "all",   // Uzbekistan
        "VU" => "all",   // Vanuatu
        "VA" => "all",   // Vatican City State (Holy See)
        "VE" => "all",   // Venezuela
        "VN" => "all",   // Viet Nam
        "WF" => "all",   // Wallis and Futuna Islands
        "EH" => "all",   // Western Sahara
        "YE" => "all 3rdcountry",   // Yemen
        "ZM" => "all 3rdcountry",   // Zambia
        "ZW" => "all",   // Zimbabwe
        "CU" => "all",   // Cuba,
        "IR" => "all",   // Iran,
    );
    
    public static function CheckBlockLog()
    {
        $file_tmp_block_log = dirname(__FILE__).'/block.log';
        if (file_exists($file_tmp_block_log))
        {
            $handle = fopen($file_tmp_block_log, "r");
            $contents = fread($handle, filesize($file_tmp_block_log));
            fclose($handle);
            
            unlink($file_tmp_block_log);
            
            $contents = explode("\n", $contents);
            if (count($contents))
            {
                foreach ($contents as $row)
                {
                    $row = (array)json_decode($row, true);
                    self::Save_Block_alert($row);
                }
            }
        }
    }



    public static function CreateSettingsFile()
    {
        $params = self::Get_Params();
        
        $line = '<?php $easy_geo_settings = "'.addslashes(json_encode($params)).'"; ?>';
        
        $fp = fopen(dirname(__FILE__).'/settings.php', 'w');
        fwrite($fp, $line);
        fclose($fp);
    }


	public static function CheckWPConfig_file()
	{
	    if (!file_exists(dirname(__FILE__).'/settings.php')) self::CreateSettingsFile();
        
	    if (!defined('DIRSEP'))
        {
    	    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') define('DIRSEP', '\\');
    		else define('DIRSEP', '/');
        }
        
		if (!defined('ABSPATH') || strlen(ABSPATH) < 8) 
		{
			$scan_path = dirname(__FILE__);
			$scan_path = str_replace(DIRSEP.'wp-content'.DIRSEP.'plugins'.DIRSEP.'easy-geo-redirect', DIRSEP, $scan_path);
    		//echo TEST;
		}
        else $scan_path = ABSPATH;
        
        $filename = $scan_path.DIRSEP.'wp-config.php';
        if (!is_file($filename)) $filename = dirname($scan_path).DIRSEP.'wp-config.php';
        $handle = fopen($filename, "r");
        if ($handle === false) return false;
        $contents = fread($handle, filesize($filename));
        if ($contents === false) return false;
        fclose($handle);
        
        if (stripos($contents, '132FLSE34GG39-START') === false)     // Not found
        {
            self::PatchWPConfig_file();
        }
    }
    
	public static function PatchWPConfig_file($action = true)   // true - insert, false - remove
	{
	    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') define('DIRSEP', '\\');
		else define('DIRSEP', '/');
        
		$file = dirname(__FILE__).DIRSEP."geo.check.php";

        $integration_code = '<?php /* Siteguarding Block 132FLSE34GG39-START */ if (file_exists("'.$file.'"))include_once("'.$file.'");/* Siteguarding Block 132FLSE34GG39-END */?>';
        
        // Insert code
		if (!defined('ABSPATH') || strlen(ABSPATH) < 8) 
		{
			$scan_path = dirname(__FILE__);
			$scan_path = str_replace(DIRSEP.'wp-content'.DIRSEP.'plugins'.DIRSEP.'easy-geo-redirect', DIRSEP, $scan_path);
    		//echo TEST;
		}
        else $scan_path = ABSPATH;
        
        $filename = $scan_path.DIRSEP.'wp-config.php';
        if (!is_file($filename)) $filename = dirname($scan_path).DIRSEP.'wp-config.php';
        $handle = fopen($filename, "r");
        if ($handle === false) return false;
        $contents = fread($handle, filesize($filename));
        if ($contents === false) return false;
        fclose($handle);
        
        $pos_code = stripos($contents, '132FLSE34GG39');
        
        if ($action === false)
        {
            // Remove block
            $contents = str_replace($integration_code, "", $contents);
        }
        else {
            // Insert block
            if ( $pos_code !== false/* && $pos_code == 0*/)
            {
                // Skip double code injection
                return true;
            }
            else {
                // Insert
                $contents = $integration_code.$contents;
            }
        }
        
        $handle = fopen($filename, 'w');
        if ($handle === false) 
        {
            // 2nd try , change file permssion to 666
            $status = chmod($filename, 0666);
            if ($status === false) return false;
            
            $handle = fopen($filename, 'w');
            if ($handle === false) return false;
        }
        
        $status = fwrite($handle, $contents);
        if ($status === false) return false;
        fclose($handle);

        
        return true;
	}
    
    
    public static function Add_IP_adresses_shutdown_function()
    {
	    $reason = error_get_last();
		$fp = fopen(dirname(__FILE__).DIRSEP.'debug_geo.txt', 'a');
		$a = date("Y-m-d H:i:s")." Reason: ".$reason['message'].' File: '.$reason['file'].' Line: '.$reason['line'];	
		fwrite($fp, $a);
		fclose($fp);
    }
    
    public static function Add_IP_adresses($remove_file = true)
    {
        error_reporting(0);
        ignore_user_abort(true);
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        
        register_shutdown_function('self::Add_IP_adresses_shutdown_function');
        
        // Find GEO DB files
        $geo_db_array = array();
        foreach (glob(dirname(__FILE__).DIRSEP."geo_base_*.db") as $filename) 
        {
            $geo_db_array[] = $filename;
        }

		global $wpdb;
        
		$table_name = $wpdb->prefix . 'plgsgegeor_ip';
        
        self::Set_Params(array('geo_update_progress' => 1));
        
        // Save data to sql
        
        
        // Trunc database with IP
        if (count($geo_db_array) > 0 && file_exists(dirname(__FILE__).DIRSEP."geo_base_0.db"))
        {
            $query = "TRUNCATE ".$table_name.";";
    		$wpdb->query( $query );
        }
        
        
        foreach ($geo_db_array as $file)
        {
            $lines = file($file);
            
            foreach ($lines as $line)
            {
                $i++;
                if (trim($line) == '') continue;
                
                $a = explode(",", $line);
                
                $ip_from = trim(str_replace('"', '', $a[0]));
                $ip_till = trim(str_replace('"', '', $a[1]));
                $country_code = trim(strtoupper(str_replace('"', '', $a[2])));
                
                if (strlen($country_code) != 2) continue;
                if (strpos($ip_from, ":") !== false || strpos($ip_till, ":") !== false) continue;
                
                if (strpos($ip_from, ".") !== false)
                {
                    // Convert to number
                    $tmp_ip = explode(".", $ip_from);
                    $ip_from = $tmp_ip[0]*256*256*256 + $tmp_ip[1]*256*256 + $tmp_ip[2]*256 + $tmp_ip[3];
                }
                if (strpos($ip_till, ".") !== false)
                {
                    // Convert to number
                    $tmp_ip = explode(".", $ip_till);
                    $ip_till = $tmp_ip[0]*256*256*256 + $tmp_ip[1]*256*256 + $tmp_ip[2]*256 + $tmp_ip[3];
                }
                
        		$sql_array = array(
        			'ip_from' => $ip_from,
        			'ip_till' => $ip_till,
                    'country_code' => $country_code
        		);
                
                $wpdb->insert( $table_name, $sql_array ); 
            }
            
            if ($remove_file) unlink($file);
        }
        
        self::Set_Params(array('geo_update_progress' => 0));
    }
    
    
    
    public static function Get_Params($vars = array())
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'plgsgegeor_config';
        
        $ppbv_table = $wpdb->get_results("SHOW TABLES LIKE '".$table_name."'" , ARRAY_N);
        if(!isset($ppbv_table[0])) return false;
        
        if (count($vars) == 0)
        {
            $rows = $wpdb->get_results( 
            	"
            	SELECT *
            	FROM ".$table_name."
            	"
            );
        }
        else {
            foreach ($vars as $k => $v) $vars[$k] = "'".$v."'";
            
            $rows = $wpdb->get_results( 
            	"
            	SELECT * 
            	FROM ".$table_name."
                WHERE var_name IN (".implode(',',$vars).")
            	"
            );
        }
        
        $a = array();
        if (count($rows))
        {
            foreach ( $rows as $row ) 
            {
            	$a[trim($row->var_name)] = trim($row->var_value);
            }
        }
    
        return $a;
    }
    
    
    public static function Set_Params($data = array())
    {
		global $wpdb;
		$table_name = $wpdb->prefix . 'plgsgegeor_config';
    
        if (count($data) == 0) return;   
        
        foreach ($data as $k => $v)
        {
            $tmp = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $table_name . ' WHERE var_name = %s LIMIT 1;', $k ) );
            
            if ($tmp == 0)
            {
                // Insert    
                $wpdb->insert( $table_name, array( 'var_name' => $k, 'var_value' => $v ) ); 
            }
            else {
                // Update
                $data = array('var_value'=>$v);
                $where = array('var_name' => $k);
                $wpdb->update( $table_name, $data, $where );
            }
        } 
    }
    
    public static function GetMyIP()
    {
		$ip_address = $_SERVER["REMOTE_ADDR"];
		if (isset($_SERVER["HTTP_X_REAL_IP"])) $ip_address = $_SERVER["HTTP_X_REAL_IP"];
		if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) $ip_address = $_SERVER["HTTP_CF_CONNECTING_IP"];
        return $ip_address ;
    }
    
    public static function GetCountryCode($ip)
    {
        if (isset($_COOKIE["GEO_country_code"]) && isset($_COOKIE["GEO_country_code_hash"]))
        {
            $cookie_GEO_country_code = trim($_COOKIE["GEO_country_code"]);
            $cookie_GEO_country_code_hash = trim($_COOKIE["GEO_country_code_hash"]);
            
            $hash = md5($ip.'-'.$cookie_GEO_country_code);
            if ($cookie_GEO_country_code_hash == $hash) return $cookie_GEO_country_code;
        }
        
        if (!class_exists('sg_Geo_IP2Country'))
        {
            include_once(dirname(__FILE__).DIRSEP.'geo.php');
        }
        
        $geo = new sg_Geo_IP2Country;
        $country_code = $geo->getCountryByIP($ip); 
        
        if ($country_code != '')
        {
            // Set cookie
            $hash = md5($ip.'-'.$country_code);
            setcookie("GEO_country_code", $country_code, time()+3600*24);
            setcookie("GEO_country_code_hash", $hash, time()+3600*24);
        }
        
        return $country_code;
        
        /*global $wpdb;
        
        $table_name = $wpdb->prefix . 'plgsgegeor_ip';
        
    	$real_ip = $ip;
        $tmp = explode(".", $ip);
        $ip = $tmp[0]*256*256*256 + $tmp[1]*256*256 + $tmp[2]*256 + $tmp[3];
        
        $query = "SELECT country_code
            FROM ".$table_name."
            WHERE ".$ip." BETWEEN ip_from AND ip_till
            LIMIT 1;";

        $rows = $wpdb->get_results($query);

        
        $a = array();
        if (count($rows))
        {
            foreach ( $rows as $row ) 
            {
                // Set cookie
                $hash = md5($ip.'-'.$row->country_code);
                setcookie("GEO_country_code", $row->country_code, time()+3600*24);
                setcookie("GEO_country_code_hash", $hash, time()+3600*24);
            	return trim($row->country_code);
            }
        }
        
        return '';*/
    }
    
    
    public static function Check_if_User_allowed($myCountryCode, $blocked_country_list = array())
    {
        if (count($blocked_country_list) && in_array($myCountryCode, $blocked_country_list)) return false;
        return true;
    }
    
    
    public static function Check_if_User_IP_allowed($ip, $ip_list = '')
    {
        if ($ip_list == '') return true;
        
        $ip_list = str_replace(array(".*.*.*", ".*.*", ".*"), ".", trim($ip_list));
        $ip_list = explode("\n", $ip_list);
        if (count($ip_list))
        {
            foreach ($ip_list as $rule_ip)
            {
                if (strpos($ip, $rule_ip) === 0) 
                {
                    // match
                    return false;
                }
            }
        }
        
        return true;
    }
    
    public static function Check_IP_in_list($ip, $ip_list = '')
    {
        if ($ip_list == '') return false;   // IP is not in the list
        
        $ip_list = str_replace(array(".*.*.*", ".*.*", ".*"), ".", trim($ip_list));
        $ip_list = explode("\n", $ip_list);
        if (count($ip_list))
        {
            foreach ($ip_list as $rule_ip)
            {
                if (strpos($ip, $rule_ip) === 0) 
                {
                    // match
                    return true;    // IP is in the list
                }
            }
        }
        
        return  false;   // IP is not in the list
    }
    
    

    public static function Save_Block_alert($alert_data)
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'plgsgegeor_stats';
        
        $sql_array = array(
            'time' => intval($alert_data['time']),
            'ip' => $alert_data['ip'],
            'country_code' => $alert_data['country_code'],
            'url' => addslashes($alert_data['url']),
        );
        
        $wpdb->insert( $table_name, $sql_array ); 
    }
    
    
    public static function Delete_old_logs($days)
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'plgsgegeor_stats';
        
        $old_time = time() - $days*24*60*60;
        
        $sql = 'DELETE FROM '.$table_name.' WHERE time < '.$old_time;
        $wpdb->query($sql); 
    }


	public static function PrepareDomain($domain)
	{
	    $host_info = parse_url($domain);
	    if ($host_info == NULL) return false;
	    $domain = $host_info['host'];
	    if ($domain[0] == "w" && $domain[1] == "w" && $domain[2] == "w" && $domain[3] == ".") $domain = str_replace("www.", "", $domain);
	    //$domain = str_replace("www.", "", $domain);
	    
	    return $domain;
	}
    
    public static function CheckIfPRO()
    {
        $domain = self::PrepareDomain(get_site_url());
        
        $params = self::Get_Params(array('registration_code'));
        if (!empty($params)) $registration_code = strtoupper( $params['registration_code'] );
		else return false;
        
        $check_code = strtoupper( md5( md5( md5($domain)."Version 1MI3WNNjkME4TUZj" )."5OJjDFMjjYZk2MZT" ) );
        
        if ($check_code == $registration_code) return true;
        else return false;
    }
    
    public static function CheckAntivirusInstallation()
    {
        // Check for wp-antivirus-site-protection
        $avp_path = dirname(__FILE__);
		$avp_path = str_replace('wp-geo-website-protection', 'wp-antivirus-site-protection', $avp_path);
        if ( file_exists($avp_path) ) return true;
        
        // Check for wp-antivirus-website-protection-and-website-firewall
        $avp_path = dirname(__FILE__);
		$avp_path = str_replace('wp-geo-website-protection', 'wp-antivirus-website-protection-and-website-firewall', $avp_path);
        if ( file_exists($avp_path) ) return true;
        
        // Check for wp-website-antivirus-protection
        $avp_path = dirname(__FILE__);
		$avp_path = str_replace('wp-geo-website-protection', 'wp-website-antivirus-protection', $avp_path);
        if ( file_exists($avp_path) ) return true;

        return false;
    }
    
    public static function GeneratePieData($days = 1)
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'plgsgegeor_stats';
        
        $new_time = time() - $days * 24 * 60 * 60;
        
        $query = "SELECT country_code, count(*) AS country_num
            FROM ".$table_name."
            WHERE time > '".$new_time."' 
            GROUP BY country_code
            ORDER BY count(*) desc";

        $rows = $wpdb->get_results($query);
        
        //print_r($rows);

        
        $data = array();
        if (count($rows))
        {
            $total = 0;
            $i_limit = 10;
            foreach ( $rows as $row ) 
            {
                $total = $total + $row->country_num;
                if ($i_limit > 0) $data[ $row->country_code ] = $row->country_num;
                else $data[ 'Other' ] += $row->country_num;
                
                $i_limit--;
            }
            
            //print_r($data);
            
            foreach ($data as $k => $v)
            {
                $data[$k] = round( 100 * $v / $total, 2);
            }
            
            //print_r($data);
        }
        
        return $data;
    }


    public static function PreparePieData($pie_array, $slice_flag = true)
    {
        $a = array();
        if (count($pie_array))
        {
            foreach ($pie_array as $country_code => $country_proc)
            {
                if ($country_code == "Other") $country_name_txt = "Other";
                else $country_name_txt = self::$country_list[ $country_code ];
                if ($country_name_txt == "") $country_name_txt = $country_code;
                
                if ($slice_flag) $txt = "{name: '".addslashes($country_name_txt)."', y: ".$country_proc.", sliced: true, selected: true}";
                else $txt = "{name: '".addslashes($country_name_txt)."', y: ".$country_proc."}";
                $a[] = $txt;
                
                $slice_flag = false;
            }
        }
        
        return $a;
    }
    
    public static function GetLatestRecords($amount)
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'plgsgegeor_stats';
        
        $new_time = time() - $days * 24 * 60 * 60;
        
        $query = "SELECT *
            FROM ".$table_name."
            ORDER BY id DESC
            LIMIT ".$amount;

        $rows = $wpdb->get_results($query);
        
        return $rows;
    }

}

?>