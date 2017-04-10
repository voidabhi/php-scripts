<?php
//construct field data for new templates
$field_data = [
    'emailsubject' => 'Agreement for ' . $config['deal_name'],
    'documents' => [
      [
        'name' => $config['deal_name'].' Document',
        'documentId' => $config['deal_id']
      ]
    ],
    'recipients' => [
        'signers' =>
        [
          [
            'roleName' => Config::get('templateRoleName'),
              //it will not work if recipient id is not set
            'recipientId' => 1,
          ]
        ]
    ],
    'envelopeTemplateDefinition' => [
        'name' => $config['deal_name'],
    ]
];
//encode field
$field_string = json_encode($field_data);
//read file
$file_string = Flysystem::read($config['doc_path']);
 
// hack, request body, inject field and file into requet body, set boundary
$request_body =
     "\r\n"
    ."\r\n"
    ."--customboundary\r\n"
    ."Content-Type: application/json\r\n"
    ."Content-Disposition: form-data\r\n"
    ."\r\n"
    ."$field_string\r\n"
    ."--customboundary\r\n"
    ."Content-Type:application/pdf\r\n"
    ."Content-Disposition: file; filename=".$config['deal_name'].";documentid=".$config['deal_id']." \r\n"
    ."\r\n"
    ."$file_string\r\n"
    ."--customboundary--\r\n"
    ."\r\n";
 
//create request, boundary is required for docusign api
$result = $this->client->createRequest('POST',"$this->baseUrl/templates", [
    'headers' => [
        'Content-Type' => 'multipart/form-data;boundary=customboundary',
        'Content-Length' => strlen($request_body),
        'X-DocuSign-Authentication' => json_encode([
            'Username' => Config::get('docusign.email'),
            'Password' => Config::get('docusign.password'),
            'IntegratorKey' => Config::get('docusign.integratorKey')
        ]),
    ],
    'body' => $request_body
]);
 
try{
    $response = $this->client->send($result);
    $parsed = json_decode($response->getBody(true));
    return $parsed->templateId;
} catch (BadResponseException $e) {
    Log::error('Request send template for deal ' . $config['deal_name'] . ' failed, reason : ' . $e->getMessage());
} catch (RequestException $e) {
    Log::error('Request send template for deal ' . $config['deal_name'] . ' failed, reason : ' . $e->getMessage());
}
