Klasa PHP array2xml
=========

Przykład uzycia:
=========

`$array = array(
  '@attributes' => array('atrybut1' => 'wartosc1', 'atrybut2' => 'wartosc2')
);

$xml = Array2XML::factory($array)->render();

echo $xml;`
