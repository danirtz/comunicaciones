<?php
namespace App\Repository;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Entity\Contact;
use App\Entity\Call;
use App\Entity\SMS;


/**
 * Repositorio de contactos con sus comunicaciones. Obtiene todos los contactos de la fuente.
 *
 * @author Daniel Ramos Martínez <rmdani@gmail.com>
 */
class ContactRepository {

	/**
	 * Comunicación de tipo Llamada.
	 * @var int
	 */
	const CALL_TYPE = 1;

	/**
	 * Comunicación de tipo SMS.
	 * @var int
	 */
	const SMS_TYPE = 2;

	/**
	 * Constante que define el caracter de llamada.
	 * @var string
	 */
	const CALL_CHAR = 'C';

	/**
	 * Constante que define el caracter de SMS.
	 * @var string
	 */
	const SMS_CHAR = 'S';

	/**
	 * Rango de caracteres en dónde se encuentra el tipo de comunicación.
	 * @var array int
	 */
	const TYPE_RANGE = [0, 1];

	/**
	 * Rango de caracteres en dónde se encuentra el número de origen.
	 * @var array int
	 */
	const ORIGIN_NUMBER_RANGE = [1, 9];

	/**
	 * Rango de caracteres en dónde se encuentra el número de destino.
	 * @var array int
	 */
	const DESTINATION_NUMBER_RANGE = [10, 9];

	/**
	 * Rango de caracteres en dónde se encuentra el dato de si la llamada es
	 * entrante.
	 * @var array int
	 */
	const INCOMING_RANGE = [19, 1];

	/**
	 * Rango de caracteres en dónde se encuentra el nombre de contacto.
	 * @var array int
	 */
	const NAME_RANGE = [20, 24];

	/**
	 * Rango de caracteres en dónde se encuentra la fecha y hora de la
	 * comunicación.
	 * @var array int
	 */
	const TIMEDATE_RANGE = [44, 14];

	/**
	 * Rango de caracteres en dónde se encuentra la duración de la comunicación.
	 * @var array int
	 */
	const DURATION_RANGE = [58, 6];


	/**
	 * @var string Fuente de datos.
	 */
	private $source;


	function __construct(ParameterBagInterface $params) {
		$this->source = $params->get('app.communications_url');
	}


    /**
     * @return Contact[]
     */
	public function getAll(): array {
		// Obtiene el contenido y lo separa por líneas.
		$content  = @file_get_contents($this->source);
		$rows     = explode("\n", $content);
		$contacts = [];

		foreach ($rows as $row) {
			$data = $this->getBasicData($row);
			if ($data) {
				// Elige el número de contacto según si la llamada es entrante o saliente.
				if ($data['is_incoming']) {
					$contactNumber = $data['origin_number'];
				} else {
					$contactNumber = $data['destination_number'];
				}

				// Índice único para almacenar los contactos.
				$contactIdx = $contactNumber . $data['contact_name'];

				// Crea un contacto nuevo si aún no estaba creado.
				if (!isset($contacts[$contactIdx])) {
					$contacts[$contactIdx] = new Contact(
						$contactNumber,
						$data['contact_name']
					);
				}

				if ($data['type'] === self::CALL_TYPE) {
					$communication = new Call();
					$communication->setIsIncoming($data['is_incoming']);
					$communication->setTimestamp($data['timestamp']);
					$communication->setDuration($data['duration']);
				} else {
					$communication = new SMS();
					$communication->setIsIncoming($data['is_incoming']);
					$communication->setTimestamp($data['timestamp']);
				}

				$contacts[$contactIdx]->addCommunication($communication);
			}

		}

		return $contacts;
	}


    /**
	 * Obtiene los datos de contacto y comunicación a partir de la línea de texto.
	 *
     * @return string[]
     */
	private function getBasicData(string $row): ?array {
		$firstChar = substr($row, self::TYPE_RANGE[0], self::TYPE_RANGE[1]);

		if ($firstChar == self::CALL_CHAR) {
			$type = self::CALL_TYPE;
		} elseif ($firstChar == self::SMS_CHAR) {
			$type = self::SMS_TYPE;
		} else {
			// Descarta las líneas que no son de llamada ni de SMS.
			return null;
		}

		$originNumber = (int) ltrim(substr(
			$row,
			self::ORIGIN_NUMBER_RANGE[0],
			self::ORIGIN_NUMBER_RANGE[1]
		));

		$destinationNumber = (int) ltrim(substr(
			$row,
			self::DESTINATION_NUMBER_RANGE[0],
			self::DESTINATION_NUMBER_RANGE[1]
		));

		$isIncoming = substr(
			$row,
			self::INCOMING_RANGE[0],
			self::INCOMING_RANGE[1]
		) === '1';

		$contactName = rtrim(substr(
			$row,
			self::NAME_RANGE[0],
			self::NAME_RANGE[1]
		));

		$dateTime = substr(
			$row,
			self::TIMEDATE_RANGE[0],
			self::TIMEDATE_RANGE[1]
		);

		$timestamp = $this->getTimestamp($dateTime);

		$duration = (int) substr(
			$row,
			self::DURATION_RANGE[0],
			self::DURATION_RANGE[1]
		);
		

		return [
			'type'               => $type,
			'origin_number'      => $originNumber,
			'destination_number' => $destinationNumber,
			'is_incoming'        => $isIncoming,
			'contact_name'       => $contactName,
			'timestamp'          => $timestamp,
			'duration'           => $duration
		];
	}


	/**
	 * Transforma la cadena de texto de fecha y hora del formado del registro a
	 * un formato comprensible.
	 */
	private function getTimestamp(string $str): string {
		$day     = substr($str, 0, 2);
		$month   = substr($str, 2, 2);
		$year    = substr($str, 4, 4);
		$hour    = substr($str, 8, 2);
		$minutes = substr($str, 10, 2);
		$seconds = substr($str, 12, 2);

		return "$day-$month-$year $hour:$minutes:$seconds";
	}

}
