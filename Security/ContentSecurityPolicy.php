<?php

namespace Security;

/**
 * Class ContentSecurityPolicy
 * @package Security
 */
class ContentSecurityPolicy
{
    /**
     * @var CSPBuilder $builder
     */
    protected $builder;

    /**
     * @var String $config
     */
    protected $config;

    /**
     * directives we want to ignore
     *
     * @var array $ignore
     */
    protected $ignore = [];

    /**
     * ContentSecurityPolicy constructor.
     * @param array $config
     * @throws \Exception
     */
    public function __construct(array $config)
    {
        // $ac_config
        $this->config = $config;

        // create the builder from the Taopix base CSP
        $this->builder = CSPBuilder::fromFile(implode(DIRECTORY_SEPARATOR, [__DIR__, 'resources', 'csp.default.json']));
    }

    /**
     * Builds up the CSP from $ac_config data
     *
     * @throws \Exception
     */
    public function build(): void
    {
        // build the array of directives we want to support
        $directives = array_diff(CSPBuilder::$directives, $this->ignore);

        // loop through and check each directive against $ac_config settings and set where necessary
        foreach ($directives as $directive) {
            $var = sprintf('CONTENTSECURITYPOLICY%s', strtoupper(str_replace('-', '', $directive)));
            if (null !== ($option = $this->getConfigVar($var, null))) {
                foreach (\explode(',', $option) as $entry) {
                    $this->builder->addSource($directive, $entry);
                }
            }
        }

        // handle report only option (alerts the licensee to breaches, but allows them)
        $isReportOnly = $this->getConfigVar('CONTENTSECURITYPOLICYREPORTONLY', 0);
        if($isReportOnly > 0) {
            $this->builder->addDirective('report-only', true);
        }

        // handle report uri option (if the licensee wants to be alerted to breaches)
        $reportingEndpoint = $this->getConfigVar('CONTENTSECURITYPOLICYREPORTURI', null);
        if ($reportingEndpoint) {
            $this->builder->setReportUri($reportingEndpoint);
        }

        // see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy/upgrade-insecure-requests
        $upgradeInsecureRequests = $this->getConfigVar('CONTENTSECURITYUPGRADEINSECURE', 0);
        if($upgradeInsecureRequests > 0) {
            $this->builder->addDirective('upgrade-insecure-requests', true);
        }
    }

    /**
     * @param $var
     * @param $default
     * @return mixed
     */
    protected function getConfigVar(string $var, $default)
    {
        if (array_key_exists($var, $this->config) && $this->config[$var] != '') {
            return $this->config[$var];
        }
        return $default;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function sendHeader() : bool
    {
        return $this->builder->sendCSPHeader();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function nonce() : string
    {
        return $this->builder->nonce('script-src');
    }

    /**
     * @return CSPBuilder
     */
    public function getBuilder(): CSPBuilder
    {
        return $this->builder;
    }


    /**
     * @param string $directive
     * @return $this
     */
    public function addIgnoredDirective(string $directive): self
    {
        $this->ignore[] = $directive;

        return $this;
    }
}