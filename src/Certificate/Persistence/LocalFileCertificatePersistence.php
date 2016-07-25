<?php

namespace AlexaPHP\Certificate\Persistence;

use AlexaPHP\Certificate\Certificate;
use AlexaPHP\Certificate\CertificateInterface;
use AlexaPHP\Exception\AlexaCertificateException;
use AlexaPHP\Exception\AlexaStorageException;
use ErrorException;

class LocalFileCertificatePersistence implements CertificatePersistenceInterface
{
	/**
	 * Filename for metadata JSON file
	 *
	 * @const string
	 */
	const METADATA_FILENAME = 'alexaphp_meta.json';

	/**
	 * Metadata storage
	 *
	 * @var string
	 */
	private $metadata;

	/**
	 * Storage directory for cache and metadata
	 *
	 * @var string
	 */
	private $storage_dir;

	/**
	 * LocalFileCertificatePersistence constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$this->storage_dir = $config['storage_dir'];

		$this->createDirectoriesIfNotExist();

		$this->metadata = json_decode($this->read($this->getMetadataFilename()), true);
	}

	/**
	 * Get the certificate for a given Key
	 *
	 * @param string $key
	 * @return \AlexaPHP\Certificate\CertificateInterface|bool
	 */
	public function getCertificateForKey($key)
	{
		if (! isset($this->metadata['certificates'][$key])) {
			return false;
		}

		$cert_metadata = $this->metadata['certificates'][$key];

		if ($cert_metadata['expires'] < time()) {
			$this->expireCertificateForKey($key);

			return false;
		}

		try {
			return Certificate::createFromLocation($cert_metadata['location']);
		} catch (AlexaCertificateException $e) {
			return false;
		}
	}

	/**
	 * Store a certificate for a given Key
	 *
	 * @param string                                     $key
	 * @param \AlexaPHP\Certificate\CertificateInterface $certificate
	 * @return bool
	 */
	public function storeCertificateForKey($key, CertificateInterface $certificate)
	{
		$data = $this->certificateToArray($key, $certificate);

		$this->metadata['certificates'][$key] = $data;

		$success = $this->updateMetadata();

		if (! $success) {
			throw new AlexaStorageException('Failed to store certificate metadata.');
		}

		$success = $this->write($data['location'], $certificate->getContents());

		if (! $success) {
			throw new AlexaStorageException('Failed to store certificate.');
		}

		return $success;
	}

	/**
	 * Force expiration for a given Key
	 *
	 * @param string $key
	 * @return bool
	 */
	public function expireCertificateForKey($key)
	{
		$cert_file = $this->metadata['certificates'][$key]['location'];

		unlink($cert_file);
		unset($this->metadata['certificates'][$key]);

		return $this->updateMetadata();
	}

	/**
	 * Get the filename (md5 hash of the url) for a certificate
	 *
	 * @param string $key
	 * @return string
	 */
	public function getFilenameForCertificate($key)
	{
		$hashed_url       = md5($key);
		$default_cert_dir = $this->getCertificateFilesDirectory();

		return "$default_cert_dir/$hashed_url.pem";
	}

	/**
	 * Storage directory
	 *
	 * @return string
	 */
	public function getStorageDirectory()
	{
		return $this->storage_dir;
	}

	/**
	 * Write metadata to disk
	 *
	 * @return bool
	 */
	protected function updateMetadata()
	{
		return $this->write($this->getMetadataFilename(), $this->metadata);
	}

	/**
	 * Certificate files directory
	 *
	 * @return string
	 */
	public function getCertificateFilesDirectory()
	{
		return $this->storage_dir . '/' . 'certificates';
	}

	/**
	 * Metadata file
	 *
	 * @return string
	 */
	public function getMetadataFilename()
	{
		return $this->getStorageDirectory() . '/' . self::METADATA_FILENAME;
	}

	/**
	 * Read a file from disk
	 *
	 * @param string $filename
	 * @return string
	 */
	protected function read($filename)
	{
		return file_get_contents($filename);
	}

	/**
	 * Write a file with contents to disk
	 *
	 * @param string       $filename
	 * @param string|array $contents
	 * @return bool
	 */
	protected function write($filename, $contents)
	{
		if (is_array($contents)) {
			$contents = json_encode($contents);
		}

		try {
			return file_put_contents($filename, $contents) !== false;
		} catch (ErrorException $e) {
			return false;
		}
	}

	/**
	 * Create cache storage and certificate storage directories
	 *
	 * @return void
	 */
	protected function createDirectoriesIfNotExist()
	{
		$storage_dir    = $this->getStorageDirectory();
		$cert_files_dir = $this->getCertificateFilesDirectory();

		if (! is_dir($storage_dir)) {
			mkdir($storage_dir);
			$this->write($this->getMetadataFilename(), $this->getDefaultJSONStructure());
		}

		if (! is_dir($cert_files_dir)) {
			mkdir($cert_files_dir);
		}
	}

	/**
	 * Get the default JSON structure as an array
	 *
	 * @return array
	 */
	protected function getDefaultJSONStructure()
	{
		return [
			'certificates' => [],
		];
	}

	/**
	 * Serialize a certificate to array
	 *
	 * @param string                                     $key
	 * @param \AlexaPHP\Certificate\CertificateInterface $certificate
	 * @return array
	 */
	protected function certificateToArray($key, CertificateInterface $certificate)
	{
		return [
			'url'      => $key,
			'location' => $this->getFilenameForCertificate($key),
			'expires'  => $certificate->getEndDate()->getTimestamp(),
		];
	}
}
