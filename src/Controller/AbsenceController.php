<?php

namespace App\Controller;

use App\Entity\Absence;
use App\Entity\Student;
use App\Form\AbsenceType;
use App\Repository\AbsenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/absence')]
#[IsGranted('ROLE_ADMIN')]
final class AbsenceController extends AbstractController
{

    private const UPLOAD_DIR = '/uploads/documents';

    #[Route(name: 'app_absence_index', methods: ['GET'])]
    public function index(AbsenceRepository $absenceRepository): Response
    {
        return $this->render('absence/index.html.twig', [
            'absences' => $absenceRepository->findAll(),
        ]);
    }

    #[Route('/new/{student}', name: 'app_absence_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, Student $student): Response
    {
        $absence = new Absence();
        $absence->setStudent($student);

        $form = $this->createForm(AbsenceType::class, $absence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleDocumentUpload($form, $absence, $slugger);

            $entityManager->persist($absence);
            $entityManager->flush();

            return $this->redirectToRoute('app_absence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('absence/new.html.twig', [
            'absence' => $absence,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_absence_show', methods: ['GET'])]
    public function show(Absence $absence): Response
    {
        return $this->render('absence/show.html.twig', [
            'absence' => $absence,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_absence_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Absence $absence, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {

        $form = $this->createForm(AbsenceType::class, $absence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->handleDocumentUpload($form, $absence, $slugger);

            $entityManager->flush();

            return $this->redirectToRoute('app_absence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('absence/edit.html.twig', [
            'absence' => $absence,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_absence_delete', methods: ['POST'])]
    public function delete(Request $request, Absence $absence, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $absence->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($absence);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_absence_index', [], Response::HTTP_SEE_OTHER);
    }

    private function handleDocumentUpload(FormInterface $form, Absence $absence, SluggerInterface $slugger): void
    {
        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null $documentFile */
        $documentFile = $form->get('documentFile')->getData();

        if (!$documentFile) {
            return; // No new file uploaded, keep the existing document unchanged
        }

        $originalFilename = pathinfo($documentFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $documentFile->guessExtension();

        try {
            $documentFile->move(
                $this->getParameter('kernel.project_dir') . '/public' . self::UPLOAD_DIR,
                $newFilename
            );
        } catch (FileException $e) {
            throw new FileException('Could not save the uploaded picture: ' . $e->getMessage());
        }

        $absence->setAbsenceDocument($newFilename);
    }
}
