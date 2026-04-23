<?php

namespace Taopix\ControlCentre\Helper\Theme;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Doctrine\Persistence\ManagerRegistry;
use Taopix\ControlCentre\Entity\SystemConfig;
use Taopix\ControlCentre\Enum\Theming\Component;

use function str_contains;

class ThemeManager
{
    private ?SystemConfig $systemConfig;

    private mixed $schema;

    public function __construct(private readonly Client $client, private readonly ManagerRegistry $doctrine)
    {
        $this->systemConfig = $this->doctrine->getRepository(SystemConfig::class)->findOneBy([]);
        $this->schema = \json_decode(\file_get_contents(__DIR__ . '/schema.json', true), true);
    }

    /**
     * Return the base theming schema (this contains all the defaults from the design system)
     *
     * @return  mixed
     */
    public function getSchema(): mixed
    {
        return $this->schema;
    }

    /**
     *  Return a specific theme from Taopix Online
     *
     * @param   int    $themeid
     *
     * @return  mixed
     */
    public function getTheme(int $themeid): mixed
    {
        $response = $this->get("api/thememanager?themeId=$themeid");
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Returns all the Themes & Colour Schemes in Taopix Online
     *
     * @return  mixed
     */
    public function getThemeData(): mixed
    {
        $response = $this->get("api/thememanager/list");
        $payload = json_decode($response->getBody()->getContents(), true);

        // loop through each colour scheme and merge the 
        // default schema with the colour scheme data
        foreach ($payload['colourSchemeList'] as &$scheme) {
            $data = $scheme['data'];
            $scheme['data'] = array_replace_recursive($this->schema, json_decode($data, true));
            $scheme['diff'] = json_decode($data, true);
        }

        return [
            'payload' => $payload, 
            'schema' => $this->getSchema()
        ];
    }

    /**
     * Update a Theme
     *
     * @param   mixed  $payload
     *
     * @return  array
     */
    public function updateTheme(mixed $payload): array
    {
        return $this->update(Component::Theme->value, $payload);
    }

    /**
     * Update a Colour Scheme
     *
     * @param   mixed  $payload
     *
     * @return  array
     */
    public function updateColourScheme(mixed $payload): array
    {
        $scheme = $this->update(Component::ColourScheme->value, $payload);
        $data = \json_decode($scheme['data'], true);
        $scheme['data'] = \array_replace_recursive($this->schema, $data);
        $scheme['diff'] = $data;
        return $scheme;
    }

    /**
     * Delete a Theme
     *
     * @param   mixed  $payload
     *
     * @return  void
     */
    public function deleteTheme(mixed $payload): void
    {
        $this->delete(Component::Theme->value, $payload);
    }

    /**
     * Delete a Colour Scheme
     *
     * @param   mixed  $payload
     *
     * @return  void
     */
    public function deleteColourScheme(mixed $payload): void
    {
        $this->delete(Component::ColourScheme->value, $payload);
    }

    /**
     * Update either a Theme or Colour Scheme
     *
     * @param   string  $component
     * @param   mixed   $payload
     *
     * @return  array
     */
    private function update(string $component, mixed $payload): array
    {
        try {
            $response = $this->post("api/thememanager/update-" . $component, $payload);
             // read the response
            return \json_decode($response->getBody()->getContents(), true);
        } catch (\GuzzleHttp\Exception\ClientException | \GuzzleHttp\Exception\ServerException $e) {
            throw new \Exception($e->getResponse()->getBody()->getContents());
        }
        return null;
    }

    /**
     * Delete either a Theme or Colour Scheme
     *
     * @param   string  $component
     * @param   mixed   $params
     *
     * @return  void
     */
    private function delete(string $component, mixed $params): void
    {
        try {
            $this->post("api/thememanager/delete-". $component, json_encode($params));
        } catch (\GuzzleHttp\Exception\ClientException | \GuzzleHttp\Exception\ServerException $e) {
            throw new \Exception($e->getResponse()->getBody()->getContents());
        }
    }

    /**
     * Post to online, appending required auth data
     *
     * @param   string  $url
     * @param   mixed   $payload
     *
     * @return  ResponseInterface
     */
    private function post(string $url, mixed $payload): ResponseInterface
    {
        return $this->client->post($url, [
            'json' => [
                ...array_merge(json_decode($payload, true), [
                    'systemKey' => $this->systemConfig->getSystemKey(),
                    "tenantId" => $this->systemConfig->getTenantid()
                ])
            ]
        ]);
    }

    /**
     * Perform a GET request
     *
     * @param   string  $url
     *
     * @return  ResponseInterface 
     */
    private function get(string $url): ResponseInterface
    {
        return $this->client->get($this->appendGetDefaults($url));
    }

    /**
     * Append the system key & tenantid to a get URL
     *
     * @param string $url
     *
     * @return  string
     */
    private function appendGetDefaults(string $url): string
    {
        $delimiter = '?';
        if (str_contains($url, '?')) {
            $delimiter = '&';
        }

        return $url
            . sprintf("%ssystemkey=%s", $delimiter, $this->systemConfig->getSystemKey())
            . sprintf("&tenantid=%s", $this->systemConfig->getTenantid());
    }
}
