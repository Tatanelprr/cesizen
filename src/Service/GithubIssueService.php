<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubIssueService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        #[Autowire(env: 'GITHUB_TOKEN')]
        private readonly string $githubToken,
        #[Autowire(env: 'GITHUB_REPO')]
        private readonly string $githubRepo,
    ) {}

    public function createIssue(string $title, string $body, array $labels = []): bool
    {
        try {
            $response = $this->httpClient->request('POST', "https://api.github.com/repos/{$this->githubRepo}/issues", [
                'headers' => [
                    'Authorization' => "Bearer {$this->githubToken}",
                    'Accept' => 'application/vnd.github+json',
                    'X-GitHub-Api-Version' => '2022-11-28',
                ],
                'json' => [
                    'title' => $title,
                    'body' => $body,
                    'labels' => $labels,
                ],
            ]);

            return $response->getStatusCode() === 201;
        } catch (\Throwable) {
            return false;
        }
    }
}
