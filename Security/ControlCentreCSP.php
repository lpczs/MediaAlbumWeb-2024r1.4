<?php

namespace Security;


final class ControlCentreCSP extends ContentSecurityPolicy
{
    /**
     * @var array $frameOptions
     */
    private $frameOptions;

	/**
	 * Returns the current control centre CSP Instance.
	 *
	 * @var ControlCentreCSP
	 */
	private static $instance = null;

    /**
     * ControlCentreCSP constructor.
     * @param array $config
     * @throws \Exception
     */
    private function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * @param mixed $frameOptions
     * @return ControlCentreCSP
     */
    public function setFrameOptions(array $frameOptions): self
    {
        $this->frameOptions = $frameOptions;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFrameOptions()
    {
        return $this->frameOptions;
    }

    /**
     * @inheritDoc
	 *
	 * @param pBrandCode Brand code we are building for.
     */
    public function build($pBrandCode = null): void
    {
        // build the base policy from $ac_config
        parent::build();

		$this->getBrandCSPAdditions($pBrandCode);

        // handle iframe options
        $ancestors = $this->frameOptions['frameancestor'];
        if ($ancestors !== 'ALLOW') {
            if (\is_array($ancestors)) {
                foreach ($ancestors as $ancestor) {
                    $this->builder->addSource('ancestor', $ancestor);
                }
            } else {
                $this->builder->addDirective('frame-ancestor', $ancestors);
            }
        }

        // As the images can be from any external source, we can not force https.
        $this->builder->disableHttpsTransformOnHttpsConnections();
    }

	/**
	 * Returns the instance of the CSP builder.
	 *
	 * @param array $config
	 * @return \self
	 */
	public static function getInstance(array $config): self
	{
		if (null === self::$instance)
		{
			self::$instance = new self($config);
		}

		return self::$instance;
	}

	/**
	 * Gets the brand specific csp rules from the auto generated configration.
	 *
	 * @param string $pBrandCode Brand code we are getting details for.
	 * @returns void
	 */
	private function getBrandCSPAdditions($pBrandCode)
	{
		$brandCSPConfig = new CSPConfigBuilder();
		$brandAdditions = $brandCSPConfig->getBrandCSP($pBrandCode);

		$this->processAdditions($brandAdditions);
	}

	/**
	 * Adds additional rules to the CSP.
	 *
	 * @param array $pBrandAdditions Array containing additional CSP rules to add.
	 * @returns void
	 */
	private function processAdditions($pBrandAdditions)
	{
		// Loop over each directive.
		foreach ($pBrandAdditions as $directive => $details)
		{
			// Loop over each url and add it as a source to the builder.
			foreach ($details as $url)
			{
				$this->builder->addSource($directive, $url);
			}
		}
	}
}