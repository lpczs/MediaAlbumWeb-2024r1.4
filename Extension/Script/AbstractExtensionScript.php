<?php

namespace Extension\Script;

use Extension\Script\Exception\ExtensionScriptMethodNotFoundException;
use Extension\Script\Exception\ExtensionScriptNotLoadedException;
use Extension\Script\Exception\UnhandledExtensionScriptErrorException;
use ErrorException;
use Exception;

abstract class AbstractExtensionScript
{
	/**
	 * @var string
	 */
	protected $extensionPath;

	/**
	 * @var bool
	 */
	private $loaded = false;

	/**
	 * Custom error handler for catching any errors that occur within an extension script
	 * and convert to an exception.
	 *
	 * @param $errorNo
	 * @param $message
	 * @param $sourceFile
	 * @param $sourceLine
	 * @throws ErrorException
	 */
	public static function exception_error_handler($errorNo, $message, $sourceFile, $sourceLine)
	{
		throw new ErrorException($message, $errorNo, 0, $sourceFile, $sourceLine);
	}

	/**
	 * Constructor
	 *
	 * @param string $extensionPath
	 */
	public function __construct($extensionPath)
	{
		$this->extensionPath = $extensionPath;
	}

	/**
	 * Get the full extension script file path to include
	 *
	 * @return string
	 */
	abstract protected function getExtensionFilePath();

	/**
	 * Get the extension class name
	 *
	 * @return string
	 */
	abstract protected function getExtensionClassName();

	/**
	 * Get the extension base class name
	 *
	 * @return string|null
	 */
	protected function getExtensionBaseClassName()
	{
		return null;
	}

	/**
	 * Call the extension method {$methodName} with the input {$input}
	 * and return the result.
	 *
	 * Catches any output the extension might produce and discards it.
	 *
	 * @param string $extensionMethodName
	 * @param mixed ...$extensionArgs
	 * @return mixed
	 * @throws ExtensionScriptMethodNotFoundException
	 * @throws ExtensionScriptNotLoadedException
	 * @throws UnhandledExtensionScriptErrorException
	 */
	protected function callExtension($extensionMethodName, ...$extensionArgs)
	{
		if (!$this->isLoaded()) {
			throw new ExtensionScriptNotLoadedException();
		}

		$extensionClassName = $this->getExtensionClassName();
		if (!method_exists($extensionClassName, $extensionMethodName)) {
			throw new ExtensionScriptMethodNotFoundException();
		}

		try {
			return call_user_func_array([$extensionClassName, $extensionMethodName], $extensionArgs);
		} catch (Exception $ex) {
			throw new UnhandledExtensionScriptErrorException($ex);
		}
	}

	/**
	 * Load the extension by loading the extension file.
	 * Returns true if the extension was loaded successfully, false otherwise.
	 *
	 * @return boolean
	 */
	public function load()
	{
		if ($this->isLoaded()) {
			return true;
		}

		// If the class name already exists, assume it was declared through a previous instantiation
		// as we cannot include the extension script file a second time and declare the class again.
		$extensionClassName = $this->getExtensionClassName();
		if (class_exists($extensionClassName)) {
			$this->loaded = true;
		} else {
			// If the extension file is not found, return failure
			$extensionFile = realpath($this->getExtensionFilePath());
			if (!file_exists($extensionFile)) {
				return false;
			}

			// Attempt to load the extension
			try {
				$this->loaded = (@(include $extensionFile) !== false ? true : false);
			} catch (Exception $ex) {
				return false;
			}
		}

		// Base class validation
		if ($this->loaded && ($extensionBaseClassName = $this->getExtensionBaseClassName()) !== null) {
			$extensionClassName = $this->getExtensionClassName();
			$this->loaded = is_subclass_of($extensionClassName, $extensionBaseClassName);
		}

		return $this->loaded;
	}

	/**
	 * Check if the extension has the desired method available
	 *
	 * @param string $extensionMethodName
	 * @return bool
	 * @throws ExtensionScriptNotLoadedException
	 */
	public function hasMethod($extensionMethodName)
	{
		if (!$this->isLoaded()) {
			throw new ExtensionScriptNotLoadedException();
		}

		$extensionClassName = $this->getExtensionClassName();
		return method_exists($extensionClassName, $extensionMethodName);
	}

	/**
	 * Check if the extension has already been loaded
	 *
	 * @return bool
	 */
	public function isLoaded()
	{
		return $this->loaded;
	}
}
