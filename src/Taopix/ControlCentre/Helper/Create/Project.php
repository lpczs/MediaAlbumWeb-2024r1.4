<?php

namespace Taopix\ControlCentre\Helper\Create;

use Taopix\ControlCentre\Enum;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class Project
{
    private string $collectionCode;
    private string $layoutCode;
    private string $groupCode;
    private string $basketRef;
    private array $modifications;
    private string $projectRef;
    private string $projectName;
    private string $designerURL;
    private array $experienceOverrides;
    private string $endpoint = '';

    public function __construct(private Client $client, private string $url, private int|null $userId, private array $projectValues)
    {
        $this->collectionCode = $projectValues['collectioncode'];
        $this->layoutCode = $projectValues['productcode'] ?? $projectValues['layoutcode'];
        $this->groupCode = $projectValues['groupcode'];
        $this->basketRef = $projectValues['basketref'] ?? '';
        $this->modifications = $projectValues['customparameters'] ?? [];
        $this->experienceOverrides = ($projectValues['experienceoverrides'] ?? []);

        $this->callOnline();

    }

    private function callOnline(): void
    {
        $payload = $this->buildPayLoadForEndPoint($this->projectValues['openmode']);

        // Only supply a value for min life when it is a none zero value.
        if (0 !== ($this->projectValues['minlife'] ?? 0)) {
            $payload['minLife'] = $this->projectValues['minlife'];
        }

        $this->endpoint = Enum\Project\OpenMode::endPoint($this->projectValues['openmode']);

        $response = $this->client->post($this->url . $this->endpoint, [RequestOptions::JSON => $payload]);
        $result = \json_decode($response->getBody(), true);

        if (null !== ($result['userId'] ?? null) && 0 < ($result['guestUserPeriod'] ?? 1)) {
            $guestPeriodToSeconds = (($result['guestUserPeriod'] ?? 1) * 60 * 60);
            \setcookie('TPX-USER-ID', $result['userId'], \time() + $guestPeriodToSeconds, '', '', true, true);
        }

        $this->projectRef = $result['projectRef'] ?? '';
        $this->projectName = $result['projectName'] ?? '';
        $this->designerURL = $result['designerUrl'] . '?ref=' . $result['ref'] . $result['route'];
        $this->userId = $result['userId'];
    }

    public function setProjectRef(string $projectRef): self
    {
        $this->projectRef = $projectRef;
        return $this;
    }

    public function getProjectRef(): string
    {
        return $this->projectRef;
    }

    public function setProjectName(string $projectName): self
    {
        $this->projectName = $projectName;
        return $this;
    }

    public function getProjectName(): string
    {
        return $this->projectName;
    }

    public function setDesignerURL(string $designerURL): self
    {
        $this->designerURL = $designerURL;
        return $this;
    }

    public function getDesignerURL(): string
    {
        return $this->designerURL;
    }

    public function setUserID(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getUserID(): int
    {
        return $this->userId;
    }

    public function customParams(): array
    {
        $mods = [];
        $pageChanges = $this->parsePageParameters();
        $textChanges = $this->parseTextReplacement();

        if (count($textChanges) > 0)
        {
            $mods['textContent'] = $textChanges;
        }

        if (count($pageChanges) > 0)
        {
            $mods['pageCount'] = $pageChanges;
        }

        if (key_exists('pagestyle', $this->modifications))
        {
            $mods['applyLayouts'] = $this->parseLayouts($this->modifications['pagestyle']);
        }

        if (key_exists('oae', $this->modifications))
        {
            $mods['componentSelections'] = $this->modifications['oae'];
        }

        return $mods;
    }

    private function parseTextReplacement(): array
    {
        $return = [];

        foreach ($this->modifications as $key => $value)
        {
            if (substr($key, 0, 3) === 'txt')
            {
                $index = str_replace('txt_', '', $key);
                $return[$index] = urlencode(str_replace("\n", chr(13), $value));
            }
        }

        return $return;
    }

    private function parseLayouts(string $param): array
    {
        $result = [];
        $list = explode('|', $param);
        foreach ($list as $value)
        {
            if (str_contains($value, ':')) {
                $sublist = explode(':', $value);
                $result[] = ['layoutName' => $sublist[1], 'pages' => $sublist[0]];
            } else {
                $result[] = ['layoutName' => $value, 'pages' => 'all'];
            }
        }

        return $this->orderLayouts($result);
    }

    private function orderLayouts(array $layouts): array
    {
        $temp = [];

        // pull out 'special' categories
        $categories = ['right', 'left', 'all', 'cover'];
        foreach ($layouts as $key => $layout)
        {
            if (in_array($layout['pages'], $categories))
            {
                $temp[$layout['pages']] = $layout;
                unset($layouts[$key]);
            }
        }

        unset($categories['cover']);

        // sort numbered pages
        usort($layouts, function ($a, $b) {
            return ($a['pages'] > $b['pages']);
        });

        if (array_key_exists('"cover"', $temp))
        {
            array_push($layouts, $temp['cover']);
            unset($temp['cover']);
        }

        foreach ($categories as $key => $category)
        {
            if (array_key_exists($category, $temp))
            {
                array_unshift($layouts, $temp[$category]);
            }
        }

        return $layouts;
    }

    private function parsePageParameters(): array
    {
        $return = [];
        $keys = ['defaultpages' => 'pageCount', 'minpages' => 'minPages', 'maxpages' => 'maxPages'];
        foreach ($keys as $key => $index)
        {
            if (key_exists($key, $this->modifications))
            {
                $return[$index] = (int) $this->modifications[$key];
            }
        }

        return $return;
    }

    private function buildPayLoadForEndPoint(int $openMode): array
    {
        $payload = [
            'experienceOverrides' => $this->experienceOverrides,
            'basketRef' => $this->basketRef,
            'ccNotificationsEnabled' => $this->projectValues['ccnotificationsenabled'] ?? false,
            'editProjectNameOnFirstSave' => $this->projectValues['editprojectnameonfirstsave'] ?? 1,
            'userId' => 0 === $this->userId ? null : $this->userId,
            'themeId' => $this->projectValues['theme'] ?? 1,
            'sessionValues' => ['basketapiworkflowtype' => ($this->projectValues['basketapiworkflowtype'] ?? 0),
                                'browserlanguagecode' => ($this->projectValues['languagecode'] ?? $this->projectValues['defaultlanguagecode']),
                                'ssotoken' => ($this->projectValues['ssotoken'] ?? ''),
                                'ssoprivatedata' => ($this->projectValues['ssoprivatedata'] ?? []),
                                'username' => ($this->projectValues['username'] ?? ''),
                                'accountcode' => ($this->projectValues['accountcode'] ?? '')],
                                'experienceOverrides' => ['retroprints'  => ($this->projectValues['retroprints'] ?? 0),
                                      'minimumprintsperproject'  => ($this->projectValues['minimumprintsperproject'] ?? 0),
                                      'aimodeoverride' => ($this->projectValues['experienceoverrides']['aimodeoverride'] ?? null),
                                      'onlineeditormode' => ($this->projectValues['experienceoverrides']['onlineeditormode'] ?? null),
                                      'enableswitchingeditor' => ($this->projectValues['experienceoverrides']['enableswitchingeditor'] ?? null),
                                      'largescreenwizardmode' => ($this->projectValues['experienceoverrides']['largescreenwizardmode'] ?? null),
                                      'canshareproject' => ($this->projectValues['experienceoverrides']['canshareproject'] ?? null),
                                      'projectname' =>  ($this->projectValues['experienceoverrides']['projectname'] ?? null),
                                      'checkoutname' =>  ($this->projectValues['experienceoverrides']['checkoutname'] ?? null),
                                      'guestworkflowmode' => ($this->projectValues['experienceoverrides']['guestworkflowmode'] ?? null),
                                      'cansignin' =>  ($this->projectValues['experienceoverrides']['cansignin'] ?? null),
                                      'inproduction' =>  ($this->projectValues['experienceoverrides']['orderfound'] ?? null),
                                      'onlinedesignerlogolinkurl' => ($this->projectValues['experienceoverrides']['onlinedesignerlogolinkurl'] ?? null),
                                      'onlinedesignerlogolinktooltip' => ($this->projectValues['experienceoverrides']['onlinedesignerlogolinktooltip'] ?? null),
                                      'bypassAssistants' => ($this->experienceOverrides['bypassAssistants'] ?? false)]
        ];
        if ($openMode === Enum\Project\OpenMode::New->value)
        {
            $payload = array_merge($payload, ['collectionCode' => $this->collectionCode,
                                                'layoutCode' => $this->layoutCode,
                                                'groupCode' => $this->groupCode,
                                                'groupData' => $this->projectValues['groupdata'],
                                                'modifications' => $this->customParams(),
                                                'assetServiceData' => ($this->projectValues['assetservicedata'] ?? []),
                                            ]);
        }
        else
        {
            $payload = array_merge($payload, ['projectRef' => $this->projectValues['projectref'],
                                                'killOpenSessions' => 1 === (int) $this->projectValues['forcekill'],
                                                'preview' => ($openMode === Enum\Project\OpenMode::PreviewExisting->value) ? true : false
                                            ]);
        }

        return $payload;
    }
}
