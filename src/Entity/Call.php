<?php
namespace App\Entity;


/**
 * Define las propiedades específicas de una llamada.
 *
 * @author Daniel Ramos Martínez <rmdani@gmail.com>
 */
class Call extends Communication {

	/**
	 * @var int
	 */
	private $duration;


	public function getDuration(): int {
		return $this->duration;
	}


	public function setDuration(int $duration): void {
		$this->duration = $duration;
	}

}

