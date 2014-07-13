<?php

namespace Czarnodziej\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Czarnodziej\CoreBundle\Form\Type\ContactType;
use Symfony\Component\HttpFoundation\Request;
//use Buzz\Exception\ClientException;

//use Symfony\Component\Serializer\Serializer;
//use Sensio\Bundle\Buzz\Browser;

class DefaultController extends Controller {

    public function indexAction(Request $request) {
        $locale = $this->getRequest()->getLocale();
        $date_mod = $this->container->get('date_mod');
        $last_mod = $date_mod->getPageModTime();

        if ($locale == 'pl') {
            $date = $date_mod->dateMod("j f Y", $last_mod);
        } else {
            $date = date("j F Y", $last_mod);
        }

        $ctx = stream_context_create(
                array(
                    'http' => array(
                        'timeout' => 1
                    )
                )
        );
$url = 'http://ws.audioscrobbler.com/2.0/?method=user.gettoptracks&user=pagodemc&'
                        . 'api_key=21f90626f951daad7849a2c2dd3607d4&'
                        . 'period=7day&limit=5&format=json';
        $response = @file_get_contents($url, 0, $ctx); //@ bypassess warnings

        if ($response !== false) {
            $tracks = json_decode($response);
        } else {
            $tracks = null;
        }

        $form = $this->createForm(new ContactType());
        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $message = \Swift_Message::newInstance()
                        ->setSubject($form->get('subject')->getData())
                        ->setFrom('kontakt@insanet.pl')
                        ->setTo('pagodemc@gmail.com')
                        ->setBody(
                        $this->renderView(
                                'CzarnodziejCoreBundle:Mail:contact.html.twig', array(
                            'ip' => $request->getClientIp(),
                            'name' => $form->get('name')->getData(),
                            'email' => $form->get('email')->getData(),
                            'message' => $form->get('message')->getData()
                                )
                        )
                );

                $this->get('mailer')->send($message);

                $request->getSession()->getFlashBag()->add('success', 'contact.flash.sent');

                return $this->redirect($this->generateUrl('czarnodziej_core_homepage') . '#contact');
            }
        }

        return
                $this->render('CzarnodziejCoreBundle:Default:index.html.twig', array(
                    'form' => $form->createView(),
                    'tracks' => $tracks,
                    'date' => $date,
                        )
        );
    }

}
