<?php

namespace App\AppAdmin\Controller\Survey;

use App\AppAdmin\Form\Survey\SurveyType;
use App\AppMain\Entity\Survey\Evaluation\Criterion;
use App\AppMain\Entity\Survey\Survey\Survey;
use App\Services\FlashMessage\FlashMessage;
use App\Services\Form\CsrfTokenValidator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/survey-system/surveys", name="manage.survey-system.survey.")
 */
class SurveyController extends AbstractController
{
    protected $flashMessage;
    protected $csrfTokenValidator;
    protected $translator;

    public function __construct(
        FlashMessage $flashMessage,
        CsrfTokenValidator $csrfTokenValidator,
        TranslatorInterface $translator)
    {
        $this->flashMessage = $flashMessage;
        $this->csrfTokenValidator = $csrfTokenValidator;
        $this->translator = $translator;
    }

    /**
     * @Route("", name="index", methods="GET")
     */
    public function index(): Response
    {
        $surveys = $this->getDoctrine()
                        ->getRepository(Survey::class)
                        ->findBy([], ['name' => 'ASC'])
        ;

        return $this->render('manage/survey-system/survey/list.html.twig', [
            'surveys' => $surveys,
        ]);
    }

    /**
     * @Route("/new", name="new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $field = new Survey();

        $form = $this->createForm(SurveyType::class, $field);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($field);
            $em->flush();

            $this->flashMessage->addSuccess(
                $this->translator->trans('survey', ['%count%' => 1]),
                $this->translator->trans('flash.add.success')
            );

            return $this->redirectToRoute('manage.survey-system.survey.index');
        }

        return $this->render('manage/survey-system/survey/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{survey}/edit", name="edit", methods="GET|POST")
     * @ParamConverter("survey", class="App\AppMain\Entity\Survey\Survey\Survey", options={"mapping": {"survey": "uuid"}})
     */
    public function edit(Request $request, Survey $survey): Response
    {
        $form = $this->createForm(SurveyType::class, $survey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($survey);
            $em->flush();

            $this->flashMessage->addSuccess(
                $this->translator->trans('survey', ['%count%' => 1]),
                $this->translator->trans('flash.edit.success')
            );

            return $this->redirectToRoute('manage.survey-system.survey.edit', ['survey' => $survey->getUuid()]);
        }

        $criteria = $this->getDoctrine()->getRepository(Criterion::class)->findBy([
            'survey' => $survey
        ]);

        return $this->render('manage/survey-system/survey/edit.html.twig', [
            'form'  => $form->createView(),
            'item' => $survey,
            'criteria' => $criteria
        ]);
    }

    /**
     * @Route("/{survey}", name="delete", methods="DELETE")
     * @ParamConverter("survey", class="App\AppMain\Entity\Survey\Survey\Survey", options={"mapping": {"survey": "uuid"}})
     */
    public function delete(Survey $survey): Response
    {
        if ($this->csrfTokenValidator->isCsrfTokenValid('delete' . $survey->getUuid())) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($survey);
            $em->flush();

            $this->flashMessage->addSuccess(
                $this->translator->trans('survey', ['%count%' => 1]),
                $this->translator->trans('flash.delete.success')
            );

            return $this->redirectToRoute('manage.survey-system.survey.index');
        }

        $this->flashMessage->addWarning(
            $this->translator->trans('survey', ['%count%' => 1]),
            $this->translator->trans('flash.csrf-token.invalid')
        );

        return $this->redirectToRoute('manage.survey-system.survey.index');
    }
}
