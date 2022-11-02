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
            error_log($error->getMessage());
        }
        return false;
    }

    public function createUser($user_id, $email, $first_name, $last_name, $team_id)
    {
        try {
            $createUserResult = $this->callStudyTubeApi('users', 'POST', [
                'user_id' => $user_id,
                'email' => $email,
                'first_name' => $first_name,
                'last_name' => $last_name
            ]);
            if ($createUserResult && $createUserResult->id) {
                return $this->addUserToTeam($createUserResult->id, $team_id);
            }
        } catch (\Throwable $error) {
            error_log($error->getMessage());
        }
        return false;
    }

    public function addUserToTeam($id, $team_id)
    {
        try {
            $addUserToTeamResult = $this->callStudyTubeApi('teams/' . $team_id . '/members', 'POST', ['id' => $id,]);
            return $addUserToTeamResult;
        } catch (\Throwable $error) {
            error_log($error->getMessage());
        }
        return false;
    }

    public function isUserExists($user_id = null)
    {
        try {
            $getUsersResult = $this->callStudyTubeApi('users/?uid=' . $user_id);
            return count($getUsersResult) > 0;
        } catch (\Throwable $error) {
            error_log($error->getMessage());
        }
        return false;
    }

    public function getUsers()
    {
        try {
            $getUsersResult = $this->callStudyTubeApi('users');
            return $getUsersResult;
        } catch (\Throwable $error) {
            error_log($error->getMessage());
        }
        return false;
    }
}
