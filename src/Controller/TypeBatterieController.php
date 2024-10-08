<?php

namespace App\Controller;

use App\Entity\TypeBatterie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\TypeBatterieType;

class TypeBatterieController extends AbstractController
{
    #[Route('/private-typebatterie', name: 'typebatterie')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $batteries = $entityManager->getRepository(TypeBatterie::class)->findAll();

        return $this->render('typebatterie/index.html.twig', [
            'batteries' => $batteries,
        ]);
    }

    #[Route('/private-typebatterie/delete/{id}', name: 'delete_typebatterie')]
public function delete(EntityManagerInterface $entityManager, $id): Response
{
    $batterie = $entityManager->getRepository(TypeBatterie::class)->find($id);

    if (!$batterie) {
        throw $this->createNotFoundException('No battery found for id ' . $id);
    }

    $entityManager->remove($batterie);
    $entityManager->flush();

    return $this->redirectToRoute('typebatterie');
}

    #[Route('/private-typebatterie/edit/{id}', name: 'edit_typebatterie')]
    public function edit(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $batterie = $entityManager->getRepository(TypeBatterie::class)->find($id);

        if (!$batterie) {
            throw $this->createNotFoundException('Pas de batterie ' . $id);
        }

        $form = $this->createFormBuilder($batterie)
            ->add('reference', TextType::class)
            ->add('capacite', NumberType::class)
            ->add('pays', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Modifier Batterie'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('typebatterie');
        }

        return $this->render('typebatterie/edit.html.twig', [
            'form' => $form->createView(),
            'batterie' => $batterie,
        ]);
    }
}
