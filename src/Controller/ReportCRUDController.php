<?php

namespace App\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class ReportCRUDController extends CRUDController
{
    public function summaryAction($id =null)
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);
         // Provide a name for your file with extension
        $date = $object->getDate()->format("Y-m-d");
        $filename = 'Informe-'.$date."-".
                  $object->getOrigin()->getName().".html";
        
        // The dinamically created content of the file
        $fileContent = $this->renderView(
            "report/by-words.html.twig",
            array('report'=> $object)
        );
        
        // Return a response with a specific content
        $response = new Response($fileContent);

        // Create the disposition of the file
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        // Set the content disposition
        $response->headers->set('Content-Disposition', $disposition);

        // Dispatch request
        return $response;
    }
}
