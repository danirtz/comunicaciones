<?php
namespace App\Entity;


/**
 * Define la clase abstracta de una comunicación.
 *
 * @author Daniel Ramos Martínez <rmdani@gmail.com>
 */
abstract class Communication {

	/**
	 * @var bool
	 */
	protected $isIncoming;


	/**
	 * @var string
	 */
	protected $timestamp;


	public function getIsIncoming(): bool {
		return $this->isIncoming;
	}


	public function setIsIncoming(bool $isIncoming): void {
		$this->isIncoming = $isIncoming;
	}


	public function getTimestamp(): string {
		return $this->timestamp;
	}


	public function setTimestamp(string $timestamp): void {
		$this->timestamp = $timestamp;
	}


	public function getClassName(): string {
		return (new \ReflectionClass($this))->getShortName();
	}

}

