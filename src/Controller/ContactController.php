<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact.index')]
    public function index(Request $request, EntityManagerInterface $manager, MailerInterface $mailer): Response
    {
        
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            $manager->persist($contact);
            $manager->flush();

            $email = (new Email())
            ->from($contact->getEmail())
            ->to('admin@thedistrict.com')

            ->subject($contact->getSubject())
            ->html($contact->getMessage());

        $mailer->send($email);

            $this->addFlash(
                'success',
                'Votre demande a bien été envoyé !'
            );
            return $this->redirectToRoute('contact.index');
        }
        return $this->render('pages/contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
        }


        

