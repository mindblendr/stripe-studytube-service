<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class StudyTubeService
{
    private string $token;

    private function setToken($version = 'v1')
    {
        try {
            if ($version == 'v1') {
                $this->token = 'Basic ' . base64_encode(env('STUDY_TUBE_V1_CLIENT_ID') . ':' . env('STUDY_TUBE_V1_CLIENT_SECRET'));
            } elseif ($version == 'v2') {
                $url = env('STUDY_TUBE_V2_OAUTH_API_URL');
                $headers = [
                    'Content-Type' => 'application/json'
                ];
                $httpClient = Http::withHeaders($headers);
                $callStudyTubeApiResult = $httpClient->post($url, [
                    "grant_type" => "client_credentials",
                    "client_id" => env('STUDY_TUBE_V2_CLIENT_ID'),
                    "client_secret" => env('STUDY_TUBE_V2_CLIENT_SECRET'),
                    "scope" => "write read"
                ]);
                $this->token = 'Bearer ' . $callStudyTubeApiResult->object()->access_token;
            }
            return;
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }
    }

    private function callApi(string $path, string $method = 'GET', $version = 'v1', array $data = [])
    {
        try {
            $this->setToken($version);
            $callStudyTubeApiResult = null;
            $url = '';
            if ($version == 'v1') {
                $url = env('STUDY_TUBE_V1_API_URL') . $path;
            } elseif ($version == 'v2') {
                $url = env('STUDY_TUBE_V2_API_URL') . $path;
            }
            error_log($url);

            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => $this->token
            ];
            $token = $this->token;
            $httpClient = Http::withHeaders($headers);
            switch ($method) {
                case 'POST':
                    $callStudyTubeApiResult = $httpClient->post($url, $data);
                    break;
                case 'GET':
                default:
                    $callStudyTubeApiResult = $httpClient->get($url);
                    break;
            }
            return $callStudyTubeApiResult->object();
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }
        return false;
    }

    public function createUser($email, $first_name, $last_name, $language = 'en')
    {
        try {
            return $this->callApi('users', 'POST', 'v2', [
                'uid' => $email,
                'email' => $email,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'language' => $language,
                'send_invite' => false,
                'assign_licence' => true,
            ]);
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }
        return false;
    }

    public function addUserToTeam($user_id, $team_id)
    {
        try {
            return $this->callApi('teams/' . $team_id . '/members', 'POST', 'v1', ['id' => $user_id]);
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }
        return false;
    }

    public function reinviteUser($user_id)
    {
        try {
            return $this->callApi('users/' . $user_id . '/reinvite', 'POST');
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }
        return false;
    }

    public function getUserByUID($user_id = null)
    {
        try {
            $getUsersResult = $this->callApi('users/?uid=' . $user_id);
            if ($getUsersResult && count($getUsersResult) == 1) {
                return $getUsersResult[0];
            }
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }
        return false;
    }

    public function getTeam($team_id)
    {
        try {
            return $this->callApi('teams/' . $team_id);
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }
        return false;
    }

    public function isUserInTeam($team_id, $user_id)
    {
        try {
            $result = $this->callApi('academy-teams/' . $team_id . '/users', 'GET', 'v2');
            $users = array_map(function ($data) {
                return $data->user;
            }, $result);
            foreach ($users as $user) {
                if ($user_id == $user->id) {
                    return true;
                }
            }
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }
        return false;
    }
}
