<?php

// ** Make sure all objects associated with the Muhimbi client can be resolved
require_once(__DIR__ . '/../../client/autoload.php');

$api_key = '';

//** Was a file uploaded?
if(isset($_FILES['file']) == true)
{
	//** Make sure an api key has been entered
	if($api_key == '') {
		echo 'Please update the sample code and enter the API Key that came with your subscription.';
		exit;
	}

	// ** Specify the API key associated with your subscription.
    MuhimbiPDFOnline\Client\Configuration::getDefaultConfiguration()->setApiKey('api_key', $api_key);

	// ** The service's host name is already set, but for debugging purposes you may want to switch between 'http' and 'https'.'
    MuhimbiPDFOnline\Client\Configuration::getDefaultConfiguration()->setHost('https://api.muhimbi.com/api');

	// ** We are dealing with the ConvertApi, so instantiate the relevant class
    $api_instance = new MuhimbiPDFOnline\Client\Api\ConvertApi();

	// ** We need to fill out the data for the conversion operation
    $input_data = new MuhimbiPDFOnline\Client\Model\ConvertData();

    // ** Always pass the name of the input file, or if unknown pass any name, but with the correct file extension.
    $input_data->setSourceFileName($_FILES["file"]["name"]);

	// ** Pass the content of the uploaded file, making sure it is base64 encoded.
    $input_data->setSourceFileContent(base64_encode(file_get_contents($_FILES["file"]["tmp_name"])));

	// ** Specify the format of the file type we wish to convert to.
    $input_data->setOutputFormat($_POST["outputFormat"]);

    //** If you are expecting long running operations then consider longer timeouts
    ini_set('default_socket_timeout', 300);
    set_time_limit ( 300 );

    try { 
		// ** Carry out the conversion
        $result = $api_instance->convert($input_data);

        // ** Send the converted file back to the user
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"convert." . $input_data->getOutputFormat() . "\"");
        echo base64_decode($result->getProcessedFileContent());
        exit;
    } catch (Exception $e) {
        echo 'Exception when calling ConvertApi->convert: ', $e->getMessage(), PHP_EOL;
    }
}
?>

<html>
  <body>
    <form action="convert.php" method="post" enctype="multipart/form-data">

      Output format: <select name="outputFormat">
        <option value="PDF">PDF</option>
        <option value="XPS">XPS</option>
        <option value="DOCX">DOCX</option>
        <option value="DOC">DOC</option>
        <option value="ODT">ODT</option>
        <option value="RTF">RTF</option>
        <option value="TXT">TXT</option>
        <option value="MHT">MHT</option>
        <option value="HTML">HTML</option>
        <option value="XML">XML</option>
        <option value="XLS">XLS</option>
        <option value="XLSX">XLSX</option>
        <option value="CSV">CSV</option>
        <option value="ODS">ODS</option>
        <option value="PPT">PPT</option>
        <option value="PPTX">PPTX</option>
        <option value="ODP">ODP</option>
        <option value="PPS">PPS</option>
        <option value="PPSX">PPSX</option>
      </select>

	  &nbsp;&nbsp;&nbsp;
      <input type="file" name="file" />

	  <br/><br>
      <input type="submit" value="Convert" />
    </form>
  </body>
</html>