<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class StudyTubeService
{
    private $token;
    public function __construct()
    {
        $this->token = base64_encode(env('STUDY_TUBE_CLIENT_ID') . ':' . env('STUDY_TUBE_CLIENT_SECRET'));
    }

    private function callStudyTubeApi(string $path, string $method = 'GET', array $data = [])
    {
        try {
            $callStudyTubeApiResult = null;
            $url = env('STUDY_TUBE_API_URL') . $path;
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . $this->token
            ];
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
            return $this->callStudyTubeApi('users', 'POST', [
                'user_id' => $email,
                'email' => $email,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'language' => $language,
                'send_invite' => false,
            ]);
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }
        return false;
    }

    public function addUserToTeam($user_id, $team_id)
    {
        try {
            return $this->callStudyTubeApi('teams/' . $team_id . '/members', 'POST', ['id' => $user_id]);
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }
        return false;
    }

    public function reinviteUser($user_id)
    {
        try {
            return $this->callStudyTubeApi('users/' . $user_id . '/reinvite', 'POST');
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }
        return false;
    }

    public function getUserByUID($user_id = null)
    {
        try {
            $getUsersResult = $this->callStudyTubeApi('users/?uid=' . $user_id);
            if ($getUsersResult && count($getUsersResult) == 1) {
                return $getUsersResult[0];
            }
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }
        return false;
    }

    public function getUsers()
    {
        try {
            $getUsersResult = $this->callStudyTubeApi('users');
            return $getUsersResult;
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }
        return false;
    }

    public function getTeam($team_id)
    {
        try {
            return $this->callStudyTubeApi('teams/' . $team_id);
        } catch (\Throwable $error) {
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }
        return false;
    }

    public function isUserInTeam($team_id, $user)
    {
        if ($user && $user->teams) {
            return count(array_filter($user->teams, function ($team) use ($team_id) {
                return $team->id == $team_id;
            })) > 0;
        }
        return false;
    }
}
