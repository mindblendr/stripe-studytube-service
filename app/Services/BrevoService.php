<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BrevoService
{
    private string $url;
    private array $headers;

    private function prepareRequest()
    {
        try {
            $this->headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'api-key' => env('EMAIL_API_KEY'),
            ];
            $this->url = env('EMAIL_API_URL');
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }
    }

    public function callApi(string $path, string $method = 'GET', array $data = [])
    {
        try {
            $this->prepareRequest();

            $callBrevoApiResult = null;

            $httpClient = Http::withHeaders($this->headers)->asJson();
            switch ($method) {
                case 'POST':
                    $callBrevoApiResult = $httpClient->post($this->url . $path, $data);
                    break;
                case 'GET':
                default:
                    $callBrevoApiResult = $httpClient->get($this->url . $path);
                    break;
            }
            return $callBrevoApiResult->object();
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }
        return false;
    }

    public function sendRegistrationInfo($user, $team)
    {
        try {
            $recipients = explode('|', env('EMAIL_RECIPIENTS'));
            foreach ($recipients as $key => &$recipient) {
                $recipient = [
                    'email' => $recipient,
                    'name' => $recipient
                ];
            }
            $sender = env('EMAIL_SENDER');

            return $this->callApi('/smtp/email', 'POST', [
                "sender" => [  
                    "name" => $sender,
                    "email" => $sender
                ],
                "to" => $recipients,
                "subject" => "User Registration Information",
                "htmlContent" => "<html>
                    <body>
                        <table>
                            <tbody>
                                <tr>
                                    <td><label>Name:</label></td>
                                    <td>{$user->first_name} {$user->last_name}</td>
                                </tr>
                                <tr>
                                    <td><label>E-mail:</label></td>
                                    <td>{$user->email}</td>
                                </tr>
                                <tr>
                                    <td><label>Team:</label></td>
                                    <td>{$team->id}</td>
                                </tr>
                            </tbody>
                        </table>
                    </body>
                </html>"
            ]);
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }

        return false;
    }
}
