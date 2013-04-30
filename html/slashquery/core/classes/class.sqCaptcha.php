<?php
/**
 * sqCaptcha - /slashquery/core/classes/class.sqCaptcha.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqCaptcha extends sqBase {

  public function __construct() {
    sqSession::Start();
    $this->capitals = json_decode('{"Afghanistan":"Kabul","Albania":"Tirana","Algeria":"Algiers","Angola":"Luanda","Armenia":"Yerevan","Aruba":"Oranjestad","Australia":"Canberra","Austria":"Vienna","Azerbaijan":"Baku","Bahamas":"Nassau","Bahrain":"Manama","Bangladesh":"Dhaka","Barbados":"Bridgetown","Belarus":"Minsk","Belgium":"Brussels","Belize":"Belmopan","Bermuda":"Hamilton","Bhutan":"Thimphu","Bolivia":"Sucre","Botswana":"Gaborone","Brazil":"Brasilia","Bulgaria":"Sofia","Burundi":"Bujumbura","Cameroon":"Yaounde","Canada":"Ottawa","Chile":"Santiago","China":"Beijing","Colombia":"Bogota","Comoros":"Moroni","Croatia":"Zagreb","Cuba":"Havana","Cyprus":"Nicosia","Denmark":"Copenhagen","Djibouti":"Djibouti","Dominica":"Roseau","Ecuador":"Quito","Egypt":"Cairo","Eritrea":"Asmara","Estonia":"Tallinn","Fiji":"Suva","Finland":"Helsinki","France":"Paris","Gabon":"Libreville","Gambia":"Banjul","Georgia":"Tbilisi","Germany":"Berlin","Ghana":"Accra","Gibraltar":"Gibraltar","Greece":"Athens","Guatemala":"Guatemala","Guinea":"Conakry","Guyana":"Georgetown","Honduras":"Tegucigalpa","Hungary":"Budapest","Iceland":"Reykjavik","Indonesia":"Jakarta","Iran":"Tehran","Iraq":"Baghdad","Ireland":"Dublin","Israel":"Jerusalem","Italy":"Rome","Jamaica":"Kingston","Japan":"Tokyo","Jordan":"Amman","Kazakhstan":"Astana","Kenya":"Nairobi","Kiribati":"Tarawa","Kosovo":"Pristina","Kuwait":"Kuwait","Kyrgyzstan":"Bishkek","Laos":"Vientiane","Latvia":"Riga","Lebanon":"Beirut","Lesotho":"Maseru","Liberia":"Monrovia","Libya":"Tripoli","Liechtenstein":"Vaduz","Lithuania":"Vilnius","Luxembourg":"Luxembourg","Macau":"Macau","Macedonia":"Skopje","Madagascar":"Antananarivo","Malawi":"Lilongwe","Maldives":"Male","Mali":"Bamako","Malta":"Valletta","Mauritania":"Nouakchott","Mayotte":"Mamoudzou","Mexico":"Mexico","Micronesia":"Palikir","Moldova":"Chisinau","Monaco":"Monaco","Montenegro":"Podgorica","Morocco":"Rabat","Mozambique":"Maputo","Myanmar":"Naypyidaw","Namibia":"Windhoek","Nauru":"Yaren","Nepal":"Kathmandu","Netherlands":"Amsterdam","Nicaragua":"Managua","Niger":"Niamey","Nigeria":"Abuja","Norway":"Oslo","Oman":"Muscat","Pakistan":"Islamabad","Palau":"Ngerulmud","Panama":"Panama","Paraguay":"Asuncion","Peru":"Lima","Philippines":"Manila","Poland":"Warsaw","Portugal":"Lisbon","Qatar":"Doha","Romania":"Bucharest","Russia":"Moscow","Rwanda":"Kigali","Samoa":"Apia","Senegal":"Dakar","Serbia":"Belgrade","Seychelles":"Victoria","Singapore":"Singapore","Slovakia":"Bratislava","Slovenia":"Ljubljana","Somalia":"Mogadishu","Spain":"Madrid","Sudan":"Khartoum","Suriname":"Paramaribo","Svalbard":"Longyearbyen","Swaziland":"Mbabane","Sweden":"Stockholm","Switzerland":"Bern","Syria":"Damascus","Taiwan":"Taipei","Tajikistan":"Dushanbe","Tanzania":"Dodoma","Thailand":"Bangkok","Togo":"Lome","Tunisia":"Tunis","Turkey":"Ankara","Turkmenistan":"Ashgabat","Tuvalu":"Funafuti","Uganda":"Kampala","Ukraine":"Kiev","Uruguay":"Montevideo","Uzbekistan":"Tashkent","Venezuela":"Caracas","Vietnam":"Hanoi","Yemen":"Sanaa","Zambia":"Lusaka","Zimbabwe":"Harare"}', true);
    $this->genCaptcha();
  }

  public function genCaptcha() {
    $this->country = array_rand($this->capitals, 1);
    $capital = $this->capitals[$this->country];
    sqSession::Set('captcha', $capital);

    $len = strlen($capital);
    $replace = floor($len / 3);

    $out = array();
    $m = rand(0, $len -1);
    for ($i=0; $i < $replace; $i++) {
      while (in_array($m, $out) ) {
      	$m = rand(0, $len -1);
      }
     $out[] = $m;
    }

    $words = array();
    foreach ($out as $value) {
      $words[] = $capital[$value];
      $capital[$value] = '_';
    }

    $this->words = $words;
    $this->capital = $capital;
  }

  public function getCaptcha() {
    return sqSession::get('captcha');
  }

}
