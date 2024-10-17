<?php
namespace App\Service;

use Google\Client;
use Google\Service\Sheets;

class GoogleSheetsService
{
    private Sheets $sheetsService;

    public function __construct(string $credentialsPath)
    {
        $client = new Client();
        $client->setAuthConfig($credentialsPath);
        $client->setScopes([Sheets::SPREADSHEETS_READONLY]);
        $this->sheetsService = new Sheets($client);
    }

    public function getSheetData(string $spreadsheetId, string $range)
    {
        $response = $this->sheetsService->spreadsheets_values->get($spreadsheetId, $range);
        return $response->getValues();
    }
}