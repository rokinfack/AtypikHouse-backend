<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReservationController extends AbstractController
{
    private KernelInterface $appKernel;

    public function __construct(KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;
    }

    #[Route('/reservation', name: 'app_reservation')]
    public function index()
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $pdfOptions->setChroot(__DIR__);
        $pdfOptions->setIsRemoteEnabled(true);
        $pdfOptions->setDefaultFont("Inter");
        $pdfOptions->setFontDir("/assets/fonts");
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('pdf/reservation.html.twig', [
            'title' => "Welcome to our PDF Test"
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Store PDF Binary Data
        $output = $dompdf->output();

        // In this case, we want to write the file in the public directory
        $publicDirectory = $this->appKernel->getProjectDir() . '/public';
        // e.g /var/www/project/public/mypdf.pdf
        $pdfFilepath = $publicDirectory . '/mypdf.pdf';

        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);

//        $Output = $dompdf->Output();
//
//        $this->render($Output);

        $response=new Response($output);
        $response->headers->set('Content-type', 'application/octect-stream');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', "Your report".time().".pdf"));
        $response->headers->set('Content-Transfer-Encoding', 'binary');
//        return $response;
        return $this->render("pdf/reservation.html.twig");
//        return $this->render();
//        return $this->json([
//            'message' => 'Welcome to your new controller!',
//            'path' => 'src/Controller/ReservationController.php',
////            'outPut' => $Output
//        ]);
    }
}
