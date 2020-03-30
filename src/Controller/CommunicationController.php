<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ContactRepository;


/**
 * Controlador para mostrar los contactos y sus comunicaciones.
 *
 * @author Daniel Ramos Martínez <rmdani@gmail.com>
 */
class CommunicationController extends AbstractController {

    public function index(ContactRepository $contactRepo) {
		$contacts = $contactRepo->getAll();

		return $this->render('communication/index.html.twig', [
			'contacts' => $contacts
		]);
    }

}

