<?php

/*
 * Enviem el POST a través de curl.
 */
function enviarSOAPCurl($de, $a)
{
    $parameters = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">
  <soap:Body>
    <ConversionRate xmlns=\"http://www.webserviceX.NET/\">
      <FromCurrency>$de</FromCurrency>
      <ToCurrency>$a</ToCurrency>
    </ConversionRate>
  </soap:Body>
</soap:Envelope>";


    $url = 'http://www.webservicex.net/CurrencyConvertor.asmx';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_ENCODING, 'utf-8');
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: text/xml; charset=utf-8',
        'SOAPAction: "http://www.webserviceX.NET/ConversionRate"',
    ));

    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);
    $result = curl_exec($curl);

    echo "<h3>1 $de és igual a $result $a</h3>";
}

/*
 * Enviem el POST a través del client SOAP
 */
function enviarSOAPClient($de, $a)
{
    $requestParams = array(
        'FromCurrency' => $de,
        'ToCurrency' => $a
    );

    $client = new SoapClient('http://www.webservicex.net/CurrencyConvertor.asmx?WSDL');
    $result = $client->ConversionRate($requestParams);


    echo "<h3>1 $de és igual a $result->ConversionRateResult $a</h3>";
}

/*
 * Agafem el registre de monedes
 */
function monedes()
{
    if ($file = fopen("monedes.txt", "r")) {
        while (!feof($file)) {
            $line = fgets($file);
            $parseLine = substr($line, 0, -1);
            echo "<option value='$parseLine'>$line</option>";
        }
        fclose($file);
    }
}

/*
 * Imprimim el resultat segons la opció escollida
 */
function resultat()
{
    if (isset($_POST['enviar'])) {
        if ($_POST['opcio'] == 'curl') {
            enviarSOAPCurl($_POST['DeMoneda'], $_POST['AMoneda']);
            echo '<h4>Utilitzem CURL</h4>';
        } elseif ($_POST['opcio'] == 'soap') {
            enviarSOAPClient($_POST['DeMoneda'], $_POST['AMoneda']);
            echo '<h4>Utilitzem Client SOAP</h4>';
        } else {
            echo "Ha succeït un error...";
        }
    }
}
?>

<h2>Conversor de monedes</h2>

<form method="post">
    <p>
        <input type="radio" checked name="opcio" value="curl">Utilitzar CURL
        <input type="radio" name="opcio" value="soap">Utilitzar Client SOAP
    </p>

    <select id="DeMoneda" name="DeMoneda">
        <option value="" selected="selected">Convertim de...</option>
        <?php monedes(); ?>
    </select>

    <select id="AMoneda" name="AMoneda">
        <option value="" selected="selected">a la moneda...</option>
        <?php monedes(); ?>
    </select>

    <button type="submit" name="enviar">Enviar</button>

</form>

<?php resultat() ?>