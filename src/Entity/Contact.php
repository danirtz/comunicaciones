<?php
namespace App\Entity;


/**
 * Define las propiedades de un contacto.
 *
 * @author Daniel Ramos Martínez <rmdani@gmail.com>
 */
class Contact {

	/**
	 * @var int
	 */
	private $phoneNumber;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var array Communication
	 */
	private $communications;


	function __construct(int $number, string $name) {
		$this->phoneNumber    = $number;
		$this->name           = $name;
		$this->communications = [];
	}


	public function getPhoneNumber(): int {
		return $this->phoneNumber;
	}


	public function getName(): string {
		return $this->name;
	}


    /**
     * @return Communication[]
     */
	public function getCommunications(): array {
		return $this->communications;
	}


	public function addCommunication(Communication $communication): void {
		$this->communications[] = $communication;
	}

}

