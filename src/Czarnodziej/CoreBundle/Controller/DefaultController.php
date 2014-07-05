<?php

namespace Czarnodziej\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Czarnodziej\CoreBundle\Form\Type\ContactType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $form = $this->createForm(new ContactType());
        if ($request->isMethod('POST'))
        {
            $form->bind($request);

            if ($form->isValid())
            {
                $message = \Swift_Message::newInstance()
                        ->setSubject($form->get('subject')->getData())
                        ->setFrom($form->get('email')->getData())
                        ->setTo('pagodemc@gmail.com')
                        ->setBody(
                        $this->renderView(
                                'CzarnodziejCoreBundle:Mail:contact.html.twig', array(
                            'ip'      => $request->getClientIp(),
                            'name'    => $form->get('name')->getData(),
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
                    'form' => $form->createView(),)
        );
    }
}
