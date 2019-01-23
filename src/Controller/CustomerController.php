<?php

declare(strict_types=1);

namespace App\Controller;

use App\Module\Customer\Customer;
use App\Module\Customer\Person;
use App\Security\CustomerAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Customer module controller
 */
class CustomerController extends AbstractController
{
    /**
     * @Route("/login", name="customer_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/customer_login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="customer_logout")
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }

    /**
     * @Route("/register", name="customer_register")
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardAuthenticatorHandler,
        CustomerAuthenticator $customerAuthenticator
    ): Response
    {
        if ($request->isMethod('POST')) {
            $person = new Person();
            $customer = new Customer();
            $customer->setLogin($request->request->get('login'));
            $customer->setPerson($person);
            $customer->setPassword($passwordEncoder->encodePassword(
                $customer,
                $request->request->get('password')
            ));
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->persist($customer);
            $em->flush();

            return $guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
                $customer,
                $request,
                $customerAuthenticator,
                'customer'
            );
        }

        return $this->render('security/register.html.twig');
    }
}
