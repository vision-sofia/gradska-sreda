<?php

namespace App\AppMain\Controller;

use App\AppMain\Entity\Achievement\AchievementBase;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/achievements/", name="app.achievements.")
 * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
 */
class AchievementController extends AbstractController
{
    protected $entityManager;
    protected $utils;
    protected $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @Route("", name="index")
     */
    public function index(): Response
    {
        $achievements = $this->getDoctrine()
            ->getRepository(AchievementBase::class)
            ->findAll()
        ;

        return $this->render('front/achievement/index.html.twig', [
            'achievements' => $achievements,
        ]);
    }
}
