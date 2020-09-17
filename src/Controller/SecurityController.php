<?php


namespace App\Controller;


use App\Entity\Casquette;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{

    /**
     * @Route("/")
     */
    public function index()
    {
        $user = $this->getUser();
        return new JsonResponse($user->getRoles(), 200);
    }

    /**
     * @Route("/agence")
     * //Security("is_granted('ROLE_AGENCE')") <== pas encore géré (?)
     * @IsGranted("ROLE_AGENCE")
     */
    public function agence()
    {
        return new JsonResponse(["OK"], 200);
    }

    /**
     * @Route("/directeur")
     */
    public function directeur()
    {
        $this->denyAccessUnlessGranted('ROLE_DIRECTEUR');
        return new JsonResponse(["OK"], 200);
    }

    /**
     * @Route("/beneficiaire")
     */
    public function beneficiaire()
    {
        $this->denyAccessUnlessGranted('ROLE_BENEFICIAIRE');
        return new JsonResponse(["OK"], 200);
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $data = json_decode($request->getContent(), true);
        $email    = $data["email"]    ?? null;
        $password = $data["password"] ?? null;

        // Authentification

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);

        if (!$user || !$passwordEncoder->isPasswordValid($user, $password)) {
            return new JsonResponse(["message" => "Bad email or password"], 401);
        }

        // Génération des tokens

        /** @var Casquette $casquette */
        foreach ($user->getCasquettes() as $casquette) {
            $casquette->setApiToken(bin2hex(random_bytes(32)));
        }
        $em->flush();

        return new JsonResponse(["user" => $user, "casquettes" => $user->getCasquettes()->toArray()], 200);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(EntityManagerInterface $em)
    {
        $user = $this->getUser();

        // Reset des tokens

        /** @var Casquette $casquette */
        foreach ($user->getCasquettes() as $casquette) {
            $casquette->setApiToken(null);
        }
        $em->flush();

        return new JsonResponse(null, 200);
    }

    /**
     * @Route("/init")
     */
    public function initData(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $users = $em->getRepository(User::class)->findAll();
        foreach ($users as $user) {
            $em->remove($user);
        }
        $em->flush();

        $user = new User();
        $user->setEmail("toto1@gmail.com");
        $user->setPassword($passwordEncoder->encodePassword($user, "azeaze"));
        $em->persist($user);

        $casquette = new Casquette();
        $casquette->setName("Bénéficiaire");
        $casquette->setRoles(["ROLE_BENEFICIAIRE"]);
        $user->addCasquette($casquette);

        $user = new User();
        $user->setEmail("toto2@gmail.com");
        $user->setPassword($passwordEncoder->encodePassword($user, "azeaze"));
        $em->persist($user);

        $casquette = new Casquette();
        $casquette->setName("Agence");
        $casquette->setRoles(["ROLE_AGENCE"]);
        $user->addCasquette($casquette);

        $casquette = new Casquette();
        $casquette->setName("Directeur");
        $casquette->setRoles(["ROLE_DIRECTEUR"]);
        $user->addCasquette($casquette);

        $em->flush();

        return new JsonResponse(null, 201);

    }

}
