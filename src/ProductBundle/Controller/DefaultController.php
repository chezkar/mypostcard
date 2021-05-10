<?php

namespace ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Bundle\SnappyBundle\Snappy\Response\JpegResponse;
use Guzzle\Client;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $session = new Session();
        $session->start();

        $imgs = [];
        $lts = [];
        $cnt = 0;
        $client = new \GuzzleHttp\Client(['base_uri' => 'https://appdsapi-6aa0.kxcdn.com']);
        $res = $client->request('GET', '/content.php?lang=de&json=1&search_text=berlin&currencyiso=EUR');
        
        $body = $res->getBody();
        $obj = get_object_vars(json_decode($body));
        $content = $obj['content'];
        
        $num_files = $obj['results'];
        $num_rows = floor($num_files/1);
        $remainder = $num_files%1;
        $i = 1;
        $j = 1;
        $kl = 1;

        foreach ($content as $key => $value) {
            if($cnt < 25)
            {
                $lts[$value->{'id'}] = $value->{'full_url'};
                $imgs[] = [
                    'id' => $value->{'id'},
                    'title' => $value->{'title'}, 
                    'price' => $value->{'price'}, 
                    'img_full' => $value->{'full_url'}, 
                    'img_thumb' => $value->{'thumb_url'}
                ];
                $prodClient = new \GuzzleHttp\Client(['base_uri' => 'https://www.mypostcard.com']);
                $url = '/mobile/product_prices.php?json=1&type=get_postcard_products&currencyiso=EUR&store_id='.$value->{'id'};
                $resp = $prodClient->request('GET', $url);
    
                $bodyResp = $resp->getBody();
                $prd = get_object_vars(json_decode($bodyResp));
                $contentProd = $prd['products'];
                foreach ($contentProd as $ky => $vl) {
                    if($vl->{'assignedtype'} == 'Greetcard'){
                        foreach ($vl->{'product_options'} as $k => $po) {
                            $imgs[$cnt]['option'][$k] = $po->{'price'};
                        }
                    }
                }
                $cnt ++;
            }
        }
        
        // set and get session attributes
        $session->set('idurl', $lts);

        return $this->render('ProductBundle:Default:index.html.php', [
            'thmls' => $imgs, 
            'num_files' => $num_files,
            'num_rows' => $num_rows, 
            'remainder' => $remainder,
            'i' => $i,
            'j' => $j,
            'k' => $kl
        ]);
    }

    public function getPdfCreatedAction(Request $request, $id)
    {
        $idurl = $this->get('session')->get('idurl');
        if(isset($idurl[$id]))
        {
            $image = $idurl[$id];
        }else{
            $image = null;
        }

        $d = date('dm_Y');
        
        $snappy = $this->get('knp_snappy.pdf');
        $snappy->setOption('page-size','A4');
        $snappy->setOption('encoding', 'UTF-8');
        $snappy->setOption('enable-javascript', true);
        $snappy->setOption('background', true);
        $snappy->setOption('enable-external-links', true);
        $snappy->setOption('images', true);
        
        $html = $this->renderView('@Product/Default/pdf.html.twig', array(
            'img' => $image
        ));
        
        $filename = date('dmHi');

        return new Response(
            $snappy->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="'.$filename.'.pdf"'
            )
        );
    }
}
