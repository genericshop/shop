<?php

$xml = simplexml_load_file(APPLICATION_PATH . '/configs/navigation.xml') or die('Error: Cannot open XML file');

echo '<?php';
foreach($xml->xpath('//label') as $label)
{
    echo 'echo _("'.$label.'");'. PHP_EOL;
}